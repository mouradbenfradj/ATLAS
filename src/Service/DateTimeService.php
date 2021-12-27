<?php
namespace App\Service;

use App\Interfaces\DateInterface;
use App\Interfaces\TimeInterface;
use App\Traits\JourFerierTrait;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class DateTimeService extends ConfigService implements DateInterface, TimeInterface
{
    use JourFerierTrait;
    const FORMATTIMEHI = 'H:i';
    const FORMATTIMEHIS = self::FORMATTIMEHI . ':s';
    const FORMATDATEDMY = 'd/m/Y';

    public function __construct(EntityManagerInterface $manager)
    {
        parent::__construct($manager)        ;
    }/**
     * DateString_m_d_Y_ToDateTime
     *
     * @param string $dateString
     * @return DateTime
     */
    public function dateString_m_d_Y_ToDateTime(string $dateString): DateTime
    {
        return DateTime::createFromFormat('m/d/Y', $dateString);
    }

    /**
     * IsDate
     *
     * @param string|null $dateString
     * @return boolean
     */
    public function isDate(?string $dateString): bool
    {
        return DateTime::createFromFormat(self::FORMATDATEDMY, $dateString) !== false;
    }

    /**
     * TimeStringToDateTime
     *
     * @param string|null $timeString
     * @return DateTime|null
     */
    public function timeStringToDateTime(?string $timeString): ?DateTime
    {
        $time = null;
        if (DateTime::createFromFormat(self::FORMATTIMEHIS, $timeString) !== false) {
            $time = DateTime::createFromFormat(self::FORMATTIMEHIS, $timeString);
        } elseif (DateTime::createFromFormat(self::FORMATTIMEHI, $timeString) !== false) {
            $time = DateTime::createFromFormat(self::FORMATTIMEHI, $timeString);
        }
        return $time;
    }

    /**
     * GenerateTime
     *
     * @param string $timeString date generer du dbf
     *
     * @return DateTime
     */
    public function generateTime(?string $timeString): DateTime
    {
        if ($timeString != "" && (DateTime::createFromFormat(self::FORMATTIMEHIS, $timeString) !== false || DateTime::createFromFormat(self::FORMATTIMEHI, $timeString) !== false)) {
            return new DateTime($timeString);
        } else {
            return new DateTime("00:00:00");
        }
    }
    
    /**
     * DiffTime
     *
     * @param DateTime $timeMax
     * @param DateTime $timeMin
     * @return int
     */
    public function diffTime(DateTime $timeMax, DateTime $timeMin): int
    {
        $max = (($timeMax->format('H')*60)*60) + ($timeMax->format('i')*60) + $timeMax->format('s');
        $min = (($timeMin->format('H')*60)*60) + ($timeMin->format('i')*60) + $timeMin->format('s');
        return  $max -  $min;
    }
    public function dateIntervalToSeconds($interval)
    {
        $seconds = $interval->h*3600
       + $interval->i*60 + $interval->s;
        return $seconds;
    }
}
