<?php
	class input {

		public $data;
		private $cryptedFields;
		
		public $type;
		public $label;
		public $name;
		public $value;
		public $mandatory;
		public $divSize;
		public $inputClass;
		public $info;
		public $breakline = '<div class="w-100"></div>';
		
		private $classMandatory;
		private $classLabel;
		private $tooltip;
		private $result;

		public function __construct($_data, $_cryptedFields=array())
		{
			$this->data 			= (object) $_data;
			$this->cryptedFields 	= $_cryptedFields;
		}

		public function create($type, $label, $name, $mandatory=false, $default="", $divSize="", $inputClass="", $info=array())
		{
			$this->type				= $type;	
			$this->label			= html($label);
			$this->placeholder		= ''; // $this->label;
			$this->name				= $name;
			$this->inputClass		= $inputClass;
			$this->info				= $info;
			$this->divSize			= $divSize;
			$this->mandatory		= $mandatory;
			$this->classMandatory 	= $mandatory == true ? 'mandatory' : '';
			$this->tooltip			= $info["tooltip"] ?? '';
			$this->popover			= $info["popover"] ?? '';
			$this->classLabel 		= '';
			
			// value
			if(property_exists($this->data,$name)){
				// Decrypt if necessary
				if ($this->data->{$name} != ""){
					if (in_array($name, $this->cryptedFields)) {
						$this->data->{$name} = strongDecrypt($this->data->{$name});
					}
					// value of array
					if(isset($this->info['arrayValue'])){
						$this->value = $this->info['arrayValue'][$this->data->{$name}];
					}else{
						if(is_array($this->data->{$name})){
							$this->value = $this->data->{$name};
						}else{
							$this->value = htmlentities($this->data->{$name},ENT_QUOTES,"UTF-8");
						}
					}
				}else{
					$this->value	= $default;
				}
			}else{
				$this->value		= $default;
			}
			return $this->{$type}();
		}
		
		private function createLabel()
		{
			if($this->label != ""){
				$result = '<label class="d-block" for="'.$this->name.'">'.$this->label;
				if($this->tooltip != ""){
					$result .= '<i class="fal fa-question-circle fa-fw ml-2" data-toggle="tooltip" data-placement="top" data-html="true" title="'.$this->tooltip.'"></i>';
				}
				if($this->popover != ""){
					$result .= '<i class="fal fa-question-circle fa-fw ml-2" data-toggle="popover" data-placement="top" data-html="true" title="'.gettext("Aide").'" data-content="'.$this->popover.'"></i>';
				}
				$result .= '</label>';
			}else{
				$result = "";
			}
			return $result;
		}
		
		private function beforeReturn(){
			if($this->divSize != ""){
				if(strpos($this->divSize, "d-none") > 0)
				{
					$this->result = "<div class='".str_replace("d-none", "", $this->divSize)."' style='display:none;'>".$this->result."</div>";
				}else{
					$this->result = "<div class='".$this->divSize."'>".$this->result."</div>";
				}
			}

		}
		
		private function hidden(){
			$this->result = '
					<div class="'.$this->inputClass.'">
						<input type="hidden" id="'.$this->name.'" name="'.$this->name.'" value="'.$this->value.'">
					</div>
			';
			$this->beforeReturn();
			return $this->result;			
		}

		private function disabled(){
			$label = $this->createLabel();
			
			if(!empty($this->info["alter"])){
				$this->value = alterData($this->info["alter"],$this->value);
			}
			
			$this->result = '
				<div class="form-group">
					'.$label.'
					<div class="'.$this->inputClass.'">
						<input type="text" class="form-control" placeholder="'.$this->placeholder.'" id="'.$this->name.'" name="'.$this->name.'" value="'.$this->value.'" disabled>
			';
			if($this->inputClass == "input-group"){
				$this->result .= '
						<span class="input-group-btn">
							<button class="btn btn-default btn-unlock" type="button"><i class="fal fa-unlock-alt" aria-hidden="true"></i></button>
						</span>
				';
			}
			$this->result .= '
					</div>
				</div>
			';
			$this->beforeReturn();
			return $this->result;
		}
		
		private function action(){
			$label = $this->createLabel();
			
			if(!empty($this->info["alter"])){
				$this->value = alterData($this->info["alter"],$this->value);
			}
			$icon = $this->info["icon"] ?? "fal fa-unlock-alt";
			$action = $this->info["action"] ?? "";
			
			$this->result = '
				<div class="form-group">
					'.$label.'
					<div class="'.$this->inputClass.'">
						<input type="text" class="form-control" placeholder="'.$this->placeholder.'" id="'.$this->name.'" name="'.$this->name.'" value="'.$this->value.'" disabled>
			';
			if($this->inputClass == "input-group"){
				$this->result .= '
						<span class="input-group-btn">
							<button class="btn btn-default btn-action" type="button" data-param=\''.$action.'\'><i class="'.$icon.'" aria-hidden="true"></i></button>
						</span>
				';
			}
			$this->result .= '
					</div>
				</div>
			';
			$this->beforeReturn();
			return $this->result;
		}
		
		private function readonly(){
			$label = $this->createLabel();
			$this->result = '
				<div class="form-group">
					'.$label.'
					<div class="'.$this->inputClass.'">
						<input type="text" class="form-control py-1" placeholder="'.$this->placeholder.'" id="'.$this->name.'" name="'.$this->name.'" value="'.$this->value.'" readonly>
			';
			$this->result .= '
					</div>
				</div>
			';
			$this->beforeReturn();
			return $this->result;
		}

		private function text(){
			$label = $this->createLabel();
			
			$max = !empty($this->info["max"])?' maxlength="'.$this->info["max"].'"':'';
			$pattern = !empty($this->info["pattern"])?' required pattern="'.$this->info["pattern"].'"':'';
			$title = !empty($this->info["title"])?' title="'.$this->info["title"].'"':'';
			
			if(array_key_exists('small', $this->info)) {
				$small = '<small class="form-text text-muted">'.$this->info["small"].'</small>';
			}else{
				$small = '';
			}
			$this->result = '
				<div class="form-group '.$this->classMandatory.' '.$this->inputClass.'">
					'.$label.'
					<div class="">
						<input type="text" class="form-control" placeholder="'.$this->placeholder.'" id="'.$this->name.'" name="'.$this->name.'" value="'.$this->value.'"'.$max.$pattern.$title.'>
						'.$small.'
					</div>
				</div>
			';
			$this->beforeReturn();
			return $this->result;
		}
		
		private function number(){
			$label = $this->createLabel();
			
			$max = !empty($this->info["max"])?' max="'.$this->info["max"].'"':'';
			$pattern = !empty($this->info["pattern"])?' required pattern="'.$this->info["pattern"].'"':'';
			$title = !empty($this->info["title"])?' title="'.$this->info["title"].'"':'';
			
			if(array_key_exists('small', $this->info)) {
				$small = '<small class="form-text text-muted">'.$this->info["small"].'</small>';
			}else{
				$small = '';
			}
			$this->result = '
				<div class="form-group '.$this->classMandatory.' '.$this->inputClass.'">
					'.$label.'
					<div class="">
						<input type="number" class="form-control" placeholder="'.$this->placeholder.'" id="'.$this->name.'" name="'.$this->name.'" value="'.$this->value.'"'.$max.$pattern.$title.'>
						'.$small.'
					</div>
				</div>
			';
			$this->beforeReturn();
			return $this->result;
		}

		private function textIn(){
			$label = $this->createLabel();
			if(isset($this->info["alter"])){
				$this->value = alterData($this->info["alter"], $this->value);
			}
			if(array_key_exists('idparent', $this->info)) {
				$idparent = 'data-idparent="'.$this->info["idparent"].'"';
			}else{
				$idparent = '';
			}
			$this->result = '
				<div class="textIn">'.$this->label.'</div>
				<input type="text" class="form-control textInInput input-sm '.$this->inputClass.'" '.$idparent.' placeholder="'.$this->placeholder.'" id="'.$this->name.'" name="'.$this->name.'" value="'.$this->value.'" '.($this->mandatory==true?"":"readonly").'>
			';
			$this->beforeReturn();
			return $this->result;
		}

		private function password(){
			$label = $this->createLabel();
			$this->result = '
				<div class="form-group '.$this->classMandatory.'">
					'.$label.'
					<div class="'.$this->inputClass.' inp_password">
						<input type="password" class="form-control" id="'.$this->name.'" name="'.$this->name.'">
					</div>
				</div>
			';
			$this->beforeReturn();
			return $this->result;
		}

		private function color(){
			$label = $this->createLabel();
			// Container
			if(isset($this->info["container"])){
				$container = $this->info["container"];
			}else{
				$container = "";
			}
			$this->result = '
				<div class="form-group '.$this->classMandatory.'">
					'.$label.'
					<div class="'.$this->inputClass.'">
						<input style="background-color:'.$this->value.';" type="text" class="form-control color-picker" placeholder="'.$this->placeholder.'" id="'.$this->name.'" name="'.$this->name.'" value="'.$this->value.'">
					</div>
				</div>
			';
			$this->beforeReturn();
			return $this->result;
		}
		
		private function url(){
			$label = $this->createLabel();
			$this->result = '
				<div class="form-group '.$this->classMandatory.'">
					'.$label.'
					<div class="input-group '.$this->inputClass.'">
						<div class="input-group-prepend">
							<a class="btn btn-primary" href="'.$this->value.'" target="_blank" id="button-addon1"><i class="fas fa-external-link-alt"></i></a>
						</div>
						<input type="text" class="form-control" placeholder="'.$this->placeholder.'" id="'.$this->name.'" name="'.$this->name.'" value="'.$this->value.'">
					</div>
				</div>
			';
			$this->beforeReturn();
			return $this->result;
		}
		
		private function mail(){
			$label = $this->createLabel();
			$this->result = '
				<div class="form-group '.$this->classMandatory.'">
					'.$label.'
					<div class="'.$this->inputClass.'">
						<input type="text" class="form-control" placeholder="'.$this->placeholder.'" id="'.$this->name.'" name="'.$this->name.'" value="'.$this->value.'">
					</div>
				</div>
			';
			$this->beforeReturn();
			return $this->result;
		}

		private function phone(){
			$label = $this->createLabel();
			$this->result = '
				<div class="form-group '.$this->classMandatory.'">
					'.$label.'
					<div class="'.$this->inputClass.'">
						<input type="text" class="form-control" placeholder="'.$this->placeholder.'" id="'.$this->name.'" name="'.$this->name.'" value="'.$this->value.'">
					</div>
				</div>
			';
			$this->beforeReturn();
			return $this->result;
		}

		private function date(){
			// format
			$format = "DD-MM-YYYY";

			$date = explode("-", $this->value);
			if((count($date) == 3)&&(checkdate($date[1],$date[2],$date[0]))){
				$this->value = $this->value;
			}else{
				$this->value = "";
			}
			if(isset($this->info["format"])){
				$format = str_ireplace(array("D","M","Y"), array("DD","MM","YYYY"), $this->info["format"]);
				//echo "format = ".$format;
				if(strpos($this->info["format"],'Y') !== false){$viewmode = "years";}
				if(strpos($this->info["format"],'m') !== false){$viewmode = "months";}
				if(strpos($this->info["format"],'d') !== false){$viewmode = "days";}
				if($this->value!=""){$this->value = date($this->info["format"],strtotime($this->value));}
			}else{
				$viewmode = "days";
				if($this->value!=""){$this->value = date('d-m-Y',strtotime($this->value));}
			}
			$label = $this->createLabel();
			$horizontal = $this->info["horizontal"]??"right";
			$this->result = '
				<div class="form-group '.$this->classMandatory.'" style="position:relative;">
					'.$label.'
					<div class="'.$this->inputClass.'">
						<input type="text" data-date-format="'.$format.'" data-view-mode="'.$viewmode.'" data-format="L" class="form-control datetimepicker-input" placeholder="'.$this->placeholder.'" id="'.$this->name.'" name="'.$this->name.'" value="'.$this->value.'" data-toggle="datetimepicker" data-target="#'.$this->name.'" data-date-widget-positioning=\''.json_encode(array("horizontal"=>$horizontal,"vertical"=>"bottom")).'\'>
					</div>
				</div>
			';
			$this->beforeReturn();
			return $this->result;
		}

		private function dateReadonly(){
			// format
			$format = "dd-mm-yyyy";

			$date = explode("-", $this->value);
			if((count($date) == 3)&&(checkdate($date[1],$date[2],$date[0]))){
				$this->value = date('d-m-Y',strtotime($this->value));
			}else{
				$this->value = "";
			}
			$label = $this->createLabel();
			
			$this->result = '
				<div class="form-group '.$this->classMandatory.'">
					'.$label.'
					<div class="'.$this->inputClass.'">
						<input type="text" class="form-control" id="'.$this->name.'" name="'.$this->name.'" value="'.$this->value.'" readonly="">
					</div>
				</div>
			';
			$this->beforeReturn();
			return $this->result;
		}

		private function dateTime(){
			$format = "DD-MM-YYYY HH:mm";
			$label = $this->createLabel();
			if($this->value!=""){$this->value = date_format(date_create($this->value),"d-m-Y H:i:s");}
			$this->result = '
				<div class="form-group '.$this->classMandatory.'">
					'.$label.'
					<div class="'.$this->inputClass.'">
						<input type="text" data-date-format="'.$format.'" data-format="LT" class="form-control datetimepicker-input" placeholder="'.$this->placeholder.'" id="'.$this->name.'" name="'.$this->name.'" value="'.$this->value.'" data-toggle="datetimepicker" data-target="#'.$this->name.'">
					</div>
				</div>
			';
			$this->beforeReturn();
			return $this->result;
		}

		private function hour(){
			$hour = explode(":", $this->value);
			if(count($hour) == 3){
				$this->value = date('H:i',strtotime($this->value));
			}else{
				$this->value = "";
			}
			$label = $this->createLabel();
			$this->result = '
				<div class="form-group '.$this->classMandatory.'">
					'.$label.'
					<div class="'.$this->inputClass.'">
						<input type="text" class="form-control clockpicker datetimepicker-input" placeholder="'.$this->placeholder.'" id="'.$this->name.'" name="'.$this->name.'" value="'.$this->value.'" data-toggle="datetimepicker" data-target="#'.$this->name.'">
					</div>
				</div>
			';
			$this->beforeReturn();
			return $this->result;
		}

		private function textarea(){
			if(array_key_exists('row', $this->info)) {
				if($this->info["row"] == 0){
					$row = max(3,ceil(strlen($this->value)/60/2));
				}else{
					$row = $this->info["row"];
				}
			}else{
				$row = 3;
			}
			if(array_key_exists('readonly', $this->info)) {
				$readonly = "readonly";
			}else{
				$readonly = "";
			}
			if(array_key_exists('small', $this->info)) {
				$small = '<small class="form-text text-muted">'.$this->info["small"].'</small>';
			}else{
				$small = '';
			}
			$label = $this->createLabel();
			$this->result = '
				<div class="form-group '.$this->classMandatory.'">
					'.($this->info["other"]??"").'
					'.$label.'
					<div class="'.$this->inputClass.'">
						<textarea class="form-control" rows="'.$row.'" placeholder="'.$this->placeholder.'" id="'.$this->name.'" name="'.$this->name.'" '.$readonly.'>'.$this->value.'</textarea>
						'.$small.'
					</div>
				</div>
			';
			$this->beforeReturn();
			return $this->result;
		}

		private function textareaEdit(){
			if(array_key_exists('row', $this->info)) {
				$row = $this->info["row"];
			}else{
				$row = 3;
			}
			$label = $this->createLabel();
			$this->result = '
				<div class="form-group '.$this->classMandatory.'">
					'.$label.'
					<div class="'.$this->inputClass.'">
						<textarea class="form-control wysiwyg" rows="'.$row.'" placeholder="'.$this->placeholder.'" id="'.$this->name.'" name="'.$this->name.'">'.$this->value.'</textarea>
					</div>
				</div>
			';
			$this->beforeReturn();
			return $this->result;
		}
		
		private function checkbox(){
			$label = $this->createLabel();
			$this->result = '
				<div class="form-group '.$this->classMandatory.'">
					'.$label.'
					<div class="form-control no-line checkbox-inline d-flex justify-content-start flex-wrap'.$this->inputClass.'">
			';
			
			if(isset($this->info["contents"])) // array
			{
				$results = $this->info["contents"];
				foreach($results as $key=>$value) {
					//$checked = $this->value == $key ? 'checked' : '';
		            $ids = explode(",", $this->value);
					$checked = in_array($key, $ids) ? 'checked' : '';
					$this->result .= '
						<div class="custom-control custom-checkbox custom-control-inline mr-4 mb-2">
							<input type="checkbox" class="custom-control-input" name="'.$this->name.'[]" id="'.$this->name.'-'.$key.'" value="'.$key.'" '.$checked.'>
							<label class="custom-control-label" for="'.$this->name.'-'.$key.'">'.$value.'</label>
						</div>
					';
				}
				if(empty($results)){
					$this->result .= $this->info['empty'];
				}
			}
			else // query
			{
				$select = new $this->info["table"];
				// Conditions
				if(isset($select->idparent)){
					$conds = array($select->idparent." IN(".($this->data->{$select->idparent}??0).")");
				}else{
					$conds = array();
				}
				$select->m_getList($this->info["idlist"], $conds);
				foreach($select->values as $result) {
					$ids = explode(",", $this->value);
					$checked = in_array($result['id'], $ids) ? 'checked' : '';
					$this->result .= '
						<div class="custom-control custom-checkbox custom-control-inline mr-4 mb-2">
							<input type="checkbox" class="custom-control-input" name="'.$this->name.'[]" id="'.$this->name.'-'.$result['id'].'" value="'.$result['id'].'" '.$checked.'>
							<label class="custom-control-label" for="'.$this->name.'-'.$result['id'].'">'.$result['val'].'</label>
						</div>
					';
				}	
			}
			
			$this->result .= '
					</div>
				</div>
			';
			$this->beforeReturn();
			return $this->result;
		}

		private function checkboxColumn(){
			$label = $this->createLabel();
			$this->result = '
				<div class="form-group '.$this->classMandatory.'">
					'.$label.'
					<div class="form-control h-100 '.$this->inputClass.'" style="display: grid;grid-gap: 1rem 1rem;grid-template-columns: repeat(auto-fit,minmax(250px,1fr));grid-auto-flow: dense;">
			';
			
			if(isset($this->info["contents"])) // array
			{
				$results = $this->info["contents"];
				foreach($results as $key=>$value) {
					$checked = $this->value == $key ? 'checked' : '';
		            $this->result .= '
						<div class="custom-control custom-checkbox">
							<input type="checkbox" class="custom-control-input" name="'.$this->name.'" id="'.$this->name.'-'.$key.'" value="'.$key.'" '.$checked.'>
							<label class="custom-control-label" for="'.$this->name.'-'.$key.'">'.$value.'</label>
						</div>
					';
				}
				if(empty($results)){
					$this->result .= $this->info['empty'];
				}
			}
			else // query
			{
				$select = new $this->info["table"];
				// Conditions
				if(isset($select->idparent)){
					$conds = array($select->idparent." IN(".($this->data->{$select->idparent}??0).")");
				}else{
					$conds = array();
				}
				$select->m_getList($this->info["idlist"], $conds);
				foreach($select->values as $result) {
					$ids = explode(",", $this->value);
					$checked = in_array($result['id'], $ids) ? 'checked' : '';
					$this->result .= '
						<div class="custom-control custom-checkbox" style="grid-row: span 2;">
							<input type="checkbox" class="custom-control-input" name="'.$this->name.'[]" id="'.$this->name.'-'.$result['id'].'" value="'.$result['id'].'" '.$checked.'>
							<label class="custom-control-label" for="'.$this->name.'-'.$result['id'].'">'.$result['val'].'</label>
						</div>
					';
				}	
			}
			
			$this->result .= '
					</div>
				</div>
			';
			$this->beforeReturn();
			return $this->result;
		}

		private function checkboxGroup(){
			//$id = $this->info[0];
			$label = $this->createLabel();
			$val3 = "";
	        $this->result = '
				<div class="form-group '.$this->classMandatory.'">
					'.$label.'
					<div class="'.$this->inputClass.'">
			';
			$select = new $this->info["table"];
			// Conditions
			if(isset($select->idparent)){
				$conds = array($select->idparent." IN(".($this->data->{$select->idparent}??0).")");
			}else{
				$conds = array();
			}
			$select->m_getList($this->info["idlist"], $conds);
			foreach($select->values as $result) {
	        	$ids = explode(",", $this->value);
				$checked = in_array($result['id'], $ids) ? 'checked' : '';
				if(($val3 != "")&&($val3 != $result['val3'])){
					$this->result .= '
					</div>
					<div class="'.$this->inputClass.'">
					';
				}
	            $this->result .= '
						<div class="col-sm-3">
							<div class="input-group input-group-sm input-group-checkbox">
								<span class="input-group-addon" style="padding:0;">
									<div class="checkbox checkbox-primary">
										<input type="checkbox" name="'.$this->name.'[]" id="'.$this->name.'-'.$result['id'].'" value="'.$result['id'].'" '.$checked.'>
										<label class="checkbox" for="'.$this->name.'-'.$result['id'].'">'.$result['val'].'</label>
									</div>
								</span>
								<input type="text" class="form-control" aria-label="..." name="'.$this->name.'-val['.$result['id'].']" value="'.$result['val'].'">
								<span class="input-group-addon">€</span>
							</div>
						</div>
				';
				$val3 = $result['val3'];
	        }
			$this->result .= '
					</div>
				</div>
			';
			$this->beforeReturn();
			return $this->result;
		}

		private function radio(){
			if(isset($this->info["disabled"])){
				$disabled = " disabled";
			}else{
				$disabled = "";
			}
		
			$label = $this->createLabel();
			$this->result = '
				<div class="form-group '.$this->classMandatory.'">
					'.$label.'
					<div class="form-control no-line '.$this->inputClass.'">
			';
			
			$id = ($this->info['prefix']??"").$this->name;
			
			if(isset($this->info["contents"])) // array
			{
				$results = $this->info["contents"];
				foreach($results as $key=>$value) {
					$checked = $this->value == $key ? 'checked' : '';
		            $this->result .= '
						<div class="custom-control custom-radio custom-control-inline">
							<input type="radio" class="custom-control-input" name="'.$this->name.'" id="'.$id.'-'.$key.'" value="'.$key.'" '.$checked.$disabled.'>
							<label class="custom-control-label" for="'.$id.'-'.$key.'">'.$value.'</label>
						</div>
					';
				}
			}
			else // query
			{
				$select = new $this->info["table"];
				// Conditions
				if(isset($select->idparent)){
					$conds = array($select->idparent."=".($this->data->{$select->idparent}??0));
				}else{
					$conds = array();
				}
				$select->m_getList($this->info["idlist"], $conds);
				foreach($select->values as $result) {
					$checked = $this->value == $result['id'] ? 'checked' : '';
					$this->result .= '
						<div class="custom-control custom-radio custom-control-inline">
							<input type="radio" class="custom-control-input" name="'.$this->name.'" id="'.$id.'-'.$result['id'].'" value="'.$result['id'].'" '.$checked.$disabled.'>
							<label class="custom-control-label" for="'.$id.'-'.$result['id'].'">'.$result['val'].'</label>
						</div>
					';
				}	
			}
			
			$this->result .= '
					</div>
				</div>
			';
			$this->beforeReturn();
			return $this->result;
		}

		private function switches(){
			$label = $this->createLabel();
			$checked = $this->value == 1 ? 'checked' : '';
			$this->result = '
				<div class="form-group '.$this->classMandatory.'">
					'.$label.'
					<div class="form-control">
						<div class="custom-control custom-switch">
							<input type="checkbox" class="custom-control-input" name="'.$this->name.'[]" id="'.$this->name.'" value="1" '.$checked.'>
							<label class="custom-control-label" for="'.$this->name.'">Mode sombre</label>
						</div>
					</div>
				</div>
			';
			$this->beforeReturn();
			return $this->result;
		}

		private function select(){
			$title = $this->info["title"] ?? gettext("Choisissez ...");
			
			// Search in select
			if(isset($this->info["search"])){
				$searchable = ' data-live-search="'.$this->info["search"].'"';
			}else{
				$searchable = '';
			}
			// none-result-text
			if(isset($this->info["none-result-text"])){
				$noneResultText = ' data-none-results-text="'.$this->info["none-result-text"].' <span data-table=\''.$this->info["table"].'\' data-id=\''.$this->name.'\'>{0}</span>"';
			}else{
				$noneResultText = "";
			}
			// Search in DB
			if(isset($this->info["searchDB"])){
				$searchDB = ' data-live-search-db="'.$this->info["searchDB"].'" data-table="'.$this->info["table"].'"';
				$showData = false;
				$title = gettext("Faites une recherche ...");
				$noneResultText .= ' data-min-size="'.gettext("Minimum 3 caractères").'"';
			}else{
				$searchDB = '';
				$showData = true;
			}
			// Container
			if(isset($this->info["container"])){
				$container = $this->info["container"];
			}else{
				$container = "body";
			}
			// SelectMultiple
			if(isset($this->info["multiple"])){
				$multiple = ' '.$this->info["multiple"];
			}else{
				$multiple = "";
			}
			// Add picto to show detail
			if(isset($this->info["detail"])){
				$detail_begin = '
					<div class="input-group group-selectpicker">
						<span class="input-group-btn">
							<button class="btn btn-detail" type="button" onclick="showDetail(\''.$this->info["table"].'\',\''.$this->name.'\')"><i class="fa fa-eye" aria-hidden="true"></i></button>
						</span>
				';
				$detail_end = '
					</div>
				';
			}else{
				$detail_begin = '';
				$detail_end = '';
			}
			// Filter child
			if(isset($this->info["filterChild"])){
				$filterChild = ' data-filter-child="'.$this->info["filterChild"].'"';
			}else{
				$filterChild = '';
			}
			// Filtering
			if(!empty($this->info["filtering"])){
				$filtering = true;
			}else{
				$filtering = false;
			}
			// Disabled
			if(isset($this->info["disabled"])){
				if($this->info["disabled"] === true){
					$disabled = ' readonly';
					$onlySelected = true;
				}else{
					$disabled = ' '.$this->info["disabled"];
					$onlySelected = false;
				}
			}else{
				$disabled = '';
				$onlySelected = false;
			}
			// Header X
			if(isset($this->info["header"])){
				$header = ' data-header="'.$this->info["header"].'"';
			}else{
				$header = '';
			}
			
			$label = $this->createLabel();
			$this->result = '
				<div class="form-group '.$this->classMandatory.' '.$this->inputClass.'">
					'.$label.'
					'.$detail_begin.'
					<select class="form-control selectpicker" 
					id="'.$this->name.'" 
					name="'.$this->name.'" 
					data-size="auto" 
					data-dropup-auto="false" 
					data-show-subtext="true" 
					data-container="'.$container.'" 
					data-title="'.$title.'"
					'.$searchable.'
					'.$searchDB.'
					'.$noneResultText.'
					'.$filterChild.'
					'.$multiple.'
					'.$header.'
					'.$disabled.'
					>
			';
			// Création de la liste
			
			if(isset($this->info["other"])) // other value
			{
				foreach($this->info["other"] as $key=>$value) {
					$selected = $this->value == $key ? ' selected' : '';
					$this->result .= '
						<option value="'.$key.'"'.$selected.'>'.$value.'</option>
					';
				}
			}
			
			if(isset($this->info["contents"])) // array
			{
				$results = $this->info["contents"];
				foreach($results as $key=>$value) {
					$selected = $this->value == $key ? ' selected' : '';
					$this->result .= '
						<option value="'.$key.'"'.$selected.''.$disabled.'>'.$value.'</option>
					';
				}
			}
			else if($showData) // query
			{
				$select = new $this->info["table"];
				// Conditions
				if(isset($select->idparent)){
					if($filtering){
						$conds = array($this->info["table"].".".$select->idparent." IN (".($this->data->{$select->idparent}??0).")");
					}else{
						if(isset($this->info["conds"])){
							$conds = $this->info["conds"];
						}else{
							$conds = array();
						}
					}
				}else{
					if(isset($this->info["conds"])){
						$conds = $this->info["conds"];
					}else{
						$conds = array();
					}
				}
				
				$select->m_getList($this->info["idlist"], $conds);
				$lev1 = "";
				$lev2 = "";
				$lev3 = "";
				foreach($select->values as $result) {
					// selected
					$selected = $this->value == $result['id'] ? ' selected' : '';
					
					// data
					$data = array();
					if(isset($this->info["data"])){
						foreach($this->info["data"] as $data_name){
							$data[] = "data-".$data_name."='".$result[$data_name]."'";
						}	
					}
					$data = implode(" ",$data);
					
					// level
					if(isset($this->info["level"])){
						$levels = $this->info["level"];
						if($lev1 != $result['level1']){
							$this->result .= '
							<option disabled class="font-italic font-weight-bold color1 pl-2">'.$result['level1'].'</option>
							'.($result['level2']!=""?'<option disabled class="font-weight-bold color2 pl-4">'.$result['level2'].'</option>':'').'
							<option class="pl-5" '.$data.' value="'.$result['id'].'"'.$selected.'>'.$result['level3'].'</option>
							';
							$lev1 = $result['level1'];
							$lev2 = $result['level2'];
						}else{
							if($lev2 != $result['level2']){
							   $this->result .= '	
								'.($result['level2']!=""?'<option disabled class="font-weight-bold color2 pl-4">'.$result['level2'].'</option>':'').'
								<option class="pl-5" '.$data.' value="'.$result['id'].'"'.$selected.'>'.$result['level3'].'</option>
								';
								$lev2 = $result['level2'];
							}else{
								$this->result .= '
								<option class="pl-5" '.$data.' value="'.$result['id'].'"'.$selected.'>'.$result['level3'].'</option>
								';
							}
						}
					}else{
						if($onlySelected){
							if(!empty($selected)){
								$this->result .= '
								<option '.$data.' value="'.$result['id'].'" '.$selected.' '.$disabled.'>'.$result['val'].'</option>
								';
							}
						}else{
							$this->result .= '
							<option '.$data.' value="'.$result['id'].'" '.$selected.' '.$disabled.'>'.$result['val'].'</option>
							';	
						}
					}
				}	
			}else{ // search in DB
				if(!empty($this->value)){
					$select = new $this->info["table"];
					$conds = array($select->key." = ".$this->value);
					$select->m_getList($this->info["idlist"], $conds);
					
					//$text = $this->data->{$this->info['keyValue']};
					$text = $select->values[0]['val'];
					if(!empty($text)){
						$this->result .= '
							<option value="'.$this->value.'" selected>'.$text.'</option>
						';
					}
				}
			}
			
			$this->result .= '
					</select>
					'.$detail_end.'
			';
			
			if(isset($this->info['addTo'])){
				$this->result .= '
						<span class="input-group-btn">
							<button class="btn btn-default btn-unlock" type="button"><i class="fal fa-unlock-alt" aria-hidden="true"></i></button>
						</span>
				';
			}
			
			$this->result .= '
				</div>
			';
			$this->beforeReturn();
			return $this->result;
		}
		
		private function selectForFilter(){
			// Search in select
			if(isset($this->info["search"])){
				$searchable = 'data-live-search="'.$this->info["search"].'"';
			}else{
				$searchable = '';
			}
			$this->result = '
					<select 
						class="selectpicker input-group-btn btn-group" 
						data-style="btn-full-height btn-min-width" 
						id="'.$this->name.'" 
				   		name="'.$this->name.'" 
						data-container="body" 
						data-title="'.$this->label.'" 
						data-dropdown-align-right="true" 
						data-dropup-auto="false" 
						data-header="'.gettext("Fermer").'" 
						'.$searchable.' 
						multiple 
						data-selected-text-format="count > 4"
					>
			';
			// Création de la liste
			foreach($this->data as $result) {
				$selected = in_array($result['id'], $this->value) ? 'selected' : '';
				$this->result .= '
							<option value="'.$result['id'].'"'.$selected.'>'.$result['val'].'</option>
				';
			}
			
			$this->result .= '
					</select>
			';
			$this->beforeReturn();
			return $this->result;
		}

		private function upload(){
			$label = $this->createLabel();
			$this->result = '
				<div class="form-group '.$this->classMandatory.'">
					'.$label.'
					<div class="'.$this->inputClass.'">
						<input class="form-control" type="hidden" id="'.$this->name.'" name="'.$this->name.'" value="'.$this->value.'">
			';
			switch($this->info['type']){
				case "img" :$preview = ($this->value != '' && file_exists('../'.$this->info['path'].$this->value)) ? $this->info['path'].$this->value : 'images/empty.png';
							$this->result .= '
						<img class="upload_preview" src="'.$preview.'">
						<div class="form_upload" data-type="'.$this->info['type'].'" data-size="'.$this->info['maxSize'].'" data-path="'.$this->info['path'].'" data-resize="'.$this->info['resize'].'">
							';
							break;
				case "doc" ://$preview = ($this->value != '' && file_exists('../'.$this->info['path'].'/'.$this->value)) ? '<a href="'.$this->info['path'].$this->value.'" target="_blank"><i class="fa fa-file-pdf-o fa-5x" aria-hidden="true"></a></i>' : '<span class="fa-stack fa-lg fa-5x"><i class="fa fa-file-pdf-o fa-stack-1x"></i><i class="fa fa-ban fa-stack-2x text-danger"></i></span>';
							$preview = ($this->value != '' && file_exists('../'.$this->info['path'].'/'.$this->value)) ? '<a href="'.$this->info['path'].$this->value.'" target="_blank"><embed src="PDFfiles/'.$this->value.'" width="200" height="400" alt="pdf" pluginspage="http://www.adobe.com/products/acrobat/readstep2.html">' : '<span class="fa-stack fa-lg fa-5x"><i class="fa fa-file-pdf-o fa-stack-1x"></i><i class="fa fa-ban fa-stack-2x text-danger"></i></span>';
							$this->result .= $preview.'
						<div class="form_upload" data-type="'.$this->info['type'].'" data-size="'.$this->info['maxSize'].'" data-path="'.$this->info['path'].'">
							';
							break;
			}
						
			$this->result .= '
						
							<div class="upload_choose mt-2">
								<button type="button" onclick="upload_img(\''.$this->name.'\')" class="btn btn-secondary">'.gettext("Choisissez un fichier").'</button>
								<input id="upload_file_img" type="file" name="upload_file_img">
							</div>
							<div class="upload_progress" style="display:none;">
								<div class="upload_bar btn-info"></div>
								<div class="upload_percent"></div>
							</div>
					    </div>
					</div>
				</div>
			';
			$this->beforeReturn();
			return $this->result;
		}
		
		private function uploadDrop(){
			$label = $this->createLabel();
			
			if($_SESSION['usr_level'] > 0){
				$trash = '
						<div class="thumb-del" onclick="deleteImage(\''.$this->name.'\', \''.$this->info['table'].'\', {resize:\''.$this->info['resize'].'\',image:\''.$this->value.'\'})">
							<i class="fal fa-trash fa-2x"></i>
						</div>
				';
			}else{
				$trash = '';
			}
			
			if($this->info['type'] == "doc"){
				$exist = file_exists('../'.UPLOAD_PATH.$this->value);
				$exist = !empty($this->value);
				$this->result = '
					<div class="form-group '.$this->classMandatory.'">
						'.$label.'
						<div class="dropzone upload_info dz-input'.$this->inputClass.'">
							<input class="dz-newName" type="hidden" id="'.$this->name.'" name="'.$this->name.'" value="'.$this->value.'">
							
							<div class="dz-message'.($exist?" d-none":"").'" id="dz-message" data-max-size="'.$this->info['maxSize'].'" data-type="'.$this->info['type'].'" data-resize="'.$this->info['resize'].'">
								<i class="fal fa-file-plus fa-4x"></i>
							</div>
							<div class="doc-thumbnail'.($exist?" d-flex flex-column justify-content-center align-items-center":" d-none").'" id="dz-image">
									<i class="fal fa-file-'.$this->info['file'].' fa-6x mt-3"></i>
									'.$this->info['realName'].'
								'.$trash.'
							</div>
						</div>
					</div>
				';
			}else{
				$resizes = explode(",",$this->info['resize']);
				$resize = explode(":",$resizes[0]);
				$smallRep = $resize[1]??PREVIEW_PATH;
				$resize = explode(":",end($resizes));
				$largeRep = $resize[1]??PREVIEW_PATH;
				//var_dump('../'.UPLOAD_PATH.$largeRep.$this->value);exit;
				$imgLarge = ($this->value != '' && file_exists('../'.UPLOAD_PATH.$largeRep.$this->value)) ? SITE_DIRECTORY.DIRECTORY_SEPARATOR.UPLOAD_PATH.$largeRep.$this->value.'?'.date("ymdhmi") : '';
				$imgSmall = ($this->value != '' && file_exists('../'.UPLOAD_PATH.$smallRep.$this->value)) ? SITE_DIRECTORY.DIRECTORY_SEPARATOR.UPLOAD_PATH.$smallRep.$this->value.'?'.date("ymdhmi") : '';
				
				$isImage = empty($imgSmall)?false:true;
				
				
				switch($this->info['type']){
					case 'img' :	$picto = "fa-images";
									break;
					case 'pdf' :	$picto = "fa-file-pdf";
									break;
					case 'img&pdf' :$picto = "fa-file-pdf";
									break;
					case 'img&mov' :$picto = "fa-photo-video";
									break;
					case 'all' :	$picto = "fa-file";
									break;
					default :		$picto = "fa-images";
									break;
			    }
				$this->result = '
					<div class="form-group '.$this->classMandatory.'">
						'.$label.'
						<div class="dropzone upload_info dz-input'.$this->inputClass.'">
							<input class="dz-newName" type="hidden" id="'.$this->name.'" name="'.$this->name.'" value="'.$this->value.'">
							
							<div class="dz-message '.($isImage?"d-none":"").'" id="dz-message" data-max-size="'.$this->info['maxSize'].'" data-type="'.$this->info['type'].'" data-resize="'.$this->info['resize'].'">
								<i class="fal '.$picto.' fa-4x"></i>
							</div>
							<div class="doc-thumbnail '.($isImage?"":"d-none").'" id="dz-image">
								<div class="zoomable" href="'.$imgLarge.'">
									<img src="'.$imgSmall.'" class="img-thumbnail">
									<button class="btn btn-light btn-close-zoom d-none" type="button"><i class="fal fa-times-circle mr-2"></i>Fermer</button>
								</div>
								'.$trash.'
							</div>
						</div>
					</div>
				';
			}
			$this->beforeReturn();
			return $this->result;
		}

		private function signature(){
			$label = $this->createLabel();
			
			$this->result = '
				<div class="form-group '.$this->classMandatory.'">
					'.$label.'
					<input type="hidden" id="'.$this->name.'" name="'.$this->name.'" value="'.$this->value.'">
			';
			if($this->value == ""){
				$this->result .= '
					<div id="signature-pad" class="signature-pad">
						<div class="signature-pad--body">
							<canvas></canvas>
						</div>
					</div>
				';
			}else{
				$preview = file_exists('../'.$this->info['path'].$this->value) ? $this->info['path'].$this->value : 'images/empty.png';
				$this->result .= '
					<div class="signature">
						<img src="'.$preview.'">
						<div class="signature-del" onclick="'.$this->info["table"].'_action(\''.$this->info["idparent"].'\', \'deleteSignature\')"><i class="fal fa-trash-alt fa-2x"></i></div>
					</div>
				';
			}		
			$this->result .= '
				</div>
			';
			$this->beforeReturn();
			return $this->result;
		}
		
		private function option(){
			foreach($this->data as $result) {
				$this->result .= '
					<option value="'.$result['id'].'" '.($result['tokens']!=""?'data-subtext="'.$result['tokens'].'"':"").'>'.$result['val'].'</option>
				';
			}
			return $this->result;
		}
		
		private function content(){
			$label = $this->createLabel();
			$this->result = '
				<div class="form-group '.$this->classMandatory.'">
					'.$label.'
					'.$this->info["content"].'
				</div>
			';
			$this->beforeReturn();
			return $this->result;
		}
		
		public function __destruct()
		{
		}
	}