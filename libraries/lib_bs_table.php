<?php
    class table{
		private $tablename;
		private $table;
		private $level;
		private $checkbox;
		private $action;
		private $actions;
		private $columnsToShow;
		private $result;
		
		public $info;

        public function __construct($tablename, $level, $checkbox=false, $actions=array())
        {
        	$this->tablename 		= $tablename;
			$this->table			= new $tablename();
			$this->level			= $level;
			$this->checkbox			= $checkbox;
			$this->action			= isset($actions["action"])?true:false;
			
			$this->actions['read']	= isset($actions['read'])?$actions['read']:false;
			$this->actions['edit']	= isset($actions['edit'])?$actions['edit']:false;
			$this->actions['delete']= isset($actions['delete'])?$actions['delete']:false;
			$this->actions['save']	= isset($actions['save'])?$actions['save']:false;
			$this->actions['undo']	= isset($actions['undo'])?$actions['undo']:false;
			$this->actions['change']= isset($actions['change'])?$actions['change']:false;
			$this->actions['add']	= isset($actions['add'])?$actions['add']:false;
			$this->actions['remove']= isset($actions['remove'])?$actions['remove']:false;
			$this->actions['pdflight']= isset($actions['pdflight'])?$actions['pdflight']:false;
			
			$this->columnsToShow 	= array();
        	$this->result 			= '';
			
        }

		public function createHead($picto, $title, $count=0, $newRecord=true)
		{
			$this->result = '
			<div id="table-toolbar" class="d-flex">
				<div class="info d-flex align-items-center">
					'.($picto??'<i class="fal fa-file"></i>').'<span class="count-numbers ml-2">'.$count.'</span><span class="count-name">'.$title.'</span>
				</div>
				'.($newRecord?'<button class="btn btn-add btn-full-height" aria-label="...." onclick="showCard(\''.$this->tablename.'\',\'0\')" title="'.gettext("Nouvel enregistrement").'" type="button"><i class="fal fa-plus mr-2"></i><span class="d-none d-lg-inline">'.$this->table->newtext.'</span></button>':'').'
			</div>
			';
			return $this->result;
		}

		public function createTableHead($columns, $class="thead-dark", $action=false)
		{
			$this->result = '
				<thead class="'.$class.'">
		            <tr>
			';
			if($this->checkbox){
				$this->result .= '<th data-checkbox = "1"></th>';
			}
			
			// unique-id
			$this->result .= '<th data-field="id" data-visible="false">ID</th>';
			
			foreach ($columns as $param)
			{
				// Ajout des colonnes à afficher
				$this->columnsToShow = array_merge($this->columnsToShow,array($param["field"]=>$param["alter"]));
				$this->result .= '<th ';
				foreach ($param as $data=>$value)
				{
					if($data != "alter" && $value != ""){
						$this->result .= 'data-'.$data.' = "'.gettext($value).'" ';
					}
				}
				$this->result .= '></th>';
			}
			if($this->action){
				$this->result .= '<th data-events="tableEvents" data-halign="center" data-align="left" data-width="50" data-field="operations">'.gettext("Opérations").'</th>';
			}
			$this->result .= '
		            </tr>
	            </thead>
			';
			return $this->result;
		}

		public function createTableFoot($colspan, $idparent)
		{
			if($this->action){$colspan++;}
			if($_SESSION['usr_level'] >= 1){
			$this->result = '
				<tfoot>
		            <tr class="table-add-row">
						<td class="text-center" colspan='.$colspan.' onclick="tableAction({tableName:\''.$this->tablename.'\',idrecord:\''.$idparent.'\',action:\'newLine\'})">
							<i class="fal fa-plus"></i>
						</td>
		            </tr>
	            </tfoot>
			';
			}else{
				$this->result = '';
			}
			return $this->result;
		}

		public function createTableTr($id, $data, $conds, $class="")
		{
			$result = '';
			foreach($data as $line)
			{
				$result .= '<tr class="'.$class.'">';
				$result .= $this->createTableTd($id, $line, $conds);
				$result .= '</tr>';	
			}
			return $result;
		}

		public function createTableTd($id, $line, $conds)
		{
			$result = '';

				if($this->checkbox){
					$result .= '<td></td>';
				}
				// unique-id
					$result .= '<td>'.encrypt($line[$id]).'</td>';
				// conditions
				$tdClass="";
				foreach($conds as $cond){
					if($line[$cond["column"]] != ""){
						$condition = "return ("."'".$line[$cond["column"]]."'".$cond["query"].");";
						if(eval($condition)){
							$tdClass = $cond["class"];
							break;
						}
					}
				}
				
				$col = 0;
				foreach($this->columnsToShow as $key=>$alter)
				{
					if($line[$key] != ""){
						// Decrypt if necessary
						if (in_array($key, $this->table->cryptfields)) {
							$line[$key] = strongDecrypt($line[$key]);
						}
						// Alter data
						if($alter != ""){
							if($alter == "array"){
								$line[$key] = $this->table->$key[$line[$key]];
							}elseif($alter == "link"){
								$line[$key] = '<a onclick="showCard(\''.$this->table->linkTable.'\',\''.encrypt($line['idlink']).'\');event.stopPropagation()" href="javascript:void(0)">'.$line[$key].'</a>';
							}else{
								//var_dump($line);
								//var_dump($key);
								$line[$key] = alterData($alter, $line[$key]);
							}
						}
					}
					$result .= '<td data-name="'.$key.'"'.($col == 0?' class="'.$tdClass.'"':'').'>'.$line[$key].'</td>';
					$col++;
				}
				if($this->action){
					$result .= '<td data-name="operations">';
					$result .= $this->createOperations();
					$result .= '</td>';
				}

			return $result;
		}
		
		public function createOperations()
		{
			$buttons = '<div class="btn-group ml-2" role="group" aria-label="">';
			if($this->actions['read']){
				$buttons .= '<button type="button" class="btn btn-sm btn-outline-info line-show" data-toggle="tooltip" data-placement="top" title="'.gettext("Afficher les détails").'"><i class="fal fa-eye fa-fw"></i></button>';
			}
			if($this->actions['edit'] && $_SESSION['usr_level'] >= 1){
				$buttons .= '<button type="button" class="btn btn-sm btn-outline-secondary line-edit" data-toggle="tooltip_no" data-placement="top" title="'.gettext("Editer la ligne").'"><i class="fal fa-pen fa-fw"></i></button>';
			}
			if($this->actions['delete'] && $_SESSION['usr_level'] >= 1){
				$buttons .= '<button type="button" class="btn btn-sm btn-outline-danger line-delete" data-toggle="tooltip_no" data-placement="top" title="'.gettext("Supprimer la ligne").'"><i class="fal fa-trash fa-fw"></i></button>';
			}
			if($this->actions['save']){
				$buttons .= '<button type="button" class="btn btn-sm btn-outline-success line-save" data-toggle="tooltip_no" data-placement="top" title="'.gettext("Enregistrer").'"><i class="fal fa-check fa-fw"></i></button>';
			}
			if($this->actions['undo']){
				$buttons .= '<button type="button" class="btn btn-sm btn-outline-secondary line-undo" data-toggle="tooltip_no" data-placement="top" title="'.gettext("Annuler").'"><i class="fal fa-undo fa-fw"></i></button>';
			}
			if($this->actions['change']){
				$buttons .= '<button type="button" class="btn btn-sm btn-outline-info line-change" data-toggle="tooltip" data-placement="top" title="'.gettext("Changer").'"><i class="fal fa-exchange-alt fa-fw"></i></button>';
			}
			if($this->actions['pdflight']){
				$buttons .= '<button type="button" class="btn btn-sm btn-outline-secondary line-pdflight" data-toggle="tooltip" data-placement="top" title="'.gettext("Créer pdf light").'"><i class="fab fa-creative-commons-nc-eu fa-fw"></i></button>';
			}
			$buttons .= '</div>';
			
			return $buttons;
		}
		
		public function createJsonTr($id, $data, $conds)
		{
			$result = array();
			$line = isset($data[0]) ? $data[0] : $data;
			
			// unique-id
			$result['id']= encrypt($line[$id]);
			//debug($this->columnsToShow, $data);
			foreach($this->columnsToShow as $key=>$alter)
			{
				// Decrypt if necessary
				if($line[$key] != ""){
					if (in_array($key, $this->table->cryptfields)) {
						$line[$key] = strongDecrypt($line[$key]);
					}
				}
				// Alter data
				if($alter != ""){
					if($alter == "array"){
						$line[$key] = $this->table->$key[$line[$key]];
					}else{
						$line[$key] = alterData($alter, $line[$key]);
					}
				}
				$result[$key] = $line[$key];
			}
			
			// conditions
			$tdClass="";
			foreach($conds as $cond){
				if($line[$cond["column"]] != ""){
					$condition = "return (".$line[$cond["column"]].$cond["query"].");";
					if(eval($condition)){
						$tdClass = $cond["class"];
						break;
					}
				}
			}
			$this->info = $tdClass;
			
			if($this->action){
				$result['operations'] = $this->createOperations();
			}
			//debug($result);
			return json_encode($result);
		}

		public function createTableBody($id, $data, $conds)
		{
			$this->result = '
				<tbody>
			';
			$this->result .= $this->createTableTr($id, $data, $conds);
			$this->result .= '
	            </tbody>
			';
			return $this->result;
		}

		public function createTable($head, $param, $tableHead, $tableBody)
		{
			$this->result = $head;
			$this->result .= '
			<div class="table-responsive pt-4" id="table-result">
				<table 
				class = "bootstrap-table"
				id="'.$this->tablename.'_table" 
				data-unique-id = "id" 
				data-toggle = "'.$this->tablename.'_table" 
				data-pagination = "true" 
				data-side-pagination = "client" 
				data-toolbar-align = "none"
				data-page-size = "50" 
				data-page-list = "[50, 100, 200, 500, 1000, 5000]" 
				data-mobile-responsive = "true" 
				data-min-width = "768" 
			';
			
			//new record
			if (array_key_exists("new-record", $param)) {
				$this->result .= 'data-new-record = "'.($_SESSION['usr_level'] >= $this->level ? $param['new-record'] : "false").'" ';
				unset($param['new-record']);
			}
			// export
			if (array_key_exists("export-table", $param)) {
				$this->result .= 'data-export-table = "'.($_SESSION['usr_level'] >= 2 ? $param['export-table'] : "false").'" ';
				unset($param['export-table']);
			}
			foreach ($param as $data=>$value)
			{
				$this->result .= 'data-'.$data.' = "'.$value.'" ';
			}
			
			$this->result .= '>';
			$this->result .= $tableHead;
			$this->result .= $tableBody;
			$this->result .= '
				</table>
			</div>
			';
			return $this->result;
		}
		
		public function createHeadLines($title)
		{
			$this->result = '<div class="bg-light col_dark text-center">'.$title.'</div>';
			return $this->result;
		}
		
		public function createTableLine($param, $tableHead, $tableBody, $tableFoot, $head="")
		{
			$this->result = $head;
			$this->result .= '
				<table 
				class = "bootstrap-table"
				id="'.$this->tablename.'_table" 
				data-unique-id = "id" 
				data-toggle = "'.$this->tablename.'_table" 
				data-table-name = "'.$this->tablename.'" 
				data-mobile-responsive = "true" 
				data-min-width = "768" 
			';
			//new record
			if (array_key_exists("new-recordtable", $param)) {
				$this->result .= 'data-new-recordtable = "'.($_SESSION['usr_level'] >= $this->level ? $param['new-recordtable'] : "false").'" ';
				unset($param['new-recordtable']);
			}
			foreach ($param as $data=>$value)
			{
				$this->result .= 'data-'.$data.' = "'.$value.'"';
			}
			$this->result .= '>';
			$this->result .= $tableHead;
			$this->result .= $tableFoot;
			$this->result .= $tableBody;
			$this->result .= '
				</table>
			';
			return $this->result;
		}
		
        public function __destruct()
        {
        }
    }