<?php

namespace App\Application\Models;

class AlertModel {
    private $table = "Alert";
    protected $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAlertsByUserId($user_id, $site_id, $top = 0) {
        $query = "SELECT 
                    Alert.*,
                    Measurement.timestamp as measure_timestamp,
                    AlertRule.name as alertrule_name,
                    AlertRule.level as alertrule_level,
                    AlertRule.field_name as alertrule_field_name,
                    CASE
                        WHEN AlertRule.field_operator = 1 THEN '<' 
                        WHEN AlertRule.field_operator = 2 THEN '<=' 
                        WHEN AlertRule.field_operator = 3 THEN '=' 
                        WHEN AlertRule.field_operator = 4 THEN '>=' 
                        WHEN AlertRule.field_operator = 5 THEN '>' 
                    END AS alertrule_field_operator,
                    AlertRule.field_threshold as alertrule_field_threshold,                                        
                    CASE
                        WHEN AlertRule.field_name = 'm1' THEN Measurement.m1 
                        WHEN AlertRule.field_name = 'm2' THEN Measurement.m2 
                        WHEN AlertRule.field_name = 'm3' THEN Measurement.m3 
                        
                    END AS measure_value,
                    Sensor.name sensor_name,
                    Site.id site_id,
                    Site.name site_name
                FROM $this->table
                INNER JOIN Measurement ON $this->table.measurement_id = Measurement.id
                INNER JOIN AlertRule ON $this->table.alertrule_id = AlertRule.id
                INNER JOIN Sensor ON AlertRule.sensor_id = Sensor.id
                INNER JOIN Site ON Sensor.site_id = Site.id
                WHERE Site.user_id = :user_id";
        
        if( $site_id > 0) {  
            $query = $query . "  AND Site.id = :site_id ";
        }

        $query = $query . " ORDER BY Alert.id DESC";
        
        if( $top > 0) {
            $query = $query . " LIMIT $top";
        }
        
        $sql = $this->db->prepare($query);        
        $sql->bindParam(':user_id', $user_id);
        if($site_id>0){
            $sql->bindParam(':site_id', $site_id);        
        }
        $sql->execute();        
        return $sql->fetchAll();
    }

}