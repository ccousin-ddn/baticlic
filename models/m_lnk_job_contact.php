<?php
/**
*** Septembre 2019@SoluFile 
**/
	require_once 'model_crud.php';
	
	class lnk_job_contact extends crud {
		
		public function __construct()
		{
			$this->table 	= "lnk_job_contact";
			$this->key		= "idjob";
			$this->keyLnk 	= "idclicontact";
		}
		
		public function __destruct()
		{
		}
	}
?>