<?php
/**
*** Juillet 2019@SoluFile 
**/
	require_once 'model_crud.php';
	
	class job_job_model extends crud {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->table 		= "job_job";
			$this->key			= "idjob";
			$this->duplicateKey = false;
			
			$this->fields 		= "job_job.*, con_reference, 
				COALESCE(quo.tot_amount,0) AS tot_amount, 
				COALESCE(inv.tot_invoice,0) AS tot_invoice, 
				COALESCE(cli_short_name,cli_name) AS cli_name, sit_name, are_name, wor_name, con.idclicontact,
				COALESCE(tot_tas,'00:00') AS tot_tas_duration, COALESCE(tot_tim,'00:00') AS tot_tim_duration";
			
			$this->joins 		= "
				LEFT JOIN cli_client USING(idclient) 
				LEFT JOIN con_contract USING(idcontract) 
				LEFT JOIN sit_site USING (idsite) 
				LEFT JOIN wor_worker USING(idworker) 
				LEFT JOIN are_area USING (idarea) 
				LEFT JOIN (
				 SELECT idjob, GROUP_CONCAT(DISTINCT lnk_job_contact.idclicontact) AS idclicontact
				 FROM lnk_job_contact
				 GROUP BY idjob
				) AS con ON con.idjob = job_job.idjob
				LEFT JOIN (
				 SELECT idjob, CONCAT(SUM(tas_duration),':00') AS tot_tas, CONCAT(FLOOR(SUM(tot_dur)/3600),':', RPAD(FLOOR(MOD(SUM(tot_dur),3600)/60),2,'0')) AS tot_tim
				 FROM tas_task 
				 LEFT JOIN (
				  SELECT idtask, SUM(TIME_TO_SEC(tim_duration)) AS tot_dur FROM tim_timesheet GROUP BY idtask
				 ) AS tim ON tim.idtask = tas_task.idtask
				 GROUP BY idjob
				) AS tas ON tas.idjob = job_job.idjob 
				LEFT JOIN (
				 SELECT idjob, SUM(quo_amount) AS tot_amount 
				 FROM quo_quotation WHERE quo_status IN(3,4) 
				 GROUP BY idjob
				) AS quo ON quo.idjob = job_job.idjob 
				LEFT JOIN (
				 SELECT idjob, SUM(IF(inv_type=0, inv_tot_articles, -inv_tot_articles)) AS tot_invoice 
				 FROM inv_invoice WHERE inv_status > 0 
				 GROUP BY idjob 
				) AS inv ON inv.idjob = job_job.idjob 
			";
			$this->conds 		= array();
			$this->groups 		= array();
			$this->orders 		= array("CAST(job_reference as unsigned) DESC");
			$this->unset		= array("are_name");
			
			$this->fieldName	= "job_reference";
			
			/*** Table filtering ***/
			$this->filters		= array();
			$this->filters['job_status'] = array("id"=>"job_status","in"=>array(2,8),"label"=>"Statut");
			$this->filters['cli_client'] = array("id"=>"cli_client.idclient","in"=>array(),"label"=>"Client");
			$this->filters['con_contract'] = array("id"=>"con_contract.idcontract","in"=>array(),"label"=>"Marché");
			$this->filters['job_month'] = array("id"=>"idmonth","in"=>array(),"label"=>"Mois");
			
			/*** Select filtering ***/
			$this->idparent		= "idclient";
		}

		public function m_newRecord($insert=false)
		{
			$cfg = new cfg_config();
			$data = array(
				"idjob"=>0,
				"idclient"=>0,
				"idsite"=>-1,
				"idworker"=>0,
				"job_reference"=>$cfg->getNumber("job"),
				"job_name"=>null,
				"job_status"=>"0",
				"job_date_begin"=>date("Y-m-d"),
				"job_date_end"=>null,
				"job_remark"=>"",
				
			);
			$this->values = (object) $data;
			if($insert){
				return $this->insert($data);
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
						if($key == "idmonth"){
							if(is_array($val)){
								$this->conds[] = "DATE_FORMAT(job_date_end, '%m-%Y') IN ('".implode("','",$val)."')";
							}else{
								$this->conds[] = "DATE_FORMAT(job_date_end, '%m-%Y') = ".$val;
							}
							//$this->dataSent["idyear"][0] = $val;
						}else{
							$this->conds[] = $key." IN(".implode(",",$val).")";
						}
					}
				}else{
					if(!empty($this->filters)){
						foreach($this->filters as $column=>$val){
							if(!empty($val['in'])){
								$this->conds[] = $val['id']." IN(".implode(",",$val['in']).")";
							}
						}
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
			$this->conds = array("job_job.idjob = ".$idrecord);
			//$this->orders = array();
			//$this->debugging = true;
			
			return $this->select();
		}
		
		public function m_insert($dataSent)
		{
			parse_str($dataSent, $data);
			$data['idmetier'] = implode(",", $data['idmetier']??[]);
			//$this->debugging = true;
			
			$this->insert($data);
			
			// *** lnk ***
			if(!empty($this->lnk)){
				$this->lnkProcess($this->result['idparent'], $data);
			}
			
			return true;
		}
		
		public function m_update($idrecord, $dataSent)
		{
			parse_str($dataSent, $data);
			// update date_end if status = 4
			if($data['job_status'] == 4 && empty($data['job_date_end'])){
				$data['job_date_end'] = date("Ymd");
			}
			$data['idmetier'] = implode(",", $data['idmetier']??[]);
			
			// *** lnk ***
			if(!empty($this->lnk)){
				$this->lnkProcess($idrecord, $data, true);
			}
			
			// *** update table ***
			$this->conds = array("idjob = ".$idrecord);
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
			$this->joins = "";
			$this->groups = array("job_job.idjob");
			switch($id){
				case 1 : 
					$this->fields = "job_job.idjob AS id, CONCAT_WS(' ',job_reference,COALESCE(job_surname, job_name)) AS val, '' AS tokens "; 
					//$this->debugging = true;
					$this->select(); 
					break;
				case 2 : $this->fields = "job_job.idjob AS id, job_reference AS val, '' AS tokens "; $this->select(); break;
				case 3 : $this->fields = "job_job.idjob AS id, job_reference AS val, COALESCE(job_surname, job_name) AS subtext, COALESCE(job_surname, job_name) AS tokens"; $this->conds=array("job_status IN (2,3)"); $this->select(); break;
				case 4 : $this->fields = "job_job.idjob AS id, CONCAT_WS(' - ', job_reference, job_surname) AS val, COALESCE(job_surname, job_name) AS subtext, '' AS tokens"; $this->select(); break;
				case 5 :
					$this->fields = "idjob AS id, CONCAT_WS('-', COALESCE(cli_short_name, cli_name),sit_name,job_name) AS val, CONCAT_WS('-',job_reference,job_surname) AS subtext ";
					$this->joins = "LEFT JOIN cli_client USING (idclient) LEFT JOIN sit_site USING (idsite) ";
					$this->conds = array("job_status = 2");
					$this->orders = array("val");
					$this->select();
					break;
				case 6 :
					$this->fields = "idjob AS id, COALESCE(job_surname, CONCAT_WS('-',COALESCE(cli_short_name, cli_name),sit_name,job_name)) AS val ";
					$this->joins = "LEFT JOIN cli_client USING (idclient) LEFT JOIN sit_site USING (idsite) ";
					$this->conds = array("job_status IN (0,1,2)");
					$this->orders = array("val");
					$this->select();
					break;
				case 7 :
					$this->fields = "idjob AS id, job_surname AS val, con_reference AS subtext ";
					$this->joins = "LEFT JOIN con_contract USING (idcontract) ";
					$this->conds[] = "job_status IN (0,1,2)";
					$this->orders = array("val");
					$this->select();
					break;
			}
			return true;
		}

		public function m_createQuotation($idrecord)
		{
			$cfg = new cfg_config();
			
			$query = "
			INSERT INTO quo_quotation (idclient, idsite, idjob, idlibrary, quo_ref, quo_date, quo_title, quo_status, quo_amount, quo_idvat, quo_tot_amount) 
			SELECT idclient, idsite, idjob, 1, '".$cfg->getNumber("quo")."', CURRENT_DATE(), job_name, 1, 0, 1, 0 
			FROM job_job 
			WHERE idjob = $idrecord
			";
			return $this->executeQuery($query);
		}
		
		public function m_stockGetAllByVehicle($idvehicle)
		{
			$query = "
			SELECT idjob, COALESCE(job_surname, job_name) AS job_name 
			FROM wor_attendance 
			LEFT JOIN pla_planning USING (idworker) 
			LEFT  JOIN job_job USING (idjob) 
			WHERE wor_attendance.idvehicle = ".$idvehicle." AND att_date = CURRENT_DATE() 
			AND CURRENT_DATE() BETWEEN pla_date_begin AND pla_date_end 
			ORDER BY job_name
			";
			//$this->debugging = true;
			if($this->executeQuery($query)){
				return true;
			}else{
				return false;
			}
		}
		
		public function m_stockGetAllByPlanning()
		{
			$query = "
			SELECT idjob, COALESCE(job_surname, job_name) AS job_name 
			FROM pla_planning 
			LEFT JOIN job_job USING (idjob) 
			WHERE idjob IS NOT NULL AND CURRENT_DATE() BETWEEN pla_date_begin AND pla_date_end 
			GROUP BY idjob 
			ORDER BY job_name
			";
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