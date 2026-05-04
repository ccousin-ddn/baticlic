<?php
/**
*** Juillet 2019@SoluFile 
**/
	require_once (dirname(__FILE__)."/../views/v_sup_supplier.php");
	
	class sup_supplier extends sup_supplier_view {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->label 		= gettext("Fournisseur");
			$this->labels 		= gettext("Fournisseurs");
			$this->newtext		= gettext("Nouveau fournisseur");
			$this->picto 		= '<i class="fal fa-warehouse-alt"></i>';
			$this->level		= 1;
			
			$this->cryptfields	= array("sup_siret","sup_tva","sup_phone","sup_fax","sup_email");
			
			$this->lnk			= array(); // GROUP_CONCAT(idlnk) idlnk
			
			//$this->arrayName	= array(0=>"No",1=>"Yes");
			$this->sup_due_type = array(1=>"Réception",2=>"Début de mois", 3=>"Fin de mois");
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
		
		public function c_newLine(){
			if($this->checkUserRight()){
				// *** model ***
				$this->m_newRecord();
				$this->values->idparent = decrypt($this->idrecord);
				$this->values = (array) $this->values;
				if($this->m_insert(http_build_query($this->values))){
					$this->json['code'] = "newLineInserted";
					$this->json['idparent'] = encrypt($this->result['idparent']);
					$this->json['info'] = getText("Fournisseur ajouté");
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
				$this->conds = array("sup_name LIKE '%".$this->dataSent['value']."%'");
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
				$doc_document->info = $this->table;
				$doc_document->idrecord = $this->idrecord;
				$doc_document->resize = "w600:preview/";
				$doc_document->maxSize = "4";
				$doc_document->type = "img&pdf";
				$doc_document->c_selectByParent(false);
				//$doc_document->json['html'] = "";
				
				// *** sup_article ***
				$sup_article = new sup_article();
				$sup_article->idrecord = $this->idrecord;
				$sup_article->c_tableByParent();
				
				// *** sup_agency ***
				$sup_agency = new sup_agency();
				$sup_agency->idrecord = $this->idrecord;
				$sup_agency->c_tableByParent();
				
				// *** view ***	
				if($res){
					$this->v_createCard($doc_document, $sup_article, $sup_agency);
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
				parse_str($this->dataSent, $data);
				if($this->m_insert($data)){
					$this->json['code'] = "inserted";
					$this->json['idparent'] = encrypt($this->result['idparent']);
					$this->json['info'] = getText("Fournisseur ajouté");
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller insert', 220);
				}
			}
		}

		public function c_insertFrom(){
			if($this->checkUserRight()){
				// model
				$this->duplicateKey = false;
				parse_str($this->dataSent, $data);
				//$this->debugging = true;
				if($this->m_insert($data)){
					$this->json['code'] = "insertedFrom";
					$this->json['idparent'] = $this->result['idparent'];
					$this->json['info'] = getText("Fournisseur ajouté");
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller insertFrom', 221);
				}
			}
		}

		public function c_update(){
			if($this->checkUserRight()){
				// *** model ***
				parse_str($this->dataSent, $data);
				if($this->m_update(decrypt($this->idrecord), $data)){
					// *** new data to update table ***
					if($this->m_getById(decrypt($this->idrecord))){
						// *** view ***
						$this->v_createTr();
						$this->json['code'] = "updated";
						$this->json['info'] = getText("Fournisseur mis à jour");
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
					$this->json['info'] = getText("Fournisseur supprimé");
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller delete', 223);
				}
			}
		}

		public function c_saveXLS(){
			//parse_str($this->dataSent, $data);
			$data['sup_rate_file'] = $this->dataSent['slug'];
			$this->m_update($this->idrecord, $data);
			
			$this->json['code'] = "sup_xlsSaved";
			$this->json['info'] = getText("XLS uploaded");
			
			return $this->json;
		}

		public function c_analyzeXLS(){
			$xls = new import_xls($this->dataSent['sup_rate_file']);
			$xls->getTitle();
			$xls->rows = array_values($xls->rows);
			
			$this->json['code'] = "sup_xlsAnalyzed";
			$this->json['info'] = getText("XLS analysé");
			$this->json['html'] = $xls->showTable();
			
			return $this->json;
		}
		
		public function c_importXLS(){
			$xls = new import_xls($this->dataSent['sup_rate_file']);
			$xls->getTitle();
			
			// Remove first row (titles)
			array_shift($xls->rows);
			
			parse_str($this->dataSent['selects'], $column);
			$idsupplier = decrypt($this->idrecord);
			
			$query = 'INSERT IGNORE INTO sup_article (idsupplier, idarticle, supart_ref, supart_description, supart_unit, supart_unit_qty, supart_price, supart_ecotax)
			VALUES ';
			foreach ($xls->rows as $line=>$data){
				//dump($column,array_search('1', $column),substr(array_search('1', $column),3),$data);
				$val_options = array(0=>"",1=>"Code Article",2=>"Référence",3=>"Description",4=>"QdM",5=>"UdC",6=>"Prix UdM",7=>"Ecotax");
				$query .= '( '.$idsupplier.',
				(SELECT idarticle FROM art_article WHERE art_code = "'.$data[substr(array_search('1', $column),3)].'"), 
				"'.$data[substr(array_search('2', $column),3)].'", 
				"'.$this->cleanSoft(substr($data[substr(array_search('3', $column),3)], 0, strrpos(substr($data[substr(array_search('3', $column),3)], 0, 80), ' '))).'", 
				"'.$data[substr(array_search('5', $column),3)].'",
				"'.floatval($data[substr(array_search('4', $column),3)]).'", 
				"'.floatval($data[substr(array_search('6', $column),3)]).'", 
				"'.floatval($data[substr(array_search('7', $column),3)]).'"
				),';
			}
			$query = substr($query, 0, strlen($query)-1);
			$query .= ' AS new_data ON DUPLICATE KEY UPDATE supart_price = new_data.supart_price, supart_unit = new_data.supart_unit, supart_ecotax = new_data.supart_ecotax';
			$sup_article = new sup_article();
			if($sup_article->executeQuery($query)){
				// show table
				$sup_article->idrecord = $this->idrecord;
				$sup_article->c_tableByParent();
			
				$this->json['code'] = "sup_xlsImported";
				$this->json['info'] = getText("XLS importé");
				$this->json['html'] = $sup_article->json['html'];
				return $this->json;
			}else{
				throw new Exception('PHP : Error in controller c_importXLS', 223);
			}
		}

		public function __destruct()
		{
		}
	}