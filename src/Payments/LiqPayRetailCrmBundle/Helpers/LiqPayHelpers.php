<?php


namespace App\Payments\LiqPayRetailCrmBundle\Helpers;

use App\Payments\LiqPayRetailCrmBundle\Entity\LiqpayOrders;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;


class LiqPayHelpers
{
    private $entityManager;
    /**
     * @var mixed
     */
    private $dataForLiqPay;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return mixed
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @param mixed $errors
     */
    public function setErrors($errors): void
    {
        $this->errors = $errors;
    }
    private $errors;

    public function setDataForLiqPay(array $dataForLiqPay,object $settings):void
    {
        //  file_put_contents('dataforliqpay.txt',print_r($dataForLiqPay,true));
        $settingsLiqPay = $settings->getSettings();
        if ( $dataForLiqPay['shopId']==''
            ||$dataForLiqPay['amount'] <= 0){
            $this->setErrors(['нет настроек для данного магазина или сумма к оплате равна нулю']);
        }

        //list for pay
        $goods = [];

        foreach ($dataForLiqPay['items'] as $item) {

            $good['name'] = isset($item['name'])?$item['name']:'default';
            $good['count'] = isset($item['quantity'])?$item['quantity']:0;
            $good['unit'] = isset($item['measurementUnit'])?$item['measurementUnit']:'шт';
            $good['amount'] = isset($item['price'])?$item['price']:0;
            $goods[] = $good;
        }
        // file_put_contents('dataforliqpay.txt', print_r($goods,true),true);
        $expiredDate = new \DateTime('now');

        $expiredDate->modify('+' . $settingsLiqPay['invoice_lifetime_hours'] . ' hour');

        $description = $settingsLiqPay['description'] . " №"
            . $dataForLiqPay['orderNumber'];

        $dataForCreatePay = [
            'action'    => 'invoice_send',
            'version'   => '3',
            'description' => $description,
            'amount'    => $dataForLiqPay['amount'],
            'currency'  => $settingsLiqPay['currency'],
            'order_id'  => $dataForLiqPay['invoiceUuid'],
            'goods'     => $goods,
            'expired_date' => $expiredDate->format('Y-m-d H:i:s'),
            'server_url' => $settingsLiqPay['baseUrl'].$settingsLiqPay['server_url_update_status'].'/'.$settings->getVariantId(),
        ];

        $fieldForSend = $this->getFieldForSend($settingsLiqPay, $dataForLiqPay['customer']);

        $dataForCreatePay[$fieldForSend['name']] = $fieldForSend['value'];

        $public_key = $settingsLiqPay['public_key'];
        $private_key = $settingsLiqPay['private_key'];

        if ($settingsLiqPay['mode'] == 'test') {
            $public_key = $settingsLiqPay['sandbox_public_key'];
            $private_key = $settingsLiqPay['sandbox_private_key'];
        }

        $dataForLiqPay = [
            'shopId' => $dataForLiqPay['shopId'],
            'externalId' => $dataForLiqPay['orderNumber'],
            'orderId' => $dataForLiqPay['orderId'],
            'public_key' => $public_key,
            'private_key' => $private_key,
            'dataForCreatePay'=>$dataForCreatePay
        ];

        $this->dataForLiqPay = $dataForLiqPay;

    }

    public function getFieldForSend($liqpaySettings, $order): array
    {
        $field['name'] = $liqpaySettings['field_for_send'];
        $field['value'] = $liqpaySettings[$liqpaySettings['field_for_send'] . '_default'];
        //file_put_contents('orderobj.txt',print_r($order,true));
        // $order=array($order);

        if ($liqpaySettings['field_for_send'] && isset($order[$liqpaySettings['field_for_send']]) &&
            !empty($order[$liqpaySettings['field_for_send']])) {
            $field['name'] = $liqpaySettings['field_for_send'];
            $field['value'] = $order[$liqpaySettings['field_for_send']];
        }
        //   file_put_contents('order.txt',print_r($order[$liqpaySettings['field_for_send']],true));
        return $field;
    }

    public function getDataForLiqPay()
    {
        return $this->dataForLiqPay;
    }

    public function checkCreateInvoiceByOrderId($retailCrmUuid): ?object
    {
        $repositoryLiqpayOrders = $this->entityManager->getRepository(LiqpayOrders::class);
        $liqpayOrder = $repositoryLiqpayOrders->findOneBy(['orderId' => $retailCrmUuid//,
        ]);
        return $liqpayOrder;
    }

    public function checkCreateInvoiceByUuid($retailCrmUuid)
    {
        $repositoryLiqpayOrders = $this->entityManager->getRepository(LiqpayOrders::class);
        $liqpayOrder =  $repositoryLiqpayOrders->findOneBy([
            'paymentId' => $retailCrmUuid//,
        ]);
        return  $liqpayOrder ;
    }

    public function createLiqPayOrders($responseLiqPay, $retailCrmData)
    {
        $liqpayOrders = new LiqpayOrders();
        $liqpayOrders->setLinkInvoice($responseLiqPay->href);
        $liqpayOrders->setSite($retailCrmData['shopId']);
        $liqpayOrders->setPaymentId($retailCrmData['dataForCreatePay']['order_id']);
        $liqpayOrders->setStatus($responseLiqPay->status);
        $liqpayOrders->setNumber($retailCrmData['externalId']);
        $liqpayOrders->setOrderId($retailCrmData['orderId']);
        $liqpayOrders->setToken($responseLiqPay->token);

        // tell Doctrine you want to (eventually) save the Product (no queries yet)
        $this->entityManager->persist($liqpayOrders);

        // actually executes the queries (i.e. the INSERT query)
        $this->entityManager->flush();
        return $liqpayOrders->getId();
    }

    public function getInvoiceDataById($retailCrmId)
    {
        $repositoryLiqpayOrders = $this->entityManager->getRepository(LiqpayOrders::class);
        $liqpayOrder = $repositoryLiqpayOrders->find($retailCrmId);
        //file_put_contents('liqpayordercancel.txt', print_r($liqpayOrder,TRUE));
        return $liqpayOrder;
    }

    public function updateStatusInvoice($liqpayOrders)
    {
        // tell Doctrine you want to (eventually) save the Product (no queries yet)
        $this->entityManager->persist($liqpayOrders);
        // actually executes the queries (i.e. the INSERT query)
        $this->entityManager->flush();
        return $liqpayOrders;
    }

    public function checkSignatureLiqPay($liqpaySettings, $data, $signatureData)
    {
        // $public_key = $liqpaySettings[$site]['public_key'];
        $private_key = $liqpaySettings['private_key'];

        if ($liqpaySettings['mode'] == 'test') {
            //      $public_key = $liqpaySettings['sandbox_public_key'];
            $private_key = $liqpaySettings['sandbox_private_key'];
        }
        $signature = base64_encode( sha1(
            $private_key .
            $data .
            $private_key
            , 1 ));
        if ($signature !== $signatureData) {
            throw new AccessDeniedHttpException();
        }

        $data = base64_decode($data);
        $data = json_decode($data);
        return $data;
    }
    public function getLiqPayParameters($id,$shopId){
        $settings = $this->entityManager->getRepository('App:Settings')->findOneBy(['clientId'=>$id,'variantId'=>$shopId]);
        if ($settings === null) {
            file_put_contents('dataforliqpay.txt',print_r('В БД нет настроект для данного магазина',true));
        }
        return $settings->getSettings();

    }
}