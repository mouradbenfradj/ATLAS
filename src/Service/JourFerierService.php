<?php
namespace App\Service;

use App\Entity\JourFerier;
use DateInterval;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class JourFerierService extends DateTimeService
{
    public function __construct(EntityManagerInterface $manager)
    {
        parent::__construct($manager)        ;
    }
    /**
     * GetJourFeriers
     *
     * @return array
     */
    public function getJourFeriers(): array
    {
        $ignoreDay = [];
        foreach ($this->getManager()->getRepository(JourFerier::class)->findAll() as $jf) {
            do {
                array_push($ignoreDay, $jf->getDebut()->format("Y-m-d"));
                /**
                 * @var DateTime
                 */
                $debut = $jf->getDebut();
                $debut->add(new DateInterval('P1D'));
            } while ($jf->getDebut() <= $jf->getFin());
        }
        return $ignoreDay;
    }
}
