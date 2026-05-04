<?php
/**
*** Juillet 2019@SoluFile 
**/
	require_once (dirname(__FILE__)."/../views/v_sto_stock.php");
	
	class sto_stock extends sto_stock_view {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->label 		= gettext("Stock");
			$this->labels 		= gettext("Stocks");
			$this->picto 		= '<i class="fas fa-pallet"></i>';
			$this->level		= 1;
			
			$this->cryptfields	= array("");
			
			$this->lnk			= array(); // GROUP_CONCAT(idlnk) idlnk
			
			$this->sho_type		= array(1=>"Matériaux",2=>"Matériel",3=>"Outillage",4=>"Equipement",5=>"Véhicule");
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
				$this->fields = "idstock, art_code, art_description, sto_quantity, sto_quantity_min, sto_quantity_max, art_price, (art_price*sto_quantity) AS total, sto_stock.update_date, art_unit";
				/*
				$this->joins .= "
				LEFT JOIN (
				 SELECT idshop, idarticle, mov_creation_date
				 FROM mov_movement
				 WHERE mov_type = 1
				) AS mvt ON sto_stock.idshop = mvt.idshop AND sto_stock.idarticle = mvt.idarticle 
				";
				*/
				$this->conds = array("sto_stock.idshop = ".decrypt($this->idrecord));
				//$this->groups = array("sto_stock.idarticle");
				//$this->debugging = true;
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
		
		public function c_tableForArticle(){
			if($this->checkUserRight()){
				// *** model ***
				$this->fields = "idstock, sho_name, CONCAT(veh_numberplate,' ',veh_model) AS veh_name, sto_quantity";
			
				$this->joins = "
					LEFT JOIN sho_shop USING (idshop)
					LEFT JOIN veh_vehicle USING (idvehicle)
				";
		   		$this->conds = array("sto_quantity > 0", "idarticle = ".$this->idarticle);
				$this->orders = array("sho_name");
				//$this->debugging = true;
				if($this->m_getAll()){
					// *** view ***
					$this->v_createLineTableArticle();
					$this->json['code'] = 1;
					return true;
				}else{
					throw new Exception('PHP : Error in controller tableForReceipt', 201);
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
					$this->json['info'] = getText("Stock ajouté");
					// *** view ***
					$this->values = array($this->values);
					$this->v_createTr();
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
		
		public function c_search(){
			if($this->checkUserRight()){
				// *** model ***
				$this->conds = array("sto_name LIKE '%".$this->dataSent['value']."%'");
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
				$doc_document->json['html'] = "";
				
				// *** view ***	
				if($res){
					$this->v_createCard($doc_document->json['html']);
					$this->json['code'] = 1;
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller showCard', 211);
				}
			}
		}

		public function c_showMaterial(){
			if($this->checkUserRight()){
				// *** model ***
				$this->fields = "sto_quantity, art_description, art_unit, wor_color";
				$this->joins = "
				LEFT JOIN art_article USING (idarticle) 
				LEFT JOIN sho_shop USING (idshop) 
				LEFT JOIN wor_worker USING (idworker) 
				";
				$this->conds = array("idshop = (SELECT idshop FROM sho_shop WHERE sho_type = 4 AND idworker = ".$_SESSION['iduser'].")");
				if($this->m_getAll()){
					// *** view ***
					$this->v_showMaterialWorker();
					$this->json['code'] = "material";
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller tableByParent', 201);
				}
			}
		}
		
		public function c_showMvtOut(){
			if($this->checkUserRight()){
				// *** shop ***
				$shop = new sho_shop();
				$shop->dataSent['sho_type'] = "1,2,3";
				$shop->c_stockGetByType();
				
				$this->v_showShopList($shop);
				$this->json['code'] = "shopsCreated";
				return $this->json;
			}
		}
		
		public function c_showAllByShop(){
			if($this->checkUserRight()){
				// *** model ***
				if($this->dataSent['place'] == 4){ // vehicle
					$lnk_vehicle_article = new lnk_vehicle_article();
					$lnk_vehicle_article->conds = array("art_qty > 0", "idvehicle = ".$this->idrecord);
					//$lnk_vehicle_article->groups = array('idvehicle', 'idarticle');
					//$lnk_vehicle_article->debugging = true;
					$lnk_vehicle_article->select();
					$this->values = $lnk_vehicle_article->values;
				}else{
					$this->conds = array("idshop = ".$this->idrecord, "sto_quantity > 0");
					$this->m_getAll(false);
				}
					// *** view ***
					$this->v_showStockArticles();
					$this->json['code'] = "showArticles";
					return $this->json;
			}else{
				throw new Exception('PHP : Error in controller tableByParent', 201);
			}
		}

		public function c_dashboard(){
			if($this->checkUserRight()){
				// *** model ***
				$this->fields = "idshop AS idline, CONCAT(art_code,' - ',art_description, ' <small>(',sho_name,')</small>',' - ',sto_quantity,'/',sto_quantity_min) AS line";
				$this->joins = "
				LEFT JOIN sho_shop USING (idshop) 
				LEFT JOIN art_article USING (idarticle) 
				";
				$this->conds = array("sho_type IN (1,5)","sto_quantity_min > 0","sto_quantity <= sto_quantity_min");
				$this->orders = array("sho_name", "art_code");
				if($this->select()){
					// *** view ***
					$this->v_createDashboard();
					$this->json['code'] = 1;
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
				if($this->m_insert($this->dataSent)){
					$this->json['code'] = "inserted";
					$this->json['idparent'] = encrypt($this->result['idparent']);
					$this->json['info'] = getText("Stock ajouté");
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
					$this->json['info'] = getText("Stock ajouté");
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
				unset($data['idparent']);
				if($this->m_update(decrypt($this->idrecord), $data)){
					// *** new data to update table ***
					if($this->m_getById(decrypt($this->idrecord))){
						// *** view ***
						$this->v_createTr(array("action"=>true,"edit"=>true,"delete"=>true));
						$this->json['code'] = "lineUpdated";
						$this->json['info'] = getText("Stock mis à jour");
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
					$this->json['info'] = getText("Stock supprimé");
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