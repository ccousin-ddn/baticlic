<?php
/**
*** Juillet 2019@SoluFile 
**/
	require_once (dirname(__FILE__)."/../views/v_job_job.php");
	
	class job_job extends job_job_view {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->label 		= gettext("Chantier");
			$this->labels 		= gettext("Chantiers");
			$this->newtext		= gettext("Nouveau chantier");
			$this->picto 		= '<i class="fal fa-digging"></i>';
			$this->level		= 1;
			
			$this->cryptfields	= array("");
			
			$this->lnk			= array("lnk_job_contact"); // GROUP_CONCAT(idlnk) idlnk
			
			//$this->arrayName	= array(0=>"No",1=>"Yes");
			$this->job_status	= array(0=>'A traiter', 1=>'En attente client', 8=>'En préparation', 2=>'En cours', 3=>'A facturer', 4=>'Facturé', 5=>'Archivé', 9=>'Perdu');
			$this->cas_urgency	= array(1=>'Très urgent', 2=>'Urgent', 3=>'Neutre', 4=>'Pas urgent');
			$this->job_price_variation = array(0=>'Ferme',1=>'Actualisation',2=>'Révision');
			
			$date = new DateTime();
			for ($month = 1;  $month <= 6; $month++) {
				$this->job_month[$date->format('m-Y')] = $date->format('m-Y');
				$date->modify('-1 months');
			}
		}

		public function c_fullTable(){
			if($this->checkUserRight()){
				// *** model ***
				unset($this->filters['job_month']);
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
					$this->json['info'] = getText("Chantier ajouté");
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
				$this->fields = "idjob AS idline, CONCAT(COALESCE(DATE_FORMAT(job_date_begin,'%d/%m/%Y'),'/'),' - ',COALESCE(cli_short_name, cli_name,'/'),' ', COALESCE(job_name,'/')) AS line";
				$this->joins = "
				LEFT JOIN cli_client USING (idclient) 
				LEFT JOIN sit_site USING (idsite) 
				LEFT JOIN quo_quotation USING (idjob) 
				";
				$this->conds = array("job_status IN (0,1,2)","idquotation IS NULL");
				$this->groups = array("job_job.idjob");
				$this->orders = array("job_date_begin DESC","cli_name","job_name");
				
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
				$this->conds = array("job_reference LIKE '%".$this->dataSent['value']."%' OR cli_name LIKE '%".$this->dataSent['value']."%' OR cli_short_name LIKE '%".$this->dataSent['value']."%' OR sit_name LIKE '%".$this->dataSent['value']."%'");
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
		
		public function c_stockGetAllBy(){
			if($this->checkUserRight()){
				// *** model ***
				if(isset($this->dataSent['idvehicle'])){
					$this->m_stockGetAllByVehicle($this->dataSent['idvehicle']);
					if($this->count == 0){
						$this->m_stockGetAllByPlanning();
					}
				}else{
					$this->m_stockGetAllByPlanning();
				}
				
				if($this->count > 0){
					// *** view ***
					$this->v_stockShowJobs();
				}else{
					$this->json['html'] = "";
				}
				$this->json['code'] = "showShops";
				return $this->json;
			}
		}
		
		public function c_stockGetAllClients(){
			if($this->checkUserRight()){
				// *** model ***
				$task = new tas_task();
				$task->fields = "cli_client.idclient, COALESCE(cli_short_name, cli_name) AS cli_name";
				$task->joins = "
				LEFT JOIN job_job USING (idjob)
				LEFT JOIN cli_client USING (idclient) 
				LEFT JOIN sit_site USING (idsite) 
				";
				$task->conds = array("tas_status = 2", "job_status IN (2,3)");
				$task->groups = array("idclient");
				$task->orders = array("cli_name");
				$task->m_getAll();
				
				$this->v_stockShowAllClients($task);
				$this->json['code'] = "showAllClients";
				return $this->json;
			}
		}
		
		public function c_stockGetAllJobs(){
			if($this->checkUserRight()){
				// *** model ***
				$task = new tas_task();
				$task->fields = "idjob, COALESCE(cli_short_name, cli_name) AS cli_name, COALESCE(job_surname, job_name) AS job_name, sit_name";
				$task->joins = "
				LEFT JOIN job_job USING (idjob)
				LEFT JOIN cli_client USING (idclient) 
				LEFT JOIN sit_site USING (idsite) 
				";
				$task->conds = array("tas_status = 2", "job_status IN (2,3)", "cli_client.idclient = ".decrypt($this->idrecord));
				$task->groups = array("idjob");
				$task->orders = array("sit_name", "job_name");
				$task->m_getAll();
				
				$this->v_stockShowAllJobs($task);
				$this->json['code'] = "showAllJobs";
				return $this->json;
			}
		}
		
		public function c_showCard(){
			if($this->checkUserRight()){
				// *** model ***
				if($this->idrecord == "0"){
					$res = $this->m_newRecord(true);
				   	$this->idrecord = encrypt($this->result['idparent']);
					$this->json['idparent'] = $this->idrecord;
					// New task
					/*
					$task = new tas_task();
					$data = array("idjob"=>$this->result['idparent'], "tas_order"=>1, "tas_name"=>"Préparation chantier", "tas_status"=>2, "tas_date_begin"=>date("d-m-Y"), "tas_block"=>0, "tas_duration"=>"02:00:00", "tas_duration_days"=>"0.25", "tas_men_needed"=>1);
					$task->insert($data);
					*/
				}else{
					$res = $this->m_getById(decrypt($this->idrecord));
					$this->values = (object) $this->values[0];
				}
				
				// *** doc_document ***
				$doc_document = new doc_document();
				$doc_document->info = $this->table;
				$doc_document->idrecord = $this->idrecord;
				$doc_document->resize = "w600:preview/";
				$doc_document->maxSize = "15";
				$doc_document->type = "img&pdf";
				$doc_document->c_selectByParent(false);
				
				// *** tas_task ***
				$tas_task = new tas_task();
				$tas_task->idrecord = $this->idrecord;
				$tas_task->c_listByParent();
				
				// *** quo_quotation ***
				$quo_quotation = new quo_quotation();
				$quo_quotation->idrecord = $this->idrecord;
				$quo_quotation->conds = array("quo_quotation.idjob = ".decrypt($this->idrecord));
				$quo_quotation->c_tableByParent();
				
				// *** inv_invoices ***
				$inv_invoice = new inv_invoice();
				$inv_invoice->idrecord = $this->idrecord;
				$inv_invoice->conds = array("inv_invoice.idjob = ".decrypt($this->idrecord));
				$inv_invoice->c_tableByParent();
				
				// *** sup_order ***
				$sup_order = new sup_order();
				$sup_order->idrecord = $this->idrecord;
				$sup_order->c_tableByParent();
				
				// *** job_picture ***
				//$job_picture = new job_picture();
				//$job_picture->idrecord = $this->idrecord;
				//$job_picture->c_tableByParent();
				
				// *** job_picture ***
				$job_picture = new doc_document();
				$job_picture->info = "job_picture";
				$job_picture->idrecord = $this->idrecord;
				$job_picture->resize = "w400:small/,w800:normal/,:large/";
				$job_picture->maxSize = "4";
				$job_picture->type = "img";
				$job_picture->c_selectByParent(false);
				
				// *** view ***	
				if($res){
					$this->v_createCard($doc_document,$tas_task,$quo_quotation,$inv_invoice,$sup_order,$job_picture);
					$this->json['code'] = 1;
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller showCard', 211);
				}
			}
		}
		
		public function c_getProfit(){
			
			$idjob = decrypt($this->idrecord);
			// *** quo_quotation ***
			$quo_quotation = new quo_quotation();
			$quo_quotation->idrecord = $this->idrecord;
			$quo_quotation->conds = array("quo_quotation.idjob = ".$idjob);
			$quo_quotation->m_getAll(false);
				
			// *** tim_sheet ***
			$tim_timesheet = new tim_timesheet();
			$tim_timesheet->fields = "idworker, wor_name, SEC_TO_TIME(SUM(TIME_TO_SEC(tim_duration))) AS duration, wor_rate, ROUND(SUM(TIME_TO_SEC(tim_duration))*(wor_rate/3600),2) AS tot";
			$tim_timesheet->joins = "LEFT JOIN tas_task USING (idtask) LEFT JOIN wor_worker USING (idworker) ";
			$tim_timesheet->conds = array("idjob = ".decrypt($this->idrecord));
			$tim_timesheet->groups = array("idworker");
			$tim_timesheet->orders = array("tot DESC");
			$tim_timesheet->select();
			
			// *** sup_order ***
			$sup_order = new sup_order();
			$sup_order->conds = array("sup_order.idjob = ".$idjob, "ord_status > 1");
			$sup_order->idrecord = $this->idrecord;
			$sup_order->orders = array("ord_amount DESC");
			$sup_order->m_getAll(false);
				
			// *** lnk_job_article ***
			$lnk_job_article = new lnk_job_article();
			$lnk_job_article->fields = "art_code, art_description, SUM(lnk_qty) AS qty, SUM(art_price*lnk_qty) AS tot";
			$lnk_job_article->joins = "LEFT JOIN art_article USING (idarticle) ";
			$lnk_job_article->conds = array("idjob = ".decrypt($this->idrecord));
			$lnk_job_article->groups = array("idarticle");
			$lnk_job_article->select();
			
			// *** veh_vehicle ***
			$veh_vehicle = new tim_timesheet();
			$veh_vehicle->fields = "veh_model, veh_numberplate, veh_hourly_rate, SEC_TO_TIME(SUM(TIME_TO_SEC(tim_duration))) AS duration, ROUND(SUM(TIME_TO_SEC(tim_duration))*(veh_hourly_rate/3600),2) AS tot";
			$veh_vehicle->joins = "
			LEFT JOIN tas_task USING (idtask) 
			INNER JOIN (
			 SELECT idworker, att_date, idvehicle, veh_model, veh_numberplate, veh_hourly_rate
			 FROM wor_attendance
			 LEFT JOIN veh_vehicle USING (idvehicle)
			 WHERE att_driver = 1
			) AS veh ON tim_date = att_date AND tim_timesheet.idworker = veh.idworker ";
			$veh_vehicle->conds = array("idjob = ".decrypt($this->idrecord));
			$veh_vehicle->groups = array("idvehicle");
			$veh_vehicle->orders = array("tot DESC");
			$veh_vehicle->select();
			
			// *** veh_article ***
			$veh_article = new tim_timesheet();
			$veh_article->fields = "art_code, art_description, art_rental_rate, SEC_TO_TIME(SUM(TIME_TO_SEC(tim_duration))) AS duration, ROUND(SUM(TIME_TO_SEC(tim_duration))*(art_rental_rate/3600),2) AS tot ";
			$veh_article->joins = "
			LEFT JOIN tas_task USING (idtask) 
			INNER JOIN (
				SELECT idworker, att_date, idarticle, art_code, art_description, art_rental_rate 
				FROM wor_attendance 
				LEFT JOIN lnk_vehicle_article USING (idvehicle) 
				LEFT JOIN art_article USING (idarticle) 
				WHERE att_driver = 1 AND art_type=2 AND att_date = mvt_date_in 
			) AS att ON tim_timesheet.idworker = att.idworker AND tim_date = att_date 
			";
			$veh_article->conds = array("idjob = ".decrypt($this->idrecord));
			$veh_article->groups = array("idarticle");
			$veh_article->orders = array("tot DESC");
			$veh_article->select();
			
			// *** view ***	
			$this->v_showProfit($quo_quotation, $tim_timesheet, $sup_order, $lnk_job_article, $veh_vehicle, $veh_article);
			$this->json['code'] = "showProfit";
			return $this->json;
		}
		
		public function c_pdf(){
			// *** model ***
			$this->fields .= ", sit_address_1, sit_pc, sit_city, GROUP_CONCAT(DISTINCT CONCAT_WS('#',con_name, con_mobile, con_phone, con_phone_perso)) AS contacts";
			$this->joins .= "LEFT JOIN cli_contact USING (idclicontact)";
			$res = $this->m_getById(decrypt($this->idrecord));
			$this->values = (object) $this->values[0];
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
			//$this->fields = "";
			//$this->joins = "";
			$res = $this->m_getById(decrypt($this->idrecord));
			$this->values = (object) $this->values[0];
			
			// *** view ***	
			if($res){
				$this->v_createPdf("mail");
				$this->json['code'] = 1;
				$this->json['info'] = strongDecrypt($email_to);
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
					$this->json['info'] = getText("Chantier ajouté");
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
					$this->json['info'] = getText("Chantier ajouté");
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
						$this->json['info'] = getText("Chantier mis à jour");
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
					$this->json['info'] = getText("Chantier supprimé");
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller delete', 223);
				}
			}
		}
		
		public function c_createFrom(){
			if($this->checkUserRight()){
				// *** model ***
				if($this->m_createQuotation(decrypt($this->idrecord))){
					$this->json['code'] = "insertedFrom";
					$this->json['idparent'] = encrypt($this->result['idparent']);
					$this->json['info'] = getText("Devis créé");
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