<?php
/**
*** Novembre 2021@SoluFile 
**/
	require_once (dirname(__FILE__)."/../models/m_wor_medical.php");
	
	class wor_medical_view extends wor_medical_model {
		
		public function __construct()
		{
			parent::__construct();
			// sorter : sortDate / sortEuro
			$this->columns = array(
				array("field"=>"wor_name", "alter"=>"", "title"=>html("Compagnon"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>"", "card-visible"=>"true"),
				array("field"=>"med_date", "alter"=>"date-be", "title"=>html("Date"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>"sortDate", "card-visible"=>"true"),
				array("field"=>"med_next_date", "alter"=>"date-be", "title"=>html("Date prochaine visite"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>"sortDate", "card-visible"=>"true"),
				
			);
			$this->columns_pdf = array(
				//array("field"=>"", "alter"=>"", "title"=>html(""), "style"=>"", "class"=>""),
			);
			$this->color_conds = array(
				array("column"=>"unix_next", "query"=>"<=".strtotime(date('Y-m-d')), "class"=>"td-danger"),
				array("column"=>"unix_next", "query"=>"<=".strtotime(changeDate('+1M')), "class"=>"td-warning"),
				array("column"=>"unix_next", "query"=>">".strtotime(changeDate('+1M')), "class"=>"td-success"),
			);
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
			$param = array("height"=>"auto", "show-columns"=>"false", "pagination"=>"true", "search"=>true, "classes"=>"table table-no-bordered table-hover table-light", "striped"=>"false", "new-record"=>"true", "export-table"=>"false", "filter-table"=>"");
			
			$this->json['html'] = str_replace(array("\r\n\t", "\t"), '', $table->createTable($head, $param, $thead, $tbody));
		}
		
		public function v_createTr($actions=array())
		{
			// *** Table object ***
			// info : new table($this->table,level[,checkbox(true/false)][,action(true/false)])
			$table = new table($this->table, $this->level, false, $actions);
			// *** create columns title ***
			$table->createTableHead($this->columns);
			
			$this->json['html'] = str_replace(array("\r\n\t", "\t"), '', $table->createJsonTr($this->key, $this->values, $this->color_conds));
			$this->json['tdClass'] = $table->info;
		}
		
		public function v_createLineTable($idparent)
		{
			// *** Table object ***
			// info : new table(inv_line,level[,checkbox(true/false)][,(array("action"=>true/false,"read"=>true/false,"edit"=>true/false,"delete"=>true/false))])
			$table = new table($this->table, $this->level, false, array("action"=>true,"read"=>false,"edit"=>true,"delete"=>true));
			// *** create columns title ***
			$thead = $table->createTableHead($this->columns,"thead-card");
			// *** create table lines ***
			$tbody = $table->createTableBody($this->key, $this->values, $this->color_conds);
			// *** create add line ***
			$tfoot = ""; //$table->createTableFoot(count($this->columns), $idparent);
			
			// *** create table ***
			$param = array("height"=>"auto", "show-columns"=>"false", "pagination"=>"false", "search"=>"false", "new-recordtable"=>"true", "id-parent"=>"$idparent", "classes"=>"table table-sm table-card", "striped"=>"false", "export-table"=>"false", "filter-table"=>"");
			
			$this->json['html'] = str_replace(array("\r\n\t", "\t"), '', $table->createTableLine($param, $thead, $tbody, $tfoot));
		}
		
		public function v_createCard($doc_document)
		{
			// *** Input object ***
			$input = new input($this->values, $this->cryptfields);
			// info : input->create(type, label, name, mandatory(true/false)[, defaultValue[, divSize[, inputClass[, info(array)]]]]) "none-result-text"=>""
			// info : defaultValue = $this->dataSent['newValue']??"" for newFrom !!! model_crud c_newFrom case
			// info : $inputs .= $input->breakline;
			$inputs = isset($this->idparent)?$input->create("hidden", "", $this->idparent, false, "", ""):"";
			$inputs .= $input->create("select", gettext("Compagnon"), "idworker", true, "", "col-md-6", "", array("idlist"=>"1","table"=>"wor_worker","search"=>"true","filterChild"=>null,"filtering"=>false));
			$inputs .= $input->create("date", gettext("Date"), "med_date", false, "", "col-md-3");
			$inputs .= $input->create("date", gettext("Date prochaine visite"), "med_next_date", false, "", "col-md-3");
			$inputs .= $input->create("textarea", gettext("Commentaire"), "med_remark", false, "", "col-md-12", "", array("row"=>2));
			
			// *** Div object ***
			$div = new div();
			// info : div->create(divClass, $inputs)
			$divs = $div->create("row", $inputs);
			
			// *** Button object ***
			$button = new button($this->table, $this->idrecord, $this->action);
			// info : button->create(type[, buttonClass])
			$btn_save 	= $button->create("save", "btn-mod");
			$btn_close 	= $button->create("close");
			//$btn_cancel = $button->create("cancel");
			$btn_delete = $button->create("delete", "btn-left btn-mod");
			$btn_pdf	= "";
			// ex : $btn_pdf	= $button->create("pdf");
			$btn_mail	= "";
			// ex : $btn_mail 	= $button->create("mail");
			
			$buttons_form = '
			<div class="d-flex flex-row justify-content-start">
				<div class="">'.$btn_pdf.'</div>
				<div class="ml-3">'.$btn_mail.'</div>
			</div>
			';
			
			// *** Form object ***
			$form = new form();
			// info : form->create("normal/upload", formId, formClass, elem1[, elem2[, elem3[, ...]]])
			$forms = $form->create("normal", $this->table."_form", "", $divs, $buttons_form);
			$formDoc = $form->create("dropzone", $this->table."_form", "", $doc_document->json['html']);
			
			// *** Tab object ***
			$tab = new tab(1);
			$tabs = $tab->create("Visite", "underline", $forms);
			$tabs = $tab->create(ngettext("Document","Documents",$doc_document->count).'<span class="ml-2 badge badge-secondary">'.$doc_document->count.'</span>', "other underline ml-2 nav-document", $formDoc);
			
			// *** Body variable ***
			// info : $body = $input[.$div][.$form][.tabs]
			$body = $tabs;
			
			// *** Title variable ***
			if(decrypt($this->idrecord) > 0){
				$title = $this->values->wor_name??$this->newtext;
			}else{
				$title = $this->newtext;
			}
			// *** Page object ***
			// info : new page(type, id, label, pageClass[or colspan], body, buttons)
			$page = new page("modal", $this->table."_modal", $this->picto." ".$title, "modal-lg noscroll", $body, $btn_save.$btn_close.$btn_delete);
			// ex : $page = new page("tr", $this->table."_tr_".$this->idrecord, "", count($this->columns)+1, $body, $btn_save.$btn_cancel.$btn_delete);

			$this->json['html'] = $page->create(array($this->values->insert_date??date("y-m-d"), $this->values->update_date??date("y-m-d")));
		}
		
		public function v_createToast()
		{
			$toast = new toast();
			foreach($this->values as $control){
				$toast->fa = "fa-user-md";
				$toast->title = $control['wor_name'];
				$toast->time = alterData("date-be",$control['med_next_date']);
				$toast->body = $this->label;
				$this->json['html'] .= $toast->create();
			}
		}
		
		public function __destruct()
		{
		}
	}