<?php
/**
*** Juillet 2019@SoluFile 
**/
	require_once (dirname(__FILE__)."/../views/v_sup_order_line.php");
	
	class sup_order_line extends sup_order_line_view {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->label 		= gettext("Ligne commande fournisseur");
			$this->labels 		= gettext("Ligne commande fournisseurs");
			$this->picto 		= '<i class=""></i>';
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
				$this->conds = array("idsuporder = ".decrypt($this->idrecord));
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
		
		public function c_tableForReceipt($sup_receipt){
			if($this->checkUserRight()){
				// *** model ***
				$this->fields = "sup_order_line.idline, COALESCE(lin_description, CONCAT_WS('-',supart_ref,art_description)) AS art_name, lin_QdM, lin_UdM, lin_QdC, lin_UdC, COALESCE(SUM(lin_qty_received),0) AS lin_qty_received_total, (lin_QdC - COALESCE(SUM(lin_qty_received),0)) AS lin_qty_remaining, COALESCE(lin_qty_received,0) AS lin_qty_received ";
			
				$this->joins = "
					LEFT JOIN sup_receipt_line ON sup_receipt_line.idline = sup_order_line.idline AND idsupreceipt <= ".$sup_receipt->idsupreceipt."
					LEFT JOIN sup_article USING(idsuparticle) 
					LEFT JOIN art_article USING(idarticle)
				";
		   		$this->conds = array("idsuporder = ".$sup_receipt->idsuporder);
				$this->groups = array("sup_order_line.idline", "lin_qty_received");
				if($this->m_getAll()){
					// *** view ***
					$this->v_createLineTableReceipt($sup_receipt->idsupreceipt, $sup_receipt->rec_status);
					$this->json['code'] = 1;
					return true;
				}else{
					throw new Exception('PHP : Error in controller tableForReceipt', 201);
				}
			}
		}
		
		public function c_tableForHistory(){
			if($this->checkUserRight()){
				// *** model ***
				$this->fields = "idline,coalesce(sup_short_name, sup_name) as sup_name, art_code, art_description, art_price, supart_ref, supart_description, supart_price, lin_QdM, lin_UdM, lin_QdC, lin_UdC, lin_pu_UdM, ord_date";
			
				$this->joins = "
					LEFT JOIN sup_article USING (idsuparticle)
					LEFT JOIN sup_supplier USING (idsupplier)
					LEFT JOIN art_article USING (idarticle)
					LEFT JOIN sup_order USING (idsuporder)
				";
		   		$this->conds = array("art_code IS NOT NULL", "ord_status > 1", "idarticle = ".$this->idarticle);
				$this->orders = array("ord_date DESC");
				//$this->debugging = true;
				if($this->m_getAll()){
					// *** view ***
					$this->v_createLineTableHistory();
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
				$this->values->idsuporder = decrypt($this->idrecord);
				$this->values = (array) $this->values;
				if($this->m_insert(http_build_query($this->values))){
					$this->json['code'] = "newLineInserted";
					$this->json['idparent'] = encrypt($this->result['idparent']);
					$this->json['info'] = getText("Ligne commande fournisseur ajouté");
					// *** view ***
					$this->values['idline'] = $this->result['idparent'];
					
					$this->values = (object) $this->values;
					$this->v_editLine();
					
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
				$this->conds = array("ord_name LIKE '%".$this->dataSent['value']."%'");
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

		public function c_insert(){
			if($this->checkUserRight()){
				// *** model ***
				$this->duplicateKey = false;
				if($this->m_insert($this->dataSent)){
					$this->json['code'] = "inserted";
					$this->json['idparent'] = encrypt($this->result['idparent']);
					$this->json['info'] = getText("Ligne commande fournisseur ajouté");
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
					$this->json['info'] = getText("Ligne commande fournisseur ajouté");
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
						$this->v_createTr(array("action"=>true,"edit"=>true,"delete"=>true));
						$this->json['code'] = "lineUpdated";
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
					$this->json['info'] = getText("Ligne commande fournisseur supprimé");
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