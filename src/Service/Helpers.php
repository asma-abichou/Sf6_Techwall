<?php

namespace App\Service;

class Helpers
{

    private $logger;
    public function __construct($logger)
    {
    }
    public function sayCC():String{
        $this->logger->info("je dis cc");
        return "cc";

    }

}