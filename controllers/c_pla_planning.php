<?php
/**
*** Octobre 2021@SoluFile 
**/
	require_once (dirname(__FILE__)."/../views/v_pla_planning.php");
	
	class pla_planning extends pla_planning_view {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->label 		= gettext("planning");
			$this->labels 		= gettext("plannings");
			$this->newtext		= gettext("Nouveau planning");
			$this->picto 		= '<i class="fal fa-calendar-week fa-fw"></i>';
			$this->level		= 1;
			
			$this->cryptfields	= array("");
			
			$this->lnk			= array(); // GROUP_CONCAT(idlnk) idlnk
			
			$this->holidays 	= array('0101','1704','0105','0805','2605','1407','1508','0111','1111','2512');
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
		
		public function c_saveCardTable(){
			if($this->checkUserRight()){
				// *** model ***
				parse_str($this->dataSent, $data);
				if(decrypt($this->idrecord) == 0){
					$this->m_insert($data);
					$this->idrecord = encrypt($this->result['idparent']);
					$this->json['code'] = "cardInserted";
				}else{
					$this->m_update(decrypt($this->idrecord), $data);
					$this->json['code'] = "cardUpdated";
				}
				// *** new data to update table ***
				if($this->m_getById(decrypt($this->idrecord))){
					// *** view ***
					$this->v_createTr(array("action"=>true,"edit"=>true,"delete"=>true));
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller saveCardTable', 206);
				}
			}
		}

		public function c_showWeek(){
			if($this->checkUserRight()){
				// *** model ***
				$year = $this->dataSent['year'];
				$week = $this->dataSent['week'];
				$date = new DateTime();
				
				$monday = clone $date->setISODate($year,$week,1);
				
				switch($this->dataSent['way']){
					case "now" :
						break;
					case "next" : 
						$monday->modify('+1 week');
						break;
					case "prev" :
						$monday->modify('-1 week');
						break;
				}
				
				$this->json['year'] = $newYear = $monday->format('o');
				$this->json['week'] = $newWeek = $monday->format('W');
				
				$sunday = clone $date->setISODate($newYear,$newWeek,7);
				
				$new_date = clone $monday;
				$this->day1 = $new_date->format('U');
				$new_date->modify('tuesday this week');
				$this->day2 = $new_date->format('U');
				$new_date->modify('wednesday this week');
				$this->day3 = $new_date->format('U');
				$new_date->modify('thursday this week');
				$this->day4 = $new_date->format('U');
				$new_date->modify('friday this week');
				$this->day5 = $new_date->format('U');
				$new_date->modify('saturday this week');
				$this->day6 = $new_date->format('U');
				$new_date->modify('sunday this week');
				$this->day7 = $new_date->format('U');

				// *** worker ***
				$this->workers = new wor_worker();
				$this->workers->fields = "
				wor_worker.idworker, wor_name, wor_login, wor_color, wor_picture, wor_type,
				GROUP_CONCAT(IF('".date("Y-m-d",$this->day1)."' BETWEEN pla_date_begin AND pla_date_end, CONCAT(COALESCE(typ_code, job_surname, CONCAT(COALESCE(cli_short_name, cli_name),'-',sit_name)),'§', idplanning,'§', IFNULL(idabstype, 0),'§', IFNULL(veh_num, 0)), NULL) SEPARATOR '£') AS day1, 
				GROUP_CONCAT(IF('".date("Y-m-d",$this->day2)."' BETWEEN pla_date_begin AND pla_date_end, CONCAT(COALESCE(typ_code, job_surname, CONCAT(COALESCE(cli_short_name, cli_name),'-',sit_name)),'§', idplanning,'§', IFNULL(idabstype, 0),'§', IFNULL(veh_num, 0)), NULL) SEPARATOR '£') AS day2, 
				GROUP_CONCAT(IF('".date("Y-m-d",$this->day3)."' BETWEEN pla_date_begin AND pla_date_end, CONCAT(COALESCE(typ_code, job_surname, CONCAT(COALESCE(cli_short_name, cli_name),'-',sit_name)),'§', idplanning,'§', IFNULL(idabstype, 0),'§', IFNULL(veh_num, 0)), NULL) SEPARATOR '£') AS day3, 
				GROUP_CONCAT(IF('".date("Y-m-d",$this->day4)."' BETWEEN pla_date_begin AND pla_date_end, CONCAT(COALESCE(typ_code, job_surname, CONCAT(COALESCE(cli_short_name, cli_name),'-',sit_name)),'§', idplanning,'§', IFNULL(idabstype, 0),'§', IFNULL(veh_num, 0)), NULL) SEPARATOR '£') AS day4, 
				GROUP_CONCAT(IF('".date("Y-m-d",$this->day5)."' BETWEEN pla_date_begin AND pla_date_end, CONCAT(COALESCE(typ_code, job_surname, CONCAT(COALESCE(cli_short_name, cli_name),'-',sit_name)),'§', idplanning,'§', IFNULL(idabstype, 0),'§', IFNULL(veh_num, 0)), NULL) SEPARATOR '£') AS day5, 
				GROUP_CONCAT(IF('".date("Y-m-d",$this->day6)."' BETWEEN pla_date_begin AND pla_date_end, CONCAT(COALESCE(typ_code, job_surname, CONCAT(COALESCE(cli_short_name, cli_name),'-',sit_name)),'§', idplanning,'§', IFNULL(idabstype, 0),'§', IFNULL(veh_num, 0)), NULL) SEPARATOR '£') AS day6, 
				GROUP_CONCAT(IF('".date("Y-m-d",$this->day7)."' BETWEEN pla_date_begin AND pla_date_end, CONCAT(COALESCE(typ_code, job_surname, CONCAT(COALESCE(cli_short_name, cli_name),'-',sit_name)),'§', idplanning,'§', IFNULL(idabstype, 0),'§', IFNULL(veh_num, 0)), NULL) SEPARATOR '£') AS day7
				";
				$this->workers->joins = "
				LEFT JOIN (SELECT pla_planning.*, veh_num FROM pla_planning LEFT JOIN veh_vehicle USING(idvehicle) WHERE pla_date_begin BETWEEN '".date("Y-m-d",$this->day1)."' AND '".date("Y-m-d",$this->day7)."') AS planning ON planning.idworker = wor_worker.idworker
				LEFT JOIN job_job USING (idjob) 
				LEFT JOIN cli_client USING (idclient) 
				LEFT JOIN sit_site USING (idsite) 
				LEFT JOIN abs_type USING (idabstype) 
				";
				$this->workers->conds = array("wor_state = 1","wor_type IN (1,2)");
				$this->workers->groups = array("wor_worker.idworker");
				$this->workers->orders = array("wor_type","wor_name");
				//$this->workers->debugging = true;
				// *** view ***	
				if($this->workers->select()){
					$this->json['info'] = "Sem. ".$newWeek." (".$monday->format('d/m')." - ".$sunday->format('d/m').")";
					$this->json['code'] = "pla_weekCreated";
					$this->v_createWeek();
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller showCard', 212);
				}
			}
		}

		public function c_showCard(){
			if($this->checkUserRight()){
				// *** model ***
				if($this->idrecord == "0"){
					$res = $this->m_newRecord(false);
					// info : if true => $this->idrecord = encrypt($this->result['idparent']);
				}else{
					$res = $this->m_getById(decrypt($this->idrecord));
					$this->values = (object) $this->values[0];
				}
				
				// *** view ***	
				if($res){
					$this->v_createCard();
					$this->json['code'] = 1;
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller showCard', 212);
				}
			}
		}

		public function c_showPlanning(){
			if($this->checkUserRight()){
				// *** model ***
				$this->fields = "idjob, job_surname, CONCAT_WS('-', COALESCE(cli_short_name, cli_name), sit_name, job_name) AS job_name, sit_address_1, sit_pc, sit_city";
				$this->joins = "
				LEFT JOIN job_job USING (idjob) 
				LEFT JOIN cli_client USING (idclient) 
				LEFT JOIN sit_site USING (idsite) 
				";
				$this->conds = array("pla_planning.idworker = ".$_SESSION['iduser'], "CURDATE() BETWEEN pla_date_begin AND pla_date_end");
				if($this->select()){
					// *** view ***
					$this->v_showPlanningWorker();
					$this->json['code'] = "planning";
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller tableByParent', 201);
				}
			}
		}

		public function c_insert(){
			if($this->checkUserRight()){
				// *** model ***
				$this->duplicateKey = false;
				parse_str($this->dataSent, $data);
				if($this->m_insert($data)){
					/*** Insert absence if needed ***/
					if($data['idabstype']>0){
						$attendance = new wor_attendance();
						$attendance->m_insert_from_planning($data['idworker'], $data['idabstype'], $data['pla_date_begin'], $data['pla_date_end']);
					}
					$this->json['code'] = "updated_notable";
					$this->json['idparent'] = encrypt($this->result['idparent']);
					$this->json['info'] = getText("planning ajouté");
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller insert', 220);
				}
			}
		}

		public function c_update(){
			if($this->checkUserRight()){
				// *** model ***
				parse_str($this->dataSent, $data);
				if($this->m_update(decrypt($this->idrecord), $data)){
					$this->json['code'] = "updated_notable";
					$this->json['info'] = getText("planning mis à jour");
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller update', 222);
				}
			}
		}

		public function c_undo(){
			if($this->checkUserRight()){
				// *** model ***
				if($this->m_getById(decrypt($this->idrecord))){
					// *** view ***
					$this->v_createTr(array("action"=>true,"edit"=>true,"delete"=>true));
					$this->json['code'] = "lineUpdated";
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller update', 222);
				}
			}
		}

		public function c_delete(){
			if($this->checkUserRight()){
				if($this->m_delete(decrypt($this->idrecord))){
					$this->json['code'] = "updated_notable";
					$this->json['info'] = getText("planning supprimé");
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