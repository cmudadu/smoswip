<?php

namespace App\Application\Controllers\Api;

use App\Application\Models\MeasurementModel;
use DateTime;
use PDO;
use Psr\Log\LoggerInterface;

class MeasurementsController {
   
    private $model;
    private $log;

    public function __construct(PDO $pdo, LoggerInterface $logger) {
        $this->model = new MeasurementModel($pdo);
        $this->log = $logger;
    }
  
   

    public function deleteMeasurement($req, $res, $args) {
        
        //$this->log->info('USER ID: '.$user_id);
        $measurement_id = $args['id'];
        $this->model->deleteMeasurement($measurement_id);
        
        $res->getBody()->write(json_encode(true));
        
        return $res
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }    
}
