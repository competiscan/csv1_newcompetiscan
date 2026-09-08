#!/usr/bin/php
<?php
require_once("includes/ehLog_set.php");
$ehL->start(__FILE__, true, E_ALL ^ E_DEPRECATED); //smtp.php Assigning the return value of new by reference is deprecated
//ini_set( "default_charset", "iso-8859-1" );
ini_set("default_charset", "utf-8");
require_once("includes/dbcon.php");
require_once("includes/functions.php");
require_once('Mail.php');
require_once('Mail/mime.php');
require_once('includes/functions_latest2.php');  //latest function
ini_set("memory_limit", "-1");
set_time_limit(0);
require_once __DIR__ . '/vendor/autoload.php';
$user = new \HS\User($DRW, $DRW_read2);
$testing = false; // Set to true for test, will not send mail or update db.
$tries = 0;
$send_success = 0;
$send_fail = 0;
$weekday = date('w'); //0 (for Sunday) through 6 (for Saturday)
$day = (int) date('j');
$email_list = $user->getEmailList();
$trendnotificationlist = getTrendNotificationListData();
$params = array(
    'username' => '',
    'password' => '',
    'persist' => true,
);
$mail = & Mail::factory('smtp', $params);
$crlf = "\n"; //if you want to send the generated MIME message using Mail  then you have to set $crlf to "\n"
$sent = false;
$send_success = 0;
$send_fail = 0;
$day_10 = date('Y-m-d', strtotime('-10 day'));
foreach ($trendnotificationlist as $row_data) {
    $andCond='';
    $sector_id =$row_data['sectorID'];
    $category_id =$row_data['categoryID'];
    $subcategory_id =$row_data['subCategoryID'];
    $subtosubcategory_id =$row_data['subSubCategoryID'];
    $audience_id =$row_data['mPanelID'];
    $country_id=$row_data['country'];
    $searchKey=$row_data['searchKey'];
    $searchType=$row_data['searchType'];
    $emailAddress=$row_data['emailAddress'];
    $userID=$row_data['userID'];
    $SID=$row_data['ID'];
    $setDate = date("Y-m-d H:i:s");
    if(!empty($searchKey)) { 
                if (!empty($SPHINX_name)) {
                    $s = startSphinx();
                    if($searchType=='trend_fulltext' ){
                         $inds = 'base_index_prod_trendreport_fulltext';

                    }else{
                        $inds = 'base_index_prod_trendreport';
                    }
                    $ps = parseSphinx($s, $searchKey);

                    if (trim($ps) != '') {
                        $currcount = 0;
                        $step = $total = 50000;
                        $s->setLimits(0, 1, 1);
                        $result = $s->query($ps, $inds);
                        if (!empty($result['matches'])) {
                            $total = (float) $result['total_found'];
                            $count = 0;
                            $minID = 0;
                            $count_save_sql = "SELECT MAX(trend_id) FROM cscan_trend_document_text";
                            $rs = $DRW->query($count_save_sql, $DRW_read2);
                            $data = $DRW->fetch_row($rs);
                            $maxID = $data[0];
                              $DRW->query('START TRANSACTION', $DRW_main);
                              $trendidsarray=array();
                            for ($offset = 0; $offset <= $maxID; $offset += $step) {
                                $s = startSphinx();
                                $s->setLimits(0, $step, $step);
                                $s->setIDRange($minID + 1, $maxID);
                                $result = $s->query($ps, $inds);
                               if (isset($result['matches'])) {
                                    foreach ($result['matches'] as $dts_id => $match) {
                                        $minID = $dts_id;
                                        $currcount++;

                                         $trendidsarray[] =   $match['attrs']['trend_id'];


                                    }
                                    if ($currcount >= $total) {
                                        break;
                                    }
                                }
                                $err = $s->getLastError();
                                $war = $s->getLastWarning();
                                if (!empty($err) || !empty($war)) {
                                    break;
                                }
                            }
                             $DRW->query('COMMIT', $DRW_main); 
                        }
                              $trendidsarray   =  array_unique($trendidsarray);
                             if (!empty($trendidsarray)) {
                                 $andUnion = '';
                                 $chunkdata=10000;
                                 if($total>600000){
                                         $chunkdata=50000;
                                 }
                                 $newarray = array_chunk($trendidsarray, $chunkdata);
                                 for ($u = 2; $u < 100; $u++) {
                                     if (count($newarray) >= $u) {

                                         $andUnion.="union ( SELECT tr.trend_id   FROM  cscan_trend_report tr  WHERE tr.trend_id IN(" . implode(',', ($newarray[$u - 1])) . "))";
                                     }else{
                                         continue;
                                     }
                                 }
                                 $wheresearchtrend = " AND ctr.trend_id in (" .implode(',',$trendidsarray) . ") ";
                             }
                             if (empty($trendidsarray)) {
                                 $andcond = '-1';
                                 $wheresearchtrend = " AND ctr.trend_id in (" . $andcond . ") ";
                             }
                    }
                }
        $andCond .= $wheresearchtrend;
    }
    if(!empty($audience_id)) { 
               $andCond .= " and audience_id In ($audience_id)";
    }
    if(!empty($sector_id)) { 
           $andCond .= " and sector_id In ($sector_id)";
    }
    if(!empty($category_id)) { 
           $andCond .= " and ctc.category_id In ($category_id)";
    }
    if(!empty($subcategory_id)) { 
           $andCond .= " and subcategory_id In ($subcategory_id)";
    }
    if(!empty($subtosubcategory_id)) { 
           $andCond .= " and subtosubcategory_id In ($subtosubcategory_id)";
    }
    if($country_id) { 
           $andCond .= " and country_id = '$country_id'";
    }
    
    $querySql="SELECT SQL_NO_CACHE DISTINCT ctr.trend_id,trend_name,trend_link,trend_date,file_path,file_name,audience_id,country_id,ctc.sector_id,ctc.category_id,ctc.subcategory_id,ctc.subtosubcategory_id,ctr.rndtrend_id FROM cscan_trend_report ctr JOIN cscan_trends_category ctc ON ctc.trend_id = ctr.trend_id WHERE 1=1  $andCond GROUP BY ctr.trend_id ORDER BY ctr.trend_date DESC";
    $result1 = $DRW->query($querySql, $DRW_read2);
    if ($result1) {
        while ($row2 = $DRW->fetch_row($result1)) {
            $sectorcategoryName='';
            $trend_id = $row2[0];
            $trend_name = $row2[1];
            $trend_link = $row2[2];
            $trend_date = $row2[3];
            $rndtrend_id = $row2[12];
            $searchkey_html='';
            if($searchKey!=''){
            $searchkey_html ='<br /><strong>Search Key: </strong>'.$searchKey;
            }
            $searchType_html='';
            $s_tye='';
            if($searchType!=''){  
                if($searchType=='trend_ocr'){
                    $s_tye = "OCR";
                }else{
                   $s_tye = "Full Text";
                }
            $searchType_html ='<br /><strong>Search Type: </strong>'.$s_tye;
            }
            $aud_html='';
            if(!empty($audience_id)){
            $audiencename = mediaPanelName($audience_id);
            $aud_html ='<br /><strong>Audience: </strong>'.$audiencename;
            }
            $sectorName='';
            $sect_html='';
            if(!empty($sector_id)){
            $sectorName = sectorName($sector_id); 
            $sectorcategoryName .=$sectorName;
            $sect_html='<br /><strong>Sector: </strong>'.$sectorName;
            }
            $categoryName='';
            $cathtml='';
            if(!empty($category_id)){
            $categoryName = categoryName($category_id);
            $sectorcategoryName .= '/'.$categoryName;
            $cathtml ='<br /><strong>Category: </strong>'.$categoryName;
            }
            $subCategoryName='';
            $subcat_html ='';
            if(!empty($subcategory_id)){
            $subCategoryName = subCategoryName($subcategory_id);
            $sectorcategoryName .= '/'.$subCategoryName;
            $subcat_html ='<br /><strong>Sub Category: </strong>'.$subCategoryName;
            }
            $subsubCategoryName='';
            $susubcat_html='';
            if(!empty($subtosubcategory_id)){
            $subsubCategoryName = subCategoryName($subtosubcategory_id);
            $sectorcategoryName .= '/'.$subsubCategoryName;
            $susubcat_html ='<br /><strong>Sub Sub Category: </strong>'.$subsubCategoryName;
            }
            $country = $country_id;
            if($country==1){
                $country='US';
            }elseif ($country==3) {
                 $country='CANADA';
            }else{
                $country='All';
            }
            if($trend_date < $day_10)
               continue;
            $subject = "E-mail Alert from Competiscan: New Trend Report - $sectorcategoryName $trend_name";
            $message = ' <html><body><p style="FONT-FAMILY: verdana, Arial, Helvetica, sans-serif;FONT-SIZE: 10pt;">A new trend report has been posted to the Trend Reports page at <a href="https://www.competiscan.com/" target="_new" title="Click here to go to Competiscan">www.competiscan.com</a>, based on the following criteria:<br /><br />
            '.$searchkey_html.$searchType_html.$aud_html.$sect_html.$cathtml.$subcat_html.$susubcat_html.'<br /><strong>Country: </strong>'.$country.'<br /><br />For your convenience, a link to the report appears below.</p><p style="FONT-FAMILY: verdana, Arial, Helvetica, sans-serif;FONT-SIZE: 10pt;"><a href="https://www.competiscan.com/index.php?trend_id=' .$rndtrend_id. '" target="_new" title="Click here to view trend report on Competiscan">' .htmlspecialchars($trend_name).'</a></p></body></html>>';
            $andCondQ3='';
            if(!empty($audience_id)) { 
               $andCondQ3 .= " and mPanelID In ($audience_id)";
                }
            if(!empty($sector_id)) { 
                   $andCondQ3 .= " and sectorID In ($sector_id)";
            }
            if(!empty($category_id)) { 
                   $andCondQ3 .= " and categoryID In ($category_id)";
            }
            if(!empty($subcategory_id)) { 
                   $andCondQ3 .= " and subCategoryID In ($subcategory_id)";
            }
            if(!empty($subtosubcategory_id)) { 
                   $andCondQ3 .= " and subSubCategoryID In ($subtosubcategory_id)";
            }
            if($country_id) { 
                   $andCondQ3 .= " and country = '$country_id'";
            }
           $sqlthree = "SELECT SQL_NO_CACHE userID FROM cscan_trend_report_search WHERE 1=1  $andCondQ3  AND LEFT(lastSentDate,10)<'$trend_date' AND lastsentTrend_id<>$trend_id AND userID='$userID' AND ID=$SID"; 
             $query3 = $DRW->query($sqlthree, $DRW_read2);
             while ($row3 = $DRW->fetch_row($query3)) { 
                 $userID = $row3[0];
            if (!isset($userID))
                    continue;
                //$to = $userID['emailAddress'];
                $to = $email_list[$userID]['emailAddress'];
               // $to='devendra.tiwari@nmgtechnologies.com,pradeep.chaurasia@nmgtechnologies.com';
                $hdrs = array('From' => "\"Competiscan\" <share@competiscan.com>", 'To' => $to, 'Subject' => $subject);
                $mime = new Mail_mime($crlf);
                $mime->setHTMLBody($message);
                $body = $mime->get();
                $headers = $mime->headers($hdrs);
                if ($testing) {
                   $ehL->write("send(to=$to, subject=$subject)");
                    continue;
                }
                $sent = false;
                $send = $mail->send($to, $headers, $body);
                $queryUpdateSQL = "UPDATE cscan_trend_report_search SET lastSentDate='".$setDate."',queryDate='".$row_data['queryDate']."',lastsentTrend_id='".$trend_id."' WHERE ID='".$SID."' AND userID='".$userID."'";
                $DRW->query($queryUpdateSQL, $DRW_main);
                if (PEAR::isError($send)) {
                    $ehL->write($send->getMessage());
                    $mail->disconnect();
                    $mail = & Mail::factory('smtp', $params);
                } else {
                    $sent = true;
                    $queryUpdateSQL = "UPDATE cscan_trend_report_search SET lastSentDate='".$setDate."',queryDate='".$row_data['queryDate']."',lastsentTrend_id='".$trend_id."' WHERE ID='".$SID."' AND userID='".$userID."'";
                    $DRW->query($queryUpdateSQL, $DRW_main);
                }
                if (!$sent) {
                    sleep(10);
                }
                if ($sent) {
                    ++$send_success;
                } else {
                    ++$send_fail;
                }
            }
           @$DRW->free_result($query3);
        }
        @$DRW->free_result($result1);
    } 
}
@$DRW->free_result($resultData);
$ehL->write("Trend: $send_success emails sent, $send_fail emails failed.");
$ehL->stop();



function getTrendNotificationListData() {
    global $DRW, $DRW_read2, $DRW_main, $DRW_crm;
    $notifyList = array();
    $queryupdate = "UPDATE cscan_users SET ordering=userID WHERE ((ordering=0) OR (ordering is null))";
    $DRW->query($queryupdate, $DRW_main);
    $query = "SELECT SQL_NO_CACHE DISTINCT ID, searchKey,searchType, mPanelID,sectorID,categoryID,subCategoryID,subSubCategoryID,s.country, s.userID,
        lastSentDate, queryDate,u.emailAddress, u.is_public_user,u.ordering
        FROM cscan_trend_report_search as s
        JOIN cscan_users as u on (u.userID=s.userID)
        WHERE emailAlert='1' AND u.userID =s.userID
             AND active='y' AND
            LEFT(lastSentDate,10)<=DATE_SUB(CURDATE(),INTERVAL 1 DAY)
            order by u.ordering ASC";
//29910
//26437
//45037
//AND s.ID=7765
    $resultData = $DRW->query($query, $DRW_read2);

    while ($row = $DRW->fetch_assoc($resultData)) {
        $notifyList[] = $row;
    }

    return $notifyList;
}
?>