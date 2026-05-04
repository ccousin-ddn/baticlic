<?php
/**
*** Juillet 2019@SoluFile 
**/
	require_once 'model_crud.php';
	
	class sup_receipt_model extends crud {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->table 		= "sup_receipt";
			$this->key			= "idsupreceipt";
			$this->duplicateKey = false;
			
			$this->fields 		= "sup_receipt.*, ord_ref, ord_title, ord_deadline, ord_del_address, COALESCE(sup_short_name, sup_name) AS sup_name, sho_name";
			
			$this->joins 		= "
				LEFT JOIN sup_order USING(idsuporder) 
				LEFT JOIN sup_supplier USING(idsupplier) 
				LEFT JOIN sho_shop USING(idshop)
			";
			$this->conds 		= array();
			$this->groups 		= array();
			$this->orders 		= array("idsupreceipt DESC");
			$this->unset		= array("sup_name", "ord_ref", "ord_title", "ord_del_address", "lines");
			
			$this->fieldName	= "";
			
			/*** Table filtering ***/
			$this->filters['rec_status'] = array("id"=>"rec_status","in"=>array(0,2),"label"=>"Réception");
			
			/*** Select filtering ***/
			//$this->idparent		= "";
		}

		public function m_newRecord($insert=false)
		{
			$this->values = (object) array(
				"idsupreceipt"=>0,
				"idsuporder"=>0,
				"idshop"=>0,
				"rec_date"=>date("Y-m-d"),
				"rec_status"=>0,
				"rec_in_stock"=>0,
				"rec_remark"=>"",
				
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
			$this->conds = array("idsupreceipt = ".$idrecord);
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
			parse_str($dataSent, $data);
			$lines = json_decode($data['lines']);
			// *** update qty if rec_status = 0
			if($data['rec_status'] == 0){
				// insert sup_receipt_line
				$update = false;
				$query = "INSERT INTO sup_receipt_line (idsupreceipt, idline, lin_qty_received) VALUES ";
				foreach($lines as $key=>$value){
					if(floatval($value) > 0){
						$query .= "(".$idrecord.",".decrypt($key).",".$value."),";
						$update = true;
					}
				}
				$query = substr($query, 0, strlen($query)-1);
				$query .= " ON DUPLICATE KEY UPDATE lin_qty_received = values(lin_qty_received)";
				if($update==true){$this->executeQuery($query);}
				
				// update sup_order_line
				/*
				$update = false;
				$query = "INSERT INTO sup_order_line (idline, lin_qty_total_received) VALUES ";
				foreach($lines as $key=>$value){
					if(floatval($value) > 0){
						$query .= "(".decrypt($key).",".$value."),";
						$update = true;
					}
				}
				$query = substr($query, 0, strlen($query)-1);
				$query .= "ON DUPLICATE KEY UPDATE lin_qty_total_received = coalesce(lin_qty_total_received,0) + values(lin_qty_total_received)";
				if($update) $this->executeQuery($query);
				*/
				
				if(!empty($data['idshop'])){
					$line = new sup_order_line();
					$insert = false;
					// insert in shop
					$query_shop = "INSERT INTO sto_stock (idshop, idarticle, sto_quantity) VALUES ";
					// insert in movement
					$query_mvt = "INSERT INTO mov_movement (iduser, idshop, idarticle, mov_quantity, mov_date, mov_type, mov_place, mov_idplace) VALUES ";
					
					foreach($lines as $key=>$value){
						if($value > 0){
							$idarticle = $line->m_getIdArticle(decrypt($key));
							if($idarticle > 0){
								if(!$insert){$insert = true;}
								$query_shop .= "(".$data['idshop'].",".$idarticle.",".$value."),";
								$query_mvt .= "(".$_SESSION['iduser'].",".$data['idshop'].",".$idarticle.",".$value.",CURRENT_DATE(),1,2,".$data['idsuporder']."),";
							}
						}
					}
					$query_shop = substr($query_shop, 0, strlen($query_shop)-1);
					$query_shop .= " ON DUPLICATE KEY UPDATE sto_quantity = coalesce(sto_quantity,0) + values(sto_quantity)";
					$query_mvt = substr($query_mvt, 0, strlen($query_mvt)-1);
					if($insert){
						$this->executeQuery($query_shop);
						$this->executeQuery($query_mvt);	
					}
				}
				
				// update rec_status
				$query = "
				SELECT SUM(tmp) AS rest FROM(
					SELECT (lin_QdC - COALESCE(SUM(lin_qty_received),0)) AS tmp 
					FROM sup_order_line 
					LEFT JOIN sup_receipt_line ON sup_receipt_line.idline = sup_order_line.idline AND idsupreceipt <= ".$idrecord."
					WHERE idsuporder = (SELECT idsuporder FROM sup_receipt WHERE idsupreceipt = ".$idrecord.") 
					GROUP BY sup_order_line.idline
				) AS tbl
				";

				$this->executeQuery($query);
				$rest = $this->values[0]['rest'];
				if($rest > 0){
					$data['rec_status'] = 2;
					$ord_status = 4;
				}else{
					$data['rec_status'] = 1;
					$ord_status = 3;
				}
				// update sup_order.ord_status
				$query = "UPDATE sup_order SET ord_status = ".$ord_status." WHERE idsuporder = (SELECT idsuporder FROM sup_receipt WHERE idsupreceipt = ".$idrecord.")";
				$this->executeQuery($query);
				
			}
			
			$this->conds = array("idsupreceipt = ".$idrecord);
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
				case 1 : $this->fields = "idsupreceipt AS id, rec_name AS val, '' AS tokens"; $this->select(); break;
			}
			return true;
		}
		
		public function __destruct()
		{
		}
	}