<?php

error_reporting(E_ALL ^ E_DEPRECATED);
ini_set('display_errors', 1);
require_once ("includes/dbcon.php");
$DRW->databaseReadWrite_die = 1;
include_once 'includes/functions.php';
$dirName = dirname(__FILE__);
function getUserEmail($contentText, $muid=0, $ctype='text/html'){
  $matches = [];
  $returnContent = null;
  if(preg_match('/[a-zA-Z0-9]\S+@\S+[a-zA-Z]/si', $contentText, $matches, PREG_OFFSET_CAPTURE) && isset($matches[0][0])){
    $returnContent = $matches[0][0];
    if(preg_match('/(mailto:)(.+)(\")/xi', $matches[0][0], $newStr, PREG_OFFSET_CAPTURE)){
      $returnContent = $newStr[2][0];
    }
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
  if(preg_match('/(.+)((S[ubjectUBJECT]{6}:)|(S[entENT]{3}:)|(D[ateATE]{3}:)|(T[oO]{1}:))/isU', $text, $matches, PREG_OFFSET_CAPTURE)){
    $userNameText = $matches[1][0];
  }
  return $userNameText;
}

function getUserName($contentText, $muid=0, $ctype='text/html'){
  //echo $muid;
  $userNameText = null;
  $matches = [];
  $contentText = str_get_html($contentText);
  if(is_object($contentText)){
    $e = $contentText->find('b', 0);
    if($e) $e->class = null;
    $e = $contentText->find('strong', 0);
    if($e) $e->class = null;
    $e = $contentText->find('span', 0);
    if($e) $e->class = null;
  }
  $foundFlag = false;
  if(!$foundFlag && preg_match('/(.+)(\>)/i', $contentText, $matches, PREG_OFFSET_CAPTURE)){
      $userNameText = getTrimmedName($matches[1][0], $muid, $ctype);
  }
  return $userNameText;
}
function checkDateContent($dateContent, $tag='br', $uid=0){
  $dateContent = htmlentities($dateContent);
  $tag = '/'.$tag;
  if(strlen($dateContent) > 50
    && (
        substr_count(substr($dateContent, 0, 50), '&lt;'.$tag.'&gt;') ||
        substr_count(substr($dateContent, 0, 50), '&lt;'.$tag.'&gt;')
      )
  ){
    return true;
  }
  return false;
}

function getDateFromText($content, $muid=0, $ctype='text/html'){
  $matches = [];
  if($ctype == 'text/html'){
    $foundContent = null; $found = false; $divFlag = false;
    if(preg_match('/((S[entENT]{3}:)|(D[ateATE]{3}:))(.+)(\<\/DIV)/xi', $content, $matches, PREG_OFFSET_CAPTURE)){
        $foundContent = $matches[4][0];
        $divFlag = checkDateContent($foundContent);
        $found = true;
    }
    if($divFlag){
      $content = $foundContent;
      $found = false;
    }
    if(!$found && preg_match('/((S[entENT]{3}:)|(D[ateATE]{3}:))(.+)(\<br)/xi', $content, $matches, PREG_OFFSET_CAPTURE)){
        $foundContent = $matches[2][0];
        $found = true;
    }
    if(!$found && preg_match('/((S[entENT]{3}:)|(D[ateATE]{3}:))(.+)(\<tr)/xi', $content, $matches, PREG_OFFSET_CAPTURE)){
        $foundContent = $matches[2][0];
        $found = true;
    }
    $divFlag = (checkDateContent($foundContent, 'DIV', $muid) || checkDateContent($foundContent, 'div', $muid));
    if($divFlag){
      $foundContent = str_replace(['</DIV>', '</Div>'], '</div>', $foundContent);
      $newContent = explode('</div', $foundContent);
    }else{
      $newContent = explode('<br', $foundContent);
    }

    if(count($newContent)){
      $foundContent = $newContent[0];
    }
    $foundContent = strip_tags($foundContent);
  }else{
    $found = false; $contentText = '';
    if(preg_match('/((D[ateATE]{3}:)|(S[entENT]{3}:))(.+)(\>)/xi', $content, $matches, PREG_OFFSET_CAPTURE)){
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
  return (preg_match("/([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}/si", $text, $match)) ? $match[0] : $text;
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
function getFromText($content, $muid=0, $ctype='text/html'){
  $foundContent = $userEmail = $userName = null; $found = false;$str=[];$date=null;
  $content = str_replace("\n", '', $content);
  //replace &lt;
  $content = str_replace('&lt;', '<', $content);
  //replace &rt;
  $content = str_replace('&rt;', '>', $content);
  //striping <b> tag
  //striping <strong> tag
  $content = str_replace(['title=', '<b>', '</b>','<span>', '</span>', '<strong>', '</strong>', '<B>', '</B>', '<STRONG>', '</STRONG>', '< STRONG>', '</STRONG >','<SPAN>', '</SPAN>',], '', $content);
  if(preg_match('/(From:)(.+)((\>)|(\<\/[TRtr]{2}>))/is', $content, $matches, PREG_OFFSET_CAPTURE)){
      $foundContent = $matches[2][0];
      $userName = getUserName($matches[2][0], $muid,$ctype);
      $foundContent = correctedText($foundContent);
  }
  //die($content);
  if($foundContent == null){
    $content = substr($content, 6, 2000);
    $userName = $date = null;
    if(preg_match('/(\bOn\b)(.+)(\>)/Us', $content, $matches, PREG_OFFSET_CAPTURE)){
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
    $foundContent = getUserEmail($foundContent);
  }
  $date = correctedText($date);
	$date = str_replace(' at ', ' ', $date);
  $userEmail = trim(str_replace(['>', '\'', '<','br', '"', '&lt;', '&gt;', '&quot;', '&nbsp;'], '', strip_tags($foundContent)));
  $userEmail = trim(str_replace(['[', ']', '&lt', '&gt','&quot', 'class=', '=', 'mailto:', 'href"', 'href=mailto:','href"mailto:', ';</div', '<img', ';<br', '<BR', '<br', ';'], '', $foundContent));
  $userEmail = returnThisEmail($userEmail);

  $userName = trim(str_replace(['>','\'', '?', '<','br', '"', '&lt;', '&gt;', 'href=mailto:', 'mailto:', '&quot;', '&nbsp;'], '', strip_tags($userName)));
  $userName = str_replace(['', '&amp;'], ' & ', $userName);
  $userName = returnThisName($userName);
  $returnData =  ['name' => $userName, 'email' => $userEmail, 'date' => $date, 'muid' => $muid];
  return $returnData;
}


$and = '';

$previousdate = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d'))));
//$and	=" AND e.email_date> '".$previousdate."' limit 100 ";
//$and	=" AND  e.is_fetch=1 ";
$hy=201707;

$sqlinsert   =  "update cscan_scrap_email_log set max_muid=(select max(muid) from cscan_email$hy where is_fetch=1) where table_name='cscan_email$hy'";
$query = $DRW->query($sqlinsert, $DRW_main);


$sql = "SELECT max_muid from cscan_scrap_email_log where table_name='cscan_email$hy'" ;
$query = $DRW->query($sql, $DRW_read);
$row = $DRW->fetch_assoc($query);
$max_muid = $row['max_muid'];

$LIMIT = 1000;
$sql = "SELECT e.muid,et.cettext,et.cettype from cscan_email$hy as e join cscan_email_text$hy as et on (et.muid=e.muid)
		where  et.cettype='text/html' AND  e.is_fetch=0  AND e.muid>$max_muid  limit {$LIMIT}" . $and;

require('simple_html_dom.php');

$f1 = fopen($dirName."/test-files/name-{$hy}.txt", 'a+');
$f2 = fopen($dirName."/test-files/email-{$hy}.txt", 'a+');
$f3 = fopen($dirName."/test-files/date-{$hy}.txt", 'a+');
$f4 = fopen($dirName."/test-files/test-file-{$hy}.txt", 'a+');
$nameEmailDate = ['total' => 0, 'name' => 0, 'email' => 0, 'date' => 0];


$query = $DRW->query($sql, $DRW_read);
if ($DRW->num_rows($query) > 0) {
		$nameEmailDate['total'] = $DRW->num_rows($query);
    while ($row = $DRW->fetch_assoc($query)) {
			$queryObj = [];
			$emailDetails = getFromText($row['cettext'], $row['muid'], $row['cettype']);
		  $emailDetails['muid'] = $row['muid'];
			if($emailDetails['name'] == null){
		    fwrite($f1, $emailDetails['muid']."\n");
		  }else if(strlen($emailDetails['name']) > 70){
		    fwrite($f1, "\t".$emailDetails['muid']."\t\t\t\t".$emailDetails['name']."\n");
		    $emailDetails['name'] = null;
		  }
		  if($emailDetails['email'] == null){
		    fwrite($f2, $emailDetails['muid']."\n");
		  }else if(!valid_email($emailDetails['email'])){
		    fwrite($f2, "\t".$emailDetails['muid']."\t\t\t\t".$emailDetails['email']."\n");
		    $emailDetails['email'] = null;
		  }
		  if($emailDetails['date'] == null){
		    fwrite($f3, $emailDetails['muid']."\n");
		  }else if(strlen($emailDetails['date']) > 76){
		    fwrite($f3, "\t".$emailDetails['muid']."\t\t\t\t".$emailDetails['date']."\n");
		    $emailDetails['date'] = null;
		  }
		  if(strlen(trim($emailDetails['name']))){
		    $nameEmailDate['name']++;
		  }
		  if(strlen(trim($emailDetails['email']))){
		    $nameEmailDate['email']++;
		  }
		  if(strlen(trim($emailDetails['date']))){
		    $nameEmailDate['date']++;
		  }

		  fwrite($f4, $emailDetails['muid']."\t".$emailDetails['name']."\t".$emailDetails['email']."\t".$emailDetails['date']."\n");
		  fwrite($f4, print_r($nameEmailDate, true)."\n");

			if(!empty($emailDetails['name'])){
				$queryObj['from_sent_name'] = $DRW->real_escape_string($emailDetails['name']);
			}else{
				$queryObj['from_sent_name'] = null;
			}
			if(!empty($emailDetails['email'])){
				$queryObj['from_sent_email_address'] = $DRW->real_escape_string($emailDetails['email']);
			}else{
				$queryObj['from_sent_email_address'] = null;
			}
			if(!empty($emailDetails['email'])){
				$queryObj['from_sent_date'] = $DRW->real_escape_string($emailDetails['date']);
				$sentdate = strtotime($emailDetails['date']);
				$queryObj['from_sent_date_format'] = date('Y-m-d H:i:s', $sentdate);
			}else{
				$queryObj['from_sent_date'] = null;
			}
			$queryObj['is_fetch'] = 1;
			$updateQuery = [];
			foreach ($queryObj as $dataKey => $dataValue) {
					$updateQuery[] ="{$dataKey}='{$dataValue}'";
			}

			$sql = "UPDATE cscan_email$hy SET ".
							implode(', ', $updateQuery) .
							"	WHERE muid ='" . $row['muid'] . "'";
			if ($DRW->query($sql, $DRW_main)) {
					$success="Success";
			}
    }
}
//echo"jjj";
exit;
?>
