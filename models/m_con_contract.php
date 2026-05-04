<?php
/**
*** 12-2023@SOLUfile SRL 
**/
	require_once 'model_crud.php';
	
	class con_contract_model extends crud {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->table 		= "con_contract";
			$this->key			= "idcontract";
			$this->duplicateKey = false;
			
			$this->fields 		= "con_contract.*, COALESCE(cli_short_name, cli_name) AS cli_name, usr_firstname";
			
			$this->joins 		= "
				LEFT JOIN cli_client USING (idclient)
				LEFT JOIN usr_user USING (iduser) 
			";
			$this->conds 		= array();
			$this->groups 		= array();
			$this->orders 		= array();
			
			$this->unset		= array("idparent");
			$this->unclean		= array();
			$this->linkTable	= "";
			
			/*** new from ***/
			$this->fieldName	= "con_reference";
			
			/*** Table filtering ***/
			$this->filters		= array();
			$this->filters['con_status'] = array("id"=>"con_status","in"=>array(0,1,2),"label"=>"Statut");
			$this->filters['cli_client'] = array("id"=>"cli_client.idclient","in"=>array(),"label"=>"Client");
			
			/*** Select filtering ***/
			$this->idparent		= "idclient";
		}

		public function m_newRecord($insert=false)
		{
			$data = array(
				"idcontract"=>0,
				"idclient"=>0,
				"iduser"=>$_SESSION['iduser'],
				"idmetier"=>null,
				"con_status"=>2,
				"con_date"=>date("Y-m-d"),
				"con_duration"=>0,
				"con_date_end"=>null,
				"con_reference"=>null,
				"con_description"=>null,
				"con_bpu_name"=>null,
				"con_bpu_file"=>null,
				
				
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

			return $this->select();
		}
		
		public function m_getById($idrecord)
		{
			$this->conds = array("con_contract.idcontract = ".$idrecord);
			//$this->orders = array();
			
			return $this->select();
		}
		
		public function m_insert($data)
		{
			$data['idmetier'] = implode(",", $data['idmetier']??[]);
			$this->insert($data);
			
			// *** lnk ***
			if(!empty($this->lnk)){
				$this->lnkProcess($this->result['idparent'], $data);
			}
			
			return true;
		}
		
		public function m_update($idrecord, $data)
		{
			$data['idmetier'] = implode(",", $data['idmetier']??[]);
			// *** lnk ***
			if(!empty($this->lnk)){
				$this->lnkProcess($idrecord, $data);
			}
			
			// *** update table ***
			$this->conds = array("con_contract.idcontract = ".$idrecord);
			//$this->joins = "";
			
			return $this->update($data);
		}
		
		public function m_delete($idrecord)
		{
			return $this->delete($idrecord);
		}
		
		public function m_deleteBPU($fileName)
		{
			$query = "
			DELETE 
			FROM lib_work
			WHERE idcontract = (SELECT idcontract FROM con_contract WHERE con_bpu_file = '$fileName')
			";
			//$this->debugging = true;
			if($this->executeQuery($query)){
				return true;
			}else{
				return false;
			}
		}
		
		public function m_getList($id, $conds=array())
		{
			$this->conds = $conds;
			switch($id){
				case 1 : $this->fields = "con_contract.idcontract AS id, con_reference AS val, '' AS tokens"; 
						$this->conds[] = "con_status = 2";
						$this->select(); 
						break;
				case 2 : $this->fields = "con_contract.idcontract AS id, con_reference AS val, '' AS tokens"; 
						$this->conds = array("con_status IN (2,5)");
						$this->select(); 
						break;
				case 3 : $this->fields = "con_contract.idcontract AS id, con_reference AS val, '' AS tokens"; $this->select(); break;
			}
			return true;
		}
		
		public function m_function()
		{
			$query = "
			SELECT 
			FROM con_contract
			WHERE 
			GROUP BY 
			";
			//$this->debugging = true;
			if($this->executeQuery($query)){
				return true;
			}else{
				return false;
			}
		}
		
		public function __destruct()
		{
		}
	}