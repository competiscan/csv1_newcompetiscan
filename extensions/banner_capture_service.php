<?php
include_once '../includes/dbcon.php';
include_once '../includes/functions.php';
include_once '../includes/thumb.php';

$GLOBALS['DRW'] = $DRW;
$GLOBALS['DRW_read'] = $DRW_read;
$GLOBALS['DRW_main'] = $DRW_main;
$GLOBALS['DRW_crm'] = $DRW_crm;

class AdCapture 
{
	private $capture_type;
	private $response;
	private $name_base;
	private $remote_IP;
	private $user_id;

	public function __construct() {
		
		if (isset($_POST['authentication_hash'])) {
			if (!$this->authenticate($_POST['authentication_hash']))
				exit;
		} else {
			//exit;
		}

		//
		if (isset($_POST['url']) && ($_POST['url'] != "")) {
			$this->capture_type = "banner_ad";
		} elseif (isset($GLOBALS["HTTP_RAW_POST_DATA"])) {
			$this->capture_type = "screenshot";
		}
		//
		$this->remote_IP = $_SERVER['REMOTE_ADDR'];
	}

	//eg.. http://google.com, google.com, www.google.com, http://www.google.com --> google
	private function getCleanDomainName($unclean) {
		global $DRW,$DRW_read,$DRW_main,$DRW_crm;
		$ad_hosturl     = $DRW->real_escape_string($unclean);

		$url_arr = explode("//",$ad_hosturl);
		$temp = $url_arr[1];
		$url_arr = explode("/", $temp);
		$temp = $url_arr[0];
		$url_arr = explode(".",$temp);
		$url_hostname = $url_arr[sizeof($url_arr) - 2];

		return $url_hostname;
	}

	/* authenticates against username/password? or with a key generated at the download moment? or both? */
	private function authenticate($authentication_hash) {
		global $DRW,$DRW_read,$DRW_main,$DRW_crm;
		//lookup the hash in the db, see if you can find a user. if so, attribute further requests to that user. if not, return false
		//depends on the delivery method
		$res = $DRW->query("SELECT user_id FROM ffox_capture_auth WHERE auth_key='".$DRW->real_escape_string($authentication_hash)."'",$DRW_read);
		if ($DRW->num_rows($res) == 0) {
			return false;
		}
		$result_array = $DRW->fetch_array($res);
		$this->user_id = $result_array['user_id'];
		return true;
	}
	
	/* no longer in use -- port this over to the approve process */
	private function getSite($full_url) {
		global $DRW,$DRW_read,$DRW_main,$DRW_crm;
		$return_array = array();

		$url_arr = explode(".",$full_url);
		$site_url = "";
		for ($i = 0; $i < sizeof($url_arr); $i++)
		{
			if ($i == (sizeof($url_arr) - 1)) {
				$site_url .= substr($url_arr[$i], 0, 3);
			}  else {
				$site_url .= $url_arr[$i].".";
			}
		}

		$res = $DRW->query("SELECT sites_id, sites_name from cscan_sites where sites_url='$site_url' or sites_url='$site_url/'",$DRW_read);
		if ($DRW->num_rows($res) == 0) {
			$site_name = $this->getCleanDomainName($site_url);
			$DRW->query("INSERT into cscan_sites (sites_name, sites_category_id, sites_url, sites_active) values('$site_name',0,'$site_url',1)",$DRW_main);
			$return_array['id'] = $DRW->insert_id($DRW_main);
			$return_array['name'] = $site_name;
		} else {
			$data = $DRW->fetch_row($res);
			$return_array['id'] = $data[0];
			$return_array['name'] = $data[1];
		}

		return $return_array;
	}

	private function captureBannerAd() {
		global $DRW,$DRW_read,$DRW_main,$DRW_crm;
		$full_url = trim($_POST['url']);
		$ad_src		= trim($_POST['banner_src']);
		$url_hostname 	= $this->getCleanDomainName($full_url);
		
		if(!preg_match('/^https?:\\/\\//i',$ad_src)){
			$temp_url = parse_url($full_url);
			if(strpos($ad_src,'/')!==0){
				if(strrpos($temp_url['path'],'/')!==(strlen($temp_url['path'])-1)){
					$ad_src = '/'.$ad_src;
				}
				$ad_src = $temp_url['path'].$ad_src;
			}
			$ad_src = 'http://'.$temp_url['host'].$ad_src;
		}
		
		$parsed_ad_url = parse_url($ad_src);
		$ad_url_path = $parsed_ad_url['path'];
		$parsed_ad_url_path = pathinfo($ad_url_path);
		$ad_type = $parsed_ad_url_path['extension'];

		$valid_types = array("swf","gif","jpg","png");
		if (in_array($ad_type, $valid_types)) {
			$ad_src_filename = "banner_ad.".$ad_type;
		} else {
		   	list($width, $height, $type, $attr) = getimagesize($ad_src);
			$ad_src_filename = "banner_ad";
			switch($type) {
				case 1:
				$ad_src_filename .= ".gif";
				break;

				case 2:
				$ad_src_filename .= ".jpg";
				break;

				case 3:
				$ad_src_filename .= ".png";
				break;

				case 4:
				$ad_src_filename .= ".swf";
				break;

				default:
				$ad_src_filename .= ".swf";
				break;
			}
		}

		$datetime = date('Y:m:d H:i:s');
		$ip = $this->remote_IP;
		$user_agent = $_SERVER['HTTP_USER_AGENT'];

		$query = "INSERT INTO cscan_observation (date_observed, user_agent_string, status, ip, ad_name, ad_src_url, site_url, simple_domain) 
			VALUES ('$datetime', '$user_agent', 1, '$ip', '".$DRW->real_escape_string($ad_src_filename)."', '".$DRW->real_escape_string($ad_src)."', '".$DRW->real_escape_string($full_url)."', '".$DRW->real_escape_string($url_hostname)."')";
		$DRW->query($query,$DRW_main);
		$new_id = $DRW->insert_id($DRW_main);

		if (!is_dir($this->name_base.$new_id))
			shell_exec("mkdir ".$this->name_base.$new_id);
		$bannerSaveName = $this->name_base.$new_id."/$ad_src_filename";
		$debug1 = system("wget -q -O \"$bannerSaveName\" ".escapeshellarg($ad_src),$status);
		$modified_name_base = substr($this->name_base,3);
		$DRW->query("UPDATE cscan_observation set local_path='".$modified_name_base.$new_id."/' where observationID=$new_id",$DRW_main);
		$this->response = "ad_src: $ad_src";
	}

	private function captureScreenshot() {
		global $DRW,$DRW_read,$DRW_main,$DRW_crm;
		$new_id_row = $DRW->fetch_row($DRW->query("select observationID from cscan_observation order by observationID desc limit 1",$DRW_read));
		$new_id = $new_id_row[0];

		$screenshot     = $GLOBALS["HTTP_RAW_POST_DATA"];
		$screenshot     = substr($screenshot, strpos($screenshot,",") + 1);
		$decodedImage   = base64_decode($screenshot);

		$screenSaveName = "screenshot.png";
		$pdfSaveName 	= "screen_pdf.pdf";
		//$fp = fopen("./$screenSaveName","w");
		//fwrite($fp, $decodedImage);
		//fclose($fp);

		$GLOBALS['AUTH_DATA']['userID'] = 0;
		//createPreviewJPG("extensions/", "screen_pdf.pdf",$new_id);
		$destination = $this->name_base."$new_id/";
		//shell_exec("mv ./$pdfSaveName $destination"); //PDF
		//shell_exec("mv ./$screenSaveName $destination"); //Original Screenshot
		$fp = fopen($destination.$screenSaveName,"w");
		fwrite($fp, $decodedImage);
		fclose($fp);
		//shell_exec("mv ./$new_id"."0.jpg $destination"); //PDF preview image

		$this->response = "Banner and Screenshot Saved (Competiscan observations folder)";
	}

	//add a debug method that can be called from the constructor..will allow the plugin to supply error feedback during testing
		
	public function getResponse() {
                return $this->response;
        }

        public function captureData() {
		if (!is_dir("../PDF/".date("Y"))) {
                        shell_exec ("mkdir ../PDF/".date("Y"));
		}
		if (!is_dir("../observations/".date("Y"))) {
			shell_exec ("mkdir ../observations/".date("Y"));
		}
		//
                if (!is_dir("../PDF/".date("Y")."/".date("m"))) {
                        shell_exec("mkdir ../PDF/".date("Y")."/".date("m"));
		}
		if (!is_dir("../observations/".date("Y")."/".date("m"))) {
			shell_exec("mkdir ../observations/".date("Y")."/".date("m"));
		}
		$this->name_base = "../observations/".date("Y")."/".date("m")."/";

                if ($this->capture_type == "banner_ad") {
                        $this->captureBannerAd();
                } elseif ($this->capture_type == "screenshot") {
                        $this->captureScreenshot();
                }
        }
}

$user_agent = $_SERVER['HTTP_USER_AGENT'];
if(preg_match('/Firefox/i',$user_agent))
{
	$capturer = new AdCapture();
	$capturer->captureData();
	echo $capturer->getResponse();
}
?>