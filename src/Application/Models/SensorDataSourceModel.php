<?php

namespace App\Application\Models;

use Exception;
use DateTime;
use SimpleXMLElement;

class SensorDataSourceModel
{
    private $table = "SensorDataSource";
    private $db;
    private $measurementModel;

    public function __construct($db)
    {
        $this->db = $db;
        $this->measurementModel = new MeasurementModel($db);
    }

    public function getSensorDataSourceList()
    {
        $sql = $this->db->prepare("SELECT * FROM $this->table WHERE enable = 1");
        $sql->execute();

        return $sql->fetchAll();
    }

    public function getSensorDataSourceBySensor($sensor_id)
    {
        $sql = $this->db->prepare("SELECT 
                                    s.*,
                                    dsync.execution_time,
                                    dsync.status
                                FROM SensorDataSource s 
                                LEFT JOIN SensorDataSourceSync dsync ON s.id = dsync.sensordatasource_id AND dsync.execution_time = (
                                        SELECT MAX(execution_time) FROM SensorDataSourceSync WHERE sensordatasource_id = s.id
                                        )
                                WHERE sensor_id = :sensor_id");
        $sql->bindParam(':sensor_id', $sensor_id);
        $sql->execute();

        return $sql->fetchAll();
    }

    public function getSensorDataSourceSyncLastTask($sensordatasource_id)
    {
        $sql = $this->db->prepare("SELECT * FROM SensorDataSourceSync WHERE sensordatasource_id = :sensordatasource_id ORDER BY execution_time DESC LIMIT 1");
        $sql->bindParam(':sensordatasource_id', $sensordatasource_id);
        $sql->execute();

        return $sql->fetch();
    }

    public function addDatasource($sensor_id, $description, $cron, $datasource, $enable) {
        $sql = $this->db->prepare("INSERT INTO $this->table (sensor_id, description, cron, datasource, enable)
                                                         VALUES (:sensor_id,:description,:cron,:datasource,:enable);");        
        $sql->bindParam(':sensor_id', $sensor_id);
        $sql->bindParam(':description', $description);
        $sql->bindParam(':cron', $cron);
        $sql->bindParam(':datasource', $datasource);
        $sql->bindParam(':enable', $enable);
        
        $sql->execute();    
        
        return $this->db->lastInsertId(); 
    }

    public function deleteDataSource($id) {
        $sql = $this->db->prepare("DELETE FROM $this->table WHERE id = :id");        
        $sql->bindParam(':id', $id);
        $sql->execute();
    }

    /**
     * $ds is SensorDataSource record
     */
    public function doSensorDataSourceSyncTask($ds)
    {
        try{

            $sensordatasource_id = $ds['id'];
            $sensor_id = $ds['sensor_id'];
            $datasource = json_decode($ds['datasource'], true);

            if( $datasource['type'] == "HTTP" ){
                if( $datasource['format'] == 'csv' ){
                    $this->downloadCSV($sensordatasource_id, $sensor_id, $datasource);
                }else if( $datasource['format'] == 'xml' ){
                    $this->downloadXML($sensordatasource_id, $sensor_id, $datasource);
                }
            }
            
            
        }catch(Exception $e){
            $this->addSensorDataSourceSync($sensordatasource_id, 'failure', $e->getMessage());
        }
        
    }

    private function downloadCSV($sensordatasource_id, $sensor_id, $datasource){
        // URL del file CSV remoto
        $url = $datasource['url'];
        $csvSeparator = $datasource['delimiter'];
        $csvHeader = $datasource['header'];

        $indexTimestamp = $datasource['indexTimestamp'];
        $indexM1 = $datasource['indexM1'];
        $indexM2 = isset($datasource['indexM2']) ? $datasource['indexM2'] : false;
        $indexM3 = isset($datasource['indexM3']) ? $datasource['indexM3'] : false;

        // Inizializza cURL
        $ch = curl_init($url);

        // Imposta le opzioni di cURL
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Esegui la richiesta e ottieni il contenuto del file CSV
        $csvContent = curl_exec($ch);

        // Verifica se il download è riuscito
        if ($csvContent !== false) {
            // Ora puoi manipolare i dati CSV come desiderato
            $csvFile = fopen('php://temp', 'r+');
            fwrite($csvFile, $csvContent);
            rewind($csvFile);

            if( $csvHeader ){
                fgetcsv($csvFile);
            }

            try {
                // Leggi il CSV utilizzando fgetcsv
                $this->db->beginTransaction();
                while (($data = fgetcsv($csvFile, null, $csvSeparator)) !== false) {
                    //var_dump($data);

                    $timestamp = $data[$indexTimestamp];
                    $valM1 = $data[$indexM1];
                    $valM2 = $indexM2 ? $data[$indexM2] : null;
                    $valM3 = $indexM3 ? $data[$indexM3] : null;
                    
                    $this->measurementModel->addMeasurement($sensor_id, 
                                                            $timestamp,
                                                            $valM1,
                                                            $valM2,
                                                            $valM3);
                }

                // Chiudi l'handle del file
                fclose($csvFile);

                $this->addSensorDataSourceSync($sensordatasource_id, 'success', '');

                $this->db->commit();

            } catch (Exception $ex) {
                
                $this->db->rollBack();
                $this->addSensorDataSourceSync($sensordatasource_id, 'failure', $ex->getMessage());
            }
            

        } else {
            
            $this->addSensorDataSourceSync($sensordatasource_id, 'failure', curl_error($ch));
        }

        // Chiudi la risorsa cURL
        curl_close($ch);
    }

    private function downloadXML($sensordatasource_id, $sensor_id, $datasource){
        // URL del file CSV remoto
        $url = $datasource['url'];
        
        $xpathList = $datasource['xpathList'];
        $xpathTimestamp = $datasource['xpathTimestamp'];
        $xpathValue = $datasource['xpathValue'];

        // Inizializza cURL
        $ch = curl_init($url);

        // Imposta le opzioni di cURL
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Esegui la richiesta e ottieni il contenuto del file CSV
        $xmlContent = curl_exec($ch);

        // Verifica se il download è riuscito
        if ($xmlContent !== false) {
            $xmlContent = str_replace('xmlns=', 'ns=', $xmlContent); //$string is a string that contains xml...
            $xml = new SimpleXMLElement($xmlContent);
           
            try {
                
                $this->db->beginTransaction();

                $result = $xml->xpath($xpathList);
                
                foreach($result as $measure) {
                    //var_dump($measure);
                    
                    $ts = $measure->xpath($xpathTimestamp)[0];

                    $time = strtotime($ts);
                    $timestamp = date('d/m/Y H:i',$time);

                    $valM1 = $measure->xpath($xpathValue)[0];
                    $valM2 = "";//$measure->xpath($xpathValue2)[0];
                    $valM3 = "";//$measure->xpath($xpathValue3)[0];
                    
                    $this->measurementModel->addMeasurement($sensor_id, 
                                                            $timestamp,
                                                            $valM1,
                                                            $valM2,
                                                            $valM3);
                  } 
                
                // Chiudi l'handle del file
                //fclose($xmlFile);

                $this->addSensorDataSourceSync($sensordatasource_id, 'success', '');

                $this->db->commit();

            } catch (Exception $ex) {
                
                $this->db->rollBack();
                $this->addSensorDataSourceSync($sensordatasource_id, 'failure', $ex->getMessage());
            }
            

        } else {
            
            $this->addSensorDataSourceSync($sensordatasource_id, 'failure', curl_error($ch));
        }

        // Chiudi la risorsa cURL
        curl_close($ch);
    }

    private function addSensorDataSourceSync($sensordatasource_id, $status, $log){
        $sql = $this->db->prepare("INSERT INTO SensorDataSourceSync (sensordatasource_id, status, log) 
                                    VALUES (:sensordatasource_id, :status, :log)");
        $sql->bindParam(':sensordatasource_id', $sensordatasource_id);
        $sql->bindParam(':status', $status);
        $sql->bindParam(':log', $log);
        $sql->execute();
    }
}
