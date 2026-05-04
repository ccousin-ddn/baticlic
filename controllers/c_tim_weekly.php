<?php
/**
*** Octobre 2021@SoluFile 
**/
	require_once (dirname(__FILE__)."/../views/v_tim_weekly.php");
	
	class tim_weekly extends tim_weekly_view {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->label 		= gettext("weekly");
			$this->labels 		= gettext("weeklys");
			$this->newtext		= gettext("Nouveau weekly");
			$this->picto 		= '<i class=" fa-fw"></i>';
			$this->level		= 1;
			
			$this->cryptfields	= array("");
			
			$this->lnk			= array(); // GROUP_CONCAT(idlnk) idlnk
			
			//$this->arrayName	= array(0=>"No",1=>"Yes");
		}

		public function c_insert(){
			if($this->checkUserRight()){
				// *** model ***
				$this->duplicateKey = false;
				$data = $this->dataSent;
				
				$data_uri = $data['signature'];
				if (strpos($data_uri, ",") !== false) {
					$encoded_image = explode(",", $data_uri)[1];
					$decoded_image = base64_decode($encoded_image);
					$file = md5(date("sYmidH")).".png";
					file_put_contents(dirname(__FILE__)."/../upload/signatures/".$file, $decoded_image);
					$data['tim_signature'] = $file;
					$data['idworker'] = decrypt($this->idrecord);
					unset($data['signature']);
					
					if($this->m_insert($data)){
						$this->json['code'] = "signatureInserted";
						$this->json['idparent'] = encrypt($this->result['idparent']);
						$this->json['info'] = getText("Signature ajoutée");
						return $this->json;
					}else{
						throw new Exception('PHP : Error in controller insert', 220);
					}	
				}
			}
		}

		public function c_delete(){
			if($this->checkUserRight()){
				if($this->m_delete(decrypt($this->idrecord))){
					$this->json['code'] = "deleted";
					$this->json['info'] = getText("weekly supprimé");
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