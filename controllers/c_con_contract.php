<?php
/**
*** 12-2023@SOLUfile SRL 
**/
	require_once (dirname(__FILE__)."/../views/v_con_contract.php");
	
	class con_contract extends con_contract_view {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->label 		= gettext("Marché");
			$this->labels 		= gettext("Marchés");
			$this->newtext		= gettext("Nouveau Marché");
			$this->picto 		= '<i class="fal fa-file-signature fa-fw"></i>';
			$this->level		= 1;
			
			$this->cryptfields	= array("");
			
			$this->lnk			= array(); // GROUP_CONCAT(idlnk) idlnk
			
			$this->con_status	= array(0=>'A traiter', 1=>'En attente client', 2=>'En cours', 5=>'Archivé', 9=>'Perdu');
			
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
				$this->conds[] = "idparentToChange = ".decrypt($this->idrecord);
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
				$this->values->idparentToChange = decrypt($this->idrecord);
				$this->values = (array) $this->values;
				if($this->m_insert($this->values)){
					$this->json['code'] = "newLineInserted";
					$this->json['idparent'] = encrypt($this->result['idparent']);
					$this->json['info'] = getText("Marché ajouté");
					// *** view ***
					$this->v_createTr(array("action"=>true,"edit"=>true,"delete"=>true));
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller newLine', 202);
				}
			}
		}

		public function c_editLineTable(){
			if($this->checkUserRight()){
				// *** model ***
				$res = $this->m_getById(decrypt($this->idrecord));
				$this->values = (object) $this->values[0];
				// *** view ***	
				if($res){
					$this->v_editLine();
					$this->json['code'] = "lineEdited";
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller editLineTable', 212);
				}
			}
		}

		public function c_newCardTable(){
			if($this->checkUserRight()){
				// *** model ***
				$this->m_newRecord();
				$this->values->{$this->idparent} = decrypt($this->idrecord);
				$this->idrecord = encrypt(0);
				// *** view ***
				$this->json['code'] = "newCardCreated";
				$this->v_createCard();
				return $this->json;
			}
		}

		public function c_editCardTable(){
			if($this->checkUserRight()){
				// *** model ***
				$res = $this->m_getById(decrypt($this->idrecord));
				$this->values = (object) $this->values[0];
				// *** view ***	
				if($res){
					$this->v_createCard();
					$this->json['code'] = "cardEdited";
				}else{
					throw new Exception('PHP : Error in controller editCardTable', 204);
				}
				return $this->json;
			}
		}
		
		public function c_cancelCardTable(){
			if($this->checkUserRight()){
				// *** model ***
				// if new record => empty
				if(decrypt($this->idrecord) == 0){
					$this->json['code'] = "newCardCanceled";
				}else{
					$res = $this->m_getById(decrypt($this->idrecord));
					//$this->values = (object) $this->values[0];
					// *** view ***	
					if($res){
						$this->v_createTr(array("action"=>true,"edit"=>true,"delete"=>true));
						$this->json['code'] = "cardUpdated";
					}else{
						throw new Exception('PHP : Error in controller cancelCardTable', 205);
					}
				}
				return $this->json;
			}
		}
		
		public function c_saveCardTable(){
			if($this->checkUserRight()){
				// *** model ***
				parse_str($this->dataSent, $data);
				if(decrypt($this->idrecord) == 0){
					$this->m_insert($data);
					$this->idrecord = encrypt($this->result['idparent']);
					$this->json['code'] = "cardInserted";
				}else{
					$this->m_update(decrypt($this->idrecord), $data);
					$this->json['code'] = "cardUpdated";
				}
				// *** new data to update table ***
				if($this->m_getById(decrypt($this->idrecord))){
					// *** view ***
					$this->v_createTr(array("action"=>true,"edit"=>true,"delete"=>true));
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller saveCardTable', 206);
				}
			}
		}

		public function c_search(){
			if($this->checkUserRight()){
				// *** model ***
				$search = "%".strtolower($this->dataSent['value'])."%";
				$this->conds = array("LOWER(con_name) LIKE '$search'");
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

		public function c_selectSearch(){
			if($this->checkUserRight()){
				// *** model ***
				$search = strtolower($this->dataSent)."%";
				$this->fields = $this->key." AS id, con_name AS val, '' AS tokens";
				$this->conds = array("LOWER(con_name) LIKE '$search'");
				$this->dataSent = array();
				if($this->m_getAll(false)){
					// *** view ***
					if($this->count > 0){
						$this->json['code'] = "results";
						$input = new input($this->values);
						$this->json['html'] = $input->create("option","","",false);
					}else{
						$this->json['code'] = "noResult";
						$this->json['html'] = gettext("Rien trouvé");
					}
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller searchSelect', 211);
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
				
				// *** doc_document *** label, maxSize, type, resize
				$doc_document = new doc_document(array("label"=>""));
				$doc_document->info = $this->table;
				$doc_document->idrecord = $this->idrecord;
				$doc_document->resize = "w600:preview/";
				$doc_document->maxSize = "4";
				$doc_document->type = "img&pdf";
				$doc_document->c_selectByParent(false);
				//$doc_document->json['html'] = "";
				
				
				// *** job_jobs ***
				$job_job = new job_job();
				$job_job->idrecord = $this->idrecord;
				$job_job->conds = array("idcontract = ".decrypt($this->idrecord),"job_status IN (1,2,3,4)");
				$job_job->c_tableByParent();
				
				// *** view ***	
				if($res){
					$this->v_createCard($doc_document, $job_job);
					$this->json['code'] = 1;
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller showCard', 212);
				}
			}
		}

		private function queryPDF(){
			// *** model ***
			$this->fields = "";
			//$this->joins = "";
			$res = $this->m_getById(decrypt($this->idrecord));
			$this->values = (object) $this->values[0];
			
			// *** lin_line ***
			$this->line = new lin_line();
			$this->line->conds = array($this->key." = ".decrypt($this->idrecord));
			$this->line->m_getAll();
			
			return $res;
		}

		public function c_pdf(){
			// *** view ***
			if($this->queryPDF()){
				$this->v_createPdf("pdf");
				$this->json['code'] = 1;
				return $this->json;
			}else{
				throw new Exception('PHP : Error in controller pdf', 213);
			}
		}
		
		public function c_mail(){
			// *** view ***
			if($this->queryPDF()){
				$this->v_createPdf("mail");
				$this->json['code'] = 1;
				$this->json['info'] = strongDecrypt($this->values->cli_email);
				return $this->json;
			}else{
				throw new Exception('PHP : Error in controller mail', 214);
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
					$this->json['info'] = getText("Marché ajouté");
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller insert', 220);
				}
			}
		}

		public function c_insertFrom(){
			if($this->checkUserRight()){
				// *** model ***
				$this->duplicateKey = false;
				parse_str($this->dataSent, $data);
				if($this->m_insert($data)){
					$this->json['code'] = "insertedFrom";
					$this->json['idparent'] = $this->result['idparent'];
					$this->json['info'] = getText("Marché ajouté");
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
						$this->v_createTr(array("action"=>false,"edit"=>true,"delete"=>true));
						$this->json['code'] = "updated";
						//$this->json['code'] = "lineUpdated";
						$this->json['info'] = getText("Marché mis à jour");
						return $this->json;
					}else{
						throw new Exception('PHP : Error in controller update', 222);
					}
				}
			}
		}

		public function c_undo(){
			if($this->checkUserRight()){
				// *** model ***
				if($this->m_getById(decrypt($this->idrecord))){
					// *** view ***
					$this->v_createTr(array("action"=>true,"edit"=>true,"delete"=>true));
					$this->json['code'] = "lineUpdated";
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller update', 222);
				}
			}
		}

		public function c_delete(){
			if($this->checkUserRight()){
				if($this->m_delete(decrypt($this->idrecord))){
					$this->json['code'] = "deleted";
					$this->json['info'] = getText("Marché supprimé");
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller delete', 223);
				}
			}
		}

		public function c_saveBPU(){
			$data['con_bpu_name'] = $this->dataSent['fileName'];
			$data['con_bpu_file'] = $this->dataSent['slug'];
			$this->m_update($this->idrecord, $data);
			
			$this->json['code'] = "bpuContractSaved";
			$this->json['info'] = getText("BPU uploaded");
			
			return $this->json;
		}

		public function c_deleteBPU(){
			
			$this->m_deleteBPU($this->dataSent['image']);
			
			$data['con_bpu_name'] = "";
			$data['con_bpu_file'] = "";
			$this->m_update($this->idrecord, $data);
			@unlink(dirname(__FILE__)."/../".UPLOAD_PATH.$this->dataSent['image']);
			
			$this->json['code'] = "bpuDeleted";
			$this->json['info'] = getText("BPU deleted");
			
			return $this->json;
		}

		public function c_analyzeBPUContract(){
			$bpu = new import_bpu($this->dataSent['con_bpu_file']);
			$bpu->getTitle();
			$bpu->rows = array_values($bpu->rows);
			
			$this->json['code'] = "bpuContractAnalyzed";
			$this->json['info'] = getText("BPU analysé");
			$this->json['html'] = $bpu->showTable();
			
			return $this->json;
		}
		
		public function c_importBPUContract(){
			$bpu = new import_bpu($this->dataSent['con_bpu_file']);
			$bpu->getTitle();
			
			// Remove first row (titles)
			array_shift($bpu->rows);
			
			parse_str($this->dataSent['selects'], $column);
			$idcontract = decrypt($this->idrecord);
			
			$val_options = array(0=>"",1=>"Catégorie",2=>"Code",3=>"Désignation",4=>"Description",5=>"Unité",6=>"PU");
			
			$query = 'INSERT INTO lib_work (idcontract, idworcategory, idworsubcategory, wor_code, wor_name, wor_description, wor_unit, wor_rate_small, wor_rate_medium, wor_rate_large, idmetier)
			VALUES ';
			foreach ($bpu->rows as $line=>$data){
				//dump($column,array_search('1', $column),substr(array_search('1', $column),3),$data);
				$cat_code = $data[substr(array_search('1', $column),3)]??"";
				if(strlen($cat_code) > 0){
					$query .= '( 
					'.$idcontract.',
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
				/*
				}else{
					//dump($column,array_search('6', $column),substr(array_search('6', $column)??0,3),$data);
					$query .= '( 
					'.$idcontract.', 0, 0, 
					"'.$data[substr(array_search('2', $column),3)].'", 
					"'.$this->cleanSoft(substr($data[substr(array_search('3', $column),3)], 0, strrpos(substr($data[substr(array_search('3', $column),3)], 0, 80), ' '))).'", 
					"'.$this->cleanSoft($data[substr(array_search('3', $column),3)]).'", 
					"'.substr($data[substr(array_search('4', $column),3)],0,10).'", 
					"'.floatval($data[substr(array_search('5', $column),3)]).'", 
					"'.floatval($data[substr(array_search('5', $column),3)]).'", 
					"'.floatval($data[substr(array_search('5', $column),3)]).'",
					"'.floatval($data[substr(array_search('6', $column),3)]??0).'"
					),';
				*/
				}
			}
			$query = substr($query, 0, strlen($query)-1);
			$work = new lib_work();
			//dump($query);
			if($work->executeQuery($query)){
				// show table
				$work->idrecord = $this->idrecord;
				$work->c_tableByParent();
				
				$this->json['code'] = "bpuContractImported";
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