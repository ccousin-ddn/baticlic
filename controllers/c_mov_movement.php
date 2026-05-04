<?php
/**
*** Juillet 2019@SoluFile 
**/
	require_once (dirname(__FILE__)."/../views/v_mov_movement.php");
	
	class mov_movement extends mov_movement_view {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->label 		= gettext("Mouvement");
			$this->labels 		= gettext("Mouvements");
			$this->newtext		= gettext("Nouveau mouvement");
			$this->picto 		= '<i class="fal fa-exchange-alt"></i>';
			$this->level		= 1;
			
			$this->cryptfields	= array("");
			
			$this->lnk			= array(); // GROUP_CONCAT(idlnk) idlnk
			
			$this->mov_type		= array(1=>"Entrée",2=>"Sortie");
			$this->mov_place	= array(1=>"Magasin",2=>"Commande",3=>"Chantier",4=>"Véhicule",5=>"Manuel");
			$shop = new sho_shop();
			$this->sho_type = $shop->sho_type;
			$this->mov_year 	= array();
			for ($y = 2020; $y <= date("Y"); $y++) {
				$this->mov_year[$y] = $y;
			}
			$this->mov_month	= array("01"=>"Janvier","02"=>"Février","03"=>"Mars","04"=>"Avril","05"=>"Mai","06"=>"Juin","07"=>"Juillet","08"=>"Août","09"=>"Septembre","10"=>"Octobre","11"=>"Novembre","12"=>"Décembre");
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
		
		public function c_search(){
			if($this->checkUserRight()){
				// *** model ***
				$this->conds = array("mov_name LIKE '%".$this->dataSent['value']."%'");
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

		public function c_newFromShop(){
			if($this->checkUserRight()){
				// *** model ***
				$this->m_newRecord(false);
				// *** view ***	
				$this->v_createCard();
				$this->json['code'] = "movFromShop";
				return $this->json;
			}else{
				throw new Exception('PHP : Error in controller showCard', 211);
			}
		}

		public function c_insert(){
			if($this->checkUserRight()){
				// *** model ***
				$this->duplicateKey = false;
				if($this->m_insert($this->dataSent)){
					$this->json['code'] = "inserted";
					$this->json['idparent'] = encrypt($this->result['idparent']);
					$this->json['info'] = getText("Mouvement ajouté");
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller insert', 220);
				}
			}
		}
		
		public function c_insertFromWarehouse(){
			if($this->checkUserRight()){
				// *** model ***
				$this->duplicateKey = false;
				$mov_type = 2;
				
				/*** column = $this->dataSent[]
				(source) = sourceType | idshop = idSource
				mov_place = destinationType | mov_idplace = idDestination
				***/
				
				$source = $this->dataSent['sourceType'];
				
				$idshop = $this->dataSent['idSource'];
				$mov_place = $this->dataSent['destinationType'];
				$mov_idplace = $this->dataSent['idDestination'];

				if($source == 4){ // from vehicle
					if($mov_place != 3){ // not if into job
						$idshop = $this->dataSent['idDestination'];
						$mov_place = 4;
						$mov_idplace = $this->dataSent['idSource'];
						$mov_type = 1;
					}
				}
				$columns = "iduser=".$_SESSION['iduser']."&idshop=".$idshop."&mov_date=".date("d-m-Y")."&mov_type=".$mov_type."&mov_place=".$mov_place."&mov_idplace=".$mov_idplace;
				
				foreach($this->dataSent["article"] as $article){
					$this->m_insert($columns."&idarticle=".$article[0]."&mov_quantity=".$article[1]);
				}
				$this->json['code'] = "movementsInserted";
				$this->json['info'] = getText("Mouvement stock réalisé");
				return $this->json;
			}else{
				throw new Exception('PHP : Error in controller insert', 220);
			}
		}

		public function c_delete(){
			if($this->checkUserRight()){
				if($this->m_delete(decrypt($this->idrecord))){
					$this->json['code'] = "deleted";
					$this->json['info'] = getText("Mouvement supprimé");
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