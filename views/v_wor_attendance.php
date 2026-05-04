<?php
/**
*** Novembre 2019@SoluFile 
**/
	require_once (dirname(__FILE__)."/../models/m_wor_attendance.php");
	
	class wor_attendance_view extends wor_attendance_model {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->columns = array(
				array("field"=>"wor_picture", "alter"=>"photo", "title"=>html("Photo"), "sortable"=>false, "searchable"=>false, "class"=>"p-0", "halign"=>"center", "align"=>"left", "width"=>"65", "valign"=>"middle"),
				array("field"=>"wor_name", "alter"=>"", "title"=>html("Compagnon"), "sortable"=>true, "searchable"=>true, "class"=>"text-break", "halign"=>"left", "align"=>"left", "sorter"=>""),
				array("field"=>"att_date", "alter"=>"date-be", "title"=>html("Date"), "sortable"=>true, "searchable"=>false, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>"sortDate"),
				array("field"=>"veh_numberplate", "alter"=>"", "title"=>html("Véhicule"), "sortable"=>true, "searchable"=>false, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>""),
				array("field"=>"att_in", "alter"=>"date-time", "title"=>html("Début"), "sortable"=>true, "searchable"=>false, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>""),
				array("field"=>"att_out", "alter"=>"date-time", "title"=>html("Fin"), "sortable"=>true, "searchable"=>false, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>""),
				array("field"=>"diner_break", "alter"=>"", "title"=>html("Panier"), "sortable"=>true, "searchable"=>false, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>""),
				//array("field"=>"typ_name", "alter"=>"", "title"=>html("Absence"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>""),
				array("field"=>"att_dur", "alter"=>"time", "title"=>html("Total pointage"), "sortable"=>true, "searchable"=>false, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>""),
				//array("field"=>"att_duration_night", "alter"=>"small-time", "title"=>html("Durée nuit"), "sortable"=>true, "searchable"=>false, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>""),
				array("field"=>"tot_tim_duration", "alter"=>"small-time", "title"=>html("Total tâches"), "sortable"=>true, "searchable"=>false, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>""),
				//array("field"=>"att_ip", "alter"=>"", "title"=>html("Adresse IP"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>""),
				array("field"=>"tot_travel", "alter"=>"small-time", "title"=>html("Durée trajet"), "sortable"=>true, "searchable"=>false, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>""),
				
			);
			
			$this->columns_state = array(
				array("field"=>"wor_picture", "alter"=>"photo", "title"=>html("Photo"), "sortable"=>false, "searchable"=>false, "class"=>"p-0", "halign"=>"center", "align"=>"left", "width"=>"65", "valign"=>"middle"),
				array("field"=>"wor_name", "alter"=>"", "title"=>html("Compagnon"), "sortable"=>true, "searchable"=>true, "class"=>"", "halign"=>"left", "align"=>"left", "sorter"=>""),
				array("field"=>"lu", "alter"=>"hour-minute", "title"=>html("Lundi"), "sortable"=>true, "searchable"=>false, "class"=>"text-nowrap", "halign"=>"right", "align"=>"center", "width"=>"100"),
				array("field"=>"ma", "alter"=>"hour-minute", "title"=>html("Mardi"), "sortable"=>true, "searchable"=>false, "class"=>"text-nowrap", "halign"=>"right", "align"=>"center", "width"=>"100"),
				array("field"=>"me", "alter"=>"hour-minute", "title"=>html("Mercredi"), "sortable"=>true, "searchable"=>false, "class"=>"text-nowrap", "halign"=>"right", "align"=>"center", "width"=>"100"),
				array("field"=>"je", "alter"=>"hour-minute", "title"=>html("Jeudi"), "sortable"=>true, "searchable"=>false, "class"=>"text-nowrap", "halign"=>"right", "align"=>"center", "width"=>"100"),
				array("field"=>"ve", "alter"=>"hour-minute", "title"=>html("Vendredi"), "sortable"=>true, "searchable"=>false, "class"=>"text-nowrap", "halign"=>"right", "align"=>"center", "width"=>"100"),
				array("field"=>"sa", "alter"=>"hour-minute", "title"=>html("Samedi"), "sortable"=>true, "searchable"=>false, "class"=>"text-nowrap", "halign"=>"right", "align"=>"center", "width"=>"100"),
				array("field"=>"di", "alter"=>"hour-minute", "title"=>html("Dimanche"), "sortable"=>true, "searchable"=>false, "class"=>"text-nowrap", "halign"=>"right", "align"=>"center", "width"=>"100"),
				array("field"=>"tot", "alter"=>"hour-minute", "title"=>html("Total Semaine"), "sortable"=>true, "searchable"=>false, "class"=>"text-nowrap font-weight-bold", "halign"=>"right", "align"=>"center", "width"=>""),
				array("field"=>"tot_sup_25", "alter"=>"hour-minute", "title"=>html("H. sup. 25%"), "sortable"=>true, "searchable"=>false, "class"=>"text-nowrap", "halign"=>"right", "align"=>"center", "width"=>""),
				array("field"=>"tot_sup_50", "alter"=>"hour-minute", "title"=>html("H. sup. 50%"), "sortable"=>true, "searchable"=>false, "class"=>"text-nowrap", "halign"=>"right", "align"=>"center", "width"=>""),
				array("field"=>"tot_diner", "alter"=>"", "title"=>html("Paniers"), "sortable"=>true, "searchable"=>false, "class"=>"text-nowrap font-weight-bold", "halign"=>"right", "align"=>"center", "width"=>""),
				
			);
			
			$this->color_conds = array(
				array("column"=>"idabstype", "query"=>" > 0", "class"=>"td-success"),
				array("column"=>"tot_diff", "query"=>" <= 900", "class"=>"td-success"),
				array("column"=>"tot_diff", "query"=>" > 900", "class"=>"td-danger"),
			);
			$this->color_conds_state = array(
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
			$param = array("height"=>"auto", "show-columns"=>"false", "pagination"=>"false", "search"=>true, "classes"=>"table table-no-bordered  table-hover table-light", "striped"=>"false", "new-record"=>"true", "export-table"=>"false", "filter-table"=>"ord_year,ord_week,wor_worker");
			$this->json['html'] = $table->createTable($head, $param, $thead, $tbody);
		}
		
		public function v_createStateTable()
		{
			// *** Table object ***
			// info : new table($this->table,level[,checkbox(true/false)][,action(true/false)])
			$table = new table($this->table, $this->level, false);
			// *** create header ***
			$head = $table->createHead('<i class="fal fa-calendar-week"></i>', ngettext("Pointage hebdomadaire", "Pointages hebdomadaires", $this->count), '', false);
			// *** create columns title ***
			$thead = $table->createTableHead($this->columns_state);
			// *** create table lines ***
			$tbody = $table->createTableBody("idworker", $this->values, $this->color_conds_state);
			// *** create table ***
			$param = array("height"=>"auto", "show-columns"=>"false", "pagination"=>"false", "search"=>true, "classes"=>"table table-no-bordered  table-hover table-light", "striped"=>"false", "new-record"=>"false", "export-table"=>"false", "filter-table"=>"wor_year,wor_week");
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
		
		public function v_createCard($doc_document,$tim_timesheet,$art_article)
		{
			// *** Input object ***
			$input = new input($this->values, $this->cryptfields);
			// info : input->create(type, label, name, mandatory(true/false)[, defaultValue[, divSize[, inputClass[, info(array)]]]])
			// info : defaultValue = $this->dataSent['newValue']??"" for newFrom
			$inputs = isset($this->idparent)?$input->create("hidden", "", $this->idparent, false, "", ""):"";
			$inputs .= $input->create("select", "Compagnon", "idworker", false, "", "col-md-6", "", array("idlist"=>"4","table"=>"wor_worker","search"=>"true","filterChild"=>null));
			$inputs .= $input->create("date", "Date", "att_date", false, "", "col-md-3");
			$inputs .= $input->create("radio", "Pause midi", "att_diner_break", false, "", "col-md-3", "no-line", array("contents"=>array(0=>"Non",1=>"Oui")));
			//$inputs .= $input->create("text", "Adresse IP", "att_ip", false, "", "col-md-3");
			$inputs .= $input->create("select", "Véhicule", "idvehicle", false, "", "col-md-3", "", array("idlist"=>"3","table"=>"veh_vehicle","search"=>"false","filterChild"=>null));
			$inputs .= $input->create("radio", "Chauffeur", "att_driver", false, "", "col-md-3", "no-line", array("contents"=>array(0=>"Non",1=>"Oui")));
			//$inputs .= $input->create(($this->values->att_driver==1?"hour":"disabled"), "Trajet aller", "att_travel_time_going", false, "", "col-md-3");
			//$inputs .= $input->create(($this->values->att_driver==1?"hour":"disabled"), "Trajet retour", "att_travel_time_coming", false, "", "col-md-3");
			$inputs .= $input->create("dateTime", "Début", "att_in", false, "", "col-md-3");
			$inputs .= $input->create("dateTime", "Fin", "att_out", false, "", "col-md-3");
			//$inputs .= $input->create("dateTime", "Début break", "att_break_start", false, "", "col-md-3");
			$inputs .= $input->create("select", "Absence", "idabstype", false, "", "col-md-6", "", array("idlist"=>"1","table"=>"abs_type","other"=>array(0=>""),"search"=>"false","filterChild"=>null));
			//$inputs .= $input->create("dateTime", "Fin break", "att_break_stop", false, "", "col-md-3");
			$inputs .= $input->create("readOnly", "Durée jour", "att_duration_day", false, "", "col-md-3");
			$inputs .= $input->create("readOnly", "Durée nuit", "att_duration_night", false, "", "col-md-3");
			//$inputs .= $input->create("text", "Raison du break", "att_break_reason", false, "", "col-md-6");
			$inputs .= $input->create("select", "Validé par", "att_validated_by", false, "", "col-md-3", "", array("idlist"=>"2","table"=>"usr_user","conds"=>array("usr_type = 2"),"search"=>"true"));
			$inputs .= $input->create("date", "Validé le", "att_validated_date", false, "", "col-md-3");
			//$inputs .= $input->create("select", "Modifié par", "att_edited_by", false, "", "col-md-3", "", array("idlist"=>"1","table"=>"usr_user","search"=>"true","filterChild"=>null));
			//$inputs .= $input->create("date", "Modifié le", "att_edited_date", false, "", "col-md-3");
			

			// *** Div object ***
			$div = new div();
			// info : div->create(divClass, elem1[, elem2[, elem3[, ...]]])
			$div1 = $div->create("row", $inputs);
			// *** related tasks ***
			$div_tim = $div->create("mb-3", $tim_timesheet->json["html"]);
			// *** related tasks ***
			$div_art = $div->create("mb-3", $art_article->json["html"]);
			
			// *** Button object ***
			$button = new button($this->table, $this->idrecord, $this->action);
			// info : button->create(type[, buttonClass])
			$btn_save 	= $button->create("save", "btn-mod");
			$btn_close 	= $button->create("close");
			//$btn_cancel = $button->create("cancel");
			$btn_delete = $button->create("delete", "btn-left btn-mod");
			$btn_duplicate = '
				<button type="button" id="btn_quo_quotation" class="btn btn-outline-info" onclick="iud(\''.$this->table.'\',\''.$this->idrecord.'\',\'update\',\'none\', function(){cardAction(\''.$this->table.'\',\''.$this->idrecord.'\',\'duplicate\',\'wor_attendance\')})" autocomplete="off" data-loading-text="<i class=\'fas fa-circle-notch fa-spin mr-2\'></i>'.gettext("En cours...").'">
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
			$form1 = $form->create("normal", $this->table."_form", "", $div1);
			$form2 = $form->create("normal", "tim_timesheet_form", "", $div_tim);
			$form3 = $form->create("normal", "art_article_form", "", $div_art);
			
			// *** Tab object ***
			$tab = new tab(1);
			// info : tab->create(label, tabClass, form)
			$tabs = $tab->create("Pointage", "underline", $form1);
			$tabs = $tab->create(ngettext("Poste","Postes",$tim_timesheet->count).'<span class="ml-2 badge badge-secondary">'.$tim_timesheet->count.'</span>', "other underline ml-2", $form2);
			$tabs = $tab->create(ngettext("Matériel","Matériels",$art_article->count).'<span class="ml-2 badge badge-secondary">'.$art_article->count.'</span>', "other underline ml-2", $form3);
			
			// *** Body variable ***
			// info : $body = $input[.$div][.$form][.tabs]
			$body = $tabs;
			
			// *** Title variable ***
			if(decrypt($this->idrecord) > 0){
				$title = $this->values->wor_name." ".alterData("date-be", $this->values->att_date);
			}else{
				$title = gettext("Nouveau ".$this->label);
			}
			// *** Page object ***
			// info : new page(type, id, label, pageClass[or colspan], body, buttons)
			$page = new page("modal", $this->table."_modal", $this->picto." ".$title, "modal-lg noscroll", $body, $buttons_form);
			// ex : $page = new page("tr", $this->table."_tr_".$this->idrecord, "", count($this->columns)+1, $body, $btn_save.$btn_cancel.$btn_delete);
			$this->json['html'] = $page->create(array($this->values->insert_date??date("y-m-d"), $this->values->update_date??date("y-m-d")));
		}
		
		public function v_createWeekCard($wor_worker, $tim_weekly, $year, $week)
		{
			$worker = (object) $wor_worker->values[0];
			
			$lines = array();
			foreach($this->values as $line){
				$lines[$line['numday']] = $line;
			}
			$new_date = new DateTime();
			$new_date->setISODate($year,$week);
			$month = $new_date->format('F Y');
			$month = ucfirst(IntlDateFormatter::formatObject($new_date, 'MMM y', 'fr'));
			$monday = $new_date->format('d/m');
			$new_date->modify('tuesday this week');
			$tuesday = $new_date->format('d/m');
			$new_date->modify('wednesday this week');
			$wednesday = $new_date->format('d/m');
			$new_date->modify('thursday this week');
			$thursday = $new_date->format('d/m');
			$new_date->modify('friday this week');
			$friday = $new_date->format('d/m');
			$new_date->modify('saturday this week');
			$saturday = $new_date->format('d/m');
			$new_date->modify('sunday this week');
			$sunday = $new_date->format('d/m');
			$preview = imgExist("upload/signatures/", $tim_weekly->tim_signature, "images/empty1x1.png");
			
			$infoWorker = '
			<div class="card mb-3">
				<div class="row no-gutters">
					<div class="col-md-1">
						<img src="upload/pictures/'.$worker->wor_picture.'" class="card-img">
					</div>
					<div class="col-md-3">
						<div class="px-3 py-2">
							<h3 class="card-title">'.$worker->wor_lastname.' '.$worker->wor_firstname.'</h3>
							<h5 class="card-text">'.$wor_worker->wor_type[$worker->wor_type].'</h5>
						</div>
					</div>
					<div class="col-md-4">
						<div class="px-3 py-2">
							<h5 class="card-text">'.$worker->wor_address.'<br>'.$worker->wor_pc.' '.$worker->wor_city.'</h5>
						</div>
					</div>
					<div class="col-md-4 text-center">
						<img src="'.$preview.'" style="max-height:85px;">
						'.alterData("date-be",$tim_weekly->tim_creation_date??"").'
					</div>
			</div>
			';
			
			$infoWeek = '
			<div class="table-responsive">
				<table class="table table-striped table-borderless table-sm">
					<thead class="thead-light" style="font-size:.8rem;">
						<tr>
							<th>JOURS</th>
							<th style="max-width:24px;"></th>
							<th class="text-break text-center" style="vertical-align: top;"><span class="badge badge-pill badge-secondary">1</span><br>Heure arrivée entreprise</th>
							<th class="text-break text-center" style="vertical-align: top;"><span class="badge badge-pill badge-secondary">2</span><br>Heure arrivée chantier</th>
							<th class="text-break text-center" style="vertical-align: top;"><span class="badge badge-pill badge-secondary">3</span><br>Durée conduite matin</th>
							<th class="text-break text-center" style="vertical-align: top;"><span class="badge badge-pill badge-secondary">4/5</span><br>Heure debut pause déjeuner</th>
							<th class="text-break text-center" style="vertical-align: top;"><span class="badge badge-pill badge-secondary">5/6</span><br>Heure fin pause déjeuner</th>
							<th class="text-break text-center" style="vertical-align: top;"><span class="badge badge-pill badge-secondary">7</span><br>Heure depart chantier</th>
							<th class="text-break text-center" style="vertical-align: top;"><span class="badge badge-pill badge-secondary">8</span><br>Heure retour entreprise</th>
							<th class="text-break text-center" style="vertical-align: top;"><span class="badge badge-pill badge-secondary">9</span><br>Durée conduite après-midi</th>
							<th class="text-break text-center" style="vertical-align: top;"><span class="badge badge-pill badge-secondary">10</span><br>Total heures travaillées</th>
							<th class="text-break text-center" style="vertical-align: top;"><span class="badge badge-pill badge-secondary">11</span><br>Total durée conduite</th>
							<th class="text-break text-center" style="vertical-align: top;"><span class="badge badge-pill badge-secondary">12</span><br>Total heures prestées</th>
							<th class="text-nowrap text-center" style="vertical-align: top;"><span class="badge badge-pill badge-secondary">13</span><br>Zone</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td class="text-nowrap">Lundi '.$monday.'</td>
							<td class="text-center">'.(isset($lines[2])?($lines[2]["att_driver"]==1?'<i class="fad fa-steering-wheel fa-fw"></i>':'<i class="fad fa-chair-office fa-fw"></i>'):'').'</td>
							<td class="text-nowrap text-center">'.($lines[2]['col1']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[2]['col2']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[2]['col3']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[2]['col4']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[2]['col5']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[2]['col6']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[2]['col7']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[2]['col8']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[2]['col9']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[2]['col10']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[2]['col11']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[2]['col12']??'-').'</td>
						</tr>
						<tr>
							<td class="text-nowrap">Mardi '.$tuesday.'</td>
							<td class="text-center">'.(isset($lines[3])?($lines[3]["att_driver"]==1?'<i class="fad fa-steering-wheel fa-fw"></i>':'<i class="fad fa-chair-office fa-fw"></i>'):'').'</td>
							<td class="text-nowrap text-center">'.($lines[3]['col1']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[3]['col2']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[3]['col3']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[3]['col4']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[3]['col5']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[3]['col6']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[3]['col7']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[3]['col8']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[3]['col9']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[3]['col10']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[3]['col11']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[3]['col12']??'-').'</td>
						</tr>
						<tr>
							<td class="text-nowrap">Mercredi '.$wednesday.'</td>
							<td class="text-center">'.(isset($lines[4])?($lines[4]["att_driver"]==1?'<i class="fad fa-steering-wheel fa-fw"></i>':'<i class="fad fa-chair-office fa-fw"></i>'):'').'</td>
							<td class="text-nowrap text-center">'.($lines[4]['col1']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[4]['col2']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[4]['col3']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[4]['col4']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[4]['col5']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[4]['col6']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[4]['col7']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[4]['col8']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[4]['col9']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[4]['col10']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[4]['col11']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[4]['col12']??'-').'</td>
						</tr>
						<tr>
							<td class="text-nowrap">Jeudi '.$thursday.'</td>
							<td class="text-center">'.(isset($lines[5])?($lines[5]["att_driver"]==1?'<i class="fad fa-steering-wheel fa-fw"></i>':'<i class="fad fa-chair-office fa-fw"></i>'):'').'</td>
							<td class="text-nowrap text-center">'.($lines[5]['col1']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[5]['col2']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[5]['col3']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[5]['col4']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[5]['col5']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[5]['col6']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[5]['col7']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[5]['col8']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[5]['col9']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[5]['col10']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[5]['col11']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[5]['col12']??'-').'</td>
						</tr>
						<tr>
							<td class="text-nowrap">Vendredi '.$friday.'</td>
							<td class="text-center">'.(isset($lines[6])?($lines[6]["att_driver"]==1?'<i class="fad fa-steering-wheel fa-fw"></i>':'<i class="fad fa-chair-office fa-fw"></i>'):'').'</td>
							<td class="text-nowrap text-center">'.($lines[6]['col1']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[6]['col2']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[6]['col3']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[6]['col4']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[6]['col5']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[6]['col6']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[6]['col7']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[6]['col8']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[6]['col9']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[6]['col10']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[6]['col11']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[6]['col12']??'-').'</td>
						</tr>
						<tr>
							<td class="text-nowrap">Samedi '.$saturday.'</td>
							<td class="text-center">'.(isset($lines[7])?($lines[7]["att_driver"]==1?'<i class="fad fa-steering-wheel fa-fw"></i>':'<i class="fad fa-chair-office fa-fw"></i>'):'').'</td>
							<td class="text-nowrap text-center">'.($lines[7]['col1']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[7]['col2']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[7]['col3']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[7]['col4']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[7]['col5']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[7]['col6']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[7]['col7']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[7]['col8']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[7]['col9']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[7]['col10']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[7]['col11']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[7]['col12']??'-').'</td>
						</tr>
						<tr>
							<td class="text-nowrap">Dimanche '.$sunday.'</td>
							<td class="text-center">'.(isset($lines[1])?($lines[1]["att_driver"]==1?'<i class="fad fa-steering-wheel fa-fw"></i>':'<i class="fad fa-chair-office fa-fw"></i>'):'').'</td>
							<td class="text-nowrap text-center">'.($lines[1]['col1']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[1]['col2']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[1]['col3']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[1]['col4']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[1]['col5']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[1]['col6']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[1]['col7']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[1]['col8']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[1]['col9']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[1]['col10']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[1]['col11']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[1]['col12']??'-').'</td>
						</tr>
					</tbody>
				</table>
			</div>
			';
			
			// *** Div object ***
			$div = new div();
			// info : div->create(divClass, elem1[, elem2[, elem3[, ...]]])
			$divs = $div->create("row", $infoWorker);
			
			// *** Button object ***
			$button = new button($this->table, $this->idrecord, $this->action);
			// info : button->create(type[, buttonClass])
			$btn_close 	= $button->create("close");
			$btn_pdf	= "";
			// ex : $btn_pdf	= $button->create("pdf");
			
			// *** Form object ***
			$form = new form();
			// info : form->create("normal/upload", formId, formClass, elem1[, elem2[, elem3[, ...]]])
			$form = $form->create("normal", $this->table."_form", "", $infoWorker, $infoWeek);
			// *** Body variable ***
			// info : $body = $input[.$div][.$form][.tabs]
			$body = $form;
			
			// *** Title variable ***
			$title = '
				<div class="d-flex flex-row justify-content-between">
					<div>'.$this->picto.' Fiche de pointage hebdomadaire</div>
					<div class="ml-3">'.ucfirst($month).' - Semaine du '.$monday.' au '.$sunday.'</div>
				</div>
			';
			
			// *** Page object ***
			// info : new page(type, id, label, pageClass[or colspan], body, buttons)
			$page = new page("modal", $this->table."_modal", $title, "modal-xlg", $body, $btn_close);
			// ex : $page = new page("tr", $this->table."_tr_".$this->idrecord, "", count($this->columns)+1, $body, $btn_save.$btn_cancel.$btn_delete);
			$this->json['html'] = $page->create(array($this->values->insert_date??date("y-m-d"), $this->values->update_date??date("y-m-d")));
		}
		
		public function v_createWeekCardWorker($tim_weekly, $year, $week)
		{
			$lines = array();
			foreach($this->values as $line){
				$lines[$line['numday']] = $line;
			}
			$new_date = new DateTime();
			$new_date->setISODate($year,$week);
			$month = $new_date->format('F Y');
			$month = ucfirst(IntlDateFormatter::formatObject($new_date, 'MMM y', 'fr'));
			$monday = $new_date->format('d/m');
			$new_date->modify('tuesday this week');
			$tuesday = $new_date->format('d/m');
			$new_date->modify('wednesday this week');
			$wednesday = $new_date->format('d/m');
			$new_date->modify('thursday this week');
			$thursday = $new_date->format('d/m');
			$new_date->modify('friday this week');
			$friday = $new_date->format('d/m');
			$new_date->modify('saturday this week');
			$saturday = $new_date->format('d/m');
			$new_date->modify('sunday this week');
			$sunday = $new_date->format('d/m');
			
			$new_date->setISODate($year,$week);
			$new_date->sub(new DateInterval('P7D'));
			$yearBefore = $new_date->format('Y');
			$weekBefore = $new_date->format('W');
			
			$new_date->setISODate($year,$week);
			$new_date->add(new DateInterval('P7D'));
			$yearAfter = $new_date->format('Y');
			$weekAfter = $new_date->format('W');
			//$date->sub(new DateInterval('P10D'));
			
			$infoWeek = '
			<div class="table-responsive">
				<table class="table table-striped table-borderless table-sm">
					<thead class="thead-light" style="font-size:.8rem;">
						<tr>
							<th>JOURS</th>
							<th style="max-width:24px;"></th>
							<th class="text-break text-center" style="vertical-align: top;"><span class="badge badge-pill badge-secondary">1</span><br>Heure arrivée entreprise</th>
							<th class="text-break text-center" style="vertical-align: top;"><span class="badge badge-pill badge-secondary">2</span><br>Heure arrivée chantier</th>
							<th class="text-break text-center" style="vertical-align: top;"><span class="badge badge-pill badge-secondary">3</span><br>Durée conduite matin</th>
							<th class="text-break text-center" style="vertical-align: top;"><span class="badge badge-pill badge-secondary">4/5</span><br>Heure debut pause déjeuner</th>
							<th class="text-break text-center" style="vertical-align: top;"><span class="badge badge-pill badge-secondary">5/6</span><br>Heure fin pause déjeuner</th>
							<th class="text-break text-center" style="vertical-align: top;"><span class="badge badge-pill badge-secondary">7</span><br>Heure depart chantier</th>
							<th class="text-break text-center" style="vertical-align: top;"><span class="badge badge-pill badge-secondary">8</span><br>Heure retour entreprise</th>
							<th class="text-break text-center" style="vertical-align: top;"><span class="badge badge-pill badge-secondary">9</span><br>Durée conduite après-midi</th>
							<th class="text-break text-center" style="vertical-align: top;"><span class="badge badge-pill badge-secondary">10</span><br>Total heures travaillées</th>
							<th class="text-break text-center" style="vertical-align: top;"><span class="badge badge-pill badge-secondary">11</span><br>Total durée conduite</th>
							<th class="text-break text-center" style="vertical-align: top;"><span class="badge badge-pill badge-secondary">12</span><br>Total heures prestées</th>
							<th class="text-nowrap text-center" style="vertical-align: top;"><span class="badge badge-pill badge-secondary">13</span><br>Zone</th>
						</tr>
					</thead>
					<tbody style="font-size:1rem;">
						<tr>
							<td class="text-nowrap">Lundi '.$monday.'</td>
							<td class="text-center">'.(isset($lines[2])?($lines[2]["att_driver"]==1?'<i class="fad fa-steering-wheel fa-fw"></i>':'<i class="fad fa-chair-office fa-fw"></i>'):'').'</td>
							<td class="text-nowrap text-center">'.($lines[2]['col1']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[2]['col2']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[2]['col3']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[2]['col4']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[2]['col5']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[2]['col6']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[2]['col7']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[2]['col8']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[2]['col9']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[2]['col10']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[2]['col11']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[2]['col12']??'-').'</td>
						</tr>
						<tr>
							<td class="text-nowrap">Mardi '.$tuesday.'</td>
							<td class="text-center">'.(isset($lines[3])?($lines[3]["att_driver"]==1?'<i class="fad fa-steering-wheel fa-fw"></i>':'<i class="fad fa-chair-office fa-fw"></i>'):'').'</td>
							<td class="text-nowrap text-center">'.($lines[3]['col1']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[3]['col2']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[3]['col3']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[3]['col4']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[3]['col5']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[3]['col6']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[3]['col7']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[3]['col8']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[3]['col9']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[3]['col10']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[3]['col11']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[3]['col12']??'-').'</td>
						</tr>
						<tr>
							<td class="text-nowrap">Mercredi '.$wednesday.'</td>
							<td class="text-center">'.(isset($lines[4])?($lines[4]["att_driver"]==1?'<i class="fad fa-steering-wheel fa-fw"></i>':'<i class="fad fa-chair-office fa-fw"></i>'):'').'</td>
							<td class="text-nowrap text-center">'.($lines[4]['col1']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[4]['col2']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[4]['col3']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[4]['col4']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[4]['col5']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[4]['col6']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[4]['col7']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[4]['col8']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[4]['col9']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[4]['col10']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[4]['col11']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[4]['col12']??'-').'</td>
						</tr>
						<tr>
							<td class="text-nowrap">Jeudi '.$thursday.'</td>
							<td class="text-center">'.(isset($lines[5])?($lines[5]["att_driver"]==1?'<i class="fad fa-steering-wheel fa-fw"></i>':'<i class="fad fa-chair-office fa-fw"></i>'):'').'</td>
							<td class="text-nowrap text-center">'.($lines[5]['col1']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[5]['col2']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[5]['col3']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[5]['col4']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[5]['col5']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[5]['col6']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[5]['col7']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[5]['col8']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[5]['col9']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[5]['col10']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[5]['col11']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[5]['col12']??'-').'</td>
						</tr>
						<tr>
							<td class="text-nowrap">Vendredi '.$friday.'</td>
							<td class="text-center">'.(isset($lines[6])?($lines[6]["att_driver"]==1?'<i class="fad fa-steering-wheel fa-fw"></i>':'<i class="fad fa-chair-office fa-fw"></i>'):'').'</td>
							<td class="text-nowrap text-center">'.($lines[6]['col1']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[6]['col2']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[6]['col3']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[6]['col4']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[6]['col5']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[6]['col6']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[6]['col7']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[6]['col8']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[6]['col9']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[6]['col10']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[6]['col11']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[6]['col12']??'-').'</td>
						</tr>
						<tr>
							<td class="text-nowrap">Samedi '.$saturday.'</td>
							<td class="text-center">'.(isset($lines[7])?($lines[7]["att_driver"]==1?'<i class="fad fa-steering-wheel fa-fw"></i>':'<i class="fad fa-chair-office fa-fw"></i>'):'').'</td>
							<td class="text-nowrap text-center">'.($lines[7]['col1']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[7]['col2']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[7]['col3']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[7]['col4']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[7]['col5']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[7]['col6']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[7]['col7']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[7]['col8']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[7]['col9']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[7]['col10']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[7]['col11']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[7]['col12']??'-').'</td>
						</tr>
						<tr>
							<td class="text-nowrap">Dimanche '.$sunday.'</td>
							<td class="text-center">'.(isset($lines[1])?($lines[1]["att_driver"]==1?'<i class="fad fa-steering-wheel fa-fw"></i>':'<i class="fad fa-chair-office fa-fw"></i>'):'').'</td>
							<td class="text-nowrap text-center">'.($lines[1]['col1']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[1]['col2']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[1]['col3']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[1]['col4']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[1]['col5']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[1]['col6']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[1]['col7']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[1]['col8']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[1]['col9']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[1]['col10']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[1]['col11']??'-').'</td>
							<td class="text-nowrap text-center">'.($lines[1]['col12']??'-').'</td>
						</tr>
					</tbody>
				</table>
			</div>
			';

			if($tim_weekly->tim_signature == ""){
				$signature = '
					<div id="signature-pad" class="signature-pad absolute-center shadow-lg" style="display:none;">
						<div class="signature-pad--body">
							<canvas></canvas>
						</div>
					</div>
				';
				$btn_sign = '
					<button id="btn_saveSignature" type="button" class="btn btn-save btn-lg" onclick="worker_action({action:\'saveSignature\', idrecord:\''.encrypt($_SESSION['iduser']).'\', tim_week:'.$week.$year.'})" style="visibility:hidden;" data-loading-text="<i class=\'fal fa-spinner fa-pulse fa-fw mr-2\'></i>'.gettext("En cours...").'"><i class="fal fa-save fa-2x mr-2"></i>'.gettext("Enregistrer").'</button>
					<button id="btn_showSignature" type="button" class="btn btn-client-light btn-lg" onclick="worker_action({action:\'showSignature\'})"><i class="fal fa-file-signature fa-2x mr-2"></i>'.gettext("Signer pour accord").'</button>
					<button id="btn_print" type="button" class="btn btn-client-light btn-lg" onclick="worker_action({action:\'print\'})" style="display:none;"><i class="fal fa-print fa-2x mr-2"></i>'.gettext("Imprimer").'</button>
				';
			}else{
				$preview = imgExist("upload/signatures/", $tim_weekly->tim_signature, "images/empty.png");
				$signature = '
					<div class="d-flex flex-row justify-content-between align-items-end">
						<div class="signature">
							<img src="'.$preview.'">
							<div class="signature-del bg-red" onclick="worker_action({action:\'deleteSignature\'})"><i class="fal fa-trash fa-2x"></i></div>
						</div>
						<button id="btn_print" type="button" class="btn btn-client-light btn-lg" onclick="worker_action({action:\'print\'})"><i class="fal fa-print fa-2x mr-2"></i>'.gettext("Imprimer").'</button>
					</div>
				';
				$btn_sign = '';
			}
			
			// *** Form object ***
			$form = new form();
			// info : form->create("normal/upload", formId, formClass, elem1[, elem2[, elem3[, ...]]])
			$form = $form->create("normal", $this->table."_form", "", $infoWeek,$signature);
			// *** Body variable ***
			// info : $body = $input[.$div][.$form][.tabs]
			$body = $form;
			
			$title = '
				<div id="title" class="d-flex justify-content-between align-items-center">
					<h3 class="m-0">
						<i class="fal fa-stopwatch mr-2"></i>'.gettext("Fiche de pointage hebdomadaire").' - '.ucfirst($month).'
					</h3>
					<div class="btn-group" role="group">
						<button type="button" onclick="worker_action({action:\'showWeekCard\',idrecord:\''.encrypt($_SESSION['iduser']).'\',year:'.$yearBefore.',week:'.$weekBefore.'})" class="btn btn-outline-client mr-1">
							<i class="fal fa-chevron-left fa-fw"></i>
						</button>
						<button type="button" class="btn btn-outline-client">
							<span id="weekInfo" data-week="'.$week.'" data-year="'.$year.'">Semaine du '.$monday.' au '.$sunday.'</span>
						</button>
						<button type="button" onclick="worker_action({action:\'showWeekCard\',idrecord:\''.encrypt($_SESSION['iduser']).'\',year:'.$yearAfter.',week:'.$weekAfter.'})" class="btn btn-outline-client ml-1">
							<i class="fal fa-chevron-right fa-fw"></i>
						</button>
					</div>
				</div>
			
			';
			
			// *** Page object ***
			// info : new page(type, id, label, pageClass[or colspan], body, buttons)
			$page = new page("card", $this->table."_modal", $title, "", $body, $btn_sign);
			// ex : $page = new page("tr", $this->table."_tr_".$this->idrecord, "", count($this->columns)+1, $body, $btn_save.$btn_cancel.$btn_delete);
			$this->json['html'] = $page->create(array($this->values->insert_date??date("y-m-d"), $this->values->update_date??date("y-m-d")));
		}
		
		public function v_showTimeSheetHome()
		{
			$location = $_COOKIE['location'] ?? "out";

			if(empty($this->values)){ // No att_in
				$inDisabled = "";
				$inMessage = gettext("Pointage - Début");
				$idatt = 0;
				$outMessage = gettext("Pointage - Fin");
				$outDisabled = "disabled";
			}else{ // att_in
				$inDisabled = "disabled";
				$inMessage = gettext("Pointage - Début le ").alterData("date-be", $this->values[0]['att_in']).gettext(" à ").alterData("small-time", $this->values[0]['att_in']);
				$idatt = encrypt($this->values[0]['idattendance']);
				if(empty($this->values[0]['att_out'])){ // No att_out
					$outMessage = gettext("Pointage - Fin");
					$outDisabled = "";
				}else{ // att_out => no click
					$duration = substr($this->values[0]['att_duration_day'],0,5)."/j - ".substr($this->values[0]['att_duration_night'],0,5)."/n";
					$outMessage = gettext("Pointage - Fin le ").alterData("date-be", $this->values[0]['att_out']).gettext("  à ").alterData("small-time", $this->values[0]['att_out'])."<br>Durée : ".$duration;
					$outDisabled = "disabled";
				}
				
			}
			
			$html = '
			<div class="bg-dark text-white w-100 p-3 text-center" style="font-size:1.5rem;" id="title">'.gettext("Mon emploi du temps").'</div>
			<div class="d-flex flex-column align-content-around" id="menu_timesheet">
			';
			if($location == "office"){
				$html .= '
				<div class="p-3 mt-3">
					<button type="button" id="att_start" class="btn btn-success btn-lg btn-block" onclick="worker_action({action:\'selectVehicle\'})" '.$inDisabled.'>
						<i class="fal fa-stopwatch mr-3 fa-2x align-middle"></i>'.$inMessage.'
					</button>
				</div>
				';
			}
			$html .= '
				<div class="p-3 d-flex flex-row">
					<button type="button" id="timesheet_edit" class="btn btn-client-light btn-lg flex-fill" onclick="worker_action({action:\'editTimesheet\',idrecord:\''.$idatt.'\'})" '.$outDisabled.' autocomplete="off" data-loading-text="<i class=\'fas fa-spinner fa-spin mr-2\'></i>En cours...">
						<i class="fal fa-clock mr-3 fa-2x align-middle"></i>'.gettext("Encoder").'
					</button>
					<button type="button" id="timesheet_show" class="btn btn-info btn-lg flex-fill" onclick="worker_action({action:\'showWeekCard\',idrecord:\''.encrypt($_SESSION['iduser']).'\',year:'.date("Y").',week:'.date("W").'})" autocomplete="off" data-loading-text="<i class=\'fas fa-spinner fa-spin mr-2\'></i>En cours..." disabled>
						<i class="fas fa-tasks mr-3"></i>'.gettext("Consulter et signer").'
					</button>
				</div>
				
			';
			if($location == "office"){
				$html .= '
				<div class="p-3">
					<button type="button" id="att_stop" class="btn btn-danger btn-lg btn-block" onclick="worker_action({action:\'stopTimer\',idrecord:\''.$idatt.'\'})" '.$outDisabled.'>
						<i class="fal fa-stopwatch mr-3 fa-2x align-middle"></i>'.$outMessage.'
					</button>
				</div>
				';
			}
			$html .= '
			</div>
			';
			$this->json['html'] = $html;
		}
		
		public function v_showVehicleSelect($vehicles)
		{
			$html = '
			<div class="bg-dark text-white w-100 p-3 text-center" style="font-size:1.5rem;" id="title">
				'.gettext("Mon véhicule").'
			</div>
			<div class="container row-content mx-auto my-5" id="vehicle">
				<ul class="list-group">
			';
			foreach($vehicles->values as $vehicle){
				$html .= '
					<li class="list-group-item d-flex flex-row justify-content-between">
						<div class="d-flex flex-row">
							<i class="fal fa-'.$vehicles->veh_type_fa[$vehicle['veh_type']].' fa-fw mr-2 fa-2x"></i>
							<div class="text-center mr-2" style="width:150px;">
								<img class="brand-logo" src="upload/brandLogo/'.($vehicle['bra_logo']??"none.png").'">
							</div>
							<div class="" style="width:150px;line-height: 50px;">
								<strong>'.$vehicle['veh_model'].'</strong>
							</div>
							<div class="veh-plate d-flex flex-row align-items-center pr-3 text-monospace">
								<img class="mr-2" src="images/plate_fr.png">
								<strong>'.$vehicle['veh_numberplate'].'</strong>
							</div>
						</div>
						<div class="d-flex flex-row">
							<button type="button" onclick="worker_action({action:\'startTimer\',idvehicle:'.$vehicle['idvehicle'].',att_driver:1})" class="btn btn-outline-dark ml-3 btn-rounded"><i class="fad fa-steering-wheel mr-2"></i>Chauffeur</button>
							<button type="button" onclick="worker_action({action:\'startTimer\',idvehicle:'.$vehicle['idvehicle'].',att_driver:0})" class="btn btn-outline-secondary ml-3 btn-rounded"><i class="fad fa-chair-office mr-2"></i>Passager</button>
						</div>
					</li>
				';
			}
			$html .= '
					<button type="button" onclick="worker_action({action:\'startTimer\',idvehicle:0,att_driver:0})" class="list-group-item list-group-item-action list-group-item-light d-flex flex-row justify-content-center align-items-center">
						<i class="fal fa-parking-circle-slash fa-fw mr-4 fa-2x"></i>
						<strong>'.gettext("Aucun").'</strong>
					</button>
				</ul>
			</div>
			';
			$this->json['html'] = $html;
		}
		
		public function v_showMaterialSelect($materials)
		{
			$html = '
			<div class="bg-dark text-white w-100 p-3 text-center" style="font-size:1.5rem;" id="title">
				Mes machines
			</div>
			<div class="container row-content mx-auto my-5" id="material">
				<ul class="list-group">
					<button type="button" onclick="worker_action({action:\'startTimer\',idvehicle:'.$this->dataSent['idvehicle'].',att_driver:'.$this->dataSent['att_driver'].'})" class="list-group-item list-group-item-action list-group-item-primary d-flex flex-row justify-content-center align-items-center">
						<i class="fal fa-empty-set fa-fw mr-4 fa-2x color2"></i>
						<strong>Pas besoin de matériel</strong>
					</button>
			';
			foreach($materials->values as $material){
				$html .= '
					<li onclick="$(this).toggleClass(\'check-active\')" class="list-group-item list-group-item-action d-flex flex-row align-items-center" data-idarticle="'.$material['idarticle'].'">
						<i class="fal fa-square fa-fw mr-2 fa-2x"></i>
						<strong>'.$material['art_description'].'</strong>
						<small class="ml-3">'.$material['art_code'].'</small>
					</li>
				';
			}
			$html .= '
					<button type="button" onclick="worker_action({action:\'startTimer\',idvehicle:'.$this->dataSent['idvehicle'].',att_driver:'.$this->dataSent['att_driver'].'})" class="list-group-item list-group-item-action list-group-item-success d-flex flex-row justify-content-center align-items-center">
						<i class="fal fa-check-circle fa-fw mr-4 fa-2x color2"></i>
						<strong>Matériel.s sélectionné.s</strong>
					</button>
				</ul>
			</div>
			';
			$this->json['html'] = $html;
		}
		
		public function __destruct()
		{
		}
	}