<?php

namespace App\Analytics\RetailCustomerHistoryBundle\Service;

use App\Analytics\RetailCustomerHistoryBundle\Repository\CustomersRepository;
use App\Repository\ConnectionsRepository;
use App\Repository\SettingsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use RetailCrm\ApiClient;
use RetailCrm\Exception\CurlException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManager;
use App\Analytics\RetailCustomerHistoryBundle\Service\RetailCustomerHistoryService;

class RetailCrmService
{
    /**
     * @var SettingsRepository
     */
    public $settingsRepository;

    /**
     * @var ConnectionsRepository
     */
    public $connectionsRepository;
    /**
     * @var EntityManagerInterface
     */
    public $entityManager;

    public $customersRepository;

    public $clientApi;

    public $rchs;

    public $rohs;

    private $logger;

    public function __construct(ConnectionsRepository $connectionsRepository,
                                EntityManagerInterface $entityManager,
                                CustomersRepository $customersRepository,
                                RetailCustomerHistoryService $rchs,
                                RetailOrderHistoryService $rohs,
                                LoggerInterface $logger)
    {
        $this->connectionsRepository = $connectionsRepository;
        $this->entityManager = $entityManager;
        $this->customersRepository = $customersRepository;
        $this->rchs = $rchs;
        $this->rohs = $rohs;
        $this->logger = $logger;
    }

    public function startHistory($connectionsSettings, $moduleSettings): bool
    {
        if ($this->createConnection($connectionsSettings, $moduleSettings)) {
            $this->getHistory($moduleSettings);
            return true;
        }
        return false;
    }

    private function createConnection($connectionsSettings, $moduleSettings)
    {
        try {
            $this->clientApi = new ApiClient(
                $connectionsSettings->getLoginUrl(),
                $connectionsSettings->getApiKey()
            );
            return true;
        } catch (CurlException $e) {
            $log = new Logger('retailConnectionLog');
            $log->pushHandler(new StreamHandler(dirname(__DIR__) . '/log/history_log.log', Logger::WARNING));
            // add records to the log
            $log->warning($e->getMessage());
            return false;
        }
    }

    private function getHistory($moduleSettings)
    {
          $settings = $moduleSettings->getSettings();
//        $log = new Logger('retailConnectionLog');
//        $log->pushHandler(new StreamHandler(dirname(__DIR__) . '/log/history_log.log', Logger::WARNING));

        if (empty($this->clientApi)) {
            return false;
        }

        if (isset($moduleSettings->getSettings()['sinceIdOrders'])) {
            $filter_orders = array('sinceId' => $moduleSettings->getSettings()['sinceIdOrders']);
        }else{
            $filter_orders = array('sinceId' => 0);
        }
        if (isset($moduleSettings->getSettings()['sinceIdCustomers'])) {
            $filter_customers = array('sinceId' => $moduleSettings->getSettings()['sinceIdCustomers']);
        }else{
            $filter_customers = array('sinceId' => 0);
        }
//work with history customer
        $this->getCustomerHistory($filter_customers,$moduleSettings);
//work with history orders
        $this->getOrderHistory($filter_orders,$moduleSettings);
        //первоначальная загрузка
//        $this->getStartOrders($moduleSettings);
        return true;
    }


    private function getSinceId($response)
    {
        if (empty($response['history'])){
            return false;
        }
        $history = $response['history'];
        $last_change = end($history);
        return $last_change['id'];
    }

    private function updateSienceId($moduleSettings)
    {
        $this->entityManager->persist($moduleSettings);
        // actually executes the queries (i.e. the INSERT query)
        $this->entityManager->flush();
    }

    public function getRetailCustomers($customersIds)
    {
        if (empty($this->clientApi)) {
            return false;
        }

        $filter = array('ids' => $customersIds);

        try {
            return $this->clientApi->request->customersList($filter,null,100);
        } catch (\Exception $e) {
            $this->logger->error('не удалось получить заказы из ритейла.',[$filter,$customersIds]);
        }
        return false;
    }

    public function getRetailOrders($ordersIds)
    {
        if (empty($this->clientApi)) {
            return false;
        }
        if ($ordersIds){
            $filter['ids'] = $ordersIds;
        }
        $filter ['extendedStatus'] = ['complete-group'];

        try {
            $res = $this->clientApi->request->ordersList($filter,null,100);
            return $res;
        } catch (\Exception $e) {
            $this->logger->error('не удалось получить заказы из ритейла.',[$filter, $ordersIds]);
        }
        return false;
    }

    public function getCustomerHistory($filter_customers,$moduleSettings)
    {
        try {
            //получить историю по заданной логике
            $response = $this->clientApi->request->customersHistory($filter_customers, null, 100);
            if (!$response->isSuccessful()) {
                return false;
            }
            //получить ид клиентов из истории
            $retailCustomersIds = $this->rchs->getCustomersIdsHistory($response);
            if ($retailCustomersIds) {
//                если таковые имеются получить данные по этим клиентам
                $retailCustomers = $this->getRetailCustomers(array_keys($retailCustomersIds));
//                обновить или создать клиентов
                if (!$retailCustomers->isSuccessful()) {
                    $this->logger->info('не удалось получить клиентов из ритейла.');
                    return false;
                }
                $this->rchs->prepareCustomers($retailCustomers, $moduleSettings);
            }
        } catch (\Exception $e) {
            $this->logger->error('нет доступа к ритейлу.',[$e->getMessage()]);
            return false;
        }
        if ($customerSinceId = $this->getSinceId($response)) {
            $settings = $moduleSettings->getSettings();
            $settings['sinceIdCustomers'] = $customerSinceId;
            $moduleSettings->setSettings($settings);
            $this->updateSienceId($moduleSettings);
        }
        return true;

    }

    public function getOrderHistory( $filter_orders, $moduleSettings )
    {
        try {
            $response = $this->clientApi->request->ordersHistory($filter_orders, null, 100);
        } catch (\Exception $e) {
            $this->logger->error('нет доступа к ритейлу.', [$e->getMessage()]);
            return false;
        }

        //получить историю по заданной логике
        if (!$response->isSuccessful()) {
            $this->logger->error('Api response ok, data 400');
            return false;
        }
        //получить ид Orders из истории
        $retailOrdersIds = $this->rohs->getOrdersIdsHistory($response);

        if ($retailOrdersIds) {
//                если таковые имеются получить данные по этим клиентам
            $retailOrders = $this->getRetailOrders(array_keys($retailOrdersIds));
//                обновить или создать заказы
            if (!$retailOrders->isSuccessful()) {
                $this->logger->info('не удалось получить Orders из ритейла.');
                return false;
            }
            $this->rohs->prepareOrders($retailOrders, $moduleSettings);
        }

        if ($orderSinceId = $this->getSinceId($response)) {
            $settings = $moduleSettings->getSettings();
            $settings['sinceIdOrders'] = $orderSinceId;
            $moduleSettings->setSettings($settings);
            $this->updateSienceId($moduleSettings);
        }
        return true;
    }

    public function getStartOrders($moduleSettings)
    {
        $retailOrders = $this->getRetailOrders();
//                обновить или создать заказы
        if (!$retailOrders->isSuccessful()) {
            $this->logger->info('не удалось получить Orders из ритейла.');
            return false;
        }
      //  $this->rohs->prepareOrders($retailOrders, $moduleSettings);
       return true;
    }
}