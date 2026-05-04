<?php
/**
*** Juillet 2019@SoluFile 
**/
	require_once 'model_crud.php';
	
	class sup_order_line_model extends crud {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->table 		= "sup_order_line";
			$this->key			= "idline";
			$this->duplicateKey = false;
			
			$this->fields 		= "sup_order_line.*, supart_ref, art_code, art_description, COALESCE(lin_description, supart_ref) AS supart_name, COALESCE(lin_description, CONCAT_WS('-',supart_ref,supart_description)) AS art_name";
			
			$this->joins 		= "
				LEFT JOIN sup_article USING(idsuparticle)
				LEFT JOIN art_article USING(idarticle) 
			";
			$this->conds 		= array();
			$this->groups 		= array();
			$this->orders 		= array("sup_order_line.idline");
			$this->fieldName	= "ord_name";
			
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
				"idsuporder"=>0,
				"idsuparticle"=>0,
				"lin_description"=>null,
				"lin_QdM"=>1,
				"lin_QdC"=>1,
				"lin_pu_UdM"=>0,
				"lin_ecotax"=>0,
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
		
		public function m_update($idrecord, $dataSent)
		{
			//$this->joins = "";
			$this->conds = array("idline = ".$idrecord);
			parse_str($dataSent, $data);
			$data['lin_QdM'] = number_format(floatval($data['lin_QdM']),2,'.','');
			$data['lin_QdC'] = number_format(floatval($data['lin_QdC']),2,'.','');
			$data['lin_pu_UdM'] = number_format(floatval($data['lin_pu_UdM']),3,'.','');
			$data['lin_ecotax'] = number_format($data['lin_QdM']*$data['lin_QdC']*floatval($data['lin_ecotax']??0),2,'.','');
			$data['lin_total'] = number_format($data['lin_QdM']*$data['lin_QdC']*$data['lin_pu_UdM'],2,'.','');
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
				case 1 : $this->fields = "idline AS id, ord_name AS val, '' AS tokens"; $this->select(); break;
				case "mvt" : $this->fields = "idarticle AS id, art_code AS val, art_description AS tokens"; $this->select(); break;
			}
			return true;
		}
		
		public function m_getTotal($idsuporder)
		{
			$this->fields = "COALESCE(SUM(lin_total),0) AS tot_amount, COALESCE(SUM(lin_ecotax),0) AS tot_ecotax";
			$this->conds = array("idsuporder = $idsuporder");
			$this->orders = array();
			$this->select();
			return $this->values[0];
		}
		
		public function m_getIdArticle($idline)
		{
			$this->fields = "idarticle";
			$this->conds = array("idline = $idline");
			$this->select();
			return $this->values[0]['idarticle'];
		}
		
		public function __destruct()
		{
		}
	}