<?php


use Doctrine\ORM\EntityManagerInterface;

interface JoursFerierInterface
{
    /**
     * __construct
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em);
    /**
     * getJourFeriers function
     *
     * @return string[]
     */
    public function getJourFeriers(): array;
}
