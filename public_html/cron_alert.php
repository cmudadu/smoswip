<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);


require '../vendor/autoload.php';
require '../src/Application/Models/SensorDataSourceModel.php';

use App\Application\Models\MeasurementModel;
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
    echo "Sending alert<br>";
    $model = new MeasurementModel($pdo);
    $model->sendAlert();
   
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . PHP_EOL;
}

echo "Done!<br>";

?>
