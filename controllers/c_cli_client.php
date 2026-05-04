<?php
/**
*** Juillet 2019@SoluFile 
**/
	require_once (dirname(__FILE__)."/../views/v_cli_client.php");
	
	class cli_client extends cli_client_view {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->label 		= gettext("Client");
			$this->labels 		= gettext("Clients");
			$this->newtext		= gettext("Nouveau client");
			$this->picto 		= '<i class="fal fa-address-card"></i>';
			$this->level		= 1;
			
			$this->cryptfields	= array("cli_siret","cli_tva","cli_rc","cli_email","cli_phone_1");
			
			$this->lnk			= array(); // GROUP_CONCAT(idlnk) idlnk
			
			$this->cli_status	= array(1=>"Client",2=>"Prospect",3=>"Perdu");
			$this->cli_rate_level = array(1=>"Small", 2=>"Medium", 3=>"Large");
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
				$this->conds = array("idparent = ".decrypt($this->idrecord));
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
				$this->values->idparent = decrypt($this->idrecord);
				$this->values = (array) $this->values;
				if($this->m_insert(http_build_query($this->values))){
					$this->json['code'] = "newLineInserted";
					$this->json['idparent'] = encrypt($this->result['idparent']);
					$this->json['info'] = getText("Client ajouté");
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
				$this->conds = array("cli_name LIKE '%".$this->dataSent['value']."%'");
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
				
				// *** sit_site ***
				$sit_site = new sit_site();
				$sit_site->idrecord = $this->idrecord;
				$res_site = $sit_site->c_tableByParent();
				
				// *** job_jobs ***
				$job_job = new job_job();
				$job_job->idrecord = $this->idrecord;
				$job_job->conds = array("sit_site.idclient = ".decrypt($this->idrecord),"job_status IN (1,2,3,4)");
				$res_job = $job_job->c_tableByParent();
				
				// *** quo_quotations ***
				$quo_quotation = new quo_quotation();
				$quo_quotation->idrecord = $this->idrecord;
				$quo_quotation->conds = array("quo_quotation.idclient = ".decrypt($this->idrecord), "quo_status IN (2,3,4)");
				$res_quotation = $quo_quotation->c_tableByParent();
				
				// *** inv_invoices ***
				$inv_invoice = new inv_invoice();
				$inv_invoice->idrecord = $this->idrecord;
				$inv_invoice->conds = array("inv_invoice.idclient = ".decrypt($this->idrecord), "inv_status IN (1,2,3)");
				$res_invoice = $inv_invoice->c_tableByParent();
				
				// *** cli_agency ***
				$cli_agency = new cli_agency();
				$cli_agency->idrecord = $this->idrecord;
				$cli_agency->c_tableByParent();
				
				// *** cli_contact ***
				$cli_contact = new cli_contact();
				$cli_contact->idrecord = $this->idrecord;
				$res_cont = $cli_contact->c_tableByParent();
				
				// *** cli_login ***
				$cli_login = new cli_login();
				$cli_login->idrecord = $this->idrecord;
				$res_login = $cli_login->c_tableByParent();
				
				// *** view ***	
				if($res){
					$this->v_createCard($sit_site,$job_job,$quo_quotation,$inv_invoice,$cli_agency,$cli_contact,$cli_login);
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
					$this->json['info'] = getText("Client ajouté");
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller insert', 220);
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
					$this->json['info'] = getText("Client ajouté");
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
						$this->json['info'] = getText("Client mis à jour");
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
					$this->json['info'] = getText("Client supprimé");
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