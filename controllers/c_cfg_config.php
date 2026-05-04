<?php
/**
*** Juillet 2019@SoluFile 
**/
	require_once (dirname(__FILE__)."/../views/v_cfg_config.php");
	
	class cfg_config extends cfg_config_view {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->label 		= gettext("Informations générales");
			$this->labels 		= gettext("Informations générales");
			$this->picto 		= '<i class="fal fa-address-card fa-fw"></i>';
			$this->level		= 1;
			
			$this->cryptfields	= array("");
			
			$this->lnk			= array(); // GROUP_CONCAT(idlnk) idlnk
			
			//$this->arrayName	= array(0=>"No",1=>"Yes");
		}
		
		public function c_getInfos(){
			// *** model ***
			$res = $this->m_getById(1);
			//$this->values = (object) $this->values[0];
			return $this->values[0];
		}
		
		public function c_showCard(){
			if($this->checkUserRight()){
				// *** model ***
				$res = $this->m_getById(1);
				$this->values = (object) $this->values[0];
				$this->values->idconfig = encrypt("1");
				
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

		public function c_update(){
			if($this->checkUserRight()){
				// *** model ***
				if($this->m_update(decrypt($this->idrecord), $this->dataSent)){
					// *** new data to update table ***
					if($this->m_getById(decrypt($this->idrecord))){
						// *** view ***
						$this->json['code'] = "updated_notable";
						$this->json['info'] = getText("Informations générales mises à jour");
						return $this->json;
					}else{
						throw new Exception('PHP : Error in controller update', 221);
					}
				}
			}
		}
		
		public function c_dashboard(){
			$query = "SELECT 
				(
				 SELECT COUNT(idtask) FROM tas_task WHERE tas_status = 2
				) AS tot_tas,
				(
				 SELECT COUNT(idjob) FROM job_job WHERE job_status = 2
				) AS tot_job,
				(
				 SELECT COUNT(idquotation) FROM quo_quotation WHERE quo_status = 2
				) AS tot_quo,
				(
				 SELECT COUNT(idsuporder) FROM sup_order WHERE ord_status = 2
				) AS tot_ord,
				(
				 SELECT SEC_TO_TIME(SUM(TIME_TO_SEC(tim_duration))) FROM tim_timesheet 
				) AS tot_tim,
				(
				 SELECT COUNT(idworker) FROM wor_worker WHERE wor_state = 1
				) AS tot_wor,
				(
				SELECT tot_quo-tot_inv FROM
				 (
				  SELECT SUM(quo_amount) AS tot_quo
				  FROM job_job
				  LEFT JOIN quo_quotation USING (idjob)
				  WHERE job_status IN (2,3,8) AND quo_status IN (3,4)
				 ) AS tot_quo
				 ,
				 (
				  SELECT SUM(IF(inv_type = 0, inv_tot_articles, -inv_tot_articles)) AS tot_inv
				  FROM job_job
				  LEFT JOIN inv_invoice USING (idjob)
				  WHERE job_status IN (2,3,8) AND inv_status > 0
				 ) AS tot_inv
				) AS tot_cap
			";
			//$this->debugging = true;
			$this->executeQuery($query);
			return (object) $this->values[0];
		}
		
		public function c_getChart(){
			if(isset($_SESSION['iduser']) && $_SESSION['usr_level'] > 1){
				$fiscal = $this->getInfo("fiscal_year_end");
				$year = $this->dataSent;
				$month = date("m");
				if(intval($fiscal) > 1){
					$year -= 1;
				}
				
				$date = new DateTime();
				$date->setDate($year, $fiscal, 1);
				$from = $date->format('Y-m-d');
				$date->add(new DateInterval('P1Y'));
				$date->sub(new DateInterval('P1M'));
				$to = $date->format('Y-m-t');
				$this->months = array();
				$date->sub(new DateInterval('P1Y'));
				for ($m=1; $m < 13; $m++) {
					$date->add(new DateInterval('P1M'));
					$this->months[] = IntlDateFormatter::formatObject(new DateTime($date->format('Y-m-d'), new DateTimeZone('Europe/Paris')), 'MMMM y', 'fr');
				}
				// *** model ***
				$query = "
				SELECT num_month, num_year, SUM(inv_tot_articles) AS tot_inv,
				IFNULL(tot_bil,0) AS tot_bil, (IFNULL(SUM(inv_tot_articles),0) - IFNULL(tot_bil,0)) AS total 
				FROM (
				 SELECT idclient, MONTH(inv_date) AS num_month, YEAR(inv_date) AS num_year, IF(inv_type=0, inv_tot_articles, -inv_tot_articles) AS inv_tot_articles, tot_bil FROM inv_invoice 
				 LEFT JOIN ( SELECT MONTH(bil_date) AS num_month, SUM(DISTINCT bil_total) AS tot_bil FROM bil_biller WHERE bil_date BETWEEN '".$from."' AND '".$to."' GROUP BY MONTH(bil_date) ) AS bil ON bil.num_month = MONTH(inv_date) 
				 WHERE inv_status > 0 AND inv_date BETWEEN '".$from."' AND '".$to."'
				) AS inv 
				GROUP BY num_month, num_year, tot_bil  
				ORDER BY num_year, num_month
				";
				if($this->executeQuery($query)){
					// *** view ***
					$this->v_createChartData();
					$this->json['code'] = "chartCreated";
					$this->json['year'] = $this->dataSent;
					$this->json['tot_ca'] = alterData("euro",array_sum(array_column($this->values,'tot_inv')));
					$this->json['tot_net'] = alterData("euro",array_sum(array_column($this->values,'total')));
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller allByProvider', 207);
				}
			}else{
				$this->json['code'] = "no-result";
				return $this->json;
			}
		}
		
		public function c_getPies(){
			if(isset($_SESSION['iduser']) && $_SESSION['usr_level'] > 1){
				$fiscal = $this->getInfo("fiscal_year_end");
				$year = $this->dataSent['year'];
				$month = $this->dataSent['month'];
				if(intval($fiscal) > 1){
					$year -= 1;
				}
				
				$date = new DateTime();
				$date->setDate($year, $month, 1);
				$date->add(new DateInterval('P'.($fiscal-1).'M'));
				
				$from = $date->format('Y-m-d');
				$to = $date->format('Y-m-t');
				
				// *** Invoice ***
				$query_inv = "
				SELECT COALESCE(cli_short_name, cli_name) AS cli_name, SUM(IF(inv_type=0, inv_tot_articles, -inv_tot_articles)) AS total 
				FROM inv_invoice 
				LEFT JOIN cli_client USING (idclient) 
				WHERE inv_status > 0 
				AND inv_date BETWEEN '".$from."' AND '".$to."' 
				GROUP BY idclient 
				ORDER BY total DESC
				";
				$this->executeQuery($query_inv);
				$this->ca = $this->values;
				
				// *** Biller ***
				$query_bil = "
				SELECT COALESCE(acc_name, 'Chantier') AS acc_name, SUM(bre_amount) AS tot_bre, SUM(DISTINCT bil_total) AS total 
				FROM bil_biller 
				LEFT JOIN bil_breakdown USING (idbiller) 
				LEFT JOIN bil_account_lev1 USING (idaccountlev1) 
				WHERE bil_date BETWEEN '".$from."' AND '".$to."' 
				GROUP BY idaccountlev1 
				ORDER BY total DESC
				";
				$this->executeQuery($query_bil);
				$this->bil = $this->values;
				
				//var_dump($this->ca, $this->bil);exit;
				
				$this->v_createPiesData();
				$this->json['code'] = "piesCreated";
				$this->json['modal_title'] = gettext("Résultat Net")." - ".IntlDateFormatter::formatObject(new DateTime($date->format('Y-m-d'), new DateTimeZone('Europe/Paris')), 'MMMM y', 'fr')." : <strong>".alterData("euro",array_sum(array_column($this->ca, 'total')) - array_sum(array_column($this->bil, 'total')))."</strong>";
				return $this->json;
			}else{
				$this->json['code'] = "no-result";
				return $this->json;
			}
		}
		
		public function c_getBreakdown(){
			$dateFrom = date_create_from_format('d-m-Y', $this->dataSent['dateFrom']);
			$dateTo = date_create_from_format('d-m-Y', $this->dataSent['dateTo']);
			$from = date_format($dateFrom, 'Y-m-d');
			$to = date_format($dateTo, 'Y-m-d');
			
			// *** Bil lev1 ***
			$query_bil_lev1 = "
			SELECT acc_name, SUM(bre_amount) AS total 
			FROM bil_biller 
			LEFT JOIN bil_breakdown USING (idbiller) 
			LEFT JOIN bil_account_lev1 USING (idaccountlev1) 
			WHERE bil_date BETWEEN '".$from."' AND '".$to."' 
			AND idaccountlev1 > 0 
			GROUP BY idaccountlev1 
			ORDER BY total DESC
			";
			//var_dump($query_bil_lev1);
			$this->executeQuery($query_bil_lev1);
			$this->bil_lev1 = $this->values;
			
			// *** Bil lev2 ***
			$query_bil_lev2 = "
			SELECT acc_name,  SUM(bre_amount) AS total 
			FROM bil_biller 
			LEFT JOIN bil_breakdown USING (idbiller) 
			LEFT JOIN bil_account_lev2 USING (idaccountlev2) 
			WHERE bil_date BETWEEN '".$from."' AND '".$to."' 
			AND idaccountlev2 > 0 
			GROUP BY idaccountlev2 
			ORDER BY total DESC
			";
			$this->executeQuery($query_bil_lev2);
			$this->bil_lev2 = $this->values;
			
			// *** Bil lev3 ***
			$query_bil_lev3 = "
			SELECT acc_name, SUM(bre_amount) AS total 
			FROM bil_biller 
			LEFT JOIN bil_breakdown USING (idbiller) 
			LEFT JOIN bil_account_lev3 USING (idaccountlev3) 
			WHERE bil_date BETWEEN '".$from."' AND '".$to."' 
			AND idaccountlev3 > 0 
			GROUP BY idaccountlev3 
			ORDER BY total DESC
			";
			$this->executeQuery($query_bil_lev3);
			$this->bil_lev3 = $this->values;
			
			//var_dump($this->bil_lev1,$this->bil_lev2,$this->bil_lev3);exit;
			
			$this->v_createPiesBreakdown();
			$this->json['code'] = "piesBreakdownCreated";
			return $this->json;
		}
		public function __destruct()
		{
		}
	}