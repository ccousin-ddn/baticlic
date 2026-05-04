<?php
/**
*** Juillet 2019@SoluFile 
**/
	require_once (dirname(__FILE__)."/../models/m_inv_invoice.php");
	
	class inv_invoice_view extends inv_invoice_model {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->columns = array(
				//array("field"=>"inv_status", "alter"=>"array", "title"=>html("statut"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				array("field"=>"inv_ref", "alter"=>"", "title"=>html("N° facture"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				array("field"=>"cli_name", "alter"=>"", "title"=>html("Client"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				array("field"=>"sit_name", "alter"=>"", "title"=>html("Site"), "sortable"=>true, "searchable"=>true, "class"=>"text-break", "halign"=>"left", "align"=>"left"),
				array("field"=>"job_reference", "alter"=>"", "title"=>html("N° chantier"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				array("field"=>"inv_situation", "alter"=>"", "title"=>html("Situation"), "sortable"=>true, "searchable"=>false, "class"=>"text-break", "halign"=>"left", "align"=>"left"),
				array("field"=>"inv_title", "alter"=>"", "title"=>html("Concerne"), "sortable"=>true, "searchable"=>true, "class"=>"text-break", "halign"=>"left", "align"=>"left"),
				array("field"=>"inv_date", "alter"=>"date-be", "title"=>html("Date facture"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"right", "align"=>"right", "sorter"=>"sortDate"),
				array("field"=>"inv_deadline", "alter"=>"date-be", "title"=>html("Echéance"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"right", "align"=>"right", "sorter"=>"sortDate"),
				//array("field"=>"inv_date_payment", "alter"=>"date-be", "title"=>html("Paiement"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"right", "align"=>"right", "sorter"=>"sortDate"),
				array("field"=>"inv_tot_situation", "alter"=>"euro", "title"=>html("HT"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"right", "align"=>"right", "sorter"=>"sortEuro"),
				array("field"=>"inv_tot_ttc", "alter"=>"euro", "title"=>html("TTC"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"right", "align"=>"right", "sorter"=>"sortEuro"),
				
			);
			$this->columns_small = array(
				array("field"=>"inv_ref", "alter"=>"", "title"=>html("N° facture"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				array("field"=>"inv_date", "alter"=>"date-be", "title"=>html("Date"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>"sortDate"),
				array("field"=>"quo_ref", "alter"=>"", "title"=>html("N° devis"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				array("field"=>"sit_name", "alter"=>"", "title"=>html("Site"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				array("field"=>"inv_title", "alter"=>"", "title"=>html("Concerne"), "sortable"=>true, "searchable"=>true, "class"=>"text-break", "halign"=>"left", "align"=>"left"),
				array("field"=>"inv_situation", "alter"=>"", "title"=>html("Situation"), "sortable"=>true, "searchable"=>false, "class"=>"text-break", "halign"=>"left", "align"=>"left"),
				array("field"=>"real_inv_tot_articles", "alter"=>"euro", "title"=>html("Total HT"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"right"),
			);
			
			$this->color_conds = array(
			array("column"=>"inv_status", "query"=>" == 0", "class"=>"td-warning text-nowrap"),
			array("column"=>"inv_status", "query"=>" == 3", "class"=>"td-success text-nowrap"),
			array("column"=>"inv_utc", "query"=>" < ".time(), "class"=>"td-danger text-nowrap"),
			array("column"=>"inv_status", "query"=>" == 1", "class"=>"td-info text-nowrap"),
			array("column"=>"inv_status", "query"=>" == 2", "class"=>"td-danger text-nowrap"),
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
			$param = array("height"=>"auto", "show-columns"=>"false", "pagination"=>"false", "search"=>true, "classes"=>"table table-no-bordered  table-hover table-light", "striped"=>"false", "new-record"=>"true", "export-table"=>"true", "filter-table"=>"inv_status,inv_type");
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
			$this->json['tdClass'] = $table->info;
		}
		
		public function v_createLineTable($idparent)
		{
			// *** Table object ***
			// info : new table(inv_line,level[,checkbox(true/false)][,action(true/false)])
			$table = new table($this->table, $this->level, false, array("action"=>true,"read"=>true));
			// *** create columns title ***
			$thead = $table->createTableHead($this->columns_small,"thead-card");
			// *** create table lines ***
			$tbody = $table->createTableBody($this->key, $this->values, $this->color_conds);
			// *** create add line ***
			$total = array_sum(array_column($this->values,'inv_tot_articles_ok'));
			$tfoot = '
				<tfoot>
					<tr>
						<td colspan=4></td>
						<td class="text-right text-success">Total</td>
						<td id="ca_total" class="text-right text-success text-nowrap font-weight-bold">'.alterData("euro",$total).'</td>
						<td></td>
					</tr>
				</tfoot>
			';
			
			// *** create table ***
			$param = array("height"=>"auto", "show-columns"=>"false", "pagination"=>"false", "search"=>"false", "new-recordtable"=>"false", "classes"=>"table table-sm", "striped"=>"false", "export-table"=>"false", "filter-table"=>"");
			$this->json['html'] = $table->createTableLine($param, $thead, $tbody, $tfoot);
		}
		
		public function v_createCard($doc_document, $inv_line, $inv_line_ecotax, $inv_line_previous, $inv_line_retention, $vat_vat, $pay_payment, $inv_tracking, $quo_quotation)
		{
			// *** Input object ***
			$input = new input($this->values, $this->cryptfields);
			// info : input->create(type, label, name, mandatory(true/false)[, defaultValue[, divSize[, inputClass[, info(array)]]]])
			// info : defaultValue = $this->dataSent['newValue']??"" for newFrom
			$inputs = $input->create("disabled", "N° facture", "inv_ref", true, "", "col-md-2", "input-group");
			$inputs .= $input->create("date", "Date facture", "inv_date", true, "", "col-md-2");
			$inputs .= $input->create("date", "Echéance", "inv_deadline", true, "", "col-md-2");
			$inputs .= $input->create("text", "BDC client", "inv_order_ref", false, "", "col-md-2");
			$inputs .= $input->create("radio", "Type", "inv_type", false, "", "col-md-2", "no-line", array("contents"=>$this->inv_type));
			$inputs .= $input->create("select", "Statut", "inv_status", false, "", "col-md-2", "", array("contents"=>$this->inv_status));
			
			$inputs .= $input->create("select", "Client", "idclient", true, "", "col-md-3", "", array("idlist"=>"1","table"=>"cli_client","search"=>"true","filterChild"=>"sit_site,job_job"));
			$inputs .= $input->create("select", "Site", "idsite", true, "", "col-md-3", "", array("idlist"=>"1","table"=>"sit_site","search"=>"true","filterChild"=>null,"filtering"=>true));
			$inputs .= $input->create("select", "Chantier", "idjob", false, "", "col-md-3", "", array("idlist"=>"4","table"=>"job_job","search"=>"true","filterChild"=>"quo_quotation","filtering"=>true));
			$inputs .= $input->create("select", "N° devis", "idquotation", false, "", "col-md-2", "", array("idlist"=>"1","table"=>"quo_quotation","search"=>"true","filterChild"=>null,"filtering"=>true));
			$inputs .= $input->create("text", "Situation", "inv_situation", false, "", "col-md-1");
			
			$inputs .= $input->create("textarea", "Concerne", "inv_title", false, "", "col-md-6", "", array("row"=>2));
			$inputs .= $input->create("textarea", "Commentaire", "inv_remark", false, "", "col-md-5", "", array("row"=>2));
			//$inputs .= $input->create("date", "Date paiement", "inv_date_payment", false, "", "col-md-3");
			$inputs .= $input->create("select", "% TVA", "inv_idvat", false, "", "col-md-1", "", array("idlist"=>"1","table"=>"vat_vat","search"=>"false","filterChild"=>null));
			
			// vat
			$inputs_vat = '';
			foreach($vat_vat as $idvat=>$vatPercent){
				$inputs_vat .= $input->create("textIn", "TVA ".$vatPercent." %", "vat".$idvat, false, "", "col-12", "", array("idparent"=>$this->idrecord));
			}
			
			$inputs_tot = $input->create("textIn", "Total Travaux", "inv_tot_articles", false, "", "col-md-3 offset-md-9 font-italic", "font-italic");
			$inputs_tot .= $input->create("textIn", "Total Sit. Préc.", "inv_tot_previous", false, "", "col-md-3 offset-md-9 font-italic", "font-italic");
			$inputs_tot .= $input->create("textIn", "Total Situation", "inv_tot_situation", false, "", "col-md-3 offset-md-9");
			$inputs_tot .= $input->create("textIn", "Total Ecotaxes", "inv_tot_ecotax", false, "", "col-md-3 offset-md-9");
			$inputs_tot .= $input->create("textIn", "Total HT", "inv_tot_ht", false, "", "col-md-3 offset-md-9");
			$inputs_tot .= $input->create("textIn", "Total TVA", "inv_tot_vat", false, "", "col-md-3 offset-md-9");
			$inputs_tot .= $input->create("textIn", "Total TTC", "inv_tot_ttc", false, "", "col-md-3 offset-md-9");
			$inputs_tot .= $input->create("textIn", "Total Retenues", "inv_tot_retention", false, "", "col-md-3 offset-md-9");
			$inputs_tot .= $input->create("textIn", "Net a payer", "inv_tot_to_pay", false, "", "col-md-3 offset-md-9");
			
			// *** Div object ***
			$div = new div();
			// info : div->create(divClass, elem1[, elem2[, elem3[, ...]]])
			$divs = $div->create("row", $inputs);
			$divs .= $div->create("my-3", $inv_line->json['html']);
			$divs .= $div->create("my-3", $inv_line_previous->json['html']);
			$divs .= $div->create("my-3", $inv_line_ecotax->json['html']);
			$divs .= $div->create("my-3", $inv_line_retention->json['html']);
			
			$divs .= '<div class="row"><div class="col-2">';
			$divs .= $div->create("row", $inputs_vat);
			$divs .= '</div><div class="col-10">';
			$divs .= $div->create("row", $inputs_tot);
			$divs .= '</div></div>';
			
			// *** related payments ***
			$div_pay = $div->create("pt-3", $pay_payment->json['html']);
			// *** related trackings ***
			$div_tra = $div->create("pt-3", $inv_tracking->json['html']);
			
			// *** Button object ***
			$button = new button($this->table, $this->idrecord, $this->action);
			// info : button->create(type[, buttonClass])
			$btn_save 	= $button->create("save", "btn-mod");
			$btn_close = $button->create("close");
			$btn_delete = $button->create("delete", "btn-mod btn-left");
			$btn_pdf	= $button->create("item-pdf");
			$btn_mail 	= $button->create("item-mail");
			
			$other_actions = '
			<div class="btn-group dropup" id="btn_other_actions">
				<button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-offset="0,10">
					<span class="d-none d-md-inline">Autres actions</span>
				</button>
				<div class="dropdown-menu dropdown-menu-right">
					<a class="dropdown-item" onclick="changeLineVat(\'inv_line\', \'inv_idvat\')" autocomplete="off" data-loading-text="<i class=\'fas fa-circle-notch fa-spin mr-2\'></i>'.gettext("En cours...").'"><i class="fal fa-sync fa-fw mr-2"></i>
						'.gettext("Modifier toutes les TVA").'</a>
					<div class="dropdown-divider"></div>
					'.$btn_pdf.'
					'.$btn_mail.'
				</div>
			</div>
			';
			
			$btn_importQuotation = '
			<div class="btn-group dropup mr-3" id="btn_import_quotation" data-idinvoice="'.$this->idrecord.'">
				<button type="button" class="btn btn-outline-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-offset="0,10">
					<span class="d-none d-md-inline">'.gettext("Ajouter un devis").'</span>
				</button>
				'.$quo_quotation->json['html'].'
			</div>
			';
			
			$buttons_form = '
			<div class="d-flex flex-column w-100">
				<div class="d-flex flex-row justify-content-between">
					<div class="d-flex flex-row justify-content-start align-items-center">
						<div class="">'.$btn_delete.'</div>
					</div>
					<div class="d-flex flex-row justify-content-start align-items-center">
						'.$btn_importQuotation.'
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
			$form1 = $form->create("normal", $this->table."_form", "", $divs);
			$formDoc = $form->create("dropzone", $this->table."_form", "", $doc_document->json['html']);
			$form_pay = $form->create("normal", "pay_payment_form in-tabs", "pt-3", $div_pay);
			$form_tra = $form->create("normal", "inv_tracking_form in-tabs", "pt-3", $div_tra);
			
			// *** Tab object ***
			$tab = new tab(1);
			$tabs = $tab->create("Facture", "underline", $form1);
			$tabs = $tab->create(ngettext("Paiement","Paiements",$pay_payment->count).'<span class="ml-2 badge badge-secondary">'.$pay_payment->count.'</span>', "underline ml-2 other", $form_pay);
			$tabs = $tab->create(ngettext("Suivi","Suivis",$inv_tracking->count).'<span class="ml-2 badge badge-secondary">'.$inv_tracking->count.'</span>', "underline ml-2 other", $form_tra);
			$tabs = $tab->create(ngettext("Document","Documents",$doc_document->count).'<span class="ml-2 badge badge-secondary">'.$doc_document->count.'</span>', "other underline ml-2 nav-document", $formDoc);
			
			// *** Body variable ***
			// info : $body = $input[.$div][.$form][.tabs]
			$body = $tabs;
			
			// *** Title variable ***
			$title = $this->values->inv_ref ?? gettext("Nouvelle ".$this->label);
			// *** Page object ***
			// info : new page(type, id, label, pageClass, body, buttons)
			$page = new page("modal", $this->table."_modal", $this->picto." ".$title, "modal-xlg", $body, $buttons_form);
			$this->json['html'] = $page->create(array($this->values->insert_date??date("y-m-d"), $this->values->update_date??date("y-m-d")));
		}
		
		public function v_createDashboard()
		{
			$html = '
			<div class="card-body d-flex align-items-end flex-column text-left text-white">
				<ul class="list-group list-group-flush" id="ul_invoice">
			';
			$tot = 0;
			foreach($this->values as $ndx=>$line){
				$idrecord = encrypt($line['idinvoice']);
				$class = "text-red";
				$html .= '
				<li class="list-group-item d-flex flex-column" data-idrecord="'.$idrecord.'" data-name="'.$line['cli_name'].'" data-days="'.abs($line['days_late']).'" style="padding-right: .75rem;">
					<div class="d-flex justify-content-start align-items-start">
						<span class="w75 text-nowrap '.$class.'">'.$line['days_late'].' '.ngettext("jour","jours",$line['days_late']).'</span>
						<div class="d-flex flex-column flex-grow-1 mx-3">
							<strong>'.$line['cli_name'].'</strong>
							<span class="col_light">'.$line['inv_title'].'</span>
						</div>
						<div class="d-flex flex-column">
							<span class="text-right text-nowrap">'.alterData("euro",$line['inv_tot_ttc']).'</span>
						</div>
						<div class="btn-group btn-group-sm ml-3" role="group">
							<button type="button" class="btn btn-sm btn-save" role="button" data-action="paid" title="Payé">
								<i class="fal fa-money-check-edit"></i>
							</button>
							<button type="button" class="btn btn-sm btn-light" role="button" data-action="add" data-amount="'.$line['inv_tot_ttc'].'" title="Additionner">
								<i class="fal fa-square"></i>
							</button>
						</div>
					</div>
				</li>';
				$tot += $line['inv_tot_ttc'];
			}
			$html .= '
				</ul>
			</div>
			';
			$this->json['html'] = $html;
			$this->json['tot'] = alterData("euro",$tot);
		}
		
		public function v_createPdf($type)
		{
			//$type="view";
			$pdf_name = ($this->values->inv_type == 0 ? gettext("Facture") : gettext("Avoir"))."-".$this->values->inv_ref;
			
			$pdfDoc = new pdf($this->table, $this->values, $pdf_name.'.pdf');
			$pdfDoc->createTable($this->line);
			
			$this->json['html'] = $pdfDoc->createPdf($type);
			$this->json['subject'] = $pdf_name;
		}
		
		public function __destruct()
		{
		}
	}