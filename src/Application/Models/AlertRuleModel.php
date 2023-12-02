<?php

namespace App\Application\Models;
use PDO;

class AlertRuleModel {
    private $table = "AlertRule";
    protected $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAlertRulesByUserId($user_id) {
        $sql = $this->db->prepare("SELECT 
                                        AlertRule.*,
                                        Sensor.name sensor_name,
                                        Site.id site_id,
                                        Site.name site_name
                                    FROM $this->table
                                    INNER JOIN Sensor ON AlertRule.sensor_id = Sensor.id
                                    INNER JOIN Site ON Sensor.site_id = Site.id
                                    WHERE Site.user_id = :user_id");        
        $sql->bindParam(':user_id', $user_id);
        $sql->execute();        
        return $sql->fetchAll();
    }

    public function getAlertRulesBySensorId($sensor_id) {
        $sql = $this->db->prepare("SELECT 
                                        AlertRule.*,
                                        (SELECT
                                               GROUP_CONCAT(DISTINCT CONCAT(arr.recipient_type,' : ',arr.Recipient)  ORDER BY arr.recipient_type SEPARATOR ', ') AS RecipientsList
                                            FROM
                                                AlertRule ar
                                            LEFT JOIN
                                                AlertRuleRecipient arr ON ar.id = arr.alertrule_id
                                            WHERE
                                                ar.id = AlertRule.id
                                            GROUP BY
                                                ar.id
                                        )
                                        as recipients,
                                        Sensor.name sensor_name,
                                        SensorType.name AS sensor_type,
                                        SensorType.um_label AS sensor_um_label,
                                        SensorType.um AS sensor_um,
                                        SensorType.number_values AS sensor_number_values,
                                        SensorType.label_m1 AS sensor_label_m1,
                                        SensorType.label_m2 AS sensor_label_m2,
                                        SensorType.label_m3 AS sensor_label_m3,
                                        Site.id site_id,
                                        Site.name site_name
                                    FROM $this->table
                                    INNER JOIN Sensor ON AlertRule.sensor_id = Sensor.id
                                    INNER JOIN SensorType ON SensorType.id = Sensor.sensortype_id
                                    INNER JOIN Site ON Sensor.site_id = Site.id
                                    WHERE Sensor.id = :sensor_id");        
        $sql->bindParam(':sensor_id', $sensor_id);
        $sql->execute();        
        return $sql->fetchAll();
    }

    public function getAlertRule($id) {
        $sql = $this->db->prepare("SELECT 
                                    AlertRule.*,
                                    Sensor.name sensor_name,
                                    Site.id site_id,
                                    Site.name site_name
                                FROM $this->table
                                INNER JOIN Sensor ON AlertRule.sensor_id = Sensor.id
                                INNER JOIN Site ON Sensor.site_id = Site.id
                                WHERE  $this->table.id = :id");        
        $sql->bindParam(':id', $id);
        $sql->execute();        
        return $sql->fetch();
    }
    
    public function addAlertRule($sensor_id, $name, $level, $field_name, $field_operator, $field_threshold, $recipient_email, $recipient_telegram) {
        $sql = $this->db->prepare("INSERT INTO $this->table (`sensor_id`, `name`, `field_name`, `field_operator`, `field_threshold`, `level`) 
                                                     VALUES (:sensor_id,   :name,  :field_name,  :field_operator, :field_threshold, :level);");        
        $sql->bindParam(':sensor_id', $sensor_id);
        $sql->bindParam(':name', $name);       
        $sql->bindParam(':field_name', $field_name);
        $sql->bindParam(':field_operator', $field_operator);
        $sql->bindParam(':field_threshold', $field_threshold);
        $sql->bindParam(':level', $level, PDO::PARAM_STR);
        $sql->execute();    
        $alertrule_id = $this->db->lastInsertId();  

        $sql = $this->db->prepare("INSERT INTO AlertRuleRecipient (`alertrule_id`, `recipient_type`, `recipient`) 
                                                     VALUES (:alertrule_id,   :recipient_type,  :recipient);");        
        $sql->bindParam(':alertrule_id', $alertrule_id);
        $sql->bindValue(':recipient_type', 'email');
        $sql->bindParam(':recipient', $recipient_email);
        $sql->execute(); 

        $sql->bindParam(':alertrule_id', $alertrule_id);
        $sql->bindValue(':recipient_type', 'telegram');
        $sql->bindParam(':recipient', $recipient_telegram);
        $sql->execute();

        return $alertrule_id;
    }

    public function addAlertRuleRecipient($alertrule_id, $recipient_type, $recipient){
        $sql = $this->db->prepare("INSERT INTO AlertRuleRecipient (`alertrule_id`, `recipient_type`, `recipient`) 
                                    VALUES (:alertrule_id,   :recipient_type,  :recipient);");        
        $sql->bindParam(':alertrule_id', $alertrule_id);
        $sql->bindParam(':recipient_type', $recipient_type);
        $sql->bindParam(':recipient', $recipient);
        $sql->execute(); 
        return $this->db->lastInsertId();
    }

    
    public function deleteAlertRuleRecipient($id) {
        $sql = $this->db->prepare("DELETE FROM AlertRuleRecipient WHERE id = :id");        
        $sql->bindParam(':id', $id);
        $sql->execute();
    }
    public function deleteAlertRule($id) {
        $sql = $this->db->prepare("DELETE FROM $this->table WHERE id = :id");        
        $sql->bindParam(':id', $id);
        $sql->execute();
    }

    public function getAlertRuleRecipients($alertrule_id) {
        $sql = $this->db->prepare("SELECT * FROM AlertRuleRecipient
                                    WHERE alertrule_id = :alertrule_id");        
        $sql->bindParam(':alertrule_id', $alertrule_id);
        $sql->execute();        
        return $sql->fetchAll();
    }
}