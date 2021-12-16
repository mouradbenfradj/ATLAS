<?php
namespace App\Traits;

use App\Entity\JourFerier;
use DateInterval;

trait JourFerierTrait
{
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
