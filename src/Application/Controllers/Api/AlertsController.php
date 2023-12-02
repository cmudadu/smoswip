<?php

namespace App\Application\Controllers\Api;

use App\Application\Models\AlertModel;
use PDO;
use Psr\Log\LoggerInterface;

class AlertsController {
   
    private $model;
    private $log;

    public function __construct(PDO $pdo, LoggerInterface $logger) {
        $this->model = new AlertModel($pdo);
        $this->log = $logger;
    }

    public function getAlerts($req, $res, $args) {
        $user_id = $_SESSION['user'];
        $params = $req->getQueryParams();
        $site_id = $params['site_id'] ?? 0;
        $top = $params['top'] ?? 0;
        $alerts = $this->model->getAlertsByUserId($user_id, $site_id, $top);
        
        $res->getBody()->write(json_encode($alerts));
        
        return $res
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

}
