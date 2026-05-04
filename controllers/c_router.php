<?php
/**
*** juin 2019@SoluFile 
**/
	include_once (dirname(__FILE__)."/../libraries/lib_include.php");
	
	class router {
		
		public $query;
		public $controller;
		public $table;
		public $action;
		public $idrecord;
		public $dataSent;
		
		public $ajax;
		
		public function __construct()
		{
			$this->query 		= array_merge($_GET, $_POST);
			if(empty($_POST)){
				$this->ajax		= false;
			}else{
				$this->ajax		= true;
			}
		}
		public function __destruct()
		{
		}
	}
	
	$router = new router();
	
	if($router->ajax){
		header('Content-type: application/json');
	}
	
	try{
		if(empty($router->query)){
			throw new Exception('ERROR : No $_GET or $_POST data', 100);
		}else{
			$router->table		= $router->query['table'];
			$router->action 	= "c_".$router->query['action'];

			$controller 			= new $router->table;
			$controller->idrecord 	= $router->query['idrecord']??0;
			$controller->dataSent 	= $router->query['dataSent']??array();
			$controller->action 	= $router->query['action']??"";
			
			$result = call_user_func(array($controller,$router->action));

			if($router->ajax){
				$result['html'] = str_replace(array(chr(13).chr(10).chr(9), chr(9)), '', $result['html']);
				$result['info'] = str_replace(array(chr(13).chr(10).chr(9), chr(9)), '', $result['info']);
				echo json_encode($result,JSON_INVALID_UTF8_IGNORE | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
			}else{
				echo $result['html'];
			}
		}
	}catch(Exception $e) {
		$from = $e->getTrace()[1];
		if($router->ajax){
			echo json_encode(
				array(
					'status' => false,
					'html' => $_SESSION['debug']?$e->getMessage():"",
					'code' => $e->getCode(),
					'file' => $_SESSION['debug']?$e->getFile():"",
					'line' => $_SESSION['debug']?$e->getLine():"",
					'from' => $_SESSION['debug']?$from['file'].":".$from['line']:"",
					'trace' => $_SESSION['debug']?str_replace('#', '<br>', $e->getTraceAsString()):"",
				)
			);
		}else{
			echo "(".$e->getCode().") ".$e->getMessage().".<br> File : ".$e->getFile().":".$e->getLine();
		}
		exit;
	}