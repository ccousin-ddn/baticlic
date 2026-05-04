<?php
/**
*** Juillet 2019@SoluFile 
**/
	require_once (dirname(__FILE__)."/../models/m_cli_client.php");
	
	class cli_client_view extends cli_client_model {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->columns = array(
				array("field"=>"idclient", "alter"=>"", "title"=>html("id"), "sortable"=>true, "searchable"=>false, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				array("field"=>"cli_name", "alter"=>"", "title"=>html("Dénomination"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				array("field"=>"cli_short_name", "alter"=>"", "title"=>html("Nom court"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				array("field"=>"typ_name", "alter"=>"", "title"=>html("Type"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				array("field"=>"cit_name", "alter"=>"", "title"=>html("Commune"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				array("field"=>"cli_email", "alter"=>"email", "title"=>html("E-mail"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				array("field"=>"cli_phone_1", "alter"=>"phone", "title"=>html("Téléphone"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
			);
			$this->color_conds = array();
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
			$param = array("height"=>"auto", "show-columns"=>"false", "pagination"=>"false", "search"=>true, "classes"=>"table table-no-bordered  table-hover table-light", "striped"=>"false", "new-record"=>"true", "export-table"=>"true", "filter-table"=>"cli_type,cli_status");
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
		}
		
		public function v_createLineTable($idparent)
		{
			// *** Table object ***
			// info : new table(inv_line,level[,checkbox(true/false)][,action(true/false)])
			$table = new table($this->table, $this->level, false, true);
			// *** create columns title ***
			$thead = $table->createTableHead($this->columns,"thead-light");
			// *** create table lines ***
			$tbody = $table->createTableBody($this->key, $this->values, $this->color_conds);
			// *** create add line ***
			$tfoot = $table->createTableFoot(count($this->columns), $idparent);
			
			// *** create table ***
			$param = array("height"=>"auto", "show-columns"=>"false", "pagination"=>"false", "search"=>"false", "new-record"=>"false", "classes"=>"table table-bordered table-sm", "striped"=>"false", "export-table"=>"false", "filter-table"=>"");
			$this->json['html'] = $table->createTableLine($param, $thead, $tbody, $tfoot);
		}
		
		public function v_createCard($sit_site="", $job_job="", $quo_quotation="", $inv_invoice="",$cli_agency="", $cli_contact="", $cli_login="")
		{
			// *** Input object ***
			$input = new input($this->values, $this->cryptfields);
			// info : input->create(type, label, name, mandatory(true/false)[, defaultValue[, divSize[, inputClass[, info(array)]]]])
			// info : defaultValue = $this->dataSent['newValue']??"" for newFrom
			$inputs = $input->create("text", "Dénomination", "cli_name", true, $this->dataSent['newValue']??"", "col-md-4");
			$inputs .= $input->create("text", "Dénomination courte", "cli_short_name", false, "", "col-md-2");
			$inputs .= $input->create("select", "Type", "idclitype", true, "", "col-md-2", "", array("idlist"=>"1","table"=>"cli_type","search"=>"true","filterChild"=>null,"none-result-text"=>"Créer le type"));
			$inputs .= $input->create("radio", "Statut", "cli_status", false, "", "col-md-4", "no-line", array("contents"=>$this->cli_status));
			$inputs .= $input->create("text", "Contact", "cli_contact", false, "", "col-md-3");
			$inputs .= $input->create("text", "E-mail", "cli_email", true, "", "col-md-3");
			$inputs .= $input->create("text", "Téléphone", "cli_phone_1", false, "", "col-md-2");
			$inputs .= $input->create("readonly", "Niveau tarif", "cli_rate_level", false, "", "col-md-4", "no-line", array("contents"=>$this->cli_rate_level));
			$inputs .= $input->create("text", "Adresse", "cli_address_1", false, "", "col-md-6");
			
			$inputs .= $input->create("select", gettext("Commune"), "idcity", false, "", "col-md-6", "", array("idlist"=>"1","table"=>"cit_city","search"=>"true","searchDB"=>"true","none-result-text"=>"Créer la commune"));
			//$inputs .= $input->create("text", "Ville", "cli_city", false, "", "col-md-3");
			
			$inputs .= $input->create("text", "Adresse (complément)", "cli_address_2", false, "", "col-md-6");
			//$inputs .= $input->create("text", "Code postal", "cli_pc", false, "", "col-md-2");
			$inputs .= $input->create("text", "N° SIRET", "cli_siret", false, "", "col-md-3");
			$inputs .= $input->create("text", "N° TVA", "cli_tva", false, "", "col-md-3");
			$inputs .= $input->create("uploadDrop", "Logo", "cli_logo", false, "", "col-md-3", "", array("table"=>$this->table,"type"=>"img","maxSize"=>"5","resize"=>"h120:logo/,w800:large/"));
			$inputs .= $input->create("textarea", "Commentaire", "cli_remark", false, "", "col-md-9", "", array("row"=>2));
			//$inputs .= $input->create("color", "Couleur principale", "cli_color_1", false, "", "col-md-1");
			//$inputs .= $input->create("color", "Couleur secondaire", "cli_color_2", false, "", "col-md-1");
			
			$inputs_inv = $input->create("text", "Dénomination", "cli_inv_name", false, "", "col-md-6");
			$inputs_inv .= $input->create("text", "Contact", "cli_inv_contact", false, "", "col-md-6");
			//$inputs_inv .= $input->breakline;
			$inputs_inv .= $input->create("text", "Adresse", "cli_inv_address_1", false, "", "col-md-6");
			$inputs_inv .= $input->create("text", "Adresse (complément)", "cli_inv_address_2", false, "", "col-md-6");
			$inputs_inv .= $input->create("text", "Code postal", "cli_inv_pc", false, "", "col-md-2");
			$inputs_inv .= $input->create("text", "Ville", "cli_inv_city", false, "", "col-md-4");
			
			// *** Div object ***
			$div = new div();
			// info : div->create(divClass, elem1[, elem2[, elem3[, ...]]])
			$div1 = $div->create("row", $inputs);
			$div2 = $div->create("row", $inputs_inv);
			
			// *** related site ***
			$div_sit = $div->create("mb-3", $sit_site->json['html']??"");
			// *** related job ***
			$div_job = $div->create("mb-3", $job_job->json['html']??"");
			// *** related quotation ***
			$div_quo = $div->create("mb-3", $quo_quotation->json['html']??"");
			// *** related invoice ***
			$div_inv = $div->create("mb-3", $inv_invoice->json['html']??"");
			// *** related contact ***
			$div_con = $div->create("mb-3", $cli_contact->json['html']??"");
			// *** related login ***
			$div_log = $div->create("mb-3", $cli_login->json['html']??"");
			// *** related agency ***
			$div_age = $div->create("mb-3", $cli_agency->json['html']??"");
			
			// *** Button object ***
			$button = new button($this->table, $this->idrecord, $this->action);
			// info : button->create(type[, buttonClass])
			$btn_save 	= $button->create("save", "btn-mod");
			$btn_close = $button->create("close");
			$btn_delete = $button->create("delete", "btn-left btn-mod");
			
			// *** Form object ***
			$form = new form();
			// info : form->create("normal/upload", formId, formClass, elem1[, elem2[, elem3[, ...]]])
			$form1 = $form->create("normal", $this->table."_form", "", $div1);
			$form2 = $form->create("normal", $this->table."_form", "", $div2);
			$form_sit = $form->create("normal", "sit_site_form", "in-tabs", $div_sit);
			$form_inv = $form->create("normal", "inv_invoice_form", "in-tabs", $div_inv);
			$form_con = $form->create("normal", "cli_contact_form", "in-tabs", $div_con);
			$form_log = $form->create("normal", "cli_login_form", "in-tabs", $div_log);
			$form_quo = $form->create("normal", "quo_quotation_form", "in-tabs", $div_quo);
			$form_job = $form->create("normal", "job_job_form", "in-tabs", $div_job);
			$form_age = $form->create("normal", "cli_agency_form", "in-tabs", $div_age);
			
			// *** Tab object ***
			$tab = new tab(1);
			// info : tab->create(label, tabClass, form)
			$tabs = $tab->create("Coordonnées", "underline", $form1);
			$tabs = $tab->create("Adresse Facturation", "underline ml-2", $form2);
			$tabs = $sit_site == "" ? $tabs : $tab->create(ngettext("Site","Sites",$sit_site->count).'<span class="ml-2 badge badge-secondary">'.$sit_site->count.'</span>', "other underline ml-2", $form_sit);
			$tabs = $job_job == "" ? $tabs : $tab->create(ngettext("Chantier","Chantiers",$job_job->count).'<span class="ml-2 badge badge-secondary">'.$job_job->count.'</span>', "other underline ml-2", $form_job);
			$tabs = $quo_quotation == "" ? $tabs : $tab->create(ngettext("Devis","Devis",$quo_quotation->count).'<span class="ml-2 badge badge-secondary">'.$quo_quotation->count.'</span>', "other underline ml-2", $form_quo);
			$tabs = $inv_invoice == "" ? $tabs : $tab->create(ngettext("Facture","Factures",$inv_invoice->count).'<span class="ml-2 badge badge-secondary">'.$inv_invoice->count.'</span>', "other underline ml-2", $form_inv);
			$tabs = $cli_agency == "" ? $tabs : $tab->create(ngettext("Agence","Agences",$cli_agency->count).'<span class="ml-2 badge badge-secondary">'.$cli_agency->count.'</span>', "other underline ml-2", $form_age);
			$tabs = $cli_contact == "" ? $tabs : $tab->create(ngettext("Contact","Contacts",$cli_contact->count).'<span class="ml-2 badge badge-secondary">'.$cli_contact->count.'</span>', "other underline ml-2", $form_con);
			$tabs = $cli_login == "" ? $tabs : $tab->create(ngettext("Utilisateur","Utilisateurs",$cli_login->count).'<span class="ml-2 badge badge-secondary">'.$cli_login->count.'</span>', "other underline ml-2", $form_log);
			
			// *** Body variable ***
			// info : $body = $input[.$div][.$form][.tabs]
			$body = $tabs;
			
			// *** Title variable ***
			$title = $this->values->cli_name ?? gettext("Nouveau ".$this->label);
			// *** Page object ***
			// info : new page(type, id, label, pageClass, body, buttons)
			$page = new page("modal", $this->table."_modal", $this->picto." ".$title, "modal-xlg", $body, $btn_save.$btn_close.$btn_delete);
			$this->json['html'] = $page->create(array($this->values->insert_date??date("y-m-d"), $this->values->update_date??date("y-m-d")));
		}
		
		public function __destruct()
		{
		}
	}