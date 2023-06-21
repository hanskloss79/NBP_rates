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
    //require_once('Database.php');
    //require_once('NBPClient.php');

    date_default_timezone_set('Europe/Warsaw');
    // new database object plus connection plus database plus tables
    $db = new Database();

    if (!$db->connection) {
        die('Nie udało się nawiązać połączenia z serwer MySQL');
    }

    $navigationMenuItems = [
        ['fileName' => 'index.php', 'itemTitle' => 'TABELA KURSÓW', 'chosenItem' => ''],
        ['fileName' => 'currencyConversion.php', 'itemTitle' => 'PRZELICZNIK WALUT', 'chosenItem' => ''],
        ['fileName' => 'lastConversions.php', 'itemTitle' => 'HISTORIA PRZEWALUTOWAŃ', 'chosenItem' => 'active']   
    ];

    HTMLElements::createNavigationMenu($navigationMenuItems);

    
    if(!empty($lastConversionsData = $db->getLastConversions(10)->fetchAll())){
        HTMLElements::displayLastConversions($lastConversionsData);
    }
    else{
        HTMLElements::displayHeading2('Nie wykonano jeszcze żadnych przewalutowań');
    }



    ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>

</body>

</html>