<?php

namespace App\Application\Controllers\Api;

use App\Application\Models\AlertRuleModel;
use PDO;
use Psr\Log\LoggerInterface;

class AlertRulesController {
   
    private $model;
    private $log;

    public function __construct(PDO $pdo, LoggerInterface $logger) {
        $this->model = new AlertRuleModel($pdo);
        $this->log = $logger;
    }

    public function getAlertRules($req, $res, $args) {
        $user_id = $_SESSION['user'];
        
        $alertrules = $this->model->getAlertRulesByUserId($user_id);
        
        $res->getBody()->write(json_encode($alertrules));
        
        return $res
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    public function getAlertRuleRecipients($req, $res, $args) {
        $alertrule_id = $args['id'];
        
        $recipients = $this->model->getAlertRuleRecipients($alertrule_id);
        
        $res->getBody()->write(json_encode($recipients));
        
        return $res
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    
    public function getAlertRule($req, $res, $args) {
        
        //$this->log->info('USER ID: '.$user_id);
        $alertrule_id = $args['id'];
        $alertrule = $this->model->getAlertRule($alertrule_id);
        
        $res->getBody()->write(json_encode($alertrule));
        
        return $res
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    public function deleteAlertRule($req, $res, $args) {
        
        //$this->log->info('USER ID: '.$user_id);
        $alertrule_id = $args['id'];
        $this->model->deleteAlertRule($alertrule_id);
        
        $res->getBody()->write(json_encode(true));
        
        return $res
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    public function addAlertRule($req, $res, $args) {
        
        $params = (array)$req->getParsedBody();
        $alertrule_id = $this->model->addAlertRule($params['sensor_id'], 
                                                    $params['name'],
                                                    $params['level'], 
                                                    $params['field_name'], 
                                                    $params['field_operator'], 
                                                    $params['field_threshold'],
                                                    $params['recipient_email'],
                                                    $params['recipient_telegram']);
      
        $res->getBody()->write(json_encode($alertrule_id));
        
        return $res
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
    
    public function addAlertRuleRecipient($req, $res, $args) {
        $alertrule_id = $args['id'];
        $params = (array)$req->getParsedBody();
        $id = $this->model->addAlertRuleRecipient($alertrule_id, 
                                                    $params['recipient_type'],
                                                    $params['recipient']);
      
        $res->getBody()->write(json_encode($id));
        
        return $res
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    public function deleteRecipient($req, $res, $args) {
        
        //$this->log->info('USER ID: '.$user_id);
        $alertrulerecipient_id = $args['id'];
        $this->model->deleteAlertRuleRecipient($alertrulerecipient_id);
        
        $res->getBody()->write(json_encode(true));

        return $res
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
}
