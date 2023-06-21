<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal walutowy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
</head>

<body>
    <?php
    function LoadClass($klasa)
    {
        require "$klasa.php";
    }
    spl_autoload_register('LoadClass');

    date_default_timezone_set('Europe/Warsaw');
    // new database object plus connection plus database plus tables
    $db = new Database();

    if (!$db->connection) {
        die('Nie udało się nawiązać połączenia z serwerem MySQL');
    }

    $navigationMenuItems = [
        ['fileName' => 'index.php', 'itemTitle' => 'TABELA KURSÓW', 'chosenItem' => ''],
        ['fileName' => 'currencyConversion.php', 'itemTitle' => 'PRZELICZNIK WALUT', 'chosenItem' => 'active'],
        ['fileName' => 'lastConversions.php', 'itemTitle' => 'HISTORIA PRZEWALUTOWAŃ', 'chosenItem' => '']
    ];

    HTMLElements::createNavigationMenu($navigationMenuItems);

    // we need at last one currency table to fill in the currency select options
    $actualDate = Validation::checkActualTime(date('Y-m-d'));
    if($db->isEmptyRatesTable())
    {
        $client = new NBPApiClient();
        $pulledRates = $client->getRates($actualDate);
        // insert data to table
        $db->insertRates($pulledRates, $actualDate);
    }
    
    $tableRatesData = $db->getRatesToDisplay($actualDate);
    HTMLElements::createCurrencyConversionForm($tableRatesData);

    if (!empty($_POST['conversionForm-submit'])) {

        $sourceCurrencyAmount = Validation::testInputData($_POST['currencyAmount']);
        //$tableRatesData = $db->getRatesToDisplay($_POST['chosenDate']);
        // we need to check if exchange rates table exists for this date in database
        // or we will do the same as in the case of generating currency rates table
        $chosenDate = Validation::checkActualTime($_POST['chosenDate']);
        if(!$db->isRatesDataForDate($chosenDate))
        {
            $client = new NBPApiClient();
            $pulledRates = $client->getRates($chosenDate);
            // insert data to table
            $db->insertRates($pulledRates, $chosenDate);
        }

        $tableRatesData = $db->getRatesToDisplay($chosenDate);

        $fromCurrencyRatio = isset($tableRatesData[$_POST['fromCurrency']]) ? 
            $tableRatesData[$_POST['fromCurrency']]['rate'] : 1 ;
        $toCurrencyRatio = isset($tableRatesData[$_POST['toCurrency']]) ? 
            $tableRatesData[$_POST['toCurrency']]['rate'] : 1 ;
        
        $conversionResult = Converter::convertCurrencies(
            $sourceCurrencyAmount,
            $fromCurrencyRatio,
            $toCurrencyRatio);
        
        // insert conversion to table
        $db->insertConversionResult($sourceCurrencyAmount, $_POST['fromCurrency'], 
                $_POST['toCurrency'], $conversionResult);

        $htmlConversionResult =  round($sourceCurrencyAmount, 2) . ' ' . $_POST['fromCurrency'] . ' to ' 
            . round($conversionResult,2) . ' ' . $_POST['toCurrency'];

        HTMLElements::displayHeading2($htmlConversionResult);
        
    }




    ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>

</body>

</html>