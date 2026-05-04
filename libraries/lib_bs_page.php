<?php 
    class page{
		public $type;
		public $id;
		public $title;
		public $classname;
		public $body;
		public $button;
		
		private $result;
		private $alertAbs;
		private $alert;

        public function __construct($type, $id, $title, $classname, $body, $button)
        {
        	$this->type 		= $type;
			$this->id  			= $id;
			$this->title 		= $title;
			$this->classname 	= $classname;
			$this->body 		= $body;
			$this->button 		= $button;
			
			$this->result		= '';
			
			$this->alertAbs = '<div id="alert-empty" class="alert alert-danger absolute-center shadow p-4" style="display:none;" role="alert">'.gettext("Certains champs obligatoires ne sont pas remplis").'</div>';
			$this->alert = '<div id="alert-empty" class="alert alert-danger rounded-0" style="display:none;" role="alert">'.gettext("Certains champs obligatoires ne sont pas remplis").'</div>';
		}

		public function create($dates = array())
		{
			switch ($this->type){
				case "simple" :
					$this->result = '
						<div class="normal-body '.$this->classname.'">
							'.$this->body.'
							'.$this->button.'
							'.$this->alertAbs.'
						</div>
					';
					break;
				case "normal" :
					$this->result = '
						<div class="normal-body '.$this->classname.'">
							'.$this->body.'
						</div>
						<div class="normal-footer">
							'.$this->button.'
						</div>
						'.$this->alertAbs.'
					';
					break;
				case "card" :
					$this->result = '
					<div class="card">
					    <div class="card-body">
							<div class="pb-3">'.$this->title.'</div>
					        '.$this->body.'
							<div class="d-flex justify-content-between">
								'.$this->button.'
							</div>	
					    </div>
					</div>
					';
					break;
				case "listgroup" :
					$this->result = '
						<div id="'.$this->id.'" class="list-group-item collapse">
							<div class="normal-body">
								'.$this->body.'
							</div>
							<div class="normal-footer">
								'.$this->button.'
							</div>
							'.$this->alertAbs.'
						</div>
					';
					break;
				case "tr" :
					$this->result = '
					<td colspan="'.$this->classname.'" class="tr-detail">
						<div id="'.$this->id.'" class="td-collapse col-md-12" style="display:none;">
							<div class="normal-body">
								'.$this->body.'
							</div>
							<div class="normal-footer">
								'.$this->button.'
							</div>
							'.$this->alertAbs.'
						</div>
					</td>
					';
					break;
				case "modal" :
					if(!empty($dates)){
						$info = '
							<div class="info">
								'.gettext("Création : ").' '.alterData("date-be",$dates[0]).' | '.gettext("Modification : ").' '.alterData("date-be",$dates[1]).'
							</div>
						';
					}else{
						$info = '';
					}
					$this->result = '
					<div class="modal fade" id="'.$this->id.'" role="dialog" aria-labelledby="modaltitle">
						<div class="modal-dialog '.$this->classname.(contains("noscroll", $this->classname)?'':' modal-dialog-scrollable').'" role="document">
							<div class="modal-content">
								<div class="modal-header">
									<h4 class="modal-title" id="modaltitle">'.$this->title.'</h4>
									<button class="close" type="button" aria-hidden="true" onclick="$(\'#'.$this->id.'\').modal(\'hide\')"><i class="fal fa-times-circle"></i></button>
								</div>
							<div class="modal-body">
								'.$this->body.'
							</div>
							<div class="modal-footer">
								'.$this->button.'
								'.$info.'
							</div>
							'.$this->alertAbs.'
						</div>
					</div>
					';
					break;
			}
			return trim($this->result);
		}
        public function __destruct()
        {
        }
    }