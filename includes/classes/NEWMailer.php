<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class NEWMailer extends PHPMailer {

	// Set default from variables
	public $From     = MAIL_FROM;
	public $FromName = MAIL_FROM_NAME;

	// SMTP configuration
	public $Host	= MAIL_HOST;
	public $Port 	= MAIL_PORT;
	public $SMTPAuth 	= true;		// SMTP requires authentication
	public $SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
	public $Username = MAIL_USER;
    public $Password = MAIL_PSWD;
    
    #public $SMTPDebug = SMTP::DEBUG_SERVER; // debug
    
    // construct
    public function __construct() {

        parent::__construct(true);
        $this->IsSMTP();
		$this->CharSet = "UTF-8";
		$this->isHTML(true); 

    }

	// mail for login
	public function send_mail_login($to_email, $subject, $template, $content) {

		# add addresses
		if(VENDOR_EMAIL_MODE==VENDOR_EMAIL_TEST) {
			$this->AddAddress("ramirozm@gmail.com");
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
			return false;
		}

		$content_search = $content['search'];
		$content_replace = $content['replace'];

		if(is_array($content_search) && count($content_search)>0 && is_array($content_replace) && count($content_replace)>0) {
			$htmlBody = str_replace($content_search, $content_replace, $htmlBody);
		}

		// set the html mail body
		$this->Body = $htmlBody;
		
		// send
        return $this->process_mail();

	}

    // mail for new payment notification
    public function vendors_notify_pos($vendors_emails, $project_title) {

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
			return false;
		}

		$content_search = array('SITE_URL', 'COMPANY', 'PROJECT');
		$content_replace = array(SITE_URL, $global_company['nombre'], $project_title);
		$htmlBody = str_replace($content_search, $content_replace, $htmlBody);

		// set the html mail body
		$this->Body = $htmlBody;
		
		// Send and verify
		return $this->process_mail();

    }
    

    // mail for po payed notification
    public function vendors_notify_payed($email_to, $project_title) {

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
            return false;
		}

        $content_search = array('SITE_URL', 'COMPANY', 'PROJECT');
		$content_replace = array(SITE_URL, $global_company['nombre'], $project_title);
		$htmlBody = str_replace($content_search, $content_replace, $htmlBody);

		// set the html mail body
		$this->Body = $htmlBody;
		
		// Send and verify
		return $this->process_mail();

    }
    

    // mail for payment complement reminder
    public function vendors_reminder_comp($vendors_emails) {

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
		$this->Subject = "Sube tu Complemento de Pago";

        // Load mail template & replace content
        $template = PATH_MAILS."mail.reminder.comp.html";
		if(file_exists($template) && is_file($template)) {
			$fp = fopen($template, "r");
			$htmlBody = fread($fp, filesize($template));
			fclose($fp);
		} else {
			return false;
		}

		$content_search = array('SITE_URL', 'COMPANY');
		$content_replace = array(SITE_URL, $global_company['nombre']);
		$htmlBody = str_replace($content_search, $content_replace, $htmlBody);

		// set the html mail body
		$this->Body = $htmlBody;
		
		// Send and verify
		return $this->process_mail();

    }
    

    // mail for new contract notification
    public function vendors_notify_contract($vendors_emails, $project_title) {

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
		$this->Subject = "Firma tu contrato del proyecto $project_title";

        // Load mail template & replace content
        $template = PATH_MAILS."mail.contract.notice.html";
		if(file_exists($template) && is_file($template)) {
			$fp = fopen($template, "r");
			$htmlBody = fread($fp, filesize($template));
			fclose($fp);
		} else {
			return false;
		}

		$content_search = array('SITE_URL', 'COMPANY', 'PROJECT');
		$content_replace = array(SITE_URL, $global_company['nombre'], $project_title);
		$htmlBody = str_replace($content_search, $content_replace, $htmlBody);

		// set the html mail body
		$this->Body = $htmlBody;
		
		// Send and verify
		return $this->process_mail();

    }
    

    // mail for contract rejected notification
    public function vendors_notify_contract_rejected($vendor_email, $project_title) {

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
            return false;
		}

        $content_search = array('SITE_URL', 'COMPANY', 'PROJECT');
		$content_replace = array(SITE_URL, $global_company['nombre'], $project_title);
		$htmlBody = str_replace($content_search, $content_replace, $htmlBody);

		// set the html mail body
		$this->Body = $htmlBody;
		
		// Send and verify
		return $this->process_mail();

    }
    

    // mail to vendors
    public function vendors_message($vendors_emails) {

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
			return false;
		}

        $content_search = array('SITE_URL', 'COMPANY');
		$content_replace = array(SITE_URL, $global_company['nombre']);
		$htmlBody = str_replace($content_search, $content_replace, $htmlBody);

		// set the html mail body
		$this->Body = $htmlBody;
		
		// Send and verify
        return $this->process_mail();

    }


    // send or display mail
	public function process_mail() {

		if(VENDOR_EMAIL_MODE==VENDOR_EMAIL_BYPASS) {
			set_alert("warning", "Email bypassed - test mode enabled.");
		} elseif(VENDOR_EMAIL_MODE==VENDOR_EMAIL_DISPLAY) {
			print "<br>Mail from: $this->From &lt;$this->FromName&gt;<br>";
			print "Mail to: <br>";
            print_r($this->getToAddresses());
            $ccs = $this->getBccAddresses();
            if(count($ccs)>0) {
                print "<br>CCs: <br>";
                print_r($ccs);
            }
            print "<br>Subject: $this->Subject<br>";
			print $this->Body."<br>";
        } else {
			if(!$this->Send()) {
				error_log("Mailer Error: " . $this->ErrorInfo);
				return false;
			}
        }

		return true;

    }
    
}


?>