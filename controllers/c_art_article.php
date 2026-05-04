<?php
/**
*** Juillet 2019@SoluFile 
**/
	require_once (dirname(__FILE__)."/../views/v_art_article.php");
	
	class art_article extends art_article_view {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->label 		= gettext("Article");
			$this->labels 		= gettext("Articles");
			$this->newtext		= gettext("Nouvel article");
			$this->picto 		= '<i class="fal fa-tools"></i>';
			$this->level		= 1;
			
			$this->cryptfields	= array("");
			
			$this->lnk			= array(); // GROUP_CONCAT(idlnk) idlnk
			
			$this->art_type		= array(1=>"Matériaux",2=>"Matériel",3=>"Outillage",4=>"Consommable");
		}

		public function c_fullTable(){
			if($this->checkUserRight()){
				// *** model ***
				if(empty($this->dataSent)){
					$withFilter=false;
					$this->json['info']="false";
				}else{
					if(isset($this->dataSent['value'])){
						unset($this->dataSent['value']);
					}
					$withFilter=true;
					$this->json['info']="true";
				}
				if($this->m_getAll($withFilter)){
					// *** view ***
					$this->v_createFullTable();
					$this->json['code'] = 1;
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller fullTable', 200);
				}
			}
		}
		
		public function c_tableByParent(){
			if($this->checkUserRight()){
				// *** model ***
				$this->conds = array("idparent = ".decrypt($this->idrecord));
				if($this->m_getAll()){
					// *** view ***
					$this->v_createLineTable($this->idrecord);
					$this->json['code'] = 1;
					return true;
				}else{
					throw new Exception('PHP : Error in controller tableByParent', 201);
				}
			}
		}
		
		public function c_tableByAttendance(){
			if($this->checkUserRight()){
				// *** model ***
				$this->joins .= "LEFT JOIN lnk_att_art USING (idarticle)";
				$this->conds = array("idattendance = ".$this->idrecord);

				if($this->m_getAll()){
					// *** view ***
					$this->v_createLineTable($this->idrecord);
					$this->json['code'] = 1;
					return true;
				}else{
					throw new Exception('PHP : Error in controller tableByParent', 201);
				}
			}
		}
		
		public function c_newLine(){
			if($this->checkUserRight()){
				// *** model ***
				$this->m_newRecord();
				$this->values->idparent = decrypt($this->idrecord);
				$this->values = (array) $this->values;
				if($this->m_insert(http_build_query($this->values))){
					$this->json['code'] = "newLineInserted";
					$this->json['idparent'] = encrypt($this->result['idparent']);
					$this->json['info'] = getText("Article ajouté");
					// *** view ***
					$this->values = array($this->values);
					$this->v_createTr();
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller newLine', 202);
				}
			}
		}
		
		public function c_search(){
			if($this->checkUserRight()){
				// *** model ***
				$this->conds = array("art_description LIKE '%".$this->dataSent['value']."%'");
				$this->dataSent = array();
				if($this->m_getAll(false)){
					// *** view ***
					$this->v_createFullTable();
					$this->json['code'] = 1;
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller search', 210);
				}
			}
		}
		
		public function c_showCard(){
			if($this->checkUserRight()){
				// *** model ***
				if($this->idrecord == "0"){
					$res = $this->m_newRecord(false);
					// info : if true => $this->idrecord = encrypt($this->result['idparent']);
				}else{
					$res = $this->m_getById(decrypt($this->idrecord));
					$this->values = (object) $this->values[0];
				}
				
				// *** doc_document ***
				$doc_document = new doc_document();
				//$doc_document->info = $this->table;
				//$doc_document->idrecord = $this->idrecord;
				//$res_document = $doc_document->c_selectByParent();
				$doc_document->json['html'] = "";
				
				// *** sup_order_line ***
				$sup_order_line = new sup_order_line();
				$sup_order_line->idarticle = decrypt($this->idrecord);
				$sup_order_line->c_tableForHistory();
				
				// *** sto_stock ***
				$sto_stock = new sto_stock();
				$sto_stock->idarticle = decrypt($this->idrecord);
				$sto_stock->c_tableForArticle();
				
				// *** view ***	
				if($res){
					$this->v_createCard($doc_document,$sup_order_line,$sto_stock);
					$this->json['code'] = 1;
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller showCard', 211);
				}
			}
		}

		public function c_insert(){
			if($this->checkUserRight()){
				// *** model ***
				$this->duplicateKey = false;
				if($this->m_insert($this->dataSent)){
					$this->json['code'] = "inserted";
					$this->json['idparent'] = encrypt($this->result['idparent']);
					$this->json['info'] = getText("Article ajouté");
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller insert', 220);
				}
			}
		}

		public function c_duplicate(){
			if($this->checkUserRight()){
				// *** model ***
				if($this->m_duplicate(decrypt($this->idrecord))){
					$this->json['idparent'] = encrypt($this->result['newId']);
					$this->json['info'] = getText("Article dupliqué");
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller duplicate', 224);
				}
			}
		}

		public function c_insertFrom(){
			if($this->checkUserRight()){
				// model
				$this->duplicateKey = false;
				if($this->m_insert($this->dataSent)){
					$this->json['code'] = "insertedFrom";
					$this->json['idparent'] = $this->result['idparent'];
					$this->json['info'] = getText("Article ajouté");
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller insertFrom', 221);
				}
			}
		}
		
		private function getCode($idcat)
		{
			$cat = new art_category();
			$cat->m_getById($idcat);
			$cat->m_update($idcat, "cat_num=".($cat->values[0]['cat_num']+1));
			
			return $cat->values[0]['cat_code'].str_pad($cat->values[0]['cat_num'], 4, "0", STR_PAD_LEFT);
		}
		
		public function c_update(){
			if($this->checkUserRight()){
				$idarticle = decrypt($this->idrecord);
				// *** model ***
				parse_str($this->dataSent, $data);
				if(empty($data['art_code'])){
					$this->dataSent.= "&art_code=".$this->getCode($data['idartcategory']);
				}
				if($this->m_update($idarticle, $this->dataSent)){
					// *** new data to update table ***
					if($this->m_getById($idarticle)){
						// *** view ***
						$this->v_createTr();
						$this->json['code'] = "updated";
						$this->json['info'] = getText("Article mis à jour");
						return $this->json;
					}else{
						throw new Exception('PHP : Error in controller update', 222);
					}
				}
			}
		}

		public function c_delete(){
			if($this->checkUserRight()){
				if($this->m_delete(decrypt($this->idrecord))){
					$this->json['code'] = "deleted";
					$this->json['info'] = getText("Article supprimé");
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller delete', 223);
				}
			}
		}
		
		public function c_importXLS(){
			$xls = new import_xls($this->dataSent['fileName']);
			$xls->getTitle();
			
			// Remove first row (titles)
			array_shift($xls->rows);
			
			$query = 'INSERT IGNORE INTO art_article (idartcategory, idartsubcategory, art_type, art_code, art_description, art_rental_rate, art_unit, art_price)
			VALUES ';
			foreach ($xls->rows as $data){
				$query .= '(
				"'.$data[0].'", 
				"'.$data[1].'", 
				"'.$data[2].'", 
				"'.$this->cleanSoft($data[3]).'", 
				"'.$this->cleanSoft($data[4]).'", 
				"'.floatval($data[5]).'", 
				"'.$this->cleanSoft($data[6]).'", 
				"'.floatval($data[7]).'"
				),';
			}
			$query = substr($query, 0, strlen($query)-1);
			$query .= ' AS new_data ON DUPLICATE KEY UPDATE art_price = new_data.art_price, art_unit = new_data.art_unit, art_rental_rate = new_data.art_rental_rate';
			//dump($query);
			$art_article = new art_article();
			if($art_article->executeQuery($query)){
			
				$this->json['code'] = "art_xlsImported";
				$this->json['info'] = getText("XLS importé");
				$this->json['html'] = $art_article->json['html'];
				return $this->json;
			}else{
				throw new Exception('PHP : Error in controller c_importXLS', 223);
			}
		}
		
		public function __destruct()
		{
		}
	}