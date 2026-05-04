<?php
/**
*** Juillet 2019@SoluFile 
**/
	require_once 'model_crud.php';
	
	class inv_line_model extends crud {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->table 		= "inv_line";
			$this->key			= "idline";
			$this->duplicateKey = false;
			
			$this->fields 		= "inv_line.*, ROUND(lin_situation * vat_percent / 100, 2) AS vat_amount, vat_percent";
			
			$this->joins 		= "
				LEFT JOIN vat_vat USING (idvat)
			";
			$this->conds 		= array();
			$this->groups 		= array();
			$this->orders 		= array("idline");
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
				"idline"=>0,
				"idinvoice"=>0,
				"idwork"=>0,
				"lin_description"=>null,
				"lin_quantity"=>1,
				"lin_pu"=>0,
				"lin_percent"=>null,
				"lin_total"=>0,
				
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
		
		public function m_update($idrecord, $data)
		{
			//$this->joins = "";
			$this->conds = array("idline = ".$idrecord);
			
			$data['lin_situation'] = number_format($data['lin_quantity']*$data['lin_pu']*($data['lin_percent']>0?$data['lin_percent']/100:0),3,'.','');
			//$this->debugging = true;
			unset($data['idparent']);
			
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
		
		public function m_getTotalByInvoice($idinvoice)
		{
			$this->fields = "COALESCE(SUM(lin_total),0) AS total, COALESCE(SUM(lin_situation),0) AS tot_situation,";
			// vat
			$vat_vat = new vat_vat();
			$vat_vat->select();
			$vat_vat = array_column($vat_vat->values, 'vat_percent', 'idvat');
			foreach($vat_vat as $key=>$value){
				$this->fields .= "SUM(IF(idvat = ".$key.", ROUND(lin_situation * vat_percent / 100, 2), 0)) AS vat".$key.",";
			}
			$this->fields = substr($this->fields, 0, strlen($this->fields)-1);
			$this->joins = "LEFT JOIN vat_vat USING (idvat)";
			$this->conds = array("idinvoice = $idinvoice");
			//$this->orders = array("idinvoice");
			//$this->debugging = true;
			$this->select();
			$this->total = $this->values[0];
			$this->total['tot_vat'] = 0;
			foreach($vat_vat as $key=>$value){
				$this->total['tot_vat'] += $this->total["vat".$key];
			}
		}
		
		public function m_updateVAT($idinvoice, $idvat)
		{
			$query = "
			UPDATE inv_line SET 
			idvat = ".$idvat." 
			WHERE idinvoice = ".$idinvoice." 
			AND idvat > 0 
			";

			//$this->debugging = true;
			
			return $this->executeQuery($query);
		}
		
		public function __destruct()
		{
		}
	}