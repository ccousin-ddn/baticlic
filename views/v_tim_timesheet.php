<?php
/**
*** Juillet 2019@SoluFile 
**/
	require_once (dirname(__FILE__)."/../models/m_tim_timesheet.php");
	
	class tim_timesheet_view extends tim_timesheet_model {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->columns = array(
				array("field"=>"wor_picture", "alter"=>"photo", "title"=>html("Photo"), "sortable"=>false, "searchable"=>false, "class"=>"p-0", "halign"=>"center", "align"=>"left", "width"=>"65", "valign"=>"middle"),
				array("field"=>"wor_name", "alter"=>"", "title"=>html("Compagnon"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				array("field"=>"tim_date", "alter"=>"date-be", "title"=>html("Date"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>"sortDate"),
				array("field"=>"cli_name", "alter"=>"", "title"=>html("Client"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				array("field"=>"job_name", "alter"=>"", "title"=>html("Chantier"), "sortable"=>true, "searchable"=>true, "class"=>"text-break", "halign"=>"left", "align"=>"left"),
				array("field"=>"tas_name", "alter"=>"", "title"=>html("Poste"), "sortable"=>true, "searchable"=>true, "class"=>"text-break", "halign"=>"left", "align"=>"left"),
				array("field"=>"tim_duration", "alter"=>"", "title"=>html("Durée"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				
				//array("field"=>"tim_hour_begin", "alter"=>"", "title"=>html("Heure début"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				//array("field"=>"tim_hour_end", "alter"=>"", "title"=>html("Heure fin"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				
			);
			
			$this->columns_small = array(
				array("field"=>"cli_name", "alter"=>"", "title"=>html("Client"), "sortable"=>true, "searchable"=>true, "class"=>"text-break", "halign"=>"left", "align"=>"left"),
				array("field"=>"job_name", "alter"=>"", "title"=>html("Chantier"), "sortable"=>true, "searchable"=>true, "class"=>"text-break", "halign"=>"left", "align"=>"left"),
				array("field"=>"tas_name", "alter"=>"", "title"=>html("Poste"), "sortable"=>true, "searchable"=>true, "class"=>"text-break", "halign"=>"left", "align"=>"left"),
				array("field"=>"tim_duration", "alter"=>"", "title"=>html("Durée"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
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
			$param = array("height"=>"auto", "show-columns"=>"false", "pagination"=>"false", "search"=>true, "classes"=>"table table-no-bordered  table-hover table-light", "striped"=>"false", "new-record"=>"true", "export-table"=>"false", "filter-table"=>"tim_year,tim_week,wor_worker");
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
			$thead = $table->createTableHead($this->columns_small,"thead-card");
			// *** create table lines ***
			$tbody = $table->createTableBody($this->key, $this->values, $this->color_conds);
			// *** create add line ***
			$tfoot = ""; //$table->createTableFoot(count($this->columns), $idparent);
			
			// *** create table ***
			$param = array("height"=>"auto", "show-columns"=>"false", "pagination"=>"false", "search"=>"false", "new-record"=>"false", "classes"=>"table table-sm", "striped"=>"false", "export-table"=>"false", "filter-table"=>"");
			$this->json['html'] = $table->createTableLine($param, $thead, $tbody, $tfoot);
		}
		
		public function v_createCard($doc_document="")
		{
			// *** Input object ***
			$input = new input($this->values, $this->cryptfields);
			// info : input->create(type, label, name, mandatory(true/false)[, defaultValue[, divSize[, inputClass[, info(array)]]]])
			// info : defaultValue = $this->dataSent['newValue']??"" for newFrom
			$inputs = $input->create("select", "Client", "idclient", false, "", "col-md-3", "", array("idlist"=>"1","table"=>"cli_client","search"=>"true","filterChild"=>"job_job"));
			//$inputs .= $input->create("select", "Site", "idsite", false, "", "col-md-3", "", array("idlist"=>"1","table"=>"sit_site","search"=>"true", "filtering"=>true,"filterChild"=>"job_job,tas_task"));
			$inputs .= $input->create("select", "Chantier", "idjob", false, "", "col-md-4", "", array("idlist"=>"1","table"=>"job_job","search"=>"true", "filtering"=>true,"filterChild"=>"tas_task"));
			$inputs .= $input->create("select", "Poste", "idtask", false, "", "col-md-4", "", array("idlist"=>"1","table"=>"tas_task","search"=>"true", "filtering"=>true,"filterChild"=>null));
			$inputs .= $input->create("select", "Compagnon", "idworker", false, "", "col-md-6", "", array("idlist"=>"4","table"=>"wor_worker","search"=>"true","filterChild"=>null));
			$inputs .= $input->create("date", "Date", "tim_date", false, "", "col-md-2");
			//$inputs .= $input->create("hour", "Début", "tim_hour_begin", false, "", "col-md-1");
			//$inputs .= $input->create("hour", "Fin", "tim_hour_end", false, "", "col-md-1");
			$inputs .= $input->create("text", "Durée", "tim_duration", false, "", "col-md-2");
			$inputs .= $input->create("select", "Validé par", "tim_validated_by", false, "", "col-md-6", "", array("idlist"=>"2","table"=>"usr_user","search"=>"false","filterChild"=>null));
			$inputs .= $input->create("date", "Validé le", "tim_validated_date", false, "", "col-md-3");
			$inputs .= $input->create("select", "Modifié par", "tim_edited_by", false, "", "col-md-6", "", array("idlist"=>"2","table"=>"usr_user","search"=>"false","filterChild"=>null));
			$inputs .= $input->create("date", "Modifié le", "tim_edited_date", false, "", "col-md-3");
			$inputs .= $input->create("textarea", "Commentaire", "tim_remark", false, "", "col-md-12", "", array("row"=>2));
			

			// *** Div object ***
			$div = new div();
			// info : div->create(divClass, elem1[, elem2[, elem3[, ...]]])
			$div1 = $div->create("row", $inputs);
			
			// *** related documents ***
			$div_doc = $doc_document !="" ? $div->create("row", $doc_document) : "";
			
			// *** Button object ***
			$button = new button($this->table, $this->idrecord, $this->action);
			// info : button->create(type[, buttonClass])
			$btn_save 	= $button->create("save");
			$btn_close = $button->create("close");
			$btn_delete = $button->create("delete", "btn-left");
			$btn_duplicate = '
				<button type="button" id="btn_tim_timesheet" class="btn btn-outline-info" onclick="iud(\''.$this->table.'\',\''.$this->idrecord.'\',\'update\',\'none\', function(){cardAction(\''.$this->table.'\',\''.$this->idrecord.'\',\'duplicate\',\'tim_timesheet\')})" autocomplete="off" data-loading-text="<i class=\'fas fa-circle-notch fa-spin mr-2\'></i>'.gettext("En cours...").'">
					<i class="fal fa-copy mr-2"></i>
					'.gettext("Dupliquer").'
				</button>
			';
			
			$buttons_form = '
			<div class="d-flex flex-column w-100">
				<div class="d-flex flex-row justify-content-start align-items-center mb-3">
					'.$btn_duplicate.'
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
			$form1 = $form->create("normal", $this->table."_form", "", $div1, $div_doc);
			
			// *** Tab object ***
			// ex : $tab = new tab(1);
			// info : tab->create(label, tabClass, form)
			// ex : $tabs = $tab->create("Onglet1", "", $form1);
			// ex : $tabs = $tab->create("Onglet2", "", $form2);
			
			// *** Body variable ***
			// info : $body = $input[.$div][.$form][.tabs]
			$body = $form1;
			
			// *** Title variable ***
			if (isset($this->values->cli_name) && isset($this->values->wor_name)){
				$title = $this->values->cli_name." - ".$this->values->wor_name;
			}else{
				$title = $this->newtext;
			}
			
			// *** Page object ***
			// info : new page(type, id, label, pageClass, body, buttons)
			$page = new page("modal", $this->table."_modal", $this->picto." ".$title, "modal-xl noscroll", $body, $buttons_form);
			$this->json['html'] = $page->create(array($this->values->insert_date??date("y-m-d"), $this->values->update_date??date("y-m-d")));
		}
		
		public function __destruct()
		{
		}
	}