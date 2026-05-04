<?php
/**
*** Juillet 2019@SoluFile 
**/
	require_once (dirname(__FILE__)."/../views/v_lib_library.php");
	
	class lib_library extends lib_library_view {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->label 		= gettext("Bibliothèque");
			$this->labels 		= gettext("Bibliothèques");
			$this->newtext		= gettext("Nouvelle Bibliothèque");
			$this->picto 		= '<i class="fal fa-th-list"></i>';
			$this->level		= 1;
			
			$this->cryptfields	= array("");
			
			$this->lnk			= array(); // GROUP_CONCAT(idlnk) idlnk
			
			//$this->arrayName	= array(0=>"No",1=>"Yes");
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
					$this->json['info'] = getText("BPU ajouté");
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
				$this->conds = array("lib_name LIKE '%".$this->dataSent['value']."%'");
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
					$res = $this->m_newRecord(true);
					$this->idrecord = $this->values->idlibrary = encrypt($this->result['idparent']);
				}else{
					$res = $this->m_getById(decrypt($this->idrecord));
					$this->values = (object) $this->values[0];
				}
				
				// *** doc_document ***
				//$doc_document = new doc_document();
				//$doc_document->info = $this->table;
				//$doc_document->idrecord = $this->idrecord;
				//$res_document = $doc_document->c_selectByParent();
				//$doc_document->json['html'] = "";
				
				// *** view ***	
				if($res){
					$this->v_createCard();
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
					$this->json['info'] = getText("BPU ajouté");
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
				if($this->m_insert($data)){
					$this->json['code'] = "insertedFrom";
					$this->json['idparent'] = $this->result['idparent'];
					$this->json['info'] = getText("BPU ajouté");
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller insertFrom', 221);
				}
			}
		}

		public function c_update(){
			if($this->checkUserRight()){
				parse_str($this->dataSent, $data);
				// *** model ***
				if($this->m_update(decrypt($this->idrecord), $data)){
					// *** new data to update table ***
					if($this->m_getById(decrypt($this->idrecord))){
						// *** view ***
						$this->v_createTr();
						$this->json['code'] = "updated";
						$this->json['info'] = getText("BPU mis à jour");
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
					$this->json['info'] = getText("BPU supprimé");
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller delete', 223);
				}
			}
		}
		
		public function c_saveBPU(){
			$data['lib_bpu_name'] = $this->dataSent['fileName'];
			$data['lib_bpu_file'] = $this->dataSent['slug'];
			$this->m_update($this->idrecord, $data);
			
			$this->json['code'] = "bpuSaved";
			$this->json['info'] = getText("BPU uploaded");
			
			return $this->json;
		}

		public function c_analyzeBPU(){
			$bpu = new import_bpu($this->dataSent['lib_bpu_file']);
			$bpu->getTitle();
			$bpu->rows = array_values($bpu->rows);
			
			$this->json['code'] = "bpuAnalyzed";
			$this->json['info'] = getText("BPU analysé");
			$this->json['html'] = $bpu->showTable();
			
			return $this->json;
		}
		
		public function c_importBPU(){
			$bpu = new import_bpu($this->dataSent['lib_bpu_file']);
			$bpu->getTitle();
			
			// Remove first row (titles)
			array_shift($bpu->rows);
			
			parse_str($this->dataSent['selects'], $column);
			$idlibrary = decrypt($this->idrecord);
			
			$val_options = array(0=>"",1=>"Catégorie",2=>"Code",3=>"Désignation",4=>"Description",5=>"Unité",6=>"PU");
			
			$query = 'INSERT INTO lib_work (idlibrary, idworcategory, idworsubcategory, wor_code, wor_name, wor_description, wor_unit, wor_rate_small, wor_rate_medium, wor_rate_large, idmetier)
			VALUES ';
			foreach ($bpu->rows as $line=>$data){
				//dump($column,array_search('1', $column),substr(array_search('1', $column),3),$data);
				$cat_code = $data[substr(array_search('1', $column),3)]??"";
				if(strlen($cat_code) > 0){
					$query .= '( 
					'.$idlibrary.',
					'.(strlen($cat_code) > 1 ? "(SELECT idworcategory FROM wor_category WHERE cat_code = UPPER('".substr($cat_code,0,1)."')), (SELECT idworsubcategory FROM wor_subcategory LEFT JOIN wor_category USING (idworcategory) WHERE cat_code = UPPER('".substr($cat_code,0,1)."') AND subcat_code = '".substr($cat_code,1)."')" : "(SELECT idworcategory FROM wor_category WHERE cat_code = UPPER('".$cat_code."')), NULL").', 
					"'.$data[substr(array_search('2', $column),3)].'", 
					SUBSTRING("'.$this->clean(substr($data[substr(array_search('3', $column),3)], 0, 100)).'",1,90), 
					"'.$this->cleanSoft($data[substr(array_search('4', $column),3)]).'", 
					"'.substr($data[substr(array_search('5', $column),3)],0,10).'", 
					'.floatval($data[substr(array_search('6', $column),3)]).', 
					'.floatval($data[substr(array_search('6', $column),3)]).', 
					'.floatval($data[substr(array_search('6', $column),3)]).',
					'."(SELECT cat_idmetier FROM wor_category WHERE cat_code = UPPER('".substr($cat_code,0,1)."'))".'
					),';
				}
			}
			$query = substr($query, 0, strlen($query)-1);
			$work = new lib_work();
			//dump($query);
			if($work->executeQuery($query)){
				// show table
				$work->idrecord = $this->idrecord;
				$work->c_tableByParent();
				
				$this->json['code'] = "bpuImported";
				$this->json['info'] = getText("BPU importé");
				$this->json['html'] = $work->json['html'];
				return $this->json;
			}else{
				throw new Exception('PHP : Error in controller c_importBPU', 223);
			}
		}

		public function __destruct()
		{
		}
	}