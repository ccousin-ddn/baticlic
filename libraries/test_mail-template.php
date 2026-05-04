<?php
	$message = "
	Bonjour %contact%,<br><br>

	Voici le code nécessaire au renouvellement de votre mot de passe :

	<p style='color:#2e6da4;'>%code%</p>
	Veuillez le copier et le coller dans la fenêtre de votre navigateur. 
	";
	
	$facebook = '
			<table align="left" border="0" cellspacing="0" cellpadding="0" style="border-spacing: 0;border-collapse: collapse;vertical-align: top;padding: 0 5px 5px 0" height="37">
				<tbody>
					<tr style="vertical-align: top">
						<td width="37" align="left" valign="middle" style="word-break: break-word;border-collapse: collapse !important;vertical-align: top">
							<a href="https://www.facebook.com/" title="Facebook" target="_blank">
								<img src="#server#images/facebook.png" alt="Facebook" title="Facebook" width="32" style="outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: block;border: none;height: auto;line-height: 100%;max-width: 32px !important">
							</a>
						</td>
					</tr>
				</tbody>
			</table>
	';
	$twitter = '
			<table align="left" border="0" cellspacing="0" cellpadding="0" style="border-spacing: 0;border-collapse: collapse;vertical-align: top;padding: 0 5px 5px 0" height="37">
				<tbody>
					<tr style="vertical-align: top">
						<td width="37" align="left" valign="middle" style="word-break: break-word;border-collapse: collapse !important;vertical-align: top">
							<a href="http://twitter.com/" title="Twitter" target="_blank">
								<img src="#server#images/twitter.png" alt="Twitter" title="Twitter" width="32" style="outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: block;border: none;height: auto;line-height: 100%;max-width: 32px !important">
							</a>
						</td>
					</tr>
				</tbody>
			</table>
	';
	$google = '
			<table align="left" border="0" cellspacing="0" cellpadding="0" style="border-spacing: 0;border-collapse: collapse;vertical-align: top;padding: 0 5px 5px 0" height="37">
				<tbody>
					<tr style="vertical-align: top">
						<td width="37" align="left" valign="middle" style="word-break: break-word;border-collapse: collapse !important;vertical-align: top">
							<a href="http://plus.google.com/" title="Google+" target="_blank">
								<img src="#server#images/googleplus.png" alt="Google+" title="Google+" width="32" style="outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: block;border: none;height: auto;line-height: 100%;max-width: 32px !important">
							</a>
						</td>
					</tr>
				</tbody>
			</table>
	';

	$body_html = file_get_contents("mail-template.html");
	
	$body_html = str_replace("#facebook#",$facebook, $body_html);
	$body_html = str_replace("#twitter#",$twitter, $body_html);
	$body_html = str_replace("#google#",$google, $body_html);
			
	$body_html = str_replace("#title#","SEFI", $body_html);
	$body_html = str_replace("#color#","#b3c7d6", $body_html);
	$body_html = str_replace("#server#","http://soluweb.dnsalias.com/sefi_wd/", $body_html);
	$body_html = str_replace("#logo#","images/logo.png", $body_html);
	$body_html = str_replace("#hello#","Modification de votre mot de passe", $body_html);
	$body_html = str_replace("#message#",$message, $body_html);
	
	echo $body_html;
?>