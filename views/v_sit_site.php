<?php
/**
*** Juillet 2019@SoluFile 
**/
	require_once (dirname(__FILE__)."/../models/m_sit_site.php");
	
	class sit_site_view extends sit_site_model {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->columns = array(
				array("field"=>"cli_name", "alter"=>"", "title"=>html("Client"), "sortable"=>true, "searchable"=>true, "class"=>"text-break", "halign"=>"left", "align"=>"left"),
				array("field"=>"sit_name", "alter"=>"", "title"=>html("Site"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				array("field"=>"sit_address_1", "alter"=>"", "title"=>html("Adresse"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				array("field"=>"cit_name", "alter"=>"", "title"=>html("Commune"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				//array("field"=>"sit_pc", "alter"=>"", "title"=>html("Code postal"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				//array("field"=>"sit_city", "alter"=>"", "title"=>html("Ville"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				array("field"=>"are_name", "alter"=>"", "title"=>html("Zone"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
			);
			$this->columns_small = array(
				array("field"=>"sit_name", "alter"=>"", "title"=>html("Site"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				//array("field"=>"typ_name", "alter"=>"", "title"=>html("Type"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				array("field"=>"sit_address_1", "alter"=>"", "title"=>html("Adresse"), "sortable"=>true, "searchable"=>true, "class"=>"text-break", "halign"=>"left", "align"=>"left"),
				array("field"=>"sit_pc", "alter"=>"", "title"=>html("Code postal"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				array("field"=>"sit_city", "alter"=>"", "title"=>html("Ville"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
			);
			$this->color_conds = array();
		}
		
		public function v_createFullTable()
		{
			// *** Table object ***
			// info : new table($this->table, level[,checkbox(true/false)][,action(true/false)])
			$table = new table($this->table, $this->level, false);
			// *** create header ***
			$head = $table->createHead($this->picto, ngettext($this->label, $this->labels, $this->count), $this->count);
			// *** create columns title ***
			$thead = $table->createTableHead($this->columns);
			// *** create table lines ***
			$tbody = $table->createTableBody($this->key, $this->values, $this->color_conds);
			// *** create table ***
			$param = array("height"=>"auto", "show-columns"=>"false", "pagination"=>"false", "search"=>true, "classes"=>"table table-no-bordered  table-hover table-light", "striped"=>"false", "new-record"=>"true", "export-table"=>"false", "filter-table"=>"cli_client");
			$this->json['html'] = $table->createTable($head, $param, $thead, $tbody);
		}
		
		public function v_createTr()
		{
			// *** Table object ***
			// info : new table($this->table, level[,checkbox(true/false)][,action(true/false)])
			$table = new table($this->table, $this->level, false);
			// *** create columns title ***
			$table->createTableHead($this->columns);
			$this->json['html'] = $table->createJsonTr($this->key, $this->values, $this->color_conds);
		}
		
		public function v_createLineTable($idparent)
		{
			// *** Table object ***
			// info : new table($this->table, level[,checkbox(true/false)][,(array("action"=>true/false,"read"=>true/false,"edit"=>true/false,"delete"=>true/false))])
			$table = new table($this->table, $this->level, false, array("action"=>true,"read"=>true));
			// *** create columns title ***
			$thead = $table->createTableHead($this->columns_small, "thead-card");
			// *** create table lines ***
			$tbody = $table->createTableBody($this->key, $this->values, $this->color_conds);
			// *** create add line ***
			$tfoot = ""; //$table->createTableFoot(count($this->columns), $idparent);
			
			// *** create table ***
			$param = array("height"=>"auto", "show-columns"=>"false", "pagination"=>"false", "search"=>"false", "new-recordtable"=>"false", "id-parent"=>"$idparent", "classes"=>"table table-sm", "striped"=>"false", "export-table"=>"false", "filter-table"=>"");
			$this->json['html'] = $table->createTableLine($param, $thead, $tbody, $tfoot);
		}
		
		public function v_createCard($doc_document)
		{
			// *** Input object ***
			$input = new input($this->values, $this->cryptfields);
			// info : input->create(type, label, name, mandatory(true/false)[, defaultValue[, divSize[, inputClass[, info(array)]]]])
			// info : defaultValue = $this->dataSent['newValue']??"" for newFrom
			$inputs = $input->create("select", "Client", "idclient", true, $this->dataSent['otherId']??"", "col-md-6", "", array("idlist"=>"1","table"=>"cli_client","search"=>"true","filterChild"=>null));
			$inputs .= $input->create("text", "Site", "sit_name", true, $this->dataSent['newValue']??"", "col-md-6");
			$inputs .= $input->create("text", "Adresse", "sit_address_1", true, "", "col-md-6");
			$inputs .= $input->create("select", gettext("Commune"), "sit_idcity", false, "", "col-md-6", "", array("idlist"=>"1","table"=>"cit_city","search"=>"true","searchDB"=>"true","none-result-text"=>"Créer la commune"));
			//$inputs .= $input->create("text", "Code postal", "sit_pc", false, "", "col-md-2");
			//$inputs .= $input->create("text", "Ville", "sit_city", false, "", "col-md-4");
			$inputs .= $input->create("text", "Adresse (complément)", "sit_address_2", false, "", "col-md-6");
			$inputs .= $input->create("select", "Zone", "idarea", false, "", "col-md-2", "", array("idlist"=>"1","table"=>"are_area","search"=>"true","filterChild"=>null,"none-result-text"=>"Créer une zone"));
			$inputs .= $input->create("text", gettext("Latitude"), "sit_lat", false, "", "col-md-2");
			$inputs .= $input->create("text", gettext("Longitude"), "sit_lng", false, "", "col-md-2");
			$inputs .= $input->create("textarea", "Commentaire", "sit_remark", false, "", "col-md-12", "", array("row"=>2));
			
			// *** Div object ***
			$div = new div();
			// info : div->create(divClass, elem1[, elem2[, elem3[, ...]]])
			$div1 = $div->create("row", $inputs);
			
			// *** Button object ***
			$button = new button($this->table, $this->idrecord, $this->action);
			// info : button->create(type[, buttonClass])
			$btn_save 	= $button->create("save","btn-mod");
			$btn_close = $button->create("close");
			$btn_delete = $button->create("delete", "btn-left btn-mod");
			$btn_pdf	= "";
			// ex : $btn_pdf	= $button->create("pdf");
			$btn_mail	= "";
			// ex : $btn_mail 	= $button->create("mail");
			
			// *** Form object ***
			$form = new form();
			// info : form->create("normal/upload", formId, formClass, elem1[, elem2[, elem3[, ...]]])
			$form1 = $form->create("normal", $this->table."_form", "", $div1, $btn_pdf, $btn_mail);
			
			// *** Tab object ***
			$tab = new tab(1);
			// info : tab->create(label, tabClass, form)
			$tabs = $tab->create("Coordonnées", "underline", $form1);
			
			// *** Body variable ***
			// info : $body = $input[.$div][.$form][.tabs]
			$body = $tabs;
			
			// *** Title variable ***
			if(decrypt($this->idrecord) > 0){
				$title = $this->values->sit_name.' - '.$this->values->cli_name;
			}else{
				$title = gettext("Nouveau ".$this->label);
			}
			// *** Page object ***
			// info : new page(type, id, label, pageClass, body, buttons)
			$page = new page("modal", $this->table."_modal", $this->picto." ".$title, "modal-lg", $body, $btn_save.$btn_close.$btn_delete);
			$this->json['html'] = $page->create(array($this->values->insert_date??date("y-m-d"), $this->values->update_date??date("y-m-d")));
		}

		public function __destruct()
		{
		}
	}