<?php
/**
*** Juillet 2019@SoluFile 
**/
	require_once (dirname(__FILE__)."/../models/m_sup_order_line.php");
	
	class sup_order_line_view extends sup_order_line_model {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->columns = array(
				array("field"=>"art_name", "alter"=>"", "title"=>html("Article"), "sortable"=>true, "searchable"=>true, "class"=>"text-break", "halign"=>"left", "align"=>"left"),
				array("field"=>"lin_code", "alter"=>"", "title"=>html("Teinte"), "sortable"=>true, "searchable"=>true, "class"=>"text-break", "halign"=>"left", "align"=>"left"),
				array("field"=>"lin_QdM", "alter"=>"", "title"=>html("Qté UdM"), "sortable"=>true, "searchable"=>true, "width"=>"90", "class"=>"text-nowrap", "halign"=>"right", "align"=>"right"),
				array("field"=>"lin_UdM", "alter"=>"", "title"=>html("UdM"), "sortable"=>false, "searchable"=>false, "width"=>"70", "class"=>"text-nowrap", "halign"=>"right", "align"=>"right"),
				array("field"=>"lin_QdC", "alter"=>"", "title"=>html("Qté UdC"), "sortable"=>true, "searchable"=>true, "width"=>"90", "class"=>"text-nowrap", "halign"=>"right", "align"=>"right"),
				array("field"=>"lin_UdC", "alter"=>"", "title"=>html("UdC"), "sortable"=>false, "searchable"=>false, "width"=>"70", "class"=>"text-nowrap", "halign"=>"right", "align"=>"right"),
				array("field"=>"lin_pu_UdM", "alter"=>"", "title"=>html("Prix UdM"), "sortable"=>true, "searchable"=>true, "width"=>"100", "class"=>"text-nowrap", "halign"=>"right", "align"=>"right"),
				array("field"=>"lin_ecotax", "alter"=>"", "title"=>html("Eco taxe"), "sortable"=>true, "searchable"=>true, "width"=>"100", "class"=>"text-nowrap", "halign"=>"right", "align"=>"right"),
				array("field"=>"lin_total", "alter"=>"", "title"=>html("Total"), "sortable"=>true, "searchable"=>true, "width"=>"150", "class"=>"text-nowrap", "halign"=>"right", "align"=>"right"),
				
			);
			$this->columnsReceiptEdit = array(
				array("field"=>"art_name", "alter"=>"", "title"=>html("Article"), "sortable"=>false, "searchable"=>false, "class"=>"text-break", "halign"=>"left", "align"=>"left"),
				array("field"=>"lin_UdC", "alter"=>"", "title"=>html("U"), "sortable"=>false, "searchable"=>false, "width"=>"70", "class"=>"text-nowrap", "halign"=>"right", "align"=>"right"),
				array("field"=>"lin_QdC", "alter"=>"", "title"=>html("Qté commandée"), "sortable"=>false, "searchable"=>false, "width"=>"150", "class"=>"text-nowrap text-primary", "halign"=>"right", "align"=>"right"),
				array("field"=>"lin_qty_received_total", "alter"=>"", "title"=>html("Qté déjà livrée"), "sortable"=>false, "searchable"=>false, "width"=>"150", "class"=>"text-nowrap text-info", "halign"=>"right", "align"=>"right"),
				array("field"=>"lin_qty_remaining", "alter"=>"", "title"=>html("Qté restante"), "sortable"=>false, "searchable"=>false, "width"=>"150", "class"=>"text-nowrap text-orange", "halign"=>"right", "align"=>"right"),
				array("field"=>"lin_qty_received", "alter"=>"line-copy", "title"=>html("Qté livrée"), "sortable"=>false, "searchable"=>false, "width"=>"150", "class"=>"text-nowrap", "halign"=>"right", "align"=>"right"),
			);
			$this->columnsReceipt = array(
				array("field"=>"art_name", "alter"=>"", "title"=>html("Article"), "sortable"=>false, "searchable"=>false, "class"=>"text-break", "halign"=>"left", "align"=>"left"),
				array("field"=>"lin_UdC", "alter"=>"", "title"=>html("U"), "sortable"=>false, "searchable"=>false, "width"=>"70", "class"=>"text-nowrap", "halign"=>"right", "align"=>"right"),
				array("field"=>"lin_QdC", "alter"=>"", "title"=>html("Qté commandée"), "sortable"=>false, "searchable"=>false, "width"=>"150", "class"=>"text-nowrap text-primary", "halign"=>"right", "align"=>"right"),
				array("field"=>"lin_qty_received_total", "alter"=>"", "title"=>html("Qté total livrée"), "sortable"=>false, "searchable"=>false, "width"=>"150", "class"=>"text-nowrap text-info", "halign"=>"right", "align"=>"right"),
				array("field"=>"lin_qty_remaining", "alter"=>"", "title"=>html("Qté restante"), "sortable"=>false, "searchable"=>false, "width"=>"150", "class"=>"text-nowrap text-danger", "halign"=>"right", "align"=>"right"),
				array("field"=>"lin_qty_received", "alter"=>"", "title"=>html("Qté livrée"), "sortable"=>false, "searchable"=>false, "width"=>"150", "class"=>"text-nowrap text-success", "halign"=>"right", "align"=>"right"),
			);
			$this->columnsHistory = array(
				array("field"=>"ord_date", "alter"=>"date-be", "title"=>html("Date"), "sortable"=>false, "searchable"=>false, "width"=>"70", "class"=>"text-nowrap text-primary", "halign"=>"right", "align"=>"right"),
				array("field"=>"sup_name", "alter"=>"", "title"=>html("Fournisseur"), "sortable"=>false, "searchable"=>false, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				array("field"=>"supart_ref", "alter"=>"", "title"=>html("Réf fourn."), "sortable"=>false, "searchable"=>false, "width"=>"150", "class"=>"text-break", "halign"=>"left", "align"=>"left"),
				array("field"=>"supart_description", "alter"=>"", "title"=>html("Description"), "sortable"=>false, "searchable"=>false, "width"=>"", "class"=>"text-break", "halign"=>"left", "align"=>"left"),
				//array("field"=>"art_price", "alter"=>"euro", "title"=>html("Prix moyen"), "sortable"=>false, "searchable"=>false, "width"=>"100", "class"=>"text-nowrap text-success", "halign"=>"right", "align"=>"right"),
				array("field"=>"supart_price", "alter"=>"euro", "title"=>html("Prix fourn."), "sortable"=>false, "searchable"=>false, "width"=>"100", "class"=>"text-nowrap text-info", "halign"=>"right", "align"=>"right"),
				array("field"=>"lin_QdM", "alter"=>"", "title"=>html("Qté UdM"), "sortable"=>true, "searchable"=>true, "width"=>"90", "class"=>"text-nowrap", "halign"=>"right", "align"=>"right"),
				array("field"=>"lin_UdM", "alter"=>"", "title"=>html("UdM"), "sortable"=>false, "searchable"=>false, "width"=>"70", "class"=>"text-nowrap", "halign"=>"right", "align"=>"right"),
				array("field"=>"lin_QdC", "alter"=>"", "title"=>html("Qté UdC"), "sortable"=>true, "searchable"=>true, "width"=>"90", "class"=>"text-nowrap", "halign"=>"right", "align"=>"right"),
				array("field"=>"lin_UdC", "alter"=>"", "title"=>html("UdC"), "sortable"=>false, "searchable"=>false, "width"=>"70", "class"=>"text-nowrap", "halign"=>"right", "align"=>"right"),
				array("field"=>"lin_pu_UdM", "alter"=>"", "title"=>html("Prix UdM"), "sortable"=>true, "searchable"=>true, "width"=>"100", "class"=>"text-nowrap", "halign"=>"right", "align"=>"right"),
			);
			$this->columns_pdf = array(
				array("field"=>"supart_ref", "alter"=>"", "title"=>html("Référence"), "style"=>"", "class"=>"text-left", "colspan"=>2),
				array("field"=>"lin_code", "alter"=>"", "title"=>html("Teinte"), "style"=>"", "class"=>"text-left", "colspan"=>2),
				array("field"=>"art_name", "alter"=>"", "title"=>html("Désignation"), "style"=>"", "class"=>"text-left", "colspan"=>2),
				array("field"=>"lin_QdM", "alter"=>"", "title"=>html("Cond (1)"), "style"=>"width:50px;", "class"=>"text-right text-nowrap"),
				array("field"=>"lin_UdM", "alter"=>"", "title"=>html("Unité vente (2)"), "style"=>"width:50px;", "class"=>"text-right text-nowrap"),
				array("field"=>"lin_QdC", "alter"=>"", "title"=>html("Qté (3)"), "style"=>"width:50px;", "class"=>"text-right text-nowrap bg-2"),
				array("field"=>"lin_UdC", "alter"=>"", "title"=>html("Unité cond (4)"), "style"=>"width:50px;", "class"=>"text-right text-nowrap bg-2"),
				array("field"=>"lin_pu_UdM", "alter"=>"euro", "title"=>html("Prix/u"), "style"=>"width:80px;", "class"=>"text-right text-nowrap"),
				array("field"=>"lin_ecotax", "alter"=>"euro", "title"=>html("Eco taxe"), "style"=>"width:80px;", "class"=>"text-right text-nowrap"),
				array("field"=>"lin_total", "alter"=>"euro", "title"=>html("Total"), "style"=>"width:100px;", "class"=>"text-right text-nowrap"),
			);
			$this->columns_pdf_light = array(
				array("field"=>"art_name", "alter"=>"", "title"=>html("Désignation"), "style"=>"", "class"=>"text-left", "colspan"=>2),
				array("field"=>"lin_unit", "alter"=>"", "title"=>html("U"), "style"=>"width:50px;", "class"=>"text-right text-nowrap"),
				array("field"=>"lin_quantity", "alter"=>"", "title"=>html("Qté"), "style"=>"width:50px;", "class"=>"text-right text-nowrap"),
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
			$param = array("height"=>"auto", "show-columns"=>"false", "pagination"=>"false", "search"=>"false", "new-record"=>"false", "id-parent"=>"$idparent", "classes"=>"table table-sm", "striped"=>"false", "export-table"=>"false", "filter-table"=>"");
			$this->json['html'] = $table->createTableLine($param, $thead, $tbody, $tfoot);
		}
		
		public function v_createLineTableReceipt($idparent,$rec_status)
		{
			// *** Table object ***
			// info : new table(inv_line,level[,checkbox(true/false)][,action(true/false)])
			$table = new table($this->table, $this->level, false);
			// *** create columns title ***
			if($rec_status > 0){
				$thead = $table->createTableHead($this->columnsReceipt,"thead-card");
			}else{
				$thead = $table->createTableHead($this->columnsReceiptEdit,"thead-card");
			}
			// *** create table lines ***
			$tbody = $table->createTableBody($this->key, $this->values, $this->color_conds);
			$tfoot = "";
			
			// *** create table ***
			$param = array("height"=>"auto", "show-columns"=>"false", "pagination"=>"false", "search"=>"false", "new-record"=>"false", "id-parent"=>"$idparent", "classes"=>"table table-sm", "striped"=>"false", "export-table"=>"false", "filter-table"=>"");
			$this->json['html'] = $table->createTableLine($param, $thead, $tbody, $tfoot);
		}
		
		public function v_createLineTableHistory()
		{
			// *** Table object ***
			// info : new table(inv_line,level[,checkbox(true/false)][,action(true/false)])
			$table = new table($this->table, $this->level, false);
			// *** create columns title ***
			$thead = $table->createTableHead($this->columnsHistory,"thead-card");
			
			// *** create table lines ***
			$tbody = $table->createTableBody($this->key, $this->values, $this->color_conds);
			$tfoot = "";
			
			// *** create table ***
			$param = array("height"=>"auto", "show-columns"=>"false", "pagination"=>"false", "search"=>"false", "new-record"=>"false", "id-parent"=>"", "classes"=>"table table-sm", "striped"=>"false", "export-table"=>"false", "filter-table"=>"");
			$this->json['html'] = $table->createTableLine($param, $thead, $tbody, $tfoot);
		}
		
		public function v_editLine()
		{
			$input = new input($this->values, $this->cryptfields);
			define('IDSUPPLIER', $this->dataSent['idsupplier']);
			
			if($this->values->lin_description != ""){
				$classSelect = "d-none form-normal";
				$classInput = "form-normal";
			}else{
				$classSelect = "form-normal";
				$classInput = "d-none form-normal";
			}
			//echo $this->values->lin_description."=>select:".$classSelect." input:".$classInput;exit;
			
			$this->values->art_name = $input->create("select", "", "idsuparticle", false, "", "", $classSelect, array("idlist"=>"2","data"=>array("tokens","subtext","supart_price","supart_unit","supart_unit_qty","supart_ecotax","art_unit"),"level"=>3,"table"=>"sup_article","search"=>"true","filterChild"=>null,"none-result-text"=>"Créer un nouvel article")).$input->create("text", "", "lin_description", false, "", "", $classInput);
			$this->values->lin_code = $input->create("text", "", "lin_code", false, "", "", "text-right form-normal");
			$this->values->lin_QdM = $input->create("text", "", "lin_QdM", false, "", "", "text-right form-normal");
			$this->values->lin_UdM = $input->create("text", "", "lin_UdM", false, "", "", "text-right form-normal");
			$this->values->lin_QdC = $input->create("text", "", "lin_QdC", false, "", "", "text-right form-normal");
			$this->values->lin_UdC = $input->create("text", "", "lin_UdC", false, "", "", "text-right form-normal");
			$this->values->lin_pu_UdM = $input->create("text", "", "lin_pu_UdM", false, "", "", "text-right form-normal");
			
			//var_dump($this->values);exit;
			$this->values = (array) $this->values;
			$this->v_createTr(array("action"=>true,"save"=>true,"change"=>true));
		}
		
		public function __destruct()
		{
		}
	}