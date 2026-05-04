<?php
/**
*** Juillet 2019@SoluFile 
**/
	require_once (dirname(__FILE__)."/../views/v_cli_contact.php");
	
	class cli_contact extends cli_contact_view {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->label 		= gettext("Contact");
			$this->labels 		= gettext("Contacts");
			$this->newtext		= gettext("Nouveau contact");
			$this->picto 		= '<i class="fal fa-id-card"></i>';
			$this->level		= 1;
			
			$this->cryptfields	= array("con_mobile","con_phone","con_phone_perso","con_email");
			
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
				$this->conds = array("cli_contact.idclient = ".decrypt($this->idrecord));
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
					$this->json['info'] = getText("Contact ajouté");
					// *** view ***
					$this->values = array($this->values);
					$this->v_createTr();
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller newLine', 202);
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
					// *** view ***	
					if($res){
						$this->v_createTr(array("action"=>true,"edit"=>true));
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
				if(decrypt($this->idrecord) == 0){
					$this->m_insert($this->dataSent);
					$this->idrecord = encrypt($this->result['idparent']);
					$this->json['code'] = "cardInserted";
				}else{
					$this->m_update(decrypt($this->idrecord), $this->dataSent);
					$this->json['code'] = "cardUpdated";
				}
				// *** new data to update table ***
				if($this->m_getById(decrypt($this->idrecord))){
					// *** view ***
					$this->v_createTr(array("action"=>true,"edit"=>true));
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller saveCardTable', 206);
				}
			}
		}
		
		public function c_search(){
			if($this->checkUserRight()){
				// *** model ***
				$this->conds = array("con_name LIKE '%".$this->dataSent['value']."%'");
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
					$this->json['info'] = getText("Contact ajouté");
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
				if($this->m_insert($this->dataSent)){
					$this->json['code'] = "insertedFrom";
					$this->json['idparent'] = $this->result['idparent'];
					$this->json['info'] = getText("Contact ajouté");
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller insertFrom', 221);
				}
			}
		}

		public function c_update(){
			if($this->checkUserRight()){
				// *** model ***
				if($this->m_update(decrypt($this->idrecord), $this->dataSent)){
					// *** new data to update table ***
					if($this->m_getById(decrypt($this->idrecord))){
						// *** view ***
						$this->v_createTr();
						$this->json['code'] = "updated";
						$this->json['info'] = getText("Contact mis à jour");
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
					$this->json['info'] = getText("Contact supprimé");
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller delete', 223);
				}
			}
		}

		public function __destruct()
		{
		}
	}