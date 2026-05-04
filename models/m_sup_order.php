<?php
/**
*** Juillet 2019@SoluFile 
**/
	require_once 'model_crud.php';
	
	class sup_order_model extends crud {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->table 		= "sup_order";
			$this->key			= "idsuporder";
			$this->duplicateKey = false;
			
			$this->fields 		= "sup_order.*, COALESCE(sup_short_name, sup_name) AS sup_name, job_reference, sit_name";
			
			$this->joins 		= "
				LEFT JOIN sup_supplier USING(idsupplier) 
				LEFT JOIN job_job USING(idjob) 
				LEFT JOIN sit_site USING(idsite) 
				LEFT JOIN cit_city AS city_s ON city_s.idcity = sup_idcity
				LEFT JOIN sup_agency USING(idsupagency) 
				LEFT JOIN cit_city AS city_a ON city_a.idcity = age_idcity
			";
			$this->conds 		= array();
			$this->groups 		= array();
			$this->orders 		= array("ord_ref DESC");
			$this->fieldName	= "ord_ref";
			
			/*** Table filtering ***/
			$this->filters		= array();
			$this->filters['ord_status'] = array("id"=>"ord_status","in"=>array(1,2),"label"=>"Statut");
			$this->filters['ord_breakdown'] = array("id"=>"ord_breakdown","in"=>array(0),"label"=>"Ventilée");
			
			/*** Select filtering ***/
			//$this->idparent		= "";
		}

		public function m_newRecord($insert=false)
		{
			$cfg = new cfg_config();
			$data = array(
				"idsuporder"=>0,
				"idsupplier"=>0,
				"idjob"=>0,
				"ord_ref"=>$cfg->getNumber("ord"),
				"ord_date"=>date("Y-m-d"),
				"ord_deadline"=>null,
				"ord_title"=>"",
				"ord_amount"=>0,
				"ord_tva_percent"=>$cfg->getInfo("pc_tva"),
				"ord_tot_amount"=>0,
				"ord_del_address"=>$cfg->getInfo("del_address"),
				"ord_status"=>1,
				"ord_breakdown"=>0,
				"ord_printAmount"=>1,
				"ord_remark"=>"",
				
				);
			$this->values = (object) $data;
			if($insert){
				return $this->insert($data);
			}else{
				return true;
			}
		}
		
		public function m_getAll($withFilter=true)
		{
			if($withFilter){
				$filters = $this->dataSent;
				if(!empty($filters)){
					foreach($filters as $key=>$val){
						$this->conds[] = $key." IN(".implode(",",$val).")";
					}
				}else{
					if(!empty($this->filters)){
						foreach($this->filters as $column=>$val){
							if(!empty($val['in'])){
								$this->conds[] = $val['id']." IN(".implode(",",$val['in']).")";
							}
						}
					}
				}
			}else{
				foreach($this->filters as $filter){
					$filter['in'] = array();
				}
			}
			//$this->conds = array();
			//$this->orders = array();
			//$this->debugging = true;

			return $this->select();
		}
		
		public function m_getById($idrecord)
		{
			$this->conds = array("idsuporder = ".$idrecord);
			//$this->orders = array();
			//$this->debugging = true;
			
			return $this->select();
		}
		
		public function m_insert($dataSent)
		{
			parse_str($dataSent, $data);
			//$this->debugging = true;
			
			return $this->insert($data);
		}
		
		public function m_update($idrecord, $dataSent)
		{
			//$this->joins = "";
			$this->conds = array("idsuporder = ".$idrecord);
			parse_str($dataSent, $data);
			//$this->debugging = true;
			
			return $this->update($data);
		}
		
		public function m_delete($idrecord)
		{
			//$this->debugging = true;
			
			return $this->delete($idrecord);
		}
		
		public function m_getList($id, $conds=array())
		{
			$this->conds = $conds;
			switch($id){
				case 1 : 
					$this->fields = "idsuporder AS id, ord_ref AS val, '' AS tokens"; 
					$this->conds = array("idsupplier = ".IDSUPPLIER);
					$this->select(); break;
				case 2 : 
					$this->fields = "idsuporder AS id, COALESCE(ord_ref, ord_title) AS val, '' AS tokens";
					$this->select(); break;
				case 3 : 
					$this->fields = "idsuporder AS id, CONCAT_WS(' - ',ord_ref, ord_sup_ref, ord_title) AS val, ord_amount AS subtext";
					$this->select(); break;
			}
			return true;
		}
		
		public function m_createReceipt($idrecord)
		{
			$query = "
			INSERT INTO sup_receipt (idsuporder, rec_date, rec_delivery_note, rec_status)
			SELECT idsuporder, COALESCE(ord_delivery_date,CURRENT_DATE()), ord_delivery_note, 0
			FROM sup_order
			WHERE idsuporder = $idrecord
			";
			return $this->executeQuery($query);
		}
		
		public function __destruct()
		{
		}
	}