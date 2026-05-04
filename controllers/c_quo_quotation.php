<?php
/**
*** Juillet 2019@SoluFile 
**/
	require_once (dirname(__FILE__)."/../views/v_quo_quotation.php");
	
	class quo_quotation extends quo_quotation_view {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->label 		= gettext("Devis");
			$this->labels 		= gettext("Devis");
			$this->newtext		= gettext("Nouveau devis");
			$this->picto 		= '<i class="fal fa-calculator"></i>';
			$this->level		= 1;
			
			$this->cryptfields	= array("");
			
			$this->lnk			= array(); // GROUP_CONCAT(idlnk) idlnk
			
			$this->quo_status	= array(1=>'Créé', 2=>'Envoyé', 3=>'Signé-Commande', 4=>'Facturé-Avance', 5=>'Facturé-Solde', 9=>'Refusé');
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
					$this->json['info'] = getText("Devis ajouté");
					// *** view ***
					$this->values = array($this->values);
					$this->v_createTr();
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller newLine', 202);
				}
			}
		}
		
		public function c_dashboard(){
			if($this->checkUserRight()){
				// *** model ***
				$this->fields = "idquotation AS idline, CONCAT(COALESCE(DATE_FORMAT(quo_date,'%d/%m/%Y'),'/'),' - ',COALESCE(cli_short_name, cli_name, '/'),' ', COALESCE(quo_title,'/')) AS line";
				$this->joins = "
				LEFT JOIN cli_client USING (idclient) 
				";
				$this->conds = array("quo_status = 1");
				$this->orders = array("quo_date DESC");
				
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
		
		public function c_search(){
			if($this->checkUserRight()){
				// *** model ***
				$this->conds = array("quo_ref LIKE '%".$this->dataSent['value']."%' OR job_reference LIKE '%".$this->dataSent['value']."%' OR cli_name LIKE '%".$this->dataSent['value']."%' OR cli_short_name LIKE '%".$this->dataSent['value']."%' OR sit_name LIKE '%".$this->dataSent['value']."%'");
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
					$this->values->idsite = 0;
					$this->values->idquotation = $this->result['idparent'];
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
				//$doc_document->json['html'] = "";
				
				// *** quo_line ***
				$quo_line = new quo_line();
				$quo_line->idrecord = $this->idrecord;
				$quo_line->c_tableByParent();
				
				// vat
				$vat_vat = new vat_vat();
				$vat_vat->select();
				$vat_vat = array_column($vat_vat->values, 'vat_percent', 'idvat');
				foreach($vat_vat as $key=>$value){
					$this->values->{"vat".$key} = 0;
				}
				//dump($quo_line->values);
				foreach($quo_line->values as $line){
					if(!empty($line["idvat"])){
						$this->values->{"vat".$line["idvat"]} += $line["vat_amount"];
					}
				}
				$this->values->quo_tot_amount = $this->values->quo_amount + array_sum(array_column($quo_line->values, 'vat_amount'));
				//dump($this->values);
				// *** quo_line_comment ***
				$quo_line_comment = new quo_line_comment();
				$quo_line_comment->idrecord = $this->idrecord;
				$quo_line_comment->c_tableByParent();
				// *** view ***	
				if($res){
					$this->v_createCard($doc_document, $quo_line, $quo_line_comment->json['html'], $vat_vat);
					$this->json['code'] = 1;
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller showCard', 211);
				}
			}
		}

		private function queryPdf(){
			// *** model ***
			$this->fields = "quo_quotation.*, cli_client.*, job_reference, sit_name, GROUP_CONCAT(TRIM(con_email)) AS con_email, GROUP_CONCAT(TRIM(con_name) SEPARATOR '<br>') AS con_name";
			$this->joins .= " LEFT JOIN lnk_job_contact USING(idjob) LEFT JOIN cli_contact USING(idclicontact)";
			$res = $this->m_getById(decrypt($this->idrecord));
			$this->values = (object) $this->values[0];

			// *** quo_line ***
			$this->line = new quo_line();
			$this->line->conds = array("idquotation = ".decrypt($this->idrecord));
			$this->line->m_getAll();
			// vat
			$vat_vat = new vat_vat();
			$vat_vat->select();
			$this->values->vat_vat = array_column($vat_vat->values, 'vat_percent', 'idvat');
			foreach($this->values->vat_vat as $key=>$value){
				$this->values->{"vat".$key} = 0;
				$this->values->{"amount".$key} = 0;
			}
			//dump($this->line->values);
			foreach($this->line->values as $line){
				if(!empty($line["idvat"])){
					$this->values->{"vat".$line["idvat"]} += $line["vat_amount"];
					$this->values->{"amount".$line["idvat"]} += $line["lin_total"];
				}
			}
			//dump($this->values);
			$this->values->quo_tot_amount = $this->values->quo_amount + array_sum(array_column($this->line->values, 'vat_amount'));
			$this->values->quo_tot_vat = array_sum(array_column($this->line->values, 'vat_amount'));
			
			// *** quo_line_comment ***
			$this->comment = new quo_line_comment();
			$this->comment->fields = "com_remark";
			$this->comment->conds = array("idquotation = ".decrypt($this->idrecord));
			$this->comment->orders = array("idline");
			$this->comment->m_getAll();
			foreach($this->comment->values as $com){
				$this->values->quo_remark .= "<br>".$com['com_remark'];
			}
			
			return $res;
		}

		public function c_pdf(){
			// *** view ***	
			if($this->queryPdf()){
				$this->v_createPdf("pdf");
				$this->json['code'] = 1;
				return $this->json;
			}else{
				throw new Exception('PHP : Error in controller pdf', 212);
			}
		}
		
		public function c_mail(){
			// *** view ***	
			if($this->queryPdf()){
				// update quo_status and job_status
				if($this->values->quo_status < 2){
					$this->m_updateStatus($this->values->idquotation, 2, 1);
				}	
			
				$this->v_createPdf("mail");
				$this->json['code'] = 1;
				// decrypt con_emails
				$crypted = explode(",",$this->values->con_email??"");
				$emails = implode("; ",array_map('strongDecrypt', $crypted));
				$this->json['info'] = $emails;
				$this->json['subject'] = "DEVIS ".$this->values->quo_ref;
				return $this->json;
			}else{
				throw new Exception('PHP : Error in controller mail', 213);
			}
		}

		public function c_insert(){
			if($this->checkUserRight()){
				// *** model ***
				$this->duplicateKey = false;
				if($this->m_insert($this->dataSent)){
					$this->json['code'] = "inserted";
					$this->json['idparent'] = encrypt($this->result['idparent']);
					$this->json['info'] = getText("Devis ajouté");
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
					$this->json['info'] = getText("Devis ajouté");
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
				//$this->debugging = true;
				if($this->m_update(decrypt($this->idrecord), $data)){
					// *** new data to update table ***
					if($this->m_getById(decrypt($this->idrecord))){
						// *** view ***
						$this->v_createTr();
						$this->json['code'] = "updated";
						$this->json['info'] = getText("Devis mis à jour");
						return $this->json;
					}else{
						throw new Exception('PHP : Error in controller update', 222);
					}
				}
			}
		}
		
		public function c_updateVariables(){
			if($this->checkUserRight()){
				// *** model ***
				parse_str($this->dataSent, $data);
				$idquotation = decrypt($this->idrecord);
				if($this->m_update($idquotation, $data)){
					// update quo_line
					// eval variables
					$quo_res = $data['quo_variables'];
					if(!empty($quo_res)){
						$quo_var = array_filter(explode(",", trim(preg_replace("/\r|\n|\t/", "",$quo_res))));
						foreach($quo_var as $var){
							eval($var.';');
						}
					}
					$quo_line = new quo_line();
					$quo_line->conds = array("idquotation = ".$idquotation);
					if($quo_line->m_getAll()){
						foreach($quo_line->values as $line){
							// calulate formula
							if(!empty($line['lin_qty_formula'])){
								eval('$qty = '.$line['lin_qty_formula'].';');
							}else{
								$qty = 0;
							}
							// calcul pu provider
							if(!empty($line['lin_pu_formula'])){
								eval('$pu = '.$line['lin_pu_formula'].';');
							}else{
								$pu = 0;
							}
							
							$data_line['lin_quantity'] = number_format(floatval($qty),2,'.','');
							$data_line['lin_pu'] = number_format(floatval($pu),3,'.','');
							
							$data_line['lin_total'] = number_format(floatval($qty)*floatval($pu),3,'.','');
							
							//update quo_line
							$quo_line->conds = array("idline = ".$line['idline']);
							$quo_line->update($data_line);
						}
					}
					// get new data
					$quo_line->idrecord = $this->idrecord;
					$quo_line->c_tableByParent();
					
					// Get lines total 
					$quo_line->m_getTotal($idquotation);
					// Update quotation
					$this->c_updateTotal($idquotation, $quo_line->total);
					
					$this->json['code'] = "lineUpdated";
					$this->json['html'] = $quo_line->json['html'];
					$this->json['info'] = getText("Variables mises à jour");
					return $this->json;
					//return array_merge($this->json, $quotation->json);
				}else{
					throw new Exception('PHP : Error in controller update', 222);
				}
			}
		}

		public function c_delete(){
			if($this->checkUserRight()){
				if($this->m_delete(decrypt($this->idrecord))){
					$this->json['code'] = "deleted";
					$this->json['info'] = getText("Devis supprimé");
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller delete', 223);
				}
			}
		}
		
		public function c_updateTotal($idquotation, $total){
			if($this->checkUserRight()){
				// *** model ***
				//parse_str($this->dataSent, $data);
				
				$data['quo_amount'] = $total['total']; 
				unset($total['total']);
				$data['quo_tot_step'] = $total['total_step']; 
				unset($total['total_step']);
				$data['quo_tot_amount'] = number_format($data['quo_amount'] + $total['tot_vat'],2,'.','');
				unset($total['tot_vat']);
				
				if($this->m_update($idquotation, $data)){
					// *** view ***
					$this->json = array();
					$this->json['ht'] = number_format($data['quo_amount'],2,'.','');
					$this->json['ttc'] = number_format($data['quo_tot_amount'],2,'.','');
					$this->json['step'] = number_format($data['quo_tot_step'],2,'.','');
					$this->json['vat'] = $total;
				}else{
					throw new Exception('PHP : Error in controller updateTotal', 223);
				}
			}
		}
		
		public function c_duplicate(){
			if($this->checkUserRight()){
				// *** model ***
				if($this->m_duplicate(decrypt($this->idrecord))){
					$this->json['idparent'] = encrypt($this->result['newId']);
					$this->json['info'] = getText("Devis dupliqué");
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller duplicate', 224);
				}
			}
		}
		
		public function c_createFrom(){
			if($this->checkUserRight()){
				// *** model ***
				if($this->m_createInvoice(decrypt($this->idrecord))){
					$this->json['code'] = "insertedFrom";
					$this->json['idparent'] = encrypt($this->result['idparent']);
					$this->json['info'] = getText("Facture créée");
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller createFrom', 224);
				}
			}
		}
		
		public function c_createPercentFrom(){
			if($this->checkUserRight()){
				// *** model ***
				$idquotation = decrypt($this->idrecord);
				if($this->m_createPercentInvoice($idquotation)){
					$idinvoice = $this->result['idparent'];
					$this->json['code'] = "insertedFrom";
					$this->json['idparent'] = encrypt($idinvoice);
					
					// Add older situations
					$inv_previous = new inv_line_previous();
					$inv_previous->m_addOlderSituations($idinvoice, $this->dataSent['idsituation'], $idquotation);
					
					// Calculate cumul and put % line situation to 0%
					$quo_line = new quo_line();
					$quo_line->m_updateStepTotal($idquotation);
					
					// Get totals
					$invoice = new inv_invoice();
					$invoice->dataSent['idinvoice'] = $this->json['idparent'];
					$invoice->c_getTotals();
					
					// Update invoice
					$data['inv_tot_previous'] = $invoice->json['pre'];
					$data['inv_tot_situation'] = $invoice->json['sit'];
					$data['inv_tot_ht'] = $invoice->json['tht'];
					$data['inv_tot_vat'] = $invoice->json['vat'];
					$data['inv_tot_ttc'] = $invoice->json['ttc'];
					$data['inv_tot_to_pay'] = $invoice->json['net'];
					$invoice->conds = array("idinvoice = ".$idinvoice);
					$invoice->update($data);
					
					$this->json['info'] = getText("Facture créée");
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller createFrom', 224);
				}
			}
		}
		
		public function c_createFullFrom(){
			if($this->checkUserRight()){
				// *** model ***
				if($this->m_createFullInvoice(decrypt($this->idrecord))){
					$this->json['code'] = "insertedFrom";
					$this->json['idparent'] = encrypt($this->result['idparent']);
					$this->json['info'] = getText("Facture créée");
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller createFrom', 224);
				}
			}
		}
		
		public function c_importToInvoice(){
			if($this->checkUserRight()){
				// *** model ***
				if($this->m_importToInvoice(decrypt($this->idrecord),decrypt($this->dataSent['idinvoice']),$this->dataSent['title'])){
					// update quo_status
					
					$this->m_updateStatus(decrypt($this->idrecord), 4, 0);
					
					$this->json['code'] = "importedToInvoice";
					$this->json['idparent'] = encrypt($this->result['idparent']);
					$this->json['info'] = getText("Devis importé");
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller createFrom', 224);
				}
			}
		}
		
		public function c_dropdown($idjob){
			if($this->checkUserRight()){
				// *** model ***
				$this->fields = "idquotation, quo_ref, quo_title";
				$this->joins = "";
				$this->conds = array("idjob = $idjob", "quo_status IN (1,2,3)");
				$this->groups = array();
				$this->orders = array("quo_ref");
				//$this->debugging = true;
				if($this->select()){
					// *** view ***
					$this->v_createDropdown();
					$this->json['code'] = "quo_dropdown";
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller tableByParent', 201);
				}
			}
		}
		
		public function c_saveDPGF(){
			$data['quo_dpgf_name'] = $this->dataSent['fileName'];
			$data['quo_dpgf_file'] = $this->dataSent['slug'];
			$this->m_update($this->idrecord, $data);
			
			$this->json['code'] = "dpgfSaved";
			$this->json['info'] = getText("DPGF uploaded");
			
			return $this->json;
		}
		
		public function c_deleteDPGF(){
			$data['quo_dpgf_name'] = "";
			$data['quo_dpgf_file'] = "";
			$this->m_update($this->idrecord, $data);
			@unlink(dirname(__FILE__)."/../".UPLOAD_PATH.$this->dataSent['image']);
			
			$this->json['code'] = "dpgfDeleted";
			$this->json['info'] = getText("DPGF deleted");
			
			return $this->json;
		}

		public function c_analyzeDPGF(){
			$bpu = new import_dpgf($this->dataSent['quo_dpgf_file']);
			$bpu->getTitle();
			$bpu->rows = array_values($bpu->rows);
			
			$this->json['code'] = "dpgfAnalyzed";
			$this->json['info'] = getText("DPGF analysé");
			$this->json['html'] = $bpu->showTable();
			
			return $this->json;
		}
		
		public function c_importDPGF(){
			$dpgf = new import_dpgf($this->dataSent['quo_dpgf_file']);
			$dpgf->getTitle();
			// Remove first row (titles)
			array_shift($dpgf->rows);
			
			parse_str($this->dataSent['selects'], $column);
			$idquotation = decrypt($this->idrecord);
			
			$val_options = array(0=>"",1=>"Métier",2=>"Code",3=>"Désignation",4=>"Unité",5=>"Qté",6=>"PU",7=>"Total");

			$query = 'INSERT INTO quo_line (idquotation, idmetier, idvat, lin_dpgf_line, lin_order, lin_description, lin_unit, lin_quantity, lin_pu, lin_total)
			VALUES ';
			$order = 0;
			foreach ($dpgf->rows as $line=>$data){
				$col = substr(array_search('4', $column),3);
				//dump($data,$col);
				if(empty($data[$col])){
					if(array_search('2', $column)){
						$description = $data[substr(array_search('2', $column),3)].' '.$this->clean($data[substr(array_search('3', $column),3)]);
					}else{
						$description = $this->clean($data[substr(array_search('3', $column),3)]);
					}
					$query .= '(
					'.$idquotation.', 
					NULL, 
					1, 
					'.$line.', 
					'.$order.', 
					"'.substr($description,0,299).'", 
					"Titre", 
					NULL, 
					NULL, 
					NULL
					),';
				}else{
					$query .= '(
					'.$idquotation.', 
					'.$data[substr(array_search('1', $column),3)].',
					1, 
					'.$line.', 
					'.$order.', 
					"'.substr($this->clean($data[substr(array_search('3', $column),3)]),0,299).'", 
					"'.substr($this->cleanSoft($data[substr(array_search('4', $column),3)]),0,10).'", 
					"'.floatval($data[substr(array_search('5', $column),3)]).'", 
					"'.floatval($data[substr(array_search('6', $column),3)]).'", 
					"'.floatval($data[substr(array_search('7', $column),3)]).'"
					),';
				}
				$order++;
			}
			$query = substr($query, 0, strlen($query)-1);
			//dump($query);
			$line = new quo_line();
			if($line->executeQuery($query)){
				// Total
				$line->m_getTotal($idquotation);
				// Update quotation
				$this->c_updateTotal($idquotation, $line->total);
				
				$this->json['html'] = "";
				$this->json['code'] = "dpgfImported";
				$this->json['info'] = getText("DPGF importé");
				return $this->json;
			}else{
				throw new Exception('PHP : Error in controller c_dpgfImport', 223);
			}
		}
		
		public function c_importDPGF_OLD(){

			foreach ($bpu->rows as $line=>$data){
				$code = $data[substr(array_search('1', $column),3)];
				if(strlen($code) > 0){
					$query .= '( 
					'.$idlibrary.',
					'.(strlen($code) > 2 ? "(SELECT idworcategory FROM wor_subcategory WHERE subcat_code = '".$code."'), (SELECT idworsubcategory FROM wor_subcategory WHERE subcat_code = '".$code."')" : "(SELECT idworcategory FROM wor_category WHERE cat_code = '".$code."'), NULL").', 
					"'.$data[substr(array_search('2', $column),3)].'", 
					"'.$this->cleanSoft(substr($data[substr(array_search('3', $column),3)], 0, strrpos(substr($data[substr(array_search('3', $column),3)], 0, 80), ' '))).'", 
					"'.$this->cleanSoft($data[substr(array_search('3', $column),3)]).'", 
					"'.$data[substr(array_search('4', $column),3)].'", 
					"'.floatval($data[substr(array_search('5', $column),3)]).'", 
					"'.floatval($data[substr(array_search('5', $column),3)]).'", 
					"'.floatval($data[substr(array_search('5', $column),3)]).'"
					),';
				}
			}
			$query = substr($query, 0, strlen($query)-1);
			$work = new lib_work();
			//dump($query);
			if($work->executeQuery($query)){
				$this->json['code'] = "dpgfImported";
				$this->json['info'] = getText("DPGF importé");
				return $this->json;
			}else{
				throw new Exception('PHP : Error in controller c_importDPGF', 223);
			}
		}
		
		public function __destruct()
		{
		}
	}