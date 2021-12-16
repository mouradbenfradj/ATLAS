<?php
namespace App\Traits;

use App\Entity\Horaire;

trait HoraireTrait
{
    /**
    * Horaire
    *
    * @var Horaire
    */
    private $horaire;

    /**
     * Get horaire
     *
     * @return  Horaire
     */
    public function getHoraire()
    {
        return $this->horaire;
    }

    /**
     * Set horaire
     *
     * @param  Horaire  $horaire  horaire
     *
     * @return  self
     */
    public function setHoraire(Horaire $horaire)
    {
        $this->horaire = $horaire;

        return $this;
    }
}
