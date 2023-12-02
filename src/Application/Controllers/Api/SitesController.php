<?php

namespace App\Application\Controllers\Api;

use App\Application\Models\SiteModel;
use App\Application\Models\SensorModel;
use PDO;
use Psr\Log\LoggerInterface;

class SitesController {
   
    private $model;
    private $modelSensor;
    private $log;

    public function __construct(PDO $pdo, LoggerInterface $logger) {
        $this->model = new SiteModel($pdo);
        $this->modelSensor = new SensorModel($pdo);
        $this->log = $logger;
    }

    public function getSites($req, $res, $args) {
        $user_id = $_SESSION['user'];
        //$this->log->info('USER ID: '.$user_id);
        $sites = $this->model->getSitesByUserId($user_id);
        
        $res->getBody()->write(json_encode($sites));
        
        return $res
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    public function getSite($req, $res, $args) {
        
        //$this->log->info('USER ID: '.$user_id);
        $site_id = $args['id'];
        $site = $this->model->getSite($site_id);
        
        $res->getBody()->write(json_encode($site));
        
        return $res
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    public function deleteSite($req, $res, $args) {
        
        //$this->log->info('USER ID: '.$user_id);
        $site_id = $args['id'];
        $this->model->deleteSite($site_id);
        
        $res->getBody()->write(json_encode(true));
        
        return $res
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    public function addSite($req, $res, $args) {
        
        $user_id = $_SESSION['user'];
        $params = (array)$req->getParsedBody();
        $site_id = $this->model->addSite($user_id, $params['name'], $params['description'], $params['lat'], $params['lon']);
        
        $res->getBody()->write(json_encode($site_id));
        
        return $res
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
    
    
}
