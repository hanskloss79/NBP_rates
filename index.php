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

    function LoadClass($someClass)
    {
        require "$someClass.php";
    }
    spl_autoload_register('LoadClass');

    date_default_timezone_set('Europe/Warsaw');
    // new database object plus connection plus database plus tables
    $db = new Database();

    if (!$db->connection) {
        die('Nie udało się nawiązać połączenia z serwer MySQL');
    }

    // displaying the navigation menu in the upper part of the page
    $navigationMenuItems = [
        ['fileName' => 'index.php', 'itemTitle' => 'TABELA KURSÓW', 'chosenItem' => 'active'],
        ['fileName' => 'currencyConversion.php', 'itemTitle' => 'PRZELICZNIK WALUT', 'chosenItem' => ''],
        ['fileName' => 'lastConversions.php', 'itemTitle' => 'HISTORIA PRZEWALUTOWAŃ', 'chosenItem' => '']   
    ];
    HTMLElements::createNavigationMenu($navigationMenuItems);

    // displaying the date form to choose the date of currency exchange rates table
    HTMLElements::createDateForm();

    if (!empty($_POST['chosenDate-submit'])) {
        // generate table with currency rates
        $chosenDate = Validation::checkActualTime($_POST['chosenDate']);
        if(!$db->isRatesDataForDate($chosenDate))
        {
            // new NBPAPIClient object
            $client = new NBPApiClient();
            $pulledRates = $client->getRates($chosenDate);
            // insert data to table
            $db->insertRates($pulledRates, $chosenDate);
        }
    
        $tableRatesData = $db->getRatesToDisplay($chosenDate);
        HTMLElements::createCurrencyRatesTable($tableRatesData, $chosenDate);
    }
    
    ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>

</body>

</html>