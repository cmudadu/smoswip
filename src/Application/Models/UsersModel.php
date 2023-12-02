<?php

namespace App\Application\Models;

class UsersModel {
    private $table = "User";
    protected $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getById($id) {
        $sql = $this->db->prepare("select * from $this->table where id = :id");
        $sql->bindParam(':id', $id);
        $sql->execute();

        return $sql->fetch();
    }

    public function getByLogin($email) {
        $sql = $this->db->prepare("select * from $this->table where lower(email) = lower(:email)");
        $sql->bindValue(':email', $email);
        $sql->execute();

        return $sql->fetch();
    }

    public function updateLastLogin($id) {
        $sql = $this->db->prepare("update $this->table set last_login = NOW() where id = :id");
        $sql->bindValue(':id', $id);
        $sql->execute();
    }
}