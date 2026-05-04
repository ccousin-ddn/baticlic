<?php
/**
*** Septembre 2021@SoluFile 
**/
	require_once 'model_crud.php';
	
	class lnk_att_art extends crud {
		
		public function __construct()
		{
			$this->table 	= "lnk_att_art";
			$this->key		= "idattendance";
			$this->keyLnk 	= "idarticle";
		}
		
		public function __destruct()
		{
		}
	}
?>