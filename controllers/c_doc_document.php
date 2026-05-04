<?php
/**
*** Juillet 2019@SoluFile 
**/
	require_once (dirname(__FILE__)."/../views/v_doc_document.php");
	
	class doc_document extends doc_document_view {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->label 		= gettext("document");
			$this->labels 		= gettext("documents");
			$this->picto 		= '';
			$this->level		= 0;
			
			$this->resize = "w400:preview/";
			$this->maxSize = "4";
			$this->type = "img&pdf"; // all, img, pdf, doc, img&pdf, img&mov
			
			$this->cryptfields	= array("");
			
			$this->lnk			= array(); // GROUP_CONCAT(idlnk) idlnk
			
			//$this->arrayName	= array(0=>"No",1=>"Yes");
		}

		public function c_selectByParent($label=true){
			if($this->checkUserRight()){
				// *** model ***
				if($this->m_getAll()){
					// *** view ***
					$this->v_show($label);
					$this->json['code'] = 1;
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller selectByParent', 200);
				}
			}
		}

		public function c_insert(){
			if($this->checkUserRight()){
				// *** model ***
				$this->duplicateKey = false;
				switch($this->dataSent['mimetype'][0]){
					case "image" : $this->dataSent['doc_type'] = 1; break;
					case "video" : $this->dataSent['doc_type'] = 3; break;
					case "text" : $this->dataSent['doc_type'] = 5; break;
					case "application" :
						switch($this->dataSent['mimetype'][1]){
							case "pdf" : $this->dataSent['doc_type'] = 2; break;
							case "vnd.openxmlformats-officedocument.spreadsheetml.sheet" : 
							case "vnd.ms-excel" :
								$this->dataSent['doc_type'] = 4; 
								break;
							case "vnd.openxmlformats-officedocument.wordprocessingml.document" :
							case "msword" :
								$this->dataSent['doc_type'] = 5; 
								break;
							default : $this->dataSent['doc_type'] = 9;
						}
						break;
				}
				unset($this->dataSent['mimetype']);
				
				$this->resize = $this->dataSent['resize'];
				unset($this->dataSent['resize']);
				
				$this->dataSent['doc_info'] = "";
				$this->dataSent['doc_creation_date'] = date("Y-m-d H:i:s");
				$this->dataSent['idfrom'] = decrypt($this->dataSent['idfrom']);
				if($this->m_insert($this->dataSent)){
					$this->json['code'] = "inserted";
					$this->json['idparent'] = encrypt($this->result['idparent']);
					$this->json['info'] = getText("document ajouté");
					// *** view ***
					$record = (object) $this->dataSent;
					
					$this->json['html'] = $this->createThumb($record, $this->json['idparent']);
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller insert', 220);
				}
			}
		}

		public function c_updateInfo(){
			if($this->checkUserRight()){
				// *** model ***
				$this->conds = array("iddocument = ".decrypt($this->idrecord));
				if($this->update($this->dataSent)){
					$this->json['code'] = "updated";
					$this->json['info'] = getText("Document mis à jour");
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller update', 222);
				}
			}
		}

		public function c_delete(){
			if($this->checkUserRight()){
				// *** get info ***
				$this->m_getById(decrypt($this->idrecord));
				// *** model ***
				if($this->m_delete(decrypt($this->idrecord))){
					// *** deleting files ***
					$resizes = explode(",",$this->dataSent['resize']);
					foreach($resizes as $resize){
						$path = explode(":",$resize);
						//var_dump(dirname(__FILE__)."/../".UPLOAD_PATH.$path[1].$this->values[0]['doc_slug_name']);
						@unlink(dirname(__FILE__)."/../".UPLOAD_PATH.$path[1].$this->values[0]['doc_slug_name']);
					}
					@unlink(dirname(__FILE__)."/../".UPLOAD_PATH.$this->values[0]['doc_slug_name']);
					
					$this->json['code'] = "deleted";
					$this->json['info'] = getText("document supprimé");
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller delete', 222);
				}
			}
		}

		public function __destruct()
		{
		}
	}