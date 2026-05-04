<?php
/**
*** Septembre 2019@SoluFile 
**/
	require_once 'model_crud.php';
	
	class quo_line_comment_model extends crud {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->table 		= "quo_line_comment";
			$this->key			= "idline";
			$this->duplicateKey = false;
			
			$this->fields 		= "quo_line_comment.*, com_name, com_remark";
			
			$this->joins 		= "
				LEFT JOIN quo_comment USING(idquocomment)
			";
			$this->conds 		= array();
			$this->groups 		= array();
			$this->orders 		= array("com_name");
			$this->unset		= array("idparent");
			
			$this->fieldName	= "com_name";
			
			/*** Table filtering ***/
			$this->filters		= array();
			//ex : $this->filters['arrayName'] = array("id"=>"idColumn","in"=>array(0),"label"=>"label");
			
			/*** Select filtering ***/
			//$this->idparent		= "";
		}

		public function m_newRecord($insert=false)
		{
			$data = array(
				"idline"=>0,
				"idquotation"=>0,
				"idquocomment"=>0,
				
			);
			$this->values = (object) $data;
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
			$this->conds = array("idline = ".$idrecord);
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
			$this->conds = array("idline = ".$idrecord);
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
				case 1 : $this->fields = "idline AS id, lin_name AS val, '' AS tokens"; $this->select(); break;
			}
			return true;
		}
		
		public function __destruct()
		{
		}
	}