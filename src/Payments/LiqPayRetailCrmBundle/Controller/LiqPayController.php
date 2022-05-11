<?php

declare(strict_types=1);

namespace App\Payments\LiqPayRetailCrmBundle\Controller;

use App\Payments\LiqPayRetailCrmBundle\Helpers\LiqPayHelpers;
use App\Payments\LiqPayRetailCrmBundle\Service\LiqPay;
use App\Repository\ConnectionsRepository;
use App\Repository\SettingsRepository;
use RetailCrm\ApiClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;


class LiqPayController extends AbstractController
{
    protected $settingsRepository;
    protected $liqPay;
    protected $liqPayHelpers;
    protected $connectionRepository;

    public function __construct(SettingsRepository $settingsRepository, ConnectionsRepository $connectionsRepository,
                                LiqPay $liqPay, LiqPayHelpers $liqPayHelpers)
    {
        $this->settingsRepository = $settingsRepository;
        $this->liqPay = $liqPay;
        $this->liqPayHelpers = $liqPayHelpers;
        $this->connectionRepository = $connectionsRepository;
    }

    /**
     * @Route("/module/liqpay/create", name="liq_pay_create")
     * @param Request $request
     * @return Response
     */
    public function sendPay(Request $request )
    {
        $retailSettings = $this->settingsRepository->findOneBy(['variantId' => $request->get('clientId')]);


        if ( !$request->isMethod('POST')
            || $retailSettings === null
            || !$request->request->has("create")){
            return new JsonResponse(["success"=>false,
                                                  "errorMsg"=>"Запрос не разрешен! Для данного пользователя.",
            ]);
        }
        $dataForLiqPayArray = json_decode($request->get("create"),true);

        //получение настроек из БД для конкретного магазина
     //   $liqpaySettings = $this->liqPayHelpers->getLiqPayParameters($retailSettings->getVariantId(),$dataForLiqPayArray['shopId']);

    //    file_put_contents('shopId.txt', $dataForLiqPayArray['shopId'], FILE_APPEND );
        //validation CreateData and generate Data from LiqPay
      //  file_put_contents('request.txt', print_r($dataForLiqPayArray,true));
        $this->liqPayHelpers->setDataForLiqPay($dataForLiqPayArray, $retailSettings);

        if (!empty($this->liqPayHelpers->getErrors())) {
            return new JsonResponse(["success"=>false,
                "errorMsg"=>"Данные по заказу не полные!",
              ]);
        }

       //get Data from LiqPay
        $retailCrmData = $this->liqPayHelpers->getDataForLiqPay();
        file_put_contents('responseLiqPay.txt', print_r('1',true));
        if ($this->liqPayHelpers->checkCreateInvoiceByOrderId($retailCrmData['dataForCreatePay']['order_id'])) {
            return new JsonResponse(["success"=>false,
                "errorMsg"=>"Счет с таким номером уже существует",
               ]);
        }
//        file_put_contents('dataforliqpay.txt',print_r('retailCrmData',true));
        //send Data to LiqPay and get Invoice
        $this->liqPay->setAuth($retailCrmData['public_key'], $retailCrmData['private_key']);
		$responseLiqPay = $this->liqPay->api("request",$retailCrmData['dataForCreatePay']);
 file_put_contents('responseLiqPay.txt', print_r($responseLiqPay,true));
        file_put_contents('responseLiqPay.txt', print_r($retailCrmData,true),FILE_APPEND);
        if ($responseLiqPay->result != 'ok') {
            return new JsonResponse(["success"=>false,
                "errorMsg"=>"LiqPay не доступен! ".$responseLiqPay->err_description,
//                "errors" => [
//                    'LiqPay' => 'Not create invoice!'
//                ]
            ]);
        }elseif ($responseLiqPay->result == 'ok' && property_exists ($responseLiqPay,'err_code') && $responseLiqPay->err_code == 'shop_blocked'){
            return new JsonResponse(["success"=>false,
                "errorMsg"=>"LiqPay не доступен! ".$responseLiqPay->err_description,
//                "errors" => [
//                    'LiqPay' => 'Not create invoice!'
//                ]
            ]);
        }
        //create Order in our DB and get ID
        $orderLiqPayId = $this->liqPayHelpers->createLiqPayOrders($responseLiqPay, $retailCrmData);
        return new JsonResponse([
            'success' => true,
            'result' => [
                'paymentId' => $orderLiqPayId,
                'invoiceUrl' => $responseLiqPay->href,
                'cancellable' => true
            ]
        ]);
    }

    /**
     * @Route("/module/liqpay/status_update/{liqPayModuleId}", name="liq_status_update")
     * @param string $liqPayModuleId
     * @param Request $request
     * @param LiqPayHelpers $liqPayHelpers
     * @return JsonResponse
     */
    public function updateStatusPay(string $liqPayModuleId, Request $request,LiqPayHelpers $liqPayHelpers): JsonResponse
    {
        $site = $liqPayModuleId;
        $signatureData = $request->get("signature");
        $data = $request->get("data");
        file_put_contents('liqpayordernotupdate.txt', print_r($_POST,true), FILE_APPEND );
        $retailSettings = $this->settingsRepository->findOneBy(['variantId' => $site]);
        if (  $retailSettings === null ){
            return new JsonResponse(["success"=>false,
                "errorMsg"=>"Запрос не разрешен! Для данного пользователя.",
            ]);
        }
        $settings = $retailSettings->getSettings();
        $connectionSettings = $this->connectionRepository->find($retailSettings->getConnectionId());
        $data = $liqPayHelpers->checkSignatureLiqPay($settings, $data, $signatureData);
        $liqpayOrder = $liqPayHelpers->checkCreateInvoiceByUuid($data->order_id);

        if (!$liqpayOrder){
            file_put_contents('liqpayordernotupdate1.txt', $request->get("signature").$request->get("data"), FILE_APPEND );
           // $logger->error($site.': Счет не обновлен! Данные от LiqPay: '.print_r($signatureData).print_r($data));
            return new JsonResponse([
                'success' => false,
                "errorMsg"=>"Счет не создан!",
            ]);
        }
        file_put_contents('liqpayorderupdate.txt', print_r($data,true), FILE_APPEND );
        $liqpayOrder->setStatus($data->status);
        $liqpayOrder->setAmount($data->amount);
        $liqpayOrder->setStatusForUpdateRetailCrm('not_update');

        $liqPayHelpers->updateStatusInvoice($liqpayOrder);

        $status = $settings['status_error'];
        if ($data->status === 'success') {
            $status = $settings['status_success'];
        }

        $editPayment = [
            'invoiceUuid' => $liqpayOrder->getPaymentId(),
            'paymentId'   => $liqpayOrder->getId(),
            'amount'      => $data->amount,
            'paidAt'      => date('Y-m-d H:i:s'),
            'status'      => $status,
          //  'comment' => 'Статус по оплате - ' . $data->status . ' Сумма платежа - ' . $data->amount
        ];

        try {
            $client = new ApiClient(
                $connectionSettings->getLoginUrl(),
                $connectionSettings->getApiKey()
              //  $settings['apiVersion']
            );
        //нужно писать в лог данніе изменения статуса оплаты
        $client->request->paymentUpdateInvoice($editPayment);
        //   $client->request->ordersPaymentEdit($editPayment, 'id', $site);
        $liqpayOrder->setStatusForUpdateRetailCrm('update');
        $liqPayHelpers->updateStatusInvoice($liqpayOrder);
        } catch (\RetailCrm\Exception\CurlException $e) {
            return new JsonResponse(['success'=>false,
                'error'=>  $e->getMessage()]);
        }
        return new JsonResponse(['success'=>true]);
    }

    /**
     * @Route("/module/liqpay/cancel", name="liq_pay_cancel")
     * @param Request $request
     * @param LiqPay $liqPay
     * @param LiqPayHelpers $liqPayHelpers
     * @return JsonResponse
     */
    public function cancelPay(Request $request, LiqPay $liqPay, LiqPayHelpers $liqPayHelpers): JsonResponse
    {
        $retailSettings = $this->settingsRepository->findOneBy(['variantId' => $request->request->get('clientId')]);
        if (  $retailSettings === null ){
            return new JsonResponse(["success"=>false,
                "errorMsg"=>"Запрос не разрешен! Для данного пользователя.",
            ]);
        }

        $liqpaySettings = $retailSettings->getSettings();

       // file_put_contents('cancel.txt', 'sadgfsdfghdsg');
      //  $retailSettings = $this->getParameter('retailCrm');

        if ( !$request->isMethod('POST')
            || $retailSettings->getVariantId()!=$request->request->get('clientId')
            || !$request->request->has("cancel")){
            return new JsonResponse(["success"=>false,
                "errorMsg"=>"Запрос не разрешен!",
//                                                 "errors" => [
//                                                     'clientId' => 'пустой или не настроен'
                //                                      ]
            ]);
        }

       $paymentId = json_decode($request->request->get("cancel"),true);
    //    file_put_contents('cancel.txt', print_r($paymentId,true));
       $invoiceData = $liqPayHelpers->getInvoiceDataById($paymentId['paymentId']);

         if (empty($invoiceData))
         {
             return new JsonResponse([
                 'success' => false,
                 "errorMsg"=>"Счет с таким номером не найден!",
             ]);
         }
//         if (!$invoiceData->getPaymentId()){
//             return new JsonResponse([
//                 'success' => false,
//                 "errorMsg"=>"Invoice id not found!",
//             ]);
//         }
 //

        if ($invoiceData->getStatus()=='success'){
            return new JsonResponse([
                'success' => false,
                "errorMsg"=>"Счет с таким номером оплачен!",
                ]);
        }
      //  $liqpaySettings = $liqPayHelpers->getLiqPayParameters($request->request->get('clientId'),$invoiceData->getSite() );
        // $liqpaySettings = $this->getParameter('liqpay');
         $public_key = $liqpaySettings['public_key'];
         $private_key = $liqpaySettings['private_key'];

         if ($liqpaySettings['mode'] == 'test') {
             $public_key = $liqpaySettings['sandbox_public_key'];
             $private_key = $liqpaySettings['sandbox_private_key'];
         }

         $liqPay->setAuth($public_key, $private_key);
         $res = $liqPay->api("request", array(
             'action'        => 'invoice_cancel',
             'version'       => '3',
             'order_id'      => $invoiceData->getPaymentId()
             ));

        if ($res->result=='ok'){
            $invoiceData->setStatus('invoice_cancel');
            $liqPayHelpers->updateStatusInvoice($invoiceData);

                    return new JsonResponse([
                        'success' => true,
                        "errorMsg"=>"Счет отменен!",
                    ]);
            }
            return new JsonResponse([
                'success' => false,
                "errorMsg"=>"Счет не может быть отменен!",
            ]);
    }

    /**
     * @Route("/module/liqpay/status", name="liq_pay_approve")
     * @return JsonResponse
     */
    public function approvePay(): JsonResponse
    {
        file_put_contents('/home/babasik/tetragon.com.ua/roman/approvefile.txt', 'GET' . serialize($_GET) . 'POST' . serialize($_POST));
        return new JsonResponse([
            'success' => true
        ]);
    }
}


