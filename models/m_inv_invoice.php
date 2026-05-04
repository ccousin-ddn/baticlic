<?php
/**
*** Juillet 2019@SoluFile 
**/
	require_once 'model_crud.php';
	
	class inv_invoice_model extends crud {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->table 		= "inv_invoice";
			$this->key			= "idinvoice";
			$this->duplicateKey = false;
			
			$this->fields 		= "inv_invoice.*, quo_ref, IF(inv_status > 0, IF(inv_type=0, inv_tot_articles, -inv_tot_articles), 0) AS inv_tot_articles_ok, IF(inv_type=0, inv_tot_situation, -inv_tot_situation) AS real_inv_tot_articles, UNIX_TIMESTAMP(inv_deadline) AS inv_utc, COALESCE(cli_short_name, cli_name) AS cli_name, sit_name, job_reference, SUM(pay_amount) AS tot_paid, (inv_tot_ttc - COALESCE(SUM(pay_amount),0)) AS tot_rest";
			
			$this->joins 		= "
				LEFT JOIN cli_client USING (idclient) 
				LEFT JOIN sit_site USING (idsite) 
				LEFT JOIN job_job USING (idjob)
				LEFT JOIN pay_payment USING (idinvoice) 
				LEFT JOIN quo_quotation USING (idquotation) 
			";
			$this->conds 		= array();
			$this->groups 		= array("idinvoice");
			$this->orders 		= array("inv_date DESC, inv_situation DESC");
			$this->fieldName	= "inv_ref";
			
			/*** Table filtering ***/
			$this->filters['inv_status'] = array("id"=>"inv_status","in"=>array(0,1),"label"=>"Statut");
			$this->filters['inv_type'] = array("id"=>"inv_type","in"=>array(0),"label"=>"Type");
			//ex : $this->filters['arrayName'] = array("id"=>"idColumn",in"=>array(0),"label"=>"label");
			
			/*** Select filtering ***/
			$this->idparent		= "cli_client";
		}

		public function m_newRecord($insert=false)
		{
			$cfg = new cfg_config();
			$data = array(
				"idinvoice"=>0,
				"idjob"=>0,
				"idclient"=>0,
				"idsite"=>0,
				"inv_type"=>0,
				"inv_ref"=>$cfg->getNumber("inv"),
				"inv_date"=>date("Y-m-d"),
				"inv_title"=>"",
				"inv_order_ref"=>null,
				"inv_status"=>"0",
				"inv_tot_articles"=>0,
				"inv_idvat"=>1,
				"inv_tot_ttc"=>0,
				"inv_deadline"=>date("Y-m-j",strtotime("+2 month", strtotime(date_format(date_create(date("Y")."-".date("m")."-15"),"Y-m-d")))),
				"inv_remark"=>"",
				
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
			$this->conds = array("idinvoice = ".$idrecord);
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
		
		public function m_update($idrecord, $dataSent)
		{
			//$this->joins = "";
			$this->conds = array("idinvoice = ".$idrecord);
			parse_str($dataSent, $data);
			//$this->debugging = true;
			
			//remove vat
			$keys = array_keys($data); // Get the keys
			$filter = preg_grep('#^vat#', $keys); // Get the keys starting with not__
			$output = array_diff_key($data, array_flip($filter)); // Filter it
			
			return $this->update($output);
		}
		
		public function m_updateStatus($idinvoice, $inv_status, $job_status)
		{
			$query = "UPDATE inv_invoice
			LEFT JOIN job_job USING (idjob)
			SET inv_status = IF(inv_status<$inv_status, $inv_status ,inv_status), job_status = IF(job_status<$job_status ,$job_status ,job_status)
			WHERE idinvoice = $idinvoice";
			
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
				case 1 : $this->fields = "idinvoice AS id, inv_name AS val, '' AS tokens"; $this->select(); break;
				case 2 : 
					$this->fields = "idinvoice AS id, inv_ref AS val, inv_title AS subtext, inv_tot_situation, inv_tot_articles"; 
					$this->joins = "";
					$this->groups = array();
					$this->select(); 
					break;
			}
			return true;
		}
		
		public function m_getRest($idinvoice)
		{
			$query = "
			SELECT (inv_tot_ttc - COALESCE(SUM(pay_amount),0)) AS rest
			FROM inv_invoice
			LEFT JOIN pay_payment USING (idinvoice)
			WHERE idinvoice = ".$idinvoice."
			GROUP BY idinvoice
			";
			//$this->debugging = true;
			if($this->executeQuery($query)){
				return $this->values[0]['rest'];
			}else{
				return false;
			}
		}
		
		public function __destruct()
		{
		}
	}