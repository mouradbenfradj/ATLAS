<?php

declare(strict_types=1);

namespace App\Implement;

use App\Abstracts\AbstractFile;
use App\Entity\Horaire;

class PointageImpl extends AbstractFile
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
     * @return  Horaire|null
     */
    public function getHoraire()
    {
        return $this->horaire;
    }

    /**
     * Set horaire
     *
     * @param  Horaire|null  $horaire  horaire
     *
     * @return  self
     */
    public function setHoraire($horaire)
    {
        $this->horaire = $horaire;

        return $this;
    }
}
