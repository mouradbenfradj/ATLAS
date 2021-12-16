<?php
<<<<<<< HEAD:src/Interfaces/DateInterface.php
namespace App\Interfaces;
=======
namespace App\Service;
>>>>>>> phpspect:src/Service/DateInterface.php

use DateTime;

interface DateInterface
{
    public function dateString_d_m_Y_ToDateTime(string $dateString): ?DateTime;
}
