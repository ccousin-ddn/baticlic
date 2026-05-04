<?php
/**
*** Juillet 2019@SoluFile 
**/
	require_once (dirname(__FILE__)."/../models/m_cli_login.php");
	
	class cli_login_view extends cli_login_model {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->columns = array(
				array("field"=>"log_name", "alter"=>"", "title"=>html("Contact"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				array("field"=>"log_login", "alter"=>"", "title"=>html("Login"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "width"=>300),
				array("field"=>"sit_name", "alter"=>"", "title"=>html("Site"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "width"=>200),
				array("field"=>"log_level", "alter"=>"", "title"=>html("Niveau"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "width"=>100),
				
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
			$param = array("height"=>"auto", "show-columns"=>"true", "pagination"=>"false", "search"=>true, "classes"=>"table table-no-bordered  table-hover table-light", "striped"=>"false", "new-record"=>"true", "export-table"=>"true", "filter-table"=>"");
			$this->json['html'] = $table->createTable($head, $param, $thead, $tbody);
		}
		
		public function v_createTr($actions)
		{
			// *** Table object ***
			// info : new table(inv_line,level[,checkbox(true/false)][,action(true/false)])
			$table = new table($this->table, $this->level, false, $actions);
			// *** create columns title ***
			$table->createTableHead($this->columns);
			$this->json['html'] = $table->createJsonTr($this->key, $this->values, $this->color_conds);
		}
		
		public function v_createLineTable($idparent)
		{
			// *** Table object ***
			// info : new table(inv_line,level[,checkbox(true/false)][,(array("action"=>true/false,"read"=>true/false,"edit"=>true/false,"delete"=>true/false))])
			$table = new table($this->table, $this->level, false, array("action"=>true,"edit"=>true));
			// *** create columns title ***
			$thead = $table->createTableHead($this->columns,"thead-card");
			// *** create table lines ***
			$tbody = $table->createTableBody($this->key, $this->values, $this->color_conds, "");
			// *** create add line ***
			$tfoot = ""; //$table->createTableFoot(count($this->columns), $idparent);
			
			// *** create table ***
			$param = array("height"=>"auto", "show-columns"=>"false", "pagination"=>"false", "search"=>"false", "new-recordtable"=>"true", "id-parent"=>"$idparent", "classes"=>"table table-sm table-card", "striped"=>"false", "export-table"=>"false", "filter-table"=>"");
			
			$this->json['html'] = $table->createTableLine($param, $thead, $tbody, $tfoot);
		}
		
		public function v_createCard($doc_document="")
		{
			// *** Input object ***
			$input = new input($this->values, $this->cryptfields);
			// info : input->create(type, label, name, mandatory(true/false)[, defaultValue[, divSize[, inputClass[, info(array)]]]])
			// info : defaultValue = $this->dataSent['newValue']??"" for newFrom
			$inputs = $input->create("hidden", "Client", "idclient", false, "", "");
			$inputs .= $input->create("text", "Contact", "log_name", false, "", "col-md-6");
			$inputs .= $input->create("select", "Site", "idsite", false, "", "col-md-6", "", array("idlist"=>"1","table"=>"sit_site","search"=>"true","filterChild"=>null));
			$inputs .= $input->create("text", "Login", "log_login", true, "", "col-md-6");
			$inputs .= $input->create("password", "Mot de passe", "log_password", true, "", "col-md-6");
			$inputs .= $input->create("radio", "Niveau", "log_level", false, "", "col-md-6", "no-line", array("contents"=>$this->log_level));
			$inputs .= $input->create("textarea", "Commentaire", "log_remark", false, "", "col-md-6", "", array("row"=>2));

			// *** Div object ***
			$div = new div();
			// info : div->create(divClass, elem1[, elem2[, elem3[, ...]]])
			$div1 = $div->create("row", $inputs);
			
			// *** related documents ***
			$div_doc = $doc_document !="" ? $div->create("row", $doc_document) : "";
			
			// *** Button object ***
			$button = new button($this->table, $this->idrecord, $this->action);
			// info : button->create(type[, buttonClass])
			$btn_save 	= $button->create("save");
			$btn_cancel = $button->create("cancel");
			$btn_delete = $button->create("delete", "btn-left");
			$btn_pdf	= "";
			// ex : $btn_pdf	= $button->create("pdf");
			$btn_mail	= "";
			// ex : $btn_mail 	= $button->create("mail");
			
			// *** Form object ***
			$form = new form();
			// info : form->create("normal/upload", formId, formClass, elem1[, elem2[, elem3[, ...]]])
			$form1 = $form->create("normal", $this->table."_form", "", $div1, $btn_pdf, $btn_mail);
			
			// *** Tab object ***
			// ex : $tab = new tab(1);
			// info : tab->create(label, tabClass, form)
			// ex : $tabs = $tab->create("Onglet1", "", $form1);
			// ex : $tabs = $tab->create("Onglet2", "", $form2);
			
			// *** Body variable ***
			// info : $body = $input[.$div][.$form][.tabs]
			$body = $form1;
			
			// *** Page object ***
			// info : new page(type, id, label, pageClass, body, buttons)
			$page = new page("tr", $this->table."_tr_".$this->idrecord, "", count($this->columns)+1, $body, $btn_save.$btn_cancel.$btn_delete);
			$this->json['html'] = $page->create(array($this->values->insert_date??date("y-m-d"), $this->values->update_date??date("y-m-d")));
		}
		
		public function __destruct()
		{
		}
	}