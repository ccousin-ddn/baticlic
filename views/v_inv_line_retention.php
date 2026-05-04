<?php
/**
*** 08-2025@SOLUfile SRL 
**/
	require_once (dirname(__FILE__)."/../models/m_inv_line_retention.php");
	
	class inv_line_retention_view extends inv_line_retention_model {
		
		public function __construct()
		{
			parent::__construct();
			// sorter : sortDate / sortEuro
			$this->columns = array(
				array("field"=>"ret_name", "alter"=>"", "title"=>html("Retenue"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>"", "card-visible"=>"true"),
				array("field"=>"ret_percent", "alter"=>"", "title"=>html("%"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"right", "align"=>"right", "width"=>"100", "sorter"=>"", "card-visible"=>"true"),
				array("field"=>"ret_total", "alter"=>"euro", "title"=>html("Total"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"right", "align"=>"right", "width"=>"100", "sorter"=>"sortEuro", "card-visible"=>"true"),
				
			);
			$this->columns_edit = array(
				array("field"=>"ret_name", "alter"=>"", "title"=>html(""), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>"", "card-visible"=>"true"),
				array("field"=>"ret_percent", "alter"=>"", "title"=>html(""), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>"sortEuro", "card-visible"=>"true"),
				array("field"=>"ret_total", "alter"=>"", "title"=>html(""), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>"sortEuro", "card-visible"=>"true"),
				
			);
			$this->columns_pdf = array(
				//array("field"=>"", "alter"=>"", "title"=>html(""), "style"=>"", "class"=>""),
			);
			$this->color_conds = array(
				//array("column"=>"", "query"=>" == 0", "class"=>"td-warning")
			);
		}
		
		public function v_createFullTable()
		{
			// *** Table object ***
			// info : new table($this->table,level[,checkbox(true/false)][,action(true/false)])
			$table = new table($this->table, $this->level, false);
			// *** create header ***
			$head = $table->createHead($this->picto, ngettext($this->label, $this->labels, $this->count), $this->count);
			// *** create columns title ***
			$thead = $table->createTableHead($this->columns);
			// *** create table lines ***
			$tbody = $table->createTableBody($this->key, $this->values, $this->color_conds);
			// *** create table ***
			$param = array("height"=>"auto", "show-columns"=>"false", "pagination"=>"true", "search"=>"false", "classes"=>"table table-bordered table-sm", "striped"=>"false", "new-record"=>"false", "export-table"=>"false", "filter-table"=>"");
			
			$this->json['html'] = $table->createTable($head, $param, $thead, $tbody);
		}
		
		public function v_createTr($actions=array())
		{
			// *** Table object ***
			// info : new table($this->table,level[,checkbox(true/false)][,action(true/false)])
			$table = new table($this->table, $this->level, false, $actions);
			// *** create columns title ***
			$table->createTableHead($this->columns_edit);
			
			$this->json['html'] = $table->createJsonTr($this->key, $this->values, $this->color_conds);
			$this->json['tdClass'] = $table->info;
		}
		
		public function v_createLineTable($idparent)
		{
			// *** Table object ***
			// info : new table(inv_line,level[,checkbox(true/false)][,(array("action"=>true/false,"read"=>true/false,"edit"=>true/false,"delete"=>true/false))])
			$table = new table($this->table, $this->level, false, array("action"=>true,"edit"=>true,"delete"=>true));
			// *** create header ***
			$head = $table->createHeadLines("Retenues");
			// *** create columns title ***
			$thead = $table->createTableHead($this->columns,"thead-card");
			// *** create table lines ***
			$tbody = $table->createTableBody($this->key, $this->values, $this->color_conds);
			// *** create add line ***
			$tfoot = $table->createTableFoot(count($this->columns), $idparent);
			
			// *** create table ***
			$param = array("height"=>"auto", "show-columns"=>"false", "pagination"=>"false", "search"=>"false", "new-record"=>"false", "id-parent"=>"$idparent", "classes"=>"table table-sm table-bordered", "striped"=>"false", "export-table"=>"false", "filter-table"=>"");
			
			$this->json['html'] = $table->createTableLine($param, $thead, $tbody, $tfoot, $head);
		}
		
		public function v_editLine()
		{
			$input = new input($this->values, $this->cryptfields);
			
			$this->values->ret_name = $input->create("select", "", "idretention", false, "", "", "text-right form-normal", array("idlist"=>"1","table"=>"ret_retention","conds"=>array(),"data"=>array(),"search"=>"false","filterChild"=>null,"filtering"=>false));
			$this->values->ret_percent = $input->create("text", "", "ret_percent", false, "", "", "text-left form-normal");
			$this->values->ret_total = $input->create("readonly", "", "ret_total", false, "", "", "text-right form-normal");
			
			//var_dump($this->values);exit;
			$this->values = (array) $this->values;
			$this->v_createTr(array("action"=>true,"save"=>true,"undo"=>true));
		}
		
		public function __destruct()
		{
		}
	}