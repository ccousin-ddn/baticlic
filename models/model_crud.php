<?php
    /**
    * @author SoluFile
    * @copyright 2019
    **/
    require_once 'model_mysqli.php';

    class crud{

		public $dataSent;
		public $values;
		public $json;
		public $count;
		public $columns;
		
		public $table;
		public $key;
		public $label;
		public $picto;
		
		public $fields;
		public $joins;
		public $conds;
		public $groups;
		public $having;
		public $orders;
		public $limit;
		public $offset;
		
		public $debugging;
		
		public $unclean = array();
		
		public $yesno = array();
		public $month = array();
		public $lang = array();

        public function __construct()
        {
            $this->table = null;
			$this->count = 0;
			$this->limit = 0;
			$this->offset = -1;
			$this->debugging = false;
			$this->dataSent = array();
			$this->values = "";
        	$this->json = array("status"=>true, "code"=>"", "html"=>"", "idparent"=>0, "idchild"=>0, "idother"=>0, "info"=>"");
			$this->yesno = array(1=>gettext("Oui"), 0=>gettext("Non"));
			$this->lang = array("fr"=>gettext("fr"),"nl"=>gettext("nl"),"en"=>gettext("en"));
			$this->gender = array(1=>gettext("Homme"),2=>gettext("Femme"));
			$this->month = array(1=>"Janvier",2=>"Février",3=>"Mars",4=>"Avril",5=>"Mai",6=>"Juin",7=>"Juillet",8=>"Août",9=>"Septembre",10=>"Octobre",11=>"Novembre",12=>"Décembre");
        }

		public function debug($lib ,$message, $query)
		{
			if($_SESSION['debug'] != false){
				$message = str_replace(array("\n","\r","\r\n","\t"), '', $message??"");
				//$query = str_replace(array("\n","\r","\r\n","\t"), '', $query);
				$query = preg_replace('/(\v|\s)+/', ' ', $query);
				$error = $message != "" ? $message." | ".$query : $query;
				$data = array('type' => 'error', 'message' => $lib.' : '.$error);
		        echo json_encode($lib.' : '.$error);
			}
			exit;
		}

		public function checkUserRight()
		{
			if($_SESSION['usr_level'] < $this->level){
				throw new Exception('SECURITY : User level to low in '.$this->table, 200);
				return false;
			}else{
				return true;
			}
		}

		public function c_filter() /*** Table filtering ***/
		{
			// *** model ***
			if(isset($this->{$this->dataSent['table']})){
				foreach($this->{$this->dataSent['table']} as $key=>$val){
					$dataList[] = array("id"=>$key,"val"=>$val);
				}
				$search = false;
			}else{
				$list = new $this->dataSent['table'];
				if($list->m_getList(1)){
					$dataList = $list->values;
				}
				$search = true;
			}
			
			$input = new input($dataList);
			$this->json['info'] = $this->filters[$this->dataSent['table']]['id'];
			$this->json['idparent'] = $this->filters[$this->dataSent['table']]['label'];
			$this->json['idchild'] = $this->filters[$this->dataSent['table']]['in'];
			$this->json['code'] = 1;
			$this->json['order'] = $this->idrecord;
			
			if($this->dataSent['table'] == "wor_year" || $this->dataSent['table'] == "wor_week"){
				$this->json['code'] = "wor_periode";
			}
			$html = $input->create("selectForFilter", $this->json['idparent'], $this->json['info'], false, $this->json['idchild'],"","",array("search"=>$search));
			$this->json['html'] = $html;
			return $this->json;
		}
		
		public function c_filterChild($numList = 1, $codeReturn = 1, $conds = array()) /*** select child filtering ***/
		{
			// *** model ***
			$numList = $this->dataSent['numList']??1;
			$codeReturn = $this->dataSent['codeReturn']??1;
			switch($this->table){
				case "job_job" :
					if(isset($this->dataSent['idclient'])){
						$conds = array("idclient = ".$this->dataSent['idclient']);
					}
					break;
				default :
					$conds = $this->dataSent['conds']??array($this->table.".".$this->idparent."=".$this->idrecord);
			}
			if($this->m_getList($numList,$conds)){
				$this->json['info'] = "";
				$this->json['idchild'] = $this->key;
				if(count($this->values) > 0){
					$this->json['code'] = $codeReturn;
					// *** view ***
					$input = new input($this->values);
					$this->json['html'] = $input->create("option","","",false);
				}else{
					$this->json['code'] = $this->dataSent['codeReturn']??0;
					$this->json['html'] = "";
				}
				return $this->json;
			}else{
				throw new Exception('PHP : Error in controller filterChild', 230);
			}
		}

		public function c_newFrom() /*** select child filtering ***/
		{
			if($this->checkUserRight()){
				// *** model ***
				$res = $this->m_newRecord(false);
				// *** view	***
				if($res){
					$doc_document = new doc_document();
					$doc_document->json['html'] = '';
					$doc_document->count = 0;
					$this->v_createCard($doc_document);
					$this->json['code'] = 1;
					$this->json['idparent'] = $this->idrecord;
					$this->json['tableChild'] = $this->table;
					$this->json['parentModal'] = $this->dataSent['parentModal'];
					$this->json['fieldName'] = $this->fieldName;
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller newFrom', 230);
				}
			}
		}

		public function c_sendMail()
		{
			$send_mail = new sendmail();
			parse_str($this->dataSent, $data);
	
			$send_mail->from       	= $data['from'];
			$send_mail->reply      	= $data['from'];
			$send_mail->to         	= $data['to'];
			$send_mail->bcc        	= $data['bcc']; //"laurent.anezo@gmail.com";
			$send_mail->subject		= $data['subject']; 
			$send_mail->message		= array('',nl2br($data['message']));
			if(isset($data['attach'])){
				$send_mail->attach	= "../pdf_tmp/".$data['attach'];
			}
			
			if($send_mail->send()){
				$this->json['info'] = gettext("Mail envoyé.");
			}else{
				$this->json['code'] = 0;
				$this->json['info'] = $send_mail->error;
			}
			return $this->json;
		}

		public function c_deleteImg()
		{
			if($this->checkUserRight()){
				// *** deleting files ***
				if(empty($this->dataSent['resize'])){
					@unlink(dirname(__FILE__)."/../".UPLOAD_PATH.$this->dataSent['image']);
				}else{
					$resizes = explode(",",$this->dataSent['resize']);
					foreach($resizes as $resize){
						$path = explode(":",$resize);
						//var_dump(dirname(__FILE__)."/../".UPLOAD_PATH.$path[1].$this->values[0]['doc_slug_name']);
						@unlink(dirname(__FILE__)."/../".UPLOAD_PATH.$path[1].$this->dataSent['image']);
					}
				}
				
				$this->json['code'] = "imageDeleted";
				return $this->json;
			}
		}

	    public function clean($string)
	    {
	    	if(gettype($string) == "integer"){return $string;}
	    	$string = trim($string??"");
			if(substr_count($string, '-') == 2){
				if(strlen($string) == 10){
				   $date = explode("-", $string);
					if(checkdate($date[1],$date[0],$date[2])){
						return $date[2].$date[1].$date[0];
					}
					if(checkdate($date[1],$date[2],$date[0])){
						return $date[0].$date[1].$date[2];
					}
				}elseif(strlen($string) == 16){
					return date('Y-m-d H:i:s', strtotime($string));
				}else{
			        $search = array("\\","\x00","\n","\r","\x1a","'",'"',"<",">",";");
			        $replace = array("\\\\","\\0","\\n","\\r","\\Z","\'",'\"');
			        $string = str_replace($search,$replace,$string);
			        return $string;					
				}
			}else{
		        $search = array("\\","\x00","\n","\r","\x1a","'",'"',"<",">",";");
		        $replace = array("\\\\","\\0","\\n","\\r","\\Z","\'",'\"');
		        $string = str_replace($search,$replace,$string);
		        return $string;
			}
	    }
		
		public function cleanSoft($string)
	    {
	    	$string = trim($string);
	        $search = array("\\","\x00","\n","\r","\x1a","'",'"',";");
	        $replace = array("\\\\","\\0","\\n","\\r","\\Z","\'",'\"');
	        $string = str_replace($search,$replace,$string);
	        return $string;
	    }
		
		private function cryptData($column, $value)
		{
			if($value != ""){
				if (in_array($column, $this->cryptfields)) {	
					$value = strongEncrypt($value);
				}
			}
			return $value;
		}

        // INSERT *****************
		public function insert($data)
		{
			// Création de la requête
			$_query = "INSERT IGNORE INTO ".$this->table." SET";
			
			if($this->table != "doc_document"){
				/*** Remove doc data***/
				if(isset($data['doc_from'])){unset($data['doc_from']);}
				if(isset($data['idfrom'])){unset($data['idfrom']);}
			}
			if(!empty($this->unset)){
				foreach($this->unset as $field){
					unset($data[$field]);
				}
			}
			
			$_set = "";
			foreach ($data as $column => $value){
				if(!is_array($value)){
					if($value == ""){
						$_set .= " ".$column." = NULL,";
					}else{
						// mysql function in data
						if(strval($value)[0] == "*"){
							$value = ltrim($value, '*');
							$_set .= " ".$column." = ".$value.",";
						}else{
							if (in_array($column, $this->unclean??array())) {	
								$_set .= " ".$column." = '".$this->cryptData($column,$this->cleanSoft($value))."',";
							}else{
								$_set .= " ".$column." = '".$this->cryptData($column,$this->clean($value))."',";
							}
						}
					}
				}
			}
			// Suppression de la dernière virgule
            $_set = substr($_set, 0, strlen($_set)-1);
			$_query .= $_set;
			
			// On duplicate key
			if($this->duplicateKey === true){
				$_query .= " ON DUPLICATE KEY UPDATE ".$this->key." = LAST_INSERT_ID(".$this->key."),".$_set;
			}

			// Debug
			if($this->debugging){$this->debug("DEBUG-MODEL",'',$_query); exit;}
			
			// Exécution de la requête
			$mysql = new mysql();
			
			if($mysql->query($_query)){
				$this->result['idparent'] = $mysql->lastId();
            	return true;
			}else{
				$this->result['info'] = $mysql->error;
				return false;
			}
		}

        // INSERT MULTI ***********
		public function insertMulti($lnk)
		{
			$_insert = "";
			// Création de la requête
			$_query = "	DELETE FROM ".$lnk->table." WHERE ".$lnk->key." = ".$lnk->keyValue.";";
						
			foreach ($lnk->values as $value){
				if(is_array($value)){
					$_insert .= "(".$lnk->keyValue.",".implode(",", $value)."),";
				}else{
					if($value > -1){
						$_insert .= "(".$lnk->keyValue.",".$value."),";
					}
				}
			}
			
			if($_insert != ""){
				$_query = $_query ."INSERT INTO ".$lnk->table." (".$lnk->key.", ".$lnk->keyLnk.") VALUES ".$_insert;
			}

			// Suppression de la dernière virgule
            $_query = substr($_query, 0, strlen($_query)-1);
			// Debug
			if($lnk->debugging){$lnk->debug("DEBUG-MODEL",'',$_query); exit;}
			
			// Exécution de la requête
			$mysql = new mysql();
			
			if($mysql->query($_query)){
            	return true;
			}else{
				return false;
			}
		}

        // INSERT MULTI COMPLEX ***
		public function insertMultiComplex($data)
		{
			// Création de la requête
			$_query = "	DELETE FROM ".$this->table." WHERE ".$this->key." = ".$data->keyValue.";
						INSERT INTO ".$this->table."
                        VALUES ";
			foreach ($data as $value){
				if(is_array($value)){
					$_query .= "(".$data->keyValue.",".implode(",", $value)."),";
				}else{
					$_query .= "(".$data->keyValue.",".$value."),";
				}
			}
			// Suppression de la dernière virgule
            $_query = substr($_query, 0, strlen($_query)-1);

			// Debug
			if($this->debugging){$this->debug("DEBUG-MODEL",'',$_query); exit;}
			
			// Exécution de la requête
			$mysql = new mysql();
            
			if($mysql->query($_query)){
            	return true;
			}else{
				return false;
			}
		}

        // UPDATE ****************
        public function update($data)
        {
        	// Création de la requête
			$_query = "UPDATE ".$this->table." SET";
			//$_query = "UPDATE ".$this->table." ".$this->joins." SET";
			
			/*** Remove doc data***/
			if(isset($data['doc_from'])){unset($data['doc_from']);}
			if(isset($data['idfrom'])){unset($data['idfrom']);}
			
			if(!empty($this->unset)){
				foreach($this->unset as $field){
					if(isset($data[$field])){unset($data[$field]);}
				}
			}
			
			foreach ($data as $column => $value){
				if(!is_array($value)){
					if($value == ""){
						$_query .= " ".$column." = NULL,";
					}else{
						if (in_array($column, $this->unclean??array())) {	
							$_query .= " ".$column." = '".$this->cryptData($column,$this->cleanSoft($value))."',";
						}else{
							$_query .= " ".$column." = '".$this->cryptData($column,$this->clean($value))."',";
						}
					}
				}
			}
			// Suppression de la dernière virgule
            $_query = substr($_query, 0, strlen($_query)-1);
			
			// Where
			if((is_array($this->conds))&&(!empty(array_filter($this->conds)))){
				$_query .= " WHERE ";
				$i = 0;
			}
			if ((is_array($this->conds))&&(!empty(array_filter($this->conds))))
	    	{
	        	foreach ($this->conds as $cond)
	        	{
	        		if($cond !=""){
		            	$_query .= ($i != 0)? " AND " : '';
		            	$_query .= $cond;
		            	$i++;
					}
	        	}
	    	}

			// Debug
			if($this->debugging){$this->debug("DEBUG-MODEL",'',$_query); exit;}
			//if($this->debugging){echo $_query; exit;}
			
			// Exécution de la requête
			$mysql = new mysql();
			
			if($mysql->query($_query)){
            	return true;
			}else{
				return false;
			}
        }

        // DELETE ****************
        public function delete($idrecord)
        {
        	// Création de la requête
			$_query = "DELETE FROM ".$this->table." WHERE ".$this->key." = ".$idrecord;
			
			// Debug
			if($this->debugging){$this->debug("DEBUG-MODEL",'',$_query); exit;}
			
			// Exécution de la requête
			$mysql = new mysql();
			
			if($mysql->query($_query)){
            	return true;
			}else{
				return false;
			}
        }

        // SELECT ********************
        public function select()
        {
        	// Création de la requête
			$_query = " SELECT ".$this->fields." 
						FROM ".$this->table." ".
						$this->joins."
			";
			// Where
			if((is_array($this->conds))&&(!empty(array_filter($this->conds)))){
				$_query .= " WHERE ";
				$i = 0;
			}
			if ((is_array($this->conds))&&(!empty(array_filter($this->conds))))
	    	{
	        	foreach ($this->conds as $cond)
	        	{
	        		if($cond !=""){
		            	$_query .= ($i != 0)? " AND " : '';
		            	$_query .= str_replace(array(";"), '',trim($cond));
		            	$i++;
					}
	        	}
	    	}
			// Group
			if ((is_array($this->groups))&&(!empty(array_filter($this->groups))))
	    	{
	        	$_query .= " GROUP BY ";
	        	$i = 0;
	        	foreach ($this->groups as $group)
	        	{
	            	$_query .= ($i != 0)? ", " : '';
	            	$_query .= $group;
	            	$i++;
	        	}
	    	}
			// Having
			if((is_array($this->having))&&(!empty(array_filter($this->having)))){
				$_query .= " HAVING ";
				$i = 0;
			}
			if ((is_array($this->having))&&(!empty(array_filter($this->having))))
	    	{
	        	foreach ($this->having as $having)
	        	{
	        		if($having !=""){
		            	$_query .= ($i != 0)? " AND " : '';
		            	$_query .= $having;
		            	$i++;
					}
	        	}
	    	}
			// Order
			if ((is_array($this->orders))&&(!empty(array_filter($this->orders))))
	    	{
	        	$_query .= " ORDER BY ";
	        	$i = 0;
	        	foreach ($this->orders as $order)
	        	{
	            	$_query .= ($i != 0)? ", " : '';
	            	$_query .= $order;
	            	$i++;
	        	}
	    	}
			// Limit
			if ($this->limit > 0)
	    	{
	        	$_query .= " LIMIT ".$this->limit;
	    	}
			// Offset
			if ($this->offset > -1)
	    	{
	        	$_query .= " OFFSET ".$this->offset;
	    	}
			
			// Debug
			if($this->debugging){$this->debug("DEBUG-MODEL",'',$_query); exit;}
			
			// Exécution de la requête
			$mysql = new mysql();

			// Retour du résultat
            if($mysql->query($_query)){
				$this->values = $mysql->result();
				$this->count = $mysql->count;
            	return true;
			}else{
				return false;
			}
        }

		public function lnkProcess($keyValue, $data, $delete=false)
		{
			foreach($this->lnk as $lnkTable){
				$lnk = new $lnkTable();
				$lnk->keyValue = $keyValue;
				if(!empty($data[$lnk->keyLnk])){
					$lnk->values = $data[$lnk->keyLnk];
					$result = $lnk->insertMulti($lnk);
				}elseif($delete){
					$lnk->delete($keyValue);
				}
			}
			return true;
		}

		// EXECUTE QUERY ****************
        public function executeQuery($_query)
        {
			// Debug
			if($this->debugging){$this->debug("DEBUG-MODEL",'',$_query); exit;}
			// Exécution de la requête
			$mysql = new mysql();
			
			if($mysql->query($_query)){
				$this->values = $mysql->result()??$this->values;
				$this->count = $mysql->count;
				$this->result['idparent'] = $mysql->lastId();
				return true;
			}else{
				return false;
			}
        }
		
		// EXECUTE MULTYQUERY ****************
        public function executeMultiQuery($_query)
        {
			// Debug
			if($this->debugging){$this->debug("DEBUG-MODEL",'',$_query); exit;}
			
			// Exécution de la requête
			$mysql = new mysql();
			
			if($mysql->query($_query)){
				$this->values = $mysql->multi_result()??$this->values;
				$this->count = $mysql->count;
				$this->multi_result['idparent'] = $mysql->lastId();
				return true;
			}else{
				return false;
			}
        }

        // CALL STORED PROC ************
        public function callStoredProc($sp, $param)
        {
        	// Création de la requête
			$_query = " CALL ".$sp."(".implode(",", $param).")";

			// Debug
			if($this->debugging){$this->debug("DEBUG-MODEL",'',$_query); exit;}
			
			// Exécution de la requête
			$mysql = new mysql();
			
			if($mysql->query($_query)){
            	return true;
			}else{
				return false;
			}
        }

        public function __destruct()
        {
        }
    }
