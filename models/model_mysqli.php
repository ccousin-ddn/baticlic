<?php
/*
* Gère l'ouverture à une base de données MySQL
* 
* 2016 SoluFile creation
*/

class mysql {
/*~*~*~*~*~*~*~*~*~*~*/
/*  1. propriétés    */
/*~*~*~*~*~*~*~*~*~*~*/

private $mysql_db;
public $mysql_cnx;
public $error;
public $count;
/*~*~*~*~*~*~*~*~*~*~*/
/*  2. méthodes      */
/*~*~*~*~*~*~*~*~*~*~*/

/****************/
/* Constructeur */
/****************/
function __construct()
{
$this->error = "";
$this->mysql_cnx = 0;
$this->result = "";
$this->connected = 0;
$this->connect();
$this->count = 0;
}
/*~*~*~*~*~*~*~*~*~*~*~*~*~*/
/*  2.1 méthodes privées   */
/*~*~*~*~*~*~*~*~*~*~*~*~*~*/
private function mysql_error($query){throw new Exception('ERROR-DB : '.mysqli_error($this->mysql_cnx).' ['.trim($query).'] ', 402);}
private function chooseDb(){$this->mysql_db = mysqli_select_db($this->mysql_cnx,MYSQL_DB);if(!$this->mysql_db){throw new Exception('ERROR-DB : '.mysqli_error($this->mysql_cnx).' [Select DB] ', 401);}else{$this->connected = 1;}}

/*~*~*~*~*~*~*~*~*~*~*~*~*~*/
/*  2.2 méthodes publiques */
/*~*~*~*~*~*~*~*~*~*~*~*~*~*/
public function connect(){if($this->connected == 0){$this->mysql_cnx = @mysqli_connect(MYSQL_SERVER, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DB, MYSQL_PORT);if (mysqli_connect_errno()){throw new Exception('ERROR-DB : '.mysqli_connect_error().' [connect] ', 400);}else{mysqli_set_charset($this->mysql_cnx,"utf8");}}}
public function query($query){$query = preg_replace('/(\v|\s)+/', ' ', $query);if($this->result = mysqli_multi_query($this->mysql_cnx,$query)){return true;}else{throw new Exception('ERROR-DB : '.mysqli_error($this->mysql_cnx).'<br><br>'.str_replace(array("\r\n","\n","\r","\t"), '', $query).'<br>', 402);return false;}}

public function result(){if ($this->result = mysqli_store_result($this->mysql_cnx)){while ($row[] = mysqli_fetch_array($this->result,MYSQLI_ASSOC)){$this->count++;};mysqli_free_result($this->result);array_pop($row);return $row;}}
public function multi_result(){do{if ($this->result = mysqli_store_result($this->mysql_cnx)){while ($row[] = mysqli_fetch_array($this->result,MYSQLI_ASSOC)){};mysqli_free_result($this->result);array_pop($row);}}while(mysqli_more_results($this->mysql_cnx) && mysqli_next_result($this->mysql_cnx));return $row;}

public function lastId(){return @mysqli_insert_id($this->mysql_cnx);}
public function fields(){return @mysqli_num_fields($this->result);}

/***************/
/* Destructeur /*
/***************/
function __destruct() {mysqli_close($this->mysql_cnx);}}
