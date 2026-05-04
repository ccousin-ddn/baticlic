<?php
/**
*** Juillet 2019@SoluFile 
**/
	require_once (dirname(__FILE__)."/../models/m_lib_work.php");
	
	class lib_work_view extends lib_work_model {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->columns = array(
				array("field"=>"lib_name", "alter"=>"", "title"=>html("Bibliothèque"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				array("field"=>"cat_name", "alter"=>"", "title"=>html("Catégorie"), "sortable"=>true, "searchable"=>true, "class"=>"text-break", "halign"=>"left", "align"=>"left"),
				array("field"=>"subcat_name", "alter"=>"", "title"=>html("Sous-catégorie"), "sortable"=>true, "searchable"=>true, "class"=>"text-break", "halign"=>"left", "align"=>"left"),
				array("field"=>"wor_code", "alter"=>"", "title"=>html("Code"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				array("field"=>"wor_name", "alter"=>"", "title"=>html("Dénomination"), "sortable"=>true, "searchable"=>true, "class"=>"text-break", "halign"=>"left", "align"=>"left"),
				array("field"=>"wor_unit", "alter"=>"", "title"=>html("Unité"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				array("field"=>"wor_rate_medium", "alter"=>"", "title"=>html("Tarif"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				
			);
			$this->columns_small = array(
				array("field"=>"wor_code", "alter"=>"", "title"=>html("Code"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				array("field"=>"cat_name", "alter"=>"", "title"=>html("Catégorie"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				array("field"=>"subcat_name", "alter"=>"", "title"=>html("Sous-catégorie"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				array("field"=>"wor_name", "alter"=>"", "title"=>html("Dénomination"), "sortable"=>true, "searchable"=>true, "class"=>"text-break", "halign"=>"left", "align"=>"left"),
				array("field"=>"wor_description", "alter"=>"", "title"=>html("Description"), "sortable"=>true, "searchable"=>true, "class"=>"text-break", "halign"=>"left", "align"=>"left"),
				array("field"=>"wor_unit", "alter"=>"", "title"=>html("Unité"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				array("field"=>"wor_rate_medium", "alter"=>"euro", "title"=>html("Tarif"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				
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
			$param = array("height"=>"auto", "show-columns"=>"false", "pagination"=>"false", "search"=>true, "classes"=>"table table-no-bordered  table-hover table-light", "striped"=>"false", "new-record"=>"true", "export-table"=>"false", "filter-table"=>"lib_library,wor_category");
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
			$table = new table($this->table, $this->level, false, array("action"=>true,"edit"=>true,"delete"=>true));
			// *** create columns title ***
			$thead = $table->createTableHead($this->columns_small,"thead-card");
			// *** create table lines ***
			$tbody = $table->createTableBody($this->key, $this->values, $this->color_conds, "");
			// *** create add line ***
			$tfoot = ""; //$table->createTableFoot(count($this->columns), $idparent);
			
			// *** create table ***
			$param = array("height"=>"auto", "show-columns"=>"false", "pagination"=>"false", "search"=>"true", "new-recordtable"=>"true", "id-parent"=>"$idparent", "classes"=>"table table-sm table-card", "striped"=>"false", "export-table"=>"false", "filter-table"=>"");
			
			$this->json['html'] = $table->createTableLine($param, $thead, $tbody, $tfoot);
		}
		
		public function v_createCard($doc_document,$wor_article)
		{
			// *** Input object ***
			$input = new input($this->values, $this->cryptfields);
			// info : input->create(type, label, name, mandatory(true/false)[, defaultValue[, divSize[, inputClass[, info(array)]]]])
			// info : defaultValue = $this->dataSent['newValue']??"" for newFrom
			$inputs = $input->create("select", "Bibliothèque", "idlibrary", true, "", "col-md-3", "", array("idlist"=>"1","table"=>"lib_library","search"=>"true","filterChild"=>null,"none-result-text"=>"Créer une bibliothèque"));
			$inputs .= $input->create("select", "Catégorie", "idworcategory", true, "", "col-md-3", "", array("idlist"=>"1","table"=>"wor_category","search"=>"true","filterChild"=>"wor_subcategory","filtering"=>true,"none-result-text"=>"Créer la catégorie"));
			$inputs .= $input->create("select", "Sous-catégorie", "idworsubcategory", false, "", "col-md-3", "", array("idlist"=>"1","table"=>"wor_subcategory","search"=>"true","filtering"=>true,"none-result-text"=>"Créer la sous-catégorie"));
			$inputs .= $input->create("select", "Métier", "idmetier", false, "", "col-md-3", "", array("idlist"=>"1","table"=>"met_metier","prefix"=>"task"));
			$inputs .= $input->create("text", "Code", "wor_code", false, "", "col-md-2");
			$inputs .= $input->create("text", "Dénomination", "wor_name", true, $this->dataSent['newValue']??"", "col-md-4");
			$inputs .= $input->create("textarea", "Description", "wor_description", false, "", "col-md-6", "", array("row"=>2));
			$inputs .= $input->create("radio", "Garantie décennale", "wor_guarantee_10", false, "", "col-md-2", "no-line", array("contents"=>$this->wor_guarantee_10));
			$inputs .= $input->create("text", "Unité", "wor_unit", false, "", "col-md-2");
			$inputs .= $input->create("text", "Main d'oeuvre (minutes)", "wor_duration", false, "", "col-md-2");
			//$inputs .= $input->create("text", "Tarif small", "wor_rate_small", false, "", "col-md-2");
			$inputs .= $input->create("text", "Tarif", "wor_rate_medium", false, "", "col-md-2");
			//$inputs .= $input->create("text", "Tarif large", "wor_rate_large", false, "", "col-md-2");
			//$inputs .= $input->breakline;
			//$inputs .= $input->create("textarea", "Commentaire", "wor_remark", false, "", "col-md-12", "", array("row"=>1));
			
			// *** Div object ***
			$div = new div();
			// info : div->create(divClass, elem1[, elem2[, elem3[, ...]]])
			$div1 = $div->create("row", $inputs);
			// *** wor_article ***
			$div_wor_article = $div->create("mb-4 mt-3", $wor_article->json['html']);
			
			// *** Button object ***
			$button = new button($this->table, $this->idrecord, $this->action);
			// info : button->create(type[, buttonClass])
			$btn_save 	= $button->create("save");
			$btn_close = $button->create("close");
			$btn_delete = $button->create("delete", "btn-left");
			$btn_pdf	= "";
			// ex : $btn_pdf	= $button->create("pdf");
			$btn_mail	= "";
			// ex : $btn_mail 	= $button->create("mail");
			
			// *** Form object ***
			$form = new form();
			// info : form->create("normal/upload", formId, formClass, elem1[, elem2[, elem3[, ...]]])
			$form1 = $form->create("normal", $this->table."_form", "", $div1, $div_wor_article, $btn_pdf, $btn_mail);
			
			// *** Tab object ***
			// ex : $tab = new tab(1);
			// info : tab->create(label, tabClass, form)
			// ex : $tabs = $tab->create("Onglet1", "", $form1);
			// ex : $tabs = $tab->create("Onglet2", "", $form2);
			
			// *** Body variable ***
			// info : $body = $input[.$div][.$form][.tabs]
			$body = $form1;
			
			// *** Title variable ***
			$title = $this->values->wor_name ?? gettext("Nouvelle ".$this->label);
			// *** Page object ***
			// info : new page(type, id, label, pageClass, body, buttons)
			$page = new page("modal", $this->table."_modal", $this->picto." ".$title, "modal-xlg", $body, $btn_save.$btn_close.$btn_delete);
			$this->json['html'] = $page->create(array($this->values->insert_date??date("y-m-d"), $this->values->update_date??date("y-m-d")));
		}
		
		public function v_createCardTable()
		{
			// *** Input object ***
			$input = new input($this->values, $this->cryptfields);
			// info : input->create(type, label, name, mandatory(true/false)[, defaultValue[, divSize[, inputClass[, info(array)]]]])
			// info : defaultValue = $this->dataSent['newValue']??"" for newFrom
			$inputs = $input->create("hidden", "", "idwork", false, "", "");
			$inputs .= $input->create("text", "Code", "wor_code", false, "", "col-md-2");
			$inputs .= $input->create("select", "Catégorie", "idworcategory", true, "", "col-md-3", "", array("idlist"=>"1","table"=>"wor_category","search"=>"true","filterChild"=>"wor_subcategory","filtering"=>true,"none-result-text"=>"Créer la catégorie"));
			$inputs .= $input->create("select", "Sous-catégorie", "idworsubcategory", false, "", "col-md-3", "", array("idlist"=>"1","table"=>"wor_subcategory","search"=>"true","filtering"=>true,"none-result-text"=>"Créer la sous-catégorie"));
			$inputs .= $input->create("text", "Dénomination", "wor_name", true, $this->dataSent['newValue']??"", "col-md-4");
			$inputs .= $input->create("textarea", "Description", "wor_description", false, "", "col-md-12", "", array("row"=>1));
			$inputs .= $input->create("text", "Unité", "wor_unit", false, "", "col-md-2");
			$inputs .= $input->create("radio", "Métier", "idmetier", false, "", "col-md-10", "", array("idlist"=>"1","table"=>"met_metier","prefix"=>"task"));
			//$inputs .= $input->breakline;
			$inputs .= $input->create("text", "Tarif small", "wor_rate_small", false, "", "col-md-3");
			$inputs .= $input->create("text", "Tarif medium", "wor_rate_medium", false, "", "col-md-3");
			$inputs .= $input->create("text", "Tarif large", "wor_rate_large", false, "", "col-md-3");
			
			// *** Div object ***
			$div = new div();
			// info : div->create(divClass, elem1[, elem2[, elem3[, ...]]])
			$div1 = $div->create("row", $inputs);
			
			// *** Button object ***
			$button = new button($this->table, $this->idrecord, $this->action);
			// info : button->create(type[, buttonClass])
			$btn_save 	= $button->create("save");
			$btn_cancel = $button->create("cancel");
			$btn_delete = $button->create("delete", "btn-left");
			
			// *** Form object ***
			$form = new form();
			// info : form->create("normal/upload", formId, formClass, elem1[, elem2[, elem3[, ...]]])
			$form1 = $form->create("normal", $this->table."_form", "", $div1);
			
			// *** Tab object ***
			// ex : $tab = new tab(1);
			// info : tab->create(label, tabClass, form)
			// ex : $tabs = $tab->create("Onglet1", "", $form1);
			// ex : $tabs = $tab->create("Onglet2", "", $form2);
			
			// *** Body variable ***
			// info : $body = $input[.$div][.$form][.tabs]
			$body = $form1;
			
			// *** Title variable ***
			$title = $this->values->wor_name ?? gettext("Nouvelle ".$this->label);
			// *** Page object ***
			// info : new page(type, id, label, pageClass, body, buttons)
			$page = new page("tr", $this->table."_tr_".$this->idrecord, "", count($this->columns)+1, $body, $btn_save.$btn_cancel.$btn_delete);
			$this->json['html'] = $page->create(array($this->values->insert_date??date("y-m-d"), $this->values->update_date??date("y-m-d")));
		}
		
		public function __destruct()
		{
		}
	}