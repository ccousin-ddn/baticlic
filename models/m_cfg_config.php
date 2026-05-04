<?php
/**
*** Juillet 2019@SoluFile 
**/
	require_once 'model_crud.php';
	
	class cfg_config_model extends crud {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->table 		= "cfg_config";
			$this->key			= "idconfig";
			$this->duplicateKey = false;
			
			$this->fields 		= "cfg_config.*";
			
			$this->joins 		= "
				
			";
			$this->conds 		= array();
			$this->groups 		= array();
			$this->orders 		= array();
		}
		
		public function getNumber($for){
			$_query = "
				UPDATE cfg_config 
				SET cfg_".$for."_num = (@num := cfg_".$for."_num) + 1 
				WHERE idconfig = 1;SELECT @num";
			$mysql = new mysql();
			$mysql->query($_query);
			$results = $mysql->multi_result();
			$num = $results[0]['@num'];
			switch($for){
				case "job" : break;
				case "del" : break;
				//case "quo" : $num = date("Y").str_pad($num, 4, "0", STR_PAD_LEFT); break;
				case "quo" : $num = date("y")." ".date("m")." ".str_pad($num, 4, "0", STR_PAD_LEFT); break;
				case "ord" : $num = date("y")." ".date("m")." ".str_pad($num, 4, "0", STR_PAD_LEFT); break;
				//case "inv" : $num = date("y")." ".date("m")." ".str_pad($num, 4, "0", STR_PAD_LEFT); break;
				case "inv" : $num = $num.".".date("m")."/".date("y"); break;
			}
			return $num;
		}
		
		public function undoNumber($for){
			$_query = "
				UPDATE cfg_config 
				SET cfg_".$for."_num = cfg_".$for."_num - 1 
				WHERE idconfig = 1";
			$mysql = new mysql();
			$mysql->query($_query);
			return $mysql->error;
		}
		
		public function getInfo($for){
			$_query = "SELECT cfg_".$for." FROM cfg_config WHERE idconfig = 1";
			$mysql = new mysql();
			$mysql->query($_query);
			$results = $mysql->result();
			return $results[0]["cfg_".$for];
		}
		
		public function getInfos(){
			$_query = "SELECT * FROM cfg_config WHERE idconfig = 1";
			$mysql = new mysql();
			$mysql->query($_query);
			$results = $mysql->result();
			return $results[0];
		}
		
		public function m_getById($idrecord)
		{
			$this->conds = array("idconfig = ".$idrecord);
			//$this->orders = array();
			//$this->debugging = true;
			
			return $this->select();
		}
		
		public function m_update($idrecord, $dataSent)
		{
			//$this->joins = "";
			$this->conds = array("idconfig = ".$idrecord);
			parse_str($dataSent, $data);
			//$this->debugging = true;
			
			return $this->update($data);
		}
		
		public function __destruct()
		{
		}
	}