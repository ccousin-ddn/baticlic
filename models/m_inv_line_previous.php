<?php
/**
*** 09-2024@SOLUfile SRL 
**/
	require_once 'model_crud.php';
	
	class inv_line_previous_model extends crud {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->table 		= "inv_line_previous";
			$this->key			= "idline";
			$this->duplicateKey = false;
			
			$this->fields 		= "inv_line_previous.*, inv_ref, inv_title";
			
			$this->joins 		= "
				LEFT JOIN inv_invoice USING (idinvoice)
			";
			$this->conds 		= array();
			$this->groups 		= array();
			$this->orders 		= array();
			
			$this->unset		= array("idparent");
			$this->unclean		= array();
			$this->linkTable	= "";
			
			/*** new from ***/
			$this->fieldName	= "lin_name";
			
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
				"idinvoice"=>0,
				"idprevinvoice"=>0,
				"inv_description"=>null,
				"inv_total"=>0,
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
			$this->conds = array("inv_line_previous.idline = ".$idrecord);
			//$this->orders = array();
			
			return $this->select();
		}
		
		public function m_insert($data)
		{
			$this->insert($data);
			
			// *** lnk ***
			if(!empty($this->lnk)){
				$this->lnkProcess($this->result['idparent'], $data);
			}
			
			return true;
		}
		
		public function m_update($idrecord, $data)
		{
			// *** lnk ***
			if(!empty($this->lnk)){
				$this->lnkProcess($idrecord, $data);
			}
			
			// *** update table ***
			$this->conds = array("inv_line_previous.idline = ".$idrecord);
			//$this->joins = "";
			
			return $this->update($data);
		}
		
		public function m_delete($idrecord)
		{
			return $this->delete($idrecord);
		}
		
		public function m_getList($id, $conds=array())
		{
			$this->conds = $conds;
			switch($id){
				case 1 : $this->fields = "inv_line_previous.idline AS id, lin_name AS val, '' AS tokens"; $this->select(); break;
			}
			return true;
		}
		
		public function m_getTotalByInvoice($idinvoice)
		{
			$this->fields = "COALESCE(SUM(inv_line.lin_situation),0) AS tot_pre,";
			// vat
			$vat_vat = new vat_vat();
			$vat_vat->select();
			$vat_vat = array_column($vat_vat->values, 'vat_percent', 'idvat');
			foreach($vat_vat as $key=>$value){
				$this->fields .= "SUM(IF(idvat = ".$key.", ROUND(inv_line.lin_situation * vat_percent / 100, 2), 0)) AS vat".$key.",";
			}
			$this->fields = substr($this->fields, 0, strlen($this->fields)-1);
			$this->joins = "
			LEFT JOIN inv_line USING (idinvoice)
			LEFT JOIN vat_vat USING (idvat)";
			$this->conds = array("idprevinvoice = $idinvoice");
			//$this->orders = array("idinvoice");
			//$this->debugging = true;
			$this->select();
			$this->total = $this->values[0];
			$this->total['tot_vat'] = 0;
			foreach($vat_vat as $key=>$value){
				$this->total['tot_vat'] -= $this->total["vat".$key];
			}
			return $this->values[0]['tot_pre']??0;
		}
		
		public function m_addOlderSituations($idinvoice, $situation, $idquotation)
		{
			$query = "
			INSERT INTO inv_line_previous (idinvoice, idprevinvoice, inv_situation, inv_description, inv_total)
			SELECT idinvoice, ".$idinvoice.", inv_situation, inv_title, inv_tot_situation 
			FROM inv_invoice 
			WHERE idquotation = $idquotation AND inv_situation < $situation
			ORDER BY inv_situation
			";
			//dump($query);
			$this->executeQuery($query);
			$this->result['idparent'] = $idinvoice;
			return true;
		}
		
		public function __destruct()
		{
		}
	}