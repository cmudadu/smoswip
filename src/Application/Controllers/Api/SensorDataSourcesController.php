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

class SensorDataSourcesController {
   
    private $model;
    private $modelSensorDataSource;
    private $log;
    
    public function __construct(PDO $pdo, LoggerInterface $logger, ContainerInterface $container) {
        $this->model = new SensorDataSourceModel($pdo);
        $this->log = $logger;
    }
  
    public function addDatasource($req, $res, $args) {
        
        //$this->log->info('USER ID: '.$user_id);
        $params = (array)$req->getParsedBody();
        $sensor_id = $params['sensor_id']; 
        $description = $params['datasource_description'];
        $cron = $params['datasource_cron'];
        //$enable =  filter_input(INPUT_POST, 'datasource_enable', FILTER_NULL_ON_FAILURE);
                                
        $enable = $params['datasource_enable'] ?? false;          
        
        $ds = [
            "type" => $params['datasource_type'],
            "url" => $params['datasource_url'], 
            "format" => $params['datasource_format'], 
            "delimiter" => $params['datasource_delimiter'], 
            "indexTimestamp" => $params['datasource_indexTimestamp'],
            "indexM1" => $params['datasource_indexM1'],
            "indexM2" => $params['datasource_indexM2'],
            "indexM3" => $params['datasource_indexM3'],
            "header"  => $params['datasource_header'] ?? false,
        ];


        $datasource = json_encode($ds, JSON_PRETTY_PRINT);
        

        $datasource_id = $this->model->addDatasource( 
                                            $sensor_id,
                                            $description,
                                            $cron, 
                                            $datasource, 
                                            $enable);
        
        $res->getBody()->write(json_encode($datasource_id));
        
        return $res
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    public function deleteDatasource($req, $res, $args) {
        
        //$this->log->info('USER ID: '.$user_id);
        $datasource_id = $args['id'];
        $this->model->deleteDatasource($datasource_id);
        
        $res->getBody()->write(json_encode(true));
        
        return $res
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
    
    
}
