<?php


use App\Entity\Config;
use Doctrine\ORM\EntityManagerInterface;

class ConfigService
{
    /**
     * config
     *
     * @var Config
     */
    private $config;
    /**
     * __construct
     *
     * @param EntityManagerInterface $em
     */
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
