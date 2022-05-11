<?php


namespace App\Controller;


//use App\Payments\LiqPayRetailCrmBundle\PaymentsLiqPayRetailCrmBundle;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    /**
     * @Route("/test", name="test")

     * @return Response
     */
    public function test()
    {
       // echo __DIR__;
       // $f=var_dump($_GET).'fdsgsdfgsdfgsdfgsdfgsdfg';
        file_put_contents('file.txt', json_encode($_POST).json_encode($_GET));

//        $handler = fopen('file.txt', "w+");
//        $a = var_export($_GET);
//        fwrite($handler, var_dump('sdfgsdfgsdfgsdfgs'));
//        fclose($handler);
        // set post fields
//        $post = [
//            'v' => '1',
//            'tid' => 'UA-178549849-1',
//            'cid'   => '140488208.1600941595',
//            't'   => 'event',
//            'ec'   => 'CRM',
//            'ea'   => 'Success_order',
//            'el'   => '239',
//            'ev'   => 16589,
//        ];
//
//        $ch = curl_init();
//        curl_setopt($ch,CURLOPT_URL,'http://roman.tetragon.com.ua/show.php');//https://www.google-analytics.com/debug/collect');
//        curl_setopt($ch,CURLOPT_POST, 1);                //0 for a get request
//        curl_setopt($ch,CURLOPT_POSTFIELDS,http_build_query($post));
//        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
//        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT ,3);
//        curl_setopt($ch,CURLOPT_TIMEOUT, 20);
//        $response = curl_exec($ch);
//        print "curl response is:" . $response;
//        curl_close ($ch);

 //execute!
//       $response = curl_exec($ch);

// close the connection, release resources used
 //       curl_close($ch);

// do anything you want with your response
 //       var_dump($response);

        return new Response('var_dump($response)');
    }

}