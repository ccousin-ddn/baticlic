<?php
	//if(($_SERVER['REQUEST_METHOD'] == "POST")||(count(get_included_files()) > 1)){
		if(session_status() !== 2){
			session_name(SESSION_NAME);
			session_set_cookie_params([
				'lifetime' => SESSION_LIFETIME,
				'path' => COOKIE_PATH,
				'domain' => COOKIE_DOMAIN,
				'secure' => 'true',
				'httponly' => 'true',
				'samesite' => 'Strict'
			]);
			session_cache_expire(SESSION_LIFETIME/60);
			ini_set('session.cookie_httponly', true);
			ini_set('session.cookie_secure', true);
			ini_set('session.use_only_cookies', true);
			ini_set('session.hash_function', 'whirlpool');
			ini_set('session.use_strict_mode', 1);
			//ini_set('session.gc_maxlifetime', $lifetime);
			//ini_set('session.save_path', __DIR__ . '/sessions');
			//ini_set('session.gc_divisor', 10);
			
			ini_set('session.save_path', SITE_ROOT.'sessions');
			/*** OVH
			ini_set('session.save_path', realpath(dirname($_SERVER['DOCUMENT_ROOT'])).'/sessions');
			.ovhconfig
			app.engine.flags=noforcetmp
			***/
			ini_set('session.gc_probability', 0);
			if(!@session_start(['gc_maxlifetime' => SESSION_LIFETIME,])){
				usleep(250000);
				session_start(['gc_maxlifetime' => SESSION_LIFETIME,]);
			}
			
			if(!isset($_SESSION['iduser'])){
				// check cryptID
				if(isset($_COOKIE['idcrypt']) && isset($_COOKIE['login'])){
					$iduser = decrypt($_COOKIE['idcrypt']);
					$usr_user = new usr_user();
					//$usr_user->m_getById($iduser);
					$usr_user->m_login($_COOKIE['login']);
					if($usr_user->count == 1 && $usr_user->values[0]['iduser'] == $iduser){
						$user = $usr_user->values[0];
						// connected
						$usr_user->dataSent["remember"] = 1;
						$usr_user->dataSent['usr_login'] = $_COOKIE['login'];
						$usr_user->c_connected($user, $_COOKIE['provider']);
						define('CONNECTED',true);
						// renew for 30 days
						addCookie("idcrypt",encrypt($iduser),30);
					}else{
						$_SESSION["usr_level"] = 1;
						define('CONNECTED',false);
					}
				}else{
					$_SESSION["usr_level"] = 1;
					define('CONNECTED',false);
				}
			}else{
				define('CONNECTED',true);
			}
		}else{
			define('CONNECTED',false);
		}

		//Limit form execution time to 10 seconds
		set_time_limit(10);
		
		// Remove header informations
		header_remove('x-powered-by');
	//}else{
	//	die;
	//}