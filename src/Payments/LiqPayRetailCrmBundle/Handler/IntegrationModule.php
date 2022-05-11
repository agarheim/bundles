<?php


namespace App\Payments\LiqPayRetailCrmBundle\Handler;

use App\Payments\LiqPayRetailCrmBundle\Helpers\RetailCrmHelpers;
use App\Payments\LiqPayRetailCrmBundle\Service\RetailCrm;

class IntegrationModule 
{
	public function setModule (array $settings, bool $activate, RetailCrm $retailCrm):Response
    {
    	if (empty($settings)){
    		return new Response('Данные для активации модуля отсутствуют');
    	}

		$retailCrmHelpers = new RetailCrmHelpers();
		$retailCrm = new RetailCrm();

		$r = $retailCrm->setModule($retailCrmHelpers->getDataForCrm($settings,$activate));
		$response = $retailCrm->getResponse($r);
		return new Response($response);
         
    }
}