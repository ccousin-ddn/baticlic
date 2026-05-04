<?php
/**
*** Janvier 2022@SoluFile 
**/
	require_once (dirname(__FILE__)."/../views/v_job_picture.php");
	
	class job_picture extends job_picture_view {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->label 		= gettext("Photos");
			$this->labels 		= gettext("Photoss");
			$this->newtext		= gettext("Nouveau Photos");
			$this->picto 		= '<i class="fal fa-camera fa-fw"></i>';
			$this->level		= 1;
			
			$this->cryptfields	= array("");
			
			$this->lnk			= array(); // GROUP_CONCAT(idlnk) idlnk
			
			//$this->arrayName	= array(0=>"No",1=>"Yes");
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
				$this->conds[] = "idjob = ".decrypt($this->idrecord);
				if($this->m_getAll()){
					//var_dump($this->values);exit;
					// *** view ***
					$this->v_createPictures();
					$this->json['code'] = 1;
					return true;
				}else{
					throw new Exception('PHP : Error in controller tableByParent', 201);
				}
			}
		}
		
		public function c_savePicture(){
			$doc = new doc_document();
			$data = $this->dataSent;
			$data['doc_from'] = 'job_picture';
			$data['idfrom'] = decrypt($this->idrecord);
			$data['doc_info'] = $_SESSION['usr_firstname'];
			$data['doc_type'] = 1;
			
			$doc->insert($data);
			
			$this->json['code'] = "pictureJobSaved";
			return $this->json;
		}

		public function c_insert(){
			if($this->checkUserRight()){
				// *** model ***
				$this->duplicateKey = false;
				parse_str($this->dataSent, $data);
				if($this->m_insert($data)){
					$this->json['code'] = "inserted";
					$this->json['idparent'] = encrypt($this->result['idparent']);
					$this->json['info'] = getText("Photos ajouté");
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller insert', 220);
				}
			}
		}

		public function c_delete(){
			if($this->checkUserRight()){
				// *** get info ***
				$this->m_getById(decrypt($this->idrecord));
				// *** model ***
				if($this->m_delete(decrypt($this->idrecord))){
					// *** deleting files ***
					@unlink(dirname(__FILE__)."/../upload/pictures/".$this->values[0]['pic_name']);
					$this->json['code'] = "pictureJobDeleted";
					$this->json['info'] = getText("Photo supprimée");
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller delete', 222);
				}
			}
		}

		public function __destruct()
		{
		}
	}