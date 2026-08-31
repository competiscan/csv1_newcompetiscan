<?php
function getUserEmail($contentText, $muid=0, $ctype='text/html'){
  $matches = [];
  $returnContent = null;
  if(preg_match('/[a-zA-Z0-9]\S+@\S+[a-zA-Z]/si', $contentText, $matches, PREG_OFFSET_CAPTURE) && isset($matches[0][0])){
    $returnContent = $matches[0][0];
    if(preg_match('/(mailto:)(.+)(\")/xi', $matches[0][0], $newStr, PREG_OFFSET_CAPTURE)){
      $returnContent = $newStr[2][0];
    }
  }
  //~ echo stripos($returnContent,'-$-');
  //~ echo $returnContent;die;
  $position = stripos($returnContent,'-$-');
  if($position){
	 $returnContent = substr($returnContent, 0, $position);
  }
  return $returnContent;
}
function getTrimmedName($text, $muid=0, $ctype='text/html'){
  $foundFlag = false;
  $matches = [];
  $userNameText = $text;
  // if($muid == 10593237){
  //
  //   echo '---------------10593218HH---';
  //   print_r($text);die;
  // }
  $text = str_replace('From:', '', $text);
  if(preg_match('/(.+)((Subject:)|(Sent:)|(Date:)|(To:))/isU', $text, $matches, PREG_OFFSET_CAPTURE)){
    $userNameText = $matches[1][0];
  }
  return strip_tags($userNameText);
}

function getUserName($contentText, $muid=0, $ctype='text/html'){
  if(substr_count($contentText, ' via ')){
    $str = explode(' via ', $contentText);
    $contentText = $str[0];
  }
  return getTrimmedName($contentText, $muid, $ctype);
}
function checkDateContent($dateContent, $tag='br', $uid=0){
  $dateContent = htmlentities($dateContent);
  $tag = '/'.$tag;
  if(strlen($dateContent) > 80
    && (
        substr_count($dateContent, '&lt;'.$tag.'&gt;') ||
        substr_count($dateContent, '&lt;'.$tag.'&gt;')
      )
  ){
    return true;
  }
  return false;
}

function getDateFromText($content, $muid=0, $ctype='text/html'){
	$matches = [];
	$content = str_replace(['<br>', '<br />', '<br/>'], '-$-', $content);
	try{
	  if($ctype == 'text/html'){
	    $foundContent = null; $found = false; $divFlag = false;
	    if(preg_match('/((Sent:)|(Date:))(.+)<\/DIV>/isU', $content, $matches, PREG_OFFSET_CAPTURE)){
	        $foundContent = $matches[4][0];
	        $divFlag = checkDateContent($foundContent, 'div', $muid);
	        $found = true;
	    }
		if($divFlag){
	      $content = $foundContent;
	      $found = false;
	    }
	    if(!$found && preg_match('/((Sent:)|(Date:))(.+)(\-\$\-)/xi', $content, $matches, PREG_OFFSET_CAPTURE)){
	        $foundContent = $matches[4][0];
	        $found = true;
	    }
	    if(!$found && preg_match('/((Sent:)|(Date:))(.+)(\<tr)/xi', $content, $matches, PREG_OFFSET_CAPTURE)){
	        $foundContent = $matches[2][0];
	        $found = true;
	    }
      if(!$found && preg_match('/<td[^\/]+?(?:\".*?\"|\'.*?\'|.*?)>(.+?)<\/td>/isx', $content, $matches, PREG_OFFSET_CAPTURE)){
	        $foundContent = $matches[0][0];
	        $found = true;
	    }
      $divFlag = checkDateContent(strtolower($foundContent), 'td', $muid);
	    if($divFlag){
	      $newContent = explode('</td>', $foundContent);
        if(count($newContent) == 1){
          $newContent = explode('</Td>', $foundContent);
          if(count($newContent) == 1){
            $newContent = explode('</TD>', $foundContent);
          }
        }
	    }else{
	      $newContent = explode('-$-', $foundContent);
	    }
	    $divFlag1 = checkDateContent(strtolower($foundContent), 'div', $muid);
	    if($divFlag1){
	      $foundContent = str_replace(['</DIV>', '</Div>'], '</div>', $foundContent);
	      $newContent = explode('</div', $foundContent);
	    }else{
	      $newContent = explode('-$-', $foundContent);
	    }

	    if(count($newContent)){
	      $foundContent = $newContent[0];
	    }
	    $foundContent = strip_tags($foundContent);
	    if(substr_count($foundContent, 'Date Received:') && strlen($foundContent) > 50){
	      $dt = explode('Date Received:', $foundContent);
	    }
	    if(isset($dt[0])) $foundContent = $dt[0];
	  }else{
	    $found = false; $contentText = '';
	    if(preg_match('/((Date:)|(Sent:))(.+)(\>)/xi', $content, $matches, PREG_OFFSET_CAPTURE)){
	      $contentText = strip_tags($matches[2][0]);
	      $found = true;
	    }
	    $dt = explode('>', $contentText);

	    if(substr_count($contentText, 'Subject:') && isset($dt[0]) && strlen($dt[0]) > 50){
	      $dt = explode('Subject:', $contentText);
	    }
	    if(substr_count($contentText, 'Subj:') && isset($dt[0]) && strlen($dt[0]) > 50){
	      $dt = explode('Subj:', $contentText);
	    }
	    if(isset($dt[0])) $foundContent = $dt[0];
	  }
	  return $foundContent;
	}catch(\Exception $e){
		return null;
	}
}
function correctedText($text){
  if(substr_count($text, 'Sent:')){
    $str = explode('Sent:', $text);
    $text = $str[0];
  }
  if(substr_count($text, 'Date:')){
    $str = explode('Date:', $text);
    $text = $str[0];
  }
  if(substr_count($text, 'Subject:')){
    $str = explode('Subject:', $text);
    $text = $str[0];
  }
  if(substr_count($text, 'Subj:')){
    $str = explode('Subj:', $text);
    $text = $str[0];
  }
  if(substr_count($text, 'To:')){
    $str = explode('To:', $text);
    $text = $str[0];
  }
  if(substr_count($text, 'From:')){
    $str = explode('From:', $text);
    $text = $str[0];
  }

  return trim($text);
}
function returnThisEmail($text){
  if(preg_match("/([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}/si", $text, $match)){
	 return $match[0];
  }
  return $text;
}
function returnThisName($text){
  return (preg_match("/(--From)(.+)$/si", $text, $match)) ? $match[2][0] : $text;
}
function getNameBeforeBrackedText($name){
  if(substr_count($name, '<br>')){
    $str = explode('<br>', $name);
    $name = $str[0];
  }
  return $name;
}
function valid_email($str) {
  return (!preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $str)) ? FALSE : TRUE;
}

function removeSpecificTags($content){
  $content = str_replace('<br>', '-$-', $content);
	$content = str_replace('<br/>', '-$-', $content);

    if(preg_match_all('/(?<=\<strong\>)(\s*.*\s*)(?=\<\/strong\>)/iUs', $content, $matches, PREG_OFFSET_CAPTURE)){
		if(isset($matches[0])){
        for($i=0;$i<count($matches[0]);$i++) {
		  $content = str_replace('<strong>'.$matches[0][$i][0].'</strong>', $matches[0][$i][0], $content);
        }
      }
    }
    if(preg_match_all('/(?<=\<span\>)(\s*.*\s*)(?=\<\/span\>)/iUs', $content, $matches, PREG_OFFSET_CAPTURE)){
		if(isset($matches[0])){
        for($i=0;$i<count($matches[0]);$i++) {
		  $content = str_replace('<span>'.$matches[0][$i][0].'</span>', $matches[0][$i][0], $content);
        }
      }
    }
    if(preg_match_all('/(?<=\<font\>)(\s*.*\s*)(?=\<\/font\>)/iUs', $content, $matches, PREG_OFFSET_CAPTURE)){
		if(isset($matches[0])){
        for($i=0;$i<count($matches[0]);$i++) {
		  $content = str_replace('<font>'.$matches[0][$i][0].'</font>', $matches[0][$i][0], $content);
        }
      }
    }
    if(preg_match_all('/(?<=\<i\>)(\s*.*\s*)(?=\<\/i\>)/iUs', $content, $matches, PREG_OFFSET_CAPTURE)){
		if(isset($matches[0])){
        for($i=0;$i<count($matches[0]);$i++) {
		  $content = str_replace('<i>'.$matches[0][$i][0].'</i>', $matches[0][$i][0], $content);
        }
      }
    }
    if(preg_match_all('/(?<=\<style\>)(\s*.*\s*)(?=\<\/style\>)/iUs', $content, $matches, PREG_OFFSET_CAPTURE)){
		if(isset($matches[0])){
        for($i=0;$i<count($matches[0]);$i++) {
		  $content = str_replace('<style>'.$matches[0][$i][0].'</style>', $matches[0][$i][0], $content);
        }
      }
    }
    if(preg_match_all('/(?<=\<b\>)(\s*.*\s*)(?=\<\/b\>)/iUs', $content, $matches, PREG_OFFSET_CAPTURE)){
      if(isset($matches[0])){
        for($i=0;$i<count($matches[0]);$i++) {
		  $content = str_replace('<b>'.$matches[0][$i][0].'</b>', $matches[0][$i][0], $content);
        }
      }
    }
    //echo '--------';
    //echo $content;die;
    while(preg_match('/<b[^\/]+?(?:\".*?\"|\'.*?\'|.*?)>(.+?)<\/b>/isx', $content, $matches, PREG_OFFSET_CAPTURE)){
		$content = str_replace($matches[0][0], $matches[1][0], $content);
	}
	while(preg_match('/<span[^\/]+?(?:\".*?\"|\'.*?\'|.*?)>(.+?)<\/span>/isx', $content, $matches, PREG_OFFSET_CAPTURE)){
		$content = str_replace($matches[0][0], $matches[1][0], $content);
	}
	while(preg_match('/<font[^\/]+?(?:\".*?\"|\'.*?\'|.*?)>(.+?)<\/font>/isx', $content, $matches, PREG_OFFSET_CAPTURE)){
		$content = str_replace($matches[0][0], $matches[1][0], $content);
	}
	while(preg_match('/<strong[^\/]+?(?:\".*?\"|\'.*?\'|.*?)>(.+?)<\/strong>/isx', $content, $matches, PREG_OFFSET_CAPTURE)){
		$content = str_replace($matches[0][0], $matches[1][0], $content);
	}
    return $content;
}
function clean($string) {
   $string = str_replace(' ', '##', $string); // Replaces all spaces with hyphens.

   return preg_replace('/[^A-Za-z0-9\#\:\+\-\,\@\.]/', '', $string); // Removes special chars.
}
function tz_list() {
	$zones_array = array();
	$usTimezones = array(
		'EST' => 'Eastern Standard Time',
		'CST' => 'Central Standard Time',
		'MST' => 'Mountain Standard Time',
		'PST' => 'Pacific Standard Time',
		'AST' => 'Alaska Standard Time',
		'HAST' => 'Hawaii-Aleutian Standard Time'
	);
	$timestamp = time();
	foreach(timezone_identifiers_list() as $key => $zone) {
		date_default_timezone_set($zone);
		$zones_array[$key]['zone'] = $zone;
		$zones_array[$key]['short_code'] = strtoupper(date('T', $timestamp));
		$zones_array[$key]['replace_code'] = isset($usTimezones[$zones_array[$key]['short_code']])?$usTimezones[$zones_array[$key]['short_code']]:'';
		$zones_array[$key]['diff_from_GMT'] = 'UTC/GMT ' . date('P', $timestamp);
	}
	return $zones_array;
}
function cleanForDate($string) {
   $string = str_replace(' ', '##', $string); // Replaces all spaces with hyphens.
   $cleaned_string = preg_replace('/[^A-Za-z0-9\#\:\+\-\,\@\.\/]/', '', $string);// Removes special chars.
   $timezoneList = tz_list();
   if(substr_count($cleaned_string, ',') == 3){
	   $stringArray = explode(',', $cleaned_string);
	   $timezone_short_codes = [];
	   foreach($timezoneList as $timezone){
		    if(trim(preg_replace('/[^A-Za-z]/', '', $timezone['short_code'])) != ''){
				$timezone_short_codes[] = preg_replace('/[^A-Za-z]/', '', $timezone['short_code']);
			}
	   }
	   $unique_timezone_short_codes = array_values(array_unique($timezone_short_codes));
	   $lastString = trim(array_pop($stringArray));
	   //check for time string excact 8 characher
	   $time_string = substr($lastString, 0, 8);
	   $time_string = trim(preg_replace('/[^0-9\:]/', '', $time_string));
	   $time_check_flag = true;
	   if($time_check_flag && $time_string == ''){
		   $time_check_flag = false;
	   }
	   $timezone_string = substr($lastString, 0, 4);
	   $timezone_string = trim(preg_replace('/[^\#]/', ' ', $timezone_string));
	   if($time_check_flag && $timezone_string != ''){
		   if(!in_array(strtolower($timezone_string), $unique_timezone_short_codes)){
			   $timezone_string = false;
		   }
	   }
	   if(!$time_check_flag){
		   $cleaned_string = implode(',', $stringArray);
	   }
   }
   $cleaned_string = str_replace('##',' ',$cleaned_string);
   foreach($timezoneList as $timezone){
		if(trim($timezone['replace_code']) != ''){
			if(strpos($cleaned_string, $timezone['replace_code'])){
				$cleaned_string = str_replace($timezone['replace_code'],$timezone['short_code'],$cleaned_string);
			}
		}
   }
   return $cleaned_string;
}
function getFromText($content, $muid=0, $ctype='text/html'){
  $foundContent = $userEmail = $userName = null; $found = false;$str=[];$date=null;
  $content = str_replace("\n", '', $content);
  //replace &lt;
  $content = str_replace('&lt;', '<', $content);
  //replace &rt;
  $content = str_replace('&rt;', '>', $content);
  $content = str_replace('Sent from my', '####', $content);

  //striping <b> tag
  //striping <strong> tag
  $content = str_replace(['title='], '', $content);
  $content = removeSpecificTags($content);

	try{
    if(preg_match('/(From:)(.+)(\<\/[divDIV]{3}|[TRtr]{2})>/isU', $content, $matches, PREG_OFFSET_CAPTURE)){
		  $foundContent = $matches[2][0];
		  //echo '<pre>';print_r($foundContent);die;
		  $userEmail = getUserEmail($foundContent);
		  $userName = getUserName($foundContent, $muid,$ctype);
	  }
	  if($foundContent == null && $userEmail == null){

	    $userEmail = $content = substr($content, 6, 2000);
	    $userName = $date = null;

	    if(preg_match('/(\bOn\b)(.+)(\>)/Ui', $content, $matches, PREG_OFFSET_CAPTURE)){
	        $foundContent = $matches[2][0];
	        if(preg_match('/(.+)(\b(([amAM]{2})|([pmPM]{2})))/Us', $foundContent, $dt, PREG_OFFSET_CAPTURE)){
	          $date = str_replace('&amp;', '&', $dt[0][0]);
	        }
	        $extraString = str_replace('&gt', '>', str_replace('&lt', '<', str_replace([$date, ','], '', $foundContent)));
	        //extract name
	        if(preg_match('/(.+)(\<)/Us', $extraString, $nameStr, PREG_OFFSET_CAPTURE)){
	          $userName = $nameStr[1][0];
	        }
	        //extract email
	        if(preg_match('/(\<)(.+)((\>))/Us', $extraString, $nameStr, PREG_OFFSET_CAPTURE)){
	          $foundContent = $nameStr[2][0];
	        }
	    }
	    if($foundContent == null){
	      $uEmail = null;
	      if(preg_match('/(.+)(\b((am)|(pm))\b)/is', $content, $matches, PREG_OFFSET_CAPTURE)){
	        $foundContent = str_replace('&gt;', '>', $matches[0][0]);
	        $foundContent = str_replace('&lt;', '<', $foundContent);


	        if(preg_match('/[a-zA-Z0-9]\S+@\S+[a-zA-Z]/si', $foundContent, $nameStr1, PREG_OFFSET_CAPTURE)){
	          $uEmail = $nameStr1[0][0];
	          $foundContent = str_replace(['&lt;', '&gt;', $uEmail], '', $foundContent);
	        }
	        if(preg_match('/(\<body)(.+)(\b((am)|(pm))\b)/si', $foundContent, $nameStr1, PREG_OFFSET_CAPTURE)){
	          $extraString = $nameStr1[0][0];

	          if(preg_match('/(.+)(\<)/si', $extraString, $nameStr1, PREG_OFFSET_CAPTURE)){
	            $userName = strip_tags($nameStr1[1][0]);
	          }
	          $extraString = strip_tags($extraString);
	          $date = str_replace([$userName, '>'], '', $extraString);
	          $foundContent = correctedText($uEmail);
	        }
	        $extraString = str_replace('&gt', '>', str_replace('&lt', '<', $foundContent));
	        //extract name
	        if(preg_match('/(.+)(\<.+@.+\>)/Us', $extraString, $nameStr, PREG_OFFSET_CAPTURE)){
	          $userName = strip_tags($nameStr[2][0]);

	        }

	      }
	    }

	  }else{
		$date = str_replace('&amp;', '&', getDateFromText($content, $muid, $ctype));
	    $userEmail = getUserEmail($foundContent);
	  }
	  //$date = '	&nbsp;Sunday, June 30, 2019 11:08 PM';
	  $removalStringArray = ['-$-', '&lt;', '&gt;', '&quot;', '&nbsp;'];
	  $date = correctedText($date);
	  $date = str_replace(' at ', ' ', $date);
      $date = trim(str_replace(['>', '\'', '<', '"', '?', '�','-$-', '&lt;', '&gt;', '&quot;', '&nbsp;'], '', strip_tags($date)));
	  $date = cleanForDate($date);
	  $date = str_replace('##', ' ', $date);
	  $date = trim(str_replace($removalStringArray, '', $date));
	  $date = substr($date, 0, 48);
	  $userEmail = trim(str_replace(['>', '\'', '<', '"', '&lt;', '&gt;', '&quot;', '&nbsp;', 'Reply-', '&#60;', '&#62;'], '', strip_tags($userEmail)));
	  $userEmail = returnThisEmail($userEmail);
	  $userName = trim(str_replace(['>','\'', '?','�', 'Reply-', '-$-', '&#60;', '&#62;', '</TD', '</td', '<', '"', '&lt;', '&gt;', '&#39;', 'href=mailto:', 'mailto:', '&quot;', '&nbsp;'], '', strip_tags($userName)));
	  $userName = str_replace(['', '&amp;'], ' & ', $userName);
	  $userName = clean(returnThisName($userName));
	  $userName = str_replace('##', ' ', $userName);
	  $userName = trim(str_replace($removalStringArray, '', $userName));
	  $userEmail = trim(str_replace($removalStringArray, '', $userEmail));
	  $returnData =  ['name' => $userName, 'email' => $userEmail, 'date' => $date, 'muid' => $muid];
	  return $returnData;
	}catch(\Exception $e){
		return ['name' => null, 'email' => null, 'date' => null, 'muid' => $muid];
	}
}
