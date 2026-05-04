<?php
/**
*** Juillet 2019@SoluFile 
**/
	require_once (dirname(__FILE__)."/../models/m_inv_line.php");
	
	class inv_line_view extends inv_line_model {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->columns = array(
				array("field"=>"lin_description", "alter"=>"", "title"=>html("Description"), "sortable"=>false, "searchable"=>false, "class"=>"text-break", "halign"=>"left", "align"=>"left"),
				array("field"=>"lin_unit", "alter"=>"", "title"=>html("U"), "sortable"=>false, "searchable"=>false, "width"=>"60", "class"=>"text-nowrap", "halign"=>"right", "align"=>"right"),
				array("field"=>"lin_quantity", "alter"=>"", "title"=>html("Qté"), "sortable"=>false, "searchable"=>false, "width"=>"120", "class"=>"text-nowrap", "halign"=>"right", "align"=>"right"),
				array("field"=>"lin_pu", "alter"=>"", "title"=>html("PU"), "sortable"=>false, "searchable"=>false, "width"=>"120", "class"=>"text-nowrap", "halign"=>"right", "align"=>"right"),
				array("field"=>"lin_percent", "alter"=>"", "title"=>html("% avancement"), "sortable"=>false, "searchable"=>false, "width"=>"70", "class"=>"text-nowrap", "halign"=>"right", "align"=>"right"),
				array("field"=>"lin_situation", "alter"=>"", "title"=>html("Total"), "sortable"=>false, "searchable"=>false, "width"=>"120", "class"=>"text-nowrap", "halign"=>"right", "align"=>"right"),
				array("field"=>"vat_percent", "alter"=>"", "title"=>html("% TVA"), "sortable"=>false, "searchable"=>false, "width"=>"70", "class"=>"text-nowrap", "halign"=>"right", "align"=>"right"),
				
			);
			$this->columns_pdf = array(
				array("field"=>"lin_description", "alter"=>"", "title"=>html("Désignation"), "style"=>"", "class"=>"text-left", "colspan"=>2),
				array("field"=>"lin_unit", "alter"=>"", "title"=>html("U"), "style"=>"width:30px;", "class"=>"text-right text-nowrap"),
				array("field"=>"lin_quantity", "alter"=>"", "title"=>html("Qté"), "style"=>"width:70px;", "class"=>"text-right text-nowrap"),
				array("field"=>"lin_pu", "alter"=>"euroNull", "title"=>html("Prix/u"), "style"=>"width:100px;", "class"=>"text-right text-nowrap"),
				array("field"=>"lin_percent", "alter"=>"percentNull", "title"=>html("% avancement"), "style"=>"width:50px;", "class"=>"text-right text-nowrap"),
				array("field"=>"vat_percent", "alter"=>"percent", "title"=>html("% TVA"), "style"=>"width:50px;", "class"=>"text-right text-nowrap text-sm"),
				array("field"=>"lin_situation", "alter"=>"euroNull", "title"=>html("Total"), "style"=>"width:100px;", "class"=>"text-right text-nowrap"),
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
			$param = array("height"=>"auto", "show-columns"=>"true", "pagination"=>"false", "search"=>true, "classes"=>"table table-no-bordered  table-hover table-light", "striped"=>"false", "new-record"=>"true", "export-table"=>"false", "filter-table"=>"");
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
			// *** create header ***
			$head = $table->createHeadLines("Travaux");
			// *** create columns title ***
			$thead = $table->createTableHead($this->columns,"thead-card");
			// *** create table lines ***
			$tbody = $table->createTableBody($this->key, $this->values, $this->color_conds);
			// *** create add line ***
			$tfoot = $table->createTableFoot(count($this->columns), $idparent);
			
			// *** create table ***
			$param = array("height"=>"auto", "show-columns"=>"false", "pagination"=>"false", "search"=>"false", "new-record"=>"false", "id-parent"=>"$idparent", "classes"=>"table table-no-bordered table-sm", "striped"=>"false", "export-table"=>"false", "filter-table"=>"");
			$this->json['html'] = $table->createTableLine($param, $thead, $tbody, $tfoot, $head);
		}
		
		public function v_editLine()
		{
			$input = new input($this->values, $this->cryptfields);
			define('IDCLIENT', $this->dataSent['idclient']);
			
			$this->values->lin_description = $input->create("text", "", "lin_description", false, "", "", "text-left form-normal");
			$this->values->lin_unit = $input->create("text", "", "lin_unit", false, "", "", "text-right form-normal");
			$this->values->lin_quantity = $input->create("text", "", "lin_quantity", false, "", "", "text-right form-normal");
			$this->values->lin_pu = $input->create("text", "", "lin_pu", false, "", "", "text-right form-normal");
			$this->values->lin_percent = $input->create("text", "", "lin_percent", false, "", "", "text-right form-normal");
			$this->values->vat_percent = $input->create("select", "", "idvat", false, 1, "", "text-right form-normal", array("idlist"=>"1","table"=>"vat_vat","search"=>"false","filterChild"=>null,"filtering"=>false));
			
			//var_dump($this->values);exit;
			$this->values = (array) $this->values;
			$this->v_createTr(array("action"=>true,"save"=>true,"undo"=>true));
		}
		
		public function __destruct()
		{
		}
	}