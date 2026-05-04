<?php
/**
*** Juillet 2019@SoluFile 
**/
	require_once 'model_crud.php';
	
	class lib_library_model extends crud {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->table 		= "lib_library";
			$this->key			= "idlibrary";
			$this->duplicateKey = false;
			
			$this->fields 		= "lib_library.*, cli_name, con_reference, con_description, COUNT(idwork) AS wor_tot";
			
			$this->joins 		= "
				LEFT JOIN cli_client USING(idclient)
				LEFT JOIN con_contract USING(idcontract)
				LEFT JOIN lib_work USING(idlibrary)
			";
			$this->conds 		= array();
			$this->groups 		= array("idlibrary");
			$this->orders 		= array("lib_name");
			$this->fieldName	= "lib_name";
			
			$this->unset		= array("wor_tot");
			
			/*** Table filtering ***/
			$this->filters		= array();
			//ex : $this->filters['arrayName'] = array("id"=>"idColumn",in"=>array(0),"label"=>"label");
			
			/*** Select filtering ***/
			//$this->idparent		= "";
		}

		public function m_newRecord($insert=false)
		{
			$data = array(
				"idlibrary"=>0,
				"idclient"=>0,
				"lib_name"=>null,
				"lib_bpu_name"=>null,
				"lib_bpu_file"=>null,
				"lib_remark"=>"",
				
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
			$this->conds = array("idlibrary = ".$idrecord);
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
			$this->conds = array("idlibrary = ".$idrecord);
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
				case 1 : $this->fields = "idlibrary AS id, lib_name AS val, '' AS tokens"; $this->select(); break;
			}
			return true;
		}
		
		public function __destruct()
		{
		}
	}