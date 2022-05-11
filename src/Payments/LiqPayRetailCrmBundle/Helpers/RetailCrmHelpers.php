<?php

namespace App\Payments\LiqPayRetailCrmBundle\Helpers;

use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;

class RetailCrmHelpers
{
    public function getDataForCrm(array $settings,$activity)
    {
        if (empty($settings['moduleCode'])
            ||empty($settings['moduleName'])
            ||empty($settings['baseUrl'])
            ||empty($settings['pathStatus'])
            ||empty($settings['clientId'])
            ||empty($settings['apiUrl'])
            ||empty($settings['apiKey'])
           ||empty($settings['apiVersion'])
        //   ||empty($settings['codeSite'])
        )
            throw new \InvalidArgumentException('список передаваемых параметров не полный');
        $integrationModule ['dataForApi'] =[
          'apiUrl' =>  $settings['apiUrl'],
          'apiKey' => $settings['apiKey'],
           'apiVersion' => $settings['apiVersion'],
//           'codeSite' => $settings['codeSite']
        ];
        if (is_string($settings['shopsLists'])){
            $settings['shopsLists'] = json_decode($settings['shopsLists'],true);
        }
        $integrationModule ['dataForModule'] = [
            'code' => $settings['moduleCode'],
            'active' => $activity,
            'name' => $settings['moduleName'],
            'logo' => $settings['pathToLogo'],
            'accountUrl' => $settings['baseUrl'].'/',
            'clientId' => $settings['clientId'],
            'baseUrl'  => $settings['baseUrl'],
            'actions'  => [
                'activity' => 'activity'
            ],
            "integrations"=> [
                   "payment"=> [
                        "actions"=> [
                        "create"=> $settings['pathCreate'],
                        "approve"=> $settings['pathApprove'],
                        "cancel"=> $settings['pathCancel'],
                        "refund"=> $settings['pathRefund'],
                        "status" => $settings['pathStatus']
                      ],
            "currencies"=> $settings['currencies'],
            "invoiceTypes"=> $settings['invoiceType'],
            "shops"=> $settings['shopsLists'],
            "availableCountries" => $settings['availableCountries']
        ]
    ],

        ];
                return $integrationModule;
    }
}