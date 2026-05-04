<?php
/**
*** Juillet 2019@SoluFile 
**/
	require_once 'model_crud.php';
	
	class art_article_model extends crud {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->table 		= "art_article";
			$this->key			= "idarticle";
			$this->duplicateKey = false;
			
			$this->fields 		= "art_article.*, cat_name, subcat_name";
			
			$this->joins 		= "
				LEFT JOIN art_category USING(idartcategory) 
				LEFT JOIN art_subcategory USING(idartsubcategory) 
			";
			$this->conds 		= array();
			$this->groups 		= array();
			$this->orders 		= array("art_code");
			
			$this->unclean		= array("art_code");
			$this->fieldName	= "art_code";
			
			/*** Table filtering ***/
			$this->filters		= array();
			$this->filters['art_type'] = array("id"=>"art_type","in"=>array(),"label"=>"Type");
			$this->filters['art_category'] = array("id"=>"idcategory","in"=>array(),"label"=>"Catégorie");
			
			/*** Select filtering ***/
			//$this->idparent		= "idshop";
		}

		public function m_newRecord($insert=false)
		{
			$this->values = (object) array(
				"idarticle"=>0,
				"idartcategory"=>0,
				"idartsubcategory"=>0,
				"art_type"=>0,
				"art_code"=>null,
				"art_description"=>"",
				"art_picture"=>null,
				"art_rental_rate"=>0,
				"art_price"=>0,
				"art_remark"=>"",
				
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
			$this->conds = array("idarticle = ".$idrecord);
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
			$this->conds = array("idarticle = ".$idrecord);
			parse_str($dataSent, $data);
			//$this->debugging = true;
			
			return $this->update($data);
		}
		
		public function m_duplicate($idrecord)
		{
			$query = "
			INSERT INTO art_article (idartcategory,idartsubcategory,art_type,art_code,art_description,art_picture,art_rental_rate,art_unit,art_price,art_remark)
			SELECT idartcategory,idartsubcategory,art_type,CONCAT(art_code,'*'),art_description,art_picture,art_rental_rate,art_unit,art_price,art_remark
			FROM art_article
			WHERE idarticle = $idrecord
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
				case 1 : $this->fields = "idarticle AS id, art_code AS val, art_description AS tokens, art_description AS subtext, art_unit AS UdM, art_price"; $this->select(); break;
				case 2 : $this->fields = "idarticle AS id, CONCAT_WS('-',art_code, CONCAT('(',art_unit,')')) AS val, art_description AS tokens, art_description AS subtext, art_unit AS UdM, art_price"; $this->select(); break;
			}
			return true;
		}
		
		public function __destruct()
		{
		}
	}