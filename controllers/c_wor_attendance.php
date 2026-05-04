<?php
/**
*** Novembre 2019@SoluFile 
**/
	require_once (dirname(__FILE__)."/../views/v_wor_attendance.php");
	
	class wor_attendance extends wor_attendance_view {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->label 		= gettext("Pointage quotidien");
			$this->labels 		= gettext("Pointages quotidiens");
			$this->newtext		= gettext("Nouveau pointage");
			$this->picto 		= '<i class="fal fa-stopwatch"></i>';
			$this->level		= 1;
			
			$this->cryptfields	= array("");
			
			$this->lnk			= array("lnk_att_art"); // GROUP_CONCAT(idlnk) idlnk
			
			$this->wor_year		= array();
			$this->wor_week		= array();
			
			$this->ord_year		= array();
			$this->ord_week		= array();
			
			for ($y = 2020; $y <= date("Y"); $y++) {
				$this->wor_year[$y] = $y;
				$this->ord_year[$y] = $y;
			}
			
			$new_date = new DateTime();
			$year = date("Y");
			$numW = date("W", strtotime($year."-12-28"));

			for ($week = 1; $week <= $numW; $week++) {
				$new_date->setISODate($year,$week);
				$monday = $new_date->format('d/m');
				$new_date->modify('sunday this week');
				$sunday = $new_date->format('d/m');
				$this->wor_week[$week] = "Sem. ".$week." (".$monday." - ".$sunday.")";
				$this->ord_week[$week] = "Sem. ".$week." (".$monday." - ".$sunday.")";
			}
		}

		public function c_fullTable(){
			if($this->checkUserRight()){
				// *** model ***
				
				//$this->filters['wor_year'] = array("id"=>"idyear","in"=>array(),"label"=>"Année");
				//$this->filters['wor_week'] = array("id"=>"idweek","in"=>array(),"label"=>"Semaine");
				
				unset($this->filters['wor_year']);
				unset($this->filters['wor_week']);
				
				if(empty($this->dataSent)){
					$withFilter=false;
					$this->json['info']="false";
				}else{
					if(isset($this->dataSent['value'])){
						unset($this->dataSent['value']);
					}
					$withFilter=true;
					$this->json['info']="true";
				}
				if($this->m_getAll($withFilter)){
					// *** view ***
					$this->v_createFullTable();
					$this->json['code'] = 1;
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller fullTable', 200);
				}
			}
		}
		
		public function c_weekTable(){
			if($this->checkUserRight()){
				// *** model ***
				$year = $this->dataSent['idyear'] ?? date("Y");
				$week = $this->dataSent['idweek'] ?? date("W");
				//$this->debugging = true;
				if($this->m_getByWeek($year, $week)){
					//dump($this->values);
					// *** view ***
					$this->v_createStateTable();
					$this->json['code'] = "weekCreated";
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller fullTable', 200);
				}
			}
		}
		
		public function c_tableByParent(){
			if($this->checkUserRight()){
				// *** model ***
				$this->conds = array("idparentToChange = ".decrypt($this->idrecord));
				if($this->m_getAll()){
					// *** view ***
					$this->v_createLineTable($this->idrecord);
					$this->json['code'] = 1;
					return true;
				}else{
					throw new Exception('PHP : Error in controller tableByParent', 201);
				}
			}
		}
		
		public function c_newLine(){
			if($this->checkUserRight()){
				// *** model ***
				$this->m_newRecord();
				$this->values->idparentToChange = decrypt($this->idrecord);
				$this->values = (array) $this->values;
				if($this->m_insert(http_build_query($this->values))){
					$this->json['code'] = "newLineInserted";
					$this->json['idparent'] = encrypt($this->result['idparent']);
					$this->json['info'] = getText("Pointage ajouté");
					// *** view ***
					$this->values = array($this->values);
					$this->v_createTr();
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller newLine', 202);
				}
			}
		}

		public function c_newCardTable(){
			if($this->checkUserRight()){
				// *** model ***
				$this->m_newRecord();
				$this->values->{$this->idparent} = decrypt($this->idrecord);
				$this->idrecord = encrypt(0);
				// *** view ***
				$this->json['code'] = "newCardCreated";
				$this->v_createCard();
				return $this->json;
			}
		}

		public function c_editCardTable(){
			if($this->checkUserRight()){
				// *** model ***
				$res = $this->m_getById(decrypt($this->idrecord));
				$this->values = (object) $this->values[0];
				// *** view ***	
				if($res){
					$this->v_createCard();
					$this->json['code'] = "cardEdited";
				}else{
					throw new Exception('PHP : Error in controller editCardTable', 204);
				}
				return $this->json;
			}
		}
		
		public function c_cancelCardTable(){
			if($this->checkUserRight()){
				// *** model ***
				// if new record => empty
				if(decrypt($this->idrecord) == 0){
					$this->json['code'] = "newCardCanceled";
				}else{
					$res = $this->m_getById(decrypt($this->idrecord));
					//$this->values = (object) $this->values[0];
					// *** view ***	
					if($res){
						$this->v_createTr();
						$this->json['code'] = "cardUpdated";
					}else{
						throw new Exception('PHP : Error in controller cancelCardTable', 205);
					}
				}
				return $this->json;
			}
		}
		
		public function c_saveCardTable(){
			if($this->checkUserRight()){
				// *** model ***
				if(decrypt($this->idrecord) == 0){
					$this->m_insert($this->dataSent);
					$this->idrecord = encrypt($this->result['idparent']);
					$this->json['code'] = "cardInserted";
				}else{
					parse_str($this->dataSent, $data);
					$this->m_update(decrypt($this->idrecord), $data);
					$this->json['code'] = "cardUpdated";
				}
				// *** new data to update table ***
				if($this->m_getById(decrypt($this->idrecord))){
					// *** view ***
					$this->v_createTr();
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller saveCardTable', 206);
				}
			}
		}

		public function c_search(){
			if($this->checkUserRight()){
				// *** model ***
				$search = strtolower($this->dataSent)."%";
				$this->conds = array("LOWER(att_name) LIKE '$search'");
				$this->dataSent = array();
				if($this->m_getAll(false)){
					// *** view ***
					$this->v_createFullTable();
					$this->json['code'] = 1;
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller search', 210);
				}
			}
		}

		public function c_selectSearch(){
			if($this->checkUserRight()){
				// *** model ***
				$search = strtolower($this->dataSent)."%";
				$this->fields = $this->key." AS id, att_name AS val, '' AS tokens";
				$this->conds = array("LOWER(att_name) LIKE '$search'");
				$this->dataSent = array();
				if($this->m_getAll(false)){
					// *** view ***
					if($this->count > 0){
						$this->json['code'] = "results";
						$input = new input($this->values);
						$this->json['html'] = $input->create("option","","",false);
					}else{
						$this->json['code'] = "noResult";
						$this->json['html'] = gettext("Rien trouvé");
					}
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller searchSelect', 211);
				}
			}
		}

		public function c_showCard(){
			if($this->checkUserRight()){
				// *** model ***
				if($this->idrecord == "0"){
					$res = $this->m_newRecord(false);
					$this->values->idvehicle = 0;
					// info : if true => $this->idrecord = encrypt($this->result['idparent']);
				}else{
					$res = $this->m_getById(decrypt($this->idrecord));
					$this->values = (object) $this->values[0];
				}
				
				// *** doc_document ***
				$doc_document = new doc_document();
				//$doc_document->info = $this->table;
				//$doc_document->idrecord = $this->idrecord;
				//$res_document = $doc_document->c_selectByParent();
				$doc_document->json['html'] = "";

				// *** related timesheet *** //
				$tim_timesheet = new tim_timesheet();
				$tim_timesheet->idrecord = $this->values->idworker;
				$tim_timesheet->conds = array("tim_timesheet.idworker = ".$this->values->idworker,"tim_date = '".$this->values->att_date."'");
				$tim_timesheet->c_tableByParent();
				
				// *** related material *** //
				$art_article = new lnk_vehicle_article();
				if($this->values->idvehicle>0){
					$art_article->idrecord = encrypt($this->values->idvehicle);
					$art_article->conds = array("idvehicle = ".$this->values->idvehicle,"mvt_date_in >= '".$this->values->att_date."'", "(mvt_date_out <= '".$this->values->att_date."' OR mvt_date_out IS NULL)");
					$art_article->c_tableByParent();	
				}else{
					$art_article->json["html"] = "";
				}
				
				
				// *** view ***	
				if($res){
					$this->v_createCard($doc_document->json['html'],$tim_timesheet,$art_article);
					$this->json['code'] = 1;
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller showCard', 212);
				}
			}
		}
		
		public function c_weekCard(){
			if($this->checkUserRight()){
				// *** model ***
				$idworker = decrypt($this->idrecord);
				$worker = new wor_worker();
				$worker->m_getById($idworker);
				
				$year = $this->dataSent['idyear'] ?? date("Y");
				$week = $this->dataSent['idweek'] ?? date("W");
				
				$tim_weekly = new tim_weekly();
				$tim_weekly->conds = array("idworker = ".$idworker, "tim_week = ".$week.$year);
				$tim_weekly->select();
				if ($tim_weekly->count > 0){
					$tim_weekly = (object) $tim_weekly->values[0];
				}else{
					$tim_weekly->m_newRecord();
					$tim_weekly = $tim_weekly->values;
				}

				//$this->debugging = true;
				if($this->m_getByWorkerWeek($idworker, $year, $week)){
					//var_dump($this->values);exit;
					$this->v_createWeekCard($worker, $tim_weekly, $year, $week);
					$this->json['code'] = "weekCardCreated";
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller showCard', 212);
				}
			}
		}
		
		public function c_weekCardWorker(){
			if($this->checkUserRight()){
				// *** model ***
				$idworker = decrypt($this->idrecord);
				$year = $this->dataSent['idyear'] ?? date("Y");
				$week = $this->dataSent['idweek'] ?? date("W");

				$tim_weekly = new tim_weekly();
				$tim_weekly->conds = array("idworker = ".$idworker, "tim_week = ".$week.$year);
				$tim_weekly->select();
				if ($tim_weekly->count > 0){
					$tim_weekly = (object) $tim_weekly->values[0];
				}else{
					$tim_weekly->m_newRecord();
					$tim_weekly = $tim_weekly->values;
				}
				
				//$this->debugging = true;
				if($this->m_getByWorkerWeek($idworker, $year, $week)){
					//var_dump($this->values);exit;
					$this->v_createWeekCardWorker($tim_weekly, $year, $week);
					$this->json['code'] = "weekCardWorkerCreated";
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller showCard', 212);
				}
			}
		}

		public function c_showTimesheetHome(){
			// *** model ***
			$this->fields = "idattendance, att_in, att_out, att_duration_day, att_duration_night";
			$this->joins = "";
			$this->conds = array("DATE(att_in) <= CURDATE() AND att_out IS NULL", "idworker = ".$_SESSION['iduser']);
			$this->orders = array("att_date DESC");
			$this->limit = 1;
			if($this->select()){
				// *** view ***
				$this->v_showTimeSheetHome();
				$this->json['code'] = "timesheetHome";
				return $this->json;
			}else{
				throw new Exception('PHP : Error in controller update', 222);
			}
		}
		
		public function c_selectVehicle(){
			// *** model ***
			$vehicle = new veh_vehicle();
			//$vehicle->orders = array("veh_type DESC","veh_numberplate");
			//$vehicle->conds = array("veh_state = 1");

			if($vehicle->m_getByPlanning()){
				// *** view ***
				$this->v_showVehicleSelect($vehicle);
				$this->json['code'] = "vehicleSelection";
				return $this->json;
			}else{
				throw new Exception('PHP : Error in controller update', 222);
			}
		}
		
		public function c_selectMaterial(){
			// *** model ***
			$materials = new art_article();
			$materials->conds = array("art_type = 2");
			$materials->orders = array("art_description");

			if($materials->select()){
				// *** view ***
				$this->v_showMaterialSelect($materials);
				$this->json['code'] = "materialSelection";
				return $this->json;
			}else{
				throw new Exception('PHP : Error in controller update', 222);
			}
		}
		
		public function c_startTimer(){
			// *** model ***
			$data['idworker'] = $_SESSION['iduser'];
			$data['att_in'] = date("Y/m/d H:i");
			$data['att_date'] = date("Y/m/d");
			$data['att_ip'] = getIp();
			$data['idvehicle'] = $this->dataSent['idvehicle'];
			$data['att_driver'] = $this->dataSent['att_driver'];
			$data['idarticle'] = $this->dataSent['idarticle']??array();
			//var_dump($this->dataSent['article']); exit;
			//$data['att_city'] = getCity($data['att_ip']);
			
			$this->duplicateKey = false;
			//$this->debugging = true;
			if($this->insert($data)){
				$this->lnkProcess($this->result['idparent'], $data);
				return $this->c_showTimesheetHome();
				//$this->json['code'] = "timerStarted";
				//$this->json['html'] = '<i class="fas fa-stopwatch mr-3"></i>Pointage - Début à '.alterData("small-time", $data['att_in']);
				//return $this->json;
			}else{
				throw new Exception('PHP : Error in controller insert', 220);
			}
		}
		
		private function calcHours($time1, $time2){
			$tt = new DateTime($time1);

			$t1 = new DateTime($time1);
			$t2 = new DateTime($time2);
			
			// diner break
			if(strtotime($t1->format('H:i:s')) <= strtotime('12:00:00') && strtotime($t2->format('H:i:s')) >= strtotime('13:00:00')){
				$dinerBreak = "1";
			}else{
				$dinerBreak = "0";
			}

			$t1_00 = new DateTime($t1->format('Y-m-d').' 00:00:00');
			$t1_06 = new DateTime($t1->format('Y-m-d').' 06:00:00');
			$t1_20 = new DateTime($t1->format('Y-m-d').' 20:00:00');

			$tt->modify('+1 day');
			$t2_00 = new DateTime($tt->format('Y-m-d').' 00:00:00');
			$t2_06 = new DateTime($tt->format('Y-m-d').' 06:00:00');
			$t2_20 = new DateTime($tt->format('Y-m-d').' 20:00:00');

			$mDay = $mNight = 0;
			
			//dump($t1->format('d-m H:i:s'),$t1_00->format('d-m H:i:s'),$t1_06->format('d-m H:i:s'),$t1_20->format('d-m H:i:s'),$t2->format('d-m H:i:s'),$t2_00->format('d-m H:i:s'),$t2_06->format('d-m H:i:s'),$t2_20->format('d-m H:i:s'));
			
			// 00 - 06
			if($t1 >= $t1_00 && $t1 <= $t1_06){
				if($t2 <= $t1_06){
					$diff = $t1->diff($t2);
					$mNight += ($diff->h * 60) + $diff->i;
				}else{ // > 06
					$diff = $t1->diff($t1_06);
					$mNight += ($diff->h * 60) + $diff->i;
					
					$diff = $t1_06->diff(min($t2,$t1_20));
					$mDay += ($diff->h * 60) + $diff->i;
				}
				if($t2 > $t1_20){
					$diff = $t1_20->diff($t2);
					$mNight += ($diff->h * 60) + $diff->i;
				}
			}
			// 06 - 20
			if($t1 > $t1_06 && $t1 <= $t1_20){
				if($t2 <= $t1_20){
					$diff = $t1->diff($t2);
					$mDay += ($diff->h * 60) + $diff->i;
				}else{ // > 20
					$diff = $t1->diff($t1_20);
					$mDay += ($diff->h * 60) + $diff->i;
					
					$diff = $t1_20->diff(min($t2,$t2_00));
					$mNight += ($diff->h * 60) + $diff->i;
				}
				if($t2 > $t2_00){
					$diff = $t2_00->diff(min($t2, $t2_06));
					$mNight += ($diff->h * 60) + $diff->i;
				}
				if($t2 > $t2_06){
					$diff = $t2_06->diff(min($t2,$t2_20));
					$mDay += ($diff->h * 60) + $diff->i;
				}
			}
			// 20 - 24
			if($t1 > $t1_20){
				if($t2 <= $t2_00){
					$diff = $t1->diff($t2);
					$mNight += ($diff->h * 60) + $diff->i;
				}
				if($t2 > $t2_00 && $t2 <= $t2_06){
					$diff = $t1->diff($t2);
					$mNight += ($diff->h * 60) + $diff->i;
				}
				if($t2 > $t2_06 && $t2 <= $t2_20){
					$diff = $t1->diff($t2_06);
					$mNight += ($diff->h * 60) + $diff->i;
					
					$diff = $t2_06->diff($t2);
					$mDay += ($diff->h * 60) + $diff->i;
				}
			}
			// diner time
			if($dinerBreak == "1"){
				$mDay -= 30;
			}

			$hD = sprintf("%02d",intdiv($mDay,60));
			$mD = sprintf("%02d",$mDay % 60);
			$hN = sprintf("%02d",intdiv($mNight,60));
			$mN = sprintf("%02d",$mNight % 60);
			
			return array("totDay"=>$hD.":".$mD, "totNight"=>$hN.":".$mN, "dinerBreak"=>$dinerBreak);
		}

		private function sum_time() {
			$i = 0;
			foreach (func_get_args() as $time) {
				sscanf($time, '%d:%d', $hour, $min);
				$i += $hour * 60 + $min;
			}
			if ($h = floor($i / 60)) {
				$i %= 60;
			}
			return sprintf('%02d:%02d', $h, $i);
		}

		public function c_saveAttTravelTime(){
			// *** model ***
			$id = decrypt($this->idrecord);
			$this->m_getById($id);
			$att = $this->values[0];
			
			parse_str($this->dataSent, $data);
			//$this->debugging = true;
			if($this->m_updateTravel_time($att['att_date'], $att['idvehicle'], $data['att_travel_time_going'], $data['att_travel_time_coming'])){
				$this->json['code'] = "travelTimeInserted";
				return $this->json;
			}else{
				throw new Exception('PHP : Error in controller insert', 220);
			}
		}

		public function c_stopTimer(){
			// *** model ***
			$this->m_getById(decrypt($this->idrecord));
			$time1 = $this->values[0]['att_in'];
			$time2 = date("Y-m-d H:i:s");
			$res = $this->calcHours($time1, $time2);
			
			$this->conds = array("idattendance = ".decrypt($this->idrecord));
			$data['att_out'] = $time2;
			$data['att_duration_day'] = $res['totDay'];
			$data['att_duration_night'] = $res['totNight'];
			$data['att_diner_break'] = $res['dinerBreak'];
			$duration = "Jour : ".str_replace(":","h",$res['totDay'])." - Nuit : ".str_replace(":","h",$res['totNight']);
			
			//$this->debugging = true;
			if($this->update($data)){
				$this->json['code'] = "timerStopped";
				$this->json['info'] = $duration;
				$this->json['html'] = '<i class="fal fa-stopwatch mr-3 fa-2x align-middle"></i>Pointage - Fin à '.alterData("small-time", $data['att_out']).'<br><span class="text-dark">'.$duration.'</span>';
				return $this->json;
			}else{
				throw new Exception('PHP : Error in controller insert', 220);
			}
		}

		public function c_insert(){
			if($this->checkUserRight()){
				// *** model ***
				$this->duplicateKey = false;
				parse_str($this->dataSent, $data);
				if(empty($data['idabstype'])){
					// break
					if(!empty($data['att_break_start']) && !empty($data['att_break_stop'])){
						$res1 = $this->calcHours($data['att_in'], $data['att_break_start']);
						$res2 = $this->calcHours($data['att_break_stop'], $data['att_out']);
						$data['att_duration_day'] = $this->sum_time($res1['totDay'],$res2['totDay']);
						$data['att_duration_night'] = $this->sum_time($res1['totNight'],$res2['totNight']);
					}else{
						$res = $this->calcHours($data['att_in'], $data['att_out']);
						$data['att_duration_day'] = $res['totDay'];
						$data['att_duration_night'] = $res['totNight'];	
					}
				}else{
					$data['att_duration_day'] = 0;
					$data['att_duration_night'] = 0;
				}
				if($this->insert($data)){
					$this->json['code'] = "inserted";
					$this->json['idparent'] = encrypt($this->result['idparent']);
					$this->json['info'] = getText("Pointage ajouté");
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller insert', 220);
				}
			}
		}
		
		public function c_duplicate(){
			if($this->checkUserRight()){
				// *** model ***
				if($this->m_duplicate(decrypt($this->idrecord))){
					$this->json['idparent'] = encrypt($this->result['newId']);
					$this->json['info'] = getText("Pointage dupliqué");
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller duplicate', 224);
				}
			}
		}
		
		public function c_insertFrom(){
			if($this->checkUserRight()){
				// model
				$this->duplicateKey = false;
				if($this->m_insert($this->dataSent)){
					$this->json['code'] = "insertedFrom";
					$this->json['idparent'] = $this->result['idparent'];
					$this->json['info'] = getText("Pointage ajouté");
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller insertFrom', 221);
				}
			}
		}

		public function c_update(){
			if($this->checkUserRight()){
				// *** model ***
				parse_str($this->dataSent, $data);
				if(empty($data['idabstype'])){
					// break
					if(!empty($data['att_break_start']) && !empty($data['att_break_stop'])){
						$res1 = $this->calcHours($data['att_in'], $data['att_break_start']);
						$res2 = $this->calcHours($data['att_break_stop'], $data['att_out']);
						$data['att_duration_day'] = $this->sum_time($res1['totDay'],$res2['totDay']);
						$data['att_duration_night'] = $this->sum_time($res1['totNight'],$res2['totNight']);
					}else{
						$res = $this->calcHours($data['att_in'], $data['att_out']);
						$data['att_duration_day'] = $res['totDay'];
						$data['att_duration_night'] = $res['totNight'];	
					}
				}else{
					$data['att_duration_day'] = 0;
					$data['att_duration_night'] = 0;
				}
				
				//$data['att_diner_break'] = $res['dinerBreak'];
				if($this->m_update(decrypt($this->idrecord), $data)){
					// update travel if driver
					//var_dump($data);exit;
					if($data['att_driver'] == 1 && $data['idvehicle'] > 0){
						//$this->debugging = true;
						//$this->m_updateTravel_time(date('Y-m-d',strtotime($data['att_date'])), $data['idvehicle'], $data['att_travel_time_going'], $data['att_travel_time_coming']);
					}
					// *** new data to update table ***
					if($this->m_getById(decrypt($this->idrecord))){
						// *** view ***
						$this->v_createTr();
						$this->json['code'] = "updated";
						$this->json['info'] = getText("Pointage mis à jour");
						return $this->json;
					}else{
						throw new Exception('PHP : Error in controller update', 222);
					}
				}
			}
		}

		public function c_delete(){
			if($this->checkUserRight()){
				if($this->m_delete(decrypt($this->idrecord))){
					$this->json['code'] = "deleted";
					$this->json['info'] = getText("Pointage supprimé");
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller delete', 223);
				}
			}
		}

		public function __destruct()
		{
		}
	}