<?php

namespace App\Service;

use DateTime;
use App\Entity\User;
use App\Entity\Horaire;
use App\Entity\Pointage;
use App\Service\DateService;
use App\Service\JourFerierService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class PointageGeneratorService
{

    /**
     * jourFerierService
     *
     * @var JourFerierService
     */
    private $jourFerierService;

    /**
     * em
     *
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * dateService
     *
     * @var DateService
     */
    private $dateService;

    /**
     * timeService
     *
     * @var TimeService
     */
    private $timeService;

    /**
     * horaireService
     *
     * @var HoraireService
     */
    private $horaireService;

    /**
     * pointageService
     *
     * @var PointageService
     */
    private $pointageService;

    /**
     * flash
     *
     * @var FlashBagInterface
     */
    private $flash;

    /**
     * @param EntityManagerInterface $em
     * @param JourFerierService $jourFerierService
     * @param DateService $dateService
     * @param TimeService $timeService
     * @param HoraireService $horaireService
     * @param PointageService $pointageService
     */
    public function __construct(
        EntityManagerInterface $em,
        PointageService $pointageService,
        JourFerierService $jourFerierService,
        DateService $dateService,
        TimeService $timeService,
        HoraireService $horaireService,
        FlashBagInterface $flash
    ) {
        $this->em = $em;
        $this->jourFerierService = $jourFerierService;
        $this->dateService = $dateService;
        $this->horaireService = $horaireService;
        $this->timeService = $timeService;
        $this->pointageService = $pointageService;
        $this->flash = $flash;
    }

    /**
     * dateInDB
     *
     * @param User $user
     */
    public function dateInDB(User $user)
    {
        return array_map(
            fn ($date): string => $date->getDate()->format('Y-m-d'),
            $this->em->getRepository(Pointage::class)->findBy(["employer" => $user])
        );
    }

    /**
     * fromXlsxFile
     *
     * @param [type] $spreadsheet
     * @param integer $userId
     * @return User
     */
    public function fromXlsxFile($spreadsheet, User $user): User
    {
        $nowDate = new DateTime();
        $horaires = [];
        $arrayDate = $this->dateInDB($user);
        foreach ($this->em->getRepository(Horaire::class)->findAll() as $horaire) {
            $horaires[$horaire->getHoraire()] = $horaire;
        }
        $sheetCount = $spreadsheet->getSheetCount();
        for ($i = 0; $i < $sheetCount; $i++) {
            $sheet = $spreadsheet->getSheet($i);
            $sheetData = $sheet->toArray(null, true, true, true);
            foreach ($sheetData as  $ligne) {
                if ($this->dateService->isDate($ligne['A']) and isset($horaires[$ligne['B']])) {
                    $dateString = $this->dateService->dateToStringY_m_d($ligne['A']);
                    $isJourFerier = $this->jourFerierService->isJourFerier($dateString);
                    $date = $this->dateService->dateString_d_m_Y_ToDateTime($ligne['A']);
                    if (
                        $isJourFerier
                        or
                        $ligne['C'] == 'CP'
                        or
                        $this->timeService->isTimeHi($ligne['C'])
                        or
                        $this->timeService->isTimeHi($ligne['D'])
                        or
                        in_array($ligne['K'], ['1', '1.5'])
                        or
                        $ligne['L']
                        or
                        $nowDate >= $date
                    ) {
                        $horaire = $horaires[$ligne['B']];
                        if (!$isJourFerier and !in_array($dateString, $arrayDate)) {
                            array_push($arrayDate, $dateString);
                            $user = $this->pointageService->addLigne($ligne, $user);
                        }
                    } else
                        $this->flash->add('danger ', 'ignored ligne ' . implode(" | ", $ligne));
                }
            }
        }
        return $user;
    }
}
