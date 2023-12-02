<?php

namespace App\Application\Models;

class SiteModel {
    private $table = "Site";
    protected $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getSitesByUserId($user_id) {
        $sql = $this->db->prepare("SELECT
                                        s.*,
                                        ar.name alertrule_name,
                                        ar.level alert_level
                                    FROM
                                        Site s
                                    JOIN
                                        Sensor se ON s.id = se.site_id
                                    JOIN
                                        SensorType st ON se.sensortype_id = st.id
                                    LEFT JOIN
                                        Measurement m ON se.id = m.sensor_id AND m.timestamp = (
                                        SELECT MAX(timestamp) FROM Measurement WHERE sensor_id = se.id
                                        )
                                    LEFT JOIN AlertRule ar ON se.id = ar.sensor_id AND (
                                        (ar.field_name = 'm1' AND m.m1 > ar.field_threshold) OR
                                        (ar.field_name = 'm2' AND m.m2 > ar.field_threshold) OR
                                        (ar.field_name = 'm3' AND m.m3 > ar.field_threshold)
                                    )
                                    WHERE s.user_id = :user_id
                                    GROUP BY s.id
                                    ORDER BY s.name ASC, ar.level ASC");        
        $sql->bindParam(':user_id', $user_id);
        $sql->execute();        
        return $sql->fetchAll();
    }

    public function getSite($id, $user_id = 0) {
        $sql = null;
        if($user_id == 0) {
            $sql = $this->db->prepare("SELECT * FROM $this->table WHERE id = :id");        
            $sql->bindParam(':id', $id);
        }else{
            $sql = $this->db->prepare("SELECT * FROM $this->table WHERE id = :id AND user_id = :user_id");        
            $sql->bindParam(':id', $id);
            $sql->bindParam(':user_id', $user_id);
        }
        
        $sql->execute();   

        return $sql->fetch();
    }
    
    public function addSite($user_id, $name, $description, $lat, $lon) {
        $sql = $this->db->prepare("INSERT INTO $this->table (user_id, name, description, lat, lon) VALUES (:user_id,:name,:description,:lat,:lon);");        
        $sql->bindParam(':user_id', $user_id);
        $sql->bindParam(':name', $name);
        $sql->bindParam(':description', $description);
        $sql->bindParam(':lat', $lat);
        $sql->bindParam(':lon', $lon);
        $sql->execute();    
        return $this->db->lastInsertId();  
    }

    public function deleteSite($id) {
        $sql = $this->db->prepare("DELETE FROM $this->table WHERE id = :id");        
        $sql->bindParam(':id', $id);
        $sql->execute();
    }
}