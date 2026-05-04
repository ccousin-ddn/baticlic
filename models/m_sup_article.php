<?php
/**
*** Juillet 2019@SoluFile 
**/
	require_once 'model_crud.php';
	
	class sup_article_model extends crud {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->table 		= "sup_article";
			$this->key			= "idsuparticle";
			$this->duplicateKey = false;
			
			$this->fields 		= "sup_article.*, sup_name, art_code, art_description, art_unit, (supart_unit_qty * supart_price) AS supart_price_udc";
			
			$this->joins 		= "
				LEFT JOIN sup_supplier USING(idsupplier)
				LEFT JOIN art_article USING(idarticle)
			";
			$this->conds 		= array();
			$this->groups 		= array();
			$this->orders 		= array("supart_ref");
			$this->fieldName	= "supart_ref";
			
			$this->unset		= array("art_unit");
			
			/*** Table filtering ***/
			$this->filters['sup_supplier'] = array("id"=>"idsupplier","in"=>array(),"label"=>"Fournisseur");
			
			/*** Select filtering ***/
			//$this->idparent		= "";
		}

		public function m_newRecord($insert=false)
		{
			$this->values = (object) array(
				"idsuparticle"=>0,
				"idsupplier"=>0,
				"idarticle"=>0,
				"supart_ref"=>null,
				"supart_price"=>0,
				"supart_remark"=>"",
				
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
			$this->conds = array("idsuparticle = ".$idrecord);
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
			$this->conds = array("idsuparticle = ".$idrecord);
			parse_str($dataSent, $data);
			//$this->debugging = true;
			
			return $this->update($data);
		}
		
		public function m_duplicate($idrecord)
		{
			$query = "
			INSERT INTO sup_article (idsupplier,idarticle,supart_ref,supart_unit,supart_price,supart_remark)
			SELECT idsupplier,idarticle,CONCAT(supart_ref,'*'),supart_unit,supart_price,supart_remark
			FROM sup_article
			WHERE idsuparticle = $idrecord
			";
			$this->executeQuery($query);
			$this->result['newId'] = $this->result['idparent'];
			
			return true;
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
				case 1 : $this->fields = "idsuparticle AS id, art_name AS val, '' AS tokens"; $this->select(); break;
				case 2 : 
					$this->fields = "idsuparticle AS id, supart_price, supart_unit, supart_unit_qty, supart_ecotax, art_unit, cat_name AS level1, subcat_name AS level2, CONCAT_WS('-',supart_ref,supart_description) AS level3, art_code AS tokens, art_description AS subtext";
					$this->joins = "LEFT JOIN art_article USING(idarticle) LEFT JOIN art_category USING(idartcategory) LEFT JOIN art_subcategory USING(idartsubcategory)";
					$this->conds = array("idsupplier = ".IDSUPPLIER);
					$this->orders = array("cat_name", "subcat_name", "level3");
					$this->select(); 
					break;
			}
			return true;
		}
		
		public function __destruct()
		{
		}
	}