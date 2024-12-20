<?php
class Database {
    private $host = DB_HOST;
    private $user = DB_USER;
    private $pass = DB_PASS;
    private $dbname = DB_NAME;

    private $dbh;
    private $stmt;
    private $error;

    public function __construct(){

        $dsn = "mysql:host=$this->host;dbname=$this->dbname";
        $options = [
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES => true
        ];
        try{
            $this->dbh = new PDO($dsn, $this->user, $this->pass, $options);        
        }catch(PDOException $e){
            $this->error = $e->getMessage();
            echo $this->error;
        }
    }
    public function query($sql){
        $this->stmt = $this->dbh->prepare($sql);
    }

    public function bind($param, $value, $type = null){
        if (is_null($type)) {
            switch (true) {
                case is_int($value):
                    $type = PDO::PARAM_INT;
                break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                break;
                default:
                    $type = PDO::PARAM_STR;
                break;
            }
        }
        $this->stmt->bindValue($param, $value, $type);
    }

    public function execute(){
        return $this->stmt->execute();
    }

    public function resultSet(){
        $this->execute();
        return $this->stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function single(){
        $this->execute();
        return $this->stmt->fetch(PDO::FETCH_OBJ);
    }

    public function rowCount(){
        return $this->stmt->rowCount();
    }

    public function first(){
		return $this->resultSet()[0];
	}

	public function last(){
		$count = $this->rowCount();
		$x = $count - 1;
		return $this->resultSet()[$x];
	}

    public function createRow($table, $data){
        $fields = '';
        $values = '';
        $count = count($data);
        $x = 0;

        foreach($data as $key => $value){
            if($x == $count - 1){
                $fields .= $key;
                $values .= ':'.$key;
            }else{
                $fields .= $key.', ';
                $values .= ':'.$key.', ';
            }
           $x++;
        }
        $this->query('INSERT INTO '.$table.' ('.$fields.') VALUES ('.$values.')');
        foreach($data as $key => $value){
            $this->bind(':'.$key, $value);
        }
        if($this->execute()){
            return true;
        }else{
            return false;
        }
	}

    public function createMultiRow($table, $data){

        foreach($data as $key => $value){
            $fields = '';
            $values = '';
            $count = count($value);
            $x = 0;
            foreach($value as $lock => $rate){
                if($x == $count - 1){
                    $fields .= $lock;
                    $values .= ':'.$lock;
                }else{
                    $fields .= $lock.', ';
                    $values .= ':'.$lock.', ';
                }
               $x++;
            }
            $this->query('INSERT INTO '.$table.' ('.$fields.') VALUES ('.$values.')');
            foreach($value as $lock => $rate){
                $this->bind(':'.$lock, $rate);
            }
            if($this->execute()){
                return true;
            }else{
                return false;
            }
        }
    }

    public function getRowById($table, $id){

        if(is_array($id)){
            $result = [];
            foreach($id as $key => $value){
                $this->query('SELECT * FROM '.$table.' WHERE id = :id');
                $this->bind(':id', $value);
                array_push($result, $this->single());
            }
            return (object)$result; 
        }elseif(is_int($id)){
            $this->query('SELECT * FROM '.$table.' WHERE id = :id');
            $this->bind(':id', $id);
            return $this->single();
        }
	}

    public function updateRowById($table, $id, $data){
        
        $fields = '';
        $count = count($data);
        $x = 0;
        foreach($data as $key => $value){
            if($x == $count - 1){
                $fields .= $key.' = :'.$key;
            }else{
                $fields .= $key.' = :'.$key.', ';
            }
           $x++;
        }

        $this->query('UPDATE '.$table.' SET '.$fields.' WHERE id = :id');
        $this->bind(':id', $id);
        foreach($data as $key => $value){
            $this->bind(':'.$key, $value);
        }
        if($this->execute()){
            return true;
        }else{
            return false;
        }
	}

	public function deleteRowById($table, $id){
        if(is_array($id)){
            foreach($id as $value){
                if(is_int($value)){
                    $this->query('DELETE FROM '.$table.' WHERE id = :id');
                    $this->bind(':id', $value);
                    if($this->execute()){
                        return true;
                    }else{
                        return false;
                    }
                }
            }
        }elseif(is_int($id)){
            $this->query('DELETE FROM '.$table.' WHERE id = :id');
            $this->bind(':id', $id);
    
            if($this->execute()){
                return true;
            }else{
                return false;
            }
        }
	}


    public function searchField($table, $field, $string){
        $this->query("SELECT * FROM ".$table." WHERE ".$field." LIKE '%".$string."%'");
        return $this->resultSet();
    }
}