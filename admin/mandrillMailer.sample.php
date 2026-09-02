<?php

class MandrillMailer 
{
	private $api_key;
	private $endpoint_url;
	private $endpoint_call;
	private $to_address_array;
	private $template;
	private $subaccount;
	private $signing_domain;
	private $attachments = array();

	public function __construct() {
		$this->api_key = "api_key";
                $this->endpoint_url = "https://mandrillapp.com/api/1.0/";
                $this->endpoint_call = "/messages/send-template.json";
                $this->subaccount = "competiscan";
                $this->signing_domain = "competiscan.com";
		$this->clean();
	}

	public function clean() { //right now, all this does is reset the "to" addresses
                $this->to_address_array = array();
	}

	public function setTemplate($template_name) {
		$this->template = $template_name;
	}

	public function addToAddress($to_address) { //eg: array("email" => "mhollingshead@highlandsolutions.com", "name" => "Max")
		$this->to_address_array[] = $to_address;
	}

	public function addAttachment($type, $name, $content) {
		$attach = array(
			"type" => $type,
			"name" => $name,
			"content" => base64_encode($content),
		);
		$this->attachments[] = $attach;
	}

	public function send($placeholder_vars = array()) {
		if ($this->template == "none" || empty($this->template))
			return;

		$request = array(
			"key" => $this->api_key,
			"template_name" => $this->template,
			"template_content" => array(), //forgot what this is for
			"message" => array(
				"to" => $this->to_address_array,
				"global_merge_vars" => $placeholder_vars, //eg: array("name" => "USER_FNAME", "content" => $firstName)
				"subaccount" => $this->subaccount,
				"signing_domain" => $this->signing_domain,
			),
		);
		if(count($this->attachments)>0){
			$request["message"]["attachments"] = $this->attachments;
		}

        	$ch = curl_init();
	        curl_setopt($ch, CURLOPT_URL, $this->endpoint_url.$this->endpoint_call);
        	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	        curl_setopt($ch, CURLOPT_POST, true); //all mandril API calls are POST'ed
        	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($request));

	        $response = curl_exec($ch);
        	curl_close($ch);
		//
        	$ret = json_decode($response, true);
	        if (!in_array($ret['status'],array('sent','queued'))) {
        	        //how do I report errors (even thought this will probably never happen.. mandrill will queue up failed sends & try again)
        	}
	}
}

?>
