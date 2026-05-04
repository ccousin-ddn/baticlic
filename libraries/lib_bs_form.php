<?php 
    class form{

		private $result;

        public function __construct()
        {
        	$this->result 		= "";
        }

		public function create()
		{
			$vars = func_get_args();
			$type = $vars[0];
			array_shift($vars);
			$id = $vars[0];
			array_shift($vars);
			$class = $vars[0];
			array_shift($vars);
			$value = implode("", $vars);
			
			switch ($type){
				case "normal" :
					$this->result = '<form class="'.$id.' '.$class.'" id="'.$id.'">'.$value.'</form>';
					break;
				case "upload" :
					$this->result = '<form class="'.$id.' '.$class.'" id="'.$id.'" method="post" enctype="multipart/form-data" action="'.dirname($_SERVER['PHP_SELF']).'/../libraries/lib_upload.php">'.$value.'</form>';
					break;
				case "dropzone" :
					$this->result = '<form class="dropzone upload_info '.$class.'" id="'.$id.'" action="libraries/lib_upload.php">'.$value.'</form>';
					break;
				case "submit" :
					$this->result = '<form class="'.$id.' '.$class.'" id="'.$id.'" onsubmit="return false">'.$value.'</form>';
					break;
				case "disabled" :
					$this->result = '<form class="'.$id.' '.$class.'" id="'.$id.'"><fieldset disabled>'.$value.'</fieldset></form>';
					break;
			}
			return $this->result;
		}
        public function __destruct()
        {
        }
    }