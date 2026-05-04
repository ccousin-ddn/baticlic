<?php
/**
*** Juillet 2019@SoluFile 
**/
	require_once 'model_crud.php';
	
	class quo_line_model extends crud {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->table 		= "quo_line";
			$this->key			= "idline";
			$this->duplicateKey = false;
			
			$this->fields 		= "quo_line.*, ROUND(lin_total * vat_percent / 100, 2) AS vat_amount, COALESCE(lin_description, CONCAT_WS(' ',cat_code,subcat_code,wor_code,wor_name)) AS art_name, COALESCE(lin_description,wor_name) AS art_short_name, wor_name, wor_description, wor_unit, wor_guarantee_10, wor_rate_small, wor_rate_medium, wor_rate_large, met_name, vat_percent";
			
			$this->joins 		= "
				LEFT JOIN lib_work USING(idwork) 
				LEFT JOIN wor_category USING(idworcategory) 
				LEFT JOIN wor_subcategory USING(idworsubcategory)
				LEFT JOIN met_metier ON quo_line.idmetier = met_metier.idmetier
				LEFT JOIN vat_vat USING (idvat)
			";
			$this->conds 		= array();
			$this->groups 		= array();
			$this->orders 		= array("lin_order");
			$this->fieldName	= "lin_name";
			
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
				"idquotation"=>0,
				"idwork"=>0,
				"lin_order"=>0,
				"lin_description"=>null,
				"lin_unit"=>"",
				"lin_quantity"=>1,
				"lin_qty_formula"=>1,
				"lin_pu"=>0,
				"lin_pu_formula"=>0,
				"lin_total"=>0,
				"lin_step_percent"=>0,
				"lin_step_total"=>0,
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
			
			// get quo_variable
			$quo = new quo_quotation();
			$quo->fields = "quo_variables";
			$quo->m_getById(decrypt($data['idparent']));
			$quo_res = $quo->values[0]["quo_variables"];
			// eval variables
			if(!empty($quo_res)){
				$quo_var = array_filter(explode(",", trim(preg_replace("/\r|\n|\t/", "",$quo_res))));
				foreach($quo_var as $var){
					eval($var.';');
				}
			}
			// calulate formula
			if(!empty($data['lin_qty_formula'])){
				eval('$qty = '.$data['lin_qty_formula'].';');
			}else{
				$qty = 0;
			}
			// calcul pu provider
			if(!empty($data['lin_pu_formula'])){
				$pu = str_replace(",",".",$data['lin_pu_formula']);
			}else{
				$pu = 0;
			}
			
			$data['lin_quantity'] = number_format(floatval($qty),2,'.','');
			$data['lin_pu'] = number_format(floatval($pu),3,'.','');
			$data['lin_total'] = number_format($data['lin_quantity']*$data['lin_pu'],3,'.','');
			//$data['lin_step_total'] = floatval($data['lin_step_total']) + floatval($data['lin_step_situation']);
			//$data['lin_step_situation'] = 0;
			//$this->debugging = true;
			unset($data['idparent']);
			
			return $this->update($data);
		}
		
		public function m_updateOrder()
		{
			//var_dump($this->dataSent); exit;
			$sign = "-";
			if($this->dataSent['oldIndex'] > $this->dataSent['newIndex']){$sign = "+";}
			$query = "
			UPDATE quo_line SET 
			lin_order = lin_order $sign 1 
			WHERE idquotation = ".decrypt($this->dataSent['idparent'])." 
			AND lin_order BETWEEN ".min($this->dataSent['newIndex'], $this->dataSent['oldIndex'])." AND ".max($this->dataSent['newIndex'], $this->dataSent['oldIndex']).";
			UPDATE quo_line SET lin_order = ".$this->dataSent['newIndex']." WHERE idline = ".decrypt($this->idrecord)."
			";
			//$this->debugging = true;
			
			return $this->executeQuery($query);
		}
		
		public function m_updateOrderBeforeInsert($idquotation, $index)
		{
			$query = "
			UPDATE quo_line SET 
			lin_order = lin_order + 1 
			WHERE idquotation = ".$idquotation." 
			AND lin_order >= ".$index."
			";

			//$this->debugging = true;
			
			return $this->executeQuery($query);
		}
		
		public function m_updateVAT($idquotation, $idvat)
		{
			$query = "
			UPDATE quo_line SET 
			idvat = ".$idvat." 
			WHERE idquotation = ".$idquotation." 
			AND idvat > 0 
			";

			//$this->debugging = true;
			
			return $this->executeQuery($query);
		}
		
		public function m_updateWithActualIndex($idquotation, $index)
		{
			$query = "
			UPDATE quo_line SET 
			lin_pu_prime = IF(lin_pu_prime IS NULL, lin_pu, lin_pu_prime), 
			lin_pu = lin_pu_prime * ".$index.", 
			lin_pu_formula = lin_pu, 
			lin_total = lin_pu * lin_quantity
			WHERE idquotation = ".$idquotation." 
			AND lin_pu > 0 
			";

			//$this->debugging = true;
			
			return $this->executeQuery($query);
		}
		
		public function m_updateStepTotal($idquotation)
		{
			$query = "
			UPDATE quo_line SET 
			lin_step_total = lin_step_total+lin_step_situation, lin_step_situation = 0
			WHERE idquotation = ".$idquotation." 
			";

			//$this->debugging = true;
			
			return $this->executeQuery($query);
		}
		
		public function m_delete($idrecord)
		{
			//$this->debugging = true;
			
			$query = "
			SELECT @idquotation := idquotation, @lin_order := lin_order
			FROM quo_line WHERE idline = $idrecord;
			DELETE FROM quo_line WHERE idline = $idrecord;
			UPDATE quo_line
			SET lin_order = lin_order - 1 WHERE idquotation = @idquotation AND lin_order > @lin_order;
			";
			
			return $this->executeMultiQuery($query);
			
		}
		
		public function m_getList($id, $conds=array())
		{
			$this->conds = $conds;
			switch($id){
				case 1 : $this->fields = "idline AS id, lin_name AS val, '' AS tokens"; $this->select(); break;
			}
			return true;
		}
		
		public function m_getTotal($idquotation)
		{
			$this->fields = "COALESCE(SUM(lin_total),0) AS total, COALESCE(SUM(lin_step_total),0) AS total_step,";
			// vat
			$vat_vat = new vat_vat();
			$vat_vat->select();
			$vat_vat = array_column($vat_vat->values, 'vat_percent', 'idvat');
			foreach($vat_vat as $key=>$value){
				$this->fields .= "SUM(IF(idvat = ".$key.", ROUND(lin_total * vat_percent / 100, 2), 0)) AS vat".$key.",";
			}
			$this->fields = substr($this->fields, 0, strlen($this->fields)-1);
			$this->joins = "LEFT JOIN vat_vat USING (idvat)";
			$this->conds = array("idquotation = $idquotation");
			//$this->orders = array("idquotation");
			//$this->debugging = true;
			$this->select();
			$this->total = $this->values[0];
			$this->total['tot_vat'] = 0;
			foreach($vat_vat as $key=>$value){
				$this->total['tot_vat'] += $this->total["vat".$key];
			}
		}
		
		public function __destruct()
		{
		}
	}