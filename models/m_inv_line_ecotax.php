<?php
/**
*** 09-2024@SOLUfile SRL 
**/
	require_once 'model_crud.php';
	
	class inv_line_ecotax_model extends crud {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->table 		= "inv_line_ecotax";
			$this->key			= "idline";
			$this->duplicateKey = false;
			
			$this->fields 		= "inv_line_ecotax.*, ROUND(lin_total * vat_percent / 100, 2) AS vat_amount, vat_percent";
			
			$this->joins 		= "
				LEFT JOIN vat_vat USING (idvat)
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
				"idvat"=>0,
				"lin_description"=>null,
				"lin_quantity"=>0,
				"lin_pu"=>0,
				"lin_total"=>0,
				
				
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
			$this->conds = array("inv_line_ecotax.idline = ".$idrecord);
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
			$this->conds = array("inv_line_ecotax.idline = ".$idrecord);
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
				case 1 : $this->fields = "inv_line_ecotax.idline AS id, lin_name AS val, '' AS tokens"; $this->select(); break;
			}
			return true;
		}
		
		public function m_getTotalByInvoiceOLD($idinvoice)
		{
			$query = "
			SELECT SUM(lin_total) as tot_eco
			FROM inv_line_ecotax
			WHERE idinvoice = $idinvoice
			GROUP BY idinvoice
			";
			//$this->debugging = true;
			if($this->executeQuery($query)){
				return $this->values[0]['tot_eco'];
			}else{
				return false;
			}
		}
		
		public function m_getTotalByInvoice($idinvoice)
		{
			$this->fields = "COALESCE(SUM(lin_total),0) AS tot_eco,";
			// vat
			$vat_vat = new vat_vat();
			$vat_vat->select();
			$vat_vat = array_column($vat_vat->values, 'vat_percent', 'idvat');
			foreach($vat_vat as $key=>$value){
				$this->fields .= "SUM(IF(idvat = ".$key.", ROUND(lin_total * vat_percent / 100, 2), 0)) AS vat".$key.",";
			}
			$this->fields = substr($this->fields, 0, strlen($this->fields)-1);
			$this->joins = "LEFT JOIN vat_vat USING (idvat)";
			$this->conds = array("idinvoice = $idinvoice");
			//$this->orders = array("idinvoice");
			$this->select();
			$this->total = $this->values[0];
			$this->total['tot_vat'] = 0;
			foreach($vat_vat as $key=>$value){
				$this->total['tot_vat'] += $this->total["vat".$key];
			}
			return $this->values[0]['tot_eco']??0;
		}
		
		public function __destruct()
		{
		}
	}