<?php
namespace Util;

class Mail 
{
	private $version='1.0';
	private $to;	
	private $cc;
	private $bcc;
	private $from='';
	private $sendmailFrom='';
	private $replyTo='';
	private $textBody='';
	private $htmlBody='';
	private $subject='';
	private $attachments;
	private $headers;
	private $contentType;
	private $boundary='';
	private $boundaryPart='';
	private $sentSuccess;
	private $encoding='"UTF-8"';
	private $eol="\n";
	private $multipartMessage=false;
	private $htmlTemplate='';
	private $htmlTemplateTag='[%-CONTENT-%]';
	private $driver = 'local';
	private $valid_drivers = array('local', 'mandrill');
		
	public function __construct()
	{
		$this->initProperties();	
	}
	
	private function initProperties()
	{
		$this->to=array();
		$this->cc=array();
		$this->bcc=array();
		$this->attachments=array();
		$this->headers=array();
		$this->addHeader('Date',date('D, j M Y H:i:s O'));
		$this->addHeader('MIME-Version','1.0');
		$this->addHeader('X-Mailer','IronClad Mailer Version '.$this->version.' using PHP Version '.phpversion());		
		$this->boundarySection='MIME_Boundary_Section_'.md5(time());
		$this->boundary='MIME_Boundary_'.md5(time());		
		if(defined('DEFAULT_MAIL_DRIVER') AND in_array(DEFAULT_MAIL_DRIVER, $this->valid_drivers)){
			$this->driver = DEFAULT_MAIL_DRIVER;
		}
	}
		
	public function parseHeaders()
	{
		/**
		 * Do not add To list to headers because mail function will add them automatically
		 */
		$this->to=$this->parseRecipient("TO:");
		$this->cc=$this->parseRecipient("CC:");
		$this->bcc=$this->parseRecipient("BCC:");	
		if ($this->cc!="") {$this->headers[]="CC: ".$this->cc;}
		if ($this->bcc!="") {$this->headers[]="BCC: ".$this->bcc;}
	    if ($this->from!="") {$this->headers[]="From: ".$this->from;}
	    
		if ($this->replyTo!="") {
			$this->headers[]="Reply-To: ".$this->replyTo;
		} else if ($this->from!="") {
			$this->headers[]="Reply-To: ".$this->from;
		}
		
		if (is_array($this->attachments) && count($this->attachments)>0) {
			$this->contentType='multipart/mixed; boundary="'.$this->boundarySection.'"';
			$this->multipartMessage=true;						
		} else if ($this->htmlBody!='' && $this->textBody!='') {
			$this->multipartMessage=true;
			$this->contentType='multipart/alternative; boundary="'.$this->boundary.'"';
		} else if ($this->htmlBody!='') {
			$this->multipartMessage=false;
			$this->contentType='text/html; charset='.$this->encoding.$this->eol;
		} else if ($this->textBody!='') {
			$this->multipartMessage=false;
			$this->contentType='text/plain; charset='.$this->encoding.$this->eol;
		}
		$this->headers[]="Content-Type: ".$this->contentType;
	}
		
	
	public function send()
	{
		$this->parseHeaders();
		$headers=$this->getHeaders();
		$subject=$this->subject;
		$to=$this->to;
		$body=$this->prepareBody();	

		return $this->sendMail($to,$subject,$body,$headers);		
	}
	
	private function sendMail($to, $subject, $body, $headers)
	{
		//using PHP's built in mail() function
		//note added -f parameter to set from at command line level
		//will add functionality to use SMTP server through sockets instead of mail function
		//for performance reasons
		
		
		//if debug mode set, save mail to local log instead of actually sending
		if (defined('DEBUG_EMAIL_LOG') AND strlen(DEBUG_EMAIL_LOG)) {
			$implodeHeader = $headers;
			if(is_array($headers)){
				$implodeHeader = join("\n", $implodeHeader);
			}
			// don't send mail - just log it to the debug file
			$debug_text = 
				'- '.date("Y-m-d H:i:s")."\n".str_repeat('-', 60)."\n".
				"To: $to\nSubject: $subject\nFrom: {$this->sendmailFrom}\n".$implodeHeader."\n\n$body\n";
			$fd = fopen(DEBUG_EMAIL_LOG, 'a');
			fwrite($fd, $debug_text."\n");
			fclose($fd);
			return true;
		}
		
		$send_mail = false;
		switch($this->driver){
			case 'local':
				$send_mail = mail($to,$subject,$body,$headers,"-f ".$this->sendmailFrom);
				break;
			case 'mandrill':
				$mandrill = new \API\Mandrill(MANDRILL_API_KEY);
				if(!is_array($to)){
					$to = array(array('email' => $to, 'type' => 'to', 'name' => $to));
				}
				$data = array();
				$data['subject'] = $subject;
				$data['from_email'] = $this->sendmailFrom;
				$data['html'] = $body;
				$data['to'] = $to;
				//$data['headers'] = $headers;
				//$data['merge_vars'] = array();
				try{
					$send_mail = $mandrill->messages->send($data, false);
				}
				catch(\Exception $e){
					$send_mail = false;
				}
				break;
		}
		return $send_mail;
		
	}
	
	public function prepareBody()
	{
		$textMessage=$this->textPart($this->textBody);
		$htmlMessage=$this->htmlPart($this->htmlBody);
		$attachments=$this->prepareAttachments();
		$body='';
		if ($attachments!="") {
			$body.='--'.$this->boundarySection.$this->eol;
			$body.='Content-Type: multipart/alternative; boundary="'.$this->boundary.'"'.$this->eol.$this->eol;
		}
		$body.=$textMessage;
		$body.=$htmlMessage;
		if ($this->multipartMessage && $this->htmlBody!="" && $this->textBody!="") {
			$body.='--'.$this->boundary.'--'.$this->eol.$this->eol;
		}
		if ($attachments!="") {			
			$body.=$attachments;
			$body.='--'.$this->boundarySection.'--'.$this->eol.$this->eol;
		}		
		
		
		return $body;		
	}
	
	private function prepareAttachments()
	{
		$strAttachments='';
		if (is_array($this->attachments)) {
			foreach ($this->attachments as $atts) {
				$file=fopen($atts,'rb');
				$data=fread($file,filesize($atts));
				fclose($file);
				$data=chunk_split(base64_encode($data));
				$fileType=mime_content_type($atts);
				$fileInfo=pathinfo($atts);
				$strAttachments.='--'.$this->boundarySection.$this->eol;
				$strAttachments.='Content-Type: '.$fileType.'; name="'.$fileInfo['filename'].'.'.$fileInfo['extension'].'"'.$this->eol;
				$strAttachments.='Content-Disposition: attachment;'.$this->eol;								
				$strAttachments.='Content-Transfer-Encoding: base64'.$this->eol.$this->eol;
				$strAttachments.=$data.$this->eol.$this->eol;
			}
		}	
		return $strAttachments;
	}
		
	private function textPart($msg) 
	{
		if ($msg!="") {
			if ($this->multipartMessage) {
				$str = '--'.$this->boundary.$this->eol;
				$str.= 'Content-Type: text/plain; charset='.$this->encoding.$this->eol;
				$str.= 'Content-Transfer-Encoding: 8bit'.$this->eol.$this->eol;
			}
			$str.=wordwrap($msg,70).$this->eol.$this->eol;
			return $str;
		} else {
			return '';
		}	
	}
	
	private function htmlPart($msg) 
	{
		$str='';
		if ($msg!="") {
			if ($this->multipartMessage) {
				$str = '--'.$this->boundary.$this->eol;
				$str.= 'Content-Type: text/html; charset='.$this->encoding.$this->eol;
				$str.= 'Content-Transfer-Encoding: 8bit'.$this->eol.$this->eol;
			}
			if ($this->htmlTemplate!='') {
				$msg=str_replace($this->htmlTemplateTag,$msg,$this->htmlTemplate);
			} 
			$str.=$msg.$this->eol.$this->eol;
			
			return $str;
		}	
	}
	
	public function addTo($email, $name='')
	{
		$this->addRecipient("TO:",$email,$name);	
	}
	
	public function addCC($email, $name='')
	{
		$this->addRecipient("CC:",$email,$name);
	}
	
	public function addBCC($email, $name='')
	{
		$this->addRecipient("BCC:",$email,$name);
	}
	
	private function addRecipient($type, $email, $name='')
	{
		if ($name!='') {
			switch ($type) {
				case "TO:":
					$this->to[]=$name.' <'.$this->clean($email).'>';
					break;
				case "CC:":
					$this->cc[]=$name.' <'.$this->clean($email).'>';
					break;
				case "BCC:":
					$this->bcc[]=$name.' <'.$this->clean($email).'>';
					break;				
			}			
		} else {
			switch ($type)
			{
				case "TO:":
					$this->to[]=$this->clean($email);
					break;
				case "CC:":
					$this->cc[]=$this->clean($email);
					break;
				case "BCC:":
					$this->bcc[]=$this->clean($email);
					break;
			}
			
		}
	}
	
	private function parseRecipient($type)
	{
		switch ($type) {
			case "TO:":
				$recipientList=$this->to;
				break;
			case "CC:":
				$recipientList=$this->cc;
				break;
			case "BCC:":
				$recipientList=$this->bcc;
				break;
		}
		
		if (is_array($recipientList)) {
			$recipientList=join(", ",$recipientList);
		}
		return $recipientList;
	}
	
	public function setSubject($subject)
	{
		$this->subject=$subject;
	}
	
	public function setFrom($email, $name='')
	{
		if ($name=='') {
			$this->from=$this->clean($email);
		} else {
			$this->from=$name.' <'.$this->clean($email).'>';
		}
		$this->sendmailFrom=$this->clean($email);

	}
	
	public function setReplyTo($email, $name='')
	{
		if ($name=='') {
			$this->replyTo=$this->clean($email);
		} else {
			$this->replyTo=$name.' <'.$this->clean($email).'>';
		}
	}

	public function addHeader($header,$value)
	{
		$this->headers[]=$this->clean($header).': '.$this->clean($value);
	}
	
	public function setHTML($msg)
	{
		$this->htmlBody=$msg;
	}
	
	public function setHTMLTemplate($template,$tag='[%-CONTENT-%]')
	{
		if ($tag!='') {
			$this->htmlTemplateTag=$tag;
		}
		$this->htmlTemplate=file_get_contents($template);
		
	}
	
	public function setText($msg)
	{
		$this->textBody=$msg;
	}
	
	public function addAttachment($file)
	{
		$this->attachments[]=$file;
	}	
	
	private function clean($txt)
	{
		$txt = str_replace("\n","",$txt);
		$txt = str_replace("\r","",$txt);
		$txt = str_replace("","",$txt);
		$txt = str_replace("\t","",$txt);
		return $txt;
	}	
	
	private function validEmail($email)
	{
		//return true for valid email or false for invalid email
		//use this when adding e-mail addresses to validate them - if they are invalid then set error
	}
	
	public function getHTML()
	{
		return $this->htmlBody;
	}
	
	public function getText()
	{
		return $this->textBody;
	}
	
	public function getHeaders()
	{
		return join($this->eol,$this->headers);
	}
	
	public function getSubject()
	{
		return $this->subject;
	}
	
	public function getTo()
	{
		return $this->to;
	}
	
	public function getDriver()
	{
		return $this->driver;
	}
	
	public function setDriver($driver = 'local')
	{
		if(!in_array($driver, $this->valid_drivers)){
			throw new \Exception($driver.' driver not supported');
		}
		$this->driver = $driver;
	}
	
} 
