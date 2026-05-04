<?php
/**
*** Novembre 2019@SoluFile 
**/
	require_once 'model_crud.php';
	
	class wor_attendance_model extends crud {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->table 		= "wor_attendance";
			$this->key			= "idattendance";
			$this->duplicateKey = false;
			
			$this->fields 		= "wor_attendance.*, IF(idabstype > 0, '<i class=\'fad fa-user-slash ml-2\'></i>',CONCAT_WS(' ', if(att_driver=1,'<i class=\'fad fa-steering-wheel ml-2\'></i>','<i class=\'fad fa-chair-office ml-2\'></i>'), veh_numberplate)) AS veh_numberplate, TIME_FORMAT(att_duration_day, '%H:%i') AS att_dur_day, TIME_FORMAT(att_duration_night, '%H:%i') AS att_dur_night, SEC_TO_TIME(TIME_TO_SEC(att_duration_day) + TIME_TO_SEC(att_duration_night)) AS att_dur, SEC_TO_TIME(TIME_TO_SEC(att_break_stop) - TIME_TO_SEC(att_break_start)) AS att_break, SEC_TO_TIME(TIME_TO_SEC(att_travel_time_going) + TIME_TO_SEC(att_travel_time_coming)) AS tot_travel, wor_name, wor_picture, IF(att_diner_break=1,'Oui','Non') AS diner_break, typ_name, tot_tim_duration, IFNULL(TIME_TO_SEC(att_duration_day) + TIME_TO_SEC(att_duration_night) - TIME_TO_SEC(tot_tim_duration), 8000) AS tot_diff";
			
			$this->joins 		= "
				LEFT JOIN veh_vehicle USING(idvehicle) 
				LEFT JOIN wor_worker USING(idworker) 
				LEFT JOIN abs_type USING(idabstype) 
				LEFT JOIN (
				 SELECT idworker, tim_date, SEC_TO_TIME(SUM(TIME_TO_SEC(tim_duration))) AS tot_tim_duration
				 FROM tim_timesheet
				 GROUP BY tim_date, idworker
				) AS tim ON tim.idworker = wor_attendance.idworker AND tim_date = att_date 
			";
			$this->conds 		= array();
			$this->groups 		= array();
			$this->orders 		= array("att_date DESC","idvehicle","att_driver DESC");
			$this->unset		= array();
			
			$this->fieldName	= "att_name";
			
			/*** Table filtering ***/
			$this->filters['wor_worker'] = array("id"=>"wor_attendance.idworker","in"=>array(),"label"=>"Compagnon");
			$this->filters['wor_year'] = array("id"=>"idyear","in"=>array(date("Y")),"label"=>"Année");
			$this->filters['wor_week'] = array("id"=>"idweek","in"=>array(date("W")),"label"=>"Semaine");
			
			$this->filters['ord_year'] = array("id"=>"idyear","in"=>array(date("Y")),"label"=>"Année");
			$this->filters['ord_week'] = array("id"=>"idweek","in"=>array(date("W")),"label"=>"Semaine");
			
			/*** Select filtering ***/
			//$this->idparent		= "";
		}

		public function m_newRecord($insert=false)
		{
			$data = array(
				"idattendance"=>0,
				"idworker"=>0,
				"att_driver"=>1,
				"att_in"=>date("Y-m-d"),
				"att_out"=>null,
				"att_break_start"=>null,
				"att_break_stop"=>null,
				"att_break_reason"=>null,
				"att_ip"=>null,
				"att_city"=>null,
				"att_date"=>date("Y-m-d"),
				"att_duration_day"=>null,
				"att_duration_night"=>null,
				"att_validated_by"=>null,
				"att_validated_date"=>null,
				"att_edited_by"=>null,
				"att_edited_date"=>null,
				"att_remark"=>null,
			);
			$this->values = (object) $data;
			if($insert){
				return $this->insert($this->values);
			}else{
				return true;
			}
		}
		
		public function m_getAll($withFilter=true)
		{
			if($withFilter){
				$filters = $this->dataSent;
				if(!empty($filters)){
					//var_dump($filters);
					foreach($filters as $key=>$val){
						if($key == "idyear"){
							if(is_array($val)){
								$this->conds[] = "YEAR(att_date) IN (".implode(",",$val).")";
							}else{
								$this->conds[] = "YEAR(att_date) = ".$val;
							}
							//$this->dataSent["idyear"][0] = $val;
						}elseif($key == "idweek"){
							if(is_array($val)){
								$this->conds[] = "WEEK(att_date, 3) IN (".implode(",",$val).")";
							}else{
								$this->conds[] = "WEEK(att_date, 3) = ".$val;
							}
						}else{
							$this->conds[] = $key." IN(".implode(",",$val).")";
						}
					}
				}else{
					if(!empty($this->filters)){
						$this->conds[] = "YEAR(att_date) = ".date("Y");
						$this->conds[] = "WEEK(att_date, 3) = ".date("W");
					}
				}
			}else{
				foreach($this->filters as $filter){
					$filter['in'] = array();
				}
			}
			//$this->conds = array();
			//$this->orders = array();
			//$this->debugging = true;

			return $this->select();
		}
		
		public function m_getById($idrecord)
		{
			$this->conds = array("idattendance = ".$idrecord);
			//$this->orders = array();
			//$this->debugging = true;
			
			return $this->select();
		}
		
		public function m_insert($dataSent)
		{
			parse_str($dataSent, $data);
			//$this->debugging = true;
			$this->insert($data);
			
			// *** lnk ***
			if(!empty($this->lnk)){
				$this->lnkProcess($this->result['idparent'], $data);
			}
			
			return true;
		}
		
		public function m_duplicate($idrecord)
		{
			$query = "
			INSERT INTO wor_attendance (idworker, att_driver, att_in, att_out, att_ip, att_date, att_diner_break)
			SELECT idworker, att_driver, att_in, att_out, att_ip, att_date, att_diner_break
			FROM wor_attendance
			WHERE idattendance = $idrecord
			";
			$this->executeQuery($query);
			$this->result['newId'] = $this->result['idparent'];
			
			return true;
		}
		
		public function m_update($idrecord, $data)
		{
			//parse_str($dataSent, $data);
			
			// *** lnk ***
			if(!empty($this->lnk)){
				//$this->lnkProcess($idrecord, $data);
			}
			
			// *** update table ***
			$this->conds = array("idattendance = ".$idrecord);
			//$this->joins = "";
			//$this->debugging = true;
			
			return $this->update($data);
		}
		
		public function m_delete($idrecord)
		{
			//$this->debugging = true;
			
			return $this->delete($idrecord);
		}
		
		public function m_getList($id, $conds=array())
		{
			$this->conds = $conds;
			switch($id){
				case 1 : $this->fields = "idattendance AS id, att_name AS val, '' AS tokens"; $this->select(); break;
			}
			return true;
		}
		
		public function m_getByWeek($year, $week)
		{
			$sem = 35 * 3600;
			$sup = 8 * 3600;
			$query = "
			SELECT idworker, wor_name, wor_picture, 
			GROUP_CONCAT(CASE numday WHEN 2 THEN COALESCE(typ_code, duration) ELSE NULL END) AS lu,
			GROUP_CONCAT(CASE numday WHEN 3 THEN COALESCE(typ_code, duration) ELSE NULL END) AS ma,
			GROUP_CONCAT(CASE numday WHEN 4 THEN COALESCE(typ_code, duration) ELSE NULL END) AS me,
			GROUP_CONCAT(CASE numday WHEN 5 THEN COALESCE(typ_code, duration) ELSE NULL END) AS je,
			GROUP_CONCAT(CASE numday WHEN 6 THEN COALESCE(typ_code, duration) ELSE NULL END) AS ve,
			GROUP_CONCAT(CASE numday WHEN 7 THEN COALESCE(typ_code, duration) ELSE NULL END) AS sa,
			GROUP_CONCAT(CASE numday WHEN 1 THEN COALESCE(typ_code, duration) ELSE NULL END) AS di,
			SEC_TO_TIME(SUM(TIME_TO_SEC(duration))) AS tot, 
			SEC_TO_TIME(GREATEST(SUM(TIME_TO_SEC(duration)) - ".$sem.", 0)) AS tot_sup, 
			SEC_TO_TIME(IF((SUM(TIME_TO_SEC(duration)) - ".$sem.") < ".$sup.", GREATEST(SUM(TIME_TO_SEC(duration)) - ".$sem.", 0), ".$sup.")) AS tot_sup_25,
			SEC_TO_TIME(GREATEST(SUM(TIME_TO_SEC(duration)) - ".$sem." - ".$sup.", 0)) AS tot_sup_50, 
			SUM(att_diner_break) AS tot_diner 
			FROM (
			 SELECT idworker, wor_name, wor_picture, DAYOFWEEK(att_date) AS numday, 
			 SEC_TO_TIME(
			  SUM(
			   TIME_TO_SEC(att_duration_day) + 
			   TIME_TO_SEC(att_duration_night) - 
			   IF(att_driver = 0, IFNULL(TIME_TO_SEC(att_travel_time_going),0), 0) - 
			   IF(att_driver = 0, IFNULL(TIME_TO_SEC(att_travel_time_coming),0), 0) 
			  )
			 ) AS duration, att_diner_break, typ_code 
			 FROM wor_attendance 
			 LEFT JOIN wor_worker USING (idworker) 
			 LEFT JOIN abs_type USING (idabstype) 
			 WHERE YEAR(att_date) = ".$year." AND WEEK(att_date, 3) = ".$week." 
			 GROUP BY att_date, idworker, att_diner_break, typ_code 
			) AS totday 
			GROUP BY idworker 
			";
			//$this->debugging = true;
			if($this->executeQuery($query)){
				return true;
			}else{
				return false;
			}
		}
		
		public function m_getByWorkerWeek($idworker, $year, $week)
		{
			$query = "
			SELECT DAYOFWEEK(att_date) AS numday, att_driver, 
			TIME_FORMAT(att_in, '%H:%i') AS col1, 
			TIME_FORMAT(SEC_TO_TIME(TIME_TO_SEC(att_in) + IFNULL(TIME_TO_SEC(att_travel_time_going),0)), '%H:%i') AS col2, 
			IF(att_driver = 1, TIME_FORMAT(att_travel_time_going, '%H:%i'), '-') AS col3,
			'12:00' AS col4,'12:30' AS col5,
			TIME_FORMAT(SEC_TO_TIME(TIME_TO_SEC(att_out) - IFNULL(TIME_TO_SEC(att_travel_time_coming),0)), '%H:%i') AS col6, 
			TIME_FORMAT(att_out, '%H:%i') AS col7,
			IF(att_driver = 1, TIME_FORMAT(att_travel_time_coming, '%H:%i'), '-') AS col8,
			TIME_FORMAT(SEC_TO_TIME(
				TIME_TO_SEC(att_duration_day) + TIME_TO_SEC(att_duration_night) - 
				IFNULL(TIME_TO_SEC(att_travel_time_going),0) - IFNULL(TIME_TO_SEC(att_travel_time_coming),0) 
			), '%H:%i') AS col9,
			TIME_FORMAT(SEC_TO_TIME(IFNULL(TIME_TO_SEC(att_travel_time_going),0) + IFNULL(TIME_TO_SEC(att_travel_time_coming),0)), '%H:%i') AS col10, 
			TIME_FORMAT(TIME(SEC_TO_TIME(TIME_TO_SEC(att_duration_day) + TIME_TO_SEC(att_duration_night) - IF(att_driver = 0, IFNULL(TIME_TO_SEC(att_travel_time_going),0), 0) - IF(att_driver = 0, IFNULL(TIME_TO_SEC(att_travel_time_coming),0), 0))), '%H:%i') AS col11,
			IF(att_driver = 0, idarea, '-') AS col12 
			FROM wor_attendance 
			LEFT JOIN (
			 SELECT tim_timesheet.idworker, tim_date, DAYOFWEEK(tim_date) AS numday, MAX(idarea) AS idarea 
			 FROM tim_timesheet 
			 LEFT JOIN tas_task USING (idtask) 
			 LEFT JOIN job_job USING (idjob) 
			 LEFT JOIN sit_site USING (idsite) 
			 WHERE tim_timesheet.idworker = ".$idworker." 
			 GROUP BY tim_date
			) AS tim ON tim.idworker = wor_attendance.idworker AND tim_date = att_date 
			WHERE YEAR(att_date) = ".$year." AND WEEK(att_date, 3) = ".$week." AND wor_attendance.idworker = ".$idworker." 
			GROUP BY att_date,att_driver, att_in, att_out, att_travel_time_coming, att_travel_time_going, att_duration_day, att_duration_night, att_break_start, att_break_stop 
			ORDER BY DAYOFWEEK(att_date)
			";
			//$this->debugging = true;
			if($this->executeQuery($query)){
				return true;
			}else{
				return false;
			}
		}
		
		public function m_updateTravel_time($date, $vehicle, $duration_going, $duration_coming)
		{
			$query = "
			UPDATE wor_attendance 
			SET att_travel_time_going = '$duration_going', att_travel_time_coming = '$duration_coming' 
			WHERE att_date = '$date' 
			AND idvehicle = $vehicle
			";
			//$this->debugging = true;
			if($this->executeQuery($query)){
				return true;
			}else{
				return false;
			}
		}
		
		public function m_insert_from_planning($idworker, $idabstype, $date_begin, $date_end)
		{
			$begin = new DateTime($date_begin);
			$end = new DateTime($date_end);
			$end->modify('+1 day');
			
			$query = "
			INSERT INTO wor_attendance (idworker, idabstype, att_date, att_diner_break)
			VALUES
			";
			
			$diff = date_diff($begin,$end);
			while ($diff->format("%a") > 0){
				if($begin->format('N')<6){
					$query .= "($idworker, $idabstype, ".$begin->format("Ymd").", 0),";
				}
				$begin->modify('+1 day');
				$diff = date_diff($begin,$end);
			}
			$query = substr($query, 0, strlen($query)-1);
			//$this->debugging = true;
			if($this->executeQuery($query)){
				return true;
			}else{
				return false;
			}
		}
		
		public function __destruct()
		{
		}
	}