<?php
/**
*** 09-2024@SOLUfile SRL 
**/
	require_once (dirname(__FILE__)."/../models/m_inv_line_ecotax.php");
	
	class inv_line_ecotax_view extends inv_line_ecotax_model {
		
		public function __construct()
		{
			parent::__construct();
			// sorter : sortDate / sortEuro
			$this->columns = array(
				array("field"=>"lin_description", "alter"=>"", "title"=>html("Ecotaxe"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>"", "card-visible"=>"true"),
				array("field"=>"lin_quantity", "alter"=>"", "title"=>html("Qté"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"right", "align"=>"right", "width"=>"100", "sorter"=>"", "card-visible"=>"true"),
				array("field"=>"lin_pu", "alter"=>"euro", "title"=>html("PU"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"right", "align"=>"right", "width"=>"100", "sorter"=>"sortEuro", "card-visible"=>"true"),
				array("field"=>"vat_percent", "alter"=>"", "title"=>html("% TVA"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"right", "align"=>"right", "width"=>"100", "sorter"=>"", "card-visible"=>"true"),
				array("field"=>"lin_total", "alter"=>"euro", "title"=>html("Total"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"right", "align"=>"right", "width"=>"100", "sorter"=>"sortEuro", "card-visible"=>"true"),
				
			);
			$this->columns_edit = array(
				array("field"=>"lin_description", "alter"=>"", "title"=>html(""), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>"", "card-visible"=>"true"),
				array("field"=>"lin_quantity", "alter"=>"", "title"=>html(""), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>"", "card-visible"=>"true"),
				array("field"=>"lin_pu", "alter"=>"", "title"=>html(""), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>"sortEuro", "card-visible"=>"true"),
				array("field"=>"vat_percent", "alter"=>"", "title"=>html(""), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>"", "card-visible"=>"true"),
				array("field"=>"lin_total", "alter"=>"euro", "title"=>html(""), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>"sortEuro", "card-visible"=>"true"),
				
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
			$table = new table($this->table, $this->level, false, array("action"=>true,"edit"=>true,"delete"=>true));
			// *** create columns title ***
			$thead = $table->createTableHead($this->columns, "thead-card");
			// *** create table lines ***
			$tbody = $table->createTableBody($this->key, $this->values, $this->color_conds);
			// *** create add line ***
			$tfoot = $table->createTableFoot(count($this->columns), $idparent);
			// *** create table ***
			$param = array("height"=>"auto", "show-columns"=>"false", "pagination"=>"false", "search"=>"false", "striped"=>"false", "new-record"=>"false", "id-parent"=>"$idparent", "classes"=>"table table-bordered table-sm", "export-table"=>"false", "filter-table"=>"");
			
			$this->json['html'] = $table->createTableLine($param, $thead, $tbody, $tfoot);
		}
		
		public function v_createTr($actions=array())
		{
			// *** Table object ***
			// info : new table($this->table,level[,checkbox(true/false)][,action(true/false)])
			$table = new table($this->table, $this->level, false, $actions);
			// *** create columns title ***
			$table->createTableHead($this->columns_edit);
			
			$this->json['html'] = $table->createJsonTr($this->key, $this->values, $this->color_conds);
			$this->json['tdClass'] = $table->info;
		}
		
		public function v_createLineTable($idparent)
		{
			// *** Table object ***
			// info : new table(inv_line,level[,checkbox(true/false)][,(array("action"=>true/false,"read"=>true/false,"edit"=>true/false,"delete"=>true/false))])
			$table = new table($this->table, $this->level, false, array("action"=>true,"edit"=>true,"delete"=>true));
			// *** create header ***
			$head = $table->createHeadLines("Ecotaxes");
			// *** create columns title ***
			$thead = $table->createTableHead($this->columns,"thead-card");
			// *** create table lines ***
			$tbody = $table->createTableBody($this->key, $this->values, $this->color_conds);
			// *** create add line ***
			$tfoot = $table->createTableFoot(count($this->columns), $idparent);
			
			// *** create table ***
			$param = array("height"=>"auto", "show-columns"=>"false", "pagination"=>"false", "search"=>"false", "new-record"=>"false", "id-parent"=>"$idparent", "classes"=>"table table-bordered table-sm", "striped"=>"false", "export-table"=>"false", "filter-table"=>"");
			
			$this->json['html'] = $table->createTableLine($param, $thead, $tbody, $tfoot, $head);
		}
		
		public function v_editLine()
		{
			$input = new input($this->values, $this->cryptfields);
			define('IDCLIENT', $this->dataSent['idclient']);
			
			$this->values->lin_description = $input->create("text", "", "lin_description", false, "", "", "text-left form-normal");
			$this->values->lin_quantity = $input->create("text", "", "lin_quantity", false, "", "", "text-right form-normal");
			$this->values->lin_pu = $input->create("text", "", "lin_pu", false, "", "", "text-right form-normal");
		   	$this->values->vat_percent = $input->create("select", "", "idvat", false, 1, "", "text-right form-normal", array("idlist"=>"1","table"=>"vat_vat","search"=>"false","filterChild"=>null,"filtering"=>false));

			//var_dump($this->values);exit;
			$this->values = (array) $this->values;
			$this->v_createTr(array("action"=>true,"save"=>true,"undo"=>true));
		}
		
		public function v_createCard($doc_document="")
		{
			// *** Input object ***
			$input = new input($this->values, $this->cryptfields);
			// info : input->create(type, label, name, mandatory(true/false)[, defaultValue[, divSize[, inputClass[, info(array)]]]]) ,"none-result-text"=>gettext("Créer le lin_name")
			// info : defaultValue = $this->dataSent['newValue']??"" for newFrom !!! model_crud c_newFrom case
			// info : $inputs .= $input->breakline;
			$inputs = isset($this->idparent)?$input->create("hidden", "", $this->idparent, false, "", ""):"";
			$inputs .= $input->create("select", gettext(""), "idinvoice", false, "", "col-md-6", "", array("idlist"=>"1","table"=>"tableselect","search"=>"true","filterChild"=>null,"filtering"=>false));
			$inputs .= $input->create("select", gettext(""), "idvat", false, "", "col-md-6", "", array("idlist"=>"1","table"=>"tableselect","search"=>"true","filterChild"=>null,"filtering"=>false));
			$inputs .= $input->create("text", gettext(""), "lin_description", false, "", "col-md-6");
			$inputs .= $input->create("text", gettext(""), "lin_quantity", false, "", "col-md-6");
			$inputs .= $input->create("text", gettext(""), "lin_pu", false, "", "col-md-6");
			$inputs .= $input->create("text", gettext(""), "lin_total", false, "", "col-md-6");
			
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
			//$formDoc = $form->create("dropzone", $this->table."_form", "", $doc_document->json['html']);
			
			// *** Tab object ***
			// ex : $tab = new tab(1);
			// info : tab->create(label, tabClass, form)
			// $tabs = $tab->create("Ligne facture ecotaxe", "underline ml-2", $forms);
			// $tabs = $tab->create(ngettext("Document","Documents",$doc_document->count).'<span class="ml-2 badge badge-secondary">'.$doc_document->count.'</span>', "other underline ml-2 nav-document", $formDoc);
			
			// *** Body variable ***
			// info : $body = $input[.$div][.$form][.tabs]
			$body = $forms;
			
			// *** Title variable ***
			if(decrypt($this->idrecord) > 0){
				$title = $this->values->lin_name;
			}else{
				$title = $this->newtext;
			}
			// *** Page object ***
			// info : new page(type, id, label, pageClass[or colspan][noscroll|modal-lg|modal-xl|modal-xlg], body, buttons)
			$page = new page("modal", $this->table."_modal", $this->picto." ".$title, "modal-lg", $body, $buttons_form);
			// ex : $page = new page("tr", $this->table."_tr_".$this->idrecord, "", count($this->columns)+1, $body, $btn_save.$btn_cancel.$btn_delete);

			$this->json['html'] = $page->create(array($this->values->insert_date??date("y-m-d"), $this->values->update_date??date("y-m-d"), $this->values->operator??''));
		}
		
		public function v_createPdf($type)
		{
			//$type="view";
			$pdf_name = gettext("")." ".$this->values->lin_name;
			
			$pdfDoc = new pdf($this->table, $this->values, $pdf_name.'.pdf');
			$pdfDoc->createTable($this->line,"Titre");
			
			$this->json['html'] = $pdfDoc->createPdf($type);
			$this->json['subject'] = $pdf_name;
		}
		
		public function __destruct()
		{
		}
	}