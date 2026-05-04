<?php
/**
*** Juillet 2019@SoluFile 
**/
	require_once (dirname(__FILE__)."/../models/m_quo_quotation.php");
	
	class quo_quotation_view extends quo_quotation_model {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->columns = array(
				array("field"=>"cli_name", "alter"=>"", "title"=>html("Client"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>""),
				array("field"=>"quo_ref", "alter"=>"", "title"=>html("N° devis"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				array("field"=>"job_title", "alter"=>"link", "title"=>html("N° chantier"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				array("field"=>"sit_name", "alter"=>"", "title"=>html("Site"), "sortable"=>true, "searchable"=>true, "class"=>"text-break", "halign"=>"left", "align"=>"left", "width"=>300),
				array("field"=>"quo_date", "alter"=>"date-be", "title"=>html("Date devis"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>"sortDate"),
				array("field"=>"quo_title", "alter"=>"", "title"=>html("Concerne"), "sortable"=>true, "searchable"=>true, "class"=>"text-break", "halign"=>"left", "align"=>"left", "width"=>300),
				array("field"=>"quo_amount", "alter"=>"euro", "title"=>html("Montant"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"right", "align"=>"right", "sorter"=>"sortEuro"),
				array("field"=>"quo_order_ref", "alter"=>"", "title"=>html("BDC client"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
			);
			$this->columns_small = array(
				array("field"=>"quo_ref", "alter"=>"", "title"=>html("N° devis"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				array("field"=>"quo_date", "alter"=>"date-be", "title"=>html("Date devis"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>"sortDate"),
				array("field"=>"sit_name", "alter"=>"", "title"=>html("Site"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				array("field"=>"quo_title", "alter"=>"", "title"=>html("Concerne"), "sortable"=>true, "searchable"=>true, "class"=>"text-break", "halign"=>"left", "align"=>"left"),
				array("field"=>"quo_amount", "alter"=>"euro", "title"=>html("Montant"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"right", "sorter"=>"sortEuro"),
			);
			$this->color_conds = array(
			array("column"=>"quo_status", "query"=>" == 1", "class"=>"td-warning text-nowrap"),
			array("column"=>"quo_status", "query"=>" == 2", "class"=>"td-info text-nowrap"),
			array("column"=>"quo_status", "query"=>" == 3", "class"=>"td-success text-nowrap"),
			array("column"=>"quo_status", "query"=>" == 4", "class"=>"td-primary text-nowrap"),
			array("column"=>"quo_status", "query"=>" == 9", "class"=>"td-danger text-nowrap")
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
			$param = array("height"=>"auto", "show-columns"=>"false", "pagination"=>"false", "search"=>true, "classes"=>"table table-no-bordered  table-hover table-light", "striped"=>"false", "new-record"=>"true", "export-table"=>"false", "filter-table"=>"quo_status");
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
			$table = new table($this->table, $this->level, false, array("action"=>true,"read"=>true,"pdflight"=>true));
			// *** create columns title ***
			$thead = $table->createTableHead($this->columns_small,"thead-card");
			// *** create table lines ***
			$tbody = $table->createTableBody($this->key, $this->values, $this->color_conds);
			// *** create add line ***
			$total = array_sum(array_column($this->values,'quo_amount_ok'));
			$tfoot = '
				<tfoot>
					<tr>
						<td colspan=3></td>
						<td class="text-right text-success">Total</td>
						<td id="ca_total" class="text-right text-success text-nowrap font-weight-bold">'.alterData("euro",$total).'</td>
						<td></td>
					</tr>
				</tfoot>
			';
			
			// *** create table ***
			$param = array("height"=>"auto", "show-columns"=>"false", "pagination"=>"false", "search"=>"false", "new-record"=>"false", "classes"=>"table table-no-bordered table-sm", "striped"=>"false", "export-table"=>"false", "filter-table"=>"");
			$this->json['html'] = $table->createTableLine($param, $thead, $tbody, $tfoot);
		}
		
		public function v_createCard($doc_document,$quo_line,$quo_line_comment,$vat_vat)
		{
			// *** Input object ***
			$input = new input($this->values, $this->cryptfields);
			// info : input->create(type, label, name, mandatory(true/false)[, defaultValue[, divSize[, inputClass[, info(array)]]]])
			// info : defaultValue = $this->dataSent['newValue']??"" for newFrom
			$inputs = $input->create("readonly", "N° devis", "quo_ref", true, "", "col-md-2");
			$inputs .= $input->create("date", "Date devis", "quo_date", true, "", "col-md-2");
			$inputs .= $input->create("date", "Date butoir", "quo_deadline", false, "", "col-md-2");
			$inputs .= $input->create("date", "Date OS", "quo_date_order", false, "", "col-md-2");
			$inputs .= $input->create("text", "BDC client", "quo_order_ref", false, "", "col-md-2");
			$inputs .= $input->create("select", "Statut", "quo_status", false, "", "col-md-2", "", array("contents"=>$this->quo_status));
			$inputs .= $input->create("select", "Client", "idclient", true, "", "col-md-3", "", array("idlist"=>"1","table"=>"cli_client","data"=>array("subtext"),"search"=>"true","filterChild"=>"job_job","none-result-text"=>"Créer un client"));
			//$inputs .= $input->create("select", "Site", "idsite", true, "", "col-md-3", "", array("idlist"=>"1","table"=>"sit_site","search"=>"true","filterChild"=>"job_job","filtering"=>true,"none-result-text"=>"Créer un site"));
			$inputs .= $input->create("select", "Chantier", "idjob", true, "", "col-md-4", "", array("idlist"=>"7","table"=>"job_job","data"=>array("subtext"),"search"=>"true","filterChild"=>null,"filtering"=>true));
			$inputs .= $input->create("select", "Bibliothèque", "idlibrary", false, "", "col-md-3", "", array("idlist"=>"1","table"=>"lib_library","search"=>"true","filterChild"=>null, "other"=>array(0=>"Aucune"),"none-result-text"=>"Créer une bibliothèque"));
			$inputs .= $input->create("select", "% TVA", "quo_idvat", false, "", "col-md-1", "", array("idlist"=>"1","table"=>"vat_vat","search"=>"false","filterChild"=>null));
			$inputs .= $input->create("text", "Situation", "quo_inv_situation", false, "", "col-md-1");
			$inputs .= $input->create("textarea", "Concerne", "quo_title", false, "", "col-md-6", "", array("row"=>2));
			
			$btn_updateFormula = '
				<div style="position:absolute;right:40px;z-index:1;background-color:white;">
					<button type="button" id="btn_update" class="btn btn-sm btn-outline-warning" onclick="cardAction(\''.$this->table.'\',\''.$this->idrecord.'\',\'updateVariables\',\'update\')" autocomplete="off" data-loading-text="<i class=\'fa fa-spinner fa-pulse fa-fw\'></i>">
					<i class="fal fa-save"></i>
					</button>
				</div>
			';
			$inputs .= $input->create("textarea", 'Variables ($a=x, $b=y)', "quo_variables", false, "", "col-md-4", "", array("row"=>2,"other"=>$btn_updateFormula));
			$inputs .= $input->create("text", "Indice révision actu", "quo_actual_index", false, "", "col-md-2");
			
			$inputs_tot = $input->create("textIn", "Total", "quo_amount", false, "", "col-md-3 offset-md-9", "tot-ht");
			// vat
			foreach($vat_vat as $idvat=>$vatPercent){
				$inputs_tot .= $input->create("textIn", "TVA ".$vatPercent." %", "vat".$idvat, false, "", "col-md-3 offset-md-9", "", array("idparent"=>$this->idrecord));
			}
			$inputs_tot .= $input->create("textIn", "Total TTC", "quo_tot_amount", false, "", "col-md-3 offset-md-9", "tot-ttc");
			$inputs_tot .= $input->create("textIn", "Total Avancement", "quo_tot_step", false, "", "col-md-3 offset-md-9", "tot-step");
			
			$inputs_com = $input->create("textarea", "Commentaire", "quo_remark", false, "", "col-md-6", "", array("row"=>2));
			$inputs_com .= $input->create("text", "Signataire", "quo_signatory", false, "", "col-md-6");
			
			$subTotal = $input->create("radio", "Que sous-totaux", "quo_printOnlySubTotal", false, "", "", "", array("contents"=>$this->yesno));
			$showCode = $input->create("radio", "Code article", "quo_printCodeArticle", false, "", "", "", array("contents"=>$this->yesno));
			
			$contextMenu = '
				<div id="contextMenu" class="dropdown">
				<div class="dropdown-menu" aria-labelledby="dropdownMenu" style="display:block;">
					<button id="insertBefore" class="dropdown-item" type="button">Ajouter une ligne avant</button>
					<div class="dropdown-divider"></div>
					<button id="insertAfter" class="dropdown-item" type="button">Ajouter une ligne après</button>
				</div>
				</div>
			';
			
			$dpgf =  $input->create("hidden", "", "idquotation", false, "", "");
			$dpgf .=  $input->create("hidden", "", "quo_dpgf_name", false, "", "");
			$dpgf .= $input->create("uploadDrop", gettext("Fichier XLS"), "quo_dpgf_file", false, "", "col-md-3", "", array("table"=>$this->table,"type"=>"doc","file"=>"excel","maxSize"=>"8","resize"=>"","realName"=>$this->values->quo_dpgf_name??""));
			
			$btn_analyze_dpgf = '
				<button type="button" id="btn_analyze_dpgf" class="btn btn-outline-client-light my-4" '.(empty($this->values->quo_dpgf_file)?"disabled ":"").'
				onclick="table_action({tablename:\''.$this->table.'\', idrecord:\''.$this->idrecord.'\', dataSent:{}, action:\'analyzeDPGF\'})" 
				autocomplete="off" data-loading-text="<i class=\'fal fa-spinner fa-spin fa-fw mr-2\'></i>'.gettext("En cours...").'">
					<i class="fal fa-eye mr-2"></i>
					'.gettext("Analyser données XLS").'
				</button>
			';
			$btn_import_dpgf = '
				<button type="button" id="btn_import_dpgf" class="btn btn-outline-client-light my-3" disabled 
				onclick="table_action({tablename:\''.$this->table.'\', idrecord:\''.$this->idrecord.'\', dataSent:{}, action:\'importDPGF\'})" 
				autocomplete="off" data-loading-text="<i class=\'fal fa-spinner fa-spin fa-fw mr-2\'></i>'.gettext("En cours...").'">
					<i class="fal fa-table mr-2"></i>
					'.gettext("Importer données XLS").'
				</button>
			';
			$dpgf .= '
			<div class="col-md-4">
				<div class="d-flex flex-column justify-content-center h-100">
					'.$btn_analyze_dpgf.'
					'.$btn_import_dpgf.'
				</div>
			</div>
			';
			
			
			// *** Div object ***
			$div = new div();
			// info : div->create(divClass, elem1[, elem2[, elem3[, ...]]])
			$div1 = $div->create("row", $inputs);
			$div2 = $div->create("row", $inputs_tot);
			$div3 = $div->create("row mt-4", $inputs_com);
			
			$div_dpgf = $div->create("row", $dpgf);
			$div_dpgf .= $div->create("my-3 table-dpgf form-normal", '');
			
			// *** quo_line ***
			$div_line = $div->create("my-3", $quo_line->json['html']);
			
			// *** quo_line_comment ***
			$div_line_comment = $div->create("mb-3 mt-4", $quo_line_comment);
			
			// *** Button object ***
			$button = new button($this->table, $this->idrecord, $this->action);
			// info : button->create(type[, buttonClass])
			$btn_save 	= $button->create("save", "btn-mod");
			$btn_close 	= $button->create("close");
			$btn_delete = $button->create("delete", "btn-mod btn-left");
			$btn_pdf	= $button->create("item-pdf");
			$btn_mail 	= $button->create("item-mail");
			
			
			$other_actions = '
			<div class="btn-group dropup btn-mod" id="btn_other_actions">
				<button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-offset="0,10">
					<span class="d-none d-md-inline">Autres actions</span>
				</button>
				<div class="dropdown-menu dropdown-menu-right">
					<a class="dropdown-item" onclick="table_action({tablename:\'tas_task\', idrecord:\''.$this->idrecord.'\', action:\'createFromQuotation\'})" autocomplete="off" data-loading-text="<i class=\'fas fa-circle-notch fa-spin mr-2\'></i>'.gettext("En cours...").'"><i class="fal fa-tasks fa-fw mr-2"></i>
						'.gettext("Créer les postes").'</a>
					<a class="dropdown-item" onclick="iud(\''.$this->table.'\',\''.$this->idrecord.'\',\'update\',\'none\', function(){cardAction(\''.$this->table.'\',\''.$this->idrecord.'\',\'duplicate\',\'quo_quotation\')})" autocomplete="off" data-loading-text="<i class=\'fas fa-circle-notch fa-spin mr-2\'></i>'.gettext("En cours...").'"><i class="fal fa-copy fa-fw mr-2"></i>
						'.gettext("Dupliquer ce devis").'</a>
					<a class="dropdown-item" onclick="changeLineVat(\'quo_line\', \'quo_idvat\')" autocomplete="off" data-loading-text="<i class=\'fas fa-circle-notch fa-spin mr-2\'></i>'.gettext("En cours...").'"><i class="fal fa-sync fa-fw mr-2"></i>
						'.gettext("Modifier toutes les TVA").'</a>
					<a class="dropdown-item" onclick="tableAction({tableName:\'quo_line\', idrecord:$(\'#quo_line_table\').data(\'idParent\'), dataSent:{index:$(\'#quo_actual_index\').val()}, action:\'updateWithActualIndex\'})" autocomplete="off" data-loading-text="<i class=\'fas fa-circle-notch fa-spin mr-2\'></i>'.gettext("En cours...").'"><i class="fal fa-calculator fa-fw mr-2"></i>
						'.gettext("Recalculer en fonction de l'indice").'</a>
					<div class="dropdown-divider"></div>
					<a class="dropdown-item" onclick="$(\'#quo_status\').val(4);iud(\''.$this->table.'\',\''.$this->idrecord.'\',\'update\',\'none\', function(){cardAction(\''.$this->table.'\',\''.$this->idrecord.'\',\'createFrom\',\'inv_invoice\')})" autocomplete="off" data-loading-text="<i class=\'fas fa-circle-notch fa-spin mr-2\'></i>'.gettext("En cours...").'"><i class="fal fa-file-invoice fa-fw mr-2"></i>
						'.gettext("Créer la facture").'</a>
					<a class="dropdown-item" onclick="$(\'#quo_status\').val(4);iud(\''.$this->table.'\',\''.$this->idrecord.'\',\'update\',\'none\', function(){cardAction(\''.$this->table.'\',\''.$this->idrecord.'\',\'createFullFrom\',\'inv_invoice_full\')})" autocomplete="off" data-loading-text="<i class=\'fas fa-circle-notch fa-spin mr-2\'></i>'.gettext("En cours...").'"><i class="fal fa-file-invoice-dollar fa-fw mr-2"></i>
						'.gettext("Créer la facture détaillée").'</a>
					<a class="dropdown-item" onclick="$(\'#quo_status\').val(4);$(\'#quo_inv_situation\').val(parseInt($(\'#quo_inv_situation\').val())+1);iud(\''.$this->table.'\',\''.$this->idrecord.'\',\'update\',\'none\', function(){cardAction(\''.$this->table.'\',\''.$this->idrecord.'\',\'createPercentFrom\',\'inv_invoice_percent\')})" autocomplete="off" data-loading-text="<i class=\'fas fa-circle-notch fa-spin mr-2\'></i>'.gettext("En cours...").'"><i class="fal fa-chart-pie fa-fw mr-2"></i>
						'.gettext("Créer la facture de situation").'</a>
					<div class="dropdown-divider"></div>
					<div class="px-3">'.$subTotal.'</div>
					<div class="px-3">'.$showCode.'</div>
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
			
			$form = new form();
			// info : form->create("normal/upload", formId, formClass, elem1[, elem2[, elem3[, ...]]])
			$form1 = $form->create("normal", $this->table."_form", "", $div1, $div_line, $div2, $div_line_comment, $div3, $contextMenu);
			$formDoc = $form->create("dropzone", $this->table."_form", "", $doc_document->json['html']);
			$form_dpgf = $form->create("normal", $this->table."_form", "", $div_dpgf);
			
			// *** Tab object ***
			$tab = new tab(1);
			$tabs = $tab->create("Devis", "underline", $form1);
			$tabs = $tab->create("DPGF", "other underline ml-2", $form_dpgf);
			$tabs = $tab->create(ngettext("Document","Documents",$doc_document->count).'<span class="ml-2 badge badge-secondary">'.$doc_document->count.'</span>', "other underline ml-2 nav-document", $formDoc);
			
			// *** Body variable ***
			// info : $body = $input[.$div][.$form][.tabs]
			$body = $tabs;
			
			// *** Title variable ***
			$title = $this->values->quo_ref ?? gettext("Nouveau ".$this->label);
			// *** Page object ***
			// info : new page(type, id, label, pageClass, body, buttons)
			$page = new page("modal", $this->table."_modal", $this->picto." ".$title, "modal-xlg", $body, $buttons_form);
			$this->json['html'] = $page->create(array($this->values->insert_date??date("y-m-d"), $this->values->update_date??date("y-m-d")));
		}
		
		public function v_createPdf($type)
		{
			//$type="view";
			$pdf_name = preg_replace( '/[^A-Za-z0-9-_\. ]/', '', gettext("Devis")."-".$this->values->quo_ref."-".$this->values->quo_title, FILTER_SANITIZE_URL );
			
			$pdfDoc = new pdf($this->table, $this->values, $pdf_name.'.pdf');
			$pdfDoc->createTable($this->line, "", array("light"=>$this->dataSent=="pdflight"?true:false, "codeArticle"=>$this->values->quo_printCodeArticle, "OnlySubTotal"=>$this->values->quo_printOnlySubTotal));
			
			$this->json['html'] = $pdfDoc->createPdf($type);
			$this->json['subject'] = $pdf_name;
		}
		
		public function v_createDashboard()
		{
			$div = new div();
			$this->json['html'] = $div->dashboard($this->values,"text-left");
		}
		
		public function v_createDropDown()
		{
			$html = '
				<div class="dropdown-menu dropdown-menu-right">
			';
			foreach($this->values as $quo){
				$html .= '
					<a class="dropdown-item" onclick="table_action({tablename:\'quo_quotation\', idrecord:\''.encrypt($quo['idquotation']).'\', dataSent:\''.$quo['quo_ref'].' '.$quo['quo_title'].'\', action:\'quo_importToInvoice\'})" autocomplete="off" data-loading-text="<i class=\'fas fa-circle-notch fa-spin mr-2\'></i>'.gettext("En cours...").'">
						<i class="fal fa-file-import fa-fw mr-2"></i>'.$quo['quo_ref'].' '.$quo['quo_title'].'
					</a>';
			}
			$html .= '
				</div>
			';
			
			$this->json['html'] = $html;
		}
		
		public function __destruct()
		{
		}
	}