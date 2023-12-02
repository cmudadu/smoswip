<?php

//error_reporting(E_ALL);
//ini_set('display_errors', 1);


require '../vendor/autoload.php';
require '../src/Application/Models/SensorDataSourceModel.php';

use App\Application\Models\SensorDataSourceModel;
use Cron\CronExpression;
use PDO;

echo "Start ".(new DateTime())->format('d/m/Y H:i')."<br>";

try {
    echo "Reading ENV<br>";
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->load();

    //phpinfo();

    $host = $_ENV['DB_HOST'];
    $dbname = $_ENV['DB_NAME'];
    $username = $_ENV['DB_USER'];
    $password = $_ENV['DB_PWD'];

    $dsn = "mysql:host=$host;dbname=$dbname;";

    echo "Connecting database<br>";
    try {
        $pdo = new PDO($dsn, $username, $password);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error in DB conn: $dsn :". $e->getMessage();
    }

    echo "Connected<br>";
    $dataSourceModel = new SensorDataSourceModel($pdo);
    $datasources = $dataSourceModel->getSensorDataSourceList();

    echo "Datasource founds " . count($datasources) ."<br>";
    if( $datasources ){
        foreach( $datasources as $ds){
            echo "Datasource " . $ds['id'] ."<br>";
            $do_task = false;
            $last_task = $dataSourceModel->getSensorDataSourceSyncLastTask($ds['id']);

            $currentDate = new DateTime();
            $cron = new CronExpression($ds['cron']);
            $nextRunDate = $cron->getNextRunDate();
            
            if($last_task){
                $lastDate = new DateTime($last_task['execution_time']);
                $do_task = $cron->isDue($lastDate);;
            }else{
                $currentDate = new DateTime();
                $do_task = $cron->isDue($currentDate);
            }
            
            $do_task=true;

            if ($do_task) {
                echo "Task running!<br>";
                $dataSourceModel->doSensorDataSourceSyncTask($ds);
            } else {
                echo "Tank not running. Next schedule: ".$nextRunDate->format('d/m/Y H:i')."<br>";
            }
        }
    }    
   
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . PHP_EOL;
}

echo "Done!<br>";

?>
