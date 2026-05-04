<?php
/**
*** Novembre 2021@SoluFile 
**/
	require_once (dirname(__FILE__)."/../views/v_lnk_job_article.php");
	
	class lnk_job_article extends lnk_job_article_view {
		
		public function __construct()
		{
			parent::__construct();
			$this->level		= 1;
			$this->cryptfields	= array("");
			$this->lnk			= array(); // GROUP_CONCAT(idlnk) idlnk
		}
		
		public function c_tableByParent(){
			if($this->checkUserRight()){
				// *** model ***
				//$this->debugging = true;
				if($this->select()){
					// *** view ***
					$this->v_createLineTable($this->idrecord);
					$this->json['code'] = 1;
					return true;
				}else{
					throw new Exception('PHP : Error in controller tableByParent', 201);
				}
			}
		}

		public function __destruct()
		{
		}
	}