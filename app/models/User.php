<?php
class User{
    private $db;
    private $userTable;

    public function __construct(){
        $this->db = new Database;
        $this->userTable = 'users';
    }

    public function register($data){
        $this->db->createRow($this->userTable, $data);
    }

    public function findUserByEmail($email){
        $this->db->query('SELECT * FROM users WHERE email = :email');
        $this->db->bind(':email', $email);
        return $this->db->single();
    }

    public function findUserById($id){
        $this->db->getRowById($this->userTable, $id);
    }

    public function login($data){
        $this->db->query('SELECT * FROM users WHERE email = :email');
        $this->db->bind(':email', $data->email);
        $row = $this->db->single();
        if(password_verify($data->password, $row->password)){
            return $row;
        }else{
            return false;
        }
    }

    public function deleteOneUserById($id){
       return $this->db->deleteRowById($this->userTable, $id);
    }

    Public function updateUser($id, $data){
        if(is_int($id)){
            $this->db->updateRowById($this->userTable, $id, $data);
        }elseif(Data::is_email($id)){
            $this->db->query('SELECT * FROM users WHERE email = :email');
            $this->db->bind(':email', $id);
            return $this->findUserByEmail($id);
        }
    }
}