<?php

namespace App\Application\Models;

class SensorModel {
    private $table = "Sensor";
    protected $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getSensorsBySiteId($site_id) {
        $sql = $this->db->prepare("SELECT Sensor.*, 
                                    SensorType.name AS sensor_type,
                                    SensorType.um_label AS sensor_um_label,
                                    SensorType.um AS sensor_um,
                                    SensorType.number_values AS sensor_number_values,
                                    SensorType.label_m1 AS sensor_label_m1,
                                    SensorType.label_m2 AS sensor_label_m2,
                                    SensorType.label_m3 AS sensor_label_m3
                                    
                                    FROM Sensor 
                                    INNER JOIN SensorType ON Sensor.sensortype_id = SensorType.id 
                                    WHERE
                                        Sensor.site_id = :site_id
                                    ");        
        $sql->bindParam(':site_id', $site_id);
        $sql->execute();        
        return $sql->fetchAll();
    }

    public function getSensorsWithLastMeasureBySiteId($site_id) {
        $sql = $this->db->prepare("SELECT Sensor.*, 
                                    SensorType.name AS sensor_type,
                                    SensorType.um_label AS sensor_um_label,
                                    SensorType.um AS sensor_um,
                                    SensorType.number_values AS sensor_number_values,
                                    SensorType.label_m1 AS sensor_label_m1,
                                    SensorType.label_m2 AS sensor_label_m2,
                                    SensorType.label_m3 AS sensor_label_m3,
                                    Measurement.timestamp,
                                    Measurement.m1,
                                    Measurement.m2,
                                    Measurement.m3,
                                    m_max.id measurement_id_last,
                                    m_max.timestamp measurement_timestamp_last,
                                    m_max.m1 m1_last,
                                    m_max.m2 m2_last,
                                    m_max.m3 m3_last
                                    FROM Sensor 
                                    INNER JOIN SensorType ON Sensor.sensortype_id = SensorType.id 
                                    LEFT JOIN Measurement ON Sensor.first_measurement_id = Measurement.id
                                    LEFT JOIN Measurement m_max ON Sensor.id = m_max.sensor_id
                                    WHERE
                                        Sensor.site_id = :site_id
                                        AND m_max.Timestamp = (
                                            SELECT 
                                                MAX(Timestamp)
                                            FROM
                                                Measurement
                                            WHERE
                                                sensor_id = Sensor.id
                                            ORDER BY Measurement.id DESC
                                            LIMIT 1
                                        )
                                        AND m_max.id = (
                                            SELECT 
                                                id
                                            FROM
                                                Measurement
                                            WHERE
                                                sensor_id = Sensor.id
                                            ORDER BY Measurement.id DESC
                                            LIMIT 1
                                        );");        
        $sql->bindParam(':site_id', $site_id);
        $sql->execute();        
        return $sql->fetchAll();
    }

    public function getSensor($id) {
        $sql = $this->db->prepare("SELECT Sensor.*, 
                                            SensorType.name AS sensor_type,
                                            SensorType.um_label AS sensor_um_label,
                                            SensorType.um AS sensor_um,
                                            SensorType.number_values AS sensor_number_values,
                                            SensorType.label_m1 AS sensor_label_m1,
                                            SensorType.label_m2 AS sensor_label_m2,
                                            SensorType.label_m3 AS sensor_label_m3,
                                            Measurement.timestamp,
                                            Measurement.m1,
                                            Measurement.m2,
                                            Measurement.m3,
                                            m_max.id measurement_id_last,
                                            m_max.m1 m1_last,
                                            m_max.m2 m2_last,
                                            m_max.m3 m3_last
                                    FROM $this->table 
                                    INNER JOIN SensorType ON Sensor.sensortype_id = SensorType.id 
                                    LEFT JOIN Measurement ON Sensor.first_measurement_id = Measurement.id
                                    LEFT JOIN Measurement m_max ON Sensor.id = m_max.sensor_id
                                    WHERE Sensor.id = :id
                                    ORDER BY m_max.timestamp DESC
                                    LIMIT 1");        
        $sql->bindParam(':id', $id);
        $sql->execute();        
        return $sql->fetch();
    }
    
    public function getSensorList($user_id){
        $sql = $this->db->prepare("SELECT Sensor.* FROM $this->table 
                                   INNER JOIN Site ON $this->table.site_id = Site.id
                                   WHERE Site.user_id = :user_id
                                   ORDER BY Site.name ASC, Sensor.name ASC");        
        $sql->bindParam(':user_id', $user_id);
        $sql->execute();        
        return $sql->fetchAll();
    }

    public function getSensorType() {
        $sql = $this->db->prepare("SELECT * FROM SensorType");
        $sql->execute();        
        return $sql->fetchAll();
    }

    public function addSensor($site_id, $sensortype_id, $name, $description, $lat, $lon, $alt) {
        $sql = $this->db->prepare("INSERT INTO $this->table (site_id, sensortype_id, name, description, lat, lon, alt) VALUES (:site_id,:sensortype_id,:name,:description,:lat,:lon,:alt);");        
        $sql->bindParam(':site_id', $site_id);
        $sql->bindParam(':sensortype_id', $sensortype_id);
        $sql->bindParam(':name', $name);
        $sql->bindParam(':description', $description);
        $sql->bindParam(':lat', $lat);
        $sql->bindParam(':lon', $lon);
        $sql->bindParam(':alt', $alt);
        $sql->execute();    
        return $this->db->lastInsertId();  
    }

    public function deleteSensor($id) {
        $sql = $this->db->prepare("DELETE FROM $this->table WHERE id = :id");        
        $sql->bindParam(':id', $id);
        $sql->execute();
    }
}