<?php
/**
*** Octobre 2021@SoluFile 
**/
	require_once (dirname(__FILE__)."/../models/m_tim_weekly.php");
	
	class tim_weekly_view extends tim_weekly_model {
		
		public function __construct()
		{
			parent::__construct();
			// sorter : sortDate / sortEuro
			$this->columns = array(
				array("field"=>"idworker", "alter"=>"", "title"=>html("Compagnon"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>""),
				array("field"=>"tim_week", "alter"=>"", "title"=>html("Semaine&Année"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>""),
				array("field"=>"tim_signature", "alter"=>"", "title"=>html("Signature"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>""),
				
			);
			$this->columns_pdf = array(
				//array("field"=>"", "alter"=>"", "title"=>html(""), "style"=>"", "class"=>""),
			);
			$this->color_conds = array(
				//array("column"=>"", "query"=>" == 0", "class"=>"td-warning")
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
			$param = array("height"=>"auto", "show-columns"=>"false", "pagination"=>"true", "search"=>true, "classes"=>"table table-no-bordered table-hover", "striped"=>"false", "new-record"=>"true", "export-table"=>"false", "filter-table"=>"");
			
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
		
		public function v_createCard($doc_document="")
		{
			// *** Input object ***
			$input = new input($this->values, $this->cryptfields);
			// info : input->create(type, label, name, mandatory(true/false)[, defaultValue[, divSize[, inputClass[, info(array)]]]]) "none-result-text"=>""
			// info : defaultValue = $this->dataSent['newValue']??"" for newFrom !!! model_crud c_newFrom case
			// info : $inputs .= $input->breakline;
			$inputs = isset($this->idparent)?$input->create("hidden", "", $this->idparent, false, "", ""):"";
			$inputs .= $input->create("select", gettext("Compagnon"), "idworker", false, "", "col-md-6", "", array("idlist"=>"1","table"=>"tableselect","search"=>"true","filterChild"=>null,"filtering"=>false));
			$inputs .= $input->create("select", gettext("Semaine&Année"), "tim_week", false, "", "col-md-6", "", array("idlist"=>"1","table"=>"tableselect","search"=>"true","filterChild"=>null,"filtering"=>false));
			$inputs .= $input->create("text", gettext("Signature"), "tim_signature", false, "", "col-md-6");
			
			// *** Div object ***
			$div = new div();
			// info : div->create(divClass, $inputs)
			$divs = $div->create("row", $inputs);
			
			// *** related documents ***
			$div_doc = $doc_document !="" ? $div->create("row", $doc_document) : "";
			
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
			$forms = $form->create("normal", $this->table."_form", "", $divs, $div_doc, $buttons_form);
			
			// *** Tab object ***
			// ex : $tab = new tab(1);
			// info : tab->create(label, tabClass, form)
			// ex : $tabs = $tab->create("Onglet1", "", $form1);
			// ex : $tabs = $tab->create("Onglet2", "", $form2);
			
			// *** Body variable ***
			// info : $body = $input[.$div][.$form][.tabs]
			$body = $forms;
			
			// *** Title variable ***
			if(decrypt($this->idrecord) > 0){
				$title = $this->values->wee_name;
			}else{
				$title = $this->newtext;
			}
			// *** Page object ***
			// info : new page(type, id, label, pageClass[or colspan], body, buttons)
			$page = new page("modal", $this->table."_modal", $this->picto." ".$title, "modal-dialog-scrollable modal-lg", $body, $btn_save.$btn_close.$btn_delete);
			// ex : $page = new page("tr", $this->table."_tr_".$this->idrecord, "", count($this->columns)+1, $body, $btn_save.$btn_cancel.$btn_delete);

			$this->json['html'] = str_replace(array("\r\n\t", "\t"), '', $page->create(array($this->values->insert_date??date("y-m-d"), $this->values->update_date??date("y-m-d"))));
		}
		
		public function v_createPdf($type)
		{
			//$type="view";
			$pdfDoc = new pdf($this->table);
			$pdfDoc->createLine($this->line);
			
			$pdf_name = gettext("")." ".$this->values->wee_name.'.pdf';
			
			$this->json['html'] = $pdfDoc->createPdf($this->values,$pdf_name,$type);
		}
		
		public function __destruct()
		{
		}
	}