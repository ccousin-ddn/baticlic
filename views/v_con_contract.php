<?php
/**
*** 12-2023@SOLUfile SRL 
**/
	require_once (dirname(__FILE__)."/../models/m_con_contract.php");
	
	class con_contract_view extends con_contract_model {
		
		public function __construct()
		{
			parent::__construct();
			// sorter : sortDate / sortEuro
			$this->columns = array(
				array("field"=>"con_reference", "alter"=>"", "title"=>html("Référence"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>"", "card-visible"=>"true"),
				array("field"=>"cli_name", "alter"=>"", "title"=>html("Client"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>"", "card-visible"=>"true"),
				array("field"=>"con_description", "alter"=>"", "title"=>html("Description"), "sortable"=>true, "searchable"=>true, "class"=>"text-break", "halign"=>"left", "align"=>"left", "sorter"=>"", "card-visible"=>"true"),
				array("field"=>"con_date", "alter"=>"date-be", "title"=>html("Date"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>"sortDate", "card-visible"=>"true"),
				array("field"=>"usr_firstname", "alter"=>"", "title"=>html("Responsable"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>"", "card-visible"=>"true"),
				array("field"=>"con_date_end", "alter"=>"date-be", "title"=>html("Date fin"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>"sortDate", "card-visible"=>"true"),
				
			);
			$this->columns_edit = array(
				array("field"=>"idclient", "alter"=>"", "title"=>html("Client"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>"", "card-visible"=>"true"),
				array("field"=>"iduser", "alter"=>"", "title"=>html("Responsable"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>"", "card-visible"=>"true"),
				array("field"=>"idmetier", "alter"=>"", "title"=>html("Métiers"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>"", "card-visible"=>"true"),
				array("field"=>"con_status", "alter"=>"", "title"=>html("Statut"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>"", "card-visible"=>"true"),
				array("field"=>"con_date", "alter"=>"date-be", "title"=>html("Date"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>"sortDate", "card-visible"=>"true"),
				array("field"=>"con_duration", "alter"=>"", "title"=>html("Durée"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>"", "card-visible"=>"true"),
				array("field"=>"con_date_end", "alter"=>"date-be", "title"=>html("Date fin"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>"sortDate", "card-visible"=>"true"),
				array("field"=>"con_reference", "alter"=>"", "title"=>html("Référence"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>"", "card-visible"=>"true"),
				array("field"=>"con_description", "alter"=>"", "title"=>html("Description"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>"", "card-visible"=>"true"),
				array("field"=>"con_bpu_name", "alter"=>"", "title"=>html("BPU"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>"", "card-visible"=>"true"),
				array("field"=>"con_bpu_file", "alter"=>"", "title"=>html("Fichier XLS"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>"", "card-visible"=>"true"),
				
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
			$param = array("height"=>"auto", "show-columns"=>"false", "pagination"=>"true", "search"=>true, "classes"=>"table table-no-bordered table-hover table-light", "striped"=>"false", "new-record"=>"true", "export-table"=>"false", "filter-table"=>"con_status,cli_client");
			
			$this->json['html'] = $table->createTable($head, $param, $thead, $tbody);
		}
		
		public function v_createTr($actions=array())
		{
			// *** Table object ***
			// info : new table($this->table,level[,checkbox(true/false)][,action(true/false)])
			$table = new table($this->table, $this->level, false, $actions);
			// *** create columns title ***
			$table->createTableHead($this->columns);
			
			$this->json['html'] = $table->createJsonTr($this->key, $this->values, $this->color_conds);
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
			
			$this->json['html'] = $table->createTableLine($param, $thead, $tbody, $tfoot);
		}
		
		public function v_editLine()
		{
			$input = new input($this->values, $this->cryptfields);
			
			$this->values->col_name = $input->create("text", "", "col_name", false, "", "", "form-normal");
			
			$this->values = (array) $this->values;
			
			$table = new table($this->table, $this->level, false, array("action"=>true,"save"=>true,"undo"=>true));
			$table->createTableHead($this->columns_edit);
			$table->table->cryptfields = array();
			
			$this->json['html'] = $table->createJsonTr($this->key, $this->values, $this->color_conds);
			$this->json['tdClass'] = $table->info;
		}
		
		public function v_createCard($doc_document, $job_job="")
		{
			if($this->dataSent['otherId']??0 > 0){
				$this->values->idclient = $this->dataSent['otherId'];
			}
			// *** Input object ***
			$input = new input($this->values, $this->cryptfields);
			// info : input->create(type, label, name, mandatory(true/false)[, defaultValue[, divSize[, inputClass[, info(array)]]]]) ,"none-result-text"=>gettext("Créer le con_name")
			// info : defaultValue = $this->dataSent['newValue']??"" for newFrom !!! model_crud c_newFrom case
			// info : $inputs .= $input->breakline;
			$inputs = isset($this->idparent)?$input->create("hidden", "", $this->idparent, false, "", ""):"";
			$inputs .= $input->create("select", gettext("Client"), "idclient", true, $this->dataSent['otherId']??0, "col-md-6", "", array("idlist"=>"1","table"=>"cli_client","data"=>array("subtext"),"search"=>"true","filterChild"=>null,"filtering"=>false,"none-result-text"=>"Créer un client"));
			$inputs .= $input->create("text", gettext("Référence"), "con_reference", true, $this->dataSent['newValue']??"", "col-md-2");
			$inputs .= $input->create("select", gettext("Type"), "idcastype", false, "", "col-md-2", "", array("idlist"=>"1","table"=>"cas_type","search"=>"false","filterChild"=>null,"filtering"=>false,"none-result-text"=>"Créer un type"));
			$inputs .= $input->create("select", gettext("Statut"), "con_status", false, "", "col-md-2", "", array("contents"=>$this->con_status));
			$inputs .= $input->create("text", gettext("Description"), "con_description", false, $this->dataSent['newValue']??"", "col-md-6");
			$inputs .= $input->create("date", gettext("Date"), "con_date", false, "", "col-md-2", "", array("horizontal"=>"right","vertical"=>"bottom"));
			$inputs .= $input->create("text", gettext("Durée"), "con_duration", false, "", "col-md-2");
			$inputs .= $input->create("date", gettext("Date fin"), "con_date_end", false, "", "col-md-2", "", array("horizontal"=>"right","vertical"=>"bottom"));
			$inputs .= $input->create("select", gettext("Responsable"), "iduser", false, "", "col-md-6", "", array("idlist"=>"2","table"=>"usr_user","conds"=>array("usr_type IN(1,2)"),"search"=>"true"));
			$inputs .= $input->create("checkbox", gettext("Métiers"), "idmetier", false, "", "col-md-6", "", array("idlist"=>"1","table"=>"met_metier"));
			
			// *** Div object ***
			$div = new div();
			// info : div->create(divClass, $inputs)
			$divs = $div->create("row", $inputs);
			// *** related job ***
			$div_job = $div->create("mb-3", $job_job->json['html']??"");
			
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
			<div class="d-flex flex-column w-100">
				<div class="d-flex flex-row justify-content-between">
					<div class="d-flex flex-row justify-content-start align-items-center">
						<div class="btn-mod">'.$btn_pdf.'</div>
						<div class="ml-3 btn-mod">'.$btn_mail.'</div>
					</div>
					<div class="d-flex flex-row justify-content-start align-items-center">
					</div>
				</div>
				<div class="d-flex flex-row justify-content-between">
					<div class="d-flex flex-row justify-content-start align-items-center">
						<div class="">'.$btn_delete.'</div>
					</div>
					<div class="d-flex flex-row justify-content-start align-items-center">
						<div class="">'.$btn_save.'</div>
						<div class="ml-3">'.$btn_close.'</div>
					</div>
				</div>
			</div>
			';
			
			// *** Form object ***
			$form = new form();
			// info : form->create("normal/upload", formId, formClass, elem1[, elem2[, elem3[, ...]]])
			$forms = $form->create("normal", $this->table."_form", "", $divs);
			$formJob = $form->create("normal", "job_job_form", "in-tabs", $div_job);
			$formDoc = $form->create("dropzone", $this->table."_form", "", $doc_document->json['html']);
			
			// *** Tab object ***
			$tab = new tab(1);
			// info : tab->create(label, tabClass, form)
			$tabs = $tab->create("Marché", "underline ml-2", $forms);
			$tabs = $tab->create(ngettext("Chantier","Chantiers",$job_job->count).'<span class="ml-2 badge badge-secondary">'.$job_job->count.'</span>', "other underline ml-2", $formJob);
			$tabs = $tab->create(ngettext("Document","Documents",$doc_document->count).'<span class="ml-2 badge badge-secondary">'.$doc_document->count.'</span>', "other underline ml-2 nav-document", $formDoc);
			
			// *** Body variable ***
			// info : $body = $input[.$div][.$form][.tabs]
			$body = $tabs;
			
			// *** Title variable ***
			if(decrypt($this->idrecord) > 0){
				$title = $this->values->con_reference;
			}else{
				$title = $this->newtext;
			}
			// *** Page object ***
			// info : new page(type, id, label, pageClass[or colspan][noscroll|modal-lg|modal-xl|modal-xlg], body, buttons)
			$page = new page("modal", $this->table."_modal", $this->picto." ".$title, "modal-xl", $body, $buttons_form);
			// ex : $page = new page("tr", $this->table."_tr_".$this->idrecord, "", count($this->columns)+1, $body, $btn_save.$btn_cancel.$btn_delete);

			$this->json['html'] = $page->create(array($this->values->insert_date??date("y-m-d"), $this->values->update_date??date("y-m-d"), $this->values->operator??''));
		}
		
		public function v_createPdf($type)
		{
			//$type="view";
			$pdf_name = gettext("")." ".$this->values->con_name;
			
			$pdfDoc = new pdf($this->table, $this->values, $pdf_name.'.pdf');
			$pdfDoc->createTable($this->line,"Titre");
			
			$this->json['html'] = $pdfDoc->createPdf($type);
			$this->json['subject'] = $pdf_name;
		}
		
		public function __destruct()
		{
		}
	}