<?php

namespace App\Application\Models;

use DateTime;
use DateTimeZone;
use PDO;

enum FieldOperator: int
{
    case LessThan = 1;
    case LessThanOrEqual = 2;
    case Equal = 3;
    case GreaterThanOrEqual = 4;
    case GreaterThan = 5;
}

class MeasurementModel
{
    private $table = "Measurement";
    protected $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getMeasurementsBySensorId($sensor_id, $date_from, $date_to, $last_days, $limitRecords, $sort_dir = "DESC")
    {

        $sql = "SELECT * FROM $this->table WHERE sensor_id = :sensor_id ";

        if (isset($date_from) && isset($date_to)) {
            $sql = $sql . "and timestamp BETWEEN :date_from AND :date_to";
        } else if (isset($date_from)) {
            $sql = $sql . "and timestamp >= :date_from ";
        } else if (isset($date_to)) {
            $sql = $sql . "and timestamp <= :date_to ";
        } else if (isset($last_days)) {
            $sql = $sql . "and timestamp >= (SELECT MAX(timestamp) - INTERVAL :last_days DAY FROM $this->table WHERE sensor_id = :sensor_id)";
        }

        $sql = $sql . " ORDER BY timestamp $sort_dir ";

        if (isset($limitRecords)) {
            $sql = $sql . " LIMIT :limitRecords";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':sensor_id', $sensor_id);

        if (isset($date_from) && isset($date_to)) {
            $stmt->bindParam(':date_from', $date_from);
            $stmt->bindParam(':date_to', $date_to);
        } else if (isset($date_from)) {
            $stmt->bindParam(':date_from', $date_from);
        } else if (isset($date_to)) {
            $stmt->bindParam(':date_to', $date_to);
        } else if (isset($last_days)) {
            $stmt->bindParam(':last_days', $last_days);
        }

        if (isset($limitRecords)) {
            $stmt->bindValue(':limitRecords', (int)$limitRecords, PDO::PARAM_INT);
        }
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getMeasurementsTrendBySensorId($sensor_id)
    {

        $sql = "SELECT * FROM
                (SELECT * FROM $this->table WHERE sensor_id = :sensor_id ORDER BY timestamp DESC LIMIT 10) AS Recent
                ORDER BY timestamp ASC";
      
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':sensor_id', $sensor_id);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getLastMeasure($user_id)
    {
        $sql = $this->db->prepare('SELECT Measurement.*,
                                          Sensor.name as sensor_name,
                                          Site.name as site_name
                                    FROM Measurement 
                                    inner join Sensor ON Measurement.sensor_id = Sensor.id 
                                    inner join Site ON Sensor.site_id = Site.id 
                                    WHERE Site.user_id = :user_id
                                    order by Measurement.timestamp DESC, Measurement.id DESC
                                    LIMIT 1');
        $sql->bindParam(':user_id', $user_id);
        $sql->execute();

        return $sql->fetch();
    }

    public function getSensorLastMeasure($sensor_id)
    {
        $sql = $this->db->prepare('SELECT Measurement.*,
                                          Sensor.name as sensor_name,
                                          Site.name as site_name
                                    FROM Measurement 
                                    inner join Sensor ON Measurement.sensor_id = Sensor.id 
                                    inner join Site ON Sensor.site_id = Site.id 
                                    WHERE Sensor.id = :sensor_id
                                    order by Measurement.timestamp DESC, Measurement.id DESC
                                    LIMIT 1');
        $sql->bindParam(':sensor_id', $sensor_id);
        $sql->execute();

        return $sql->fetch();
    }

    public function getMeasurementsBySiteId($site_id)
    {
        $sql = "SELECT
        se.id sensor_id,
        se.name sensor_name,
        st.name sensor_type,
        st.um sensor_um,
        st.number_values sensor_number_values,
        st.label_m1 sensor_label_m1,
        st.label_m2 sensor_label_m2,
        st.label_m3 sensor_label_m3,        
        m.timestamp measurement_timestamp_last,
        m.m1 m1_last,
        m.m2 m2_last,
        m.m3 m3_last,
        ar.name alertrule_name,
        ar.field_name alertrule_fieldname,
        ar.field_threshold alertrule_threshold,
        ar.level alert_level
       FROM
         Site s
       JOIN
         Sensor se ON s.id = se.site_id
       JOIN
         SensorType st ON se.sensortype_id = st.id
       JOIN
         Measurement m ON se.id = m.sensor_id AND m.timestamp = (
           SELECT MAX(timestamp) FROM Measurement WHERE sensor_id = se.id
         )
       LEFT JOIN AlertRule ar ON se.id = ar.sensor_id AND (
           (ar.field_name = 'm1' AND m.m1 > ar.field_threshold) OR
           (ar.field_name = 'm2' AND m.m2 > ar.field_threshold) OR
           (ar.field_name = 'm3' AND m.m3 > ar.field_threshold)
       )
       WHERE s.id = :site_id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':site_id', $site_id);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function addMeasurement($sensor_id, $ts, $m1, $m2, $m3, $timezone = 'Europe/Rome', $format = 'd/m/Y H:i')
    {  

        $timestamp = $this->formatDateTime($ts, $timezone, $format);

        $deleteSql = "DELETE FROM $this->table WHERE sensor_id = :sensor_id AND timestamp = :timestamp";
        $deleteStmt = $this->db->prepare($deleteSql);
        $deleteStmt->bindParam(':sensor_id', $sensor_id);
        $deleteStmt->bindValue(':timestamp', $timestamp->format('Y-m-d H:i'));
        $deleteStmt->execute();

        $sql = $this->db->prepare("INSERT INTO $this->table (sensor_id, timestamp, year, month, day, hour, m1, m2, m3) VALUES (:sensor_id, :timestamp, :year, :month, :day, :hour, :m1, :m2, :m3);");
        $sql->bindParam(':sensor_id', $sensor_id);
        $sql->bindValue(':timestamp', $timestamp->format('Y-m-d H:i'));
        $sql->bindValue(':year',  $timestamp->format('Y'));
        $sql->bindValue(':month', $timestamp->format('m'));
        $sql->bindValue(':day',   $timestamp->format('d'));
        $sql->bindValue(':hour',  $timestamp->format('H'));
        $sql->bindParam(':m1', $m1);
        $sql->bindParam(':m2', $m2);
        $sql->bindParam(':m3', $m3);
        $sql->execute();
        $id = $this->db->lastInsertId();

        $this->checkAlertRules($id, $sensor_id, $m1, $m2, $m3);

        return $id;
    }

    public function uploadCSV($sensor_id, $filepath)
    {
        //$timezone = new DateTimeZone('Europe/Rome');
        //$format = "d/m/Y H:i";
        if (($handle = fopen($filepath, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

               //$timestamp = DateTime::createFromFormat($format, $data['0'], $timezone);
                $timestamp = $this->formatDateTime($data['0']);
                $m1 = $data[1];
                $m2 = $data[2];
                $m3 = $data[3];

                $this->addMeasurement($sensor_id, $timestamp, $m1, $m2, $m3);
            }
            fclose($handle);
        }
    }

    public function deleteMeasurement($id)
    {
        $sql = $this->db->prepare("DELETE FROM $this->table WHERE id = :id");
        $sql->bindParam(':id', $id);
        $sql->execute();
    }

    public function checkAlertRules($measurement_id, $sensor_id, $m1, $m2, $m3, $sendAlert = true)
    {
        $verified_rules = array();
        $sql = $this->db->prepare('SELECT 
                                    AlertRule.*,
                                    Sensor.name as sensor_name,
                                    Site.name as site_name 
                                   FROM AlertRule 
                                   INNER JOIN Sensor ON AlertRule.sensor_id = Sensor.id
                                   INNER JOIN Site ON Sensor.site_id = Site.id
                                   WHERE AlertRule.sensor_id = :sensor_id ');
        $sql->bindParam(':sensor_id', $sensor_id);
        $sql->execute();

        $rules =  $sql->fetchAll();

        if (count($rules) > 0) {
            for ($i = 0; $i < count($rules); $i++) {

                $alertrule_id = $rules[$i]['id'];
                $alertrule_name = $rules[$i]['name'];
                $alertrule_level = $rules[$i]['level'];
                $field_name = $rules[$i]['field_name'];
                $field_operator = $rules[$i]['field_operator'];
                $field_threshold  = $rules[$i]['field_threshold'];
                $sensor_name = $rules[$i]['sensor_name'];
                $site_name  = $rules[$i]['site_name'];

                $ruleVerified = false;
                $value_to_match = 0;
                switch ($field_name) {
                    case "m1":
                        $value_to_match = $m1;
                        break;
                    case "m2":
                        $value_to_match = $m2;
                        break;
                    case "m3":
                        $value_to_match = $m3;
                        break;
                }

                $ruleVerified = $this->checkRule($value_to_match, FieldOperator::from($field_operator), $field_threshold);

                if ($ruleVerified) {
                    array_push($verified_rules, [
                        "alertrule_id" => $alertrule_id,
                        "alertrule_name" => $alertrule_name,
                        "rule_verified" => $ruleVerified,
                        "field_name" => $field_name,
                        "alertrule_level" => $alertrule_level
                    ]);
                }

                if ($ruleVerified && $sendAlert) {
                    $sql = $this->db->prepare("INSERT INTO Alert(alertrule_id, measurement_id, sent) 
                                                VALUES (:alertrule_id,:measurement_id, 0)");
                    $sql->bindParam(':alertrule_id', $alertrule_id);
                    $sql->bindParam(':measurement_id', $measurement_id);
                    $sql->execute();
                }
            }
        }

        return $verified_rules;
    }

    public function sendAlert(){
        $sql = $this->db->prepare('SELECT 
                                        Alert.*,
                                        AlertRule.name as alertrule_name,
                                        AlertRule.field_name,
                                        AlertRule.field_threshold,
                                        Sensor.name as sensor_name,
                                        Site.name as site_name
                                    FROM Alert 
                                    JOIN AlertRule ON Alert.alertrule_id = AlertRule.id
                                    JOIN Sensor ON AlertRule.sensor_id = Sensor.id
                                    JOIN Site ON Sensor.site_id = Site.id
                                    WHERE Alert.sent = 0');
        $sql->execute();
        $alerts =  $sql->fetchAll();

        foreach($alerts as $alert){
            
            $sql = $this->db->prepare('SELECT * FROM AlertRuleRecipient WHERE alertrule_id = :alertrule_id ');
            $sql->bindParam(':alertrule_id', $alert['alertrule_id']);
            $sql->execute();
    
            $recipients =  $sql->fetchAll();

            if (count($recipients) > 0) {
                for ($i = 0; $i < count($recipients); $i++) {
    
                    $alertrule_name = $alert['alertrule_name'];
                    $message = "Rule: $alertrule_name \n\n";
                    $message = $message . "Field name: ".$alert['field_name']."\n\n";
                    $message = $message . "Field threshold: ".$alert['field_threshold']." \n\n";
                    $message = $message . "Sensor name: ".$alert['sensor_name']." \n\n";
                    $message = $message . "Site name: ".$alert['site_name']."\n\n";
    
                    switch ($recipients[$i]['recipient_type']) {
                        case "email":                            
                            $this->sendEmail($recipients[$i]['recipient'], "Alert: $alertrule_name", $message);
                            break;
                        case "telegram":
                            $this->sendTelegram($recipients[$i]['recipient'], $message);
                            break;
                    }
                }
            }

            $sql = $this->db->prepare('UPDATE Alert set sent = 1 WHERE id = :id ');
            $sql->bindParam(':id', $alert['id']);
            $sql->execute();
    
        }
        
    }

    public function checkRule($value, FieldOperator $field_operator, $field_threshold)
    {
        $matched = false;
        switch ($field_operator) {
            case FieldOperator::LessThan:
                $matched = $value < $field_threshold;
                break;
            case FieldOperator::LessThanOrEqual:
                $matched = $value <= $field_threshold;
                break;
            case FieldOperator::Equal:
                $matched = $value == $field_threshold;
                break;
            case FieldOperator::GreaterThanOrEqual:
                $matched = $value >= $field_threshold;
                break;
            case FieldOperator::GreaterThan:
                $matched = $value > $field_threshold;
                break;
        }
        return $matched;
    }

    private function formatDateTime($timestamp, $timezone = 'Europe/Rome', $format = 'd/m/Y H:i'){
        
        $dtTimeZone = new DateTimeZone($timezone);

        $timestamp = DateTime::createFromFormat($format, $timestamp, $dtTimeZone);

        return $timestamp;
    }

    private function sendEmail($recipient, $alertrule_name, $message){
        $headers = array(
            'From' => 'noreply@smoswip.tech',
            'X-Mailer' => 'PHP/' . phpversion()
        );
        mail(
            $recipient,
            "Alert: $alertrule_name",
            $message,
            $headers
        );
    }

    private function sendTelegram($recipient, $message){
        $botApiToken = $_ENV['TELEGRAM_BOT_API_TOKEN'];
        $channelId = $recipient;
        

        $query = http_build_query([
            'chat_id' => $channelId,
            'text' => $message,
        ]);
        
        $url = "https://api.telegram.org/bot{$botApiToken}/sendMessage?{$query}";

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));
        curl_exec($curl);
        curl_close($curl);
    }
}
