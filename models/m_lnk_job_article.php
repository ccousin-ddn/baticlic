<?php
/**
*** Novembre 2021@SoluFile 
**/
	require_once 'model_crud.php';
	
	class lnk_job_article_model extends crud {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->table 	= "lnk_job_article";
			$this->key		= "idjob";
			$this->keyLnk 	= "idarticle";
			
			$this->duplicateKey = false;
			
			$this->fields 		= "lnk_job_article.*, COALESCE(job_surname, job_name) AS job_name, art_code, art_description, COUNT(idarticle) AS tot_qty";
			
			$this->joins 		= "
				LEFT JOIN art_article USING (idarticle) 
				LEFT JOIN job_job USING (idjob) 
				";
			$this->conds 		= array();
			$this->groups 		= array("idjob", "idarticle");
			$this->orders = array("art_code");
			$this->fieldName	= "";
		}
		
		public function m_getList($id, $conds=array())
		{
			$this->conds = $conds;
			switch($id){
				case "mvt" : 
					$this->fields = "idarticle AS id, art_code AS val, art_description AS tokens"; 
					
					$this->select(); 
					break;
			}
			return true;
		}
		
		public function __destruct()
		{
		}
	}
?>