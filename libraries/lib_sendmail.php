<?php
	// https://github.com/PHPMailer/PHPMailer/
	
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\SMTP;
	use PHPMailer\PHPMailer\Exception;

    class sendmail{
    	public $template;
		public $from;
		public $fromName;
		public $reply;
		public $replyName;
		public $to;
		public $cc;
		public $bcc;
		public $subject;
		public $name;
		public $site_url;
		public $message;
		public $logo;
		public $attach;
		public $error;
		
		private $result;
		private $body_text;
		private $body_html;
		private $facebook;
		private $twitter;
		private $instagram;
		
        public function __construct()
        {
			$this->fromName   	= COMPANY_NAME;
			$this->replyName  	= COMPANY_NAME;
			$this->cc         	= "";
			$this->site_url		= (isset($_SERVER["HTTPS"]) ? "https" : "http")."://".SITE_URL;
			$this->template		= "../libraries/mail-template.html";
			$this->logo 		= "images/logo.png";
			$this->attach 		= "";
        	$this->result 		= true;
			$this->error 		= "";
			if(!empty(COMPANY_FACEBOOK)){
				$this->facebook = '
					<table align="left" border="0" cellspacing="0" cellpadding="0" style="border-spacing: 0;border-collapse: collapse;vertical-align: top;padding: 0 5px 5px 0" height="37">
						<tbody>
							<tr style="vertical-align: top">
								<td width="37" align="left" valign="middle" style="word-break: break-word;border-collapse: collapse !important;vertical-align: top">
									<a href="'.COMPANY_FACEBOOK.'" title="Facebook" target="_blank">
										<img src="#server#/images/facebook.png" alt="Facebook" title="Facebook" width="32" style="outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: block;border: none;height: auto;line-height: 100%;max-width: 32px !important">
									</a>
								</td>
							</tr>
						</tbody>
					</table>
				';
			}
			if(!empty(COMPANY_TWITTER)){
				$this->twitter = '
					<table align="left" border="0" cellspacing="0" cellpadding="0" style="border-spacing: 0;border-collapse: collapse;vertical-align: top;padding: 0 5px 5px 0" height="37">
						<tbody>
							<tr style="vertical-align: top">
								<td width="37" align="left" valign="middle" style="word-break: break-word;border-collapse: collapse !important;vertical-align: top">
									<a href="'.COMPANY_TWITTER.'" title="Twitter" target="_blank">
										<img src="#server#/images/twitter.png" alt="Twitter" title="Twitter" width="32" style="outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: block;border: none;height: auto;line-height: 100%;max-width: 32px !important">
									</a>
								</td>
							</tr>
						</tbody>
					</table>
				';
			}
			if(!empty(COMPANY_INSTAGRAM)){
				$this->instagram = '
					<table align="left" border="0" cellspacing="0" cellpadding="0" style="border-spacing: 0;border-collapse: collapse;vertical-align: top;padding: 0 5px 5px 0" height="37">
						<tbody>
							<tr style="vertical-align: top">
								<td width="37" align="left" valign="middle" style="word-break: break-word;border-collapse: collapse !important;vertical-align: top">
									<a href="'.COMPANY_INSTAGRAM.'" title="Instagram" target="_blank">
										<img src="#server#/images/instagram.png" alt="Instagram" title="Instagram" width="32" style="outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: block;border: none;height: auto;line-height: 100%;max-width: 32px !important">
									</a>
								</td>
							</tr>
						</tbody>
					</table>
				';
			}
        }
		
		public function createMail()
		{
			$this->body_text = $this->message[0]."/n/n".$this->message[1];
			
			// Fill template with data
			$this->body_html = file_get_contents($this->template);
			
			$this->body_html = str_replace("#facebook#",$this->facebook??"", $this->body_html);
			$this->body_html = str_replace("#twitter#",$this->twitter??"", $this->body_html);
			$this->body_html = str_replace("#instagram#",$this->instagram??"", $this->body_html);
			
			$this->body_html = str_replace("#title#",strtoupper("projectName"), $this->body_html);
			$this->body_html = str_replace("#color#","#b3c7d6", $this->body_html);
			$this->body_html = str_replace("#server#",$this->site_url, $this->body_html);
			$this->body_html = str_replace("#logo#",$this->logo, $this->body_html);
			//$this->body_html = str_replace("#hello#",$this->message[0], $this->body_html);
			$this->body_html = str_replace("#message#",$this->message[1], $this->body_html);
		}
		
		public function show()
		{
			$this->createMail();
			echo $this->body_html;
			return $this->result;
		}

		public function send()
		{
			$this->createMail();
			$mail = new PHPMailer;
			$mail->CharSet = 'utf-8';

			//$mail->SMTPDebug = 3;                               // Enable verbose debug output

			$mail->isSMTP();                                      // Set mailer to use SMTP
			$mail->Host = 'ssl0.ovh.net';  // Specify main and backup SMTP servers
			$mail->SMTPAuth = true;                               // Enable SMTP authentication
			$mail->Username = 'soluweb@solufile.be';                 // SMTP username
			$mail->Password = 'one.com1977';                           // SMTP password
			$mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
			$mail->Port = 465;                                    // TCP port to connect to
			$mail->setFrom("soluweb@solufile.be", "SOLUweb on SOLUX");

			$mail->setFrom($this->from, $this->fromName);
			//$mail->addAddress('laurent.anezo@gmail.com', 'Laurent Anezo');     // Add a recipient
			$cleanMails = str_replace(array(" ",",",";"),",",$this->to);
			$tabMails = explode(",",$cleanMails);
			foreach($tabMails as $tabMail){
				if (filter_var($tabMail, FILTER_VALIDATE_EMAIL)) {
					$mail->addAddress($tabMail);
				}
			}
			$mail->addReplyTo($this->reply, $this->replyName);
			if(!empty($this->cc)){$mail->addCC($this->cc);}
			if(!empty($this->bcc)){$mail->addBCC($this->bcc);}

			$mail->isHTML(true);                                  // Set email format to HTML

			$mail->Subject = $this->subject;
			$mail->Body    = $this->body_html;
			$mail->AltBody = $this->body_text;
			
			// Attachment
			if($this->attach != ""){
				$mail->addAttachment($this->attach);
			}

			if(!$mail->send()) {
				$this->result = false;
				$this->error = 'Message could not be sent :'.$mail->ErrorInfo;
			}else{
				$this->result = "ok";
			}
			
			return $this->result;
		}
	
		public function __destruct()
        {
        }
	}