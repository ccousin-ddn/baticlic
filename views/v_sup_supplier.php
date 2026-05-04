<?php
/**
*** Juillet 2019@SoluFile 
**/
	require_once (dirname(__FILE__)."/../models/m_sup_supplier.php");
	
	class sup_supplier_view extends sup_supplier_model {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->columns = array(
				array("field"=>"sup_name", "alter"=>"", "title"=>html("Dénomination"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				array("field"=>"sup_short_name", "alter"=>"", "title"=>html("Dénomination courte"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				array("field"=>"sup_contact", "alter"=>"", "title"=>html("Contact"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				array("field"=>"sup_email", "alter"=>"email", "title"=>html("E-mail"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				array("field"=>"sup_phone", "alter"=>"phone", "title"=>html("Mobile"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				array("field"=>"sup_skills", "alter"=>"", "title"=>html("Compétences"), "sortable"=>true, "searchable"=>true, "class"=>"", "halign"=>"left", "align"=>"left"),
				array("field"=>"sup_url", "alter"=>"url", "title"=>html("Site Web"), "sortable"=>true, "searchable"=>true, "class"=>"text-break", "halign"=>"left", "align"=>"left"),
				
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
			$param = array("height"=>"auto", "show-columns"=>"false", "pagination"=>"false", "search"=>true, "classes"=>"table table-no-bordered  table-hover table-light", "striped"=>"false", "new-record"=>"true", "export-table"=>"false", "filter-table"=>"met_metier");
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
		
		public function v_createCard($doc_document,$sup_article=array(),$sup_agency=array())
		{
			// *** Input object ***
			$input = new input($this->values, $this->cryptfields);
			// info : input->create(type, label, name, mandatory(true/false)[, defaultValue[, divSize[, inputClass[, info(array)]]]])
			// info : defaultValue = $this->dataSent['newValue']??"" for newFrom
			$inputs = $input->create("text", "Dénomination", "sup_name", true, $this->dataSent['newValue']??"", "col-md-4");
			$inputs .= $input->create("text", "Dénomination courte", "sup_short_name", false, "", "col-md-2");
			$inputs .= $input->create("text", "Contact", "sup_contact", false, "", "col-md-2");
			$inputs .= $input->create("select", "Type échéance", "sup_due_type", false, "", "col-md-2", "", array("contents"=>$this->sup_due_type));
			$inputs .= $input->create("text", "Durée échéance", "sup_due_days", false, "", "col-md-2");
			$inputs .= $input->breakline;
			$inputs .= $input->create("text", "Adresse", "sup_address_1", false, "", "col-md-4");
			$inputs .= $input->create("text", "Adresse (complément)", "sup_address_2", false, "", "col-md-4");
			$inputs .= $input->create("select", gettext("Commune"), "sup_idcity", false, "", "col-md-3", "", array("idlist"=>"1","table"=>"cit_city","search"=>"true","searchDB"=>"true","none-result-text"=>"Créer la commune"));
			$inputs .= $input->breakline;
			$inputs .= $input->create("text", "Téléphone", "sup_phone", false, "", "col-md-2");
			$inputs .= $input->create("text", "E-mail", "sup_email", false, "", "col-md-3");
			$inputs .= $input->create("text", "N° SIRET", "sup_siret", false, "", "col-md-2");
			$inputs .= $input->create("text", "N° TVA", "sup_tva", false, "", "col-md-2");
			$inputs .= $input->create("text", "Site Web", "sup_url", false, "", "col-md-3");
			//$inputs .= $input->create("text", "Mobile", "sup_mobile", false, "", "col-md-3");
			$inputs .= $input->create("checkbox", "Métiers", "idmetier", false, "", "col-md-12", "", array("idlist"=>"1","table"=>"met_metier"));
			//$inputs .= $input->create("text", "Login", "sup_login", false, "", "col-md-3");
			//$inputs .= $input->create("password", "Mot de passe", "sup_password", false, "", "col-md-3");
			
			$inputs .= $input->create("text", "Compétences", "sup_skills", false, "", "col-md-4");
			$inputs .= $input->create("textarea", "Commentaire", "sup_remark", false, "", "col-md-8", "", array("row"=>1));
			
			$inputsXLS =  $input->create("hidden", "", "idsupplier", false, "", "");
			$inputsXLS .= $input->create("hidden", gettext("Tarifs"), "con_bpu_name", false, "", "");
			$inputsXLS .= $input->create("uploadDrop", gettext("Fichier XLS"), "sup_rate_file", false, "", "col-md-6", "", array("table"=>$this->table,"type"=>"doc","file"=>"excel","maxSize"=>"8","resize"=>"","realName"=>$this->values->sup_rate_file));
			
			$btn_analyze_xls = '
				<button type="button" id="btn_analyze_xls" class="btn btn-outline-client-light my-4" '.(empty($this->values->sup_rate_file)?"disabled ":"").'
				onclick="table_action({tablename:\''.$this->table.'\', idrecord:\''.$this->idrecord.'\', dataSent:{}, action:\'sup_analyzeXLS\'})" 
				autocomplete="off" data-loading-text="<i class=\'fal fa-spinner fa-spin fa-fw mr-2\'></i>'.gettext("En cours...").'">
					<i class="fal fa-eye mr-2"></i>
					'.gettext("Analyser données XLS").'
				</button>
			';
			$btn_import_xls = '
				<button type="button" id="btn_import_xls" class="btn btn-outline-client-light my-3" disabled 
				onclick="table_action({tablename:\''.$this->table.'\', idrecord:\''.$this->idrecord.'\', dataSent:{}, action:\'sup_importXLS\'})" 
				autocomplete="off" data-loading-text="<i class=\'fal fa-spinner fa-spin fa-fw mr-2\'></i>'.gettext("En cours...").'">
					<i class="fal fa-table mr-2"></i>
					'.gettext("Importer données XLS").'
				</button>
			';
			$inputsXLS .= '
			<div class="col-md-4">
				<div class="d-flex flex-column justify-content-center h-100">
					'.$btn_analyze_xls.'
					'.$btn_import_xls.'
				</div>
			</div>
			';

			// *** Div object ***
			$div = new div();
			// info : div->create(divClass, elem1[, elem2[, elem3[, ...]]])
			$div1 = $div->create("row", $inputs);
			
			// *** related article ***
			$div_art = $div->create("row mt-4", $inputsXLS);
			$div_art .= $div->create("my-3 table-xls form-normal", '');
			$div_art .= $div->create("mb-3 table-result", $sup_article->json['html']??"");
			
			// *** Button object ***
			$button = new button($this->table, $this->idrecord, $this->action);
			// info : button->create(type[, buttonClass])
			$btn_save 	= $button->create("save", "btn-mod");
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
			$form2 = $form->create("normal", "sup_article_form", "in-tabs", $div_art);
			$form3 = $form->create("normal", "sup_agency_form", "in-tabs", $sup_agency->json['html']);
			$formDoc = $form->create("dropzone", $this->table."_form", "", $doc_document->json['html']);
			
			// *** Tab object ***
			$tab = new tab(1);
			$tabs = $tab->create("Coordonnées", "underline", $form1);
			$tabs = $tab->create(ngettext("Tarif","Tarifs",$sup_article->count??0).'<span class="ml-2 badge badge-secondary">'.($sup_article->count??0).'</span>', "other underline ml-2", $form2);
			$tabs = $tab->create(ngettext("Agence","Agences",$sup_agency->count??0).'<span class="ml-2 badge badge-secondary">'.($sup_agency->count??0).'</span>', "other underline ml-2", $form3);
			$tabs = $tab->create(ngettext("Document","Documents",$doc_document->count).'<span class="ml-2 badge badge-secondary">'.$doc_document->count.'</span>', "other underline ml-2 nav-document", $formDoc);
			
			
			// *** Body variable ***
			// info : $body = $input[.$div][.$form][.tabs]
			$body = $tabs;
			
			// *** Title variable ***
			$title = $this->values->sup_name ?? gettext("Nouveau ".$this->label);
			// *** Page object ***
			// info : new page(type, id, label, pageClass, body, buttons)
			$page = new page("modal", $this->table."_modal", $this->picto." ".$title, "modal-xl", $body, $btn_save.$btn_close.$btn_delete);
			$this->json['html'] = $page->create(array($this->values->insert_date??date("y-m-d"), $this->values->update_date??date("y-m-d")));
		}
		
		public function __destruct()
		{
		}
	}