<?php

namespace App\Analytics\RetailCustomerHistoryBundle\Controller;

use App\Analytics\RetailCustomerHistoryBundle\Entity\Customers;
use App\Analytics\RetailCustomerHistoryBundle\Entity\Orders;
use App\Analytics\RetailCustomerHistoryBundle\Repository\CustomersRepository;
use App\Analytics\RetailCustomerHistoryBundle\Repository\OrdersRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;
use DOMDocument;
use PhpOffice\PhpSpreadsheet\Reader\Xml;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Writer\Exception;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\SettingsRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpKernel\KernelInterface;


class AnalyticsRetailCrmController extends AbstractController
{

    protected $settingsRepository;
    protected $customersRepository;
    protected $ordersRepository;
    protected $doctrine;

    public function __construct(SettingsRepository $settingsRepository,
                                CustomersRepository $customersRepository,
                                OrdersRepository $ordersRepository,
                                ManagerRegistry $doctrine
                               )
    {
        $this->settingsRepository = $settingsRepository;
        $this->customersRepository = $customersRepository;
        $this->ordersRepository = $ordersRepository;
        $this->doctrine = $doctrine;
    }

    /**
     * @Route("/module/analytics/createxls", name="create_analytic_xls")
     * @throws Exception
     */
    public function createAnalyticXls(Request $request)
    {
        if (!empty($fromDate = $request->request->get('start_date'))) {

        }
        if (!empty($toDate = $request->request->get('end_date'))) {

        }

        if (!empty($fromDate) || !empty($toDate)) {
            if (!empty($fromDate)) {
                $from = new \DateTime($fromDate);
            } else {
                $from = '1970-01-01'; /*если стартовой даты нет, берем время с эпохи UNIX*/
            }
            if (!empty($toDate)) {
                $to = new \DateTime($toDate);
            } else {
                $to = new \DateTime(); /*если конечной даты нет, берем за дату "сегодня"*/
            }

            if ($result = $this->queryByDate($from, $to)) {
                if ($result) {
                    $spreadsheet = new Spreadsheet();
                    $sheet = $spreadsheet->getActiveSheet();
                    $sheet->getDefaultRowDimension()->setRowHeight(25);
                    $sheet->getColumnDimension('A')->setAutoSize(true);
                    $sheet->getColumnDimension('B')->setAutoSize(true);
                    $sheet->getColumnDimension('C')->setAutoSize(true);
                    $sheet->getColumnDimension('D')->setAutoSize(true);
                    $sheet->getColumnDimension('E')->setAutoSize(true);
                    $sheet->getColumnDimension('F')->setAutoSize(true);
                    $sheet->getColumnDimension('G')->setAutoSize(true);
                    $sheet->getColumnDimension('H')->setAutoSize(true);
                    $sheet->getColumnDimension('I')->setWidth(42);
                    $sheet->getStyle('I')->getAlignment()->setWrapText(true);
                    $sheet->setCellValue('A1', 'ФИО')
                        ->setCellValue('B1', 'телефон')
                        ->setCellValue('C1', 'Email')
                        ->setCellValue('D1', 'город')
                        ->setCellValue('E1', 'адрес')
                        ->setCellValue('F1', 'Общее Кол-во заказов')
                        ->setCellValue('G1', 'Кол-во выполненных заказов')
                        ->setCellValue('H1', 'Сумма выполненных заказов')
                        ->setCellValue('I1', 'Товары в выполненных заказах');


                    $index = 2;
                    $customerId = null;
                    foreach ($result as $key => $order_item) {
                        if ($customerId != $order_item['customerId']) {
                            $address = $order_item['address'];
                            $customerId = $order_item['customerId'];
                            $offers = $this->getOffersForXls($order_item['offers']) ?? '';
                            $total_summ = $order_item['totalsumm'];
                            $total_orders = 1;

                            foreach ($result as $key_offer => $order_offer) {
                                if ($key !== $key_offer && $order_offer['customerId'] == $order_item['customerId']) {
                                    $offers .= $this->getOffersForXls($order_offer['offers']);
                                    $total_summ += $order_offer['totalsumm'];
                                    $total_orders++;
                                    if (!isset($address['city']) && isset($order_offer['address']['city'])) {
                                        $address['city'] = $order_offer['address']['city'];
                                    }
                                    if (!isset($address['text']) && isset($order_offer['address']['text'])) {
                                        $address['text'] = $order_offer['address']['text'];
                                    }
                                }
                            }
//var_dump($order_item);
                            $name = array_key_exists('firstName', $order_item) ? $order_item['firstName'] : '';
                            $lastN = array_key_exists('lastName', $order_item) ? $order_item['lastName'] : '';
                            $patronymic = array_key_exists('patronymic', $order_item) ? $order_item['patronymic'] : '';
                            $sheet
                                ->setCellValue('A' . $index, $name . ' ' . $lastN . ' ' . $patronymic)
                                ->setCellValue('B' . $index, implode(',', $order_item['phones']))
                                ->setCellValue('C' . $index, isset($order_item['email']) ? $order_item['email'] : '')
                                ->setCellValue('D' . $index, isset($address['city']) ? $address['city'] : '')
                                ->setCellValue('E' . $index, isset($address['text']) ? $address['text'] : '')
                                ->setCellValue('F' . $index, isset($order_item['ordersCount']) ? $order_item['ordersCount'] : '')
                                ->setCellValue('G' . $index, $total_orders)
                                ->setCellValue('H' . $index, $total_summ)
                                ->setCellValue('I' . $index, $offers);
                            $sheet->getStyle('B' . $index)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
                            $sheet->getStyle('H' . $index)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
                            $index++;
//                                echo $customerId;
                        }
                    }
                    $writer = new Xlsx($spreadsheet);
                    $writer->save('./analytics/' . date("Ymd") . '_analytics.xlsx');
                    $spreadsheet->disconnectWorksheets();
                    unset($spreadsheet);

                    return new JsonResponse(
                        '<a href="/analytics/' . date("Ymd") . '_analytics.xlsx">Скачать Xls</a>');
                }
            }

        }

        return new JsonResponse(
            'данных нет или в запросе ошибка, попробуйте еще раз');
//        return $this->redirectToRoute('default');

    }

    /**
     * @Route("/module/analytics/createxml", name="create_analytic_xml")
     * @throws Exception
     */
    public function createAnalyticXml(Request $request)
    {
        if (!empty($fromDate = $request->request->get('start_date'))) {

        }
        if (!empty($toDate = $request->request->get('end_date'))) {

        }
        if (!empty($fromDate) ||
            !empty($toDate)) {
            if (!empty($fromDate)) {
                $from = new \DateTime($fromDate);
            } else {
                $from = '1970-01-01'; /*если стартовой даты нет, берем время с эпохи UNIX*/
            }
            if (!empty($toDate)) {
                $to = new \DateTime($toDate);
            } else {
                $to = new \DateTime(); /*если конечной даты нет, берем за дату "сегодня"*/
            }

            if ($result = $this->queryByDate($from, $to)) {
                if ($result) {
                    //Creates XML string and XML document using the DOM
                    $doc = new DOMDocument('1.0', 'UTF-8');

                    $root = $doc->createElement('Клиенты');
                    $root = $doc->appendChild($root);


                    //add root == jukebox
                    //  $jukebox = $doc->appendChild($doc->createElement('Отчет'));


                    $customerId = null;
                    foreach ($result as $key => $order_item) {
                        if ($customerId != $order_item['customerId']) {
                            $address = $order_item['address'];
                            $customerId = $order_item['customerId'];
                            $offers = $this->getOffersForXls($order_item['offers']) ?? '';
                            $total_summ = $order_item['totalsumm'];
                            $total_orders = 1;
                            $orders_for_xml = [];
                            $index = 0;
                            if (count($order_item['offers']) > 0) {
                                $order_item['offers'] += [
                                    'o_id' => $order_item['orderId'],
                                    'totalsum' => $order_item['totalsumm'],
                                    'createdAt' => $order_item['createdAt']->format('d-m-Y H:i')
                                ];
                                $orders_for_xml[] = $order_item['offers'];
                            }


                            foreach ($result as $key_offer => $order_offer) {
                                if ($key !== $key_offer && $order_offer['customerId'] == $order_item['customerId']) {
                                    $offers .= $this->getOffersForXls($order_offer['offers']);
                                    if (count($order_offer['offers']) > 0) {
                                        $order_offer['offers'] += [
                                            'o_id' => $order_item['orderId'],
                                            'totalsum' => $order_item['totalsumm'],
                                            'createdAt' => $order_item['createdAt']->format('d-m-Y H:i')
                                        ];
                                        $orders_for_xml[]= $order_offer['offers'];
                                    }
                                    $total_summ += $order_offer['totalsumm'];
                                    $total_orders++;
                                    if (!isset($address['city']) && isset($order_offer['address']['city'])) {
                                        $address['city'] = $order_offer['address']['city'];
                                    }
                                    if (!isset($address['text']) && isset($order_offer['address']['text'])) {
                                        $address['text'] = $order_offer['address']['text'];
                                    }
                                }
                            }

                            $name = array_key_exists('firstName', $order_item) ? $order_item['firstName'] : '';
                            $lastN = array_key_exists('lastName', $order_item) ? $order_item['lastName'] : '';
                            $patronymic = array_key_exists('patronymic', $order_item) ? $order_item['patronymic'] : '';

                            $alert = $doc->createElement('Клиент');
                            $alert = $root->appendChild($alert);
                            //добавление ид
                            $id = $doc->createElement('Ид');
                            $id_text = $doc->createTextNode($customerId);
                            $id->appendChild($id_text);
                            $alert->appendChild($id);
                            //добавление ФИО
                            $fio = $doc->createElement('ФИО');
                            $fio_text = $doc->createTextNode($name . ' ' . $lastN . ' ' . $patronymic);
                            $fio->appendChild($fio_text);
                            $alert->appendChild($fio);
                            //добавление телефонов
                            if (isset($order_item['phones']) && count($order_item['phones']) > 0) {
                                $phones = $doc->createElement('Телефоны');
                                $phones_text = $doc->createTextNode(implode(',', $order_item['phones']));
                                $phones->appendChild($phones_text);
                                $alert->appendChild($phones);
                            }
                            //add Email
                            if (isset($order_item['email'])) {
                                $email = $doc->createElement('Email');
                                $email_text = $doc->createTextNode($order_item['email']);
                                $email->appendChild($email_text);
                                $alert->appendChild($email);
                            }
                            //address
                            $addr = $doc->createElement('Адрес');
                            $address_info = isset($address['city']) ? $address['city'] : '';
                            $address_info1 = isset($address['text']) ? $address['text'] : '';
                            $addr_text = $doc->createTextNode($address_info . ': ' . $address_info1);
                            $addr->appendChild($addr_text);
                            $alert->appendChild($addr);
                            //ii
                            $orders = $doc->createElement('Заказы');
                            //Attribute to orders
                            $col = $doc->createAttribute('Количество');
                            $col->appendChild($doc->createTextNode($total_orders));
                            $orders->appendChild($col);
                            $ts = $doc->createAttribute('ОбщаяСумма');
                            $ts->appendChild($doc->createTextNode($total_summ));
                            $orders->appendChild($ts);
                            //Add orders to Clustomer
                            if (count($orders_for_xml) > 0) {
                                foreach ($orders_for_xml as $key_ofx => $ofx) {

                                    $order = $doc->createElement('Заказ');

                                    $order_i = $doc->createElement('Ид');
                                    $order_i->appendChild($doc->createTextNode($ofx['o_id']));
                                    $order->appendChild($order_i);

                                    $t_s = $doc->createElement('Сумма');
                                    $t_s->appendChild($doc->createTextNode($ofx['totalsum']));
                                    $order->appendChild($t_s);

                                    $c_at = $doc->createElement('ДатаСоздания');
                                    $c_at->appendChild($doc->createTextNode($ofx['createdAt']));
                                    $order->appendChild($c_at);

                                    foreach ( array_keys($ofx) as $ofx_items) {
                                        if (is_int($ofx_items)){
                                            $offer = $doc->createElement('Товар');
                                            if (isset($ofx[$ofx_items]['xmlId']) && strlen($ofx[$ofx_items]['xmlId'])>2){
                                                $xmlId = $doc->createElement('xmlId');
                                                $xmlId->appendChild($doc->createTextNode($ofx[$ofx_items]['xmlId']));
                                                $offer->appendChild($xmlId);
                                            }
                                            if (isset($ofx[$ofx_items]['quantity'])){
                                                $quantity = $doc->createElement('Количество');
                                                $quantity->appendChild($doc->createTextNode($ofx[$ofx_items]['quantity']));
                                                $offer->appendChild($quantity);
                                            }
                                            if (isset($ofx[$ofx_items]['externalId'])){
                                                $externalId = $doc->createElement('externalId');
                                                $externalId->appendChild($doc->createTextNode($ofx[$ofx_items]['externalId']));
                                                $offer->appendChild($externalId);
                                            }
                                            if (isset($ofx[$ofx_items]['displayName'])){
                                                $t_s = $doc->createElement('Название');
                                                $t_s->appendChild($doc->createTextNode($ofx[$ofx_items]['displayName']));
                                                $offer->appendChild($t_s);
                                            }
                                            $order->appendChild($offer);
                                        }
                                    }
                                    $orders->appendChild($order);
                                }
                            }
                            $alert->appendChild($orders);
                        }
                    }
                    $doc->formatOutput = true; // set the formatOutput attribute of domDocument to true
                    // save XML as string or file
                    $test1 = $doc->saveXML(); // put string in test1
                    $doc->save('./analytics/xml/' . date("Ymd") . '_analytics.xml'); // save as file
                    return new JsonResponse(
                        '<a href="/analytics/xml/' . date("Ymd") . '_analytics.xml">Скачать XML</a>');
                }
            }
        }

        return new JsonResponse('данных нет или в запросе ошибка, попробуйте еще раз');

//        return new JsonResponse(
//            '<a href="/analytics/'.date("Ymd").'_analytics.xlsx">Download Xls File</a>');
    }

    private function queryByDate($from=null,$to=null)
    {
        $entityManager = $this->doctrine->getManager();
        $qb = $entityManager->createQueryBuilder();
        $qb->select(['o.totalsumm',
            'o.orderId',
            'o.createdAt',
            'o.customerId',
            'o.offers',
            'c.clientIdinRetailCrm',
            'c.firstName',
            'c.lastName',
            'c.patronymic',
            'c.email',
            'c.phones',
            'c.city',
            'c.address',
            'c.ordersCount',

        ])
            ->from(Orders::class, 'o')
            ->join(Customers::class, 'c', Join::WITH, 'o.customerId = c.clientIdinRetailCrm')
            ->where("o.createdAt BETWEEN :from AND :to ")
            ->setParameter('to', $to)
            ->setParameter('from', $from)
            //->groupBy('o.customerId')
            ->orderBy('o.customerId', 'DESC');

        //file_put_contents('select_query.txt', print_r($qb,true));
        $query = $qb->getQuery();
        return $query->getResult();

    }

    private function getOffersForXls(array $offers)
    {
        $result=NULL;
        foreach ($offers as $offer){
            if (isset($offer['displayName'])){
                $result.='; '.$offer['displayName'];
            }
        }
        return $result;
    }
//    private function getOffersForXml(array $offers)
//    {
//        $result=NULL;
//        foreach ($offers as $offer){
//            if (isset($offer['displayName'])){
//                $result[]=$offer['displayName'];
//            }
//        }
//        return $result;
//    }

    /**
     * @Route("/module/analytics/gethistory/{varid}", name="get_history_retail")
     * @throws Exception
     */
    public function getHistoryRetail(string $varid, KernelInterface $kernel):Response
    {
//        var_dump($varid);
        $application = new Application($kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput([
            'command' => 'app:run-get-history',
            // (optional) define the value of command arguments
            'variantId' => $varid,
            // (optional) pass options to the command

        ]);

        // You can use NullOutput() if you don't need the output
        $output = new BufferedOutput();
        $application->run($input, $output);

        // return the output, don't use if you used NullOutput()
        $content = $output->fetch();

        // return new Response(""), if you used NullOutput()
        return new Response($content);


    }
}

