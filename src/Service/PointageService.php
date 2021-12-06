<?php
namespace App\Service;

use App\Entity\Pointage;

class PointageService extends EmployerService
{
    /**
     * pointage
     *
     * @var Pointage
     */
    private $pointage;

    /**
     * Get pointage
     *
     * @return  Pointage
     */
    public function getPointage()
    {
        return $this->pointage;
    }

    /**
     * Set pointage
     *
     * @param  Pointage  $pointage  pointage
     *
     * @return  self
     */
    public function setPointage(Pointage $pointage)
    {
        $this->pointage = $pointage;

        return $this;
    }
}
