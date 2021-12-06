<?php


use DateTime;

class DateTimeService implements DateInterface, TimeInterface
{
    /* private $jourFerierService;
    private $horaireService;
    public function __construct(HoraireService $horaireService, JourFerierService $jourFerierService)
    {
        $this->jourFerierService = $jourFerierService;
        $this->horaireService = $horaireService;
    } */
    /**
     * dateString_d_m_Y_ToDateTime
     *
     * @param string $dateString
     * @return DateTime
     */
    public function dateString_d_m_Y_ToDateTime(string $dateString): DateTime
    {
        return DateTime::createFromFormat('d/m/Y', $dateString);
    }
    /**
     * generateTime
     *
     * @param string $timeString
     *
     * @return DateTime
     */
    public function generateTime(string $timeString): DateTime
    {
        if ($timeString != "" and (DateTime::createFromFormat('H:i:s', $timeString) !== false or DateTime::createFromFormat('H:i', $timeString) !== false)) {
            return new DateTime($timeString);
        } else {
            return new DateTime("00:00:00");
        }
    }
}
