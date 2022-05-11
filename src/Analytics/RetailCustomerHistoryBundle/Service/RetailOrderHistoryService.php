<?php

namespace App\Analytics\RetailCustomerHistoryBundle\Service;

use App\Analytics\RetailCustomerHistoryBundle\Entity\Orders;
use App\Analytics\RetailCustomerHistoryBundle\Repository\OrdersRepository;
use Doctrine\Persistence\ManagerRegistry;

class RetailOrderHistoryService
{
    public $ordersRepository;
    public $entityManager;

    public function __construct(ManagerRegistry $doctrine,
                                OrdersRepository $ordersRepository
                                )
    {
        $this->entityManager = $doctrine->getManager();
        $this->ordersRepository = $ordersRepository;
    }
    public function getOrdersIdsHistory($response)
    {
            if (empty($response['history'])) {
                return false;
            }
        return $this->getRetailOrdersIds($response['history']);
    }

    public function getRetailOrdersIds($ordersHistory)
    {
        $orders = array();
        foreach ($ordersHistory as $change) {
            $orders[$change['order']['id']] = true;
        }
        return $orders;
    }

    public function prepareOrders($retailOrders,$moduleSettings)
    {
        $orderIds = $this->getOrdersIds($retailOrders->getOrders());

        $ordersIsset = $this->ordersRepository->findBy(['orderId' => array_keys($orderIds)]);

        $event = 0;
        foreach ($retailOrders->getOrders() as $order) {
           // print_r($order['id']);
            if (isset($ordersIsset) && count($ordersIsset) > 0) {
                foreach ($ordersIsset as $ordersIsset_item) {
                    if (isset($order['statusUpdatedAt'])) {
//                        if ($ordersIsset_item->getStatusUpdatedAt()==new \DateTime($order['statusUpdatedAt'])){
//                            break;
//                        }
                        $ordersIsset_item->setStatusUpdatedAt(new \DateTime($order['statusUpdatedAt']));
                    }
                    if ($ordersIsset_item->getOrderId() == $order['id']) {
                        if (isset($order['customer']['id'])) {
                            $ordersIsset_item->setCustomerId($order['customer']['id']);
                        }
                        if (isset($order['totalSumm'])) {
                            $ordersIsset_item->setTotalsumm($order['totalSumm']);
                        }

                        if (isset($order['delivery']['address'])) {
                            $delivery = null;
                            if (isset($order['delivery']['address']['city'])){
                                $delivery['city'] = $order['delivery']['address']['city'];
                            }
                            if (isset($order['delivery']['address']['text'])){
                                $delivery['text'] = $order['delivery']['address']['text'];
                            }
                                if (isset($delivery)){
                                    $ordersIsset_item->setAddress($delivery);
                                }
                        }
                        if (isset($order['status'])) {
                            $ordersIsset_item->setOrderStatus($order['status']);
                        }

                        if (isset($order['items']) && is_array($order['items'])) {
                            $offer = $this->prepareItems($order['items']);
                            if ($offer && count($offer) > 0) {
                                $ordersIsset_item->setOffers($offer);
                            }
                        }
                        $this->entityManager->persist($ordersIsset_item);
                        $event = 1;

                    }
                }
            }
            if ($event === 1) {
                $event = 0;
                continue;
            }
            if ($order['status'] != 'complete') {
                continue;
            }

            $orderEntity = new Orders();

            $orderEntity->setModuleId($moduleSettings->getVariantId());

            $orderEntity->setOrderId($order['id']);

            if (isset($order['customer']['id'])) {
                $orderEntity->setCustomerId($order['customer']['id']);
            }
            if (isset($order['totalSumm'])) {
                $orderEntity->setTotalsumm($order['totalSumm']);
            }
            if (isset($order['createdAt'])) {
                $orderEntity->setCreatedAt(new \DateTime($order['createdAt']));
            }
            if (isset($order['statusUpdatedAt'])) {
                $orderEntity->setStatusUpdatedAt(new \DateTime($order['statusUpdatedAt']));
            }
            if (isset($order['status'])) {
                $orderEntity->setOrderStatus($order['status']);
            }

            if (isset($order['items'])) {
                $offer = $this->prepareItems($order['items']);
                if ($offer && count($offer) > 0) {
                    $orderEntity->setOffers($offer);
                }
            }
            if (isset($order['delivery']['address'])) {
                $delivery=null;
                if (isset($order['delivery']['address']['city'])) {
                    $delivery['city'] = $order['delivery']['address']['city'];
                }
                if (isset($order['delivery']['address']['text'])) {
                    $delivery['text'] = $order['delivery']['address']['text'];
                }
                if ($delivery) {
                    $orderEntity->setAddress($delivery);
                }
            }
            $this->entityManager->persist($orderEntity);

        }
        $this->entityManager->flush();

        return true;
    }

    public function getOrdersIds($retailOrders): array
    {
        $fixOrdersIds = [];
        foreach ($retailOrders as $order) {
            $fixOrdersIds[$order['id']] = $order['id'];
        }
        return $fixOrdersIds;
     }

    public function prepareItems(array $items): array
    {
        $newItems = [];
        foreach ($items as $key => $item)
        {
            if ($item['status'] == 'saled'){
                $newItems[] = [
                    'quantity' => $item['quantity'],
                    'displayName' => $item['offer']['displayName'],
                    'externalId' => isset($item['offer']['externalId'])?$item['offer']['externalId']:'',
                    'xmlId'      => isset($item['offer']['xmlId'])?$item['offer']['xmlId']:'',
                ];
            }
//            var_dump($item);

        }
        return $newItems;
     }
}