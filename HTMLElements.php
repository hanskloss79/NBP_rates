<?php

class HTMLElements
{
    static function createNavigationMenu($menuItems) 
    {
        $navigationMenu = '<div class="container">
            <header class="d-flex justify-content-center py-3">
            <ul class="nav nav-pills">';
        foreach ($menuItems as $item)
        {
            $navigationMenu .= '<li class="nav-item"><a href="' . $item['fileName'] .
                '" class="nav-link ' . $item['chosenItem'] . '">' . $item['itemTitle'] . '</a></li>';
        }
        $navigationMenu .= '</ul> </header> </div>';

        echo $navigationMenu;
    }
    
    
    static function createDateForm()
    {
        $htmlDateForm = '<form action="" method="POST" class="d-flex my-3 justify-content-center">
        <div class="row gap-2 align-items-center">
        <label for="chosenDate" class="col-auto">Proszę wybrać datę dla tabeli kursów:</label>
        <input type="date" class="col-auto" id="chosenDate" name="chosenDate" max="' . date("Y-m-d") . '" 
                value="' . (isset($_POST['chosenDate']) ? $_POST['chosenDate'] : date("Y-m-d")) . '">
        <input type="submit" class="btn btn-primary col-auto" name="chosenDate-submit" value="Wygeneruj tabelę kursów">
        </div>
        </form>';
        echo $htmlDateForm;
    }

    static function createCurrencyRatesTable($tableRatesData, $tableDate)
    {
        echo '<div class="col-10 col-md-6 mx-auto my-2">';
        echo '<table class="table table-bordered border-primary table-striped caption-top">
                <caption class="text-center fs-3">Średni kurs walut wg NBP z dnia: ' . $tableDate . '</caption>';
        echo '<tr><th>Nazwa waluty</th><th>Kod waluty</th><th>Średni kurs</th>';
        foreach ($tableRatesData as $key => $row) {
            $tableRow = '<tr><td>' . $row['currency'] . '</td><td>' . $key .
                '</td><td>' .  $row['rate'] . '</td></tr>';
            echo $tableRow;
        }
        echo '</table></div>';
    }

    static function createCurrencyConversionForm($tableRatesData)
    {
        $fromCurrencyOptions = '<option '.((!isset($_POST['fromCurrency']) || $_POST['fromCurrency'] == 'PLN') 
        ? 'selected' : '').' value="PLN">złoty polski</option>';

        foreach ($tableRatesData as $key => $row) {
            $fromCurrencyOptions .= '<option '.((isset($_POST['fromCurrency']) && $_POST['fromCurrency'] == $key) ? 
            'selected' : ''). ' value="' . $key . '">' . $row['currency'] . '</option>' ;
        }

        $toCurrencyOptions = '<option '.((!isset($_POST['toCurrency']) || $_POST['toCurrency'] == 'PLN') 
        ? 'selected' : '').' value="PLN">złoty polski</option>';

        foreach ($tableRatesData as $key => $row) {
            $toCurrencyOptions .= '<option '.((isset($_POST['toCurrency']) && $_POST['toCurrency'] == $key) ? 
            'selected' : ''). ' value="' . $key . '">' . $row['currency'] . '</option>' ;
        }
        
        $htmlCurrencyConversionForm = '<h1 class="mt-4 text-center">Przelicznik walut</h1>
        <form action="" method="POST" class="mb-5 col-10 col-md-6 mx-auto justify-content-center justify-items-center">
        <div class="flex-row gap-2 align-items-center">
        <label for="chosenDate" >Proszę wybrać datę dla tabeli kursów:</label>
        <input type="date" id="chosenDate" name="chosenDate" max="' . date("Y-m-d") . 
        '" value="' .  (isset($_POST['chosenDate']) ? $_POST['chosenDate'] : date("Y-m-d"))  . '">
        </div>
        <div class="d-flex flex-row gap-2 align-items-center mt-3">
        <label for="currencyAmount" class="col-auto">Kwota:</label>
        <input type="number" min="0" value="100" step="0.01" class="col-md-2 col-2" id="currencyAmount" name="currencyAmount" 
        placeholder="Wpisz kwotę">
        <label for="fromCurrency" class="col-auto">Przelicz z </label>
        <select class="mb-2 col-auto" id="fromCurrency" name="fromCurrency">';
        $htmlCurrencyConversionForm .= $fromCurrencyOptions;      
        $htmlCurrencyConversionForm .= '</select>';
        $htmlCurrencyConversionForm .= '<label for="toCurrency" class="col-auto">na </label>
        <select class="mb-2 col-auto" id="toCurrency" name="toCurrency">';
        $htmlCurrencyConversionForm .= $toCurrencyOptions;    
        $htmlCurrencyConversionForm .= '</select>
        <input type="submit" class="btn btn-primary col-auto" name="conversionForm-submit" value="Przelicz">
        </div>
        </form>';

        echo $htmlCurrencyConversionForm;
        //foreach ($tableRatesData as $row) {
        //    print_r($row);
        //}
        //print_r($tableRatesData);
    }

    static function displayHeading2($heading){
        echo '<h2 class="text-center">' . $heading . '</h2>';
    }

    static function displayLastConversions($lastConversionsData){
        echo '<div class="col-10 col-md-6 mx-auto my-2">';
        echo '<table class="table table-bordered border-primary table-striped caption-top">
                <caption class="text-uppercase text-center fs-2">Lista ostatnich przewalutowań</caption>';
        echo '<tr><th>Data przewalutowania</th><th>Kwota</th><th>Waluta źródłowa</th><th>Kwota</th><th>Waluta docelowa</th>';
        foreach ($lastConversionsData as $row) {
            $tableRow = '<tr><td>' . $row['created_at'] . '</td><td>' . $row['source_amount'] . '</td><td>' . 
            $row['source_currency'] . '</td><td>' . $row['target_amount'] . '</td><td>' .  
            $row['target_currency'] . '</td></tr>';
            echo $tableRow;
        }
        echo '</table></div>';
    }
}
