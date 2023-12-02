<?php

namespace App\Application\Controllers;


use App\Application\Models\AlertModel;
use App\Application\Models\AlertRuleModel;
use App\Application\Models\MeasurementModel;
use App\Application\Models\SensorModel;
use App\Application\Models\SiteModel;
use PDO;
use Psr\Log\LoggerInterface;
use Slim\Views\PhpRenderer;

class HomeController {
    // Access to model that contains DB queries
    private $view;
    private $log;
    private $modelSite;
    private $modelSensor;

    private $modelAlertRule;

    private $modelMeasurement;

    private $modelAlert;

    // Get the containers classes you need by Dependency Injection (PDO, Logger, etc)
    public function __construct(PhpRenderer $view, LoggerInterface $logger, PDO $pdo,) {

        $this->view = $view;
        $this->log = $logger;
        $this->modelSite = new SiteModel($pdo);
        $this->modelSensor = new SensorModel($pdo);
        $this->modelMeasurement = new MeasurementModel($pdo);
        $this->modelAlertRule = new AlertRuleModel($pdo);
        $this->modelAlert = new AlertModel($pdo);
    }

    public function index($req, $res) {
        return $this->view->render($res, '_layoutlogin.php', [
            "page" => "login.php"
        ]);
    }

    public function sse($req, $res) {
        session_write_close();

        // disable default disconnect checks
        ignore_user_abort(true);

        // set headers for stream
        header("Content-Type: text/event-stream");
        header("Cache-Control: no-cache");
        header("Access-Control-Allow-Origin: *");

        //$user_id = $_SESSION['user'];

        // Is this a new stream or an existing one?
        $lastEventId = floatval(isset($_SERVER["HTTP_LAST_EVENT_ID"]) ? $_SERVER["HTTP_LAST_EVENT_ID"] : 0);
        if ($lastEventId == 0) {
            $lastEventId = floatval(isset($_GET["lastEventId"]) ? $_GET["lastEventId"] : 0);
            if ($lastEventId == 0) {
                $user_id = $_SESSION['user'];
                $alerts = $this->modelAlert->getAlertsByUserId($user_id, 1);
                if(count($alerts) > 0) {
                    $lastEventId = $alerts[0]['id'];
                }
            }
        }

        echo ":" . str_repeat(" ", 2048) . "\n"; // 2 kB padding for IE
        echo "retry: 3000\n";

        // start stream
        while(true){
            if(connection_aborted()){
                exit();
            } else{

                $user_id = $_SESSION['user'];
                $alerts = $this->modelAlert->getAlertsByUserId($user_id, 1);
                if(count($alerts) > 0) {
                    $latestEventId = $alerts[0]['id'];
                }
                
                if($lastEventId < $latestEventId){
                    $data_json = json_encode($alerts[0]['id']);
                    echo "id: " . $latestEventId . "\n";
                    echo "data: ". $data_json. " \n\n";
                    
                    $lastEventId = $latestEventId;
                    
                    ob_flush();
                    flush();
                } else{
                    // no new data to send
                    echo ": heartbeat\n\n";
                    ob_flush();
                    flush();
                }
            }
    
            // 3 second sleep then carry on
            sleep(3);
        }

    }

    public function dashboard($req, $res) {
        $template = "_layout.php";
        if ($req->getHeaderLine('X-Requested-With') === 'XMLHttpRequest') {
            $template = "dashboard.php";
        }
        $user_id = $_SESSION['user'];
        $user_email = $_SESSION['user_email'];
        $sites = $this->modelSite->getSitesByUserId($user_id);
       
        return $this->view->render($res,  $template, [
            "page" => "dashboard.php",
            "script" => "dashboard.js",
            "user_email" => $user_email,
            "site" => ["id"=>"0"],
            "sites" => $sites
        ]);
    }

    /*
    public function sites($req, $res) {
        $template = "_layout.php";
        if ($req->getHeaderLine('X-Requested-With') === 'XMLHttpRequest') {
            $template = "sites.php";
        }
        
        return $this->view->render($res, $template, [
            "page" => "sites.php",
            "script" => "sites.js",
            "user_email" => $_SESSION['user_email']
        ]);
    }*/

    public function doc($req, $res) {
        return $this->view->render($res, "_layout.php", [
            "page" => "doc.php",
            "script" => "doc.js",
            "user_email" => $_SESSION['user_email']
        ]);
    }

    public function error404($req, $res) {
        return $this->view->render($res, "_layout.php", [
            "page" => "error404.php",
            "script" => "error.js",
            "user_email" => $_SESSION['user_email']
        ]);
    }

    public function site_detail($req, $res, $args) {
        $site_id = $args['id'];
        $user_id = $_SESSION['user'];
        $site = $this->modelSite->getSite($site_id, $user_id);
        $sites = $this->modelSite->getSitesByUserId($user_id);
       
        //$this->log->info('SITE: '.$site);
        
        if($site == false){
            return $res
            ->withHeader('Location', '/error404')
            ->withStatus(302);
        }

        return $this->view->render($res, '_layout.php', [
            "page" => "site_detail.php",
            "script" => "sites.js",
            "user_email" => $_SESSION['user_email'],
            "site" => $site,
            "sites" => $sites
        ]);
    }
   
    public function site_overview($req, $res, $args) {
        $user_id = $_SESSION['user'];
        $site_id = $args['id'];

        //$sensors = $this->modelSensor->getSensorsBySiteId($site_id);
        $sensors = $this->modelMeasurement->getMeasurementsBySiteId($site_id);
        $site = $this->modelSite->getSite($site_id, $user_id);        
        $sites = $this->modelSite->getSitesByUserId($user_id);
       
        return $this->view->render($res, '_layout.php', [
            "page" => "site_overview.php",
            "script" => "site_overview.js",
            "user_email" => $_SESSION['user_email'],
            "sensors" => $sensors,
            "site" => $site,
            "sites" => $sites
        ]);
    }

    public function sensor_detail($req, $res, $args) {
        $user_id = $_SESSION['user'];
        $sensor_id = $args['id'];
        
        $sensor = $this->modelSensor->getSensor($sensor_id);
        
        $last_measure = $this->modelMeasurement->getSensorLastMeasure($sensor["id"]);
        $rules_verified = [];
        if($last_measure != false){
            $rules_verified = $this->modelMeasurement->checkAlertRules($last_measure["id"], 
                                                                        $sensor_id, 
                                                                        $last_measure["m1"], 
                                                                        $last_measure["m2"], 
                                                                        $last_measure["m3"],
                                                                        false);
        }     


        $site = $this->modelSite->getSite($sensor["site_id"], $user_id);
        $sites = $this->modelSite->getSitesByUserId($user_id);
       
        return $this->view->render($res, '_layout.php', [
            "page" => "sensor_detail.php",
            "script" => "sensor.js",
            "user_email" => $_SESSION['user_email'],
            "sensor" => $sensor,
            "rules_verified" => $rules_verified,
            "site" => $site,
            "sites" => $sites
        ]);
    }

    public function sensor_detail_preview($req, $res, $args) {
        $user_id = $_SESSION['user'];
        $sensor_id = $args['id'];
        
        $sensor = $this->modelSensor->getSensor($sensor_id);
        
        $last_measure = $this->modelMeasurement->getSensorLastMeasure($sensor["id"]);
        $rules_verified = [];
        if($last_measure != false){
            $rules_verified = $this->modelMeasurement->checkAlertRules($last_measure["id"], 
                                                                        $sensor_id, 
                                                                        $last_measure["m1"], 
                                                                        $last_measure["m2"], 
                                                                        $last_measure["m3"],
                                                                        false);
        }     


        //$site = $this->modelSite->getSite($sensor["site_id"], $user_id);
        //$sites = $this->modelSite->getSitesByUserId($user_id);
       
        return $this->view->render($res, '_layout_ajax.php', [
            "page" => "sensor_detail_preview.php",
            "script" => "sensor.js",
            "user_email" => $_SESSION['user_email'],
            "sensor" => $sensor,
            "rules_verified" => $rules_verified,
            //"site" => $site,
            //"sites" => $sites
        ]);
    }
    
    public function alerts($req, $res) {
        $user_id = $_SESSION['user'];
        $params = $req->getQueryParams();
        $site_id = $params['site_id'] ?? 0;

        $site = $this->modelSite->getSite($site_id, $user_id);
        if($site==false){
            $site = ["id"=>"0"];
        }
        $sites = $this->modelSite->getSitesByUserId($user_id);
       
        return $this->view->render($res, '_layout.php', [
            "page" => "alerts.php",
            "script" => "alerts.js",
            "user_email" => $_SESSION['user_email'],
            "site" => $site,
            "sites" => $sites
        ]);
    }

    public function alertrules($req, $res) {
        return $this->view->render($res, '_layout.php', [
            "page" => "alertrules.php",
            "script" => "alertrules.js",
            "user_email" => $_SESSION['user_email']
        ]);
    }

    
    public function alertrule_detail($req, $res, $args) {
        $alertrule_id = $args['id'];
        $alertrule = $this->modelAlertRule->getAlertRule($alertrule_id);
       
       
        return $this->view->render($res, '_layout.php', [
            "page" => "alertrule_detail.php",
            "script" => "alertrules.js",
            "user_email" => $_SESSION['user_email'],
            "alertrule" => $alertrule
        ]);
    }
}
