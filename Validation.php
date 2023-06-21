<?php

class Validation
{

    static function testInputData($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }


    /**
     * NBP ratesTable apperead daily between 11:45 - 12:15
     * @return string
     */
    static function checkActualTime($chosenDate) : string
    {

        if ($chosenDate == date('Y-m-d')) {
            $timeHour = date('H');
            $timeMinute = date('i');
            if ($timeHour < 12 || ($timeHour == 12) && ($timeMinute < 15)) {
                $chosenDate = date_format(date_sub(
                    date_create($chosenDate),
                    date_interval_create_from_date_string('1 day')
                ), 'Y-m-d');
            }
        }
        return $chosenDate;
    }
}
