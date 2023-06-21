<?php

class Database
{
    var $hostname = 'localhost';
    var $username = 'root';
    var $password = '';
    var $port = '3306';

    var $connection = null;

    /**
     * Summary of __construct
     */
    public function __construct()
    {
        $this->createConnection();

        $this->createDatabase('NBP_calc');

        $this->createTables();
    }

    private function createConnection()
    {
        try {
            $dbh = new PDO("mysql:host=$this->hostname;charset=utf8mb4", $this->username, $this->password);
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
            $dbh = null;
        }
        $this->connection = $dbh;
    }
    private function createDatabase($dbName)
    {
        if ($this->connection) {
            try {
                $sql = "CREATE DATABASE IF NOT EXISTS $dbName CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;";
                $this->connection->exec($sql);
                $this->connection->query("use $dbName");
            } catch (PDOException $e) {
                print "Error!: " . $e->getMessage() . "<br/>";
            }
        }
    }

    private function createTables()
    {

        if ($this->connection) {
            try {
                $ratesTable =
                    'CREATE TABLE IF NOT EXISTS rates( 
                    id  INT AUTO_INCREMENT,
                    currency  VARCHAR(100) NOT NULL, 
                    short VARCHAR(9) NOT NULL, 
                    rate DECIMAL(10,4) NOT NULL,
                    created_at DATE NOT NULL,
                    PRIMARY KEY(id))';

                $conversionsTable =
                    'CREATE TABLE IF NOT EXISTS conversions( 
            id  INT AUTO_INCREMENT,
            source_amount DECIMAL(10,2) NOT NULL,
            source_currency VARCHAR(9) NOT NULL,
            target_currency VARCHAR(9) NOT NULL,
            target_amount DECIMAL(10,2) NOT NULL,
            created_at DATETIME NOT NULL,
            PRIMARY KEY(id))';

                $this->connection->exec($ratesTable);
                $this->connection->exec($conversionsTable);
            } catch (PDOException $e) {
                print "Error!: " . $e->getMessage() . "<br/>";
            }
        }
    }

    public function isEmptyRatesTable(): bool 
    {
        $sqlString = 'SELECT COUNT(id) AS ratesNumber FROM rates';
        $statement = $this->connection->prepare($sqlString);
        $statement->execute();
        $numberOfRows = $statement->fetch()['ratesNumber'];
        return $numberOfRows==0;
    }

    public function isRatesDataForDate($chosenDate): bool
    {
        $sqlString = 'SELECT COUNT(id) AS ratesNumber FROM rates WHERE created_at = :chosenDate';
        $statement = $this->connection->prepare($sqlString);
        $statement->execute([':chosenDate' => $chosenDate]);
        $numberOfRows = $statement->fetch()['ratesNumber'];
        return $numberOfRows==0;
    }

    public function insertRates($pulledRates, $date)
    {
        $sqlString = 'SELECT COUNT(id) AS ratesNumber FROM rates WHERE created_at = :date';
        $statement = $this->connection->prepare($sqlString);
        $statement->execute([':date' => $date]);
        $numberRatesDate = $statement->fetch();

        // dodajemy tabele kursów z danego dnia tylko jeżeli nie ma żadnego rekordu z datą 
        // tabeli zaciągniętej z NBP przez API żeby nie dublować danych
        if ($numberRatesDate['ratesNumber'] == 0) {
            $sql = 'INSERT INTO rates (currency, short, rate, created_at) VALUES (:currency, :short, :rate, :created_at)';
            $statement = $this->connection->prepare($sql);
            foreach ($pulledRates[0]->rates as $currencyRate) {
                $statement->execute([
                    ':currency' => $currencyRate->currency,
                    ':short' => $currencyRate->code,
                    ':rate' => $currencyRate->mid,
                    ':created_at' => $date
                ]);
            }
        }
    }

    public function getRatesToDisplay($tableDate)
    {
        $result = $this->connection->query("SELECT * FROM rates WHERE created_at='$tableDate'");
        $tableRatesData = [];
        foreach ($result as $row) {

            $tableRatesData[$row['short']] = [
                'currency' => $row['currency'],
                'rate' => $row['rate']
            ];
            //echo $row['short'] . PHP_EOL . '<br>';
        }
        //var_dump($tableRatesData);
        /*
        echo "<pre>";
        print_r($currencyCodes);
        print_r($currencyNames);
        print_r($currencyRates);
        echo "</pre>";
        */

        return $tableRatesData;
    }

    public function insertConversionResult($sourceCurrencyAmount, $sourceCurrency, $targetCurrency, $targetCurrencyAmount)
    {
        try {
            $sql = 'INSERT INTO conversions (source_amount, source_currency, target_currency, target_amount, created_at) 
                VALUES (:source_amount, :source_currency, :target_currency, :target_amount, :created_at)';
            $statement = $this->connection->prepare($sql);
            $statement->execute([
                ':source_amount' => $sourceCurrencyAmount,
                ':source_currency' => $sourceCurrency,
                ':target_currency' => $targetCurrency,
                ':target_amount' => $targetCurrencyAmount,
                ':created_at' => date('Y-m-d H:i:s'),
            ]);
        } catch (PDOException $e) {
            echo $sql . "<br>" . $e->getMessage();
        }
    }

    public function getLastConversions($conversionsNumber)
    {
        return $this->connection->query("SELECT * FROM conversions ORDER BY id DESC LIMIT $conversionsNumber");
    }
}
