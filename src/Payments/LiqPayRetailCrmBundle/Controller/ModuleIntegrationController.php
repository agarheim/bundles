<?php


namespace App\Payments\LiqPayRetailCrmBundle\Controller;

use App\Payments\LiqPayRetailCrmBundle\Helpers\RetailCrmHelpers;
use App\Payments\LiqPayRetailCrmBundle\Service\RetailCrm;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class ModuleIntegrationController extends AbstractController
{
    public function activate (array $settings, bool $activate, RetailCrm $retailCrm):Response
    {
         if (!empty($settings)){
             $retailCrmHelpers = new RetailCrmHelpers();
             // $retailCrm = new RetailCrm();
             $r = $retailCrm->setModule($retailCrmHelpers->getDataForCrm($settings,$activate));
             $response = $retailCrm->getResponse($r);
             file_put_contents('log_module_data.txt',print_r($settings,true));
             file_put_contents('log_activate.txt',print_r($response,true));
             return new Response($response);
         }else{
             return new Response('Данные для активации модуля отсутствуют');
         }
    }

//    public function deactivate(array $settings)
//    {
//        if (!empty($settings)){
//            $retailCrmHelpers = new RetailCrmHelpers();
//            $retailCrm = new RetailCrm();
//            $r = $retailCrm->setModule($retailCrmHelpers->getDataForCrm($settings,false));
//            $response = $retailCrm->getResponse($r);
//            return new Response('ДеАктивирован.<br>'.$response);
//        }
//    }

}