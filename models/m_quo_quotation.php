<?php
/**
*** Juillet 2019@SoluFile 
**/
	require_once 'model_crud.php';
	
	class quo_quotation_model extends crud {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->table 		= "quo_quotation";
			$this->key			= "idquotation";
			$this->duplicateKey = false;
			
			$this->fields 		= "quo_quotation.*, quo_quotation.idjob AS idlink, IF(quo_status IN (3,4), quo_amount, 0) AS quo_amount_ok, COALESCE(cli_short_name, cli_name) AS cli_name, CONCAT_WS(' - ', job_reference, job_surname) AS job_title, job_reference, job_name, sit_name, job_job.idsite AS job_idsite";
			
			$this->joins 		= "
				LEFT JOIN cli_client USING (idclient) 
				LEFT JOIN job_job USING (idjob) 
				LEFT JOIN sit_site ON job_job.idsite = sit_site.idsite 
			";
			$this->conds 		= array();
			$this->groups 		= array();
			$this->orders 		= array("quo_date DESC");
			$this->unset		= array("idquocomment","idwork","lin_description","lin_unit","lin_qty_formula","lin_pu");
			$this->fieldName	= "quo_ref";
			$this->linkTable	= "job_job";
			
			/*** Table filtering ***/
			$this->filters		= array();
			$this->filters['quo_status'] = array("id"=>"quo_status","in"=>array(1),"label"=>"Statut");
			
			/*** Select filtering ***/
			$this->idparent		= "idjob";
		}

		public function m_newRecord($insert=false)
		{
			$cfg = new cfg_config();
			$data = array(
				"idquotation"=>0,
				"idclient"=>0,
				"idsite"=>0,
				"idjob"=>0,
				"idlibrary"=>null,
				"quo_ref"=>$cfg->getNumber("quo"),
				"quo_date"=>date("Y-m-d"),
				"quo_title"=>"",
				"quo_status"=>1,
				"quo_amount"=>0,
				"quo_idvat"=>1,
				"quo_tot_amount"=>0,
				"quo_order_ref"=>null,
				"quo_signatory"=>null,
				"quo_remark"=>"",
				"quo_dpgf_file"=>"",
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
					foreach($filters as $key=>$val){
						$this->conds[] = $key." IN(".implode(",",$val).")";
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
			$this->conds = array("idquotation = ".$idrecord);
			//$this->orders = array();
			//$this->debugging = true;
			
			return $this->select();
		}
		
		public function m_insert($dataSent)
		{
			parse_str($dataSent, $data);
			//$this->debugging = true;
			
			return $this->insert($data);
		}
		
		public function m_update($idrecord, $data)
		{
			//$this->joins = "";
			$this->conds = array("idquotation = ".$idrecord);
			//$this->debugging = true;
			
			// update job_status => En cours if quo_status = Signé
			if(isset($data['quo_status']) && $data['quo_status'] == 3){
				$query = "UPDATE quo_quotation 
				LEFT JOIN job_job USING (idjob) 
				SET job_status = IF(job_status<2 ,2 ,job_status) 
				WHERE idquotation = $idrecord";
				
				$this->executeQuery($query);
			}
			
			//remove vat
			$keys = array_keys($data); // Get the keys
			$filter = preg_grep('#^vat#', $keys); // Get the keys starting with not__
			$output = array_diff_key($data, array_flip($filter)); // Filter it
			
			return $this->update($output);
		}
		
		public function m_updateStatus($idquotation, $quo_status, $job_status)
		{
			$query = "UPDATE quo_quotation 
			LEFT JOIN job_job USING (idjob) 
			SET quo_status = IF(quo_status<$quo_status, $quo_status ,quo_status), job_status = IF(job_status<$job_status ,$job_status ,job_status) 
			WHERE idquotation = $idquotation";
			
			return $this->executeQuery($query);
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
				case 1 : $this->fields = "idquotation AS id, quo_ref AS val, '' AS tokens"; $this->select(); break;
			}
			return true;
		}
		
		public function m_duplicate($idrecord)
		{
			$cfg = new cfg_config();
			
			// quo_quotation
			$query = "
			INSERT INTO quo_quotation (idclient, idsite, idjob, idlibrary, quo_ref, quo_date, quo_title, quo_status, quo_variables, quo_amount, quo_tva_percent, quo_tot_amount, quo_ref_client, quo_order_ref, quo_remark) 
			SELECT idclient, idsite, idjob, idlibrary, '".$cfg->getNumber("quo")."', CURRENT_DATE(), quo_title, 1, quo_variables, quo_amount, quo_tva_percent, quo_tot_amount, quo_ref_client, '', quo_remark 
			FROM quo_quotation 
			WHERE idquotation = $idrecord
			";
			$this->executeQuery($query);
			$this->result['newId'] = $this->result['idparent'];
			
			// quo_line
			$query = "
			INSERT INTO quo_line (idquotation, idvat, lin_order, idwork, lin_description, lin_unit, lin_quantity, lin_qty_formula, lin_pu, lin_pu_formula, lin_total) 
			SELECT ".$this->result['newId'].", idvat, lin_order, idwork, lin_description, lin_unit, lin_quantity, lin_qty_formula, lin_pu, lin_pu_formula, lin_total 
			FROM quo_line 
			WHERE idquotation = $idrecord 
			ORDER BY lin_order
			";
			$this->executeQuery($query);
			
			// quo_line_comment
			$query = "
			INSERT INTO quo_line_comment (idquotation, idquocomment) 
			SELECT ".$this->result['newId'].", idquocomment 
			FROM quo_line_comment 
			WHERE idquotation = $idrecord
			";
			$this->executeQuery($query);
			
			return true;
		}
		
		public function m_createInvoice($idrecord)
		{
			$cfg = new cfg_config();
			// inv_invoice
			$query = "
			INSERT INTO inv_invoice (idclient, idsite, idjob, idquotation, inv_type, inv_ref, inv_date, inv_title, inv_order_ref, inv_status, inv_tot_articles, inv_idvat, inv_tot_ttc, inv_deadline) 
			SELECT idclient, idsite, idjob, idquotation, 0, '".$cfg->getNumber("inv")."', CURRENT_DATE(), quo_title, quo_order_ref, 0, quo_amount, quo_idvat, quo_tot_amount, '".date("Y-m-j",strtotime("+2 month", strtotime(date_format(date_create(date("Y")."-".date("m")."-15"),"Y-m-d"))))."' 
			FROM quo_quotation 
			WHERE idquotation = $idrecord
			";
			$this->executeQuery($query);
			$idinvoice = $this->result['idparent'];
			// inv_line
			$query = "
			INSERT INTO inv_line (idinvoice, idvat, lin_description, lin_unit, lin_quantity, lin_pu, lin_total) 
			SELECT ".$idinvoice.", quo_idvat, CONCAT('Suivant Devis n° ', quo_ref), '', 1, quo_amount, quo_amount 
			FROM quo_quotation 
			WHERE idquotation = $idrecord
			";
			$this->executeQuery($query);
			$this->result['idparent'] = $idinvoice;
			return true;
		}
		
		public function m_createPercentInvoice($idrecord)
		{
			$cfg = new cfg_config();
			// inv_invoice
			$query = "
			INSERT INTO inv_invoice (idclient, idsite, idjob, idquotation, inv_type, inv_ref, inv_date, inv_title, inv_situation, inv_order_ref, inv_status, inv_tot_articles, inv_idvat, inv_tot_ttc, inv_deadline) 
			SELECT idclient, idsite, idjob, idquotation, 0, '".$cfg->getNumber("inv")."', CURRENT_DATE(), quo_title, quo_inv_situation, quo_order_ref, 0, quo_amount, quo_idvat, 0, '".date("Y-m-j",strtotime("+2 month", strtotime(date_format(date_create(date("Y")."-".date("m")."-15"),"Y-m-d"))))."' 
			FROM quo_quotation 
			WHERE idquotation = $idrecord
			";
			$this->executeQuery($query);
			$idinvoice = $this->result['idparent'];
			// inv_line
			$query = "
			INSERT INTO inv_line (idinvoice, idquoline, idwork, idvat, lin_description, lin_unit, lin_quantity, lin_pu, lin_total, lin_percent, lin_situation) 
			SELECT ".$idinvoice.", idline, idwork, idvat, COALESCE(lin_description, wor_name), lin_unit, lin_quantity, lin_pu, lin_total, lin_step_situation, IF(lin_step_situation > 0, lin_quantity*lin_pu*lin_step_situation/100, 0)
			FROM quo_line 
			LEFT JOIN lib_work USING (idwork) 
			WHERE idquotation = $idrecord
			ORDER BY lin_order
			";
			$this->executeQuery($query);
			$this->result['idparent'] = $idinvoice;
			return true;
		}
		
		public function m_createFullInvoice($idrecord)
		{
			$cfg = new cfg_config();
			// inv_invoice
			$query = "
			INSERT INTO inv_invoice (idclient, idsite, idjob, idquotation, inv_type, inv_ref, inv_date, inv_title, inv_order_ref, inv_status, inv_tot_articles, inv_idvat, inv_tot_ttc, inv_deadline) 
			SELECT idclient, idsite, idjob, idquotation, 0, '".$cfg->getNumber("inv")."', CURRENT_DATE(), quo_title, quo_order_ref, 0, quo_amount, quo_idvat, quo_tot_amount, '".date("Y-m-j",strtotime("+2 month", strtotime(date_format(date_create(date("Y")."-".date("m")."-15"),"Y-m-d"))))."' 
			FROM quo_quotation 
			WHERE idquotation = $idrecord
			";
			$this->executeQuery($query);
			$idinvoice = $this->result['idparent'];
			// inv_line
			$query = "
			INSERT INTO inv_line (idinvoice, idvat, lin_description, lin_unit, lin_quantity, lin_pu, lin_total) 
			SELECT ".$idinvoice.", idvat, COALESCE(lin_description, wor_name), lin_unit, lin_quantity, lin_pu, lin_total 
			FROM quo_line 
			LEFT JOIN lib_work USING (idwork) 
			WHERE idquotation = $idrecord 
			ORDER BY lin_order
			";
			$this->executeQuery($query);
			$this->result['idparent'] = $idinvoice;
			return true;
		}
		
		public function m_importToInvoice($idquotation, $idinvoice, $title)
		{
			// title
			$query = "
			INSERT INTO inv_line (idinvoice, lin_description, lin_unit) 
			VALUES (".$idinvoice.", '".$title."', 'titre')
			";
			//dump($query);
			$this->executeQuery($query);
			
			// inv_line
			$query = "
			INSERT INTO inv_line (idinvoice, idvat, lin_description, lin_unit, lin_quantity, lin_pu, lin_total) 
			SELECT ".$idinvoice.", idvat, COALESCE(lin_description, wor_name), lin_unit, lin_quantity, lin_pu, lin_total 
			FROM quo_line 
			LEFT JOIN lib_work USING (idwork) 
			WHERE idquotation = $idquotation 
			ORDER BY lin_order
			";
			$this->executeQuery($query);
			$this->result['idparent'] = $idinvoice;
			return true;
		}
		
		public function __destruct()
		{
		}
	}