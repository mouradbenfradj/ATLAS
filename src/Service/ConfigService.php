<?php
namespace App\Service;

use App\Entity\Config;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Exception\SessionNotFoundException;
use Symfony\Component\HttpFoundation\Session\Session;

class ConfigService
{
    /**
     * manager
     *
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * Get manager
     *
     * @return  EntityManagerInterface
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * Set manager
     *
     * @param  EntityManagerInterface  $manager  manager
     *
     * @return  self
     */
    public function setManager(EntityManagerInterface $manager)
    {
        $this->manager = $manager;

        return $this;
    }
    /**
     * config
     *
     * @var Config
     */
    private $config;
    /**
     * __construct
     *
     * @param EntityManagerInterface $manager
     */
    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
        $this->config = $manager->getRepository(Config::class)->findOneBy(['active' => true]);
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

    /**
     * Adds a flash message to the current session for type.
     *
     * @throws \LogicException
     */
    protected function addFlash(string $type, $message): void
    {
        $session = new Session();
        try {
            $session->getFlashBag()->add($type, $message);
        } catch (SessionNotFoundException $e) {
            throw new \LogicException('You can not use the addFlash method if sessions are disabled. Enable them in "config/packages/framework.yaml".', 0, $e);
        }
    }
}
