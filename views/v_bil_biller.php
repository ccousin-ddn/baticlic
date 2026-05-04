<?php
/**
*** Novembre 2019@SoluFile 
**/
	require_once (dirname(__FILE__)."/../models/m_bil_biller.php");
	
	class bil_biller_view extends bil_biller_model {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->columns = array(
				array("field"=>"sup_name", "alter"=>"", "title"=>html("Fournisseur"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>""),
				array("field"=>"bil_date", "alter"=>"date-be", "title"=>html("Date facture"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>"sortDate"),
				array("field"=>"bil_ref", "alter"=>"", "title"=>html("N° facture"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>""),
				array("field"=>"bil_account_number", "alter"=>"", "title"=>html("N° pièce"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>""),
				array("field"=>"bil_description", "alter"=>"", "title"=>html("Concerne"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>""),
				array("field"=>"bil_total", "alter"=>"euro", "title"=>html("HT"), "sortable"=>true, "searchable"=>false, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>"sortEuro"),
				array("field"=>"bil_total_to_pay", "alter"=>"euro", "title"=>html("TTC"), "sortable"=>true, "searchable"=>false, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>"sortEuro"),
				array("field"=>"bil_deposit", "alter"=>"euro", "title"=>html("Acompte"), "sortable"=>true, "searchable"=>false, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>"sortEuro"),
				array("field"=>"bil_deadline", "alter"=>"date-be", "title"=>html("Echéance"), "sortable"=>true, "searchable"=>false, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>"sortDate"),
				array("field"=>"bil_date_payment", "alter"=>"date-be", "title"=>html("Date paiement"), "sortable"=>true, "searchable"=>false, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>"sortDate"),
			);
			$this->color_conds = array();
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
			$param = array("height"=>"auto", "show-columns"=>"false", "pagination"=>"false", "search"=>true, "classes"=>"table table-no-bordered  table-hover table-light", "striped"=>"false", "new-record"=>"true", "export-table"=>"false", "filter-table"=>"bil_paid,bil_status");
			$this->json['html'] = $table->createTable($head, $param, $thead, $tbody);
		}
		
		public function v_createTr()
		{
			// *** Table object ***
			// info : new table($this->table,level[,checkbox(true/false)][,action(true/false)])
			$table = new table($this->table, $this->level, false);
			// *** create columns title ***
			$table->createTableHead($this->columns);
			$this->json['html'] = $table->createJsonTr($this->key, $this->values, $this->color_conds);
		}
		
		public function v_createLineTable($idparent)
		{
			// *** Table object ***
			// info : new table(inv_line,level[,checkbox(true/false)][,(array("action"=>true/false,"read"=>true/false,"edit"=>true/false,"delete"=>true/false))])
			$table = new table($this->table, $this->level, false, array("action"=>true,"read"=>false,"edit"=>true,"delete"=>true));
			// *** create columns title ***
			$thead = $table->createTableHead($this->columns,"thead-light");
			// *** create table lines ***
			$tbody = $table->createTableBody($this->key, $this->values, $this->color_conds);
			// *** create add line ***
			$tfoot = $table->createTableFoot(count($this->columns), $idparent);
			
			// *** create table ***
			$param = array("height"=>"auto", "show-columns"=>"false", "pagination"=>"false", "search"=>"false", "new-recordtable"=>"false", "id-parent"=>"$idparent", "classes"=>"table table-bordered table-sm", "striped"=>"false", "export-table"=>"false", "filter-table"=>"");
			$this->json['html'] = $table->createTableLine($param, $thead, $tbody, $tfoot);
		}
		
		public function v_createCard($doc_document, $bil_breakdown)
		{
			// *** Input object ***
			$input = new input($this->values, $this->cryptfields);
			
			// info : input->create(type, label, name, mandatory(true/false)[, defaultValue[, divSize[, inputClass[, info(array)]]]])
			// info : defaultValue = $this->dataSent['newValue']??"" for newFrom
			$inputs = isset($this->idparent)?$input->create("hidden", "", $this->idparent, false, "", ""):"";
			$inputs .= $input->create("select", "Fournisseur", "idsupplier", true, "", "col-md-4", "", array("idlist"=>"1","table"=>"sup_supplier","search"=>"true","filterChild"=>null,"none-result-text"=>"Créer un fournisseur"));
			$inputs .= $input->create("date", "Date facture", "bil_date", true, "", "col-md-2");
			$inputs .= $input->create("text", "N° facture", "bil_ref", true, "", "col-md-2");
			$inputs .= $input->create("date", "Echéance", "bil_deadline", true, "", "col-md-2");
			$inputs .= $input->create("radio", "Visible", "bil_visible", false, "", "col-md-2", "no-line", array("contents"=>$this->bil_visible));
			
			$inputs .= $input->create("text", "Concerne", "bil_description", false, "", "col-md-4");
			$inputs .= $input->create("text", "N° pièce", "bil_account_number", false, "", "col-md-2");
			$inputs .= $input->create("select", "Statut", "bil_status", false, "", "col-md-2", "no-line", array("contents"=>$this->bil_status));
			$inputs .= $input->create("text", "Acompte", "bil_deposit", false, "", "col-md-2");
			$inputs .= $input->create("date", "Date paiement", "bil_date_payment", false, "", "col-md-2");
			
			$inputs .= $input->create("text", "Total HT", "bil_total", true, "", "col-md-2");
			$inputs .= $input->create("text", "Montant frais", "bil_expenses", false, "", "col-md-2");
			$inputs .= $input->create("text", "Eco taxe", "bil_ecotax", false, "", "col-md-2");
			$inputs .= $input->create("text", "Montant principal", "bil_amount", false, "", "col-md-2");
			$inputs .= $input->create("text", "Montant TVA", "bil_vat", false, "", "col-md-2");
			//$inputs .= $input->create("readonly", "Total ventilation", "bre_tot", false, "", "col-md-2", "bre_tot");
			$inputs .= $input->create("text", "Total TTC", "bil_total_to_pay", true, "", "col-md-2");
			
			$inputs .= $input->create("hidden", "", "bil_validation_1", false, "", "");
			$inputs .= $input->create("action", "Validation Assistant.e", "val1_name", false, "", "col-md-4", "input-group", array("icon"=>"fal fa-file-check","action"=>'{"column":"bil_validation_1","iduser":"'.$_SESSION['iduser'].'", "usr_name":"'.$_SESSION['usr_name'].'"}'));
			$inputs .= $input->create("hidden", "", "bil_validation_2", false, "", "");
			$inputs .= $input->create("action", "Validation Conducteur", "val2_name", false, "", "col-md-4", "input-group", array("icon"=>"fal fa-file-check","action"=>'{"column":"bil_validation_2","iduser":"'.$_SESSION['iduser'].'", "usr_name":"'.$_SESSION['usr_name'].'"}'));
			$inputs .= $input->create("hidden", "", "bil_validation_3", false, "", "");
			$inputs .= $input->create("action", "Validation Responsable", "val3_name", false, "", "col-md-4", "input-group", array("icon"=>"fal fa-file-check","action"=>'{"column":"bil_validation_3","iduser":"'.$_SESSION['iduser'].'", "usr_name":"'.$_SESSION['usr_name'].'"}'));
			
			
			//$inputs .= $input->create("select", "Validée par", "iduser", true, "", "col-md-4", "", array("idlist"=>"2","table"=>"usr_user","search"=>"true","filterChild"=>null));
			$inputs .= $input->create("select", "Type paiement", "idtypepayment", false, "", "col-md-2", "", array("idlist"=>"1","table"=>"bil_type_payment","search"=>"true","filterChild"=>null,"none-result-text"=>"Créer un nouveau type"));
			$inputs .= $input->create("text", "Info paiement", "bil_info_payment", false, "", "col-md-2");
			$inputs .= $input->create("textarea", "Commentaire", "bil_remark", false, "", "col-md-8", "", array("row"=>1));

			// *** Div object ***
			$div = new div();
			// info : div->create(divClass, elem1[, elem2[, elem3[, ...]]])
			$div1 = $div->create("row", $inputs);
			
			// *** related breakdowns ***
			$div_breakdown = $div->create("mb-3", $bil_breakdown);
			
			// *** Button object ***
			$button = new button($this->table, $this->idrecord, $this->action);
			// info : button->create(type[, buttonClass])
			$btn_save 	= $button->create("save", "btn-mod");
			$btn_close 	= $button->create("close");
			//$btn_cancel = $button->create("cancel");
			$btn_delete = $button->create("delete", "btn-left btn-mod");
			
			$btn_duplicateBiller = '
				<div class="d-flex flex-column justify-content-end">
					<div class="bre_tot d-flex flex-row mb-4 align-items-center">
						<i class="fal fa-info-square mr-2"></i>
						<label for="tmp" class="m-0">Total ventilation :</label>
						<input type="text" class="form-control-plaintext ml-2" style="width: 100px;padding: unset;" placeholder="" id="tmp" value="'.$this->values->bre_tot.'" readonly="">
					</div>
					
					<button type="button" id="btn_bil_biller" class="btn btn-outline-info" onclick="iud(\''.$this->table.'\',\''.$this->idrecord.'\',\'update\',\'none\', function(){cardAction(\''.$this->table.'\',\''.$this->idrecord.'\',\'duplicate\',\'bil_biller\')})" autocomplete="off" data-loading-text="<i class=\'fal fa-circle-notch fa-spin mr-2\'></i>'.gettext("En cours...").'">
						<i class="fal fa-copy mr-2"></i>
						'.gettext("Dupliquer la charge").'
					</button>
				</div>
			';
			
			$buttons_form = '
			<div class="d-flex">
				<div class="ml-auto">'.$btn_duplicateBiller.'</div>
			</div>
			';
			
			// *** Form object ***
			$form = new form();
			// info : form->create("normal/upload", formId, formClass, elem1[, elem2[, elem3[, ...]]])
			$form1 = $form->create("normal", $this->table."_form", "", $div1, $div_breakdown, $buttons_form);
			$formDoc = $form->create("dropzone", $this->table."_form", "", $doc_document->json['html']);
			
			// *** Tab object ***
			$tab = new tab(1);
			$tabs = $tab->create("Charge", "underline", $form1);
			$tabs = $tab->create(ngettext("Document","Documents",$doc_document->count).'<span class="ml-2 badge badge-secondary">'.$doc_document->count.'</span>', "other underline ml-2 nav-document", $formDoc);
			
			// *** Body variable ***
			// info : $body = $input[.$div][.$form][.tabs]
			$body = $tabs;
			
			// *** Title variable ***
			if(decrypt($this->idrecord) > 0){
				$title = $this->values->bil_ref ?? $this->newtext;
			}
			// *** Page object ***
			// info : new page(type, id, label, pageClass[or colspan], body, buttons)
			$page = new page("modal", $this->table."_modal", $this->picto." ".$title, "modal-xl", $body, $btn_save.$btn_close.$btn_delete);
			// ex : $page = new page("tr", $this->table."_tr_".$this->idrecord, "", count($this->columns)+1, $body, $btn_save.$btn_cancel.$btn_delete);
			$this->json['html'] = $page->create(array($this->values->insert_date??date("y-m-d"), $this->values->update_date??date("y-m-d")));
		}
		
		public function v_createDashboard()
		{
			$html = '
			<div class="card-body d-flex align-items-end flex-column text-left text-white">
				<ul class="list-group list-group-flush" id="ul_biller">
			';
			$tot = 0;
			foreach($this->values as $ndx=>$line){
				$idrecord = encrypt($line['idbiller']);
				$class = "text-red";
				$html .= '
				<li class="list-group-item d-flex flex-column" data-idrecord="'.$idrecord.'" data-name="'.$line['sup_name'].'" data-days="'.abs($line['days_late']).'" style="padding-right: .75rem;">
					<div class="d-flex justify-content-start align-items-start">
						<span class="w75 text-nowrap '.$class.'">'.$line['days_late'].' '.ngettext("jour","jours",$line['days_late']).'</span>
						<div class="d-flex flex-column flex-grow-1 mx-3">
							<strong>'.$line['sup_name'].'</strong>
							<span class="col_light">'.$line['bil_ref'].'<br>'.$line['bil_description'].'</span>
						</div>
						<div class="d-flex flex-column">
							<span class="text-right text-nowrap">'.alterData("euro",$line['bil_total_to_pay']).'</span>
						</div>
						<div class="btn-group btn-group-sm ml-3" role="group">
							<button type="button" class="btn btn-sm btn-save" role="button" data-action="paid" title="Payé">
								<i class="fal fa-money-check-edit"></i>
							</button>
							<button type="button" class="btn btn-sm btn-light" role="button" data-action="add" data-amount="'.$line['bil_total_to_pay'].'" title="Additionner">
								<i class="fal fa-square"></i>
							</button>
						</div>
					</div>
				</li>';
				$tot += $line['bil_total_to_pay'];
			}
			$html .= '
				</ul>
			</div>
			';
			$this->json['html'] = $html;
			$this->json['tot'] = alterData("euro",$tot);
		}
		
		public function __destruct()
		{
		}
	}