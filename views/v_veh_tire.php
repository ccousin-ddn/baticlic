<?php
/**
*** Juillet 2022@SoluFile 
**/
	require_once (dirname(__FILE__)."/../models/m_veh_tire.php");
	
	class veh_tire_view extends veh_tire_model {
		
		public function __construct()
		{
			parent::__construct();
			// sorter : sortDate / sortEuro
			$this->columns = array(
				array("field"=>"veh_name", "alter"=>"", "title"=>html("Véhicule"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>"", "card-visible"=>"true"),
				array("field"=>"tir_date", "alter"=>"date-be", "title"=>html("Date"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>"sortDate", "card-visible"=>"true"),
				array("field"=>"tir_mileage", "alter"=>"", "title"=>html("Kilométrage"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>"", "card-visible"=>"true"),
				array("field"=>"tir_reason", "alter"=>"", "title"=>html("Raison"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>"", "card-visible"=>"true"),
				
			);
			$this->columns_small = array(
				array("field"=>"tir_date", "alter"=>"date-be", "title"=>html("Date"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>"sortDate", "card-visible"=>"true"),
				array("field"=>"tir_mileage", "alter"=>"", "title"=>html("Kilométrage"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>"", "card-visible"=>"true"),
				
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
			$param = array("height"=>"auto", "show-columns"=>"false", "pagination"=>"true", "search"=>true, "classes"=>"table table-no-bordered table-hover table-light", "striped"=>"false", "new-record"=>"true", "export-table"=>"false", "filter-table"=>"");
			
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
			$table = new table($this->table, $this->level, false, array("action"=>true,"read"=>true));
			// *** create columns title ***
			$thead = $table->createTableHead($this->columns_small,"thead-card");
			// *** create table lines ***
			$tbody = $table->createTableBody($this->key, $this->values, $this->color_conds);
			// *** create add line ***
			$tfoot = ""; //$table->createTableFoot(count($this->columns), $idparent);
			
			// *** create table ***
			$param = array("height"=>"auto", "show-columns"=>"false", "pagination"=>"false", "search"=>"false", "new-recordtable"=>"false", "id-parent"=>"$idparent", "classes"=>"table table-sm table-card", "striped"=>"false", "export-table"=>"false", "filter-table"=>"");
			
			$this->json['html'] = $table->createTableLine($param, $thead, $tbody, $tfoot);
		}
		
		public function v_createCard($doc_document)
		{
			// *** Input object ***
			$input = new input($this->values, $this->cryptfields);
			// info : input->create(type, label, name, mandatory(true/false)[, defaultValue[, divSize[, inputClass[, info(array)]]]]) ,"none-result-text"=>gettext("Créer le tir_name")
			// info : defaultValue = $this->dataSent['newValue']??"" for newFrom !!! model_crud c_newFrom case
			// info : $inputs .= $input->breakline;
			$inputs = isset($this->idparent)?$input->create("hidden", "", $this->idparent, false, "", ""):"";
			$inputs .= $input->create("select", gettext("Véhicule"), "idvehicle", true, "", "col-md-6", "", array("idlist"=>"2","table"=>"veh_vehicle","search"=>"true","filterChild"=>null,"filtering"=>false));
			$inputs .= $input->create("date", gettext("Date"), "tir_date", true, "", "col-md-3", "", array("horizontal"=>"right","vertical"=>"bottom"));
			$inputs .= $input->create("number", gettext("Kilométrage"), "tir_mileage", true, "", "col-md-3", "", array("pattern"=>"[0-9]{6}", "max"=>999999));
			$inputs .= $input->create("radio", gettext("Avant Gauche"), "tir_fl", false, "", "col-md-6", "", array("contents"=>$this->tireAction));
			$inputs .= $input->create("radio", gettext("Avant Droit"), "tir_fr", false, "", "col-md-6", "", array("contents"=>$this->tireAction));
			$inputs .= $input->create("radio", gettext("Arrière Gauche"), "tir_bl", false, "", "col-md-6", "", array("contents"=>$this->tireAction));
			$inputs .= $input->create("radio", gettext("Arrière Droit"), "tir_br", false, "", "col-md-6", "", array("contents"=>$this->tireAction));
			$inputs .= $input->create("text", gettext("Raison"), "tir_reason", false, "", "col-md-6");
			$inputs .= $input->create("text", gettext("Montant"), "tir_amount", false, "", "col-md-6");
			
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
			// $tabs = $tab->create("Pneu", "underline ml-2", $forms);
			// $tabs = $tab->create(ngettext("Document","Documents",$doc_document->count).'<span class="ml-2 badge badge-secondary">'.$doc_document->count.'</span>', "other underline ml-2 nav-document", $formDoc);
			
			// *** Body variable ***
			// info : $body = $input[.$div][.$form][.tabs]
			$body = $forms;
			
			// *** Title variable ***
			if(decrypt($this->idrecord) > 0){
				$title = $this->label;
			}else{
				$title = $this->newtext;
			}
			// *** Page object ***
			// info : new page(type, id, label, pageClass[or colspan][noscroll|modal-lg|modal-xl|modal-xlg], body, buttons)
			$page = new page("modal", $this->table."_modal", $this->picto." ".$title, "modal-lg noscroll", $body, $buttons_form);
			// ex : $page = new page("tr", $this->table."_tr_".$this->idrecord, "", count($this->columns)+1, $body, $btn_save.$btn_cancel.$btn_delete);

			$this->json['html'] = $page->create(array($this->values->insert_date??date("y-m-d"), $this->values->update_date??date("y-m-d")));
		}
		
		public function v_createPdf($type)
		{
			//$type="view";
			$pdfDoc = new pdf($this->table);
			$pdfDoc->createTable($this->line,"Titre");
			
			$pdf_name = gettext("")." ".$this->values->tir_name.'.pdf';
			
			$this->json['html'] = $pdfDoc->createPdf($this->values,$pdf_name,$type);
			$this->json['subject'] = gettext("")." ".$this->values->tir_name;
		}
		
		public function __destruct()
		{
		}
	}