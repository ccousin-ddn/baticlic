<?php
/**
*** 09-2024@SOLUfile SRL 
**/
	require_once (dirname(__FILE__)."/../models/m_inv_line_previous.php");
	
	class inv_line_previous_view extends inv_line_previous_model {
		
		public function __construct()
		{
			parent::__construct();
			// sorter : sortDate / sortEuro
			$this->columns = array(
				array("field"=>"inv_ref", "alter"=>"", "title"=>html("Facture"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "width"=>"100", "sorter"=>"", "card-visible"=>"true"),
				array("field"=>"inv_situation", "alter"=>"", "title"=>html("Situation"), "sortable"=>true, "searchable"=>false, "class"=>"text-nowrap", "halign"=>"right", "align"=>"right", "width"=>"50", "sorter"=>"", "card-visible"=>"true"),
				array("field"=>"inv_description", "alter"=>"", "title"=>html("Description"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>"", "card-visible"=>"true"),
				array("field"=>"inv_total", "alter"=>"euro", "title"=>html("Montant"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"right", "align"=>"right", "width"=>"100", "sorter"=>"sortEuro", "card-visible"=>"true"),
				
			);
			$this->columns_edit = array(
				//array("field"=>"idinvoice", "alter"=>"", "title"=>html(""), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>"", "card-visible"=>"true"),
				array("field"=>"inv_ref", "alter"=>"", "title"=>html(""), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>"", "card-visible"=>"true"),
				array("field"=>"inv_description", "alter"=>"", "title"=>html(""), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "width"=>"500", "sorter"=>"", "card-visible"=>"true"),
				array("field"=>"inv_total", "alter"=>"", "title"=>html(""), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"right", "align"=>"right", "width"=>"100", "sorter"=>"", "card-visible"=>"true"),
				
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
			$head = $table->createHeadLines("Déduction situations précédentes");
			// *** create columns title ***
			$thead = $table->createTableHead($this->columns,"thead-card");
			// *** create table lines ***
			$tbody = $table->createTableBody($this->key, $this->values, $this->color_conds);
			// *** create add line ***
			$tfoot = $table->createTableFoot(count($this->columns), $idparent);
			
			// *** create table ***
			$param = array("height"=>"auto", "show-columns"=>"false", "pagination"=>"false", "search"=>"false", "new-record"=>"false", "id-parent"=>"$idparent", "classes"=>"table table-bordered table-sm", "striped"=>"false", "export-table"=>"false", "filter-table"=>"");
			
			$this->json['html'] = $table->createTableLine($param, $thead, $tbody, $tfoot, $head);
		}
		
		public function v_editLine()
		{
			$input = new input($this->values, $this->cryptfields);
			
			$this->values->inv_ref = $input->create("select", "", "idprevinvoice", false, 1, "", "text-right form-normal", array("idlist"=>"2","table"=>"inv_invoice","conds"=>array("idsite = ".$this->dataSent['idsite']),"data"=>array("inv_tot_situation","subtext"),"search"=>"true","filterChild"=>null,"filtering"=>false));
			$this->values->inv_description = $input->create("text", "", "inv_description", false, "", "", "text-left form-normal");
			$this->values->inv_total = $input->create("text", "", "inv_total", false, "", "", "text-right form-normal");
			
			//var_dump($this->values);exit;
			$this->values = (array) $this->values;
			$this->v_createTr(array("action"=>true,"save"=>true,"undo"=>true));
		}
		
		public function __destruct()
		{
		}
	}