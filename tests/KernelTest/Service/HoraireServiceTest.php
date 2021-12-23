<?php

namespace App\Tests\KernelTest\Service;

use App\Service\HoraireService;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class HoraireServiceTest extends KernelTestCase
{
    public function testGetHoraireForDateNORMAL(): void
    {
        $date = new DateTime('2018-04-02');
        $datefin = new DateTime('2021-12-01');
        $container = static::getContainer();
        $ramadan= [
            ['2018-05-16 00:00:00','2018-06-14 00:00:00'],
            ['2019-05-05 00:00:00','2019-06-03 00:00:00'],
            ['2020-04-23 00:00:00','2020-05-23 00:00:00'],
            ['2021-04-13 00:00:00','2021-05-14 00:00:00']
        ];
        $su= [
            ['2019-07-01 00:00:00','2019-08-31 00:00:00'],
            ['2020-07-01 00:00:00','2020-08-31 00:00:00'],
            ['2021-07-01 00:00:00','2021-08-31 00:00:00']
        ];
        /**
         * @var HoraireService
         */
        $horaireService = $container->get(HoraireService::class);
        do {
            $test = true;
            for ($i = 0; $i < count($ramadan);$i++) {
                $debut = DateTime::createFromFormat('Y-m-d H:i:s', $ramadan[$i][0]);
                $fin =  DateTime::createFromFormat('Y-m-d H:i:s', $ramadan[$i][1]);
                if ((($date>= $debut)&& ($date<=$fin))) {
                    $test = false;
                }
            }
         
            for ($i = 0; $i < count($su);$i++) {
                $debut = DateTime::createFromFormat('Y-m-d H:i:s', $su[$i][0]);
                $fin =  DateTime::createFromFormat('Y-m-d H:i:s', $su[$i][1]);
                if (($date>= $debut) && ($date<=$fin)) {
                    $test = false;
                }
            }
            if ($test) {
                $horaire = $horaireService->getHoraireForDate($date);
                $this->assertNotNull($horaire, 'for date '.$date->format('Y-m-d H:i:s'));
                $this->assertEquals(
                    'NORMAL',
                    $horaire->getHoraire(),
                    'for date '.$date->format('Y-m-d H:i:s').
                ' horaire '.$horaire->getHoraire()
                );
            }
            $date->modify('+1 day');
        } while ($date <= $datefin) ;
    }

    public function testGetHoraireForDateRAMADAN(): void
    {
        $date = new DateTime('2018-04-01');
        $datefin = new DateTime('2022-04-01');
        $container = static::getContainer();
        $ramadan= [
            ['2018-05-16 00:00:00','2018-06-14 00:00:00'],
            ['2019-05-05 00:00:00','2019-06-03 00:00:00'],
            ['2020-04-23 00:00:00','2020-05-23 00:00:00'],
            ['2021-04-13 00:00:00','2021-05-14 00:00:00']
        ];
        /**
         * @var HoraireService
         */
        $horaireService = $container->get(HoraireService::class);
        do {
            for ($i = 0; $i < count($ramadan);$i++) {
                $debut = DateTime::createFromFormat('Y-m-d H:i:s', $ramadan[$i][0]);
                $fin =  DateTime::createFromFormat('Y-m-d H:i:s', $ramadan[$i][1]);
                if (($date>= $debut)&& ($date<=$fin)) {
                    $horaire = $horaireService->getHoraireForDate($date);
                    $this->assertNotNull($horaire, 'for date '.$date->format('Y-m-d H:i:s'));
                    $this->assertEquals(
                        'RAMADAN',
                        $horaire->getHoraire(),
                        'for date '.$date->format('Y-m-d H:i:s').
                    ' horaire '.$horaire->getHoraire().
                    ' debut '. $debut->format('Y-m-d  H:i:s').
                    ' fin '. $fin->format('Y-m-d  H:i:s').
                    ' res '. (!(($date>= $debut) && ($date<=$fin)))
                    );
                }
            }
            $date->modify('+1 day');
        } while ($date <= $datefin) ;
    }
    public function testGetHoraireForDateSU(): void
    {
        $date = new DateTime('2018-04-01');
        $datefin = new DateTime('2022-04-01');
        $container = static::getContainer();
        $su= [
            ['2019-07-01 00:00:00','2019-08-31 00:00:00'],
            ['2020-07-01 00:00:00','2020-08-31 00:00:00'],
            ['2021-07-01 00:00:00','2021-08-31 00:00:00']
        ];
        /**
         * @var HoraireService
         */
        $horaireService = $container->get(HoraireService::class);
        do {
            for ($i = 0; $i < count($su);$i++) {
                $debut = DateTime::createFromFormat('Y-m-d H:i:s', $su[$i][0]);
                $fin =  DateTime::createFromFormat('Y-m-d H:i:s', $su[$i][1]);
                if (($date>= $debut) && ($date<=$fin)) {
                    $horaire = $horaireService->getHoraireForDate($date);
                    $this->assertNotNull($horaire, 'for date '.$date->format('Y-m-d H:i:s'));
                    $this->assertEquals('SU', $horaire->getHoraire());
                }
            }
            $date->modify('+1 day');
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
