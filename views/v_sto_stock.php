<?php
/**
*** Juillet 2019@SoluFile 
**/
	require_once (dirname(__FILE__)."/../models/m_sto_stock.php");
	
	class sto_stock_view extends sto_stock_model {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->columns = array(
				array("field"=>"art_code", "alter"=>"", "title"=>html("Code article"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				array("field"=>"art_description", "alter"=>"", "title"=>html("Article"), "sortable"=>true, "searchable"=>true, "class"=>"text-break", "halign"=>"left", "align"=>"left"),
				array("field"=>"sto_quantity", "alter"=>"", "title"=>html("Quantité"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				array("field"=>"art_price", "alter"=>"euro", "title"=>html("Prix achat"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				array("field"=>"total", "alter"=>"euro", "title"=>html("Total"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				array("field"=>"update_date", "alter"=>"date-be", "title"=>html("Date"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>"sortDate")
			);
			$this->columns_small = array(
				array("field"=>"art_code", "alter"=>"", "title"=>html("Code article"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				array("field"=>"art_description", "alter"=>"", "title"=>html("Article"), "sortable"=>true, "searchable"=>true, "class"=>"text-break", "halign"=>"left", "align"=>"left"),
				array("field"=>"sto_quantity", "alter"=>"", "title"=>html("Quantité"), "sortable"=>true, "searchable"=>false, "class"=>"text-nowrap font-weight-bold", "halign"=>"left", "align"=>"right"),
				array("field"=>"art_price", "alter"=>"euro", "title"=>html("Prix achat"), "sortable"=>true, "searchable"=>false, "class"=>"text-nowrap", "halign"=>"left", "align"=>"right"),
				array("field"=>"update_date", "alter"=>"date-be", "title"=>html("Date"), "sortable"=>true, "searchable"=>false, "class"=>"text-nowrap", "halign"=>"left", "align"=>"right", "sorter"=>"sortDate"),
				array("field"=>"sto_quantity_min", "alter"=>"", "title"=>html("Qté min"), "sortable"=>true, "searchable"=>false, "class"=>"text-nowrap", "halign"=>"left", "align"=>"right"),
				array("field"=>"sto_quantity_max", "alter"=>"", "title"=>html("Qté max"), "sortable"=>true, "searchable"=>false, "class"=>"text-nowrap", "halign"=>"left", "align"=>"right"),
			);
			$this->columnsArticle = array(
				array("field"=>"sho_name", "alter"=>"", "title"=>html("Magasin"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				array("field"=>"sto_quantity", "alter"=>"", "title"=>html("Quantité"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
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
		
		public function v_createTr($actions=array())
		{
			// *** Table object ***
			// info : new table(inv_line,level[,checkbox(true/false)][,action(true/false)])
			$table = new table($this->table, $this->level, false, $actions);
			// *** create columns title ***
			$table->createTableHead($this->columns_small);
			$this->json['html'] = $table->createJsonTr($this->key, $this->values, $this->color_conds);
		}
		
		public function v_createLineTable($idparent)
		{
			// *** Table object ***
			// info : new table(inv_line,level[,checkbox(true/false)][,action(true/false)])
			$table = new table($this->table, $this->level, false, array("action"=>true, "edit"=>true,"delete"=>true));
			// *** create columns title ***
			$thead = $table->createTableHead($this->columns_small,"thead-card");
			// *** create table lines ***
			$tbody = $table->createTableBody($this->key, $this->values, $this->color_conds);
			// *** create add line ***
			$tfoot = "";
			
			// *** create table ***
			$param = array("height"=>"auto", "show-columns"=>"false", "pagination"=>"false", "search"=>"true", "new-record"=>"false", "classes"=>"table table-no-bordered table-sm", "striped"=>"false", "export-table"=>"false", "filter-table"=>"");
			$this->json['html'] = $table->createTableLine($param, $thead, $tbody, $tfoot);
		}
		
		public function v_createLineTableArticle()
		{
			// *** Table object ***
			// info : new table(inv_line,level[,checkbox(true/false)][,action(true/false)])
			$table = new table($this->table, $this->level, false);
			// *** create columns title ***
			$thead = $table->createTableHead($this->columnsArticle,"thead-card");
			
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
			
			$this->values->sto_quantity_min = $input->create("text", "", "sto_quantity_min", false, "", "", "form-normal");
			$this->values->sto_quantity_max = $input->create("text", "", "sto_quantity_max", false, "", "", "form-normal");
			
			
			//var_dump($this->values);exit;
			$this->values = (array) $this->values;
			$this->v_createTr(array("action"=>true,"save"=>true,"undo"=>true));
		}
		
		public function v_showMaterialWorker()
		{
			$html = '
			<div class="bg-dark text-white w-100 p-3 text-center" style="font-size:1.5rem;" id="title">'.gettext("Mon équipement").'</div>
			<div class="container row-content mx-auto mt-5" id="material">
				<ul class="list-group">
			';
			foreach($this->values as $mat){
				$html .= '
				<li class="list-group-item d-flex justify-content-between align-items-center">
					'.$mat['art_description'].'
					<span class="badge badge-1 badge-pill">'.$mat['sto_quantity'].'</span>
				</li>
				';
			}
			$html .= '
				</ul>
			</div>
			';
			$this->json['html'] = $html;
		}
		
		public function v_showShopList($shop)
		{
			$html = '
			<div class="col-4 pt-2 px-2">
				<ul class="list-group text-center mb-3">
					<li class="list-group-item disabled font-weight-bold" aria-disabled="true">'.gettext("Source").'<i class="fal fa-sign-in-alt ml-2"></i></li>
				</ul>
				<ul class="list-group mb-3 select-shop-type text-center" id="wh_mvt_source" data-parent="wh_mvt_from">
					<a data-place="1" class="list-group-item list-group-item-action active" ><i class="fal fa-warehouse-alt mr-2"></i>'.gettext("Magasins").'</a>
					<a data-place="4" class="list-group-item list-group-item-action"><i class="fal fa-truck mr-2"></i>'.gettext("Véhicules").'</a>
					<a data-place="14" class="list-group-item list-group-item-action"><i class="fal fa-tools mr-2"></i>'.gettext("Equipements").'</a>
				</ul>
				<ul class="list-group" id="wh_mvt_from">
					'.$shop->json["html"].'
				</ul>
			</div>
			<div class="col-4 pt-2 px-2">
				<ul class="list-group mb-3 text-center">
					<li class="list-group-item disabled font-weight-bold" aria-disabled="true"><i class="fal fa-shopping-cart mr-2"></i>'.gettext("Articles").'</li>
				</ul>
				<ul class="list-group mb-3">
					<button id="btn_save" type="button" class="list-group-item font-weight-bold btn-save" disabled="disabled" data-loading-text="<i class=\'fal fa-spinner fa-pulse fa-fw mr-2\'></i>'.gettext("En cours...").'">'.gettext("Valider").'<i class="fal fa-arrow-circle-right ml-2"></i></button>
				</ul>
				<ul id="searchArticle">
					<form class="form-inline h-100 mb-0 flex-grow-1">
						<i class="fal fa-search" style="font-size: 1.5rem;"></i>
						<input id="btnSearchArticle" class="h-100 flex-fill" style="font-size: 1.5rem;" type="search" placeholder="" aria-label="Search" autocomplete="off" value="">
					</form>
				</ul>
				<ul class="list-group" id="wh_mvt_article">
				</ul>
			</div>
			<div class="col-4 pt-2 px-2">
				<ul class="list-group mb-3 text-center">
					<li class="list-group-item disabled font-weight-bold" aria-disabled="true"><i class="fal fa-sign-out-alt mr-2"></i>'.gettext("Destination").'</li>
				</ul>
				<ul class="list-group mb-3 select-shop-type text-center" id="wh_mvt_destination" data-parent="wh_mvt_to">
					<a data-place="1" class="list-group-item list-group-item-action active"><i class="fal fa-warehouse-alt mr-2"></i>'.gettext("Magasins").'</a>
					<a data-place="4" class="list-group-item list-group-item-action"><i class="fal fa-truck mr-2"></i>'.gettext("Véhicules").'</a>
					<a data-place="14" class="list-group-item list-group-item-action"><i class="fal fa-tools mr-2"></i>'.gettext("Equipements").'</a>
					<a data-place="3" class="list-group-item list-group-item-action"><i class="fal fa-digging mr-2"></i>'.gettext("Chantiers").'</a>
					<a data-place="15" class="list-group-item list-group-item-action" style="display:none;"><i class="fal fa-user-hard-hat mr-2"></i>'.gettext("Compagnons").'</a>
				</ul>
				<ul class="list-group" id="wh_mvt_to">
					'.$shop->json["html"].'
				</ul>
			</div>
			';
			$this->json['html'] = $html;
		}
		
		public function v_showStockArticles()
		{
			$html = '';
			foreach($this->values as $material){
				$html .= '
					<li class="list-group-item list-group-item-action d-flex flex-column wh-article" data-idarticle="'.$material['idarticle'].'">
						<div class="d-flex flex-row justify-content-between">
							<div class="d-flex">
								<i class="fal fa-square fa-fw mr-2"></i>
								<small class="searchable">'.$material['art_code'].'</small>
							</div>
							<input type="number" min="1" step="1" class="inp-qty" value="'.($material['sto_quantity']??$material['art_qty']).'">
						</div>
						<div class="searchable">
							'.$material['art_description'].'
						</div>
					</li>
				';
			}
			$this->json['html'] = $html;
		}
		
		public function v_createDashboard()
		{
			$div = new div();
			$this->json['html'] = $div->dashboard($this->values,"text-left");
		}
		
		public function __destruct()
		{
		}
	}