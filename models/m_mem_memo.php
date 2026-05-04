<?php
/**
*** Juillet 2019@SoluFile 
**/
	require_once 'model_crud.php';
	
	class mem_memo_model extends crud {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->table 		= "mem_memo";
			$this->key			= "idmemo";
			$this->duplicateKey = false;
			
			$this->fields 		= "mem_memo.*, COALESCE(job_surname, CONCAT(COALESCE(cli_short_name, cli_name),'-',sit_name,'-',job_name)) AS job_name, usr_firstname";
			
			$this->joins 		= "
				LEFT JOIN usr_user USING (iduser) 
				LEFT JOIN job_job USING (idjob) 
				LEFT JOIN cli_client USING (idclient) 
				LEFT JOIN sit_site USING (idsite) 
			";
			$this->conds 		= array();
			$this->groups 		= array();
			$this->orders 		= array("mem_date");
			$this->fieldName	= "";
			
			/*** Table filtering ***/
			$this->filters		= array();
			//ex : $this->filters['arrayName'] = array("id"=>"idColumn",in"=>array(0),"label"=>"label");
			
			/*** Select filtering ***/
			//$this->idparent		= "";
		}

		public function m_newRecord($insert=false)
		{
			$this->values = (object) array(
				"idmemo"=>0,
				"idjob"=>null,
				"iduser"=>$_SESSION['iduser'],
				"mem_date"=>date("Y-m-d"), //date("d-m-Y"),
				
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
			$this->conds = array("idmemo = ".$idrecord);
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
			$this->conds = array("idmemo = ".$idrecord);
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
				case 1 : $this->fields = "idmemo AS id, mem_text AS val, '' AS tokens"; $this->select(); break;
			}
			return true;
		}
		
		public function __destruct()
		{
		}
	}