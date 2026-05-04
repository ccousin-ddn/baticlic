<?php
/**
*** Juillet 2019@SoluFile 
**/
	require_once (dirname(__FILE__)."/../views/v_inv_invoice.php");
	
	class inv_invoice extends inv_invoice_view {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->label 		= gettext("Facture");
			$this->labels 		= gettext("Factures");
			$this->newtext		= gettext("Nouvelle facture");
			$this->picto 		= '<i class="fal fa-file-invoice"></i>';
			$this->level		= 0;
			
			$this->cryptfields	= array("");
			
			$this->lnk			= array(); // GROUP_CONCAT(idlnk) idlnk
			
			$this->inv_type		= array(0=>"Facture",1=>"Avoir");
			$this->inv_status	= array(0=>"Créée",1=>"Envoyée",2=>"Rappel",3=>"Payée");
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
					$this->json['info'] = getText("Facture ajouté");
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
				// model
				$this->conds = array("inv_ref LIKE '%".$this->dataSent['value']."%'");
				$this->dataSent = array();
				if($this->m_getAll(false)){
					// view
					$this->v_createFullTable();
					$this->json['code'] = 1;
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller search', 210);
				}
			}
		}
		
		public function c_dashboard(){
			if($this->checkUserRight()){
				// *** model ***
				$this->fields = "idinvoice, DATEDIFF(inv_deadline, CURDATE()) AS days_late, COALESCE(cli_short_name, cli_name) AS cli_name, inv_title, inv_tot_ttc, CONCAT_WS(' - ', DATE_FORMAT(inv_deadline,'%d/%m/%Y'),COALESCE(cli_short_name, cli_name), CONCAT(inv_tot_ttc, ' €')) AS line";
				$this->joins = "
				LEFT JOIN cli_client USING (idclient)
				";
				$this->conds = array("inv_status IN (1,2)","inv_deadline < CURDATE()");
				$this->orders = array("inv_deadline","cli_name");
				
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
		
		public function c_showCard(){
			if($this->checkUserRight()){
				// model
				if($this->idrecord == "0"){
					$res = $this->m_newRecord(true);
					$this->idrecord = encrypt($this->result['idparent']);
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
				
				// *** inv_line ***
				$inv_line = new inv_line();
				$inv_line->idrecord = $this->idrecord;
				$inv_line->c_tableByParent();
				
				// *** inv_line_ecotax ***
				$inv_line_ecotax = new inv_line_ecotax();
				$inv_line_ecotax->idrecord = $this->idrecord;
				$inv_line_ecotax->c_tableByParent();
				
				// *** inv_line_previous ***
				$inv_line_previous = new inv_line_previous();
				$inv_line_previous->idrecord = $this->idrecord;
				$inv_line_previous->c_tableByParent();
				
				// *** inv_line_retention ***
				$inv_line_retention = new inv_line_retention();
				$inv_line_retention->idrecord = $this->idrecord;
				$inv_line_retention->c_tableByParent();

				// vat
				$vat_vat = new vat_vat();
				$vat_vat->select();
				$vat_vat = array_column($vat_vat->values, 'vat_percent', 'idvat');
				foreach($vat_vat as $key=>$value){
					$this->values->{"vat".$key} = 0;
				}

				foreach($inv_line->values as $line){
					if(!empty($line["idvat"])){
						$this->values->{"vat".$line["idvat"]} += $line["vat_amount"];
					}
				}
				//dump($this->values);
				//$this->values->inv_tot_ttc = $this->values->inv_tot_articles + array_sum(array_column($inv_line->values, 'vat_amount'));
				
				// *** related payment ***
				$pay_payment = new pay_payment();
				$pay_payment->idrecord = $this->idrecord;
				$pay_payment->c_tableByParent();
				
				// *** related tracking ***
				$inv_tracking = new inv_tracking();
				$inv_tracking->idrecord = $this->idrecord;
				$inv_tracking->c_tableByParent();
				
				// *** quo_quotation ***
				$quo_quotation = new quo_quotation();
				$quo_quotation->c_dropdown($this->values->idjob);

				// view	
				if($res){
					$this->v_createCard($doc_document, $inv_line, $inv_line_ecotax, $inv_line_previous, $inv_line_retention, $vat_vat, $pay_payment, $inv_tracking, $quo_quotation);
					$this->json['code'] = 1;
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller showCard', 211);
				}
			}
		}
		
		private function queryPdf(){
			// *** model ***
			$idinvoice = decrypt($this->idrecord);
			$this->fields = "inv_invoice.*, COALESCE(cli_inv_name, cli_name) AS cli_name, cli_tva, COALESCE(cli_inv_contact, cli_contact) AS cli_contact, COALESCE(cli_inv_address_1, cli_address_1) AS cli_address_1, COALESCE(cli_inv_address_2, cli_address_2) AS cli_address_2, COALESCE(cli_inv_pc, cit_pc) AS cli_pc, COALESCE(cli_inv_city, cit_name) AS cli_city, cli_email, sit_name, job_reference, job_name";
			$this->joins = "
			LEFT JOIN cli_client ON inv_invoice.idclient = cli_client.idclient
			LEFT JOIN cit_city USING(idcity)
			LEFT JOIN sit_site USING(idsite) 
			LEFT JOIN job_job USING(idjob) 
			";
			$res = $this->m_getById($idinvoice);
			$this->values = (object) $this->values[0];

			// *** inv_line ***
			$this->line = new inv_line();
			$this->line->conds = array("idinvoice = ".$idinvoice);
			$this->line->m_getAll();
			
			// *** inv_line_ecotax ***
			$line_ecotax = new inv_line_ecotax();
			$line_ecotax->conds = array("idinvoice = ".$idinvoice);
			$line_ecotax->m_getAll();
			$this->values->ecotax = $line_ecotax->values;
			
			// *** inv_line_previous ***
			$line_previous = new inv_line_previous();
			$line_previous->conds = array("inv_line_previous.idinvoice = ".$idinvoice);
			$line_previous->m_getAll();
			$this->values->previous = $line_previous->values;
			
			// *** inv_line_retention ***
			$line_retention = new inv_line_retention();
			$line_retention->conds = array("idinvoice = ".$idinvoice);
			$line_retention->m_getAll();
			$this->values->retention = $line_retention->values;
			
			// vat
			$vat_vat = new vat_vat();
			$vat_vat->select();
			$this->values->vat_vat = array_column($vat_vat->values, 'vat_percent', 'idvat');
			foreach($this->values->vat_vat as $key=>$value){
				$this->values->{"vat".$key} = 0;
				$this->values->{"amount".$key} = 0;
			}
			
			foreach($this->line->values as $line){
				if(!empty($line["idvat"])){
					$this->values->{"vat".$line["idvat"]} += $line["vat_amount"];
					$this->values->{"amount".$line["idvat"]} += $line["lin_total"];
				}
			}
			foreach($line_ecotax->values as $line){
				if(!empty($line["idvat"])){
					$this->values->{"vat".$line["idvat"]} += $line["vat_amount"];
					$this->values->{"amount".$line["idvat"]} += $line["lin_total"];
				}
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
				// update inv_status and job_status
				if($this->values->inv_status < 1){
					$this->m_updateStatus($this->values->idinvoice, 1, 4);
				}
			
				$this->v_createPdf("mail");
				$this->json['code'] = 1;
				$this->json['info'] = strongDecrypt($this->values->cli_email);
				$this->json['subject'] = "FACTURE ".$this->values->inv_ref." - ".$this->values->sit_name." - ".$this->values->job_name;
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
					$this->json['info'] = getText("Facture ajouté");
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
					$this->json['info'] = getText("Facture ajouté");
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller insertFrom', 221);
				}
			}
		}

		public function c_paid(){
			if($this->checkUserRight()){
				// *** model ***
				$idinvoice = decrypt($this->idrecord);
				// 1. create payment
				$payment = new pay_payment();
				$payment->m_newRecord(false);
				$pay_data = (array) $payment->values;
				$pay_data['idinvoice'] = $idinvoice;
				$pay_data['pay_amount'] = $this->m_getRest($idinvoice);
				//$payment->debugging = true;
				$payment->insert($pay_data);
				
				$this->dataSent = "inv_status=3";
				if($this->m_update(decrypt($this->idrecord), $this->dataSent)){
					$this->json['code'] = "dashboardUpdated";
					$this->json['info'] = getText("Facture mise à jour");
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller update', 222);
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
						$this->json['info'] = getText("Facture mise à jour");
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
					$this->json['info'] = getText("Facture supprimé");
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller delete', 223);
				}
			}
		}

		public function c_getTotals(){
			if($this->checkUserRight()){
				$idinvoice = decrypt($this->dataSent['idinvoice']);
				// *** inv_line ***
				$inv_line = new inv_line();
				$inv_line->m_getTotalByInvoice($idinvoice);
				$ht = $inv_line->total['total'];
				unset($inv_line->total['total']);
				$tot_sit = $inv_line->total['tot_situation'];
				unset($inv_line->total['tot_sit']);
				$tot_vat = $inv_line->total['tot_vat'];
				unset($inv_line->total['tot_vat']);
				
				// *** inv_line_previous ***
				$inv_line_previous = new inv_line_previous();
				$inv_line_previous->m_getTotalByInvoice($idinvoice);
				$tot_pre = $inv_line_previous->total['tot_pre'];
				unset($inv_line_previous->total['tot_pre']);
				//$tot_vat += $inv_line_previous->total['tot_vat'];
				unset($inv_line_previous->total['tot_vat']);
				
				// *** inv_line_ecotax ***
				$inv_line_ecotax = new inv_line_ecotax();
				$tot_eco = $inv_line_ecotax->m_getTotalByInvoice($idinvoice);
				unset($inv_line_ecotax->total['tot_eco']);
				$tot_vat += $inv_line_ecotax->total['tot_vat'];
				unset($inv_line_ecotax->total['tot_vat']);
				
				// *** inv_line_retention ***
				$inv_line_retention = new inv_line_retention();
				$tot_ret = $inv_line_retention->m_getTotalByInvoice($idinvoice);
				
				// *** Totals ***
				//$tot_sit = $ht - $tot_pre;
				$tot_ht = $tot_sit + $tot_eco;
				$tot_ttc = $tot_ht + $tot_vat;
				$tot_net = $tot_ttc  - $tot_ret;
				
				$allVat = $inv_line->total;
				foreach($inv_line_ecotax->total as $k => $v) {
					if(array_key_exists($k, $allVat)) {
						$allVat[$k] += $v;
					} else {
						$allVat[$k] = $v; 
					}
				}
				/*
				foreach($inv_line_previous->total as $k => $v) {
					if(array_key_exists($k, $allVat)) {
						$allVat[$k] -= $v;
					} else {
						$allVat[$k] = $v; 
					}
				}
				*/
				$allVat = array_map(function($v){return number_format($v,2,'.','');},$allVat);
				//dump($inv_line->total,$inv_line_ecotax->total,$allVat);
					// *** view ***
				$this->json['code'] = "inv_totals";
				$this->json['allvat'] = $allVat;
				$this->json['ht'] = number_format($ht,2,'.','');
				$this->json['pre'] = number_format($tot_pre,2,'.','');
				$this->json['sit'] = number_format($tot_sit,2,'.','');
				$this->json['ret'] = number_format($tot_ret,2,'.','');
				$this->json['eco'] = number_format($tot_eco,2,'.','');
				$this->json['tht'] = number_format($tot_ht,2,'.','');
				$this->json['vat'] = number_format($tot_vat,2,'.','');
				$this->json['ttc'] = number_format($tot_ttc,2,'.','');
				$this->json['net'] = number_format($tot_net,2,'.','');
				
				return $this->json;
			}else{
				throw new Exception('PHP : Error in controller updateTotal', 223);
			}
		}
		
		public function __destruct()
		{
		}
	}