<?php
namespace App\Interfaces;

use DateTime;

interface DateInterface
{
    public function dateString_d_m_Y_ToDateTime(string $dateString): ?DateTime;
}
