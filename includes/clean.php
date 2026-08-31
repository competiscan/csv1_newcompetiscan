<?php
$jarray = array('onload','onunload','onclick','ondblclick','onmousedown','onmouseup','onmouseover','onmousemove','onmouseout','onfocus','onblur','onkeypress','onkeydown','onkeyup','onsubmit','onreset','onselect','onchange');
function cleanHTML($in,$dotargets=false){
	$out = preg_replace('/<script.+?<\\/script>/is','',$in); //remove any <script
	$out = preg_replace('/(\'|")\\s*javascript\\s*:.+?(\\1)/is','"#"',$out); //remove any javascript: in href
	$out = preg_replace('/href\\s*=\\s*javascript:.+?>/is','href="#">',$out); //remove any javascript: in href
	foreach($GLOBALS['jarray'] as $check){
		/*//old
		$out = preg_replace('/(\\s+|\'|")'.$check.'(\\s*=.+?>)/is','$1>',$out);
		*/
		$out = preg_replace('/(\\s+|\'|")'.$check.'\\s*=\\s*("|\'|\\b).+?\\2/is','$1',$out);
	}
	/*
	$out = preg_replace('/src\\s*=\\s*"?([^\\?]+?)\\?.*?>/is','src="$1">',$out); //remove any src with ?
	*/
	if($dotargets){
		if(preg_match_all('/(<a\\s+[^>]*>)/is',$out,$matches,PREG_SET_ORDER)){
			for($i=0;$i<count($matches);$i++){
				for($j=1;$j<count($matches[$i]);$j++){
					if(!preg_match('/\\btarget\\s*=/is',$matches[$i][$j])){
						$out = str_replace($matches[$i][$j],substr($matches[$i][$j],0,-1)." target=\"_blank\">",$out);
					}
				}
			}
		}
	}
	return $out;
}
function emailDecode($data,$etype=5){
	if($etype==3 || $etype==4) {
		$data = preg_replace('/(\\r?\\n|\\r)/','',$data);
		if($etype==3) {
			return base64_decode($data);
		}
		elseif($etype==4) {
			return quoted_printable_decode($data);
		}
	}
	else {
		return $data;
	}
}
?>