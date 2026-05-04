<?php
/**
*** Juillet 2019@SoluFile 
**/
	require_once (dirname(__FILE__)."/../models/m_job_job.php");
	
	class job_job_view extends job_job_model {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->columns = array(
				array("field"=>"cli_name", "alter"=>"", "title"=>html("Client"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				array("field"=>"con_reference", "alter"=>"", "title"=>html("Marché"), "sortable"=>true, "searchable"=>true, "class"=>"text-break", "halign"=>"left", "align"=>"left", "width"=>400),
				array("field"=>"sit_name", "alter"=>"", "title"=>html("Site"), "sortable"=>true, "searchable"=>true, "class"=>"text-break", "halign"=>"left", "align"=>"left", "width"=>400),
				array("field"=>"job_reference", "alter"=>"", "title"=>html("N° chantier"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				array("field"=>"job_surname", "alter"=>"", "title"=>html("Nom"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				//array("field"=>"job_name", "alter"=>"", "title"=>html("Description"), "sortable"=>true, "searchable"=>true, "class"=>"text-break", "halign"=>"left", "align"=>"left", "width"=>600),
				//array("field"=>"job_date_begin", "alter"=>"date-be", "title"=>html("Date début"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>"sortDate"),
				//array("field"=>"job_cas_date_deadline", "alter"=>"date-be", "title"=>html("Date butoir"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"right", "align"=>"right", "sorter"=>"sortDate"),
				array("field"=>"job_date_begin", "alter"=>"date-be", "title"=>html("Date début"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"right", "align"=>"right", "sorter"=>"sortDate"),
				array("field"=>"job_date_end", "alter"=>"date-be", "title"=>html("Date fin"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"right", "align"=>"right", "sorter"=>"sortDate"),
				array("field"=>"tot_amount", "alter"=>"euro", "title"=>html("Total devis"), "sortable"=>true, "searchable"=>false, "class"=>"text-nowrap", "halign"=>"right", "align"=>"right", "sorter"=>"sortEuro"),
				array("field"=>"tot_invoice", "alter"=>"euro", "title"=>html("Total factures"), "sortable"=>true, "searchable"=>false, "class"=>"text-nowrap", "halign"=>"right", "align"=>"right", "sorter"=>"sortEuro"),
				//array("field"=>"tot_task", "alter"=>"", "title"=>html("Nbr tâches"), "sortable"=>true, "searchable"=>false, "class"=>"text-nowrap", "halign"=>"right", "align"=>"right"),
				array("field"=>"tot_tas_duration", "alter"=>"", "title"=>html("H prévues"), "sortable"=>true, "searchable"=>false, "class"=>"text-nowrap", "halign"=>"right", "align"=>"right", "sorter"=>"sortDuration"),
				array("field"=>"tot_tim_duration", "alter"=>"", "title"=>html("H réalisées"), "sortable"=>true, "searchable"=>false, "class"=>"text-nowrap", "halign"=>"right", "align"=>"right", "sorter"=>"sortDuration"),
			);
			$this->columns_small = array(
				array("field"=>"sit_name", "alter"=>"", "title"=>html("Site"), "sortable"=>true, "searchable"=>true, "class"=>"text-break", "halign"=>"left", "align"=>"left", "width"=>400),
				array("field"=>"job_reference", "alter"=>"", "title"=>html("N° chantier"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				array("field"=>"job_name", "alter"=>"", "title"=>html("Description"), "sortable"=>true, "searchable"=>true, "class"=>"text-break", "halign"=>"left", "align"=>"left", "width"=>400),
				array("field"=>"job_date_begin", "alter"=>"date-be", "title"=>html("Date début"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>"sortDate"),
				//array("field"=>"job_cas_date_deadline", "alter"=>"date-be", "title"=>html("Date butoir"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>"sortDate"),
			);
			$this->color_conds = array(
			array("column"=>"job_status", "query"=>" == 0", "class"=>"td-danger"),
			array("column"=>"job_status", "query"=>" == 1", "class"=>"td-warning"),
			array("column"=>"job_status", "query"=>" == 2", "class"=>"td-info"),
			array("column"=>"job_status", "query"=>" == 3", "class"=>"td-primary"),
			array("column"=>"job_status", "query"=>" == 4", "class"=>"td-success"),
			array("column"=>"job_status", "query"=>" == 5", "class"=>"td-secondary"),
			array("column"=>"job_status", "query"=>" == 9", "class"=>"td-secondary")
			);
		}
		
		public function v_createFullTable()
		{
			// *** Table object ***
			// info : new table(inv_line,level[,checkbox(true/false)][,action(true/false)])
			$table = new table($this->table, $this->level, false);
			// *** create header ***
			$head = $table->createHead($this->picto, ngettext($this->label, $this->labels, $this->count), $this->count);
			// *** create columns title ***
			$thead = $table->createTableHead($this->columns);
			// *** create table lines ***
			$tbody = $table->createTableBody($this->key, $this->values, $this->color_conds);
			// *** create table ***
			$param = array("height"=>"auto", "show-columns"=>"false", "pagination"=>"false", "search"=>true, "classes"=>"table table-no-bordered table-hover table-light", "striped"=>"false", "new-record"=>"true", "export-table"=>"true", "filter-table"=>"job_status,job_month,cli_client,con_contract");
			$this->json['html'] = $table->createTable($head, $param, $thead, $tbody);
		}
		
		public function v_createTr()
		{
			// *** Table object ***
			// info : new table(inv_line,level[,checkbox(true/false)][,action(true/false)])
			$table = new table($this->table, $this->level, false);
			// *** create columns title ***
			$table->createTableHead($this->columns);
			$this->json['html'] = $table->createJsonTr($this->key, $this->values, $this->color_conds);
			$this->json['tdClass'] = $table->info;
		}
		
		public function v_createLineTable($idparent)
		{
			// *** Table object ***
			// info : new table(inv_line,level[,checkbox(true/false)][,action(true/false)])
			$table = new table($this->table, $this->level, false, array("action"=>true,"read"=>true));
			// *** create columns title ***
			$thead = $table->createTableHead($this->columns_small, "thead-card");
			// *** create table lines ***
			$tbody = $table->createTableBody($this->key, $this->values, $this->color_conds);
			// *** create add line ***
			$tfoot = "";
			
			// *** create table ***
			$param = array("height"=>"auto", "show-columns"=>"false", "pagination"=>"false", "search"=>"false", "new-record"=>"false", "classes"=>"table table-sm", "striped"=>"false", "export-table"=>"false", "filter-table"=>"");
			$this->json['html'] = $table->createTableLine($param, $thead, $tbody, $tfoot);
		}
		
		public function v_createCard($doc_document, $tas_task, $quo_quotation, $inv_invoice, $sup_order, $job_picture)
		{
			// *** Input object ***
			$input = new input($this->values, $this->cryptfields);
			// info : input->create(type, label, name, mandatory(true/false)[, defaultValue[, divSize[, inputClass[, info(array)]]]])
			// info : defaultValue = $this->dataSent['newValue']??"" for newFrom
			$inputs = $input->create("disabled", "N° chantier", "job_reference", true, "", "col-md-2", "input-group");
			$inputs .= $input->create("select", "Client", "idclient", true, "", "col-md-2", "", array("idlist"=>"1","table"=>"cli_client","data"=>array("subtext"),"search"=>"true","filterChild"=>"con_contract,sit_site","none-result-text"=>"Créer un client"));
			$inputs .= $input->create("select", "Marché", "idcontract", true, "", "col-md-3", "", array("idlist"=>($this->values->idclient == 0?1:2),"table"=>"con_contract","search"=>"true","filterChild"=>null,"filtering"=>true,"none-result-text"=>"Créer un marché"));
			$inputs .= $input->create("select", "Site", "idsite", true, "", "col-md-3", "", array("idlist"=>"1","table"=>"sit_site","search"=>"true","filterChild"=>null,"filtering"=>true,"none-result-text"=>"Créer un site"));
			$inputs .= $input->create("select", "Statut", "job_status", false, "", "col-md-2", "", array("contents"=>$this->job_status));
			
			$inputs .= $input->create("text", "Nom", "job_surname", true, "", "col-md-2", "", array("max"=>30,"small"=>"(max 30 car.)"));
			$inputs .= $input->create("text", "Description", "job_name", false, "", "col-md-6");
			$inputs .= $input->create("select", "Variation prix", "job_price_variation", false, "", "col-md-2", "", array("contents"=>$this->job_price_variation));
			$inputs .= $input->create("readonly", "Zone", "are_name", false, "", "col-md-2");
			$inputs .= $input->create("select", "Responsable", "iduser", false, "", "col-md-3", "", array("idlist"=>"2","table"=>"usr_user","conds"=>array("usr_type = 1"),"search"=>"true"));
			$inputs .= $input->create("select", "Conducteur des travaux", "idworker", false, "", "col-md-3", "", array("idlist"=>"2","table"=>"usr_user","conds"=>array("usr_type = 2"),"search"=>"true"));
			$inputs .= $input->create("date", "Date début", "job_date_begin", false, "", "col-md-2");
			$inputs .= $input->create("date", "Date fin", "job_date_end", false, "", "col-md-2");
			$inputs .= $input->create("date", "Date PV réception", "job_date_acceptance", false, "", "col-md-2");
			$inputs .= $input->create("textarea", "Commentaire", "job_remark", false, "", "col-md-6", "", array("row"=>2));
			$inputs .= $input->create("checkbox", "Métiers", "idmetier", false, "", "col-md-6", "", array("idlist"=>"1","table"=>"met_metier"));
			
			//$inputs_case = $input->create("text", "Référence", "job_cas_reference", false, "", "col-md-3");
			//$inputs_case .= $input->create("text", "N° commande", "job_ord_reference", false, "", "col-md-3");
			//$inputs_case .= $input->create("select", "Type", "job_idcastype", false, "", "col-md-6", "", array("idlist"=>"1","table"=>"cas_type","search"=>"true","none-result-text"=>"Créer un type"));
			//$inputs_case .= $input->create("date", "Date dossier", "job_cas_date_begin", false, "", "col-md-3");
			//$inputs_case .= $input->create("date", "Date butoir", "job_cas_date_deadline", false, "", "col-md-3");
			//$inputs_case .= $input->create("date", "Date fin contractuelle", "job_cas_date_end", false, "", "col-md-3");
			//$inputs_case .= $input->create("select", "Urgence", "job_cas_urgency", false, "", "col-md-3", "", array("contents"=>$this->cas_urgency));
			//$inputs_case .= $input->create("url", "One drive", "job_cas_cloud_folder", false, "", "col-md-12", "");
			//$inputs_case .= $input->create("textarea", "Contact", "job_cas_remark", false, "", "col-md-9", "", array("row"=>3));
			
			$input_con = $input->create("checkboxColumn", "", "idclicontact", false, "", "col-md-12", " no-line", array("idlist"=>"2","table"=>"cli_contact"));
			
			// *** Div object ***
			$div = new div();
			// info : div->create(divClass, elem1[, elem2[, elem3[, ...]]])
			$div1 = $div->create("row", $inputs);
			//$div_case = $div->create("row", $inputs_case);
			
			// *** related tasks ***
			$div_tas = $div->create("mb-3", $tas_task->json["html"]);
			// *** related quotations ***
			$div_quo = $div->create("mb-3", $quo_quotation->json['html']);
			// *** related invoice ***
			$div_inv = $div->create("mb-3", $inv_invoice->json['html']);
			// *** related order ***
			$div_ord = $div->create("mb-3", $sup_order->json['html']);
			// *** profit ***
			//$div_pro = $div->create("row", $profit);
			// *** related pictures ***
			//$div_pic = $div->create("row", $job_picture->json['html']);
			
			// *** Button object ***
			$button = new button($this->table, $this->idrecord, $this->action);
			// info : button->create(type[, buttonClass])
			$btn_save 	= $button->create("save", "btn-mod");
			$btn_close 	= $button->create("close");
			$btn_delete = $button->create("delete", "btn-left btn-mod");
			$btn_pdf	= "";
			// ex : $btn_pdf	= $button->create("pdf");
			$btn_mail	= "";
			// ex : $btn_mail 	= $button->create("mail");
			$btn_createQuotation = '
				<div class="d-flex justify-content-end">
					<button type="button" id="btn_quo_quotation" class="btn btn-outline-info" onclick="iud(\''.$this->table.'\',\''.$this->idrecord.'\',\'update\',\'none\', function(){cardAction(\''.$this->table.'\',\''.$this->idrecord.'\',\'createFrom\',\'quo_quotation\')})" autocomplete="off" data-loading-text="<i class=\'fas fa-circle-notch fa-spin mr-2\'></i>'.gettext("En cours...").'">
						<i class="fas fa-calculator mr-2"></i>
						'.gettext("Créer un devis").'
					</button>
				</div>
			';
			$btn_pdfCase = '
				<div class="d-flex justify-content-end">
					<button type="button" id="btn_job_case" class="btn btn-outline-info" onclick="iud(\''.$this->table.'\',\''.$this->idrecord.'\',\'update\',\'none\', function(){cardAction(\''.$this->table.'\',\''.$this->idrecord.'\',\'pdf\',\'job_case\')})" autocomplete="off" data-loading-text="<i class=\'fas fa-circle-notch fa-spin mr-2\'></i>'.gettext("En cours...").'">
						<i class="fas fa-file-pdf mr-2"></i>
						'.gettext("Imprimer dossier").'
					</button>
				</div>
			';
			
			// *** Form object ***
			$form = new form();
			// info : form->create("normal/upload", formId, formClass, elem1[, elem2[, elem3[, ...]]])
			$form1 = $form->create("upload", $this->table."_form", "", $div1, $btn_createQuotation);
			//$form2 = $form->create("normal", $this->table."_form", "", $div_case, $btn_pdfCase);
			$form3 = $form->create("normal", "quo_quotation_form", "in-tabs", $div_quo);
			$form4 = $form->create("normal", "tas_task_form", "", $div_tas);
			$form5 = $form->create("normal", $this->table."_form", "in-tabs", $input_con);
			$form6 = $form->create("normal", "inv_invoice_form", "in-tabs", $div_inv);
			$form7 = $form->create("normal", "sup_order_form", "in-tabs", $div_ord);
			//$form8 = $form->create("normal", "", "", $div_pro);
			//$form9 = $form->create("normal", "", "", $div_pic);
			$form9 = $form->create("dropzone", "job_picture_form", "", $job_picture->json['html']);
			//$formDoc = $form->create("upload", $this->table."_form", "", $div_doc);
			$formDoc = $form->create("dropzone", $this->table."_form", "", $doc_document->json['html']);
			
			// *** Tab object ***
			$contact = isset($this->values->idclicontact)?explode(",",$this->values->idclicontact):array();
			
			$tab = new tab(1);
			$tabs = $tab->create("Chantier", "underline", $form1);
			//$tabs = $tab->create("Dossier", "underline ml-2", $form2);
			$tabs = $tab->create(ngettext("Poste","Postes",$tas_task->count).'<span class="ml-2 badge badge-secondary">'.$tas_task->count.'</span>', "other underline ml-2", $form4);
			$tabs = $tab->create(ngettext("Contact","Contacts",count($contact)).'<span class="ml-2 badge badge-secondary">'.count($contact).'</span>', "underline ml-2", $form5);
			$tabs = $tab->create(ngettext("Devis","Devis",$quo_quotation->count).'<span class="ml-2 badge badge-secondary">'.$quo_quotation->count.'</span>', "other underline ml-2", $form3);
			$tabs = $tab->create(ngettext("Facture","Factures",$inv_invoice->count).'<span class="ml-2 badge badge-secondary">'.$inv_invoice->count.'</span>', "other underline ml-2", $form6);
			$tabs = $tab->create(ngettext("Commande","Commandes",$sup_order->count).'<span class="ml-2 badge badge-secondary">'.$sup_order->count.'</span>', "other underline ml-2", $form7);
			$tabs = $tab->create("Rentabilité", "underline ml-2 nav-profit", "");
			$tabs = $tab->create(ngettext("Photo","Photos",$job_picture->count).'<span class="ml-2 badge badge-secondary">'.$job_picture->count.'</span>', "other underline ml-2 nav-job-picture", $form9);
			$tabs = $tab->create(ngettext("Document","Documents",$doc_document->count).'<span class="ml-2 badge badge-secondary">'.$doc_document->count.'</span>', "other underline ml-2 nav-document", $formDoc);
			
			// *** Body variable ***
			// info : $body = $input[.$div][.$form][.tabs]
			$body = $tabs;
			
			// *** Title variable ***
			if(decrypt($this->idrecord) > 0){
				$title = $this->values->job_reference." | ".$this->values->job_name;
			}else{
				$title = gettext("Nouveau ".$this->label);
			}
			// *** Page object ***
			// info : new page(type, id, label, pageClass, body, buttons)
			$page = new page("modal", $this->table."_modal", $this->picto." ".$title, "modal-xlg", $body, $btn_save.$btn_close.$btn_delete);
			$this->json['html'] = $page->create(array($this->values->insert_date??date("y-m-d"), $this->values->update_date??date("y-m-d")));
		}
		
		public function v_showProfit($quo_quotation, $tim_timesheet, $sup_order, $job_article, $veh_vehicle, $veh_article)
		{
			// *** Profit ***
			$cfg = new cfg_config();
			$coef_charge = $cfg->getInfo("coef_charge");
			$coef_fgmo = $cfg->getInfo("coef_fgmo");
			$coef_fgmtx = $cfg->getInfo("coef_fgmtx");
			$coef_fgmtl = $cfg->getInfo("coef_fgmtl");
			$coef_fgst = $cfg->getInfo("coef_fgst");
			$amount_diner = $cfg->getInfo("amount_diner");
			
			$totPos = 0;
			$totNeg = 0;
			
			$acc_timesheet = '';
			$acc_materials = ''; // matériaux (commande + job_article)
			$acc_equipment = ''; // matériel (bunker + véhicule)
			
			$li_pos = '';
			foreach($quo_quotation->values as $quo){
				if($quo['quo_status'] == 3 || $quo['quo_status'] == 4){
					$li_pos .= '
									<li class="list-group-item d-flex justify-content-between align-items-center">
										Devis '.$quo['quo_ref'].'
										<span class="badge badge-light">'.alterData("euro",$quo['quo_amount']).'</span>
									</li>
					';
					$totPos += $quo['quo_amount'];
				}
			}
			
			/*** timesheet ***/
			$tim_tot = 0;
			$li_timesheet = '';
			foreach($tim_timesheet->values as $tim){
				//var_dump($tim_timesheet->values);exit;
				$tot = round($tim['tot']*$coef_charge*$coef_fgmo,2); //getPercent($tim['tot'],$coef_charge,$round=2) + getPercent($tim['tot'],$coef_fgmo,$round=2);
				$tim_tot += $tot;
				$li_timesheet .= '
					<li class="list-group-item d-flex justify-content-between align-items-center px-5">
						<span class="flex-grow-1">'.$tim['wor_name'].'</span>
						<small>'.$tim['duration'].'</small>
						<span class="badge badge-light ml-3 text-right" style="width:110px";>'.alterData("euro",$tot).'</span>
					</li>
				';
			}
			$acc_timesheet = '
						<button class="btn btn-light btn-accordion border-0 rounded-0 px-0 py-2 collapsed" type="button" data-toggle="collapse" data-target="#tim_timesheet" aria-expanded="false" aria-controls="tim_timesheet">
							<div class="d-flex flex-row justify-content-between align-items-center p-1">
								<div class="d-flex flex-row align-items-center">
									<i class="fal fa-chevron-up chevron-ease-180 mx-2"></i>
									<div class="flex-grow-1 ml-2">
										<span class="loc-title w-50">Main d\'oeuvre</span>
									</div>
								</div>
								<div class="px-3"><span class="badge badge-light">'.alterData("euro",$tim_tot).'</span></div>
							</div>
						</button>
						<div id="tim_timesheet" class="collapse" style="" data-parent="#accordionParent">
							<ul class="list-group list-group-flush">
								'.$li_timesheet.'
							<ul>
						</div>
			';
			
			/*** materials ***/
			$mat_tot = 0;
			$con_tot = 0;
			// order
			$li_order = '';
			// contrator
			$li_contractor = '';
			foreach($sup_order->values as $ord){
				$tot = 0;
				switch($ord['ord_type']){ //Type#0=>'Matériaux',1=>'Sous-traitance',2=>'Matériel'
					case 0 : $tot = round($ord['ord_amount'] * $coef_fgmtx, 2); $mat_tot += $tot; break;
					case 1 : $tot = round($ord['ord_amount'] * $coef_fgst, 2); $con_tot += $tot; break;
					case 2 : $tot = round($ord['ord_amount'] * $coef_fgmtx, 2); $mat_tot += $tot; break;
				}
				if($ord['ord_type'] == 1){ // contractors
					$li_contractor .= '
					<li class="list-group-item d-flex justify-content-between align-items-center px-5">
						<i class="fal fa-shopping-cart fa-fw mr-2"></i><span class="mr-2">'.$ord['sup_name'].'</span>
						<small class="flex-grow-1">'.$ord['ord_title'].'</small>
						<span class="badge badge-light">'.alterData("euro",$tot).'</span>
					</li>
					';
				}else{
					$li_order .= '
					<li class="list-group-item d-flex justify-content-between align-items-center px-5">
						<i class="fal fa-shopping-cart fa-fw mr-2"></i><span class="mr-2">'.$ord['sup_name'].'</span>
						<small class="flex-grow-1">'.$ord['ord_title'].'</small>
						<span class="badge badge-light">'.alterData("euro",$tot).'</span>
					</li>
					';
				}
			}
			
			$acc_contractor = '
						<button class="btn btn-light btn-accordion border-0 rounded-0 px-0 py-2 collapsed" type="button" data-toggle="collapse" data-target="#contractors" aria-expanded="false" aria-controls="contractors">
							<div class="d-flex flex-row justify-content-between align-items-center p-1">
								<div class="d-flex flex-row align-items-center">
									<i class="fal fa-chevron-up chevron-ease-180 mx-2"></i>
									<div class="flex-grow-1 ml-2">
										<span class="loc-title w-50">Sous-traitants</span>
									</div>
								</div>
								<div class="px-3"><span class="badge badge-light">'.alterData("euro",$con_tot).'</span></div>
							</div>
						</button>
						<div id="contractors" class="collapse" style="" data-parent="#accordionParent">
							<ul class="list-group list-group-flush">
								'.$li_contractor.'
							<ul>
						</div>
			';
			
			// job_article
			$li_article = '';
			foreach($job_article->values as $art){
				$tot = round($art['tot'] * $coef_fgmtx, 2);
				$mat_tot += $tot;
				$li_article .= '
					<li class="list-group-item d-flex justify-content-between align-items-center px-5">
						<i class="fal fa-digging fa-fw mr-2"></i><span class="flex-grow-1">'.$art['art_code'].'</span>
						<small>'.$art['qty'].'</small>
						<span class="badge badge-light ml-3">'.alterData("euro",$tot).'</span>
					</li>
				';
			}
			$acc_materials = '
						<button class="btn btn-light btn-accordion border-0 rounded-0 px-0 py-2 collapsed" type="button" data-toggle="collapse" data-target="#materials" aria-expanded="false" aria-controls="materials">
							<div class="d-flex flex-row justify-content-between align-items-center p-1">
								<div class="d-flex flex-row align-items-center">
									<i class="fal fa-chevron-up chevron-ease-180 mx-2"></i>
									<div class="flex-grow-1 ml-2">
										<span class="loc-title w-50">Matériaux</span>
									</div>
								</div>
								<div class="px-3"><span class="badge badge-light">'.alterData("euro",$mat_tot).'</span></div>
							</div>
						</button>
						<div id="materials" class="collapse" style="" data-parent="#accordionParent">
							<ul class="list-group list-group-flush">
								'.$li_order.'
								'.$li_article.'
							<ul>
						</div>
			';
			
			/*** equipment ***/
			$equ_tot = 0;
			// vehicle
			$li_vehicle = '';
			foreach($veh_vehicle->values as $veh){
				$tot = round($veh['tot'] * $coef_fgmtl, 2);
				$equ_tot += $tot;
				$li_vehicle .= '
					<li class="list-group-item d-flex justify-content-between align-items-center px-5">
						<i class="fal fa-truck fa-fw mr-2"></i><span class="flex-grow-1">'.$veh['veh_model'].' '.$veh['veh_numberplate'].'</span>
						<small>'.$veh['duration'].'</small>
						<span class="badge badge-light ml-3 text-right" style="width:110px";>'.alterData("euro",$tot).'</span>
					</li>
				';
			}
			// vehicle_article
			$li_article = '';
			foreach($veh_article->values as $veh){
				$tot = round($veh['tot'] * $coef_fgmtl, 2);
				$equ_tot += $tot;
				$li_article .= '
					<li class="list-group-item d-flex justify-content-between align-items-center px-5">
						<i class="fal fa-tools fa-fw mr-2"></i><span class="flex-grow-1">'.$veh['art_description'].'</span>
						<small>'.$veh['duration'].'</small>
						<span class="badge badge-light ml-3 text-right" style="width:110px";>'.alterData("euro",$tot).'</span>
					</li>
				';
			}
			$acc_equipment = '
						<button class="btn btn-light btn-accordion border-0 rounded-0 px-0 py-2 collapsed" type="button" data-toggle="collapse" data-target="#equipment" aria-expanded="false" aria-controls="equipment">
							<div class="d-flex flex-row justify-content-between align-items-center p-1">
								<div class="d-flex flex-row align-items-center">
									<i class="fal fa-chevron-up chevron-ease-180 mx-2"></i>
									<div class="flex-grow-1 ml-2">
										<span class="loc-title w-50">Matériels</span>
									</div>
								</div>
								<div class="px-3"><span class="badge badge-light">'.alterData("euro",$equ_tot).'</span></div>
							</div>
						</button>
						<div id="equipment" class="collapse" style="" data-parent="#accordionParent">
							<ul class="list-group list-group-flush">
								'.$li_vehicle.'
								'.$li_article.'
							<ul>
						</div>
			';
			
			$totNeg = $tim_tot + $con_tot + $mat_tot + $equ_tot;
			
			$profit = '
			<div class="row">
				<div class="col-sm-6 d-flex flex-column justify-content-between">
					<div class="card border-success badge-lg">
						<div class="card-header text-white bg-success text-center p-0"><h1 class="m-0">+</h1></div>
						<div class="card-body p-0">
							<ul class="list-group list-group-flush">
								'.$li_pos.'
							</ul>
						</div>
						<div class="card-footer bg-transparent border-success d-flex justify-content-between align-items-center">
							Total
							<span class="badge badge-light text-success">'.alterData("euro",$totPos).'</span>
						</div>
					</div>
					<div class="card border-secondary badge-lg mt-2">
						<div class="card-header text-white bg-secondary text-center p-0"><h1 class="m-0">Gains</h1></div>
						<div class="card-footer bg-transparent border-secondary d-flex justify-content-between align-items-center">
							&nbsp;
							<span class="badge '.($totPos>$totNeg?"badge-success":"badge-danger").'">'.alterData("euro",$totPos-$totNeg).'</span>
						</div>
					</div>
				</div>
				
				<div class="col-sm-6">
					<div class="card border-danger badge-lg accordion" id="accordionParent">
						<div class="bg-danger text-white text-center">
							<h1 class="m-0">-</h1>
						</div>
						'.$acc_timesheet.'
						'.$acc_contractor.'
						'.$acc_materials.'
						'.$acc_equipment.'
						<div class="card-footer bg-transparent border-danger d-flex justify-content-between align-items-center">
							Total
							<span class="badge badge-light text-danger">'.alterData("euro",$totNeg).'</span>
						</div>
					</div>
				</div>
			</div>
			';
			$this->json['html'] = $profit;
		}
		
		public function v_createPdf($type)
		{
			$job_contacts = explode(",",$this->values->contacts);
			$contacts = "";
			foreach($job_contacts as $contact){
				$phones = explode("#",$contact);
				$contacts .= $phones[0].'<br>';
				unset($phones[0]);
				foreach($phones as $phone){
					$contacts .= strongDecrypt($phone).'<br>';
				}
			}
			//echo '<pre><code>'.$contacts.'</code></pre>';exit;
			$page1 = '
				<div style="width:100%; height:100%;">
					<div style="width:50%; height:100%; float:left; background: transparent url(../images/logo-pdf.png) no-repeat center center; background-size:50%;">
					</div>
					<div style="width:50%; height:100%; float:right; background: transparent url(../images/logo-pdf.png) no-repeat center center; background-size:50%;">
						
						<!-- GREEN PART -->
						<div style="width:100%; background-color:#6fb342; padding:20px;">
							<div style="width:33%; height:80px; float:left; text-align:center; font-size:20px;">
								<strong>Fini</strong>
								<div style="margin:0 auto;width:36px;height:30px;background-color:white;" class="strong badge"></div>
							</div>
							<div style="width:33%; height:80px; float:left; text-align:center; font-size:20px;">
								<strong>N° Commande</strong>
								<br>'.$this->values->job_ord_reference.'
							</div>
							<div style="width:33%; height:80px; float:left; text-align:center; font-size:20px;">
								<strong>N° de JOB</strong>
								<br>'.$this->values->job_reference.'
							</div>
							<div style="display:inline-block; width:100%; border:2px solid black; border-radius:15px; padding:15px; text-align:center; font-size:20px;">
								<strong class="underline">Désignation Travaux</strong>
								<p>'.$this->values->sit_pc.' '.$this->values->sit_city.' - '.$this->values->sit_address_1.'
								<br>'.$this->values->job_name.'
								</p>
							</div>
						</div>
						
						<!-- CONTACT -->
						<div style="width:100%; padding:20px;">
							<div style="width:50%; float:left; font-size:18px;">
								<span class="underline">Personne à contacter :</span>
								<p class="strong">'.nl2br($this->values->job_cas_remark??"").'</p>
							</div>
							
							<div style="width:50%; float:left; font-size:18px;">
								<span class="underline">Maître d\'ouvrage :</span>
								<p class="strong">'.$this->values->cli_name.'
								<br>'.$this->values->sit_name.'
								</p>
								<span class="underline">Interlocuteur.s :</span>
								<p class="strong">'.$contacts.'</p>
							</div>
						</div>
						
						<!-- DATE -->
						<div style="width:100%; padding:20px;">
							<div style="width:50%; float:left;">
								<div style="width:100%; font-size:20px; border-left:1px solid #6fb342; padding-left:40px;">
									<span class="">Date de début</span>
									<p class="strong">'.alterData("date-be",$this->values->job_date_begin).'</p>
								</div>
							</div>
							
							<div style="width:50%; float:right;">
								<div style="width:100%; font-size:20px; border-left:1px solid #6fb342; padding-left:40px;">
									<span class="">Date de fin</span>
									<p class="strong" style="color:red;">'.alterData("date-be",$this->values->job_cas_date_end).'</p>
									<p class="strong">Semaine n° '.alterData("week",$this->values->job_cas_date_end).'</p>
								</div>
							</div>
						</div>
						
						<!-- HOURS -->
						<div style="width:100%; padding:20px;">
							<div style="width:50%; float:left;">
								<div style="width:100%; border-left:1px solid #6fb342; padding:10px;">
									<div style="float:left;">Nombres d\'heures prévues</div>
									<div style="float:right;width:60px;height:30px;" class="strong badge">'.alterData("hour",$this->values->tot_tas_duration).'</div>
								</div>
							</div>
							
							<div style="width:50%; float:left;">
								<div style="width:100%; border-left:1px solid #6fb342; padding:10px;">
									<div style="float:left;">Nombres d\'heures réalisées</div>
									<div style="float:right;width:60px;height:30px;" class="strong badge"></div>
								</div>
							</div>
						</div>
						
						<!-- REMARK -->
						<div style="display:inline-block; width:100%; height:200px; border:1px solid black; border-radius:15px; padding:15px; text-align:left; font-size:20px;">
							<strong class="underline">Commentaires</strong>
							<br><br><br><br>
							<span class="underline">Sous-traitant.s</span>
						</div>
					</div>
				</div>
			';
			
			$page2 = '
				<div style="width:100%; height:100%;">
					<div style="width:45%; height:100%; float:left;">
						<span class="strong underline" style="font-size:24px;">
							Travaux complémentaires et relevés contradictoires :
						</span>
						<table style="margin-top:50px;">
							<tbody>
							<tr>
								<td class="strong">Détails des travaux complémentaires</td>
								<td class="strong">Unité</td>
								<td class="strong">Quantité</td>
							</tr>
							<tr>
								<td class="layout"></td><td class="layout"></td><td class="layout"></td>
							</tr>
							</tbody>
						</table>
						<div style="text-align:center;"><img src="../images/builders.jpg" height="200" alt=""></div>
					</div>
					<div style="width:50%; height:100%; float:right;">
						<span class="" style="font-size:24px;">
						</span>
						<table style="margin-top:60px;">
							<tbody>
							<tr>
								<td class="strong">Schéma explicatif</td>
							</tr>
							<tr>
								<td class="layout"></td>
							</tr>
							</tbody>
						</table>
						<div style="text-align:center;"><img src="../images/builders.jpg" height="200" alt=""></div>
					</div>
				</div>
			';
			
			$css = "<style>\n";
			$css .= file_get_contents('../css/mpdfstylecase.css');
			$css .= "\n</style>\n";
			//echo $css.$page1;exit;
			// Create pdf
			setlocale(LC_ALL, 'fr_FR.utf8');
		    //include($_SERVER['DOCUMENT_ROOT']."/mpdf/mpdf.php");
			include("../libraries/mpdf57/mpdf.php");

		    $mpdf=new mPDF('utf-8','A3-L','','',15,10,10,10,0,0);// left, right, top, bottom, header-top, footer-bottom

			$mpdf->SetFont('nunito');
			
			$mpdf->SetTitle(strcode2utf("Dossier"));
			$mpdf->SetAuthor(COMPANY_NAME);
			
			$mpdf->SetProtection(array('print','copy'));

			$mpdf->SetDisplayMode('fullpage');
			
			$stylesheet = file_get_contents('../css/mpdfstylecase.css');
			$mpdf->WriteHTML($stylesheet,1);
			$mpdf->WriteHTML(preg_replace('/(\v|\s)+/', ' ', $page1));
			$mpdf->AddPage();
			$mpdf->WriteHTML(preg_replace('/(\v|\s)+/', ' ', $page2));
			
			$mpdf->Output("test",'I');
		}
		
		public function v_createDashboard()
		{
			$div = new div();
			$this->json['html'] = $div->dashboard($this->values,"text-left");
		}
		
		public function v_stockShowJobs()
		{
			$html = '';
			foreach($this->values as $job){
				$html .= '
					<a class="list-group-item list-group-item-action list-group-item-client-light d-flex justify-content-between align-items-center" data-id="'.$job['idjob'].'">
						'.$job['job_name'].'
					</a>
				';
			}
			// other task
			$html .= '
			<button onclick="stock_action({action:\'stockGetAllClients\'})" class="btn btn-client-light btn-lg my-3 text-center">Autres chantiers<i class="fal fa-arrow-alt-down ml-2"></i></button>
			<div id="wh_other_job"></div>
			';
			$this->json['html'] = $html;
		}
		
		public function v_stockShowAllClients($task)
		{
			$html = '
				<ul id="searchJob">
					<form class="form-inline h-100 mb-0 flex-grow-1">
						<i class="fal fa-search" style="font-size: 1.5rem;"></i>
						<input id="btnSearchJob" class="h-100 flex-fill" style="font-size: 1.5rem;" type="search" placeholder="" aria-label="Search" autocomplete="off" value="">
					</form>
				</ul>
			';
			foreach($task->values as $res){
				$html .= '
					<button onclick="stock_action({action:\'stockGetAllJobs\',idrecord:\''.encrypt($res['idclient']).'\'})" type="button" class="btn btn-outline-client-light btn-lg btn-block rounded-0">'.$res['cli_name'].'</button>
				';
			}
			$this->json['html'] = $html;
		}
		
		public function v_stockShowAllJobs($task)
		{
			$html = '';
			foreach($task->values as $job){
				$html .= '
					<a class="list-group-item list-group-item-action list-group-item-client-light d-flex justify-content-between align-items-center" data-id="'.$job['idjob'].'">
						'.$job['cli_name'].' '.$job['sit_name'].' - '.$job['job_name'].'
					</a>
				';
			}
			$this->json['html'] = $html;
		}
		
		public function __destruct()
		{
		}
	}