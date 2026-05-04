<?php
/**
*** Juillet 2019@SoluFile 
**/
	require_once (dirname(__FILE__)."/../models/m_cfg_config.php");
	
	class cfg_config_view extends cfg_config_model {
		
		public function __construct()
		{
			parent::__construct();
		}
		
		public function v_createCard()
		{
			// *** Input object ***
			$input = new input($this->values, $this->cryptfields);
			// info : input->create(type, label, name, mandatory(true/false)[, defaultValue[, divSize[, inputClass[, info(array)]]]])
			$inputs = $input->create("text", "Dénomination", "cfg_name", false, "", "col-md-6");
			$inputs .= $input->create("text", "Siret", "cfg_siret", false, "", "col-md-3");
			$inputs .= $input->create("text", "TVA", "cfg_vat", false, "", "col-md-2");
			$inputs .= $input->create("text", "% TVA", "cfg_pc_tva", false, "", "col-md-1");
			$inputs .= $input->create("text", "IBAN", "cfg_iban", false, "", "col-md-4");
			$inputs .= $input->create("text", "BIC", "cfg_bic", false, "", "col-md-2");
			$inputs .= $input->create("text", "Téléphone", "cfg_phone", false, "", "col-md-3");
			$inputs .= $input->create("text", "Site Web", "cfg_web", false, "", "col-md-3");
			$inputs .= $input->create("text", "Assurance", "cfg_insurance", false, "", "col-md-4", "", array("row"=>1));
			$inputs .= $input->create("text", "Qualibat", "cfg_qualibat", false, "", "col-md-2", "", array("row"=>1));
			$inputs .= $input->create("text", "Courriel", "cfg_mail", false, "", "col-md-3");
			$inputs .= $input->create("text", "Cci courriel", "cfg_cci", false, "", "col-md-3");
			$inputs .= $input->create("textarea", "Adresse", "cfg_address", false, "", "col-md-3", "", array("row"=>3));
			$inputs .= $input->create("select", "Mois début exercice fiscale", "cfg_fiscal_year_end", false, "", "col-md-2", "", array("contents"=>$this->month));
			$inputs .= $input->create("text", "NAF", "cfg_naf", false, "", "col-md-1");
			$inputs .= $input->create("text", "Latitude", "cfg_lat", false, "", "col-md-2");
			$inputs .= $input->create("text", "Longitude", "cfg_lng", false, "", "col-md-2");
			$inputs .= $input->create("text", "N° chantier", "cfg_job_num", false, "", "col-md-2");

			$inputs_quo = $input->create("text", "N° devis", "cfg_quo_num", true, "", "col-md-2");
			$inputs_quo .= $input->create("text", "Contact", "cfg_contact_quo", false, "", "col-md-4");
			$inputs_quo .= $input->breakline;
			$inputs_quo .= $input->create("textarea", "Contenu mail", "cfg_template_quo_quotation", false, "", "col-md-6", "", array("row"=>7));

			$inputs_inv = $input->create("text", "N° facture", "cfg_inv_num", true, "", "col-md-2");
			$inputs_inv .= $input->create("text", "Contact", "cfg_contact_inv", false, "", "col-md-4");
			$inputs_inv .= $input->breakline;
			$inputs_inv .= $input->create("textarea", "Contenu mail", "cfg_template_inv_invoice", false, "", "col-md-6", "", array("row"=>7));

			$inputs_ord = $input->create("text", "N° commande", "cfg_ord_num", true, "", "col-md-2");
			$inputs_ord .= $input->create("text", "Contact", "cfg_contact_ord", false, "", "col-md-4");
			$inputs_ord .= $input->breakline;
			$inputs_ord .= $input->create("textarea", "Contenu mail", "cfg_template_sup_order", false, "", "col-md-6", "", array("row"=>7));
			$inputs_ord .= $input->create("textarea", "Adresse livraison", "cfg_del_address", false, "", "col-md-3", "", array("row"=>7));
			$inputs_ord .= $input->create("textarea", "Remarque commande fournisseur", "cfg_info_sup_order", false, "", "col-md-3", "", array("row"=>7));

			$inputs_del = $input->create("text", "N° livraison", "cfg_del_num", true, "", "col-md-2");
			$inputs_del .= $input->create("text", "Contact", "cfg_contact_del", false, "", "col-md-4");
			$inputs_del .= $input->breakline;
			$inputs_del .= $input->create("textarea", "Contenu mail", "cfg_template_sub_delivery", false, "", "col-md-6", "", array("row"=>7));

			$inputs_coef = $input->create("text", "Coef Charge", "cfg_coef_charge", false, "", "col-md-2");
			$inputs_coef .= $input->create("text", "Coef FGMO", "cfg_coef_fgmo", false, "", "col-md-2");
			$inputs_coef .= $input->create("text", "Coef FGMTX", "cfg_coef_fgmtx", false, "", "col-md-2");
			$inputs_coef .= $input->create("text", "Coef FGMTL", "cfg_coef_fgmtl", false, "", "col-md-2");
			$inputs_coef .= $input->create("text", "Coef FGST", "cfg_coef_fgst", false, "", "col-md-2");
			$inputs_coef .= $input->create("text", "Panier", "cfg_amount_diner", false, "", "col-md-2");

			// *** Div object ***
			$div = new div();
			// info : div->create(divClass, elem1[, elem2[, elem3[, ...]]])
			$div1 = $div->create("row", $inputs);
			$div2 = $div->create("row", $inputs_quo);
			$div3 = $div->create("row", $inputs_inv);
			$div4 = $div->create("row", $inputs_ord);
			$div5 = $div->create("row", $inputs_del);
			$div6 = $div->create("row", $inputs_coef);

			// *** Button object ***
			$button = new button($this->table, $this->values->idconfig);
			// info : button->create(type[, buttonClass])
			$btn_save 	= $button->create("save");
			$btn_close = $button->create("close");
			$btn_addCookie = '
				<div class="float-left">
					<button type="button" id="btn_cookie" class="btn btn-outline-dark" onclick="setCookie(\'location\', \'office\', \''.(365.25*20).'\')" autocomplete="off" data-loading-text="<i class=\'fal fa-circle-notch fa-spin mr-2\'></i>'.gettext("En cours...").'">
						<i class="fal fa-user-secret mr-2"></i>
						<span class="d-none d-lg-inline">'.gettext("Ajouter cookie bureau").'</span>
					</button>
				</div>
			';
			$btn_addCookieWorker = '
				<div class="btn-left">
					<button type="button" id="btn_cookie_worker" class="btn btn-outline-dark" onclick="setCookie(\'location\', \'mobile\', \''.(365.25*20).'\')" autocomplete="off" data-loading-text="<i class=\'fal fa-circle-notch fa-spin mr-2\'></i>'.gettext("En cours...").'">
						<i class="fal fa-mobile mr-2"></i>
						<span class="d-none d-lg-inline">'.gettext("Ajouter cookie mobile").'</span>
					</button>
				</div>
			';
			// *** Form object ***
			$form = new form();
			// info : form->create("normal/upload", formId, formClass, elem1[, elem2[, elem3[, ...]]])
			$form1 = $form->create("normal", $this->table."_form", "", $div1);
			$form2 = $form->create("normal", $this->table."_form", "", $div2);
			$form3 = $form->create("normal", $this->table."_form", "", $div3);
			$form4 = $form->create("normal", $this->table."_form", "", $div4);
			$form5 = $form->create("normal", $this->table."_form", "", $div5);
			$form6 = $form->create("normal", $this->table."_form", "", $div6);
			
			// *** Tab object ***
			$tab = new tab(1);
			$tabs = $tab->create("Coordonnées", "underline", $form1);
			$tabs = $tab->create("Infos Devis", "underline ml-2", $form2);
			$tabs = $tab->create("Infos Facture", "underline ml-2", $form3);
			$tabs = $tab->create("Infos Commande", "underline ml-2", $form4);
			$tabs = $tab->create("Infos Livraison", "underline ml-2", $form5);
			$tabs = $tab->create("Coefficients", "underline ml-2", $form6);
			
			// *** Body variable ***
			// info : $body = $input[.$div][.$form][.tabs]
			$body = $tabs;
			
			// *** Page object ***
			// info : new page(type, id, label, pageClass, body, buttons)
			$page = new page("modal", $this->table."_modal", $this->picto." ".$this->label, "modal-dialog-scrollable modal-xl", $body, $btn_addCookie.$btn_addCookieWorker.$btn_save.$btn_close);
			$this->json['html'] = $page->create(array($this->values->insert_date??date("y-m-d"), $this->values->update_date??date("y-m-d")));
		}
		
		public function v_createChartData()
		{
			$this->json['html'] = '<canvas id="chBar" height="160px" width="100"></canvas>';
			//$months = array(gettext("janv."),gettext("févr."),gettext("mars"),gettext("avr."),gettext("mai"),gettext("juin"),gettext("juill."),gettext("août"),gettext("sept."),gettext("oct."),gettext("nov."),gettext("déc."));
			
			$this->json['labels'] = $this->months;
			$this->json['data'] = $this->values;
			$this->json['label'] = array(gettext("CA"), gettext("Net"));
			//$this->json['tot_year'] = gettext("Total")." ".$this->dataSent." : ".alterData("euro",array_sum(array_column($this->values, 'total')));
			$id = array_search(date("m"), array_column($this->values, 'num_month'));
			/*
			if($id !== false){
				$this->json['tot_month'] = ucfirst(strftime("%B %Y"))." : ".alterData("euro",$this->values[$id]['total']);
			}else{
				$this->json['tot_month'] = ucfirst(strftime("%B %Y"))." : ".alterData("euro",0);
			}
			*/
		}
		
		public function v_createPiesData()
		{
			$this->json['html_inv'] = '<canvas id="chPie_inv" height="280"></canvas>';
			$this->json['html_bil'] = '<canvas id="chPie_bil" height="280"></canvas>';
			$this->json['data_inv'] = $this->ca;
			$this->json['data_bil'] = $this->bil;
			
			$tot = count($this->ca);
			$count = round(130/$tot);
			for($i=1; $i<=$tot; $i++)
			{
				$this->json['col_inv'][] = adjustBrightness("#00441B", $i*$count);
			}
			
			$tot = count($this->bil);
			$count = round(230/$tot);
			for($i=1; $i<=$tot; $i++)
			{
				$this->json['col_bil'][] = adjustBrightness("#67000D", $i*$count);
			}
			
			$this->json['label_inv'] = gettext("Chiffre d'affaire")." : ".alterData("euro",array_sum(array_column($this->ca, 'total')));
			$this->json['label_bil'] = array(gettext("Charges totales")." : ".alterData("euro",array_sum(array_column($this->bil, 'total'))), gettext("Charges ventilées")." : ".alterData("euro",array_sum(array_column($this->bil, 'tot_bre'))));
		}
		
		public function v_createPiesBreakdown()
		{
			$this->json['data_bil1'] = $this->bil_lev1;
			$this->json['data_bil2'] = $this->bil_lev2;
			$this->json['data_bil3'] = $this->bil_lev3;
			
			$tot = count($this->bil_lev1);
			if($tot>0){
				$count = round(130/$tot);
				for($i=1; $i<=$tot; $i++){$this->json['col_bil1'][] = adjustBrightness("#3E1F47", $i*$count);}
			}
			$tot = count($this->bil_lev2);
			if($tot>0){
				$count = round(230/$tot);
				for($i=1; $i<=$tot; $i++){$this->json['col_bil2'][] = adjustBrightness("#0B525B", $i*$count);}
			}
			$tot = count($this->bil_lev3);
			if($tot>0){
				$count = round(130/$tot);
				for($i=1; $i<=$tot; $i++){$this->json['col_bil3'][] = adjustBrightness("#1B3A4B", $i*$count);}
			}
			
			$this->json['label_bil1'] = gettext("Charges niv. 1")." : ".alterData("euro",array_sum(array_column($this->bil_lev1, 'total')));
			$this->json['label_bil2'] = gettext("Charges niv. 2")." : ".alterData("euro",array_sum(array_column($this->bil_lev2, 'total')));
			$this->json['label_bil3'] = gettext("Charges niv. 3")." : ".alterData("euro",array_sum(array_column($this->bil_lev3, 'total')));
		
			$this->json['html'] = '
			<div class="pt-3"></div>
			<div id="planning" class="p-3 bg-light">
				<div id="title" class="d-flex justify-content-between align-items-center">
					<h3 class="m-0">
						<i class="fal fa-calendar-week mr-2"></i>'.gettext("Ventilation charges").'
					</h3>
					<form class="form-inline" style="position:relative;">
						<label for="dateFrom" class="mr-2">Du</label>
						<input type="text" style="width:115px;" data-date-format="DD-MM-YYYY" data-format="L" class="form-control datetimepicker-input" placeholder="Du" id="dateFrom" name="dateFrom" value="'.$this->dataSent['dateFrom'].'" data-toggle="datetimepicker" data-target="#dateFrom" data-date-widget-positioning=\''.json_encode(array("horizontal"=>"right","vertical"=>"bottom")).'\'>
						<label for="dateTo" class="ml-2 mr-2">au</label>
						<input type="text" style="width:115px;" data-date-format="DD-MM-YYYY" data-format="L" class="form-control datetimepicker-input" placeholder="au" id="dateTo" name="dateTo" value="'.$this->dataSent['dateTo'].'" data-toggle="datetimepicker" data-target="#dateTo" data-date-widget-positioning=\''.json_encode(array("horizontal"=>"right","vertical"=>"bottom")).'\'>
						<button type="button" class="btn btn-outline-client ml-3" onclick="table_action({idrecord:0,tablename:\'cfg_config\',dataSent:{},action:\'refreshBreakdownPie\'})">
							<i class="fal fa-sync-alt"></i>
						</button>
					</form>
				</div>
			
				<div class="row mt-3">
					<div class="col-4"><canvas id="chPie_bil1"></canvas></div>
					<div class="col-4"><canvas id="chPie_bil2"></canvas></div>
					<div class="col-4"><canvas id="chPie_bil3"></canvas></div>
				</div>
			</div>
			';
		}
		
		public function __destruct()
		{
		}
	}