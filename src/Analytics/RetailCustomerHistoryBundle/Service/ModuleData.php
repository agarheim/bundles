<?php


namespace App\Analytics\RetailCustomerHistoryBundle\Service;

use Symfony\Component\HttpFoundation\Request;


class ModuleData
{
//    public $shopsLists;
//    public $mode;
//    public $sandbox_public_key;
//    public $sandbox_private_key;
//    public $shop_id;
//    public $public_key;
//    public $private_key;
//    public $email_default;
//    public $phone_default;
//    public $field_for_send;
//    public $field_for_send_default;
//    public $invoice_lifetime_hours;
//    public $description;
//    public $api;
//    public $currency = 'UAH';
//    public $paymentType = 'liqpay';
//    public $paymentStatusForCreateInvoice = 'invoice';
//    public $server_url_update_status = '/module/liqpay/status_update';
//    public $status_success = 'succeeded';
//    public $status_pandidng = 'pending';
//    public $status_error = 'canceled';
//
//    //liqpaydata
//    public $moduleCode ;
//    public $moduleName ;
//    public $baseUrl ;
//    public $hashActivateModule;
//
//    public $apiVersion = 'v5';
//    public $clientId;
//    public $pathToLogo;
//    public $pathCreate = '/module/liqpay/create';
//    public $pathApprove = '/module/liqpay/approve';
//    public $pathCancel = '/module/liqpay/cancel';
//    public $pathRefund = '/module/liqpay/refund';
//    public $pathStatus = '/module/liqpay/status';
//    public $currencies = [ 'UAH'];
//    public $invoiceType = ["link"];
//    public $availableCountries = ['UA'];


// /**
//  * @return string
//  */
//    public function getShopsLists():string
//{
//    return json_decode($this->shopsLists,true);
//}
//
//    /**
//     * @param mixed $shopsLists
//     */
//    public function setShopsLists($shopsLists): void
//{
//    $this->shopsLists = json_encode($shopsLists);
//}
//
//    /**
//     * @return mixed
//     */
//    public function getMode()
//    {
//        return $this->mode;
//    }
//
//    /**
//     * @param mixed $mode
//     */
//    public function setMode($mode): void
//    {
//        $this->mode = $mode;
//    }
//
//    /**
//     * @return mixed
//     */
//    public function getSandboxPublicKey()
//    {
//        return $this->sandbox_public_key;
//    }
//
//    /**
//     * @param mixed $sandbox_public_key
//     */
//    public function setSandboxPublicKey($sandbox_public_key): void
//    {
//        $this->sandbox_public_key = $sandbox_public_key;
//    }
//
//    /**
//     * @return mixed
//     */
//    public function getSandboxPrivateKey()
//    {
//        return $this->sandbox_private_key;
//    }
//
//    /**
//     * @param mixed $sandbox_private_key
//     */
//    public function setSandboxPrivateKey($sandbox_private_key): void
//    {
//        $this->sandbox_private_key = $sandbox_private_key;
//    }
//
//    /**
//     * @return mixed
//     */
//    public function getShopId()
//    {
//        return $this->shop_id;
//    }
//
//    /**
//     * @param mixed $shop_id
//     */
//    public function setShopId($shop_id): void
//    {
//        $this->shop_id = $shop_id;
//    }
//
//    /**
//     * @return mixed
//     */
//    public function getPublicKey()
//    {
//        return $this->public_key;
//    }
//
//    /**
//     * @param mixed $public_key
//     */
//    public function setPublicKey($public_key): void
//    {
//        $this->public_key = $public_key;
//    }
//
//    /**
//     * @return mixed
//     */
//    public function getPrivateKey()
//    {
//        return $this->private_key;
//    }
//
//    /**
//     * @param mixed $private_key
//     */
//    public function setPrivateKey($private_key): void
//    {
//        $this->private_key = $private_key;
//    }
//
//    /**
//     * @return mixed
//     */
//    public function getEmailDefault()
//    {
//        return $this->email_default;
//    }
//
//    /**
//     * @param mixed $email_default
//     */
//    public function setEmailDefault($email_default): void
//    {
//        $this->email_default = $email_default;
//    }
//
//    /**
//     * @return mixed
//     */
//    public function getPhoneDefault()
//    {
//        return $this->phone_default;
//    }
//
//    /**
//     * @param mixed $phone_default
//     */
//    public function setPhoneDefault($phone_default): void
//    {
//        $this->phone_default = $phone_default;
//    }
//
//    /**
//     * @return mixed
//     */
//    public function getFieldForSend()
//    {
//        return $this->field_for_send;
//    }
//
//    /**
//     * @param mixed $field_for_send
//     */
//    public function setFieldForSend($field_for_send): void
//    {
//        $this->field_for_send = $field_for_send;
//    }
//
//    /**
//     * @return mixed
//     */
//    public function getFieldForSendDefault()
//    {
//        return $this->field_for_send_default;
//    }
//
//    /**
//     * @param mixed $field_for_send_default
//     */
//    public function setFieldForSendDefault($field_for_send_default): void
//    {
//        $this->field_for_send_default = $field_for_send_default;
//    }
//
//    /**
//     * @return mixed
//     */
//    public function getInvoiceLifetimeHours()
//    {
//        return $this->invoice_lifetime_hours;
//    }
//
//    /**
//     * @param mixed $invoice_lifetime_hours
//     */
//    public function setInvoiceLifetimeHours($invoice_lifetime_hours): void
//    {
//        $this->invoice_lifetime_hours = $invoice_lifetime_hours;
//    }
//
//    /**
//     * @return mixed
//     */
//    public function getDescription()
//    {
//        return $this->description;
//    }
//
//    /**
//     * @param mixed $description
//     */
//    public function setDescription($description): void
//    {
//        $this->description = $description;
//    }
//
//    /**
//     * @return mixed
//     */
//    public function getApi()
//    {
//        return $this->api;
//    }
//
//    /**
//     * @param mixed $api
//     */
//    public function setApi($api): void
//    {
//        $this->api = $api;
//    }
//
//    /**
//     * @return mixed
//     */
//    public function getCurrency()
//    {
//        return $this->currency;
//    }
//
//    /**
//     * @param mixed $currency
//     */
//    public function setCurrency($currency): void
//    {
//        $this->currency = $currency;
//    }
//
//    /**
//     * @return mixed
//     */
//    public function getPaymentType()
//    {
//        return $this->paymentType;
//    }
//
//    /**
//     * @param mixed $paymentType
//     */
//    public function setPaymentType($paymentType): void
//    {
//        $this->paymentType = $paymentType;
//    }
//
//    /**
//     * @return mixed
//     */
//    public function getPaymentStatusForCreateInvoice()
//    {
//        return $this->paymentStatusForCreateInvoice;
//    }
//
//    /**
//     * @param mixed $paymentStatusForCreateInvoice
//     */
//    public function setPaymentStatusForCreateInvoice($paymentStatusForCreateInvoice): void
//    {
//        $this->paymentStatusForCreateInvoice = $paymentStatusForCreateInvoice;
//    }
//
//    /**
//     * @return mixed
//     */
//    public function getServerUrlUpdateStatus()
//    {
//        return $this->server_url_update_status;
//    }
//
//    /**
//     * @param mixed $server_url_update_status
//     */
//    public function setServerUrlUpdateStatus($server_url_update_status): void
//    {
//        $this->server_url_update_status = $server_url_update_status;
//    }
//
//    /**
//     * @return mixed
//     */
//    public function getStatusSuccess()
//    {
//        return $this->status_success;
//    }
//
//    /**
//     * @param mixed $status_success
//     */
//    public function setStatusSuccess($status_success): void
//    {
//        $this->status_success = $status_success;
//    }
//
//    /**
//     * @return mixed
//     */
//    public function getStatusPandidng()
//    {
//        return $this->status_pandidng;
//    }
//
//    /**
//     * @param mixed $status_pandidng
//     */
//    public function setStatusPandidng($status_pandidng): void
//    {
//        $this->status_pandidng = $status_pandidng;
//    }
//
//    /**
//     * @return mixed
//     */
//    public function getStatusError()
//    {
//        return $this->status_error;
//    }
//
//    /**
//     * @param mixed $status_error
//     */
//    public function setStatusError($status_error): void
//    {
//        $this->status_error = $status_error;
//    }
//    /**
//     * @return mixed
//     */
//    public function getModuleCode()
//    {
//        return $this->moduleCode;
//    }
//
//    /**
//     * @param mixed $moduleCode
//     */
//    public function setModuleCode($moduleCode): void
//    {
//        $this->moduleCode = $moduleCode;
//    }
//
    /**
     * @return string
     */
    public function getModuleName(): string
    {
        return $this->moduleName;
    }

    /**
     * @param string $moduleName
     */
    public function setModuleName(string $moduleName): void
    {
        $this->moduleName = $moduleName;
    }
//
//    /**
//     * @return mixed
//     */
//    public function getBaseUrl()
//    {
//        return $this->baseUrl;
//    }
//
//    /**
//     * @param mixed $baseUrl
//     */
//    public function setBaseUrl($baseUrl): void
//    {
//        $this->baseUrl =  $baseUrl;
//    }
//
//    /**
//     * @return mixed
//     */
//    public function getHashActivateModule()
//    {
//        return $this->hashActivateModule;
//    }
//
//    /**
//     * @param mixed $hashActivateModule
//     */
//    public function setHashActivateModule($hashActivateModule): void
//    {
//        $this->hashActivateModule = $hashActivateModule;
//    }
//
//    /**
//     * @return mixed
//     */
//    public function getApiVersion()
//    {
//        return $this->apiVersion;
//    }
//
//    /**
//     * @param mixed $apiVersion
//     */
//    public function setApiVersion($apiVersion): void
//    {
//        $this->apiVersion = $apiVersion;
//    }
//
//    /**
//     * @return mixed
//     */
//    public function getClientId()
//    {
//        return $this->clientId;
//    }
//
//    /**
//     * @param mixed $clientId
//     */
//    public function setClientId($clientId): void
//    {
//        $this->clientId = $clientId;
//    }
//
//    /**
//     * @return mixed
//     */
//    public function getPathToLogo()
//    {
//        return $this->pathToLogo;
//    }
//
//    /**
//     * @param mixed $pathToLogo
//     */
//    public function setPathToLogo($pathToLogo): void
//    {
//        $this->pathToLogo = $pathToLogo;
//    }
//
//    /**
//     * @return mixed
//     */
//    public function getPathCreate()
//    {
//        return $this->pathCreate;
//    }
//
//    /**
//     * @param mixed $pathCreate
//     */
//    public function setPathCreate($pathCreate): void
//    {
//        $this->pathCreate = $pathCreate;
//    }
//
//    /**
//     * @return mixed
//     */
//    public function getPathApprove()
//    {
//        return $this->pathApprove;
//    }
//
//    /**
//     * @param mixed $pathApprove
//     */
//    public function setPathApprove($pathApprove): void
//    {
//        $this->pathApprove = $pathApprove;
//    }
//
//    /**
//     * @return mixed
//     */
//    public function getPathCancel()
//    {
//        return $this->pathCancel;
//    }
//
//    /**
//     * @param mixed $pathCancel
//     */
//    public function setPathCancel($pathCancel): void
//    {
//        $this->pathCancel = $pathCancel;
//    }
//
//    /**
//     * @return mixed
//     */
//    public function getPathRefund()
//    {
//        return $this->pathRefund;
//    }
//
//    /**
//     * @param mixed $pathRefund
//     */
//    public function setPathRefund($pathRefund): void
//    {
//        $this->pathRefund = $pathRefund;
//    }
//
//    /**
//     * @return mixed
//     */
//    public function getPathStatus()
//    {
//        return $this->pathStatus;
//    }
//
//    /**
//     * @param mixed $pathStatus
//     */
//    public function setPathStatus($pathStatus): void
//    {
//        $this->pathStatus = $pathStatus;
//    }
//
//    /**
//     * @return mixed
//     */
//    public function getCurrencies()
//    {
//        return json_decode($this->currencies,true);
//    }
//
//    /**
//     * @param mixed $currencies
//     */
//    public function setCurrencies($currencies): void
//    {
//        $this->currencies = json_encode($currencies);
//    }
//
//    /**
//     * @return mixed
//     */
//    public function getInvoiceType()
//    {
//        return json_decode($this->invoiceType,true);
//    }
//
//    /**
//     * @param mixed $invoiceType
//     */
//    public function setInvoiceType($invoiceType): void
//    {
//        $this->invoiceType = json_encode($invoiceType);
//    }
//
//    /**
//     * @return string[]
//     */
//    public function getAvailableCountries(): array
//    {
//        return json_decode($this->availableCountries, true);
//    }
//
//    /**
//     * @param string[] $availableCountries
//     */
//    public function setAvailableCountries(array $availableCountries): void
//    {
//        $this->availableCountries = json_encode($availableCountries);
//    }

    public function prepareModuleData(object $request) : object
    {
//        if (empty($request->request->get('shop_name'))){
//            $errors[] = ['shops' => 'не корректно заполнены данные по магазинам'];
//            $shop = [];
//        }else{
//            $shop = [];
//            foreach ($request->request->get('shop_name') as $key => $shopItem ):
//                if (!empty($shopItem['name']) && !empty($shopItem['code'])):
//                    $shop[$key]['code'] = $shopItem['code'];
//
//                    $shop[$key]['name'] = $shopItem['name'];
//                    $shop[$key]['active'] = !empty($shopItem['active']);
//                endif;
//            endforeach;
//            if(empty($shop)){
//                $errors[] = ['shops' => 'добавьте хотя бы один магазин'];
//            }
//        }

//        $this->setShopsLists($shop);
//        $this->setMode($request->request->get('mode'));
//        $this->setSandboxPublicKey($request->request->get('sandbox_public_key'));
//        $this->setSandboxPrivateKey($request->request->get('sandbox_private_key'));
//        $this->setShopId($request->request->get('shop_id'));
//        $this->setPublicKey($request->request->get('public_key'));
//        $this->setPrivateKey($request->request->get('private_key'));
//        $this->setEmailDefault($request->request->get('email_default'));
//        $this->setPhoneDefault($request->request->get('phone_default'));
//        $this->setFieldForSend($request->request->get('field_for_send'));
//        $this->setFieldForSendDefault($request->request->get('field_for_send_default'));
//        $this->setInvoiceLifetimeHours($request->request->get('invoice_lifetime_hours'));
//        $this->setDescription($request->request->get('description'));
//        $this->setCurrency('UAH');
//        $this->setPaymentType('liqpay');
//        $this->setPaymentStatusForCreateInvoice('invoice');
//        $this->setServerUrlUpdateStatus('/module/liqpay/status_update');
//        $this->setStatusSuccess('succeeded');
//        $this->setStatusPandidng('pending');
//        $this->setStatusError( 'canceled');
//        $this->setBaseUrl( 'https://'.$_SERVER['HTTP_HOST']);
        $this->setModuleName($request->request->get('module_name'));

    return $this;
    }
    public function updateModuleData($responseRetailCrm, $settings) : object
    {
        return new \stdClass();
    }


}