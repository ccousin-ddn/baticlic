<?php
/**
*** Juillet 2019@SoluFile 
**/
	require_once (dirname(__FILE__)."/../models/m_cli_contact.php");
	
	class cli_contact_view extends cli_contact_model {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->columns = array(
				array("field"=>"con_name", "alter"=>"", "title"=>html("Contact"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				array("field"=>"con_email", "alter"=>"email", "title"=>html("Email"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				array("field"=>"age_name", "alter"=>"", "title"=>html("Agence"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				array("field"=>"typ_name", "alter"=>"", "title"=>html("Fonction"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "width"=>400),
				
			);
			$this->color_conds = array();
		}
		
		public function v_createFullTable()
		{
			// *** Table object ***
			// info : new table($this->table,level[,checkbox(true/false)][,action(true/false)])
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
			// info : new table($this->table,level[,checkbox(true/false)][,action(true/false)])
			$table = new table($this->table, $this->level, false, $actions);
			// *** create columns title ***
			$table->createTableHead($this->columns);
			$this->json['html'] = $table->createJsonTr($this->key, $this->values, $this->color_conds);
		}
		
		public function v_createLineTable($idparent)
		{
			// *** Table object ***
			// info : new table($this->table,level[,checkbox(true/false)][,action(true/false)])
			$table = new table($this->table, $this->level, false, array("action"=>true,"edit"=>true));
			// *** create columns title ***
			$thead = $table->createTableHead($this->columns,"thead-card");
			// *** create table lines ***
			$tbody = $table->createTableBody($this->key, $this->values, $this->color_conds, "");
			// *** create add line ***
			$tfoot = ""; //$table->createTableFoot(count($this->columns), $idparent);
			
			// *** create table ***
			$param = array("height"=>"auto", "show-columns"=>"false", "pagination"=>"false", "search"=>"false", "new-recordtable"=>"true", "id-parent"=>"$idparent", "classes"=>"table table-sm table-card", "striped"=>"false", "export-table"=>"false", "filter-table"=>"");
			
			$this->json['html'] = str_replace(array("\r\n\t", "\t"), '', $table->createTableLine($param, $thead, $tbody, $tfoot));
		}
		
		public function v_createCard($doc_document="")
		{
			// *** Input object ***
			$input = new input($this->values, $this->cryptfields);
			// info : input->create(type, label, name, mandatory(true/false)[, defaultValue[, divSize[, inputClass[, info(array)]]]])
			// info : defaultValue = $this->dataSent['newValue']??"" for newFrom
			$inputs = isset($this->idparent)?$input->create("hidden", "", $this->idparent, false, "", ""):"";
			$inputs .= $input->create("text", "Contact", "con_name", true, "", "col-md-4");
			$inputs .= $input->create("select", "Agence", "idcliagency", true, "", "col-md-4", "", array("idlist"=>"1","table"=>"cli_agency","search"=>"true","filtering"=>"true","filterChild"=>null));
			$inputs .= $input->create("select", "Fonction", "idcontype", false, "", "col-md-4", "", array("idlist"=>"1","table"=>"con_type","search"=>"true","filterChild"=>null,"none-result-text"=>"Créer la fonction"));
			$inputs .= $input->create("text", "Email", "con_email", false, "", "col-md-3");
			$inputs .= $input->create("text", "Téléphone", "con_phone", false, "", "col-md-3");
			$inputs .= $input->create("text", "Téléphone personnel", "con_phone_perso", false, "", "col-md-3");
			$inputs .= $input->create("text", "Mobile", "con_mobile", false, "", "col-md-3");
			$inputs .= $input->create("textarea", "Commentaire", "con_remark", false, "", "col-md-12", "", array("row"=>2));
			
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
			$form1 = $form->create("normal", $this->table."_form", "", $div1, $div_doc, $btn_pdf, $btn_mail);
			
			// *** Tab object ***
			// ex : $tab = new tab(1);
			// info : tab->create(label, tabClass, form)
			// ex : $tabs = $tab->create("Onglet1", "", $form1);
			// ex : $tabs = $tab->create("Onglet2", "", $form2);
			
			// *** Body variable ***
			// info : $body = $input[.$div][.$form][.tabs]
			$body = $form1;
			
			// *** Title variable ***
			$title = $this->values->con_name ?? gettext("Nouveau ".$this->label);
			// *** Page object ***
			// info : new page(type, id, label, pageClass/colspan, body, buttons)
			$page = new page("tr", "cli_contact_tr_".$this->idrecord, "", count($this->columns)+1, $body, $btn_save.$btn_cancel.$btn_delete);
			$this->json['html'] = $page->create(array($this->values->insert_date??date("y-m-d"), $this->values->update_date??date("y-m-d")));
		}
		
		public function __destruct()
		{
		}
	}