<?php
/**
*** Juillet 2019@SoluFile 
**/
	require_once (dirname(__FILE__)."/../views/v_tas_task.php");
	
	class tas_task extends tas_task_view {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->label 		= gettext("Poste");
			$this->labels 		= gettext("Postes");
			$this->newtext		= gettext("Nouveau poste");
			$this->picto 		= '<i class="fas fa-tasks"></i>';
			$this->level		= 1;
			
			$this->cryptfields	= array("");
			
			$this->lnk			= array("lnk_task_worker"); // GROUP_CONCAT(idlnk) idlnk
			
			$this->tas_status	= array(2=>'En cours', 4=>'Fini');
		}

		public function c_fullTable(){
			if($this->checkUserRight()){
				// *** model ***
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
		
		public function c_listByParent(){
			if($this->checkUserRight()){
				// *** model ***
				$this->conds = array("idjob = ".decrypt($this->idrecord));
				$this->orders = array("tas_order");
				if($this->m_getAll()){
					// *** view ***
					$this->v_createListGroup($this->idrecord);
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
				$this->values->idparent = decrypt($this->idrecord);
				$this->values = (array) $this->values;
				if($this->m_insert(http_build_query($this->values))){
					$this->json['code'] = "newLineInserted";
					$this->json['idparent'] = encrypt($this->result['idparent']);
					$this->json['info'] = getText("Poste ajouté");
					// *** view ***
					$this->values = array($this->values);
					$this->v_createTr();
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller newLine', 202);
				}
			}
		}
		
		public function c_lineMove(){
			if($this->checkUserRight()){
				// *** model ***
				if($this->m_updateOrder()){
					$this->json['code'] = "lineMoved";
					// *** view ***
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller listMove', 203);
				}
			}
		}
		
		public function c_newCardLine(){
			if($this->checkUserRight()){
				// *** model ***
				$this->m_newRecord(true);
				//$this->values->{$this->idparent} = decrypt($this->idrecord);
				//$this->idrecord = encrypt(0);
				$this->idrecord = encrypt($this->result['idparent']);
				$this->json['idchild'] = $this->idrecord;
				$this->values['idtask'] = $this->result['idparent'];
				
				$this->values = array($this->values);
				// *** view ***
				$this->json['code'] = "newLineCreated";
				$this->v_createListItem(true);
				$tmp = $this->json['html'];
				
				$this->values = (object) $this->values[0];
				$this->v_createCard();
				$this->json['html'] = $tmp.$this->json['html'];
				return $this->json;
			}
		}

		public function c_editCardLine(){
			if($this->checkUserRight()){
				// *** model ***
				$res = $this->m_getById(decrypt($this->idrecord));
				$this->values = (object) $this->values[0];
				// *** view ***	
				if($res){
					$this->v_createCard();
					$this->json['code'] = "lineEdited";
				}else{
					throw new Exception('PHP : Error in controller editCardTable', 204);
				}
				return $this->json;
			}
		}

		public function c_saveCardLine(){
			if($this->checkUserRight()){
				$idrecord = decrypt($this->idrecord);
				// *** model ***
				if($idrecord == 0){
					$this->m_insert($this->dataSent);
					$idrecord = $this->result['idparent'];
					$this->idrecord = encrypt($idrecord);
					$this->json['code'] = "lineCreated";
					$createItem = true;
				}else{
					$this->m_update($idrecord, $this->dataSent);
					$this->json['code'] = "lineUpdated";
					$createItem = false;
				}
				// *** new data to update item ***
				if($this->m_getById($idrecord)){
					// *** view ***
					$this->v_createListItem($createItem);
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller saveCardTable', 206);
				}
			}
		}
		
		public function c_search(){
			if($this->checkUserRight()){
				// *** model ***
				$this->conds = array("tas_name LIKE '%".$this->dataSent['value']."%'");
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
				
				// *** doc_document ***
				//$doc_document = new doc_document();
				//$doc_document->info = $this->table;
				//$doc_document->idrecord = $this->idrecord;
				//$res_document = $doc_document->c_selectByParent();
				//$doc_document->json['html'] = "";
				
				// *** view ***	
				if($res){
					$this->v_createCard();
					$this->json['code'] = 1;
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller showCard', 211);
				}
			}
		}

		public function c_insert(){
			if($this->checkUserRight()){
				// *** model ***
				$this->duplicateKey = false;
				if($this->m_insert($this->dataSent)){
					$this->json['code'] = "inserted";
					$this->json['idparent'] = encrypt($this->result['idparent']);
					$this->json['info'] = getText("Poste ajouté");
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller insert', 220);
				}
			}
		}
		
		public function c_createFromQuotation(){
			if($this->checkUserRight()){
				// get quotation lines values
				$quo_line = new quo_line();
				$quo_line->fields = "idquotation, idmetier, idjob, SUM(lin_total / met_rate) AS met_total, met_name AS lin_description ";
				$quo_line->joins = "
					LEFT JOIN met_metier USING (idmetier) 
					LEFT JOIN quo_quotation USING (idquotation)
				";
				$quo_line->conds = array("idquotation = ".decrypt($this->idrecord),"lin_total > 0");
				$quo_line->groups = array("idquotation", "idmetier");
				$quo_line->orders = array("idmetier");
				//$quo_line->debugging = true;
				$quo_line->select();
				
				// *** model ***
				foreach($quo_line->values as $line){
					$this->duplicateKey = false;
					$data['idjob'] = $line['idjob'];
					$data['idquotation'] = $line['idquotation'];
					$data['tas_name'] = $line['lin_description'];
					$data['idmetier'] = $line['idmetier'];
					$data['tas_duration'] = round($line['met_total'], 0, PHP_ROUND_HALF_UP).":00";
					$data['tas_status'] = 2;
					$data['tas_date_begin'] = date("d-m-Y");
					$this->insert($data);
				}
				
				$this->json['code'] = "taskCreated";
				$this->json['info'] = getText("Postes ajoutés");
				return $this->json;
				
			}
		}
		
		public function c_insertFrom(){
			if($this->checkUserRight()){
				// model
				$this->duplicateKey = false;
				if($this->m_insert($this->dataSent)){
					$this->json['code'] = "insertedFrom";
					$this->json['idparent'] = $this->result['idparent'];
					$this->json['info'] = getText("Poste ajouté");
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller insertFrom', 221);
				}
			}
		}

		public function c_update(){
			if($this->checkUserRight()){
				// *** model ***
				if($this->m_update(decrypt($this->idrecord), $this->dataSent)){
					// *** new data to update table ***
					if($this->m_getById(decrypt($this->idrecord))){
						// *** view ***
						$this->v_createTr();
						$this->json['code'] = "updated";
						$this->json['info'] = getText("Poste mis à jour");
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
					$this->json['info'] = getText("Poste supprimé");
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller delete', 223);
				}
			}
		}
		
		public function c_gantt(){
			if($this->checkUserRight()){
				// *** model ***
				$query = "
				SELECT idtask AS id, idjob, tas_date_begin AS start, tas_date_end AS end, tas_deadline, 
				CONCAT(COALESCE(cli_short_name, cli_name),'-',sit_name,'-',job_name,'-',tas_name) AS name, GROUP_CONCAT(wor_color SEPARATOR '/') AS colors
				FROM (SELECT * FROM tas_task WHERE tas_status = 2 ORDER BY tas_date_begin, idjob, tas_order) AS tmp
				LEFT JOIN job_job USING(idjob) 
				LEFT JOIN cli_client USING(idclient) 
				LEFT JOIN sit_site USING(idsite) 
				LEFT JOIN lnk_task_worker USING (idtask) 
				LEFT JOIN wor_worker ON wor_worker.idworker = lnk_task_worker.idworker 
				WHERE tas_status = 2 
				GROUP BY idtask 
				ORDER BY tas_date_begin, idjob, tas_order;
				";
				//$this->debugging = true;
				if($this->executeQuery($query)){
					// *** view ***
					$dep = "";
					$idjob = 0;
					$idcolor = 1;
					foreach ($this->values as $key => $value) {
						if($dep !="" ){
							if($idjob == $value['idjob']){
								$this->values[$key]['dependencies'] = $dep;
							}else{
								$this->values[$key]['dependencies'] = "";
								$idcolor++;
								if($idcolor == 8){$idcolor = 1;}
							}
						}else{
							$this->values[$key]['dependencies'] = "";
						}
						$dep = $value['id'];
						$idjob = $value['idjob'];
						// date end
						if(empty($value['end'])){
							$date = new DateTime($value['start']);
							$date->modify('+1 day');
							$this->values[$key]['end'] = $date->format('Y-m-d');
						}
						
						if(!empty($value['tas_deadline'])){
							$date1 = date_create($value['start']);
							$date2 = date_create("2019-11-05");
							$today = date("U");
							$interval1 = date_diff(date_create($value['start']), date_create($value['end'])??$today);
							$interval2 = date_diff(date_create($value['start']), date_create($value['tas_deadline'])??$today);
							$int1 = $interval1->format('%a');
							$int2 = $interval2->format('%a');
							//$this->values[$key]['progress'] = round(100/($int2??1)*($int1??1),0,PHP_ROUND_HALF_UP );
							//$this->values[$key]['end'] = $value['tas_deadline'];
						}
						
						// color
						//$this->values[$key]['custom_class'] = "bar-color-".$idcolor;
						$colors = explode('/',$value['colors']);
						//echo $colors[0]; exit;
						$this->values[$key]['custom_style'] = "fill:".rgba2hex($colors[0]);
					}
					
					//print_r($this->values);exit;

					$this->json['code'] = 1;
					$this->json['html'] = json_encode($this->values);
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller fullTable', 200);
				}
			}
		}
		
		public function c_editTimesheet(){
			// wor_attendance info to get driver
			if(!empty($this->idrecord)){
				$attendance = new wor_attendance();
				$attendance->m_getById(decrypt($this->idrecord));
				$this->att_driver = $attendance->values[0]['att_driver'];
			}else{
				$this->att_driver = 0;
			}
			// *** model ***
			$this->fields = "job_job.idclient, COALESCE(cli_short_name, cli_name) AS cli_name";
			$this->joins = "
			LEFT JOIN job_job USING (idjob) 
			LEFT JOIN cli_client USING (idclient) 
			LEFT JOIN sit_site USING (idsite) 
			";
			$this->conds = array("tas_status = 2", "job_status = 2");
			$this->groups = array("idclient");
			$this->orders = array("cli_name");
			
			// task from planning
			$this->planning = new tas_task();
			$this->planning->selectTaskFromPlanning();
			
			if($this->m_getAll()){
				// *** view ***
				$this->v_selectClient();
				$this->json['code'] = "timesheetEdited";
				return $this->json;
			}else{
				throw new Exception('PHP : Error in controller tableByParent', 201);
			}
		}
		
		public function c_taskSelectSite(){
			// *** model ***
			$this->fields = "idsite, sit_name, COALESCE(cli_short_name, cli_name) AS cli_name";
			$this->joins = "
			LEFT JOIN job_job USING (idjob) 
			LEFT JOIN cli_client USING (idclient) 
			LEFT JOIN sit_site USING (idsite) 
			";
			$this->conds = array("tas_status = 2", "job_status = 2", "job_job.idclient = ".decrypt($this->idrecord));
			$this->groups = array("idsite");
			$this->orders = array("sit_name");
			if($this->m_getAll()){
				// *** view ***
				$this->v_selectSite();
				$this->json['code'] = "showSelectSite";
				return $this->json;
			}else{
				throw new Exception('PHP : Error in controller tableByParent', 201);
			}
		}
		
		public function c_taskSelectTask(){
			// *** model ***
			$this->fields = "idtask, tas_name, job_name, sit_name, COALESCE(cli_short_name, cli_name) AS cli_name";
			$this->joins = "
			LEFT JOIN job_job USING (idjob) 
			LEFT JOIN cli_client USING (idclient) 
			LEFT JOIN sit_site USING (idsite) 
			";
			$this->conds = array("tas_status = 2", "job_status = 2", "idsite = ".decrypt($this->idrecord));
			$this->orders = array("job_name", "tas_name");
			if($this->m_getAll()){
				// *** view ***
				$this->v_selectTask();
				$this->json['code'] = "showSelectTask";
				return $this->json;
			}else{
				throw new Exception('PHP : Error in controller tableByParent', 201);
			}
		}
		
		public function c_createTimesheet(){
			// *** model ***
			$idworker = $_SESSION['iduser'];
			$idtask = $this->idrecord;
			if($this->m_createTimesheet($idworker, $idtask)){
				// *** view ***
				$this->json['code'] = "timesheetCreated";
				return $this->json;
			}else{
				throw new Exception('PHP : Error in controller tableByParent', 201);
			}
		}

		private function selectTaskFromPlanning(){
			// *** model ***
			$this->fields = "idtask, tas_name, job_name, sit_name, COALESCE(cli_short_name, cli_name) AS cli_name";
			$this->joins = "
			LEFT JOIN job_job USING (idjob) 
			LEFT JOIN pla_planning USING (idjob) 
			LEFT JOIN cli_client USING (idclient) 
			LEFT JOIN sit_site USING (idsite) 
			";
			$this->conds = array("pla_planning.idworker = ".$_SESSION['iduser'], "CURDATE() BETWEEN pla_date_begin AND pla_date_end");
			$this->orders = array("job_name", "tas_name");
			if($this->select()){
				// *** view ***
				$this->v_taskFromPlanning();
			}else{
				throw new Exception('PHP : Error in controller tableByParent', 201);
			}
		}
		
		public function __destruct()
		{
		}
	}