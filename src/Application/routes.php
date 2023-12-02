<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

// Group different routes under the same path
return function (App $app) {
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    // App routes
    $app->get('/',          'App\Application\Controllers\HomeController:index')->setName('root');
    $app->get('/dashboard', 'App\Application\Controllers\HomeController:dashboard')->setName('dashboard');
    $app->get('/sse',       'App\Application\Controllers\HomeController:sse')->setName('sse');
    
    //$app->get('/sites',     'App\Application\Controllers\HomeController:sites')->setName('sites');
    $app->get('/sites/{id:[0-9]+}',         'App\Application\Controllers\HomeController:site_detail')->setName('site_detail');
    $app->get('/sites/{id:[0-9]+}/overview', 'App\Application\Controllers\HomeController:site_overview')->setName('site_overview');
    
    $app->get('/sensors/{id:[0-9]+}', 'App\Application\Controllers\HomeController:sensor_detail')->setName('sensor_detail');
    $app->get('/sensors/{id:[0-9]+}/preview', 'App\Application\Controllers\HomeController:sensor_detail_preview')->setName('sensor_detail_preview');
    
    $app->get('/alerts',              'App\Application\Controllers\HomeController:alerts')->setName('alerts');
    
    $app->get('/alertrules',             'App\Application\Controllers\HomeController:alertrules')->setName('alertrules');
    $app->get('/alertrules/{id:[0-9]+}', 'App\Application\Controllers\HomeController:alertrule_detail')->setName('alertrule_detail');
    
    $app->get('/doc',              'App\Application\Controllers\HomeController:doc')->setName('doc');
    $app->get('/error404',              'App\Application\Controllers\HomeController:error404')->setName('error404');
    
    // API routes
    $app->group('/api', function (Group $group) {
        $group->post('/login',            'App\Application\Controllers\Api\LoginController:doLogin')->setName('apiLogin');
        $group->post('/logout',            'App\Application\Controllers\Api\LoginController:doLogout')->setName('apiLogout');

        $group->get('/sites',                     'App\Application\Controllers\Api\SitesController:getSites')->setName('apiGetSites');
        $group->get('/sites/{id:[0-9]+}',         'App\Application\Controllers\Api\SitesController:getSite')->setName('apiGetSite');
        //$group->get('/sites/{id:[0-9]+}/sensors', 'App\Application\Controllers\Api\SitesController:getSensorsBySite')->setName('apiGetSensorsBySite');
        $group->post('/sites',                    'App\Application\Controllers\Api\SitesController:addSite')->setName('apiAddSite');
        $group->delete('/sites/{id:[0-9]+}',      'App\Application\Controllers\Api\SitesController:deleteSite')->setName('apiDeleteSite');
        
        $group->get('/sensors',                                     'App\Application\Controllers\Api\SensorsController:getSensorList')->setName('apiGetSensorList');
        $group->get('/sensors/{id:[0-9]+}',                         'App\Application\Controllers\Api\SensorsController:getSensor')->setName('apiGetSensor');
        $group->get('/sensors/type',                                'App\Application\Controllers\Api\SensorsController:getSensorType')->setName('apiGetSensorType');
        $group->get('/sensors/{id:[0-9]+}/measurements',            'App\Application\Controllers\Api\SensorsController:getMeasurementsBySensor')->setName('apiGetMeasurementsBySensor');
        $group->get('/sensors/{id:[0-9]+}/measurementstrend',            'App\Application\Controllers\Api\SensorsController:getMeasurementsTrendBySensor')->setName('apiGetMeasurementsTrendBySensor');
        $group->get('/sensors/site/{id:[0-9]+}', 'App\Application\Controllers\Api\SensorsController:getSensorsBySite')->setName('apiGetSensorsBySite');
        $group->get('/sensors/{id:[0-9]+}/alertrules',              'App\Application\Controllers\Api\SensorsController:getAlertRulesBySensor')->setName('apiGetAlertRulesBySensor');
        $group->get('/sensors/{id:[0-9]+}/datasources',              'App\Application\Controllers\Api\SensorsController:getDatasourcesBySensor')->setName('apiGetDatasourcesBySensor');
        
        $group->post('/sensors',                                    'App\Application\Controllers\Api\SensorsController:addSensor')->setName('apiAddSensor');
        $group->post('/sensors/{id:[0-9]+}/measurements',           'App\Application\Controllers\Api\SensorsController:addMeasurement')->setName('apiAddMeasurement');
        $group->post('/sensors/{id:[0-9]+}/measurements/uploadCSV', 'App\Application\Controllers\Api\SensorsController:uploadCSV')->setName('apiUploadCSVSensorMeasurement');
        $group->delete('/sensors/{id:[0-9]+}',                      'App\Application\Controllers\Api\SensorsController:deleteSensor')->setName('apiDeleteSensor');
        
       
        $group->get('/alertrules',                        'App\Application\Controllers\Api\AlertRulesController:getAlertRules')->setName('apiGetAlertRules');
        $group->get('/alertrules/{id:[0-9]+}',            'App\Application\Controllers\Api\AlertRulesController:getAlertRule')->setName('apiGetAlertRule');
        $group->get('/alertrules/{id:[0-9]+}/recipients', 'App\Application\Controllers\Api\AlertRulesController:getAlertRuleRecipients')->setName('apiGetAlertRuleRecipients');
        $group->post('/alertrules',                       'App\Application\Controllers\Api\AlertRulesController:addAlertRule')->setName('apiSddAlertRule');
        $group->post('/alertrules/{id:[0-9]+}/recipients', 'App\Application\Controllers\Api\AlertRulesController:addAlertRuleRecipient')->setName('apiAddAlertRuleRecipient');
        $group->delete('/alertrules/{id:[0-9]+}',         'App\Application\Controllers\Api\AlertRulesController:deleteAlertRule')->setName('apiDeleteAlertRule');
       
        $group->post('/datasources',               'App\Application\Controllers\Api\SensorDataSourcesController:addDatasource')->setName('apiAddDatasource');
        $group->delete('/datasources/{id:[0-9]+}', 'App\Application\Controllers\Api\SensorDataSourcesController:deleteDatasource')->setName('apiDeleteDatasource');
        
        $group->get('/alerts',              'App\Application\Controllers\Api\AlertsController:getAlerts')->setName('alerts');
    
        $group->delete('/measurements/{id:[0-9]+}',        'App\Application\Controllers\Api\MeasurementsController:deleteMeasurement')->setName('apiDeleteMeasurement');
        $group->delete('/alertrulerecipients/{id:[0-9]+}', 'App\Application\Controllers\Api\AlertRulesController:deleteRecipient')->setName('apiDeleteRecipient');
        
    });
};