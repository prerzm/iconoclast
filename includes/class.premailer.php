<?php

/** RZM PHP Framework **/

# includes
require("phpmailer/class.phpmailer.php");
require("phpmailer/class.smtp.php");

class PREMailer extends phpmailer {

	// Set default variables for all new objects
	public $From     = MAIL_FROM;
	public $FromName = MAIL_FROM_NAME;

	// SMTP configuration
	public $Host	= MAIL_HOST;
	public $Port 	= MAIL_PORT;
	public $SMTPAuth 	= true;		// SMTP requires authentication
	public $SMTPSecure	= "ssl";
	public $Username = MAIL_USER;
    public $Password = MAIL_PSWD;
    
    // construct
    public function __construct() {

        $this->IsSMTP();
		$this->CharSet = "UTF-8";

    }

	// Send mail with print option
	public function send_mail_login($to_email, $subject, $template, $content, $debug=false) {

		global $global_company;
	
		# add addresses
		if(VENDOR_EMAIL_MODE==VENDOR_EMAIL_TEST) {
			$this->AddAddress($global_company['email']);
		} else {
			$this->AddAddress($to_email);
		}

		# subject
		$this->Subject = $subject;

		// Load mail template & replace content
		if(file_exists($template) && is_file($template)) {
			$fp = fopen($template, "r");
			$htmlBody = fread($fp, filesize($template));
			fclose($fp);
		} else {
			$htmlBody = "<!-- template not found: $template -->";
			$debug = true;
		}

		$content_search = $content['search'];
		$content_replace = $content['replace'];

		if(is_array($content_search) && count($content_search)>0 && is_array($content_replace) && count($content_replace)>0) {
			$htmlBody = str_replace($content_search, $content_replace, $htmlBody);
		}

		// set the html mail body
		$this->MsgHTML($htmlBody);
		
		// Send and verify
		if(VENDOR_EMAIL_MODE==VENDOR_EMAIL_DISPLAY || $debug==true) {

			print "<br><br><br>";
			print "Mail from: $this->From &lt;$this->FromName&gt;<br>";
			print "Mail to: $to_email<br>Subject: $subject\n<br><br>";
			print $htmlBody;
			print "<br><br>";
			return true;

		} else {

			if(!$this->Send()) {
				error_log("Mailer Error: " . $this->ErrorInfo);
				return false;
			} else {
				return true;
			}

		}

	}

    public function vendors_notify_pos($vendors_emails, $project_title, $debug=false) {

		global $global_company;

        if(!is_array($vendors_emails) || count($vendors_emails)==0) {
            return false;
        }
    
		# add addresses
		if(VENDOR_EMAIL_MODE==VENDOR_EMAIL_TEST) {
			$this->AddAddress($global_company['email']);
		} else {
			$this->AddAddress($global_company['email']);
			foreach($vendors_emails as $email) {
				$this->AddBCC($email);
			}
		}

		# subject
		$this->Subject = "Sube tu factura del proyecto $project_title";

        // Load mail template & replace content
        $template = PATH_MAILS."mail.notice.".session_get_data("companyId").".html";
		if(file_exists($template) && is_file($template)) {
			$fp = fopen($template, "r");
			$htmlBody = fread($fp, filesize($template));
			fclose($fp);
		} else {
			set_alert("error", "Archivo no encontrado");
			return false;
		}

        $content_search = array('SITE_URL', 'COMPANY', 'PROJECT');
		$content_replace = array(SITE_URL, $global_company['nombre'], $project_title);
		$htmlBody = str_replace($content_search, $content_replace, $htmlBody);

		// set the html mail body
		$this->MsgHTML($htmlBody);
		
		// Send and verify
		if(VENDOR_EMAIL_MODE==VENDOR_EMAIL_DISPLAY || $debug==true) {

			print "<br><br><br>";
			print "Mail from: $this->From &lt;$this->FromName&gt;<br>";
			print "Mail to: ".$global_company['email']."<br>Subject: Nuevo pago disponible\n<br><br>";
			print "Bccs: ";
			var_dump($this->bcc);
			print "<br>";
			print $htmlBody;
			print "<br><br>";
			return true;

		} else {

			if(!$this->Send()) {
				error_log("Mailer Error: " . $this->ErrorInfo);
				return false;
			} else {
				return true;
			}

		}

    }
    
    public function vendors_notify_payed($email_to, $project_title, $debug=false) {

		global $global_company;

		# add addresses
		if(VENDOR_EMAIL_MODE==VENDOR_EMAIL_TEST) {
			$this->AddAddress($global_company['email']);
		} else {
			$this->AddAddress($email_to);
		}

		# subject
		$this->Subject = "Pago realizado";

        // Load mail template & replace content
        $template = PATH_MAILS."mail.payment.html";
		if(file_exists($template) && is_file($template)) {
			$fp = fopen($template, "r");
			$htmlBody = fread($fp, filesize($template));
			fclose($fp);
		} else {
			$htmlBody = "Template not found: $template";
			$debug = true;
		}

        $content_search = array('SITE_URL', 'COMPANY', 'PROJECT');
		$content_replace = array(SITE_URL, $global_company['nombre'], $project_title);
		$htmlBody = str_replace($content_search, $content_replace, $htmlBody);

		// set the html mail body
		$this->MsgHTML($htmlBody);
		
		// Send and verify
		if(VENDOR_EMAIL_MODE==VENDOR_EMAIL_DISPLAY || $debug==true) {

			print "<br><br><br>";
			print "Mail from: $this->From &lt;$this->FromName&gt;<br>";
			print "Mail to: $email_to<br>Subject: Pago realizado\n<br><br>";
			print $htmlBody;
			print "<br><br>";
			return true;

		} else {

			if(!$this->Send()) {
				error_log("Mailer Error: " . $this->ErrorInfo);
				return false;
			} else {
				return true;
			}

		}

    }
    
    public function vendors_notify_contract_rejected($vendor_email, $project_title, $debug=false) {

		global $global_company;

		# add addresses
		if(VENDOR_EMAIL_MODE==VENDOR_EMAIL_TEST) {
			$this->AddAddress($global_company['email']);
		} else {
			$this->AddAddress($vendor_email);
		}

		# subject
		$this->Subject = "Contrato de $project_title rechazado";

        // Load mail template & replace content
        $template = PATH_MAILS."mail.contract.rejected.html";
		if(file_exists($template) && is_file($template)) {
			$fp = fopen($template, "r");
			$htmlBody = fread($fp, filesize($template));
			fclose($fp);
		} else {
			$htmlBody = "Template not found: $template";
			$debug = true;
		}

        $content_search = array('SITE_URL', 'COMPANY', 'PROJECT');
		$content_replace = array(SITE_URL, $global_company['nombre'], $project_title);
		$htmlBody = str_replace($content_search, $content_replace, $htmlBody);

		// set the html mail body
		$this->MsgHTML($htmlBody);
		
		// Send and verify
		if(VENDOR_EMAIL_MODE==VENDOR_EMAIL_DISPLAY || $debug==true) {

			print "<br><br><br>";
			print "Mail from: $this->From &lt;$this->FromName&gt;<br>";
			print "Mail to: ".$global_company['email']."<br>Subject: Nuevo pago disponible\n<br><br>";
			print "Bccs: ";
			var_dump($this->bcc);
			print "<br>";
			print $htmlBody;
			print "<br><br>";
			return true;

		} else {

			if(!$this->Send()) {
				error_log("Mailer Error: " . $this->ErrorInfo);
				return false;
			} else {
				return true;
			}

		}

    }
    
    public function vendors_message($vendors_emails, $debug=false) {

		global $global_company;

        if(!is_array($vendors_emails) || count($vendors_emails)==0) {
            return false;
        }
    
		# add addresses
		if(VENDOR_EMAIL_MODE==VENDOR_EMAIL_TEST) {
			$this->AddAddress($global_company['email']);
		} else {
			$this->AddAddress($global_company['email']);
			foreach($vendors_emails as $email) {
				$this->AddBCC($email);
			}
		}

		# subject
		$this->Subject = "Aviso a Proveedores";

        // Load mail template & replace content
        $template = PATH_MAILS."mail.message.html";
		if(file_exists($template) && is_file($template)) {
			$fp = fopen($template, "r");
			$htmlBody = fread($fp, filesize($template));
			fclose($fp);
		} else {
			$htmlBody = "Template not found: $template";
			$debug = true;
		}

        $content_search = array('SITE_URL', 'COMPANY');
		$content_replace = array(SITE_URL, $global_company['nombre']);
		$htmlBody = str_replace($content_search, $content_replace, $htmlBody);

		// set the html mail body
		$this->MsgHTML($htmlBody);
		
		// Send and verify
		if(VENDOR_EMAIL_MODE==VENDOR_EMAIL_DISPLAY || $debug==true) {

			print "<br><br><br>";
			print "Mail from: $this->From &lt;$this->FromName&gt;<br>";
			print "Mail to: ".$global_company['email']."<br>Subject: Aviso a Proveedores\n<br><br>";
			print "Bccs: ";
			var_dump($this->bcc);
			print "<br>";
			print $htmlBody;
			print "<br><br>";
			return true;

		} else {

			if(!$this->Send()) {
				error_log("Mailer Error: " . $this->ErrorInfo);
				return false;
			} else {
				return true;
			}

		}

    }
    
}


?>