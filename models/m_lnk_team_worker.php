<?php
/**
*** Juillet 2019@SoluFile 
**/
	require_once 'model_crud.php';
	
	class lnk_team_worker extends crud {
		
		public function __construct()
		{
			$this->table 	= "lnk_team_worker";
			$this->key		= "idworteam";
			$this->keyLnk 	= "idworker";
		}
		
		public function __destruct()
		{
		}
	}
?>