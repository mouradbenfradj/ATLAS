<?php

namespace App\Service;

use App\Entity\Config;
use Doctrine\ORM\EntityManagerInterface;

class ConfigService
{
    private $config;
    public function __construct(EntityManagerInterface $em)
    {
        $this->config = $em->getRepository(Config::class)->findOneBy(['active' => true]);
    }

    /**
     * Get the value of config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Set the value of config
     *
     * @return  self
     */
    public function setConfig($config)
    {
        $this->config = $config;

        return $this;
    }
}
