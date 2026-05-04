<?php
/**
*** Juillet 2019@SoluFile 
**/
	require_once 'model_crud.php';
	
	class lnk_task_worker extends crud {
		
		public function __construct()
		{
			$this->table 	= "lnk_task_worker";
			$this->key		= "idtask";
			$this->keyLnk 	= "idworker";
		}
		
		public function __destruct()
		{
		}
	}
?>