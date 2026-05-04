<?php
/**
*** Juillet 2019@SoluFile 
**/
	require_once 'model_crud.php';
	
	class wor_team_model extends crud {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->table 		= "wor_team";
			$this->key			= "idworteam";
			$this->duplicateKey = false;
			
			$this->fields 		= "wor_team.*, wor_name, GROUP_CONCAT(lnk_team_worker.idworker) AS idworker";
			
			$this->joins 		= "
				LEFT JOIN wor_worker ON idteamleader = idworker
				LEFT JOIN lnk_team_worker USING(idworteam)
			";
			$this->conds 		= array();
			$this->groups 		= array("idworteam");
			$this->orders 		= array("tea_name");
			$this->fieldName	= "tea_name";
			
			/*** Table filtering ***/
			$this->filters		= array();
			//ex : $this->filters['arrayName'] = array("id"=>"idColumn",in"=>array(0),"label"=>"label");
			
			/*** Select filtering ***/
			//$this->idparent		= "";
		}

		public function m_newRecord($insert=false)
		{
			$this->values = (object) array(
				"idworteam"=>0,
				"tea_name"=>null,
				"idteamleader"=>0,
				"tea_remark"=>"",
				
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
			$this->conds = array("idworteam = ".$idrecord);
			//$this->orders = array();
			//$this->debugging = true;
			
			return $this->select();
		}
		
		public function m_insert($dataSent)
		{
			parse_str($dataSent, $data);
			//$this->debugging = true;
			
			$this->insert($data);
			
			// *** lnk ***
			if(!empty($this->lnk)){
				$this->lnkProcess($this->result['idparent'], $data);
			}
			
			return true;
		}
		
		public function m_update($idrecord, $dataSent)
		{
			parse_str($dataSent, $data);
			
			// *** lnk ***
			if(!empty($this->lnk)){
				$this->lnkProcess($idrecord, $data);
			}
			
			// *** update table ***
			$this->conds = array("idworteam = ".$idrecord);
			//$this->joins = "";
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
				case 1 : $this->fields = "idworteam AS id, tea_name AS val, '' AS tokens"; $this->select(); break;
			}
			return true;
		}
		
		public function __destruct()
		{
		}
	}