<?php
/**
*** Octobre 2021@SoluFile 
**/
	require_once (dirname(__FILE__)."/../models/m_pla_planning.php");
	
	class pla_planning_view extends pla_planning_model {
		
		public function __construct()
		{
			parent::__construct();
			// sorter : sortDate / sortEuro
			$this->columns = array(
				array("field"=>"idworker", "alter"=>"", "title"=>html(""), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>""),
				array("field"=>"idjob", "alter"=>"", "title"=>html(""), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>""),
				array("field"=>"pla_date_begin", "alter"=>"", "title"=>html(""), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>""),
				array("field"=>"pla_date_end", "alter"=>"", "title"=>html(""), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>""),
				
			);
			$this->columns_pdf = array(
				//array("field"=>"", "alter"=>"", "title"=>html(""), "style"=>"", "class"=>""),
			);
			$this->color_conds = array(
				//array("column"=>"", "query"=>" == 0", "class"=>"td-warning")
			);
		}
		
		public function v_createWeek()
		{
			$html = '
			<div class="pt-3"></div>
			<div id="planning" class="p-3 bg-light">
				<div id="title" class="d-flex justify-content-between align-items-center">
					<h3 class="m-0">
						<i class="fal fa-calendar-week mr-2"></i>'.gettext("Planning Main d'oeuvre").'
					</h3>
					<div class="btn-group" role="group">
						<button type="button" onclick="table_action({tablename:\'pla_planning\', dataSent:\'prev\', action:\'pla_changeWeek\'})" class="btn btn-outline-client mr-1">
							<i class="fal fa-chevron-left fa-fw"></i>
						</button>
						<button type="button" class="btn btn-outline-client">
							<span id="weekInfo" data-week="'.$this->json['week'].'" data-year="'.$this->json['year'].'">'.$this->json['info'].'</span>
						</button>
						<button type="button" onclick="table_action({tablename:\'pla_planning\', dataSent:\'next\', action:\'pla_changeWeek\'})" class="btn btn-outline-client ml-1">
							<i class="fal fa-chevron-right fa-fw"></i>
						</button>
					</div>
				</div>
			';
			
			$html .= '
					<div class="th mt-3">
						<span>Compagnons</span>
			';
			
			$date = new DateTime();
			$today = new DateTime();
		    for($day=1; $day<8; $day++){
				$date->setTimestamp($this->{"day".$day});
				$interval = $date->diff($today);
				$html .= '<span class="'.($date->format('ymd')==$today->format('ymd')?"strong":"").'">'.ucfirst(IntlDateFormatter::formatObject($date, 'eeee dd-MMM', 'fr')).'</span>';
		    }
			$html .= '
					</div>
					<div id="weeks" style="overflow-y:scroll; height:calc(100vh - 230px);">
			';
			
			foreach($this->workers->values as $worker){
				$picture = imgExist("upload/pictures/", $worker['wor_picture'], "images/worker.png");
				$html .= '
					<div class="week">
						<div class="worker">
							<img src="'.$picture.'" class="mr-3"><small>('.$this->workers->wor_type[$worker['wor_type']].')</small>
							<div><strong>'.$worker['wor_name'].'</strong></div>
						</div>
					';
				for($day=1; $day<8; $day++){
					$dayClass = in_array(date("dm",$this->{"day".$day}), $this->holidays)?"nowork":"";
					$date = date("Y-m-d", $this->{"day".$day});
				
					$html .= '
						<div class="day '.$dayClass.'" data-date="'.$date.'" data-idworker="'.$worker['idworker'].'">
					';
					if(!empty($worker['day'.$day])){
						$lines = explode("£", $worker['day'.$day]);
						foreach($lines as $line){
							$info = explode("§", $line);
							$html .= '
								<button type="button" style="border-color:'.$worker['wor_color'].';" class="d-flex justify-content-between align-items-center btn btn-sm btn-block '.($info[2]>0?"btn-danger":($worker['wor_type']==1?"btn-outline-dark":"btn-outline-secondary")).'" data-idplanning="'.encrypt($info[1]).'">
									'.$info[0].'<span class="badge badge-secondary">'.$info[3].'</span>
								</button>
							';
						}
					}
					$html .= '
						</div>
					';
			    }
				$html .= '
					</div>';
			}
			$html .= '
					</div>
				</div>
			</div>
			';
			
			$this->json['html'] = $html;
		}
		
		public function v_createCard()
		{
			// *** Input object ***
			$input = new input($this->values, $this->cryptfields);
			// info : input->create(type, label, name, mandatory(true/false)[, defaultValue[, divSize[, inputClass[, info(array)]]]]) "none-result-text"=>""
			// info : defaultValue = $this->dataSent['newValue']??"" for newFrom !!! model_crud c_newFrom case
			// info : $inputs .= $input->breakline;
			$inputs = isset($this->idparent)?$input->create("hidden", "", $this->idparent, false, "", ""):"";
			$inputs .= $input->create("select", gettext("Compagnon"), "idworker", true, "", "col-md-9", "", array("idlist"=>"1","table"=>"wor_worker","search"=>"false", "disabled"=>true,"filterChild"=>null,"filtering"=>false));
			$inputs .= $input->create("select", gettext("Chantier"), "idjob", false, "", "col-md-12", "", array("idlist"=>"5","table"=>"job_job","search"=>"true","data"=>array("subtext"),"filterChild"=>null,"filtering"=>false));
			$inputs .= $input->create("select", gettext("Véhicule"), "idvehicle", false, "", "col-md-12", "", array("idlist"=>"3","table"=>"veh_vehicle","search"=>"false","filterChild"=>null,"filtering"=>false));
			$inputs .= $input->create("select", gettext("Absence"), "idabstype", false, "", "col-md-12", "", array("idlist"=>"1","table"=>"abs_type","search"=>"false","filterChild"=>null,"filtering"=>false));
			$inputs .= $input->create("date", gettext("Du"), "pla_date_begin", true, "", "col-md-4");
			$inputs .= $input->create("date", gettext("Au"), "pla_date_end", true, "", "col-md-4");
			
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
			$btn_delete = $button->create("deleteAllLevel", "btn-left btn-mod");
			
			// *** Form object ***
			$form = new form();
			// info : form->create("normal/upload", formId, formClass, elem1[, elem2[, elem3[, ...]]])
			$forms = $form->create("normal", $this->table."_form", "", $divs);

			$body = $forms;
			
			// *** Title variable ***
			if(decrypt($this->idrecord) > 0){
				$title = $this->values->wor_name;
			}else{
				$title = $this->newtext;
			}
			// *** Page object ***
			// info : new page(type, id, label, pageClass[or colspan], body, buttons)
			$page = new page("modal", $this->table."_modal", $this->picto." ".$title, "noscroll", $body, $btn_save.$btn_close.$btn_delete);
			// ex : $page = new page("tr", $this->table."_tr_".$this->idrecord, "", count($this->columns)+1, $body, $btn_save.$btn_cancel.$btn_delete);

			$this->json['html'] = $page->create(array($this->values->insert_date??date("y-m-d"), $this->values->update_date??date("y-m-d")));
		}

		public function v_showPlanningWorker()
		{
			$html = '
			<div class="bg-dark text-white w-100 p-3 text-center" style="font-size:1.5rem;" id="title">Mon planning</div>
			<div class="d-flex flex-column mt-3" id="planning">
			';
			foreach($this->values as $pla){
				$html .= '
				<div class="d-flex flex-row mb-2 line">
					<div class="d-flex flex-column flex-grow-1 p-2 justify-content-between">
						<div class="font-weight-bold">
							'.($pla['job_surname']??$pla['job_name']).'
						</div>
						<div class="small">
							'.$pla['sit_address_1'].'<br>'.$pla['sit_pc'].' '.$pla['sit_city'].'
						</div>
					</div>
					<div class="d-flex flex-column justify-content-between">
						<div class="dropzone" data-idjob="'.encrypt($pla['idjob']).'">
							<button type="button" class="btn btn-dark py-2 px-3 dz-message">
								<i class="fal fa-camera fa-2x"></i>
							</button>
						</div>
						<a class="btn btn-light py-2 px-3" href="https://www.google.com/maps/search/'.urlencode($pla['sit_address_1'].' '.$pla['sit_pc'].' '.$pla['sit_city']).'" target="_blank">
							<i class="fal fa-map-marker-alt fa-2x"></i>
						</a>
					</div>
				</div>
				';
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