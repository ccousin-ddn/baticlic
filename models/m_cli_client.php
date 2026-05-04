<?php
/**
*** Juillet 2019@SoluFile 
**/
	require_once 'model_crud.php';
	
	class cli_client_model extends crud {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->table 		= "cli_client";
			$this->key			= "idclient";
			$this->duplicateKey = false;
			
			$this->fields 		= "cli_client.*, typ_name, CONCAT(cit_pc,' ',cit_name) AS cit_name";
			
			$this->joins 		= "
				LEFT JOIN cli_type USING (idclitype)
				LEFT JOIN cit_city USING (idcity)
			";
			$this->conds 		= array();
			$this->groups 		= array();
			$this->orders 		= array("cli_name");
			$this->fieldName	= "cli_name";
			
			/*** Table filtering ***/
			$this->filters['cli_status'] = array("id"=>"cli_status","in"=>array(1),"label"=>"Statut");
			$this->filters['cli_type'] = array("id"=>"cli_type.idclitype","in"=>array(),"label"=>"Type");
			
			/*** Select filtering ***/
			////$this->idparent		= "";
		}

		public function m_newRecord($insert=false)
		{
			$this->values = (object) array(
				"idclient"=>0,
				"idclitype"=>1,
				"cli_status"=>2,
				"cli_name"=>null,
				"cli_short_name"=>null,
				"cli_contact"=>null,
				"cli_address_1"=>null,
				"cli_address_2"=>null,
				"cli_pc"=>null,
				"cli_city"=>null,
				"cli_siret"=>null,
				"cli_tva"=>null,
				"cli_email"=>null,
				"cli_phone_1"=>null,
				"cli_logo"=>null,
				"cli_color_1"=>null,
				"cli_color_2"=>null,
				"cli_rate_level"=>2,
				"cli_inv_contact"=>null,
				"cli_inv_address_1"=>null,
				"cli_inv_address_2"=>null,
				"cli_inv_pc"=>null,
				"cli_inv_city"=>null,
				"cli_remark"=>"",
				
				);
			if($insert){
				return $this->insert($this->values);
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
			$this->conds = array("idclient = ".$idrecord);
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
			$this->conds = array("idclient = ".$idrecord);
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
				case 1 : $this->fields = "idclient AS id, COALESCE(cli_short_name, cli_name) AS val, cli_name AS subtext"; $this->orders = array("val"); $this->select(); break;
			}
			return true;
		}
		
		public function __destruct()
		{
		}
	}