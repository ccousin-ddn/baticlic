<?php
/**
*** Juillet 2019@SoluFile 
**/

/*
SELECT SEC_TO_TIME(SUM(TIME_TO_SEC(tim_duration)))
FROM tim_timesheet
WHERE idworker = 3
*/

	require_once (dirname(__FILE__)."/../views/v_tim_timesheet.php");
	
	class tim_timesheet extends tim_timesheet_view {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->label 		= gettext("Emploi du temps");
			$this->labels 		= gettext("Emplois du temps");
			$this->newtext		= gettext("Nouvel emploi du temps");
			$this->picto 		= '<i class="fal fa-clock"></i>';
			$this->level		= 1;
			
			$this->cryptfields	= array("");
			
			$this->lnk			= array(); // GROUP_CONCAT(idlnk) idlnk
			
			$this->tim_year		= array();
			$this->tim_week		= array();
			
			for ($y = 2020; $y <= date("Y"); $y++) {
				$this->tim_year[$y] = $y;
			}
			
			$new_date = new DateTime();
			$year = date("Y");
			$numW = date("W", strtotime($year."-12-28"));

			for ($week = 1; $week <= $numW; $week++) {
				$new_date->setISODate($year,$week);
				$monday = $new_date->format('d/m');
				$new_date->modify('sunday this week');
				$sunday = $new_date->format('d/m');
				$this->tim_week[$week] = "Sem. ".$week." (".$monday." - ".$sunday.")";
			}
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
		
		public function c_tableByParent(){
			if($this->checkUserRight()){
				// *** model ***
				if($this->m_getAll(false)){
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
				$this->values->idparent = decrypt($this->idrecord);
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
		
		public function c_search(){
			if($this->checkUserRight()){
				// *** model ***
				$this->conds = array("tim_name LIKE '%".$this->dataSent['value']."%'");
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
					$res = $this->m_newRecord(true);
					$this->idrecord = encrypt($this->result['idparent']);
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
				if($this->m_update(decrypt($this->idrecord), $this->dataSent)){
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