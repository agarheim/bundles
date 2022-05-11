<?php


namespace App\Service;

//use http\Exception;
use Psr\Log\LoggerInterface;
use \RetailCrm\ApiClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;


class RetailCrm  extends AbstractController
{
    protected $apiClient;
    protected $logger;

    public function __construct(LoggerInterface $logger)
    {
       // $this->settings = $this->getParameter('retailCrm');
        $this->logger = $logger;
    }

    public function setModule(array $settings)
    {
       $apiClient = $this->getApiClient($settings['dataForApi']);
        try {
            $response = $apiClient->request->integrationModulesEdit($settings['dataForModule']);
        } catch (\RetailCrm\Exception\CurlException $e) {
            throw new AccessDeniedException($e->getMessage());
        }
        return $response;
    }

    private function getApiClient($settings) {
        if (empty($settings['apiUrl']) || empty($settings['apiKey'])) {
            return false;
        }
        try {
             return new ApiClient($settings['apiUrl'], $settings['apiKey'], $settings['apiVersion']);
        } catch (Exception $e) {
          return $e->getMessage();
        }
    }
    public function getResponse($response)
    {
        return '<p>Модуль активирован <br>status: '.$response->success.'<br>Info: '.print_r($response);

    }







}