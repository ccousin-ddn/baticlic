<?php
/**
*** Juillet 2019@SoluFile 
**/
	require_once (dirname(__FILE__)."/../views/v_wor_worker.php");
	
	class wor_worker extends wor_worker_view {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->label 		= gettext("Compagnon");
			$this->labels 		= gettext("Compagnons");
			$this->newtext		= gettext("Nouveau compagnon");
			$this->picto 		= '<i class="fal fa-user-hard-hat"></i>';
			$this->level		= 1;
			
			$this->cryptfields	= array("");
			
			$this->lnk			= array(); // GROUP_CONCAT(idlnk) idlnk
			
			$this->wor_type		= array(1=>"Salarié",2=>"Intérimaire",3=>"Apprenti");
			$this->wor_state	= array(1=>'Actif',2=>'Inactif');
			$this->wor_level	= array(1=>'N1P1',2=>'N1P2',3=>'N2',4=>'N3P1',5=>'N3P2',6=>'N4P1',7=>'N4P2');
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
					$this->json['info'] = getText("Compagnon ajouté");
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
				$this->conds = array("wor_name LIKE '%".$this->dataSent['value']."%'");
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
		
		public function c_selectLogin(){
			// *** model ***
			$this->fields = "wor_worker.idworker, wor_name, wor_login, wor_color, wor_picture";
			$this->conds = array("wor_state=1","wor_type IN (1,3)");
			$this->orders = array("wor_name");
			if($this->m_getAll(false)){
				// *** view ***
				$this->v_createLogin();
				$this->json['code'] = 1;
				return $this->json;
			}else{
				throw new Exception('PHP : Error in controller fullTable', 200);
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
				//var_dump($this->values);
				// *** doc_document ***
				$doc_document = new doc_document();
				$doc_document->info = $this->table;
				$doc_document->idrecord = $this->idrecord;
				$doc_document->resize = "w600:preview/";
				$doc_document->maxSize = "4";
				$doc_document->type = "img&pdf";
				$doc_document->c_selectByParent(false);
				
				// *** related stock ***
				$sto_stock = new sto_stock();
				$sto_stock->idrecord = encrypt($this->values->idshop);
				$sto_stock->c_tableByParent();
				
				// *** view ***	
				if($res){
					$this->v_createCard($doc_document,$sto_stock);
					$this->json['code'] = 1;
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller showCard', 211);
				}
			}
		}

		public function c_showHomePage(){
			if($this->m_getById($_SESSION['iduser'])){
				// *** view ***
				define ('LOCATION',$_COOKIE['location']??"out");
				$this->v_createHomepage();
				$this->json['code'] = "homePage";
				return $this->json;
			}else{
				throw new Exception('PHP : Error in controller update', 222);
			}
		}

		public function c_insert(){
			if($this->checkUserRight()){
				// *** model ***
				$this->duplicateKey = false;
				if($this->m_insert($this->dataSent)){
					$this->json['code'] = "inserted";
					$this->json['idparent'] = encrypt($this->result['idparent']);
					$this->json['info'] = getText("Compagnon ajouté");
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
					$this->json['info'] = getText("Compagnon ajouté");
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
						$this->json['info'] = getText("Compagnon mis à jour");
						return $this->json;
					}else{
						throw new Exception('PHP : Error in controller update', 222);
					}
				}
			}
		}
		
		public function c_savePicture(){
			$img = $this->dataSent['wor_picture'];
			list($type, $img) = explode(';', $img);
			list(, $img)      = explode(',', $img);
			$picture = base64_decode($img);
			$picName = md5(date("YmdHis")).'.jpg';
			file_put_contents($_SERVER['DOCUMENT_ROOT'] . SITE_DIRECTORY."/upload/pictures/".$picName, $picture);
			
			$data['wor_picture'] = $picName;
			$this->conds = array("idworker = ".decrypt($this->idrecord));
			$this->update($data);
			
			$this->json['code'] = "pictureSaved";
			$this->json['src'] = "upload/pictures/".$picName;
			return $this->json;
		}
		
		public function c_delete(){
			if($this->checkUserRight()){
				if($this->m_delete(decrypt($this->idrecord))){
					$this->json['code'] = "deleted";
					$this->json['info'] = getText("Compagnon supprimé");
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller delete', 223);
				}
			}
		}

		public function c_stockGetAll(){
			if($this->checkUserRight()){
				// *** model ***
				$this->fields = "idworker, wor_name, SUM(equ_qty) AS tot_art";
				$this->joins = "left join wor_equipment using (idworker)";
				$this->conds = array("wor_state = 1");
				$this->groups = array("idworker");
				$this->orders = array("wor_name");
				if($this->select()){
					// *** view ***
					$this->v_createListGroup();
					$this->json['code'] = "showWorkers";
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller search', 210);
				}
			}
		}

		public function __destruct()
		{
		}
	}