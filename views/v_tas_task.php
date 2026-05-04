<?php
/**
*** Juillet 2019@SoluFile 
**/
	require_once (dirname(__FILE__)."/../models/m_tas_task.php");
	
	class tas_task_view extends tas_task_model {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->columns = array(
				array("field"=>"idjob", "alter"=>"", "title"=>html("Chantier"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				array("field"=>"idtastype", "alter"=>"", "title"=>html("Type"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				array("field"=>"tas_name", "alter"=>"", "title"=>html("Description"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				array("field"=>"tas_status", "alter"=>"", "title"=>html("Statut"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				array("field"=>"tas_date_begin", "alter"=>"", "title"=>html("Date début"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				array("field"=>"tas_date_end", "alter"=>"", "title"=>html("Date fin"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				array("field"=>"tas_duration_days", "alter"=>"", "title"=>html("Heure début"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				array("field"=>"tas_deadline", "alter"=>"", "title"=>html("Heure fin"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				array("field"=>"tas_order", "alter"=>"", "title"=>html("Ordre"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				array("field"=>"tas_block", "alter"=>"", "title"=>html("Bloquant"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				array("field"=>"tas_remark", "alter"=>"", "title"=>html("Commentaire"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				
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
			$param = array("height"=>"auto", "show-columns"=>"true", "pagination"=>"false", "search"=>true, "classes"=>"table table-no-bordered  table-hover table-light", "striped"=>"false", "new-record"=>"true", "export-table"=>"true", "filter-table"=>"");
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
			// info : new table($this->table,level[,checkbox(true/false)][,action(true/false)])
			$table = new table($this->table, $this->level, false, true);
			// *** create columns title ***
			$thead = $table->createTableHead($this->columns,"thead-light");
			// *** create table lines ***
			$tbody = $table->createTableBody($this->key, $this->values, $this->color_conds);
			// *** create add line ***
			$tfoot = $table->createTableFoot(count($this->columns), $idparent);
			
			// *** create table ***
			$param = array("height"=>"auto", "show-columns"=>"false", "pagination"=>"false", "search"=>"false", "new-record"=>"false", "classes"=>"table table-bordered table-sm", "striped"=>"false", "export-table"=>"false", "filter-table"=>"");
			$this->json['html'] = $table->createTableLine($param, $thead, $tbody, $tfoot);
		}
		
		public function v_createListGroup($idparent)
		{
			// *** Listgroup object ***
			// info : new listgroup($this->table,level)
			$listgroup = new listgroup($this->table, $this->level);
			// *** create list lines ***
			foreach($this->values as $item)
			{
				$listgroup->createItem(encrypt($item['idtask']), $this->v_createContent($item));
			}
			$this->json['html'] = $listgroup->createList($idparent);
		}
		
		public function v_createListItem($createItem=false)
		{
			// *** Listgroup object ***
			// info : new listgroup($this->table,level)
			$listgroup = new listgroup($this->table, $this->level);
			// *** create list line ***
			$item = $this->values[0];
			if($createItem){
				$this->json['html'] = $listgroup->createItem(encrypt($item['idtask']), $this->v_createContent($item));
			}else{
				$this->json['html'] = $this->v_createContent($item);
			}
		}
		
		public function v_createContent($item)
		{
			switch($item['tas_status']){
				case 1 : $date = '<small class="text-muted">'.alterData("date-be",$item['tas_date_begin']).'</small><i class="fas fa-hourglass-start ml-3 text-warning"></i>'; break;
				case 2 : $date = '<small class="text-muted">'.alterData("date-be",$item['tas_date_begin']).'</small><i class="fas fa-hourglass-half ml-3 text-info"></i>'; break;
				case 3 : $date = '<small class="text-muted">'.alterData("date-be",$item['tas_date_begin']).'</small><i class="far fa-hourglass ml-3 text-danger"></i>'; break;
				case 4 : $date = '<small class="text-muted">'.alterData("date-be",$item['tas_date_end']).'</small><i class="fas fa-hourglass-end ml-3 text-success"></i>'; break;
				case 5 : $date = '<small class="text-muted">'.alterData("date-be",$item['tas_date_end']).'</small><i class="fas fa-calendar-check ml-3 text-success"></i>'; break;
				case 9 : $date = '<small class="text-muted">'.alterData("date-be",$item['tas_date_end']).'</small><i class="fas fa-ban ml-3 text-danger"></i>'; break;
			}
			$content= '	<div class="col-md-1 text-center"><i class="fas fa-arrows-alt handle"></i></div>
						<div class="col-md-8"><span class="font-weight-bolder">'.$item['tas_name'].'</span><small class="ml-2">('.$item['tas_duration'].' h)</small></div>
			            <div class="col-md-3 text-right">'.$date.'</div>
			';
			
			return $content;
		}
		
		public function v_createCard($doc_document="")
		{
			// *** Input object ***
			$input = new input($this->values, $this->cryptfields);
			// info : input->create(type, label, name, mandatory(true/false)[, defaultValue[, divSize[, inputClass[, info(array)]]]])
			// info : defaultValue = $this->dataSent['newValue']??"" for newFrom
			$inputs = $input->create("hidden", "Job", "idjob", false, "");
			$inputs .= $input->create("text", "Description", "tas_name", true, "", "col-md-6");
			//$inputs .= $input->create("select", "Type", "idtastype", false, "", "col-md-3", "", array("idlist"=>"1","table"=>"tas_type","search"=>"true","filterChild"=>null));
			$inputs .= $input->create("select", "Statut", "tas_status", false, "", "col-md-3", "", array("contents"=>$this->tas_status));
			$inputs .= $input->breakline;
			$inputs .= $input->create("date", "Date début", "tas_date_begin", true, "", "col-md-2", "", array("horizontal"=>"left"));
			$inputs .= $input->create("date", "Date butoir", "tas_deadline", false, "", "col-md-2");
			$inputs .= $input->create("date", "Date fin", "tas_date_end", false, "", "col-md-2");
			$inputs .= $input->create("text", "Jours prévus", "tas_duration_days", false, "", "col-md-2");
			$inputs .= $input->create("text", "Hommes prévus", "tas_men_needed", false, "", "col-md-2");
			$inputs .= $input->create("text", "Heures prévues", "tas_duration", true, "", "col-md-2", "", array("max"=>6));
			//$inputs .= $input->create("checkbox", "Compagnons", "idworker", false, "", "col-md-12", " no-line", array("idlist"=>"2","table"=>"wor_worker"));
			$inputs .= $input->create("textarea", "Commentaire", "tas_remark", false, "", "col-md-6", "", array("row"=>1));
			$inputs .= $input->create("radio", "Métier", "idmetier", false, "", "col-md-6", "", array("idlist"=>"1","table"=>"met_metier","prefix"=>"task"));
			
			// *** Div object ***
			$div = new div();
			// info : div->create(divClass, elem1[, elem2[, elem3[, ...]]])
			$div1 = $div->create("row", $inputs);
			
			// *** related documents ***
			$div_doc = $doc_document !="" ? $div->create("row", $doc_document) : "";
			
			// *** Button object ***
			$button = new button($this->table, $this->idrecord, $this->action);
			// info : button->create(type[, buttonClass])
			$btn_save 	= $button->create("saveLine", "mr-4");
			$btn_close 	= $button->create("closeLine");
			$btn_delete = $button->create("deleteLine", "btn-left");
			$btn_pdf	= "";
			// ex : $btn_pdf	= $button->create("pdf");
			$btn_mail	= "";
			// ex : $btn_mail 	= $button->create("mail");
			
			// *** Form object ***
			$form = new form();
			// info : form->create("normal/upload", formId, formClass, elem1[, elem2[, elem3[, ...]]])
			$form1 = $form->create("normal", $this->table."_form", "", $div1, $div_doc, $btn_pdf, $btn_mail);
			
			// *** Tab object ***
			// ex : $tab = new tab(1);
			// info : tab->create(label, tabClass, form)
			// ex : $tabs = $tab->create("Onglet1", "", $form1);
			// ex : $tabs = $tab->create("Onglet2", "", $form2);
			
			// *** Body variable ***
			// info : $body = $input[.$div][.$form][.tabs]
			$body = $form1;
			
			// *** Title variable ***
			$title = $this->values->tas_name ?? gettext("Nouveau ".$this->label);
			// *** Page object ***
			// info : new page(type, id, label, pageClass, body, buttons)
			$page = new page("listgroup", $this->idrecord, $this->picto." ".$title, "", $body, $btn_save.$btn_close.$btn_delete);
			$this->json['html'] = $page->create(array($this->values->insert_date??date("y-m-d"), $this->values->update_date??date("y-m-d")));
		}
		
		public function v_createForWorker()
		{
			$html = '
			<div class="bg-dark text-white w-100 p-3 text-center" style="font-size:1.5rem;" id="title">Détail de mon emploi du temps</div>
			<div class="d-flex flex-column justify-content-center p-3" id="edit_timesheet">
			';
			if($this->att_driver > 0){
				$html .= '
				<div class="card border-danger mt-3" id="att_travel_time_div">
					<h5 class="card-header text-white bg-danger">Chauffeur</h5>
					<div class="card-body">
						<h5 class="card-title">Durée trajets</h5>
						<form class="form-inline" id="att_travel_time_form">
							<div class="input-group input-group-lg mr-3">
								<div class="input-group-prepend" id="button-addon1">
									<span class="input-group-text">Aller</span>
								</div>
								<input type="text" name="att_travel_time_going" id="att_travel_time_going" class="form-control clockpicker" placeholder="Durée" readonly="readonly">
							</div>
							
							<div class="input-group input-group-lg mr-3">
								<div class="input-group-prepend" id="button-addon2">
									<span class="input-group-text">Retour</span>
								</div>
								<input type="text" name="att_travel_time_coming" id="att_travel_time_coming" class="form-control clockpicker" placeholder="Durée" readonly="readonly">
							</div>
							
							<div class="btn-group btn-group-lg mr-3" role="group" aria-label="Basic example">
								<button class="btn btn-success" onclick="worker_action({action:\'saveAttTravelTime\',idrecord:\''.$this->idrecord.'\'})" type="button"><i class="fas fa-check"></i></button>
								<button class="btn btn-danger" onclick="worker_action({action:\'emptyAttTravelTime\'})" type="button"><i class="fas fa-undo-alt"></i></button>
							</div>
							
							<div class="alert alert-danger mt-2 mt-sm-0" role="alert" id="alert_att_travel_time" style="display:none;">
								<i class="fal fa-exclamation-triangle mr-2"></i>Veuillez indiquer une durée.
							</div>
						</form>
					</div>
				</div>
				';
			}
			foreach($this->values as $res){
				$html .= '
				<div class="card border-dark mt-3" id="div_task_'.$res['idtask'].'">
					<h5 class="card-header text-white bg-info">'.$res['cli_name'].' - '.$res['sit_name'].' - '.$res['job_reference'].' - '.$res['job_name'].'</h5>
					<div class="card-body">
						<h5 class="card-title">'.$res['tas_name'].'</h5>
						<form class="form-inline">
							<div class="input-group input-group-lg mr-3">
								<input type="text" name="tim_duration" id="tim_duration_'.$res['idtask'].'" class="form-control clockpicker" placeholder="Durée" readonly="readonly">
								<div class="input-group-append" id="button-addon4">
									<button class="btn btn-success" onclick="worker_action({action:\'createTimesheet\',idtask:\''.$res['idtask'].'\'})" type="button"><i class="fas fa-check"></i></button>
									<button class="btn btn-danger" onclick="worker_action({action:\'emptyTimeDuration\',idtask:\''.$res['idtask'].'\'})" type="button"><i class="fas fa-undo-alt"></i></button>
								</div>
							</div>
							<div class="alert alert-danger mt-2 mt-sm-0" role="alert" id="alert_'.$res['idtask'].'" style="display:none;">
								<i class="fal fa-exclamation-triangle mr-2"></i>Veuillez indiquer une durée.
							</div>
						</form>
					</div>
				</div>
				';
			}
			
			$html .='
			</div>
			';
			$this->json['html'] = $html;
		}
		
		public function v_selectClient()
		{
			$html = '
			<div class="bg-dark text-white w-100 p-3 text-center" style="font-size:1.5rem;" id="title">Détail de mon emploi du temps</div>
			<div class="d-flex flex-column justify-content-center p-3" id="edit_timesheet">
			';
			/*
			if($this->att_driver > 0){
				$html .= '
				<div class="card border-danger mb-3" id="att_travel_time_div">
					<h5 class="card-header text-white bg-danger">Chauffeur</h5>
					<div class="card-body">
						<h5 class="card-title">Durée trajets</h5>
						<form class="form-inline" id="att_travel_time_form">
							<div class="input-group input-group-lg mr-3">
								<div class="input-group-prepend" id="button-addon1">
									<span class="input-group-text">Aller</span>
								</div>
								<input type="text" name="att_travel_time_going" id="att_travel_time_going" class="form-control clockpicker" placeholder="Durée" readonly="readonly">
							</div>
							
							<div class="input-group input-group-lg mr-3">
								<div class="input-group-prepend" id="button-addon2">
									<span class="input-group-text">Retour</span>
								</div>
								<input type="text" name="att_travel_time_coming" id="att_travel_time_coming" class="form-control clockpicker" placeholder="Durée" readonly="readonly">
							</div>
							
							<div class="btn-group btn-group-lg mr-3" role="group" aria-label="Basic example">
								<button class="btn btn-success" onclick="worker_action({action:\'saveAttTravelTime\',idrecord:\''.$this->idrecord.'\'})" type="button"><i class="fas fa-check"></i></button>
								<button class="btn btn-danger" onclick="worker_action({action:\'emptyAttTravelTime\'})" type="button"><i class="fas fa-undo-alt"></i></button>
							</div>
							
							<div class="alert alert-danger mt-2 mt-sm-0" role="alert" id="alert_att_travel_time" style="display:none;">
								<i class="fal fa-exclamation-triangle mr-2"></i>Veuillez indiquer une durée.
							</div>
						</form>
					</div>
				</div>
				';
			}
			*/
			$html .= $this->planning->json["html"];
			
			$html .= '
			<div class="bg-2 text-white w-100 p-3 my-3 text-center" style="font-size:1.5rem;" id="title">Autres chantiers<i class="fal fa-arrow-alt-down ml-2"></i></div>
			';
			
			foreach($this->values as $res){
				$html .= '
					<button onclick="worker_action({action:\'taskSelectSite\',idrecord:\''.encrypt($res['idclient']).'\'})" type="button" class="btn btn-outline-client-light btn-lg">'.$res['cli_name'].'</button>
				';
			}
			$html .='
			</div>
			';
			$this->json['html'] = $html;
		}
		
		public function v_selectSite()
		{
			$html = '
			<div class="bg-dark text-white w-100 p-3 text-center" style="font-size:1.5rem;" id="title">'.$this->values[0]['cli_name'].'</div>
			<div class="d-flex flex-column justify-content-center p-3" id="edit_timesheet">
			';
			foreach($this->values as $res){
				$html .= '
					<button onclick="worker_action({action:\'taskSelectTask\',idrecord:\''.encrypt($res['idsite']).'\'})" type="button" class="btn btn-outline-client-light btn-lg">'.$res['sit_name'].'</button>
				';
			}
			$html .='
			</div>
			';
			$this->json['html'] = $html;
		}
		
		public function v_selectTask()
		{
			$html = '
			<div class="bg-dark text-white w-100 p-3 text-center" style="font-size:1.5rem;" id="title">'.$this->values[0]['cli_name'].' - '.$this->values[0]['sit_name'].'</div>
			<div class="d-flex flex-column justify-content-center p-3" id="edit_timesheet">
			';
			foreach($this->values as $res){
				$html .= '
				<div class="card border-dark mt-3" id="div_task_'.$res['idtask'].'">
					<h5 class="card-header bg-light color1">'.$res['job_name'].'</h5>
					<div class="card-body">
						<h5 class="card-title">'.$res['tas_name'].'</h5>
						<form class="form-inline">
							<div class="input-group input-group-lg mr-3">
								<input type="text" name="tim_duration" id="tim_duration_'.$res['idtask'].'" class="form-control clockpicker" placeholder="Durée" readonly="readonly">
								<div class="input-group-append" id="button-addon4">
									<button class="btn btn-success" onclick="worker_action({action:\'createTimesheet\',idtask:\''.$res['idtask'].'\'})" type="button"><i class="fas fa-check"></i></button>
									<button class="btn btn-danger" onclick="worker_action({action:\'emptyTimeDuration\',idtask:\''.$res['idtask'].'\'})" type="button"><i class="fas fa-undo-alt"></i></button>
								</div>
							</div>
							<div class="alert alert-danger mt-2 mt-sm-0" role="alert" id="alert_'.$res['idtask'].'" style="display:none;">
								<i class="fal fa-exclamation-triangle mr-2"></i>Veuillez indiquer une durée.
							</div>
						</form>
					</div>
				</div>
				';
			}
			$html .='
			</div>
			';
			$this->json['html'] = $html;
		}
		
		public function v_taskFromPlanning()
		{
			$html = '';
			foreach($this->values as $res){
				$html .= '
				<div class="card border-dark mt-3" id="div_task_'.$res['idtask'].'">
					<h5 class="card-header bg-light color1">'.$res['cli_name'].' - '.$res['sit_name'].' - '.$res['job_name'].'</h5>
					<div class="card-body">
						<h5 class="card-title">'.$res['tas_name'].'</h5>
						<form class="form-inline">
							<div class="input-group input-group-lg mr-3">
								<input type="text" name="tim_duration" id="tim_duration_'.$res['idtask'].'" class="form-control clockpicker" placeholder="Durée" readonly="readonly">
								<div class="input-group-append" id="button-addon4">
									<button class="btn btn-success" onclick="worker_action({action:\'createTimesheet\',idtask:\''.$res['idtask'].'\'})" type="button"><i class="fas fa-check"></i></button>
									<button class="btn btn-danger" onclick="worker_action({action:\'emptyTimeDuration\',idtask:\''.$res['idtask'].'\'})" type="button"><i class="fas fa-undo-alt"></i></button>
								</div>
							</div>
							<div class="alert alert-danger mt-2 mt-sm-0" role="alert" id="alert_'.$res['idtask'].'" style="display:none;">
								<i class="fal fa-exclamation-triangle mr-2"></i>Veuillez indiquer une durée.
							</div>
						</form>
					</div>
				</div>
				';
			}
			$this->json['html'] = $html;
		}
		
		public function v_createDashboard()
		{
			$div = new div();
			$this->json['html'] = str_replace(array("\r\n\t", "\t"), '', $div->dashboard($this->values,"text-left"));
		}
		
		public function __destruct()
		{
		}
	}