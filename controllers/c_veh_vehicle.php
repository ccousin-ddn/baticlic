<?php
/**
*** Ao�t 2021@SoluFile 
**/
	require_once (dirname(__FILE__)."/../views/v_veh_vehicle.php");
	
	class veh_vehicle extends veh_vehicle_view {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->label 		= gettext("Véhicule");
			$this->labels 		= gettext("Véhicules");
			$this->newtext		= gettext("Nouveau Véhicule");
			$this->picto 		= '<i class="fal fa-truck fa-fw"></i>';
			$this->level		= 1;
			
			$this->cryptfields	= array("");
			
			$this->lnk			= array(); // GROUP_CONCAT(idlnk) idlnk
			
			$this->veh_type		= array(1=>'Voiture',2=>'Camionnette',3=>'Camion',4=>"Remorque",0=>'Autre');
			$this->veh_type_fa	= array(1=>'car-side',2=>'shuttle-van',3=>'truck-container',4=>'trailer',0=>'ellipsis-h');
			$this->veh_state	= array(1=>'Actif',0=>'Inactif');
			$this->veh_financial= array(0=>"Achat",1=>"LLD",2=>"LOA",3=>"Location");
			$this->veh_graycard	= array(0=>"Non",1=>"Oui",2=>"Copie");
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
					$this->json['info'] = getText("Véhicule ajouté");
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
				$this->conds = array("LOWER(veh_name) LIKE '$search'");
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
				$this->fields = $this->key." AS id, veh_name AS val, '' AS tokens";
				$this->conds = array("LOWER(veh_name) LIKE '$search'");
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
				
				// *** doc_document ***
				$doc_document = new doc_document();
				$doc_document->info = $this->table;
				$doc_document->idrecord = $this->idrecord;
				$doc_document->resize = "w600:preview/";
				$doc_document->maxSize = "4";
				$doc_document->type = "img&pdf";
				$doc_document->c_selectByParent(false);
				
				// *** sto_stock ***
				$lnk_vehicle_article = new lnk_vehicle_article();
				$lnk_vehicle_article->idrecord = $this->idrecord;
				$lnk_vehicle_article->conds = array("mvt_date_out IS NULL", "idvehicle = ".decrypt($this->idrecord));
				$lnk_vehicle_article->c_tableByParent();
				
				// *** veh_full ***
				$veh_full = new veh_full();
				$veh_full->idrecord = $this->idrecord;
				$veh_full->c_tableByParent();
				
				// *** veh_maintenance ***
				$veh_maintenance = new veh_maintenance();
				$veh_maintenance->idrecord = $this->idrecord;
				$veh_maintenance->c_tableByParent();
				
				// *** veh_control ***
				$veh_control = new veh_control();
				$veh_control->idrecord = $this->idrecord;
				$veh_control->c_tableByParent();
				
				// *** veh_insurance ***
				$veh_insurance = new veh_insurance();
				$veh_insurance->idrecord = $this->idrecord;
				$veh_insurance->c_tableByParent();
				/*
				// *** veh_tire ***
				$veh_tire = new veh_tire();
				$veh_tire->idrecord = $this->idrecord;
				$veh_tire->c_tableByParent();
				*/
				// *** view ***	
				if($res){
					$this->v_createCard($doc_document,$lnk_vehicle_article,$veh_full,$veh_maintenance,$veh_control,$veh_insurance);
					$this->json['code'] = 1;
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller showCard', 212);
				}
			}
		}

		public function c_stockGetAll(){
			if($this->checkUserRight()){
				// *** model ***
				$this->orders = array("veh_type DESC","veh_numberplate");
				$this->conds = array("veh_state = 1");
				if($this->select()){
					// *** view ***
					$this->v_createListGroup();
					$this->json['code'] = "showShops";
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller search', 210);
				}
			}
		}

		public function c_addFuel(){
			if($this->checkUserRight()){
				// get fuel
				$this->fuel = new sho_shop();
				$this->fuel->fields = "idarticle, art_code, art_description, art_unit, idshop, sho_name, sto_quantity";
				$this->fuel->joins = "LEFT JOIN sto_stock USING (idshop) LEFT JOIN art_article USING (idarticle)";
				$this->fuel->conds = array("sho_type = 5");
				$this->fuel->groups = array();
				$this->fuel->select();
				
				// *** model ***
				$this->orders = array("veh_type DESC","veh_numberplate");
				$this->conds = array("veh_state = 1");
				if($this->select()){
					// *** view ***
					$this->v_createAddFuel();
					$this->json['code'] = "showAddFuel";
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller search', 210);
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
					$this->json['info'] = getText("Véhicule ajouté");
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
					$this->json['info'] = getText("Véhicule ajouté");
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
						$this->v_createTr(array("action"=>true,"edit"=>true,"delete"=>true));
						$this->json['code'] = "updated";
						//$this->json['code'] = "lineUpdated";
						$this->json['info'] = getText("Véhicule mis à jour");
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
					$this->json['info'] = getText("Véhicule supprimé");
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