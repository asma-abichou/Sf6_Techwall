<?php

namespace App\Service;

use App\Entity\User;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class Helpers
{

    public function __construct(private LoggerInterface $logger, Security $security) {
    }
    public function sayCc(): string {
        $this->logger->info('Je dis cc');
        return 'cc';
    }
    public function getUser():User
    {
        return $this->security->getUser();
    }

}