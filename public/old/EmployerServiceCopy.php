<?php


use App\Entity\User;
use DateInterval;
use DateTime;

class EmployerServiceCopy
{
    private $configService;
    private $dateService;
    private $timeService;

    public function __construct(ConfigService $configService, DateService $dateService, TimeService $timeService)
    {
        $this->configService = $configService;
        $this->dateService = $dateService;
        $this->timeService = $timeService;
    }
    public function calculerSoldConger(User $employer)
    {
        $nowDay = new DateTime();
        $debutSoldConger = $this->configService->getConfig()->getDebutSoldConger();
        $incConger = $this->configService->getConfig()->getIncSoldConger();
        $perimierJourDeTravaille = new DateTime($employer->getDebutTravaille()->format("Y-m-d"));
        $perimierJourDeTravaille->modify("first day of this month");
        while ($perimierJourDeTravaille < $nowDay) {
            $debutSoldConger += $incConger;
            $perimierJourDeTravaille->modify('+1 month');
        }

        foreach ($employer->getCongers() as $conger) {
            if ($conger->getDemiJourner())
                $debutSoldConger -= 0.5;
            else
                $debutSoldConger -= 1;
        }

        return $debutSoldConger;
    }
    public function calculerAS(User $employer)
    {
        $nowDay = new DateTime();
        $debutSoldAS = $this->configService->getConfig()->getDebutSoldAS();
        $incAS = $this->configService->getConfig()->getIncAutorisationSortie();
        $perimierJourDeTravaille = new DateTime($employer->getDebutTravaille()->format("Y-m-d"));
        $perimierJourDeTravaille->modify("first day of january this year");
        while ($perimierJourDeTravaille <= $nowDay) {
            $perimierJourDeTravaille->modify('+1 year');
        }
        $perimierJourDeTravaille->modify('-1 year');
        /**
         *
         */
        foreach ($employer->getAutorisationSorties() as $as) {
            if ($perimierJourDeTravaille <= $as->getDateAutorisation()) {
                $diffAs = $this->timeService->dateTimeToDateInterval($as->getHeurAutoriser());
                $debutSoldAS->sub($diffAs);
            }
        }
        while ($perimierJourDeTravaille < $nowDay) {
            $debutSoldAS->add($this->timeService->dateTimeToDateInterval($incAS));
            $perimierJourDeTravaille->modify('+1 month');
        }
        return $debutSoldAS;
    }
}
