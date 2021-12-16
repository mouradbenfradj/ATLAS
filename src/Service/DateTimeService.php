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
    }
    /**
     * DateString_d_m_Y_ToDateTime
     *
     * @param string $dateString
     * @return DateTime|null
     */
    public function dateString_d_m_Y_ToDateTime(string $dateString): ?DateTime
    {
        if (DateTime::createFromFormat(self::FORMATDATEDMY, $dateString) !== false) {
            return DateTime::createFromFormat(self::FORMATDATEDMY, $dateString);
        }
        return null;
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
     * @return DateTime
     */
    public function diffTime(DateTime $timeMax, DateTime $timeMin): DateTime
    {
        $timeMax = new DateTime(date(self::FORMATTIMEHIS, strtotime($timeMax->format(self::FORMATTIMEHIS))));
        $timeMin = new DateTime(date(self::FORMATTIMEHIS, strtotime($timeMin->format(self::FORMATTIMEHIS))));

        $diff =  date_diff($timeMax, $timeMin);
        return new DateTime($diff->h . ':' . $diff->i . ':' . $diff->s);
    }
}
