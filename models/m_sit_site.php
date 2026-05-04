<?php
/**
*** Juillet 2019@SoluFile 
**/
	require_once 'model_crud.php';
	
	class sit_site_model extends crud {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->table 		= "sit_site";
			$this->key			= "idsite";
			$this->duplicateKey = false;
			
			$this->fields 		= "sit_site.*, CONCAT_WS(' - ',cli_short_name,cli_name) AS cli_name, are_name, CONCAT(cit_pc,' ',cit_name) AS cit_name";
			
			$this->joins 		= "
				LEFT JOIN cli_client USING (idclient)
				LEFT JOIN are_area USING (idarea) 
				LEFT JOIN cit_city ON sit_site.sit_idcity = cit_city.idcity
			";
			$this->conds 		= array();
			$this->groups 		= array();
			$this->orders 		= array("sit_name");
			$this->fieldName	= "sit_name";
			
			$this->unset		= array("city");
			/*** Table filtering ***/
			$this->filters['cli_client'] = array("id"=>"cli_client.idclient","in"=>array(),"label"=>"Client");
			//ex : $this->filters['arrayName'] = array("id"=>"idColumn",in"=>array(0),"label"=>"label");
			
			/*** Select filtering ***/
			$this->idparent		= "idclient";
		}

		public function m_newRecord($insert=false)
		{
			$this->values = (object) array(
				"idsite"=>0,
				"idclient"=>null,
				"idclitype"=>null,
				"sit_name"=>null,
				"sit_address_1"=>null,
				"sit_address_2"=>null,
				"sit_pc"=>null,
				"sit_city"=>null,
				"sit_remark"=>"",
				
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
			$this->conds = array("idsite = ".$idrecord);
			//$this->orders = array();
			//$this->debugging = true;
			
			return $this->select();
		}
		
		public function m_insert($data)
		{
			//$this->debugging = true;
			
			return $this->insert($data);
		}
		
		public function m_update($idrecord, $data)
		{
			//$this->joins = "";
			$this->conds = array("idsite = ".$idrecord);
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
					$this->fields = "idsite AS id, sit_name AS val, '' AS tokens"; 
					$this->select();
					//array_unshift($this->values, array("id"=>0,"val"=>"","tokens"=>""));
					//var_dump($this->values);
					break;
				case 2 : $this->fields = "idsite AS id, sit_name AS val, '' AS tokens"; $this->conds=array(); $this->select(); break;
			}
			return true;
		}
		
		public function __destruct()
		{
		}
	}