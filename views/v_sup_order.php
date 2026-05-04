<?php
/**
*** Juillet 2019@SoluFile 
**/
	require_once (dirname(__FILE__)."/../models/m_sup_order.php");
	
	class sup_order_view extends sup_order_model {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->columns = array(
				array("field"=>"sup_name", "alter"=>"", "title"=>html("Fournisseur"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				array("field"=>"ord_ref", "alter"=>"", "title"=>html("N° commande"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				array("field"=>"ord_date", "alter"=>"date-be", "title"=>html("Date commande"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>"sortDate"),
				array("field"=>"ord_title", "alter"=>"", "title"=>html("Concerne"), "sortable"=>true, "searchable"=>true, "class"=>"text-break", "halign"=>"left", "align"=>"left"),
				array("field"=>"job_reference", "alter"=>"", "title"=>html("Chantier"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				array("field"=>"ord_deadline", "alter"=>"date-be", "title"=>html("Date livraison prévue"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>"sortDate"),
				array("field"=>"ord_amount", "alter"=>"euro", "title"=>html("Montant"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>"sortEuro"),
				
			);
			$this->columns_small = array(
				array("field"=>"sup_name", "alter"=>"", "title"=>html("Fournisseur"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				array("field"=>"ord_ref", "alter"=>"", "title"=>html("N° commande"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				array("field"=>"ord_title", "alter"=>"", "title"=>html("Concerne"), "sortable"=>true, "searchable"=>true, "class"=>"text-break", "halign"=>"left", "align"=>"left"),
				array("field"=>"ord_amount", "alter"=>"euro", "title"=>html("Montant"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>"sortEuro"),
				array("field"=>"ord_status", "alter"=>"array", "title"=>html("Statut"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>"sortEuro"),
				
			);
			$this->color_conds = array(
				array("column"=>"ord_status", "query"=>" == 1", "class"=>"td-warning"),
				array("column"=>"ord_status", "query"=>" == 2", "class"=>"td-primary"),
				array("column"=>"ord_status", "query"=>" == 3", "class"=>"td-info"),
				array("column"=>"ord_status", "query"=>" == 4", "class"=>"td-danger"),
				array("column"=>"ord_status", "query"=>" == 5", "class"=>"td-success"),
				array("column"=>"ord_status", "query"=>" == 6", "class"=>"td-warning"),
				array("column"=>"ord_status", "query"=>" == 7", "class"=>"td-primary"),
			);
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
			$param = array("height"=>"auto", "show-columns"=>"false", "pagination"=>"false", "search"=>true, "classes"=>"table table-no-bordered  table-hover table-light", "striped"=>"false", "new-record"=>"true", "export-table"=>"false", "filter-table"=>"ord_breakdown,ord_status");
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
			$table = new table($this->table, $this->level, false, array("action"=>true,"read"=>true));
			// *** create columns title ***
			$thead = $table->createTableHead($this->columns_small, "thead-card");
			// *** create table lines ***
			$tbody = $table->createTableBody($this->key, $this->values, $this->color_conds);
			// *** create add line ***
			$tfoot = "";
			
			// *** create table ***
			$param = array("height"=>"auto", "show-columns"=>"false", "pagination"=>"false", "search"=>"false", "new-record"=>"false", "classes"=>"table table-sm", "striped"=>"false", "export-table"=>"false", "filter-table"=>"");
			$this->json['html'] = $table->createTableLine($param, $thead, $tbody, $tfoot);
		}
		
		public function v_createCard($doc_document,$sup_order_line,$sup_receipt)
		{
			// *** Input object ***
			$input = new input($this->values, $this->cryptfields);
			// info : input->create(type, label, name, mandatory(true/false)[, defaultValue[, divSize[, inputClass[, info(array)]]]])
			// info : defaultValue = $this->dataSent['newValue']??"" for newFrom
			$inputs = $input->create("readonly", "N° commande", "ord_ref", false, "", "col-md-2");
			$inputs .= $input->create("select", "Chantier", "idjob", false, "", "col-md-4", "", array("idlist"=>($this->values->ord_status==1?"3":"4"),"table"=>"job_job", "data"=>array("subtext"),"search"=>"true","filterChild"=>null,"filtering"=>false));
			$inputs .= $input->create("date", "Date commande", "ord_date", true, "", "col-md-2");
			$inputs .= $input->create("date", "Date livraison prévue", "ord_deadline", false, "", "col-md-2");
			$inputs .= $input->create("date", "Date réception", "ord_delivery_date", false, "", "col-md-2");
			$inputs .= $input->create("select", "Fournisseur", "idsupplier", true, "", "col-md-3", "", array("idlist"=>"1","table"=>"sup_supplier","search"=>"true","filterChild"=>"sup_agency","none-result-text"=>"Créer le fournisseur"));
			$inputs .= $input->create("select", "Agence", "idsupagency", false, "", "col-md-3", "", array("idlist"=>"1","table"=>"sup_agency","search"=>"false","filterChild"=>null,"filtering"=>true,"none-result-text"=>"Créer le fournisseur"));
			$inputs .= $input->create("text", "Vos références", "ord_sup_ref", false, "", "col-md-2");
			$inputs .= $input->create("select", "Statut", "ord_status", false, "", "col-md-2", "", array("contents"=>$this->ord_status));
			$inputs .= $input->create("text", "N° BL", "ord_delivery_note", false, "", "col-md-2");
			$inputs .= $input->create("textarea", "Concerne", "ord_title", false, "", "col-md-5", "", array("row"=>1));
			$inputs .= $input->create("radio", "Type", "ord_type", false, "", "col-md-5", "no-line", array("contents"=>$this->ord_type));
			$inputs .= $input->create("radio", "Ventilée", "ord_breakdown", false, "", "col-md-2", "no-line", array("contents"=>$this->ord_bd));
			
			$input9 = $input->create("textIn", "Total", "ord_amount", false, "", "col-md-3 offset-md-9", "tot-amount");
			$input10 = $input->create("textIn", "Eco taxe", "ord_ecotax", false, "", "col-md-3 offset-md-9", "tot-ecotax");
			$input11 = $input->create("textIn", "Total HT", "ord_tot_ht", false, "", "col-md-3 offset-md-9", "tot-ht");
			$input12 = $input->create("textIn", "% TVA", "ord_tva_percent", true, "", "col-md-3 offset-md-9", "", array("idparent"=>$this->idrecord));
			$input13 = $input->create("textIn", "Total TTC", "ord_tot_amount", false, "", "col-md-3 offset-md-9", "tot-ttc");
			
			$input20 = $input->create("textarea", "Adresse de livraison", "ord_del_address", false, "", "col-md-6", "", array("row"=>2));
			$input21 = $input->create("textarea", "Remarques", "ord_remark", false, "", "col-md-6", "", array("row"=>2));
			
			$printAmount = $input->create("radio", "Imprimer tarifs", "ord_printAmount", false, "", "", "", array("contents"=>$this->yesno));

			// *** Div object ***
			$div = new div();
			// info : div->create(divClass, elem1[, elem2[, elem3[, ...]]])
			$div1 = $div->create("row", $inputs);
			$div2 = $div->create("row", $input9, $input10, $input11, $input12, $input13);
			$div3 = $div->create("row mt-4", $input20, $input21);
			
			// *** $sup_order_line ***
			$div_line = $div->create("mb-3", $sup_order_line);
			// *** related receipt ***
			$div_rec = $div->create("mb-3", $sup_receipt->json['html']);
			
			// *** Button object ***
			$button = new button($this->table, $this->idrecord, $this->action);
			// info : button->create(type[, buttonClass])
			$btn_save 	= $button->create("save", "btn-mod");
			$btn_close = $button->create("close");
			$btn_delete = $button->create("delete", "btn-left btn-mod");
			$btn_pdf	= $button->create("item-pdf", "btn-mod");
			$btn_mail 	= $button->create("item-mail", "btn-mod");
			
			if(in_array($this->values->ord_status,array(2,4,7))){
				$btn_createQuotation = '
					<a class="dropdown-item" id="btn_sup_receipt" onclick="iud(\'sup_order\',\''.$this->idrecord.'\',\'update\',\'none\', function(){cardAction(\''.$this->table.'\',\''.$this->idrecord.'\',\'createFrom\',\'sup_receipt\')})" autocomplete="off" data-loading-text="<i class=\'fas fa-circle-notch fa-spin mr-2\'></i>'.gettext("En cours...").'">
						<i class="fal fa-dolly fa-fw mr-2"></i>
						'.gettext("Réceptionner").'
					</a>
				';
			}else{
				$btn_createQuotation = '';
			}
			
			$other_actions = '
			<div class="btn-group dropup" id="btn_other_actions">
				<button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-offset="0,10">
					<span class="d-none d-md-inline">Autres actions</span>
				</button>
				<div class="dropdown-menu dropdown-menu-right">
					'.$btn_createQuotation.'
					<div class="dropdown-divider"></div>
					<div class="px-3">'.$printAmount.'</div>
					<div class="dropdown-divider"></div>
					'.$btn_pdf.'
					'.$btn_mail.'
				</div>
			</div>
			';
			
			$buttons_form = '
			<div class="d-flex flex-column w-100">
				<div class="d-flex flex-row justify-content-between">
					<div class="d-flex flex-row justify-content-start align-items-center">
						<div class="">'.$btn_delete.'</div>
					</div>
					<div class="d-flex flex-row justify-content-start align-items-center">
						'.$other_actions.'
						<div class="ml-4">'.$btn_save.'</div>
						<div class="">'.$btn_close.'</div>
					</div>
				</div>
			</div>
			';
			
			// *** Form object ***
			$form = new form();
			// info : form->create("normal/upload", formId, formClass, elem1[, elem2[, elem3[, ...]]])
			$form1 = $form->create("normal", $this->table."_form", "", $div1, $div_line, $div2, $div3);
			$form2 = $form->create("normal", "sup_receipt_form", "in-tabs", $div_rec);
			$formDoc = $form->create("dropzone", $this->table."_form", "", $doc_document->json['html']);
			
			// *** Tab object ***
			$tab = new tab(1);
			$tabs = $tab->create("Commande", "underline", $form1);
			$tabs = $tab->create(ngettext("Réception","Réceptions",$sup_receipt->count).'<span class="ml-2 badge badge-secondary">'.$sup_receipt->count.'</span>', "other underline ml-2", $form2);
			$tabs = $tab->create(ngettext("Document","Documents",$doc_document->count).'<span class="ml-2 badge badge-secondary">'.$doc_document->count.'</span>', "other underline ml-2 nav-document", $formDoc);
			
			// *** Body variable ***
			// info : $body = $input[.$div][.$form][.tabs]
			$body = $tabs;
			
			// *** Title variable ***
			$title = $this->values->ord_ref ?? gettext("Nouvelle ".$this->label);
			// *** Page object ***
			// info : new page(type, id, label, pageClass, body, buttons)
			$page = new page("modal", $this->table."_modal", $this->picto." ".$title, "modal-xl", $body, $buttons_form);
			$this->json['html'] = $page->create(array($this->values->insert_date??date("y-m-d"), $this->values->update_date??date("y-m-d")));
		}
		
		public function v_createPdf($type)
		{
			//$type="view";
			$pdf_name = gettext("BDC")." ".$this->values->ord_ref.'.pdf';
			
			$pdfDoc = new pdf($this->table, $this->values, $pdf_name);
			if($this->values->ord_printAmount == 0){
				$this->line->columns_pdf = $this->line->columns_pdf_light;
			}
			$pdfDoc->createTable($this->line, "", array());
			
			$this->json['html'] = $pdfDoc->createPdf($type);
			$this->json['subject'] = gettext("BDC")." ".$this->values->ord_ref." - ".$this->values->ord_title;
		}
		
		public function v_createDashboard()
		{
			$div = new div();
			$this->json['html'] = $div->dashboard($this->values,"text-left");
		}
		
		public function __destruct()
		{
		}
	}