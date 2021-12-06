<?php


use DateTime;

interface TimeInterface
{
    public function generateTime(string $timeString): DateTime;
}
