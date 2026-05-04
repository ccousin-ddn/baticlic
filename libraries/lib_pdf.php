<?php
	// error_reporting(0);
	// https://mpdf.github.io/installation-setup/installation-v7-x.html
    class pdf{
		
		private $result;
		private $cfg;
		private $header;
		private $body;
		private $tables;
		private $values;
		private $fileName;
		private $lines;
		private $totals;
		private $footer;
		private $template;
		private $table;
		private $th;
		private $annex;
		private $footnote;
		public $mb;

        public function __construct($table,$values,$fileName)
        {
        	$cfg_config = new cfg_config();
			$this->cfg = $cfg_config->c_getInfos();
			$this->template = "pdf_";
			$this->table = $table;
			$this->values = $values;
			$this->fileName = str_replace('/',' ',$fileName);
			$this->annex = "";
			$this->mb = 50;
        }
		
		private function createHeader()
		{
			$logo = '<img style="max-height:100px; width:auto; height:auto;" src="../images/logo.png">';
			$this->header = file_get_contents(PHP_ROOT."templates/".$this->template."header.html");
			$this->header = str_replace("%company_logo%",$logo, $this->header);
			$this->header = str_replace("%company_name%",$this->cfg['cfg_name'], $this->header);
			$this->header = str_replace("%company_adresse%",nl2br($this->cfg['cfg_address']), $this->header);
			$this->header = str_replace("%company_vat%",(!empty($this->cfg['cfg_vat'])?gettext("TVA").' : '.$this->cfg['cfg_vat']:''), $this->header);
			$this->header = str_replace("%company_email%",$this->cfg['cfg_mail']??"", $this->header);
			$this->header = str_replace("%company_phone%",$this->cfg['cfg_phone']??"", $this->header);
			$this->header = str_replace("%company_website%",$this->cfg['cfg_web']??"", $this->header);
		}
		
		private function createBody()
		{
			$info1 = $info2 = $info3 = $info4 = $info5 = $info_price = '';
			$about_title = 'Concerne';
			$footer1_title = 'Remarque';
			
			switch($this->table){
				case "quo_quotation" : 
					$numdoc = gettext("Devis n°").' '.$this->values->quo_ref;
					$datedoc = gettext("Date").' : <span class="date">'.alterData("date/be",$this->values->quo_date).'</span>';
					
					$info1 = gettext("Site").' : <span class="text-l">'.$this->values->sit_name.'</span>';
					$info2 = gettext("Nos références").' : <span class="text-l">Chantier n°'.$this->values->job_reference.'</span>';
					$info3 = gettext("Vos références").' : <span class="text-l">'.$this->values->quo_ref_client.'</span>';
					
					$about_content = $this->values->quo_title;
					
					if(($_GET['dataSent']??"") == "pdflight"){
						$this->totals = "";
						$this->values->quo_remark = "";
						$class_footer = 'hidden';
					}else{
						$tot_ht_txt = gettext("Total HT");
						$tot_ht = number_format($this->values->quo_amount, 2, ',', '.')." €";
						$tot_vat_txt = gettext("Total TVA");
						//$tot_vat = number_format(($this->values->quo_amount*$this->values->quo_tva_percent/100), 2, ',', '.')." €";
						$tot_vat = number_format($this->values->quo_tot_vat, 2, ',', '.')." €";
						$tot_ttc_txt = gettext("Total TTC");
						$tot_ttc = number_format($this->values->quo_tot_amount, 2, ',', '.')." €";
						$this->createTotalsQuotation($tot_ht_txt,$tot_ht,$tot_vat_txt,$tot_vat,$tot_ttc_txt,$tot_ttc);
						$this->annex = "cg.pdf";
					}
					
					$class_footer1 = 'w50 left';
					$footer1_content = $this->values->quo_remark;
					$class_footer2 = 'w50 right';
					//$footer2_title = 'Cachet et visa client';
					//$footer2_content = '<div style="font-size:7px;">«bon pour accord», le client reconnait avoir pris connaissance des conditions générales de vente ci-annexées.</div><img class="signature" src="../upload/signatures/'.$this->values->quo_signature.'" height="90"><div class="signatory">'.$this->values->quo_signatory.'</div>';
					
					$this->values->cli_contact = $this->values->con_name??$this->values->cli_contact??"";
					
					break;
				case "inv_invoice" : 
					$numdoc = ($this->values->inv_type == 0 ? gettext("Facture n°") : gettext("Avoir n°")).' '.$this->values->inv_ref;
					$datedoc = gettext("Date").' : <span class="date">'.alterData("date/be",$this->values->inv_date).'</span>';
					
					$info1 = gettext("Echéance").' : <span class="date">'.alterData("date/be",$this->values->inv_deadline).'</span>';
					$info2 = gettext("Site").' : <span class="text-l">'.$this->values->sit_name.'</span>';
					$info3 = gettext("Nos références").' : <span class="text-l">Chantier n°'.$this->values->job_reference.'</span>';
					$info4 = gettext("Vos références").' : <span class="text-l">'.$this->values->inv_order_ref.'</span>';

					$about_content = $this->values->inv_title;
					
					$tot_amount['lines'] = number_format($this->values->inv_tot_articles, 2, ',', '.')." €";
					$tot_text['lines'] = gettext("[1] Total Articles");
					
					$tot_amount['pre'] = number_format(array_sum(array_column($this->values->previous, 'lin_total')), 2, ',', '.')." €";
					$tot_text['pre'] = gettext("[2] Total Sit. Préc.");
					
					$tot_amount['sit'] = number_format($this->values->inv_tot_situation, 2, ',', '.')." €";
					$tot_text['sit'] = gettext("[3] Total Situation");
					
					$tot_amount['ret'] = number_format(array_sum(array_column($this->values->retention, 'ret_total')), 2, ',', '.')." €";
					$tot_text['ret'] = gettext("[8] Total Retenues");
					
					$tot_amount['eco'] = number_format(array_sum(array_column($this->values->ecotax, 'lin_total')), 2, ',', '.')." €";
					$tot_text['eco'] = gettext("[4] Total Ecotaxes");
					
					$tot_amount['ht'] = number_format($this->values->inv_tot_ht, 2, ',', '.')." €";
					$tot_text['ht'] = gettext("[5] Total HT");
					
					$tot_amount['vat'] = number_format($this->values->inv_tot_vat, 2, ',', '.')." €";
					$tot_text['vat'] = gettext("[6] Total TVA");
					
					$tot_amount['ttc'] = number_format($this->values->inv_tot_ttc, 2, ',', '.')." €";
					$tot_text['ttc'] = gettext("[7] Total TTC");
					
					$tot_amount['net'] = number_format($this->values->inv_tot_to_pay, 2, ',', '.')." €";
					$tot_text['net'] = gettext("[9] Net à payer");
					
					$this->createTotalsInvoice($tot_amount, $tot_text);
					
					$class_footer1 = 'w100';
					$footer1_content = $this->values->inv_remark;
					
					if($this->values->inv_idvat == 4){
						$info_price = "Autoliquidation de TVA en application du 13° de l'article 242 nonies A de l'Anexe II du code général des impots.";
					}
					
					$this->footnote = "<div class='text-xs pb-2'>En cas de dépassement de la date de règlement, une pénalité de retard sera appliquée de plein droit à hauteur de trois fois le taux d’intérêt légal <br>(article L 441-6 alinéa 12 du code de commerce), outre le montant de l’indemnité forfaitaire de 40 euros pour frais de recouvrement (article D 441-5 du code de commerce).</div>";
					
					break;
				case "sup_order" : 
					$numdoc = gettext("Bon de commande n°").' '.$this->values->ord_ref;
					$datedoc = gettext("Date").' : <span class="date">'.alterData("date/be",$this->values->ord_date).'</span>';
					
					$info1 = gettext("Délai livraison").' : <span class="text-l date">'.alterData("date/be",$this->values->ord_deadline).'</span>';
					$info2 = empty($this->values->job_reference) ? "" : gettext("Nos références").' : <span class="text-l">Chantier n°'.$this->values->job_reference.'</span>';
					
					$about_content = $this->values->ord_title;
					
					if($this->values->ord_printAmount == 1){
						$tot_amount_txt = 'Total Articles';
						$tot_amount = alterData("euro", $this->values->ord_amount);
						$tot_ecotax_txt = 'Total Eco taxe';
						$tot_ecotax = alterData("euro", $this->values->ord_ecotax);
						$tot_ht_txt = 'Total HT';
						$tot_ht = alterData("euro", $this->values->ord_tot_ht);
						$tot_vat_txt = 'TVA ('.number_format($this->values->ord_tva_percent, 1, ',', '.').'%)';
						$tot_vat = number_format(($this->values->ord_tot_ht*$this->values->ord_tva_percent/100), 2, ',', '.')." €";
						$tot_ttc_txt = 'Total TTC';
						$tot_ttc = alterData("euro", $this->values->ord_tot_amount);
						$this->createTotalsOrder($tot_amount_txt,$tot_amount,$tot_ecotax_txt,$tot_ecotax,$tot_ht_txt,$tot_ht,$tot_vat_txt,$tot_vat,$tot_ttc_txt,$tot_ttc);
					}else{
						$this->totals = '';
					}
					
					$info_price = "(1) Conditionnement<br>(2) Unité de vente<br>(3) Quantité commandé par conditionnement<br>(4) Unité de conditionnement";
					$this->footnote = "<div class='text-xs pb-2'>Veuillez rappeler sur toutes vos factures le numero de chantier, le nom de chantier et le numero du bon de commande.</div>";
					
					$class_footer = 'mb-3';
					$class_footer1 = 'w50 left';
					$footer1_content = $this->values->ord_remark;
					$class_footer2 = 'w50 right';
					$footer2_title = 'Livraison';
					$footer2_content = $this->values->ord_del_address;
					
					$this->values->cli_name = empty($this->values->age_name) ? $this->values->sup_name : $this->values->sup_name."<br>".$this->values->age_name;
					$this->values->cli_contact = $this->values->age_contact??$this->values->sup_contact;
					$this->values->cli_address_1 = $this->values->age_address??$this->values->sup_address_1;
					$this->values->cli_address_2 = $this->values->sup_address_2;
					$this->values->cli_pc = $this->values->sup_cp;
					$this->values->cli_city = $this->values->sup_city;
					$this->values->cli_tva = $this->values->sup_tva;
					
					break;
			}
			$client_contact = $this->values->cli_contact!=""?gettext("A l'attention de").' :<br>'.$this->values->cli_contact:'';
			$client_address = $this->values->cli_address_1.'<br>'.($this->values->cli_address_2!=""?$this->values->cli_address_2.'<br>':'').$this->values->cli_pc.' '.$this->values->cli_city;
			$client_vat = !empty($this->values->cli_tva)?gettext("TVA").' : '.strongDecrypt($this->values->cli_tva):'';
			
			$this->body = file_get_contents(PHP_ROOT."templates/".$this->template."body.html");
			$this->body = str_replace("%numdoc%",$numdoc, $this->body);
			$this->body = str_replace("%datedoc%",$datedoc, $this->body);
			$this->body = str_replace("%info1%",$info1, $this->body);
			$this->body = str_replace("%info2%",$info2, $this->body);
			$this->body = str_replace("%info3%",$info3, $this->body);
			$this->body = str_replace("%info4%",$info4, $this->body);
			$this->body = str_replace("%info5%",$info5, $this->body);
			
			$this->body = str_replace("%client_name%",$this->values->cli_name, $this->body);
			$this->body = str_replace("%client_contact%",$client_contact, $this->body);
			$this->body = str_replace("%client_address%",$client_address, $this->body);
			$this->body = str_replace("%client_vat%",$client_vat, $this->body);
			
			$this->body = str_replace("%class_about%",(empty($about_content)?"hidden":""), $this->body);
			$this->body = str_replace("%about_title%",$about_title, $this->body);
			$this->body = str_replace("%about_content%",nl2br($about_content), $this->body);
			
			$this->body = str_replace("%tables%",$this->tables, $this->body);
			$this->body = str_replace("%totals%",$this->totals, $this->body);
			
			$this->body = str_replace("%class_info_price%",(empty($info_price)?"hidden":"text-xs"), $this->body);
			$this->body = str_replace("%info_price%",$info_price, $this->body);
			
			$this->body = str_replace("%class_footer%",$class_footer??"", $this->body);
			
			$this->body = str_replace("%class_footer1%",(empty($footer1_content)?"hidden":$class_footer1), $this->body);
			$this->body = str_replace("%footer1_title%",$footer1_title??"", $this->body);
			$this->body = str_replace("%footer1_content%",nl2br($footer1_content??""), $this->body);
			
			$this->body = str_replace("%class_footer2%",(empty($footer2_content)?"hidden":$class_footer2), $this->body);
			$this->body = str_replace("%footer2_title%",$footer2_title??"", $this->body);
			$this->body = str_replace("%footer2_content%",nl2br($footer2_content??""), $this->body);
		}
		
		public function createTable($lines,$caption="",$info=array())
		{
			//var_dump($lines);exit;
			if($lines->count > 0){
				$this->tables .= '
				<table class="table-content-border mb-2" autosize="1">
					<thead>
				';
				if($caption !=''){
					$this->tables .= '
						<tr><th class="caption text-color" colspan="'.count($lines->columns_pdf).'">'.$caption.'</th></tr>
					';
				}
				$this->tables .= '
						<tr>
				';
				foreach($lines->columns_pdf as $col){
					$this->tables .='
								<th class="'.$col['class'].'" style="'.$col['style'].'">'.$col['title'].'</th>
					';
					$this->th .='
								<th class="'.$col['class'].'" style="'.$col['style'].'"></th>
					';
				}
				$this->tables .='
						</tr>
					</thead>
					<tbody>
				';
				$tot_title = 0;
				$bloc = false;
				$columns = $lines->columns_pdf;
				foreach($lines->values as $val){
					//dump($info);
					$tr_class = "";
					$lines->columns_pdf = $columns;
					switch($this->table){
						case "inv_invoice" :
							$qty = $val['lin_quantity'];
							$total = $val['lin_total'];
							if($val['lin_percent'] > 0){
								$lines->columns_pdf[6]['alter'] = "";
								$val['lin_total'] = number_format($total, 2, ',', '.')." €".'<br><small style="font-style: italic;color:#aaa;">'.number_format($val['lin_pu']*$qty, 2, ',', '.').' €</small>';
							}else{
								$val['vat_percent'] = "";
							}
							$val['lin_quantity'] = ($qty!=0?number_format($qty, 2, ',', '.'):"");
							// Title
							if(strtolower($val['lin_unit']??"") == "titre"){
								$val['lin_description'] = "<strong>".$val['lin_description']."</strong>";
								$val['lin_unit'] = "";
								$val['lin_quantity'] = "";
								$val['vat_percent'] = "";
								$val['lin_pu'] = 0;
								$val['lin_total'] = 0;
								$bloc = true;
								$tr_class = "bg";
							}
							if($bloc){
								$tot_title += $total;//floatval
								$td_art_class = "left tab";
							}
							if(strtolower($val['lin_unit']??"") == "total"){
								$lines->columns_pdf[0]['class'] = "text-right";
								$val['lin_description'] = "<strong><em>Sous-total&nbsp;</em></strong>";
								$val['lin_unit'] = "";
								$val['lin_quantity'] = "";
								$val['vat_percent'] = "";
								$val['lin_pu'] = "";
								$lines->columns_pdf[6]['alter'] = "";
								$val['lin_total'] = "<strong><em>".number_format($tot_title, 2, ',', '.')." €"."</em></strong>";
								$bloc = false;
								$tot_title = 0;
							}
							
							break;
						case "quo_quotation" :
							$val['art_name'] = ((!$info['codeArticle'] || $info['light'])?$val['art_short_name']:$val['art_name']).'<br><em class="small">'.$val['wor_description'].'</em>';
							$val['lin_quantity'] = ($val['lin_quantity']!=0?number_format($val['lin_quantity'], 2, ',', '.'):"");
							$val['vat_percent'] = ($val['lin_quantity']>0?$val['vat_percent']:"");
							
							// Title
							if(strtolower($val['lin_unit']??"") == "titre"){
								$val['art_name'] = "<strong>".$val['art_name']."</strong>";
								$val['lin_unit'] = "";
								$val['lin_quantity'] = "";
								$val['vat_percent'] = "";
								$val['lin_pu'] = 0;
								$val['lin_total'] = 0;
								$bloc = true;
								$tr_class = "bg";
							}
							if($bloc){
								$tot_title += $val['lin_total'];//floatval
								$td_art_class = "left tab";
							}
							
							if($info['OnlySubTotal'] || $info['light']){
								$val['lin_pu'] = 0;
								$val['lin_total'] = 0;
							}
							
							if(strtolower($val['lin_unit']??"") == "total"){
								if($info['light']){
									$val['art_name'] = "";
									$val['lin_unit'] = "";
									$val['lin_quantity'] = "";
									$val['vat_percent'] = "";
								}else{
									$lines->columns_pdf[0]['class'] = "text-right";
									$val['art_name'] = "<strong><em>Sous-total&nbsp;</em></strong>";
									$val['lin_unit'] = "";
									$val['lin_quantity'] = "";
									$val['vat_percent'] = "";
									$val['lin_pu'] = "";
									$lines->columns_pdf[5]['alter'] = "";
									$val['lin_total'] = "<strong><em>".number_format($tot_title, 2, ',', '.')." €"."</em></strong>";
									$bloc = false;
									$tot_title = 0;
								}
							}
							break;
						default : 
					}
						
					$this->tables .='
						<tr class="'.$tr_class.'">
					';
					foreach($lines->columns_pdf as $col){
						$this->tables .='
							<td class="'.$col['class'].'" style="'.$col['style'].'">'.alterData($col['alter'], $val[$col['field']]).'</td>
						';
					}
					$this->tables .='
						</tr>
					';
				}
				$this->tables .='
					</tbody>
				';
				$this->tables .= '
				</table>
				';
			}
			
		}
		
		public function createLine($lines)
		{
			if($lines->count > 0){
				$this->lines .='
					<thead>
						<tr>
				';
				foreach($lines->columns_pdf as $col){
					$this->lines .='
								<th class="'.$col['class'].'" style="'.$col['style'].'" colspan='.($col['colspan']??0).'>'.$col['title'].'</th>
					';
					$this->th .='
								<th class="'.$col['class'].'" style="'.$col['style'].'"></th>
					';
				}
				$this->lines .='
						</tr>
					</thead>
					<tbody>
				';
				foreach($lines->values as $val){
					$this->lines .='
						<tr>
					';
					foreach($lines->columns_pdf as $col){
						$this->lines .='
							<td class="'.$col['class'].'" style="'.$col['style'].'" colspan='.($col['colspan']??0).'>'.alterData($col['alter'], $val[$col['field']]).'</td>
						';		
					}
					$this->lines .='
						</tr>
					';
				}
				$this->lines .='
					</tbody>
				';
			}
		}
		
		private function createTotalsQuotation($tot_ht_txt,$tot_ht,$tot_vat_txt,$tot_vat,$tot_ttc_txt,$tot_ttc)
		{
			$this->totals = '
			<table class="table-content-border mb-2" style="page-break-inside:avoid">
				<tbody>
					<tr>
						<td class="no-border"></td>
						<td class="total-line border-color" style="width:150px;">'.$tot_ht_txt.'</td>
						<td class="text-right total-line border-color" style="width:100px;">'.$tot_ht.'</td>
					</tr>
					<tr>
						<td class="no-border"></td>
						<td class="" style="width:250px;" colspan=2>
						<table style="width:100%;" id="vat_detail">
							<tr>
								<td>TVA</td>
								<td>Base</td>
								<td>Montant</td>
							</tr>
			';
			foreach($this->values->vat_vat as $idvat=>$vatPercent){
				if($this->values->{"vat".$idvat} > 0){
					$this->totals .= '
							<tr>
								<td>'.$vatPercent.' %</td>
								<td>'.number_format($this->values->{"amount".$idvat}, 2, ',', '.').' €</td>
								<td>'.number_format($this->values->{"vat".$idvat}, 2, ',', '.').' €</td>
							</tr>
					';
				}
			}
			$this->totals .= '
						</table>
						</td>
					</tr>
					<tr>
						<td class="no-border"></td>
						<td  style="width:150px;">'.$tot_vat_txt.'</td>
						<td class="text-right" style="width:100px;">'.$tot_vat.'</td>
					</tr>
					<tr>
						<td class="no-border"></td>
						<td class="text-l text-color total" style="width:150px;">'.$tot_ttc_txt.'</td>
						<td class="text-right text-l text-color total" style="width:100px;">'.$tot_ttc.'</td>
					</tr>
				</tbody>
			</table>
			';
		}
		
		private function createTotalsInvoice($tot_amount, $tot_text)
		{
			//*** pre ***
			$html_pre = '<table class="table-detail">
							<tr>
								<td>Facture</td>
								<td>Description</td>
								<td>Montant</td>
							</tr>
			';
			foreach($this->values->previous as $previous){
				$html_pre .= '
						<tr>
							<td>'.$previous['inv_ref'].'</td>
							<td>'.$previous['lin_description'].'</td>
							<td class="text-right">'.number_format($previous['lin_total'], 2, ',', '.').' €</td>
						</tr>
				';
			}
			$html_pre .= '
						</table>';
			
			//*** ret ***
			$html_ret = '<table class="table-detail">
							<tr>
								<td></td>
								<td>%</td>
								<td>Total</td>
							</tr>
			';
			foreach($this->values->retention as $retention){
				$html_ret .= '
						<tr>
							<td>'.$retention['ret_name'].'</td>
							<td>'.$retention['ret_percent'].' %</td>
							<td class="text-right">'.number_format($retention['ret_total'], 2, ',', '.').' €</td>
						</tr>
				';
			}
			$html_ret .= '
						</table>';
			
			//*** ECO ***
			$html_eco = '<table class="table-detail">
							<tr>
								<td></td>
								<td>Qté</td>
								<td>PU</td>
								<td>TVA</td>
								<td>Total</td>
							</tr>
			';
			foreach($this->values->ecotax as $ecotax){
				$html_eco .= '
						<tr>
							<td>'.$ecotax['lin_description'].'</td>
							<td class="text-right">'.$ecotax['lin_quantity'].'</td>
							<td class="text-right">'.number_format($ecotax['lin_pu'], 2, ',', '.').' €</td>
							<td class="text-right">'.$ecotax['vat_percent'].' %</td>
							<td class="text-right">'.number_format($ecotax['lin_total'], 2, ',', '.').' €</td>
						</tr>
				';
			}
			$html_eco .= '
						</table>';
						
			//*** VAT ***
			$html_vat = '<table class="table-detail">
							<tr>
								<td></td>
								<td>Base</td>
								<td>Montant</td>
							</tr>
			';
			foreach($this->values->vat_vat as $idvat=>$vatPercent){
				if($this->values->{"vat".$idvat} > 0){
					$html_vat .= '
							<tr>
								<td>'.$vatPercent.' %</td>
								<td class="text-right">'.number_format($this->values->{"amount".$idvat}, 2, ',', '.').' €</td>
								<td class="text-right">'.number_format($this->values->{"vat".$idvat}, 2, ',', '.').' €</td>
							</tr>
					';
				}
			}
			$html_vat .= '
						</table>';
			
			$this->totals = '
<table class="table-content-border mb-2" style="page-break-inside:avoid">
	<tbody>
		<tr>
			<td class="no-border">
				<table name="tables" align="left">
					<tr><td class="caption">Déduction situations précédentes [2]</td></tr>
					<tr>
						<td class="no-border">'.$html_pre.'</td>
					</tr>
					<tr><td class="caption">Ecotaxes [4]</td></tr>
					<tr>
						<td class="no-border">'.$html_eco.'</td>
					</tr>
					<tr><td class="caption">Détail TVA [6]</td></tr>
					<tr>
						<td class="no-border">'.$html_vat.'</td>
					</tr>
					<tr><td class="caption">Retenues [8]</td></tr>
					<tr>
						<td class="no-border">'.$html_ret.'</td>
					</tr>
				</table>
			</td>
			<td class="no-border">
				<table name="totals" align="right">
					<tr>
						<td class="no-border"></td>
						<td class="total-line border-color" style="width:150px;">'.$tot_text['lines'].'</td>
						<td class="text-right total-line border-color" style="width:100px;">'.$tot_amount['lines'].'</td>
					</tr>
					<tr>
						<td class="no-border"></td>
						<td style="width:150px;">'.$tot_text['pre'].'</td>
						<td class="text-right" style="width:100px;">'.$tot_amount['pre'].'</td>
					</tr>
					<tr>
						<td class="no-border"></td>
						<td style="width:150px;">'.$tot_text['sit'].'</td>
						<td class="text-right" style="width:100px;">'.$tot_amount['sit'].'</td>
					</tr>
					<tr>
						<td class="no-border"></td>
						<td style="width:150px;">'.$tot_text['eco'].'</td>
						<td class="text-right" style="width:100px;">'.$tot_amount['eco'].'</td>
					</tr>
					<tr>
						<td class="no-border"></td>
						<td style="width:150px;">'.$tot_text['ht'].'</td>
						<td class="text-right" style="width:100px;">'.$tot_amount['ht'].'</td>
					</tr>
					<tr>
						<td class="no-border"></td>
						<td style="width:150px;">'.$tot_text['vat'].'</td>
						<td class="text-right" style="width:100px;">'.$tot_amount['vat'].'</td>
					</tr>
					<tr>
						<td class="no-border"></td>
						<td class="total-line border-color" style="width:150px;">'.$tot_text['ttc'].'</td>
						<td class="text-right total-line border-color" style="width:100px;">'.$tot_amount['ttc'].'</td>
					</tr>
					<tr>
						<td class="no-border"></td>
						<td style="width:150px;">'.$tot_text['ret'].'</td>
						<td class="text-right" style="width:100px;">'.$tot_amount['ret'].'</td>
					</tr>
					<tr>
						<td class="no-border"></td>
						<td class="text-l text-color total" style="width:150px;">'.$tot_text['net'].'</td>
						<td class="text-right text-l text-color total" style="width:100px;">'.$tot_amount['net'].'</td>
					</tr>
				</table>
			</td>
		</tr>
	</tbody>
</table>
			';
		}
		
		private function createTotalsOrder($tot_amount_txt,$tot_amount,$tot_ecotax_txt,$tot_ecotax,$tot_ht_txt,$tot_ht,$tot_vat_txt,$tot_vat,$tot_ttc_txt,$tot_ttc)
		{
			$this->totals = '
			<table class="table-content-border mb-2" style="page-break-inside:avoid">
				<tbody>
					<tr>
						<td class="no-border"></td>
						<td class="" style="width:150px;">'.$tot_amount_txt.'</td>
						<td class="text-right" style="width:100px;">'.$tot_amount.'</td>
					</tr>
					<tr>
						<td class="no-border"></td>
						<td class="" style="width:150px;">'.$tot_ecotax_txt.'</td>
						<td class="text-right" style="width:100px;">'.$tot_ecotax.'</td>
					</tr>
					<tr>
						<td class="no-border"></td>
						<td class="total-line border-color" style="width:150px;">'.$tot_ht_txt.'</td>
						<td class="text-right total-line border-color" style="width:100px;">'.$tot_ht.'</td>
					</tr>
					<tr>
						<td class="no-border"></td>
						<td  style="width:150px;">'.$tot_vat_txt.'</td>
						<td class="text-right" style="width:100px;">'.$tot_vat.'</td>
					</tr>
					<tr>
						<td class="no-border"></td>
						<td class="text-l text-color total" style="width:150px;">'.$tot_ttc_txt.'</td>
						<td class="text-right text-l text-color total" style="width:100px;">'.$tot_ttc.'</td>
					</tr>
				</tbody>
			</table>
			';
		}
		
		private function createFooter()
		{
			$template = $this->template;
			
			$this->footer = file_get_contents(PHP_ROOT."templates/".$template."footer.html");
			$this->footer = str_replace("%numpage%",$this->fileName.' - '.gettext("page"). "{PAGENO}", $this->footer);
			$this->footer = str_replace("%company_rc%",gettext("Siret").' : '.$this->cfg['cfg_siret'], $this->footer);
			$this->footer = str_replace("%company_iban%",gettext("IBAN").' : '.$this->cfg['cfg_iban'], $this->footer);
			$this->footer = str_replace("%company_bic%",gettext("BIC").' : '.$this->cfg['cfg_bic'], $this->footer);
			$this->footer = str_replace("%company_naf%",gettext("NAF").' : '.$this->cfg['cfg_naf'], $this->footer);
			$this->footer = str_replace("%company_qualibat%",$this->cfg['cfg_qualibat'], $this->footer);
			$this->footer = str_replace("%company_vat%",gettext("TVA").' : '.$this->cfg['cfg_vat'], $this->footer);
			$this->footer = str_replace("%company_email%",gettext("Mail").' : '.$this->cfg['cfg_mail'], $this->footer);
			$this->footer = str_replace("%company_phone%",gettext("Tél").' : '.$this->cfg['cfg_phone'], $this->footer);
			$this->footer = str_replace("%company_website%",gettext("Web").' : '.$this->cfg['cfg_web'], $this->footer);
			$this->footer = str_replace("%company_insurance%",gettext("Assurance").' : '.$this->cfg['cfg_insurance'], $this->footer);
		}
		
		public function createPdf($action, $page2="")
		{
			$this->createHeader();
			$this->createBody();
			$this->createFooter();

			setlocale(LC_ALL, 'fr_FR.utf8');
			/*** FONTS ***/
			$defaultConfig = (new Mpdf\Config\ConfigVariables())->getDefaults();
			$fontDirs = $defaultConfig['fontDir'];
			$defaultFontConfig = (new Mpdf\Config\FontVariables())->getDefaults();
			$fontData = $defaultFontConfig['fontdata'];
			$mpdf = new \Mpdf\Mpdf([
				'mode' => 'utf-8', 'format' => 'A4', 
				'margin_left'=>15, 'margin_right'=>15, 
				'margin_top'=>40, 'margin_bottom'=>$this->mb, 
				'margin_header'=>5, 'margin_footer'=>5, 
				//'setAutoBottomMargin' => 'stretch',
				'autoMarginPadding' => 2,
				//'mirrorMargins' => true,
				'fontDir' => array_merge($fontDirs, [
					PHP_ROOT.'webfonts',
				]),
				'fontdata' => $fontData + [
					'nunito' => [
						'R' => 'NunitoSans-Regular.ttf',
						'B' => 'NunitoSans-Bold.ttf',
						'I' => 'NunitoSans-RegularItalic.ttf',
						'BI' => 'NunitoSans-BoldItalic.ttf',
					]
				],
				'default_font' => 'nunito'
			]);
			
			$title = \Mpdf\Utils\UtfString::strcode2utf($this->fileName);
			$mpdf->SetTitle($title);
			$mpdf->SetAuthor(COMPANY_NAME);
			$mpdf->SetProtection(array('print','copy'));
			$mpdf->SetDisplayMode('fullpage');
			$mpdf->defaultheaderline = 3;
			$mpdf->SetHTMLHeader($this->header,'O');
			$mpdf->SetHTMLFooter($this->footer,'EO');
			
			$stylesheet = file_get_contents(PHP_ROOT."templates/".$this->template."css.css");
			$stylesheet = str_replace("%color%",$this->cfg['cfg_color'], $stylesheet,$i);

			$mpdf->WriteHTML($stylesheet,\Mpdf\HTMLParserMode::HEADER_CSS);
			//echo($this->body);exit;
			$mpdf->WriteHTML($this->body,\Mpdf\HTMLParserMode::HTML_BODY);
			
			if($page2 != ""){
				$mpdf->SetHTMLHeader($this->header,'O');
				$mpdf->SetHTMLHeader($this->header,'E');
				$mpdf->WriteHTML($page2);
			}
			
			if($this->footnote != ""){
				$mpdf->SetHTMLFooter($this->footnote.$this->footer);
			}
			// Annex
			if(!empty($this->annex)){
				$pagecount = $mpdf->SetSourceFile(PHP_ROOT."libraries/".$this->annex);
				for($i=1; $i<=$pagecount; $i++){
					$mpdf->AddPage('','','','1','on','','','','','','','','','','','-1','-1','-1','-1');
					$tplId = $mpdf->importPage($i);
					$mpdf->useTemplate($tplId);
				}
			}
			
			// Show or Mail
			//$action = "other";
			if($action == "pdf"){
				ob_end_clean();
				$mpdf->Output($this->fileName,'I');
				return true;
			}elseif($action == "mail"){
				$mpdf->Output(PHP_ROOT.'pdf_tmp/'.$this->fileName,'F');
				return $this->fileName;
			}else{
				$html = "<style>\n";
				$html .= $stylesheet;
				$html .= "\n</style>\n";
				$html .= "<div style='width:19cm;margin: 0 auto;border: 1px solid#ccc; padding:15px 40px 50px 40px;'>";
				$html .= $this->header;
				$html .= "\n".$this->body."\n";
				$html .= $this->footer;
				$html .= "</div>";
				return $html;
			}
		}
		
        public function __destruct()
        {
        }
    }