<?php
/**
*** Juillet 2019@SoluFile 
**/
	require_once (dirname(__FILE__)."/../models/m_quo_line.php");
	
	class quo_line_view extends quo_line_model {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->columns = array(
				array("field"=>"art_name", "alter"=>"", "title"=>html("Description"), "sortable"=>false, "searchable"=>false, "class"=>"text-break td-max", "halign"=>"left", "align"=>"left"),
				array("field"=>"met_name", "alter"=>"", "title"=>html("Metier"), "sortable"=>false, "searchable"=>false, "width"=>"150","class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				array("field"=>"lin_unit", "alter"=>"", "title"=>html("U"), "sortable"=>false, "searchable"=>false, "width"=>"80", "class"=>"text-nowrap", "halign"=>"right", "align"=>"right"),
				array("field"=>"lin_quantity", "alter"=>"", "title"=>html("Qté"), "sortable"=>false, "searchable"=>false, "width"=>"80", "class"=>"text-nowrap", "halign"=>"right", "align"=>"right"),
				array("field"=>"lin_pu", "alter"=>"", "title"=>html("PU"), "sortable"=>false, "searchable"=>false, "width"=>"100", "class"=>"text-nowrap", "halign"=>"right", "align"=>"right"),
				array("field"=>"lin_total", "alter"=>"", "title"=>html("Total"), "sortable"=>false, "searchable"=>false, "width"=>"100", "class"=>"text-nowrap", "halign"=>"right", "align"=>"right"),
				array("field"=>"vat_percent", "alter"=>"", "title"=>html("% TVA"), "sortable"=>false, "searchable"=>false, "width"=>"70", "class"=>"text-nowrap", "halign"=>"right", "align"=>"right"),
				array("field"=>"lin_step_situation", "alter"=>"", "title"=>html("Situation %"), "sortable"=>false, "searchable"=>false, "width"=>"70", "class"=>"text-nowrap", "halign"=>"right", "align"=>"right"),
				array("field"=>"lin_step_total", "alter"=>"", "title"=>html("Cumulé %"), "sortable"=>false, "searchable"=>false, "width"=>"70", "class"=>"text-nowrap", "halign"=>"right", "align"=>"right"),
				
			);
			$this->columns_pdf = array(
				array("field"=>"art_name", "alter"=>"", "title"=>html("Désignation"), "style"=>"", "class"=>"text-left", "colspan"=>2),
				array("field"=>"lin_unit", "alter"=>"", "title"=>html("U"), "style"=>"width:50px;", "class"=>"text-right text-nowrap"),
				array("field"=>"lin_quantity", "alter"=>"", "title"=>html("Qté"), "style"=>"width:50px;", "class"=>"text-right text-nowrap"),
				array("field"=>"lin_pu", "alter"=>"euroNull", "title"=>html("Prix/u"), "style"=>"width:100px;", "class"=>"text-right text-nowrap"),
				array("field"=>"vat_percent", "alter"=>"percent", "title"=>html("% TVA"), "style"=>"width:50px;", "class"=>"text-right text-nowrap text-sm"),
				array("field"=>"lin_total", "alter"=>"euroNull", "title"=>html("Total"), "style"=>"width:100px;", "class"=>"text-right text-nowrap"),
			);
			$this->color_conds = array();
		}
		
		public function v_createFullTable()
		{
			// *** Table object ***
			// info : new table(inv_line,level[,checkbox(true/false)][,action(true/false)])
			$table = new table($this->table, $this->level, false);
			// *** create header ***
			$head = $table->createHead($this->picto, ngettext($this->label, $this->labels, $this->count), $this->count);
			// *** create columns title ***
			$thead = $table->createTableHead($this->columns, "thead-card");
			// *** create table lines ***
			$tbody = $table->createTableBody($this->key, $this->values, $this->color_conds);
			// *** create table ***
			$param = array("height"=>"auto", "show-columns"=>"true", "pagination"=>"false", "search"=>true, "classes"=>"table table-no-bordered  table-hover table-light", "striped"=>"false", "new-record"=>"true", "export-table"=>"true", "filter-table"=>"");
			$this->json['html'] = $table->createTable($head, $param, $thead, $tbody);
		}
		
		public function v_createTr($actions)
		{
			// *** Table object ***
			// info : new table(inv_line,level[,checkbox(true/false)][,action(true/false)])
			$table = new table($this->table, $this->level, false, $actions);
			// *** create columns title ***
			$table->createTableHead($this->columns);
			$this->json['html'] = $table->createJsonTr($this->key, $this->values, $this->color_conds);
		}
		
		public function v_createLineTable($idparent)
		{
			// *** Table object ***
			// info : new table(inv_line,level[,checkbox(true/false)][,action(true/false)])
			$table = new table($this->table, $this->level, false, array("action"=>true,"edit"=>true,"delete"=>true));
			// *** create columns title ***
			$thead = $table->createTableHead($this->columns,"thead-card");
			// *** create table lines ***
			$tbody = $table->createTableBody($this->key, $this->values, $this->color_conds);
			// *** create add line ***
			$tfoot = $table->createTableFoot(count($this->columns), $idparent);
			
			// *** create table ***
			$param = array("height"=>"auto", "show-columns"=>"false", "pagination"=>"false", "search"=>"false", "new-record"=>"false", "id-parent"=>"$idparent", "classes"=>"table table-bordered table-sm dragdrop", "striped"=>"false", "export-table"=>"false", "filter-table"=>"");
			$this->json['html'] = $table->createTableLine($param, $thead, $tbody, $tfoot);
		}
		
		public function v_editLine()
		{
			$input = new input($this->values, $this->cryptfields);
			define('IDCLIENT', $this->dataSent['idclient']);
			define('idlibrary', $this->dataSent['idlibrary']);
			define('IDJOB', $this->dataSent['idjob']);
			//debug($this->values);
			//echo $this->values->lin_description."=>select:".$classSelect." input:".$classInput;exit;
			
			if(!empty($this->values->lin_description)){
				$classSelect = "d-none form-normal";
				$classInput = "form-normal";
				$this->values->art_name = "";
			}else{
				$classSelect = "form-normal";
				$classInput = "d-none form-normal";
			}
			if($this->dataSent['idlibrary'] > 0){
				// Library
				$this->values->art_name = $input->create("select", "", "idwork", false, "", "", $classSelect, array("idlist"=>"2","data"=>array("wor_rate","wor_unit","subtext"),"level"=>3,"table"=>"lib_work","search"=>"true","filterChild"=>null,"none-result-text"=>"Créer un article"));
			}elseif($this->dataSent['idjob'] > 0){
				// BPU
				$this->values->art_name = $input->create("select", "", "idwork", false, "", "", $classSelect, array("idlist"=>"3","data"=>array("wor_rate","wor_unit","subtext"),"level"=>3,"table"=>"lib_work","search"=>"true","filterChild"=>null,"none-result-text"=>"Créer un article"));
			}
			$this->values->art_name .= $input->create("text", "", "lin_description", false, "", "", $classInput);
			
			//$this->values->art_name = ($this->values->idwork > 1 ? $input->create("select", "", "idwork", false, "", "", $classSelect, array("idlist"=>"2","data"=>array("wor_rate","wor_unit","subtext"),"level"=>3,"table"=>"lib_work","search"=>"true","filterChild"=>null,"none-result-text"=>"Créer un article")):"").$input->create("text", "", "lin_description", false, "", "", $classInput);
			$this->values->met_name = $input->create("select", "", "idmetier", false, 1, "", "text-right form-normal", array("idlist"=>"1","table"=>"met_metier","search"=>"false","filterChild"=>null,"filtering"=>false));
			$this->values->lin_unit = $input->create("text", "", "lin_unit", false, "", "", "text-right form-normal");
			$this->values->lin_quantity = $input->create("text", "", "lin_qty_formula", false, "", "", "text-left form-normal");
			$this->values->lin_pu = $input->create("text", "", "lin_pu_formula", false, "", "", "text-left form-normal");
			$this->values->vat_percent = $input->create("select", "", "idvat", false, 1, "", "text-right form-normal", array("idlist"=>"1","table"=>"vat_vat","search"=>"false","filterChild"=>null,"filtering"=>false));
			$this->values->lin_step_situation = $input->create("text", "", "lin_step_situation", false, "", "", "text-left form-normal");
			//$this->values->lin_step_total = $input->create("text", "", "lin_step_total", false, "", "", "text-left form-normal");
			
			//var_dump($this->values);exit;
			$this->values = (array) $this->values;
			$this->v_createTr(array("action"=>true,"save"=>true,"change"=>true));
		}
		
		public function __destruct()
		{
		}
	}