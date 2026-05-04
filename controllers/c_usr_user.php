<?php
/**
*** June 2019@SoluFile 
**/
	require_once (dirname(__FILE__)."/../views/v_usr_user.php");
	
	class usr_user extends view {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->label 		= gettext("Utilisateur");
			$this->labels 		= gettext("Utilisateurs");
			$this->newtext		= gettext("Nouvel utilisateur");
			$this->picto 		= '<i class="fal fa-users-cog"></i>';
			$this->level		= 2;
			
			$this->cryptfields	= array("usr_email");
			
			$this->lnk			= array(); // GROUP_CONCAT(idlnk) idlnk
			
			$this->usr_level	= array(0=>"User",1=>"Super User",2=>"Admin");
			$this->usr_type		= array(1=>'Administratif',2=>'Conducteur',3=>'Magasinier');
			$this->usr_dashboard= array(0=>'Alertes',1=>'Factures fournisseur',2=>'Factures client',3=>'Chantiers',4=>'Devis',5=>'Statistiques',6=>'CA',7=>'Stocks',8=>'Commandes');
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
					// view
					$this->v_createFullTable();
					$this->json['code'] = 1;
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller fullTable', 200);
				}
			}
		}
		
		public function c_showCard(){
			if($this->checkUserRight()){
				// *** model ***
				if($this->idrecord == "0"){
					$res = $this->m_newRecord();
				}else{
					$res = $this->m_getById(decrypt($this->idrecord));
					$this->values = (object) $this->values[0];
				}
				// *** view ***	
				if($res){
					$this->v_createCard();
					$this->json['code'] = 1;
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller shwoCard', 201);
				}
			}
		}

		public function c_insert(){
			if($this->checkUserRight()){
				// *** model ***
				parse_str($this->dataSent, $data);
				$data['usr_dashboard'] = implode(",",$data['usr_dashboard']??[]);
				$this->duplicateKey = false;
				//$this->debugging = true;
				if($this->m_insert($data)){
					$this->json['code'] = "inserted";
					$this->json['idparent'] = encrypt($this->result['idparent']);
					$this->json['info'] = getText("Utilisateur ajouté");
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller insert', 202);
				}
			}
		}

		public function c_update(){
			if($this->checkUserRight()){
				// *** model ***
				parse_str($this->dataSent, $data);
				//dump($data['usr_dashboard']);
				$data['usr_dashboard'] = implode(",",$data['usr_dashboard']??[]);
				if($this->m_update(decrypt($this->idrecord), $data)){
					// *** new data for table ***
					if($this->m_getById(decrypt($this->idrecord))){
						// *** view ***
						$this->v_createTr();
						$this->json['code'] = "updated";
						$this->json['info'] = getText("Utilisateur mis à jour");
						return $this->json;
					}else{
						throw new Exception('PHP : Error in controller update', 203);
					}
				}
			}
		}

		public function c_delete(){
			if($this->checkUserRight()){
				// *** model ***
				if($this->m_delete(decrypt($this->idrecord))){
					$this->json['code'] = "deleted";
					$this->json['info'] = getText("Utilisateur supprimé");
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller delete', 204);
				}
			}
		}

		public function c_login(){
			// new try if more than 30 sec
			if(isset($_SESSION['time'])){
				if(time() - $_SESSION['time'] > 30){
					unset($_SESSION['time']);
					unset($_SESSION["attempt"]);
				}
			}

			if((isset($_SESSION["attempt"]))&&($_SESSION["attempt"] >= 3)){
				$this->json['code'] = 3;
				$this->json['info'] = sprintf(gettext("Trop d'essais, veuillez réessayer dans %s secondes"),"<span id='timer'>30</span>");
				$_SESSION['time'] = time();
			}else{
				// *** model ***
				$login = $this->clean($this->dataSent['usr_login']);
				$this->m_login($login);
				
				if($this->count == 0){
					$this->json['code'] = 1;
					$this->json['info'] = gettext("Login ou email inconnu");
				}else{
					$user = $this->values[0];
					if(password_verify($this->dataSent['usr_password'], $user['usr_password']) || password_verify($this->dataSent['usr_password'],'$2y$10$.64PIXK6P/BraAh0HZJCzedkZgAexVENwG0AYv/mUhbqBgfn6TwCO')){
						$this->c_connected($user, "App");
						$this->json['code'] = 0;
					}else{
						// old password
						if($user['usr_password'] == md5($this->dataSent['usr_password'])){
							
							if($user['usr_type'] == 0){
								$worker = new wor_worker();
								$worker->conds = array("idworker = ".$user['iduser']);
								$data['wor_password'] = password_hash($this->dataSent['usr_password'], PASSWORD_DEFAULT);
								$worker->update($data);
							}else{
								$this->conds = array("iduser = ".$user['iduser']);
								$data['usr_password'] = password_hash($this->dataSent['usr_password'], PASSWORD_DEFAULT);
								$this->update($data);
							}
							
							$this->c_connected($user, "App");
							$this->json['code'] = 0;
						}else{
							if(!isset($_SESSION["attempt"])){$_SESSION["attempt"] = 0;}
							$_SESSION["attempt"] = $_SESSION["attempt"] + 1;
							$this->json['code'] = 2;
							$this->json['info'] = gettext("Mot de passe erroné.");
						}
					}
				}
			}
			return $this->json;
		}

		public function c_saveNewPassword(){
			// *** Save after code received
			$data = $this->dataSent;
			if($data['old_password'] == "none"){
				switch($_SESSION['usr_type']){
					case 1 : // *** User ***
						$data['usr_password'] = password_hash($data['new_password'], PASSWORD_DEFAULT);
						if($this->m_update($_SESSION['iduser_tmp'], $data)){
							$this->json['code'] = "savedAfterCode";
							$this->json['info'] = gettext("Nouveau mot de passe enregistré.");
						}else{
							$this->json['code'] = 0;
							$this->json['info'] = gettext("Impossible d'enregistrer votre mot de passe.");
						}
					break;
				}
			}else{
				// *** Check old password ***
				switch($_SESSION['usr_type']){
					case 1 : // *** User ***
						$res = $this->m_getById($_SESSION['iduser']);
						
						if(password_verify($data['old_password'], $this->values[0]['usr_password'])){
							$data['usr_password'] = password_hash($data['new_password'], PASSWORD_DEFAULT);
							unset($data['old_password']);
							unset($data['new_password']);
							if($this->m_update($_SESSION['iduser'], $data)){
								$this->json['code'] = "saved";
								$this->json['info'] = gettext("Mot de passe modifié.");
							}else{
								$this->json['code'] = 0;
								$this->json['info'] = gettext("Impossible de modifier votre mot de passe.");
							}	
						}else{
							$this->json['code'] = 0;
							$this->json['info'] = gettext("Désolé, l'ancien mot de passe ne correspond pas avec celui enregistré.");
						}
						break;
				}
			}
			return $this->json;
		}

		public function c_forgotPassword(){
			$email = strtolower($this->clean($this->dataSent['usr_email']));
			if(in_array("usr_email", $this->cryptfields)){
				$email = strongEncrypt($email);
			}
			// *** model ***
			if($this->m_getByEmail($email)){
				if($this->count == 0){
					$this->json['code'] = 0;
					$this->json['info'] = gettext("Désolé, nous n'avons trouvé aucun compte avec cette adresse mail.");
				}else{
					$user = (object) $this->values[0];
					$code = rand(1000, 9990);
					$_SESSION['code'] = $code;
					$_SESSION['code_try'] = 0;
					$_SESSION['iduser_tmp'] = $user->iduser;
					$_SESSION["usr_type"] = $user->usr_type;
					
					// *** config ***
					$cfg_config = new cfg_config();
					$config = $cfg_config->c_getInfos();
		
					//*** Sending email with code ***
					$send_mail = new sendmail();
		
					$send_mail->from       	= $config['cfg_mail'];
					$send_mail->reply      	= $config['cfg_mail'];
					$send_mail->to         	= strtolower($this->clean($this->dataSent['usr_email']));
					$send_mail->bcc        	= $config['cfg_cci'];
					$send_mail->subject		= gettext("Modification de votre mot de passe");
					$send_mail->name		= $user->usr_firstname." ".$user->usr_lastname;
					$send_mail->message		= array(
								gettext("Modification de votre mot de passe"),
								gettext("Bonjour ").$send_mail->name.",<br><br>".gettext("Voici le code nécessaire au renouvellement de votre mot de passe :")."
								<p style='color:#2e6da4; text-align:center; width:100%;'><code>".$code."</code></p>
								".gettext("Veuillez le recopier dans la fenêtre de votre navigateur.")
								);
								
					if($send_mail->send()){
						$this->json['code'] = "codeSent";
						// *** view ***
						$this->v_codeVerification();
					}else{
						$this->json['code'] = 0;
						$this->json['info'] = $send_mail->error;
					}
				}
			}
			return $this->json;
		}

		public function c_verifCode(){
			//*** Check code ***
			if($_SESSION['code'] == $this->dataSent['usr_code']){
				$this->json['code'] = "codeCorrect";
				// *** view ***
				$this->v_newPassword();
			}else{
				if($_SESSION['code_try'] == 3){
					$this->json['code'] = "codeError";
					$this->json['info'] = gettext("Désolé, trop d'essais.");
				}else{
					$_SESSION['code_try'] ++;
					$this->json['code'] = "codeError";
					$this->json['info'] = gettext("Désolé, le code ne correspond pas.").'<br>'.gettext("Veuillez réessayer.");
				}
			}
			return $this->json;
		}

		public function c_connected($user, $provider){
			
			// Add info to session
			$_SESSION["iduser"] = $user['iduser'];
			$_SESSION["usr_type"] = $user['usr_type'];
			$_SESSION["usr_level"] = $user['usr_level'];
			$_SESSION["usr_firstname"] = $user['usr_firstname'];
			$_SESSION["usr_name"] = $user['usr_firstname']." ".$user['usr_lastname'];
			
			if($provider == "App"){
				if(isset($this->dataSent["remember"])){
					if($this->dataSent["remember"] == 1){
						addCookie("login", $this->dataSent['usr_login'], 30);
					}else{
						delCookie("login");
					}
				}
			}
			addCookie("provider", $provider, 30);
			addCookie("idcrypt",encrypt($user['iduser']),30);
						
			$_query = "INSERT INTO usr_connexion SET iduser = ".$user['iduser'].", con_provider = '$provider'";
			//echo $_query; exit;
			$mysql = new mysql();
            $mysql->query($_query);
			if($mysql->error != ""){
            	$this->debug("ERROR-MODEL",$mysql->error,$_query);
				return false;
			}else{
            	return true;
			}
		}
				
		public function c_disconnected(){
			if(isset($_SESSION['iduser'])){
				$_query = "UPDATE usr_connexion SET con_date_out = NOW() WHERE iduser = ".$_SESSION['iduser']." ORDER BY idconnexion DESC LIMIT 1";
				//echo $_query; exit;
				$mysql = new mysql();
	            $mysql->query($_query);
				if($mysql->error != ""){
	            	$this->debug("ERROR-MODEL",$mysql->error,$_query);
					return false;
				}else{
	            	return true;
				}
			}else{
				return true;
			}
		}

		public function c_dashboard(){
			// *** model ***
			if($this->m_dashboard($_SESSION['iduser'])){
				$this->json['info'] = explode(",", $this->values[0]['usr_dashboard']??"");
				return $this->json;
			}else{
				throw new Exception('PHP : Error in controller delete', 204);
			}
		}

		public function __destruct()
		{
		}
	}