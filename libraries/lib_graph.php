<?php
    class graph{
		public $data;
		public $error;
		public $label;
		
		private $color = array(
			'red'=>'rgb(255, 99, 132)',
			'orange'=>'rgb(255, 159, 64)',
			'yellow'=>'rgb(255, 205, 86)',
			'green'=>'rgb(75, 192, 192)',
			'blue'=>'rgb(54, 162, 235)',
			'purple'=>'rgb(153, 102, 255)',
			'grey'=>'rgb(201, 203, 207)'
		);
		private $axes = array(
			'month_short'=>array("Jan", "Fév", "Mar", "Avr", "Mai", "Juin", "Juil", "Août", "Sep", "Oct", "Nov", "Déc"),
			'month_long'=>array("Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"),
			'probality'=>array("20%", "40%", "60%", "80%")
		);

        public function __construct()
        {
        	$this->result = true;
			$this->error = "";

        }
		
		public function createMail()
		{
			$this->body_text = $this->message[0]."/n/n".$this->message[1];
			
			// Fill template with data
			$this->body_html = file_get_contents($this->template);
			
			$this->body_html = str_replace("#facebook#",$this->facebook, $this->body_html);
			$this->body_html = str_replace("#twitter#",$this->twitter, $this->body_html);
			$this->body_html = str_replace("#google#",$this->google, $this->body_html);
			
			$this->body_html = str_replace("#title#",strtoupper("LPDN"), $this->body_html);
			$this->body_html = str_replace("#color#","#b3c7d6", $this->body_html);
			$this->body_html = str_replace("#server#",$this->site_url, $this->body_html);
			$this->body_html = str_replace("#logo#","images/logo.png", $this->body_html);
			$this->body_html = str_replace("#hello#",$this->message[0], $this->body_html);
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
			$mail = new phpmailer;
			$mail->CharSet = 'utf-8';

			//$mail->SMTPDebug = 3;                               // Enable verbose debug output

			//$mail->isSMTP();                                      // Set mailer to use SMTP
			//$mail->Host = 'smtp1.example.com;smtp2.example.com';  // Specify main and backup SMTP servers
			//$mail->SMTPAuth = true;                               // Enable SMTP authentication
			//$mail->Username = 'user@example.com';                 // SMTP username
			//$mail->Password = 'secret';                           // SMTP password
			//$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
			//$mail->Port = 587;                                    // TCP port to connect to

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
			$mail->addCC($this->cc);
			$mail->addBCC($this->bcc);

			//$mail->addAttachment($attach);         // Add attachments
			//$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
			$mail->isHTML(true);                                  // Set email format to HTML

			$mail->Subject = $this->subject;
			$mail->Body    = $this->body_html;
			$mail->AltBody = $this->body_text;

			if(!$mail->send()) {
				$this->result = false;
				$this->error = 'Message could not be sent :'.$mail->ErrorInfo;
			}
			
			return $this->result;
		}
	
		public function __destruct()
        {
        }
	}