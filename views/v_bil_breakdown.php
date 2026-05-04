<?php
/**
*** Novembre 2019@SoluFile 
**/
	require_once (dirname(__FILE__)."/../models/m_bil_breakdown.php");
	
	class bil_breakdown_view extends bil_breakdown_model {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->columns = array(
				array("field"=>"ord_name", "alter"=>"", "title"=>html("Commande n°"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>""),
				array("field"=>"lev1_name", "alter"=>"", "title"=>html("Charge niv. 1"), "sortable"=>true, "searchable"=>true, "width"=>"250", "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>""),
				array("field"=>"lev2_name", "alter"=>"", "title"=>html("Charge niv. 2"), "sortable"=>true, "searchable"=>true, "width"=>"250", "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>""),
				array("field"=>"lev3_name", "alter"=>"", "title"=>html("Charge niv. 3"), "sortable"=>true, "searchable"=>true, "width"=>"250", "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>""),
				array("field"=>"bre_amount", "alter"=>"", "title"=>html("Montant"), "sortable"=>true, "searchable"=>true, "width"=>"100", "class"=>"text-nowrap bre_amount", "halign"=>"left", "align"=>"right", "sorter"=>""),
			);
			$this->color_conds = array();
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
			$param = array("height"=>"auto", "show-columns"=>"true", "pagination"=>"false", "search"=>true, "classes"=>"table table-no-bordered  table-hover table-light", "striped"=>"false", "new-record"=>"true", "export-table"=>"true", "filter-table"=>"");
			$this->json['html'] = $table->createTable($head, $param, $thead, $tbody);
		}
		
		public function v_createTr($actions)
		{
			// *** Table object ***
			// info : new table($this->table,level[,checkbox(true/false)][,action(true/false)])
			$table = new table($this->table, $this->level, false, $actions);
			// *** create columns title ***
			$table->createTableHead($this->columns);
			$this->json['html'] = $table->createJsonTr($this->key, $this->values, $this->color_conds);
		}
		
		public function v_createLineTable($idparent)
		{
			// *** Table object ***
			// info : new table(inv_line,level[,checkbox(true/false)][,(array("action"=>true/false,"read"=>true/false,"edit"=>true/false,"delete"=>true/false))])
			$table = new table($this->table, $this->level, false, array("action"=>true,"edit"=>true,"delete"=>true));
			// *** create columns title ***
			$thead = $table->createTableHead($this->columns,"thead-card");
			// *** create table lines ***
			$tbody = $table->createTableBody($this->key, $this->values, $this->color_conds);
			// *** create add line ***
			$tfoot = $table->createTableFoot(count($this->columns), $idparent);
			
			// *** create table ***
			$param = array("height"=>"auto", "show-columns"=>"false", "pagination"=>"false", "search"=>"false", "new-record"=>"false", "id-parent"=>"$idparent", "classes"=>"table table-sm", "striped"=>"false", "export-table"=>"false", "filter-table"=>"");
			$this->json['html'] = $table->createTableLine($param, $thead, $tbody, $tfoot);
		}
		
		public function v_editLine()
		{
			$input = new input($this->values, $this->cryptfields);
			
			$this->values->ord_name = $input->create("select", "", "idsuporder", false, "", "", "", array("idlist"=>"3","table"=>"sup_order", "conds"=>array("ord_status IN (3,4,5,6)", "ord_breakdown = 0", "sup_order.idsupplier = ".$this->dataSent['idsupplier']),"data"=>array("subtext"),"search"=>"true","filterChild"=>null));
			$this->values->lev1_name = $input->create("select", "", "idaccountlev1", false, "", "", "", array("idlist"=>"1","table"=>"bil_account_lev1","search"=>"true","filterChild"=>null,"none-result-text"=>"Créer un nouveau compte"));
			$this->values->lev2_name = $input->create("select", "", "idaccountlev2", false, "", "", "", array("idlist"=>"1","table"=>"bil_account_lev2","search"=>"true","filterChild"=>null,"none-result-text"=>"Créer un nouveau compte"));
			$this->values->lev3_name = $input->create("select", "", "idaccountlev3", false, "", "", "", array("idlist"=>"1","table"=>"bil_account_lev3","search"=>"true","filterChild"=>null,"none-result-text"=>"Créer un nouveau compte"));
			$this->values->bre_amount = $input->create("text", "", "bre_amount", false, "", "", "form-normal text-right");
			
			
			$this->values = (array) $this->values;
			//var_dump($this->values);exit;
			$this->v_createTr(array("action"=>true,"save"=>true,"undo"=>true));
		}
		
		public function __destruct()
		{
		}
	}