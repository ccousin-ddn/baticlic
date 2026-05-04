<?php 
    class button{
		
		public $tableName;
		public $idKey;
		
		private $table;
		private $level;
		private $result;
		private $action;

        public function __construct($tableName, $idKey, $action="showCard")
        {
        	$this->tableName	= $tableName;
			$this->table		= new $this->tableName();
			$this->level		= $this->table->level;
			$this->idKey 		= $idKey;
        	$this->result 		= "";
			$this->action		= $action;
        }

		public function create($type, $class="", $action="", $dataSent="")
		{
			switch ($type){
				case "save" :
					decrypt($this->idKey) > 0 ? $iu = "update" : $iu = "insert";
					$this->action == "newFrom" ? $iu = "insertFrom" : $iu = $iu;
					if($_SESSION['usr_level'] >= 1){
						if(($this->action == "editCardTable")||($this->action == "newCardTable")){
							$onclick = "tableAction({tableName:'".$this->tableName."',idrecord:'".$this->idKey."',action:'saveCardTable'})";
						}else{
							$onclick = "iud('".$this->tableName."','".$this->idKey."','".$iu."','showCard')";
						}
						$this->result = '
						<button type="button" class="mr-4 btn btn-save '.$class.'" onclick="'.$onclick.'" autocomplete="off" data-loading-text="<i class=\'fal fa-spinner fa-pulse fa-fw mr-2\'></i>'.gettext("En cours...").'">
							<i class="fal fa-check mr-md-2"></i>
							<span class="d-none d-md-inline">'.gettext("Enregistrer").'</span>
						</button>
						';
					}else{
						$this->result = '
						<button type="button" class="mr-4 btn btn-save '.$class.'" disabled>
							<i class="fal fa-check mr-md-2"></i>
							<span class="d-none d-md-inline">'.gettext("Enregistrer").'</span>
						</button>
						';
					}
					break;
				case "saveLine" :
					if($_SESSION['usr_level'] >= $this->level){
						$onclick = "listgroupAction('".$this->tableName."','".$this->idKey."','','saveCardLine')";
						$this->result = '
						<button type="button" class="btn btn-save '.$class.'" onclick="'.$onclick.'" autocomplete="off" data-loading-text="<i class=\'fal fa-spinner fa-pulse fa-fw mr-2\'></i>'.gettext("En cours...").'">
							<i class="fal fa-check mr-md-2"></i>
							'.gettext("Enregistrer").'
						</button>
						';
					}else{
						$this->result = '';
					}
					break;
				case "close" :
					$onclick = "$('#".$this->tableName."_modal').modal('hide')";
					$this->result = '
					<button type="button" class="btn btn-outline-secondary '.$class.'" onclick="'.$onclick.'">
						<span class="d-none d-md-inline">'.gettext("Sortir").'</span>
						<i class="fal fa-angle-right ml-md-2"></i>
					</button>
					';
					break;
				case "closeLine" :
					$onclick = "$(jq('".$this->idKey."','#')).collapse('hide')";
					$this->result = '
					<button type="button" class="btn btn-outline-secondary '.$class.'" onclick="'.$onclick.'">
						<i class="fal fa-chevron-up mr-md-2"></i>
						'.gettext("Fermer").'
					</button>
					';
					break;
				case "cancel" :
					$onclick = "tableAction({tableName:'".$this->tableName."',idrecord:'".$this->idKey."',action:'cancelCardTable'})";
					$this->result = '
					<button type="button" class="btn btn-outline-secondary '.$class.'" onclick="'.$onclick.'">
						<i class="fal fa-chevron-up mr-md-2"></i>
						<span class="d-none d-md-inline">'.gettext("Fermer").'</span>
					</a>
					';
					break;
				case "delete" :
					$onclick = "open_popup('popup_ask_delete.php', {idrecord:'".$this->idKey."', table:'".$this->tableName."'}, 'popup-delete')";
					if(((decrypt($this->idKey) > 0)||($this->idKey > 0))&&($_SESSION['usr_level'] > 1)){
						$this->result = '
						<button type="button" style="order:-1;margin-right:auto;" class="btn btn-outline-danger '.$class.'" onclick="'.$onclick.'">
							<i class="fal fa-trash mr-md-2"></i>
							<span class="d-none d-md-inline">'.gettext("Supprimer").'</span>
						</button>
						';
					}else{
						$this->result = '
						<button type="button" style="order:-1;margin-right:auto;" class="btn btn-outline-danger '.$class.'" disabled>
							<i class="fal fa-trash mr-md-2"></i>
							<span class="d-none d-md-inline">'.gettext("Supprimer").'</span>
						</button>
						';
					}
					break;
				case "deleteAllLevel" :
					$onclick = "open_popup('popup_ask_delete.php', {idrecord:'".$this->idKey."', table:'".$this->tableName."'}, 'popup-delete')";
					if(decrypt($this->idKey) > 0 || $this->idKey > 0){
						$this->result = '
						<button type="button" style="order:-1;margin-right:auto;" class="btn btn-outline-danger '.$class.'" onclick="'.$onclick.'">
							<i class="fal fa-trash mr-md-2"></i>
							<span class="d-none d-md-inline">'.gettext("Supprimer").'</span>
						</button>
						';
					}else{
						$this->result = '
						<button type="button" style="order:-1;margin-right:auto;" class="btn btn-outline-danger '.$class.'" disabled>
							<i class="fal fa-trash mr-md-2"></i>
							<span class="d-none d-md-inline">'.gettext("Supprimer").'</span>
						</button>
						';
					}
					break;
				case "deleteLine" :
					if($_SESSION['usr_level'] >= $this->level){
						$onclick = "listgroupAction('".$this->tableName."','".$this->idKey."','','deleteCardLine')";
						$this->result = '
						<button type="button" style="order:-1;margin-right:auto;" class="btn btn-outline-danger '.$class.'" onclick="'.$onclick.'">
							<i class="fal fa-times mr-lg-2"></i>
							'.gettext("Supprimer").'
						</button>
						';
					}else{
						$this->result = '';
					}
					break;
				case "pdf" :
					$onclick = "iud('".$this->tableName."','".$this->idKey."','update','none', function(){cardAction('".$this->tableName."','".$this->idKey."','pdf','".$this->tableName."_pdf')})";
					$this->result = '
					<button type="button" id="btn_'.$this->tableName.'_pdf" class="btn btn-outline-client '.$class.'" onclick="'.$onclick.'" autocomplete="off" data-loading-text="<i class=\'fal fa-spinner fa-pulse mr-2\'></i>'.gettext("En cours...").'">
						<i class="fal fa-file-pdf mr-md-2"></i>
						<span class="d-none d-md-inline">'.(!empty($dataSent)?$dataSent:gettext("Imprimer")).'</span>
					</button>
					';
					break;
				case "item-pdf" :
					$onclick = "iud('".$this->tableName."','".$this->idKey."','update','none', function(){cardAction('".$this->tableName."','".$this->idKey."','pdf','".$this->tableName."_pdf')})";
					$this->result = '
					<a class="dropdown-item" id="btn_'.$this->tableName.'_pdf" onclick="'.$onclick.'" autocomplete="off" data-loading-text="<i class=\'fas fa-circle-notch fa-spin mr-2\'></i>'.gettext("En cours...").'">
						<i class="fal fa-file-pdf fa-fw mr-2"></i>
						'.(!empty($dataSent)?$dataSent:gettext("Imprimer (PDF)")).'
					</a>
					';
					break;
				case "mail" :
					$onclick = "iud('".$this->tableName."','".$this->idKey."','update','none', function(){cardAction('".$this->tableName."','".$this->idKey."','mail','".$this->tableName."_mail')})";
					$this->result = '
					<button type="button" id="btn_'.$this->tableName.'_mail" class="btn btn-outline-client '.$class.'" onclick="'.$onclick.'" autocomplete="off" data-loading-text="<i class=\'fal fa-spinner fa-pulse mr-2\'></i>'.gettext("Préparation du mail...").'">
						<i class="fal fa-envelope mr-md-2"></i>
						<span class="d-none d-md-inline">'.gettext("Envoyer par mail").'</span>
					</button>
					';
					break;
				case "item-mail" :
					$onclick = "iud('".$this->tableName."','".$this->idKey."','update','none', function(){cardAction('".$this->tableName."','".$this->idKey."','mail','".$this->tableName."_mail')})";
					$this->result = '
					<a class="dropdown-item" id="btn_'.$this->tableName.'_mail" onclick="'.$onclick.'" autocomplete="off" data-loading-text="<i class=\'fas fa-circle-notch fa-spin mr-2\'></i>'.gettext("En cours...").'">
						<i class="fal fa-envelope fa-fw mr-2"></i>
						'.gettext("Envoyer par mail").'
					</a>
					';
					break;
				case "card" :
					$onclick = "showCardFrom('".$this->tableName."', '".$this->idKey."', '".$this->action."')";
					$this->result = '
					<button type="button" class="btn btn-outline-secondary '.$class.'" onclick="'.$onclick.'" autocomplete="off" data-loading-text="<i class=\'fal fa-spinner fa-pulse fa-fw mr-2\'></i>'.gettext("En cours...").'">
						<i class="fal fa-eye mr-md-2"></i>
						'.gettext("Voir fiche").'
					</button>
					';
					break;
			}
			return $this->result;
		}
        public function __destruct()
        {
        }
    }