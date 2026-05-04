<?php
$html = '
<body>
	<h1>mPDF</h1>
	<h2>Floating & Fixed Position elements</h2>
	<h4>CSS "Float"</h4>
	<div class="gradient">
		Block elements can be positioned alongside each other using the CSS property float: left or right. The clear property can also be used, set as left|right|both. Float is only supported on block elements (i.e. not SPAN etc.) and is not fully compliant with the CSS specification. 
		Float only works properly if a width is set for the float, otherwise the width is set to the maximum available (full width, or less if floats already set).
		<br />
		Margin-right can still be set for a float:right and vice-versa.
		<br />
		A block element next to a float has the padding adjusted so that content fits in the remaining width. Text next to a float should wrap correctly, but backgrounds and borders will overlap and/or lie under the floats in a mess.
		<br />
		NB The width that is set defines the width of the content-box. So if you have two floats with width=50% and either of them has padding, margin or border, they will not fit together on the page.
	</div>
';
$html2 ='
	<h4>CSS "Position"</h4>
	At the bottom of the page are two DIV elements with position:fixed and position:absolute set
	<div class="myfixed1">
		<table class="table-content">
			<tr>
				<td>
					<h2>Remarques :</h2>
				</td>
			</tr>
			<tr>
				<td>
					ici
				</td>
			</tr>
		</table>
	</div>
</body>';
//==============================================================
//==============================================================
//==============================================================
include("mpdf57/mpdf.php");
$mpdf=new mPDF('utf-8','A4','','',15,15,35,20,5,5);// left, right, top, bottom, header-top, footer-bottom
$mpdf->SetFont('nunito');
$mpdf->mirrorMargins = 1;  // Use different Odd/Even headers and footers and mirror margins
$mpdf->SetTitle(strcode2utf("titre"));
$mpdf->SetAuthor("RC");
$mpdf->SetProtection(array('print','copy'));
$mpdf->SetDisplayMode('fullpage');
$mpdf->useOddEven = 1;
$mpdf->SetHTMLHeader("header",'O');
$mpdf->SetHTMLFooter("footer",'O');
$mpdf->SetHTMLFooter("footer",'E');
$stylesheet = file_get_contents('../css/mpdfstyletables.css');
$mpdf->WriteHTML($stylesheet,1);
$mpdf->WriteHTML($html);
$mpdf->WriteHTML($html2);
$mpdf->Output("fichier",'I');

$mpdf=new mPDF('utf-8','A4','','',15,15,35,20,5,5);// left, right, top, bottom, header-top, footer-bottom
$mpdf->SetFont('nunito');
$mpdf->mirrorMargins = 1;  // Use different Odd/Even headers and footers and mirror margins
$mpdf->SetTitle(strcode2utf($fileName));
$mpdf->SetAuthor(COMPANY_NAME);
$mpdf->SetProtection(array('print','copy'));
$mpdf->SetDisplayMode('fullpage');
$mpdf->useOddEven = 1;
$mpdf->SetHTMLHeader("header",'O');
$mpdf->SetHTMLFooter("footer",'O');
$mpdf->SetHTMLFooter("footer",'E');
$stylesheet = file_get_contents('../css/mpdfstyletables.css');
$mpdf->WriteHTML($stylesheet,1);
//$mpdf->WriteHTML("<div>".trim($content)."</div>");
$mpdf->WriteHTML($this->html2);
$mpdf->Output();

exit;
//==============================================================
//==============================================================
//==============================================================
?>