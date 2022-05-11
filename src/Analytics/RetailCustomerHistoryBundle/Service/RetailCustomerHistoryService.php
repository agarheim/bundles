<?php

namespace App\Analytics\RetailCustomerHistoryBundle\Service;

use App\Analytics\RetailCustomerHistoryBundle\Entity\CustomerPrepare;
use App\Analytics\RetailCustomerHistoryBundle\Entity\Customers;
use App\Analytics\RetailCustomerHistoryBundle\Repository\CustomersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use RetailCrm\ApiClient;
use RetailCrm\Exception\CurlException;

class RetailCustomerHistoryService
{
    public $customersRepository;
    public $entityManager;

    public function __construct(ManagerRegistry $doctrine,
                                CustomersRepository $customersRepository
                                )
    {
        $this->entityManager = $doctrine->getManager();
        $this->customersRepository = $customersRepository;
    }
    public function getCustomersIdsHistory($response)
    {
        $retailCustomersIds = [];
            if (empty($response['history'])) {
                return false;
            }
        return $this->getRetailCustomersIds($response['history']);
    }

    public function getRetailCustomersIds($customersHistory)
    {
        //print_r($customersHistory);
        $customers = array();
        foreach ($customersHistory as $change) {
            $customers[$change['customer']['id']] = true;
        }
        //  print_r($customers);
        return $customers;
    }

    public function prepareCustomers($retailCustomers,$moduleSettings)
    {
        $customerIds = $this->getCustomerIds($retailCustomers->getCustomers());

        $customersIsset = $this->customersRepository->findBy(['clientIdinRetailCrm' => array_keys($customerIds)]);
        $event = 0;
        foreach ($retailCustomers->getCustomers() as $customer) {
            if (isset($customersIsset) && count($customersIsset) > 0) {
                foreach ($customersIsset as $customersIsset_item) {
                    if ($customersIsset_item->getClientIdinRetailCrm() == $customer['id']) {

                        if (isset($customer['ordersCount'])) {
                            $customersIsset_item->setOrdersCount($customer['ordersCount']);
                        }
                        if (isset($customer['address']) && count($customer['address']) > 0) {
                            $customersIsset_item->setAddress($customer['address']);
                        }
                        if (isset($customer['firstName'])) {
                            $customersIsset_item->setFirstName($customer['firstName']);
                        }
                        if (isset($customer['lastName'])) {
                            $customersIsset_item->setLastName($customer['lastName']);
                        }
                        if (isset($customer['email'])) {
                            $customersIsset_item->setEmail($customer['email']);
                        }
                        if (isset($customer['patronymic'])) {
                            $customersIsset_item->setPatronymic($customer['patronymic']);
                        }
                        if (isset($customer['phones']) && count($customer['phones']) > 0) {
                            $ph=[];
                            foreach ($customer['phones'] as $phone)
                            {
                                $ph[]=$phone['number'];
                            }
                            $customersIsset_item->setPhones($ph);
                        }
                        $this->entityManager->persist($customersIsset_item);
                        $event = 1;
                        break;
                    }
                }
            }
            if ($event == 1) {
                $event = 0;
                continue;
            }
            $event = 0;
            $user = new Customers();

            if ($customer['type'] != 'customer') {
                continue;
            }
            $user->setModuleId($moduleSettings->getVariantId());

            $user->setClientIdinRetailCrm($customer['id']);

            if (isset($customer['ordersCount'])) {
                $user->setOrdersCount($customer['ordersCount']);
            }
            if (isset($customer['address']) && count($customer['address']) > 0) {
                $user->setAddress($customer['address']);
            }
            if (isset($customer['firstName'])) {
                $user->setFirstName($customer['firstName']);
            }
            if (isset($customer['lastName'])) {
                $user->setLastName($customer['lastName']);
            }
            if (isset($customer['email'])) {
                $user->setEmail($customer['email']);
            }
            if (isset($customer['patronymic'])) {
                $user->setPatronymic($customer['patronymic']);
            }
            if (isset($customer['phones']) && count($customer['phones']) > 0) {
                $ph=[];
                foreach ($customer['phones'] as $phone)
                {
                    $ph[]=$phone['number'];
                }
                $user->setPhones($ph);
            }
            $this->entityManager->persist($user);

        }
        $this->entityManager->flush();

        return true;
    }

    public function getCustomerIds($retailCustomers)
    {
        $fixCustomersIds = [];
        foreach ($retailCustomers as $customer) {
            if ($customer['type']!='customer'){
                continue;
            }
            $fixCustomersIds[$customer['id']] = $customer['id'];
        }
        return $fixCustomersIds;
     }
}