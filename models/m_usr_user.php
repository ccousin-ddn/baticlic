<?php
/**
*** June 2019@SoluFile 
**/
	require_once 'model_crud.php';
	
	class model extends crud {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->table 		= 'usr_user';
			$this->key			= 'iduser';
			$this->duplicateKey = false;
			
			$this->fields 		= 'usr_user.*';
			
			$this->joins 		= '';
			$this->conds 		= array();
			$this->groups 		= array();
			$this->orders 		= array("usr_lastname");
			$this->unset		= array("usr_new_password");
			$this->unclean		= array();
			
			$this->filters['usr_type'] = array("id"=>"usr_type","in"=>array(),"label"=>"Type");
		}

		public function m_newRecord()
		{
			$this->values = (object) array(
				"iduser"=>0,
				"usr_firstname"=>"",
				"usr_lastname"=>"",
				"usr_email"=>"",
				"usr_login"=>"",
				"usr_password"=>"",
				"usr_level"=>0,
				"usr_type"=>1,
				"usr_dashboard"=>"5",
				"usr_language"=>LANG,
				);
			return true;
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
			$this->conds = array("usr_user.iduser = ".$idrecord);
			//$this->orders = array();
			//$this->debugging = true;
			
			return $this->select();
		}
		
		public function m_getByEmail($email)
		{
			$this->fields = "usr_user.*, 1 AS idtype";
			$this->conds = array("LOWER(usr_email) = '".$email."'");
			//$this->orders = array();
			//$this->debugging = true;
			
			return $this->select();
		}
		
		public function m_checkEmail($email)
		{
			$this->fields = "usr_user.iduser";
			$this->joins = "";
			$this->conds = array("usr_email = '".strongEncrypt($email)."'");
			//$this->orders = array();
			//$this->debugging = true;
			$this->select();
			if($this->count > 0) {return false;}else{return true;}
		}
		
		public function m_insert($data)
		{
			//$this->joins = "";
			if(!empty($data['usr_new_password'])){
				$data['usr_password'] = password_hash($data['usr_new_password'], PASSWORD_DEFAULT);
			}
			//$this->debugging = true;
			
			return $this->insert($data);
		}
		
		public function m_update($idrecord, $data)
		{
			//$this->joins = "";
			$this->conds = array("usr_user.iduser = ".$idrecord);
			if(!empty($data['usr_new_password'])){
				$data['usr_password'] = password_hash($data['usr_new_password'], PASSWORD_DEFAULT);
			}
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
				case 1 : $this->fields = "usr_user.iduser AS id, usr_firstname AS val"; $this->select(); break;
				case 2 : $this->fields = "usr_user.iduser AS id, CONCAT(usr_firstname,' ',usr_lastname) AS val"; $this->select(); break;
				case "usr_level" : 
					foreach($this->usr_level as $key=>$val){
						$newArr[] = array("id"=>$key,"val"=>$val);
					}
					$this->result['idparent'] = "Niveau";
					return $newArr;
					break;
			}
			return true;
		}
		
		public function m_login($login)
		{
			$query = "
				SELECT iduser, usr_firstname, usr_lastname, usr_level, usr_type, usr_password 
				FROM usr_user 
				WHERE LOWER(usr_login) = LOWER('$login') 
				UNION 
				SELECT idworker AS iduser, wor_name AS usr_firstname, '' AS usr_lastname, 1 AS usr_level, 0 AS usr_type, wor_password AS usr_password 
				FROM wor_worker 
				WHERE LOWER(wor_login) = LOWER('$login')
			";
			if($this->executeQuery($query)){
				return true;
			}else{
				return false;
			}
		}
		
		public function m_dashboard($iduser)
		{
			$query = "SELECT usr_dashboard FROM usr_user WHERE iduser = ".$iduser;
			
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
