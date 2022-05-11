<?php

namespace App\Payments\LiqPayRetailCrmBundle\Service;

use Psr\Log\LoggerInterface;

class Logger
{
	private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function getHappyMessage()
    {
        $this->logger->info('About to find a happy message!');
        // ...
        var_dump('expression');die;
    }

    public function setArticles($articles)
    {
        $str = implode(' , ', $articles);
        $this->logger->info('Не соответсвуют артикула в Тильде - ' . $str);
        // ...
        var_dump('expression');die;
    }
}