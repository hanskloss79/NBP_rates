<?php

class NBPApiClient
{
    public const DATE_FORMAT = 'Y-m-d';

    public function getRates($chosenDate)
    {
        try {
            // maximum number of days without exchange rates table
            $counter = 6;
            do {
                $cURLConnection = curl_init('http://api.nbp.pl/api/exchangerates/tables/a/' . $chosenDate . '/');
                curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
                $apiResponse = curl_exec($cURLConnection);
                curl_close($cURLConnection);
                $chosenDate = date_format(date_sub(date_create($chosenDate), 
                    date_interval_create_from_date_string('1 day')), self::DATE_FORMAT);
            } while (str_contains($apiResponse, '404') && ($counter-- > 0));
                    // $apiResponse - available data from the API request
                    $jsonArrayResponse = json_decode($apiResponse);
        } catch (\Throwable $th) 
        {
            $jsonArrayResponse = [];
        }
        return $jsonArrayResponse;
    }
}
