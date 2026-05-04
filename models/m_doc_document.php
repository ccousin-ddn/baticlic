<?php
/**
*** Juillet 2019@SoluFile 
**/
	require_once 'model_crud.php';
	
	class doc_document_model extends crud {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->table 		= "doc_document";
			$this->key			= "iddocument";
			$this->duplicateKey = false;
			
			$this->fields 		= "doc_document.*";
			
			$this->joins 		= "
				
			";
			$this->conds 		= array();
			$this->groups 		= array();
			$this->orders 		= array("iddocument");
			
			$this->unclean		= array();
			
			$this->filters		= array();
		}

		public function m_newRecord()
		{
			$this->values = (object) array(
				"iddocument"=>0,
				"doc_from"=>null,
				"idfrom"=>0,
				"doc_info"=>null,
				"doc_type"=>1,
				"doc_real_name"=>null,
				"doc_slug_name"=>null,
				"doc_preview"=>null,
				);
			return true;
		}
		
		public function m_getAll()
		{
			$this->conds = array("doc_from = '".$this->info."'", "idfrom = ".decrypt($this->idrecord));
			$this->orders = array("iddocument");
			//$this->debugging = true;

			return $this->select();
		}
		
		public function m_getById($idrecord)
		{
			$this->conds = array("iddocument = ".$idrecord);
			//$this->orders = array();
			//$this->debugging = true;
			
			return $this->select();
		}
		
		public function m_insert($dataSent)
		{
			//$this->debugging = true;
			
			return $this->insert($dataSent);
		}
		
		public function m_update($idrecord, $dataSent)
		{
			//$this->joins = "";
			$this->conds = array("iddocument = ".$idrecord);
			parse_str($dataSent, $data);
			//$this->debugging = true;
			
			return $this->update($data);
		}
		
		public function m_delete($idrecord)
		{
			//$this->debugging = true;
			
			return $this->delete($idrecord);
		}

		public function __destruct()
		{
		}
	}