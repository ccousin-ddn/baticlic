<?php
/**
*** Juillet 2019@SoluFile 
**/
	require_once (dirname(__FILE__)."/../models/m_lib_library.php");
	
	class lib_library_view extends lib_library_model {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->columns = array(
				array("field"=>"lib_name", "alter"=>"", "title"=>html("Dénomination"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				array("field"=>"cli_name", "alter"=>"", "title"=>html("Client"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				array("field"=>"con_description", "alter"=>"", "title"=>html("Marché"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				array("field"=>"wor_tot", "alter"=>"", "title"=>html("Prestations"), "sortable"=>true, "searchable"=>false, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				
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
			$param = array("height"=>"auto", "show-columns"=>"false", "pagination"=>"false", "search"=>true, "classes"=>"table table-no-bordered  table-hover table-light", "striped"=>"false", "new-record"=>"true", "export-table"=>"false", "filter-table"=>"");
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
		
		public function v_createCard()
		{
			// *** Input object ***
			$input = new input($this->values, $this->cryptfields);
			// info : input->create(type, label, name, mandatory(true/false)[, defaultValue[, divSize[, inputClass[, info(array)]]]])
			// info : defaultValue = $this->dataSent['newValue']??"" for newFrom
			//$inputs =  $input->create("hidden", "", "idlibrary", false, "", "");
			$inputs = $input->create("select", "Client", "idclient", true, $this->dataSent['otherId']??"", "col-md-6", "", array("idlist"=>"1","table"=>"cli_client","search"=>"true","filterChild"=>"con_contract"));
			$inputs .= $input->create("select", "Marché", "idcontract", false, "", "col-md-6", "", array("idlist"=>"1","table"=>"con_contract","search"=>"true","filterChild"=>null,"filtering"=>true));
			$inputs .= $input->create("text", "Dénomination", "lib_name", true, $this->dataSent['newValue']??"", "col-md-6");
			$inputs .= $input->create("readonly", "Prestations", "wor_tot", false, "", "col-md-2");
			$inputs .= $input->create("textarea", "Commentaire", "lib_remark", false, "", "col-md-4", "", array("row"=>1));
			
			$inputs .=  $input->create("hidden", "", "lib_bpu_name", false, "", "");
			$inputs .= $input->create("uploadDrop", gettext("Fichier XLS"), "lib_bpu_file", false, "", "col-md-8", "", array("table"=>$this->table,"type"=>"doc","file"=>"excel","maxSize"=>"8","resize"=>"","realName"=>$this->values->lib_bpu_name));
			
			$btn_analyze_bpu = '
				<button type="button" id="btn_analyze_bpu" class="btn btn-outline-client-light my-4" '.(empty($this->values->lib_bpu_file)?"disabled ":"").'
				onclick="table_action({tablename:\''.$this->table.'\', idrecord:\''.$this->idrecord.'\', dataSent:{}, action:\'analyzeBPU\'})" 
				autocomplete="off" data-loading-text="<i class=\'fal fa-spinner fa-spin fa-fw mr-2\'></i>'.gettext("En cours...").'">
					<i class="fal fa-eye mr-2"></i>
					'.gettext("Analyser données XLS").'
				</button>
			';
			$btn_import_bpu = '
				<button type="button" id="btn_import_bpu" class="btn btn-outline-client-light my-3" disabled 
				onclick="table_action({tablename:\''.$this->table.'\', idrecord:\''.$this->idrecord.'\', dataSent:{}, action:\'importBPU\'})" 
				autocomplete="off" data-loading-text="<i class=\'fal fa-spinner fa-spin fa-fw mr-2\'></i>'.gettext("En cours...").'">
					<i class="fal fa-table mr-2"></i>
					'.gettext("Importer données XLS").'
				</button>
			';
			$inputs .= '
			<div class="col-md-4">
				<div class="d-flex flex-column justify-content-center h-100">
					'.$btn_analyze_bpu.'
					'.$btn_import_bpu.'
				</div>
			</div>
			';
			
			// *** Div object ***
			$div = new div();
			// info : div->create(divClass, elem1[, elem2[, elem3[, ...]]])
			$divs = $div->create("row", $inputs);
			$divs .= $div->create("my-3 table-bpu form-normal", '');
			
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
			$form1 = $form->create("normal", $this->table."_form", "", $divs);
			
			// *** Tab object ***
			// ex : $tab = new tab(1);
			// info : tab->create(label, tabClass, form)
			// ex : $tabs = $tab->create("Onglet1", "", $form1);
			// ex : $tabs = $tab->create("Onglet2", "", $form2);
			
			// *** Body variable ***
			// info : $body = $input[.$div][.$form][.tabs]
			$body = $form1;
			
			// *** Title variable ***
			$title = $this->values->lib_name ?? gettext("Nouvelle ".$this->label);
			// *** Page object ***
			// info : new page(type, id, label, pageClass, body, buttons)
			$page = new page("modal", $this->table."_modal", $this->picto." ".$title, "modal-lg", $body, $btn_save.$btn_close.$btn_delete);
			$this->json['html'] = $page->create(array($this->values->insert_date??date("y-m-d"), $this->values->update_date??date("y-m-d")));
		}
		
		public function __destruct()
		{
		}
	}