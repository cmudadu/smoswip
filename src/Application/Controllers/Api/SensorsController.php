<?php

namespace App\Application\Controllers\Api;

use App\Application\Models\AlertRuleModel;
use App\Application\Models\SensorModel;
use App\Application\Models\MeasurementModel;
use App\Application\Models\SensorDataSourceModel;
use DateTime;
use PDO;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class SensorsController {
   
    private $model;
    private $modelMeasurement;
    private $modelAlertRule;
    private $modelSensorDataSource;
    private $log;
    private $settings;
    private $upload_directory;

    public function __construct(PDO $pdo, LoggerInterface $logger, ContainerInterface $container) {
        $this->model = new SensorModel($pdo);
        $this->modelMeasurement = new MeasurementModel($pdo);
        $this->modelAlertRule = new AlertRuleModel($pdo);
        $this->modelSensorDataSource = new SensorDataSourceModel($pdo);
        $this->log = $logger;
        $this->upload_directory = $container->get('settings')['upload_directory'];
    }
  

    public function getSensor($req, $res, $args) {
        
        //$this->log->info('USER ID: '.$user_id);
        $sensor_id = $args['id'];
        $sensor = $this->model->getSensor($sensor_id);
        
        $res->getBody()->write(json_encode($sensor));
        
        return $res
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    public function getSensorList($req, $res, $args) {
        
        //$this->log->info('USER ID: '.$user_id);
        $user_id = $_SESSION['user'];
        
        $sensors = $this->model->getSensorList($user_id);
        
        $res->getBody()->write(json_encode($sensors));
        
        return $res
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }    

    public function getSensorsBySite($req, $res, $args) {
        //$user_id = $_SESSION['user'];
        //$this->log->info('USER ID: '.$user_id);
        $site_id = $args['id'];
        $sensors = $this->model->getSensorsBySiteId($site_id);
        
        $res->getBody()->write(json_encode($sensors));
        
        return $res
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    public function addSensor($req, $res, $args) {
        
        $params = (array)$req->getParsedBody();
        $sensor_id = $this->model->addSensor( $params['site_id'],
                                            $params['sensortype_id'],
                                            $params['name'], 
                                            $params['description'], 
                                            $params['lat'], 
                                            $params['lon'], 
                                            $params['alt']);
        
        $res->getBody()->write(json_encode($sensor_id));
        
        return $res
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    public function getSensorType($req, $res){
        $sensor_type_list = $this->model->getSensorType();

        $res->getBody()->write(json_encode($sensor_type_list));

        return $res
        ->withHeader('Content-Type', 'application/json')
        ->withStatus(200);
    }

    public function deleteSensor($req, $res, $args) {
        
        //$this->log->info('USER ID: '.$user_id);
        $sensor_id = $args['id'];
        $this->model->deleteSensor($sensor_id);
        
        $res->getBody()->write(json_encode(true));
        
        return $res
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
    
    public function getMeasurementsBySensor($req, $res, $args) {
        //$user_id = $_SESSION['user'];
        //$this->log->info('USER ID: '.$user_id);
        $sensor_id = $args['id'];

        $params = $req->getQueryParams();
        $dateFrom = $params['date_from'] ?? null;
        $dateTo = $params['date_to'] ?? null;
        $lastDays = $params['lastDays'] ?? null;
        $top = $params['top'] ?? null;
        $sort = $params['sort'] ?? 'DESC';//$params['sort'] ?? 'DESC';
   
        
        $measurements = $this->modelMeasurement->getMeasurementsBySensorId($sensor_id, $dateFrom, $dateTo, $lastDays, $top, $sort);
        
        $res->getBody()->write(json_encode($measurements));
        
        return $res
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    public function getMeasurementsTrendBySensor($req, $res, $args) {
        //$user_id = $_SESSION['user'];
        //$this->log->info('USER ID: '.$user_id);
        $sensor_id = $args['id'];
        
        $measurements = $this->modelMeasurement->getMeasurementsTrendBySensorId($sensor_id);
        
        $res->getBody()->write(json_encode($measurements));
        
        return $res
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    public function getAlertRulesBySensor($req, $res, $args) {
        //$user_id = $_SESSION['user'];
        //$this->log->info('USER ID: '.$user_id);
        $sensor_id = $args['id'];
       
        $alert_rules = $this->modelAlertRule->getAlertRulesBySensorId($sensor_id);
        
        $res->getBody()->write(json_encode($alert_rules));
        
        return $res
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    public function getDatasourcesBySensor($req, $res, $args) {
        //$user_id = $_SESSION['user'];
        //$this->log->info('USER ID: '.$user_id);
        $sensor_id = $args['id'];
       
        $datasources = $this->modelSensorDataSource->getSensorDataSourceBySensor($sensor_id);
        
        $res->getBody()->write(json_encode($datasources));
        
        return $res
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    public function addMeasurement($req, $res, $args) {
        $sensor_id = $args['id'];
        $params = (array)$req->getParsedBody();
        $timestamp = new DateTime($params['timestamp']);
        //$this->log->info('Timestamp: '. $timestamp->format('Y-m-d H:i'));
        $measurement_id = $this->modelMeasurement->addMeasurement( $sensor_id,
                                            $timestamp, 
                                            $params['m1'], 
                                            $params['m2'], 
                                            $params['m3']);
        
        $res->getBody()->write(json_encode($measurement_id));
        
        return $res
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    public function uploadCSV($req, $res, $args) {
        $sensor_id = $args['id'];
        $directory = $this->upload_directory;
       
        $uploadedFiles = $req->getUploadedFiles();
        $uploadedFile = $uploadedFiles['file'];
       
        if ($uploadedFile->getError() === UPLOAD_ERR_OK) {

            $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);

            // see http://php.net/manual/en/function.random-bytes.php
            $basename = bin2hex(random_bytes(8));
            $filename = sprintf('%s.%s.%0.8s', $sensor_id , $basename, $extension);

            $filepath = $directory . DIRECTORY_SEPARATOR . $filename;
            $uploadedFile->moveTo($filepath);

            $this->modelMeasurement->uploadCSV($sensor_id, $filepath);
           
        }
        
        return $res
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
}
