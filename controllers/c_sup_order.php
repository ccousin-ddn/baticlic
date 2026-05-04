<?php
/**
*** Juillet 2019@SoluFile 
**/
	require_once (dirname(__FILE__)."/../views/v_sup_order.php");
	
	class sup_order extends sup_order_view {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->label 		= gettext("Commande");
			$this->labels 		= gettext("Commandes");
			$this->newtext		= gettext("Nouvelle commande");
			$this->picto 		= '<i class="fal fa-shopping-cart"></i>';
			$this->level		= 1;
			
			$this->cryptfields	= array("");
			
			$this->lnk			= array(); // GROUP_CONCAT(idlnk) idlnk
			
			$this->ord_status	= array(1=>'En préparation', 2=>'Envoyée', 7=>"BL-reçu", 3=>'Reçu-ok', 4=>'Reçu-pas ok', 5=>'Facture-ok', 6=>'Facture-pas ok');
			$this->ord_bd = array(0=>'Non', 1=>'Oui');
			$this->ord_breakdown= array(0=>'Pas ventilée', 1=>'Ventilée');
			$this->ord_type		= array(0=>'Matériaux',1=>'Sous-traitance',2=>'Matériel');
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
				$this->conds = array("idjob = ".decrypt($this->idrecord));
				if($this->m_getAll(false)){
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
					$this->json['info'] = getText("Commande ajoutée");
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
				$this->conds = array("ord_ref LIKE '%".$this->dataSent['value']."%' OR sup_name LIKE '%".$this->dataSent['value']."%' OR sup_short_name LIKE '%".$this->dataSent['value']."%' OR sit_name LIKE '%".$this->dataSent['value']."%'");
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
					$this->idrecord = encrypt($this->result['idparent']);
					$this->json['idparent'] = $this->idrecord;
				}else{
					$res = $this->m_getById(decrypt($this->idrecord));
					$this->values = (object) $this->values[0];
				}
				
				// *** doc_document ***
				$doc_document = new doc_document();
				$doc_document->info = $this->table;
				$doc_document->idrecord = $this->idrecord;
				$doc_document->resize = "w600:preview/";
				$doc_document->maxSize = "8";
				$doc_document->type = "img&pdf";
				$doc_document->c_selectByParent(false);
				
				// *** sup_order_line ***
				$sup_order_line = new sup_order_line();
				$sup_order_line->idrecord = $this->idrecord;
				$sup_order_line->c_tableByParent();
				
				// *** sup_receipt ***
				$sup_receipt = new sup_receipt();
				$sup_receipt->idrecord = $this->idrecord;
				$sup_receipt->c_tableByParent();
				
				// *** view ***	
				if($res){
					$this->v_createCard($doc_document, $sup_order_line->json['html'], $sup_receipt);
					$this->json['code'] = 1;
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller showCard', 211);
				}
			}
		}

		public function c_pdf(){
			// *** model ***
			$this->fields = "sup_order.*, sup_supplier.*, job_reference, sup_agency.*, COALESCE(city_a.cit_pc, city_s.cit_pc) AS sup_cp, COALESCE(city_a.cit_name, city_s.cit_name) AS sup_city";
			//$this->joins = "";
			$res = $this->m_getById(decrypt($this->idrecord));
			$this->values = (object) $this->values[0];
			
			// *** sup_order_line ***
			$this->line = new sup_order_line();
			$this->line->conds = array("idsuporder = ".decrypt($this->idrecord));
			$this->line->m_getAll();
			
			// *** view ***	
			if($res){
				$this->v_createPdf("pdf");
				$this->json['code'] = 1;
				return $this->json;
			}else{
				throw new Exception('PHP : Error in controller pdf', 212);
			}
		}
		
		public function c_mail(){
			// *** model ***
			$this->fields = "sup_order.*, sup_supplier.*, job_reference";
			//$this->joins = "";
			$res = $this->m_getById(decrypt($this->idrecord));
			$this->values = (object) $this->values[0];
			
			// *** sup_order_line ***
			$this->line = new sup_order_line();
			$this->line->conds = array("idsuporder = ".decrypt($this->idrecord));
			$this->line->m_getAll();
			
			// update ord_status
			if($this->values->ord_status < 2){
				$this->conds = array("idsuporder = ".decrypt($this->idrecord));
				$data['ord_status'] = 2;
				$this->update($data);
			}
			
			// *** view ***	
			if($res){
				$this->v_createPdf("mail");
				$this->json['code'] = 1;
				$this->json['info'] = strongDecrypt($this->values->sup_email);
				return $this->json;
			}else{
				throw new Exception('PHP : Error in controller mail', 214);
			}
		}

		public function c_dashboard(){
			if($this->checkUserRight()){
				// *** model ***
				$this->fields = "idsuporder AS idline, CONCAT(DATE_FORMAT(ord_date,'%d/%m/%Y'),' - ',ord_title, ' <small>(',COALESCE(sup_short_name,sup_name),')</small>') AS line";
				$this->joins = "
				LEFT JOIN sup_supplier USING (idsupplier) 
				";
				$this->conds = array("ord_status = 2","ord_type = 0");
				$this->orders = array("ord_date DESC", "idsuporder");
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
					$this->json['info'] = getText("Commande ajoutée");
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
					$this->json['info'] = getText("Commande ajoutée");
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
						$this->json['info'] = getText("Commande mise à jour");
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
					$this->json['info'] = getText("Commande supprimée");
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller delete', 223);
				}
			}
		}
		
		public function c_getTotal(){
			if($this->checkUserRight()){
				$idsuporder = decrypt($this->idrecord);
				$vat = floatval($this->dataSent);
				// total
				$sup_order_line = new sup_order_line();
				$line = $sup_order_line->m_getTotal($idsuporder);
				$tot_amount = $line['tot_amount'];
				$tot_ecotax = $line['tot_ecotax'];
				$tot_ht = $tot_amount + $tot_ecotax;
				$tot_vat = number_format($tot_ht * $vat / 100,2,'.','');
				$tot_ttc = number_format($tot_ht + $tot_vat,2,'.','');
				$this->dataSent = "ord_amount=".$tot_amount."&ord_ecotax=".$tot_ecotax."&ord_tot_ht=".$tot_ht."&ord_tot_amount=".$tot_ttc;
				// *** model ***
				if($this->m_update(decrypt($this->idrecord), $this->dataSent)){
					// *** view ***
					$this->json['amount'] = number_format($tot_amount,2,'.','');
					$this->json['ecotax'] = number_format($tot_ecotax,2,'.','');
					$this->json['ht'] = number_format($tot_ht,2,'.','');
					$this->json['ttc'] = number_format($tot_ttc,2,'.','');
					$this->json['code'] = "setTotal";
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller getTotal', 223);
				}
			}
		}
		
		public function c_createFrom(){
			if($this->checkUserRight()){
				// *** model ***
				if($this->m_createReceipt(decrypt($this->idrecord))){
					$this->json['code'] = "insertedFrom";
					$this->json['idparent'] = encrypt($this->result['idparent']);
					$this->json['info'] = getText("Bon de réception créé");
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller createFrom', 224);
				}
			}
		}
		
		public function __destruct()
		{
		}
	}