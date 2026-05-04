<?php
	//error_reporting(0);
    class pdf{
		
		private $result;
		private $cfg;
		private $header;
		private $footer;
		private $doc_class;
		private $bottom;
		
		private $addPdf;

        public function __construct($addPdf, $doc_ref, $penalty=false)
        {
        	
			$logo = PHP_ROOT."images/logo-mail.jpg";
			$this->doc_class = "";
			$this->addPdf = $addPdf;
			
        	$cfg_config = new cfg_config();
			$this->cfg = $cfg_config->c_getInfos();
			$this->result = "";
			$this->header = '
			<table width="100%" style=" font-size: 10px; color: #666;">
				<tr>
					<td valign="top"><img style="max-height:100px; width:auto; height:auto;" src="'.$logo.'"></td>
					<td style="text-align: right; width:350px;">
						<strong>'.$this->cfg['cfg_name'].'</strong><br>
						'.nl2br($this->cfg['cfg_address']).'<br>
						TVA : '.$this->cfg['cfg_vat'].'
					</td>
				</tr>
			</table>
			';
			
			if($penalty){
				$this->footer = '
				<div style="font-size: 7pt;">En cas de dépassement de la date de règlement, une pénalité de retard sera appliquée de plein droit à hauteur de trois fois le taux d’intérêt légal <br>(article L 441-6 alinéa 12 du code de commerce), outre le montant de l’indemnité forfaitaire de 40 euros pour frais de recouvrement <br>(article D 441-5 du code de commerce).</div>
				<br>
				';
				$this->bottom = 45;
			}else{
				$this->footer = '';
				$this->bottom = 45;
			}
			$this->footer .= '
			<div style="text-align:center; width:100%; font-size: 8px; color: #666; clear:both;">'.$doc_ref.' - page {PAGENO}</div>
			<table width="100%" style="border-top: 1px solid #ccc; margin-top:5px; vertical-align: bottom; font-size: 10px; color: #666;">
				<tr>
					<td width="7.5cm" style="vertical-align:top">
						Siret : '.$this->cfg['cfg_siret'].'<br>
						IBAN : '.$this->cfg['cfg_iban'].'<br>
						BIC : '.$this->cfg['cfg_bic'].'<br>
						NAF : 4120B
					</td>
					<td width="3.5cm" style="text-align:center;font-size:7px;">
						<img class="" src="../images/qualibat.png" width="2cm"><br>
						<strong>'.$this->cfg['cfg_qualibat'].'</strong>
					</td>
					<td style="vertical-align:top;text-align:right;">
						Tél : '.$this->cfg['cfg_phone'].'<br>
						Mail : '.$this->cfg['cfg_mail'].'<br>
						Assurance : '.$this->cfg['cfg_insurance'].'
					</td>
				</tr>
			</table>
			';
        }

		public function createPdf($content, $fileName, $action, $page2="")
		{
			setlocale(LC_ALL, 'fr_FR.utf8');
		    //include($_SERVER['DOCUMENT_ROOT']."/mpdf/mpdf.php");
			include("mpdf57/mpdf.php");

		    $mpdf=new mPDF('utf-8','A4','','',15,15,45,$this->bottom,10,10);// left, right, top, bottom, header-top, footer-bottom
			//$mpdf->showImageErrors = true;
			$mpdf->SetFont('nunito');
			//$mpdf->mirrorMargins = 1;  // Use different Odd/Even headers and footers and mirror margins
			
			$mpdf->SetTitle(strcode2utf($fileName));
			$mpdf->SetAuthor(COMPANY_NAME);
			
			$mpdf->SetProtection(array('print','copy'));

			$mpdf->SetDisplayMode('fullpage');
			$mpdf->useOddEven = 1;

			$mpdf->SetHTMLHeader($this->header);
			$mpdf->SetHTMLFooter($this->footer,'O');
			$mpdf->SetHTMLFooter($this->footer,'E');
			
			$stylesheet = file_get_contents('../css/mpdfstyletables.css');
			$mpdf->WriteHTML($stylesheet,1);
			$mpdf->WriteHTML("<div>".trim($content)."</div>");
			
			if($page2 != ""){
				//$mpdf->shrink_tables_to_fit = 1;
				//$mpdf->AddPage();
				$mpdf->SetHTMLHeader($this->header,'O');
				$mpdf->SetHTMLHeader($this->header,'E');
				$mpdf->WriteHTML("<div class='$this->doc_class'>".$page2."</div>");
			}
			
			if(!empty($this->addPdf)){
				$mpdf->AddPage('','','','1','on','','','','','','','','','','','-1','-1','-1','-1');
				$mpdf->SetImportUse(); 
				$pagecount = $mpdf->SetSourceFile('../libraries/cg.pdf');
				$tplId = $mpdf->ImportPage(1);
				$mpdf->UseTemplate($tplId);
				$mpdf->AddPage();
				$tplId = $mpdf->ImportPage(2);
				$mpdf->UseTemplate($tplId);
			}
			
			// Show or Mail
			if($action == "pdf"){
				$mpdf->Output($fileName,'I');
				return true;
			}elseif($action == "mail"){
				$mpdf->Output(dirname(__FILE__).'/../pdf_tmp/'.$fileName,'F');
				return $fileName;
			}else{
				$html = "<style>\n";
				$html .= file_get_contents('../css/mpdfstyletables.css');
				$html .= "\n</style>\n";
				//echo "<html>\n";
				$html .= $this->header;
				$html .= "\n<div class='$this->doc_class'>\n".$content."\n</div>\n";
				$html .= $this->footer;
				//echo "\n</html>";
				return $html;
			}
		}
		
        public function __destruct()
        {
        }
    }