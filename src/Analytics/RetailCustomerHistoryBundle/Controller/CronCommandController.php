<?php

namespace App\Analytics\RetailCustomerHistoryBundle\Controller;

use App\Analytics\RetailCustomerHistoryBundle\Service\RetailCrmService;
use App\Entity\Connections;
use App\Repository\ConnectionsRepository;
use App\Repository\SettingsRepository;
use Psr\Log\InvalidArgumentException;

class CronCommandController
{
    protected $settingsRepository;
    protected $connectionsRepository;
    protected $retailCrmService;
    protected $moduleSettings;

    public function __construct(SettingsRepository $settingsRepository,ConnectionsRepository $connectionsRepository,RetailCrmService $retailCrmService)
    {
        $this->settingsRepository = $settingsRepository;
        $this->connectionsRepository = $connectionsRepository;
        $this->retailCrmService = $retailCrmService;
    }

    /**
     * @param string $variantId
     */
    public function getSettings(string $variantId)
    {
        //'a9c07bfc5b_10caefc38dfe94be13f81adfc0866ecb'
        if ($this->moduleSettingsSettings = $this->settingsRepository->findOneBy(['variantId' => $variantId])){
            return $this->moduleSettingsSettings;
        }
        return false;
    }

    public function startCRM($moduleSettings):bool
    {
        if (is_object($moduleSettings)){
            $connectionSettings = $this->connectionsRepository->find($moduleSettings->getConnectionId());
            $this->retailCrmService->startHistory($connectionSettings,$moduleSettings);
            return true;
        }
        return false;

    }

}