<?php

namespace App\Implement;

use App\Abstracts\AbstractFile;

class XlsxImpl extends AbstractFile
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
