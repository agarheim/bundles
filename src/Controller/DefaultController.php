<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="default")
     */
    public function index()
    {
        if ($this->getUser()===null) {
            return $this->redirectToRoute('app_login');
        }
        return $this->redirectToRoute('account_modules_list');
        // return $this->render('default/index.html.twig',
        //     [
        //         'products'=> 'Products our company'
        //     ]);
        //можно и так ответ отправить
        //return new Response('Hello, World!');
    }

}