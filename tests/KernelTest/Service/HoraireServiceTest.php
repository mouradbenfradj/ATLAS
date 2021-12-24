<?php

namespace App\Tests\KernelTest\Service;

use App\Service\HoraireService;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class HoraireServiceTest extends KernelTestCase
{
    const FORMAT_DATETIME_D_M_Y_H_I_S =  'Y-m-d H:i:s';
    const MESSAGE_USED_DATE =  'for date ';
    const INC1DAY =  '+1 day';
    const RAMADAN= [
        ['2018-05-16 00:00:00','2018-06-14 00:00:00'],
        ['2019-05-05 00:00:00','2019-06-03 00:00:00'],
        ['2020-04-23 00:00:00','2020-05-23 00:00:00'],
        ['2021-04-13 00:00:00','2021-05-14 00:00:00']
    ];
    const SU= [
        ['2019-07-01 00:00:00','2019-08-31 00:00:00'],
        ['2020-07-01 00:00:00','2020-08-31 00:00:00'],
        ['2021-07-01 00:00:00','2021-08-31 00:00:00']
    ];
    public function testGetHoraireForDateNORMAL(): void
    {
        $date = new DateTime('2018-04-02');
        $datefin = new DateTime('2021-12-01');
        $container = static::getContainer();
        /**
         * @var HoraireService
         */
        $horaireService = $container->get(HoraireService::class);
        do {
            $test = true;
            for ($i = 0; $i < count(self::RAMADAN);$i++) {
                $debut = DateTime::createFromFormat(self::FORMAT_DATETIME_D_M_Y_H_I_S, self::RAMADAN[$i][0]);
                $fin =  DateTime::createFromFormat(self::FORMAT_DATETIME_D_M_Y_H_I_S, self::RAMADAN[$i][1]);
                if (($date>= $debut)&& ($date<=$fin)) {
                    $test = false;
                }
            }
         
            for ($i = 0; $i < count(self::SU);$i++) {
                $debut = DateTime::createFromFormat(self::FORMAT_DATETIME_D_M_Y_H_I_S, self::SU[$i][0]);
                $fin =  DateTime::createFromFormat(self::FORMAT_DATETIME_D_M_Y_H_I_S, self::SU[$i][1]);
                if (($date>= $debut) && ($date<=$fin)) {
                    $test = false;
                }
            }
            if ($test) {
                $horaire = $horaireService->getHoraireForDate($date);
                $this->assertNotNull($horaire, self::MESSAGE_USED_DATE.$date->format(self::FORMAT_DATETIME_D_M_Y_H_I_S));
                $this->assertEquals(
                    'NORMAL',
                    $horaire->getHoraire(),
                    self::MESSAGE_USED_DATE.$date->format(self::FORMAT_DATETIME_D_M_Y_H_I_S).
                ' horaire '.$horaire->getHoraire()
                );
            }
            $date->modify(self::INC1DAY);
        } while ($date <= $datefin) ;
    }

    public function testGetHoraireForDateRAMADAN(): void
    {
        $date = new DateTime('2018-04-01');
        $datefin = new DateTime('2022-04-01');
        $container = static::getContainer();
        /**
         * @var HoraireService
         */
        $horaireService = $container->get(HoraireService::class);
        do {
            for ($i = 0; $i < count(self::RAMADAN);$i++) {
                $debut = DateTime::createFromFormat(self::FORMAT_DATETIME_D_M_Y_H_I_S, self::RAMADAN[$i][0]);
                $fin =  DateTime::createFromFormat(self::FORMAT_DATETIME_D_M_Y_H_I_S, self::RAMADAN[$i][1]);
                if (($date>= $debut)&& ($date<=$fin)) {
                    $horaire = $horaireService->getHoraireForDate($date);
                    $this->assertNotNull($horaire, self::MESSAGE_USED_DATE.$date->format(self::FORMAT_DATETIME_D_M_Y_H_I_S));
                    $this->assertEquals(
                        'RAMADAN',
                        $horaire->getHoraire(),
                        self::MESSAGE_USED_DATE.$date->format(self::FORMAT_DATETIME_D_M_Y_H_I_S).
                    ' horaire '.$horaire->getHoraire().
                    ' debut '. $debut->format('Y-m-d  H:i:s').
                    ' fin '. $fin->format('Y-m-d  H:i:s').
                    ' res '. (!(($date>= $debut) && ($date<=$fin)))
                    );
                }
            }
            $date->modify(self::INC1DAY);
        } while ($date <= $datefin) ;
    }
    public function testGetHoraireForDateSU(): void
    {
        $date = new DateTime('2018-04-01');
        $datefin = new DateTime('2022-04-01');
        $container = static::getContainer();
        /**
         * @var HoraireService
         */
        $horaireService = $container->get(HoraireService::class);
        do {
            for ($i = 0; $i < count(self::SU);$i++) {
                $debut = DateTime::createFromFormat(self::FORMAT_DATETIME_D_M_Y_H_I_S, self::SU[$i][0]);
                $fin =  DateTime::createFromFormat(self::FORMAT_DATETIME_D_M_Y_H_I_S, self::SU[$i][1]);
                if (($date>= $debut) && ($date<=$fin)) {
                    $horaire = $horaireService->getHoraireForDate($date);
                    $this->assertNotNull($horaire, self::MESSAGE_USED_DATE.$date->format(self::FORMAT_DATETIME_D_M_Y_H_I_S));
                    $this->assertEquals('SU', $horaire->getHoraire());
                }
            }
            $date->modify(self::INC1DAY);
        } while ($date <= $datefin) ;
    }
    public function testgetHoraireByHoraireName(): void
    {
        $container = static::getContainer();
        $horaireService = $container->get(HoraireService::class);
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
        $this->assertEquals(null, $horaireService->getDate());
    }
}
