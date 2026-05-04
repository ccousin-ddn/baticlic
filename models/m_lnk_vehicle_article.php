<?php
/**
*** Octobre 2021@SoluFile 
**/
	require_once 'model_crud.php';
	
	class lnk_vehicle_article_model extends crud {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->table 	= "lnk_vehicle_article";
			$this->key		= "idvehicle";
			$this->keyLnk 	= "idarticle";
			
			$this->duplicateKey = false;
			
			$this->fields 		= "idvehicle, idarticle, SUM(art_qty) art_qty, CONCAT_WS(' ',veh_model,veh_numberplate) AS veh_name, art_code, art_description, COUNT(idarticle) AS tot_qty, MAX(mvt_date_in) AS mvt_date_in";
			
			$this->joins 		= "
				LEFT JOIN art_article USING (idarticle) 
				LEFT JOIN veh_vehicle USING (idvehicle) 
				";
			$this->conds 		= array();
			$this->groups 		= array("idvehicle", "idarticle");
			$this->orders = array("art_code");
			$this->fieldName	= "";
		}
		
		public function m_getList($id, $conds=array())
		{
			$this->conds = $conds;
			switch($id){
				case "mvt" : 
					$this->fields = "idarticle AS id, art_code AS val, art_description AS tokens"; 
					$this->conds[] = "art_qty > 0";
					
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