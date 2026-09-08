<?php
if(!defined('ENV')){ 
    define('ENV',getenv('SERVER_NAME'));
}
if(ENV != 'localhost' || ENV != 'demo.competiscan.com'){
    $redirect_page='https://cs.competiscan.com/';
    header("Location: $redirect_page");
    die;
}  
$TITLE = 'Competiscan Email';
require_once('panelist_top.php');
//$show_search_user=array(26,197,143,126,261,99,315,118,373,279,236,328);
// ini_set('display_startup_errors', 1);
// ini_set('display_errors', 1);
// error_reporting(-1);
// ini_set("memory_limit", "600");
// set_time_limit(600);
//echo '<pre>';print_r($_REQUEST);die;
$searchid = 0;
$searchsubj = 0;
$searchsender = 0;
$searchbody = 0;
$searchtext = '';
$searchstate = 0;
$searchcountry = 'US';
$panelist_ids = '';
$searchownbiz = -1;
$mtypes = array(0,1);
$noread = 0;
$ctype = 0;

$panelist_core_search = array();
//$postArray to save user search
//@Author : Pradeep Kumar
//Dated : Oct 12, 2019
$postArrary = [
    'searchKey' => 'searchtext',
    'searchName' => 'searchName',
    'CUID' => 'searchid',
    'Subject' => 'searchsubj',
    'Body' => 'searchbody',
    'SenderEmail' => 'searchsender',
    'HideMarkedRead' => 'noread',
    'FromDateM' => 'start_m',
    'FromDateD' => 'start_d',
    'FromDateY' => 'start_y',
    'ToDateM' => 'end_m',
    'ToDateD' => 'end_d',
    'ToDateY' => 'end_y',
    'Panelists' => 'panelist_ids',
    'StateProvince' => 'searchstate',
    'SelectionCountry' => 'searchcountry',
    'Owner' => 'searchownbiz',
    'searchPartition' => 'hy',
    'cType' => 'ctype'
];

if (isset($_GET['sort'])) {
    $_SESSION['sort'] = $sort = (int)$_GET['sort'];
} else {
    if (isset($_SESSION['sort'])) {
        $sort = $_SESSION['sort'];
    } else {
        $_SESSION['sort'] = $sort = 5;
    }
}

if (isset($_REQUEST['e_assigned_admin_userID'])) {
    $_SESSION['e_assigned_admin_userID'] = (int) $_REQUEST['e_assigned_admin_userID'];
}

if (checkGroup(20) && isset($_GET['ctype'])) {
    $_SESSION['ctype'] = $ctype = (int)$_GET['ctype'];
    setcookie('competiscan_content_contact', $ctype, time()+(86400*364), $COOKIEPATH, $COOKIEDOMAIN);
} else {
    if (isset($_SESSION['ctype'])) {
        $ctype = $_SESSION['ctype'];
    } elseif (isset($_COOKIE['competiscan_content_contact'])) {
        $_SESSION['ctype'] = $ctype = $_COOKIE['competiscan_content_contact'];
    } else {
        $_SESSION['ctype'] = $ctype = 2;
    }
} 

$panelist_core_options = array('C'=>'C','EN'=>'EN','N'=>'N','RL'=>'RL','TC'=>'TC','TL'=>'TL');

$searchMessages = [
    1 => "<style>.search-message{color:red;}</style><div class=\"search-message\">This search name already exists. Please try another.<script>setTimeout(function(){var list = document.getElementsByClassName(\"search-message\");for(var i = list.length - 1; 0 <= i; i--){ if(list[i] && list[i].parentElement){list[i].parentElement.removeChild(list[i]);}}}, 2500)</script></div>",
    2 =>  "<script>chk_search_name(document.searchNameForm.searchName.value);</script>",
];
if (isset($_REQUEST['ctype'])) {
    $ctype = $_REQUEST['ctype'];
}
if ( $_SESSION['ctype']==1) {
    $panelist_core_options = array('ID'=>'ID','PT'=>'PT','PN'=>'PN');
}
if ($_SESSION['ctype']==1) {
    $monthago = mktime(0, 0, 0, (int)date('n')-1, (int)date('j'), (int)date('Y'));
    $start_m = date('m', $monthago);
    $start_d = date('d', $monthago);
    $start_y = date('Y', $monthago);
    $end_m = date('m');
    $end_d = date('d');
    $end_y = date('Y');
} else {
    $start_m = '00';
    $start_d = '00';
    $start_y = '0000';
    $end_m = '00';
    $end_d = '00';
    $end_y = '0000';
}
$readTypes = array(0=>'Unread',1=>'Read');

if (isset($_GET['page'])) {
    $_SESSION['page'] = $page = (int)$_GET['page'];
} else {
    if (isset($_SESSION['page'])) {
        $page = $_SESSION['page'];
    } else {
        $_SESSION['page'] = $page = 0;
    }
}

if (isset($_GET['limshow'])) {
    $_SESSION['limshow'] = $limshow = (int)$_GET['limshow'];
    setcookie('competiscan_limshow', $limshow, time()+(86400*364), $COOKIEPATH, $COOKIEDOMAIN);
} else {
    if (isset($_SESSION['limshow'])) {
        $limshow = $_SESSION['limshow'];
    } elseif (isset($_COOKIE['competiscan_limshow'])) {
        $_SESSION['limshow'] = $limshow = $_COOKIE['competiscan_limshow'];
    } else {
        $_SESSION['limshow'] = $limshow = 10;
    }
}

if (isset($_SESSION['hy'])) {
    $hy = $_SESSION['hy'];
} else {
    $hy = '';
}

if (isset($_POST['sendmass']) && $_POST['muids']!='') {
    $muids = explode(',', $_POST['muids']);
    foreach ($muids as $m_uid) {
        if (isset($_POST["processed$m_uid"]) || isset($_POST["read$m_uid"]) || isset($_POST["panelist_score$m_uid"]) || isset($_POST["panelist_core$m_uid"])) {
            $set = '';
            if (isset($_POST["processed$m_uid"]) && $_POST["processed$m_uid"]!=$_POST["old_processed$m_uid"]) {
                $set .= "`deleted`='".$DRW->real_escape_string($_POST["processed$m_uid"])."'";
            }
            if (isset($_POST["read$m_uid"]) && $_POST["read$m_uid"]!=$_POST["old_read$m_uid"]) {
                if ($set!='') {
                    $set .= ', ';
                }
                $set .= "`email_read`='".$DRW->real_escape_string($_POST["read$m_uid"])."'";
            }
            if (isset($_POST["panelist_score$m_uid"])) {
                $panelist_score = ($_POST["panelist_score$m_uid"]);
                $panelist_score_old = ($_POST["old_panelist_score$m_uid"]);
                if ($panelist_score!=$panelist_score_old) {
                    if ($set!='') {
                        $set .= ', ';
                    }
                    $set .= "`panelist_score`='".$DRW->real_escape_string($_POST["panelist_score$m_uid"])."'";
                }
            }
            if (isset($_POST["panelist_core$m_uid"])) {
                $panelist_core = $_POST["panelist_core$m_uid"];
                $panelist_core_old = $_POST["old_panelist_core$m_uid"];
                if ($panelist_core!=$panelist_core_old) {
                    if ($set!='') {
                        $set .= ', ';
                    }
                    $set .= "`panelist_core`='".$DRW->real_escape_string($_POST["panelist_core$m_uid"])."'";
                }
            }
            if ($set!='') {
                $query = "UPDATE `cscan_email$hy` SET $set WHERE `muid`='".$DRW->real_escape_string($m_uid)."'";
                $DRW->query($query, $DRW_main);
                //uncomment to log user actions
                //$query = "REPLACE INTO cscan_email_check (muid,check_date,check_user,check_query) VALUES ('".$DRW->real_escape_string($m_uid)."',NOW(),{$AUTH_DATA['userID']},'".$DRW->real_escape_string($set." ($panelist_score_old=>$panelist_score)")."')";
                //$DRW->query($query,$DRW_main);
            }
        }
    }
    if (!empty($_POST['copies_id']) && !empty($_POST['marked'])) {
        $cmuid = $_POST['copies_id'];
        $productID = 0;
        $hyupdate='';
        if (preg_match('/^\\d{4}\\-\\d{2}\\-\\d{2}\\-\\d+$/', $cmuid)) {
            $query_p = "select productID,gender,age,incomeID,state,lastSeen from cscan_product_detail where entryID='".$DRW->real_escape_string($cmuid)."'";
            $query_result_p = $DRW->query($query_p, $DRW_read2);
            $data = $DRW->fetch_row($query_result_p);
            $productID = $data[0];
            $check = "SELECT muid,history_year FROM cscan_product_email WHERE isTmp=0 AND productID=".$productID;
            $check = $DRW->query($check, $DRW_read2);
            $data2 = $DRW->fetch_row($check);
            $cmuid = (float)$data2[0];
            if (!empty($cmuid) && !empty($data2[1])) {
                //$hy = $data2[1];
                $hyupdate = $data2[1];
            }
        } else {
            $check = "SELECT muid,gender,age,incomeID,state,competi_ids,invitation_ids,tracking_ids,fico_ids FROM cscan_product_email WHERE muid='".$DRW->real_escape_string($cmuid)."' AND isTmp=0";
            $check = $DRW->query($check, $DRW_read2);
            $data = $DRW->fetch_row($check);
            $cmuid = $data[0];
        }
        if (!empty($cmuid)) {
            $hydata     =   $hy;
            if (!empty($hyupdate)) {
                $hydata     =   $hyupdate;
            }
            $sqlc = "SELECT deleted,panelist_core,panelist_score,email_read FROM cscan_email$hydata WHERE muid='".$DRW->real_escape_string($cmuid)."'";
            $rsc = $DRW->query($sqlc, $DRW_read2);
            $datac = $DRW->fetch_row($rsc);
            $deleted = 4;//$datac[0];
            $panelist_core = $datac[1];
            $panelist_score = $datac[2];
            $email_read = $datac[3];
        }
        if (!empty($cmuid) || !empty($productID)) {
            $oldgender = $data[1];
            if (!empty($data[2])) {
                $oldage = explode(',', $data[2]);
            } else {
                $oldage = array();
            }
            if (!empty($data[3])) {
                $oldincomeID = explode(',', $data[3]);
            } else {
                $oldincomeID = array();
            }
            if (!empty($data[4])) {
                $oldstate = explode(',', $data[4]);
            } else {
                $oldstate = array();
            }
            $dates = array();
            $cpanelist_ids = array();
            $ageArray = array();
            $sql = "SELECT age_pID,age_pmin,age_pmax FROM cscan_age_product ORDER BY age_psort";
            $result = $DRW->query($sql, $DRW_read2);
            while ($row = $DRW->fetch_row($result)) {
                $ageArray[$row[0]] = array($row[1],$row[2]);
            }
            if (empty($productID)) {
                $competi_ids = explode(',', $data[5]);
                $invitation_ids = explode('|', $data[6]);
                $tracking_ids = explode('|', $data[7]);
                $fico_ids = explode('|', $data[8]);
                foreach ($competi_ids as $cid) {
                    list($pid, $pd) = explode('|', $cid);
                    $cpanelist_ids[] = $pid;
                    $dates[] = $pd;
                }
                $lastSeen = '';
            } else {
                $lastSeen = $data[5].' 00:00:00';
                $check = "SELECT ppdate FROM cscan_panelists_product pp join cscan_panelists cp on (pp.panelist_id=cp.panelist_id) WHERE productID=$productID";
                $check = $DRW->query($check, $DRW_read2);
                while ($datas = $DRW->fetch_row($check)) {
                    $dates[] = $datas[0];
                }
            }
            foreach ($dates as $d) {
                if (empty($lastSeen) || $d>$lastSeen) {
                    $lastSeen = $d;
                }
            }
            if (!empty($lastSeen)) {
                $ppdate = $lastSeen;
            } else {
                $ppdate = date('Y-m-d H:i:s');
            }
            $lastSeen = substr($data[5], 0, 10);
            foreach ($_POST['marked'] as $m) {
                if ($m!=$cmuid) {
                    if (!empty($cmuid)) {
                        $query = "UPDATE `cscan_email$hy` SET deleted='$deleted',panelist_core='$panelist_core',panelist_score='$panelist_score',email_read='$email_read' WHERE `muid`='".$DRW->real_escape_string($m)."'";
                        $DRW->query($query, $DRW_main);
                    } else {
                        $query = "UPDATE `cscan_email$hy` SET deleted='4' WHERE `muid`='".$DRW->real_escape_string($m)."'";
                        $DRW->query($query, $DRW_main);
                    }
                    $query = "SELECT DATE_FORMAT(`email_date`,'%Y-%m-%d'),panelist_id FROM `cscan_email$hy` WHERE `muid`='".$DRW->real_escape_string($m)."'";
                    $query_result = $DRW->query($query, $DRW_read2);
                    $data = $DRW->fetch_row($query_result);
                    $firstseen = $data[0];
                    $panelist_id = $data[1];
                    if (!empty($panelist_id) && !in_array($panelist_id, $cpanelist_ids)) {
                        $defs = "SELECT DATEDIFF(CURDATE(),birthdate) as agedays,stateID,gender,homeownershipID,incomeID,fico_score,contactTypeID,ownbiz,postalcode,parent_panelist_id
							FROM cscan_panelists WHERE panelist_id=$panelist_id";
                        $resultD = $DRW->query($defs, $DRW_read2);
                        $dataD = $DRW->fetch_row($resultD);
                        $ppage = floor($dataD[0]/365);
                        $ppstateID = (int)$dataD[1];
                        $pgender = strtoupper(substr(trim($dataD[2]), 0, 1));
                        $homeownershipID = $dataD[3];
                        $pincomeID = $dataD[4];
                        $ppfico_score = $dataD[5];
                        $contactTypeID = $dataD[6];
                        $ownbiz = (int)$dataD[7];
                        $pppostalcode = trim($dataD[8]);
                        $ppageID = 0;
                        $parent_panelist_id = $dataD[9];
                        if ($parent_panelist_id>0) {
                            $pprimary = 0;
                        } else {
                            $pprimary = 1;
                        }
                        foreach ($ageArray as $aID=>$a_array) {
                            if ($ppage>=$a_array[0] && $ppage<=$a_array[1]) {
                                $ppageID = $aID;
                                break;
                            }
                        }
                        if ($pgender!=$oldgender) {
                            if ($oldgender=='N') {
                                $oldgender = $pgender;
                            } elseif ($pgender!='N') {
                                $oldgender = 'B';
                            }
                        }
                        if (!empty($ppageID) && !in_array($ppageID, $oldage)) {
                            $oldage[] = $ppageID;
                        }
                        if (!empty($pincomeID) && !in_array($pincomeID, $oldincomeID)) {
                            $oldincomeID[] = $pincomeID;
                        }
                        if (!empty($ppstateID) && !in_array($ppstateID, $oldstate)) {
                            $oldstate[] = $ppstateID;
                        }
                        if (empty($productID)) {
                            $competi_ids[] = $panelist_id.'|'.$ppdate;
                            $invitation_ids[] = '';
                            $tracking_ids[] = '';
                            $fico_ids[] = '';
                        } else {
                            $sqlU = "INSERT IGNORE INTO cscan_panelists_product (productID,panelist_id,ppdate,ppage,ppstateID,pgender,homeownershipID,pincomeID,ppageID,ppfico_score,isBiz,pppostalcode,ppaddeddate,pprimary)
								VALUES ($productID,".$panelist_id.",'$ppdate',$ppage,$ppstateID,'$pgender',$homeownershipID,$pincomeID,$ppageID,$ppfico_score,$ownbiz,'".$DRW->real_escape_string($pppostalcode)."',NOW(),$pprimary)";
                            $DRW->query($sqlU, $DRW_main);
                        }
                    }
                }
            }
            if (empty($productID)) {
                $sqlU = "UPDATE cscan_product_email SET gender='$oldgender',age='".implode(',', $oldage)."',incomeID='".implode(',', $oldincomeID)."',state='".implode(',', $oldstate)."',competi_ids='".implode(',', $competi_ids)."',invitation_ids='".implode('|', $invitation_ids)."',tracking_ids='".implode('|', $tracking_ids)."',fico_ids='".implode('|', $fico_ids)."' WHERE muid='".$DRW->real_escape_string($cmuid)."' AND isTmp=0";
                $DRW->query($sqlU, $DRW_main);
            } else {
                $sqlU = "UPDATE cscan_product_detail SET gender='".$DRW->real_escape_string($oldgender)."',
					age='".$DRW->real_escape_string(implode(',', $oldage))."',incomeID='".$DRW->real_escape_string(implode(',', $oldincomeID))."',
					state='".$DRW->real_escape_string(implode(',', $oldstate))."',lastSeen='$lastSeen' WHERE productID=$productID";
                $DRW->query($sqlU, $DRW_main);
                updateStateLookup($productID);
            }
        }
    }
    ob_end_clean();
    header("Location: {$_SERVER['PHP_SELF']}?updated=1&ctype={$ctype}");
    exit;
}

if (isset($_REQUEST['upp'])) {
    exec("/usr/bin/php sugar_transfer.php > /dev/null 2>&1 &");
    sleep(3);
    ob_end_clean();
    header("Location: {$_SERVER['PHP_SELF']}?ctype={$ctype}");
    exit;
}
if (isset($_REQUEST['checkmail'])) {
    exec("/usr/bin/php imap_back.php > /dev/null 2>&1 &");
    sleep(3);
    ob_end_clean();
    header("Location: {$_SERVER['PHP_SELF']}?ctype={$ctype}");
    exit;
}

if (checkGroup(20)) {
    print "<div style=\"margin-bottom:4px;\"><form action=\"{$_SERVER['PHP_SELF']}?ctype={$ctype}\" style=\"display:inline;\" method=\"get\">
      <input class=\"button\" type=\"submit\" name=\"checkmailsub\" value=\"Check Mail\" /> &nbsp;
      <input class=\"button\" type=\"submit\" name=\"report\" value=\"Get Report\" onclick=\"document.location.href='panelist_report_month.php'; return false;\" /> &nbsp;
      <input class=\"button\" type=\"submit\" name=\"upp\" value=\"Update Panelists\" onclick=\"var rLocation ='{$_SERVER['PHP_SELF']}?upp=1'; document.location.href=rLocation; return false;\" />";
    if ($ctype==2) {
        print " &nbsp; <input class=\"button\" type=\"submit\" name=\"upp\" value=\"Last Panelist ID\" onclick=\"winPopScore('consumer_inc.php'); return false;\" />";
    }
    print "<input type=\"hidden\" name=\"checkmail\" value=\"1\" /></form>";
    if (isset($_GET['updated'])) {
        print ' &nbsp; &nbsp; <span class="error">Updated</span>';
    }
    print " &nbsp; &nbsp; <a href=\"imapSavedSearch.php?page=0\" class=\"button\">Saved Search</a>";
    print "</div>";
}

echo '<div><div style="float:left;">';
$n = 0;
$cwhere = '';
if (isset($_SESSION['e_assigned_admin_userID']) && $_SESSION['e_assigned_admin_userID']!=0) {
    $e_assigned_admin_userID = $_SESSION['e_assigned_admin_userID'];

    $cwhere .= " AND e_assigned_admin_userID=".$_SESSION['e_assigned_admin_userID'];
} else {
    $e_assigned_admin_userID = 0;
}
$last  = count($contactType) - 1;
foreach ($contactType as $contact_type_m_c=>$contact_type_m_cTitle) {
    if (checkGroup(20) || $n==2) {
        if ($ctype==$n) {
            if ($n>0) {
                $cwhere .= " AND `contact_type_m_c`='{$contact_type_m_c}'";
            }
            print "<span class=\"headings\">$contact_type_m_cTitle</span>";
        } else {
            print "<a href=\"{$_SERVER['PHP_SELF']}?ctype=$n\" class=\"bluelink\">$contact_type_m_cTitle</a>";
        }
        if ($n!=$last) {
            print ' &nbsp; | &nbsp; ';
        }
    }
    $n++;
}
echo '</div>';

$search_id = 0;
$lastSearchFlag = false;
if (isset($_REQUEST['ssid']) && $_REQUEST['ssid']!='') {
    $search_id = (int)$_REQUEST['ssid'];
}
if (isset($_REQUEST['rid']) && $_REQUEST['rid']!='') {
    $search_id = (int)$_REQUEST['rid'];
}
if (isset($_REQUEST['lstSrch']) && $_REQUEST['lstSrch']!='') {
    $search_id = (int)$_REQUEST['lstSrch'];
    $lastSearchFlag = true;
}
if ($search_id > 0) {
    if ($lastSearchFlag) {
        $searchResorceQuery = "SELECT * FROM cscan_content_site_search WHERE userID={$AUTH_DATA['userID']} AND searchSave=0 AND cType={$ctype};";
    } else {
        $searchResorceQuery = "SELECT * FROM cscan_content_site_search WHERE userID={$AUTH_DATA['userID']} AND ID={$search_id};";
    }
    //echo "\nID={$search_id}: ".$searchResorceQuery."\n";
    $searchResorce = $DRW->query($searchResorceQuery, $DRW_read2);
    $searchtext = '';
    if (!empty($searchResorce) && $searchResorce->num_rows) {
        $userLastSearch = $DRW->fetch_assoc($searchResorce, $DRW_read2);
          //echo '<pre>';print_r($userLastSearch);die;
            $searchid = $userLastSearch['CUID'];//CUID
            $searchsubj = $userLastSearch['Subject'];//Subject
            $searchsender = $userLastSearch['SenderEmail'];//SenderEmail
            $searchbody = $userLastSearch['Body'];//Body
            $searchtext = $userLastSearch['searchKey'];//searchKey
            $searchstate = $userLastSearch['StateProvince'];//StateProvince
            $searchcountry = $userLastSearch['SelectionCountry'];//SelectionCountry
            $panelist_ids = $userLastSearch['Panelists'];//Panelists
            $searchownbiz = $userLastSearch['Owner'];//Owner
            $mtypes =  explode(';', $userLastSearch['pTypes']);//pTypes
            $noread = $userLastSearch['HideMarkedRead'];//HideMarkedRead
            $panelist_core_search = explode(',', $userLastSearch['Flag']);//Flag

            $start_m = ($userLastSearch['FromDateM'] != '00')?$userLastSearch['FromDateM']:'';//FromDateM
            $start_d = ($userLastSearch['FromDateD'] != '00')?$userLastSearch['FromDateD']:'';//FromDateD
            $start_y = ($userLastSearch['FromDateY'] != '0000')?$userLastSearch['FromDateY']:'0000';//FromDateY
            $end_m = ($userLastSearch['ToDateM'] != '00')?$userLastSearch['ToDateM']:'';//ToDateM
            $end_d = ($userLastSearch['ToDateD'] != '00')?$userLastSearch['ToDateD']:'';//ToDateD
            $end_y = ($userLastSearch['ToDateY'] != '0000')?$userLastSearch['ToDateY']:'';//ToDateY

            $hy = $userLastSearch['searchPartition'];//searchPartition
            if ($userLastSearch['cType']==1) {
                $panelist_core_options = array('ID'=>'ID','PT'=>'PT','PN'=>'PN');
            }
        if (isset($_REQUEST['search'])) {
            $_REQUEST['searchName'] = $userLastSearch['searchName'];
        }else{
          $_REQUEST['searchName'] = $userLastSearch['searchName'];
        }
        //echo '<pre>';	print_r($panelist_core_search);
				$_SESSION['searchid'] = $searchid;
		    $_SESSION['searchsubj'] = $searchsubj;
		    $_SESSION['searchsender'] = $searchsender;
		    $_SESSION['searchbody'] = $searchbody;
		    $_SESSION['searchtext'] = $searchtext;
		    $_SESSION['searchstate'] = $searchstate;
		    $_SESSION['searchcountry'] = $searchcountry;
		    $_SESSION['panelist_ids'] = $panelist_ids;
		    $_SESSION['searchownbiz'] = $searchownbiz;
		    $_SESSION['mtypes'] = $mtypes;
		    $_SESSION['noread'] = $noread;
		    $_SESSION['panelist_core_search'] = $panelist_core_search;

		    $_SESSION['start_m'] = $start_m;
		    $_SESSION['start_d'] = $start_d;
		    $_SESSION['start_y'] = $start_y;
		    $_SESSION['end_m'] = $end_m;
		    $_SESSION['end_d'] = $end_d;
		    $_SESSION['end_y'] = $end_y;

        $_SESSION['hy'] = $hy;
    }

    //die(print_r($panelist_core_search));
}

//echo '<pre>';print_r($_REQUEST);die;
if (checkGroup(20) && isset($_POST['sendsearch'])) {
    //echo '<pre>';print_r($_REQUEST);die;
    ###### for remove the ocr search options #####################
//        if(!in_array($_SESSION['_auth_COMPETI']['data']['userID'],$show_search_user)){
//            $_POST['searchtext']='';
//        }
    $DRW->query("DELETE FROM cscan_search_email WHERE uid={$AUTH_DATA['userID']}", $DRW_main);
    if (isset($_REQUEST['clear'])) {
      $qString = ['page' => 0, 'ctype' => $ctype];
      $qString = http_build_query($qString);
      header("Location: {$_SERVER['PHP_SELF']}?{$qString}");
    }
    if (!isset($_POST['clear'])) {
        if (isset($_POST['searchid'])) {
            $searchid = (int)$_POST['searchid'];
        }
        if (isset($_POST['searchsubj'])) {
            $searchsubj = (int)$_POST['searchsubj'];
        }
        if (isset($_POST['searchsender'])) {
            $searchsender = (int)$_POST['searchsender'];
        }
        if (isset($_POST['searchbody'])) {
            $searchbody = (int)$_POST['searchbody'];
        }
        $searchtext = trim($_POST['searchtext']);
        $searchstate = (int)$_POST['searchstate'];
        $searchcountry = $_POST['searchcountry'];
        $panelist_ids = trim($_POST['panelist_ids']);
        $searchownbiz = (int)$_POST['searchownbiz'];
        if ($searchtext!='' && !$searchsubj && !$searchsender && !$searchbody && !$searchid) {
            $searchsubj = 1;
            $searchbody = 1;
        }
        if (isset($_POST['mtypes'])) {
            $mtypes = $_POST['mtypes'];
        }
        if (isset($_POST['noread'])) {
            $noread = (int)$_POST['noread'];
        }
        foreach ($panelist_core_options as $val=>$option) {
            if (isset($_POST[$val])) {
                $panelist_core_search[] = $val;
            }
        }
        $start_m = $_POST['start_m'];
        $start_d = $_POST['start_d'];
        $start_y = $_POST['start_y'];
        $end_m = $_POST['end_m'];
        $end_d = $_POST['end_d'];
        $end_y = $_POST['end_y'];
        $hy = $_POST['hy'];

        //$postArray to save user search
        //@Author : Pradeep Kumar
        //Dated : Oct 12, 2019
        //***************************************************start
        //echo '<pre>';print_r($_REQUEST);die;
        $panelistOptions = [];
        foreach ($panelist_core_options as $panelistKey => $panelistValue) {
            if (isset($_REQUEST[$panelistKey])) {
                $panelistOptions[] = $panelistKey;
            }
        }
        $searchDataToSave = [];
        foreach (array_flip($postArrary) as $postKey => $dataKey) {
            if (isset($_REQUEST[$postKey])) {
                $searchDataToSave[$dataKey] = $_REQUEST[$postKey];
            } else {
                $searchDataToSave[$dataKey] = null;
            }
        }
        if (isset($_REQUEST['mtypes'])) {
            $searchDataToSave['pTypes'] = implode(';', $_REQUEST['mtypes']);
        }
        $searchDataToSave['userID'] = $AUTH_DATA['userID'];
        $panelist_core_search = $panelistOptions;
        $searchDataToSave['Flag'] = implode(',', $panelistOptions);
        $searchDataToSave['searchSave'] = 0;

        if(isset($_REQUEST['rid'])){
          $_REQUEST['searchName'] = $userLastSearch['searchName'];
          //echo '<pre>';print_r($_REQUEST);die;
        }else{
          $searchDataToSave['searchName'] = '';
        }
        if (isset($_REQUEST['saved']) && $_REQUEST['saved'] == 1 && isset($_REQUEST['ssid']) && $_REQUEST['ssid'] > 0) {
            unset($searchDataToSave['searchName']);
            unset($searchDataToSave['searchSave']);
        }
        $searchSaveQuery = [];



        $searchSId = 0;
        if (isset($_REQUEST['rid']) && $_REQUEST['rid'] > 0){
          $searchSId = $_REQUEST['rid'];
        }
        if (isset($_REQUEST['ssid']) && $_REQUEST['ssid'] > 0){
          $searchSId = $_REQUEST['ssid'];
        }
        $searchName = isset($_REQUEST['searchName'])? $_REQUEST['searchName']:'';
        if ($searchSId == 0){
          //echo '<pre>';print_r($_REQUEST);die;
          if(isset($_REQUEST['searchName']) && $_REQUEST['searchName'] != '') {
            $S = "SELECT * FROM cscan_content_site_search WHERE userID={$AUTH_DATA['userID']} AND searchName='{$_REQUEST['searchName']}';";
            $searchResorceUserBasedUnique = $DRW->query($S, $DRW_read2);
            if ($searchResorceUserBasedUnique->num_rows > 0) {
                $searchResorceUserBasedUniqueMessage = 1;
                $_REQUEST['search'] = 1;
            } else {
                $searchResorce = $DRW->query("SELECT ID FROM cscan_content_site_search WHERE userID={$AUTH_DATA['userID']} AND searchSave=0 AND cType={$ctype};", $DRW_read2);
                if ($searchResorce->num_rows > 0) {
                  $SID = $DRW->fetch_row($searchResorce)[0];
                  $searchDataToSave['searchName'] = $searchName;
                  $searchDataToSave['searchSave'] = 1;
                  $searchDataToSave['createdAt'] = date('Y-m-d H:i:s');
                  foreach ($searchDataToSave as $dataKey => $dataValue) {
                      $searchSaveQuery[] ="{$dataKey}='{$dataValue}'";
                  }


                //$searchResorce = $DRW->query("SELECT ID FROM cscan_content_site_search WHERE userID={$AUTH_DATA['userID']} AND searchSave=1 AND cType={$ctype} AND ID !={$searchSId} AND searchName='{$searchName}';", $DRW_read2);
                //if (!empty($searchResorce) && $searchResorce->num_rows == 0) {
                  $searchSaveQueryUpdate = 'UPDATE cscan_content_site_search SET '.implode(', ', $searchSaveQuery)." WHERE ID={$SID};";
                  $DRW->query($searchSaveQueryUpdate, $DRW_main);
                  $qString = ['page' => 0, 'ctype' => $ctype, 'rid' => $_REQUEST['ssid'], 'search' => 1];
                  $qString = http_build_query($qString);

        				  header("Location: {$_SERVER['PHP_SELF']}?{$qString}");
                }else{
                  $searchResorceUserBasedUniqueMessage = 3;
                  $_REQUEST['search'] = 1;
                }
            }
          } else {
              //$searchResorceUserBasedUniqueMessage = 2;
              $searchResorce = $DRW->query("SELECT ID FROM cscan_content_site_search WHERE userID={$AUTH_DATA['userID']} AND searchSave=0 AND cType={$ctype};", $DRW_read2);
              foreach ($searchDataToSave as $dataKey => $dataValue) {
                  $searchSaveQuery[] ="{$dataKey}='{$dataValue}'";
              }
              if (!empty($searchResorce) && $searchResorce->num_rows) {
                  $searchSaveQueryUpdate = 'UPDATE cscan_content_site_search SET '.implode(', ', $searchSaveQuery)." WHERE ID={$DRW->fetch_row($searchResorce)[0]};";
                  $DRW->query($searchSaveQueryUpdate, $DRW_main);
              } else {
                  $searchSaveQuery = 'INSERT INTO cscan_content_site_search SET '.implode(', ', $searchSaveQuery).';';
                  $DRW->query($searchSaveQuery, $DRW_main);
              }
          }

        }else{
          //doe code
          //echo '<pre>';print_r($_REQUEST);die;
          if(isset($_REQUEST['searchName']) && $_REQUEST['searchName'] != '') {
            $S = "SELECT * FROM cscan_content_site_search WHERE userID={$AUTH_DATA['userID']} AND ID !={$searchSId} AND searchName='{$_REQUEST['searchName']}';";
            $searchResorceUserBasedUnique = $DRW->query($S, $DRW_read2);
            if ($searchResorceUserBasedUnique->num_rows > 0) {
                if(isset($_REQUEST['saveIt'])) $searchResorceUserBasedUniqueMessage = 1;
                $_REQUEST['search'] = 1;
            } else {
                $searchResorce = $DRW->query("SELECT ID FROM cscan_content_site_search WHERE userID={$AUTH_DATA['userID']} AND searchSave=1 AND cType={$ctype} AND ID !={$searchSId} AND searchName='{$searchName}';", $DRW_read2);
                if ($searchResorce->num_rows == 0) {
                  $SID = $DRW->fetch_row($searchResorce)[0];
                  $searchDataToSave['searchName'] = $searchName = $_REQUEST['searchName'];
                  $searchDataToSave['searchSave'] = 1;
                  foreach ($searchDataToSave as $dataKey => $dataValue) {
                      $searchSaveQuery[] ="{$dataKey}='{$dataValue}'";
                  }
                  if(isset($_REQUEST['saveIt'])){
                    $searchSaveQueryUpdate = 'UPDATE cscan_content_site_search SET '.implode(', ', $searchSaveQuery)." WHERE ID={$searchSId};";
                    if($DRW->query($searchSaveQueryUpdate, $DRW_main) && isset($_REQUEST['saveIt'])){
                      $qString = ['page' => 0, 'ctype' => $ctype, 'rid' => $_REQUEST['ssid']];
                      $qString = http_build_query($qString);
            				  header("Location: {$_SERVER['PHP_SELF']}?{$qString}");
                    }
                  }else{
                    $searchResorce = $DRW->query("SELECT ID FROM cscan_content_site_search WHERE userID={$AUTH_DATA['userID']} AND searchSave=0 AND cType={$ctype};", $DRW_read2);
                    if (!empty($searchResorce) && $searchResorce->num_rows) {
                      $searchSId = $DRW->fetch_row($searchResorce)[0];
                      $searchDataToSave['searchName'] = null;
                      $searchDataToSave['searchSave'] = 0;
                      $searchSaveQuery = [];
                      foreach ($searchDataToSave as $dataKey => $dataValue) {
                          $searchSaveQuery[] ="{$dataKey}='{$dataValue}'";
                      }
                      $searchSaveQueryUpdate = 'UPDATE cscan_content_site_search SET '.implode(', ', $searchSaveQuery)." WHERE ID={$searchSId};";
                      $DRW->query($searchSaveQueryUpdate, $DRW_main);
                    }
                  }
                }else{
                  if(isset($_REQUEST['saveIt'])) $searchResorceUserBasedUniqueMessage = 3;
                  $_REQUEST['search'] = 1;
                }
            }
          } else {
              $searchResorceUserBasedUniqueMessage = 2;
              $_REQUEST['search'] = 1;
          }
        }

      }
      //echo '<pre>';print_r($searchSaveQuery);die;
      if(isset($searchSavedDataFlag)){
        $ID = isset($_REQUEST['ssid'])?$_REQUEST['ssid']:(isset($_REQUEST['rid'])?$_REQUEST['rid']:0);
        $qString = ['page' => 0, 'ctype' => $ctype, 'search' => 1];
        if($ID > 0){
          $qString['rid'] = $ID;//$DRW->insert_id($DRW_main);
        }
        $qString = http_build_query($qString);
        header("Location:{$_SERVER['PHP_SELF']}?{$qString}");
      }
    //echo '<pre>';print_r($searchSaveQuery);die;
    //********************************end**********************

    $_SESSION['searchid'] = $searchid;
    $_SESSION['searchsubj'] = $searchsubj;
    $_SESSION['searchsender'] = $searchsender;
    $_SESSION['searchbody'] = $searchbody;
    $_SESSION['searchtext'] = $searchtext;
    $_SESSION['searchstate'] = $searchstate;
    $_SESSION['searchcountry'] = $searchcountry;
    $_SESSION['panelist_ids'] = $panelist_ids;
    $_SESSION['searchownbiz'] = $searchownbiz;
    $_SESSION['mtypes'] = $mtypes;
    $_SESSION['noread'] = $noread;
    $_SESSION['panelist_core_search'] = $panelist_core_search;

    $start_m = ($start_m != '00')?$start_m:'';
    $start_d = ($start_d != '00')?$start_d:'';
    $start_y = ($start_y != '0000')?$start_y:'0000';
    $end_m = ($end_m != '00')?$end_m:'';
    $end_d = ($end_d != '00')?$end_d:'';
    $end_y = ($end_y != '0000')?$end_y:'';

    $_SESSION['start_m'] = $start_m;
    $_SESSION['start_d'] = $start_d;
    $_SESSION['start_y'] = $start_y;
    $_SESSION['end_m'] = $end_m;
    $_SESSION['end_d'] = $end_d;
    $_SESSION['end_y'] = $end_y;

    $_SESSION['hy'] = $hy;
    // if(isset($_REQUEST['ssid']) && $_REQUEST['ssid'] > 0){
    // 	header('Location:imapSavedSearch.php');
    // }

    setcookie('competiscan_esearch3', implode(';', $mtypes).",$noread,".implode(';', $panelist_core_search), time()+(86400*364), $COOKIEPATH, $COOKIEDOMAIN);
} else {
    if (isset($_COOKIE['competiscan_esearch3'])) {
        $temp_cookie = explode(',', $_COOKIE['competiscan_esearch3']);
        list($mtypes_tmp, $noread, $panelist_core_search_tmp) = $temp_cookie;
        if (!empty($mtypes_tmp)) {
            $mtypes = explode(';', $mtypes_tmp);
        }
        if (!empty($panelist_core_search_tmp)) {
            $panelist_core_search = explode(';', $panelist_core_search_tmp);
        }
    }
    if (isset($_SESSION['searchid'])) {
        $searchid = $_SESSION['searchid'];
    }
    if (isset($_SESSION['searchsubj'])) {
        $searchsubj = $_SESSION['searchsubj'];
    }
    if (isset($_SESSION['searchsender'])) {
        $searchsender = $_SESSION['searchsender'];
    }
    if (isset($_SESSION['searchbody'])) {
        $searchbody = $_SESSION['searchbody'];
    }
    if (isset($_SESSION['searchtext'])) {
        $searchtext = $_SESSION['searchtext'];
    }
    if (isset($_SESSION['searchstate'])) {
        $searchstate = $_SESSION['searchstate'];
    }
    if (isset($_SESSION['searchcountry'])) {
        $searchcountry = $_SESSION['searchcountry'];
    }
    if (isset($_SESSION['panelist_ids'])) {
        $panelist_ids = $_SESSION['panelist_ids'];
    }
    if (isset($_SESSION['searchownbiz'])) {
        $searchownbiz = $_SESSION['searchownbiz'];
    }
    if (isset($_SESSION['mtypes'])) {
        $mtypes = $_SESSION['mtypes'];
    }
    if (isset($_SESSION['noread'])) {
        $noread = $_SESSION['noread'];
    }
    if (isset($_SESSION['panelist_core_search'])) {
        $panelist_core_search = $_SESSION['panelist_core_search'];
    }
    if (isset($_SESSION['start_m'])) {
        $start_m = $_SESSION['start_m'];
    }
    if (isset($_SESSION['start_d'])) {
        $start_d = $_SESSION['start_d'];
    }
    if (isset($_SESSION['start_y'])) {
        $start_y = $_SESSION['start_y'];
    }
    if (isset($_SESSION['end_m'])) {
        $end_m = $_SESSION['end_m'];
    }
    if (isset($_SESSION['end_d'])) {
        $end_d = $_SESSION['end_d'];
    }
    if (isset($_SESSION['end_y'])) {
        $end_y = $_SESSION['end_y'];
    }
    if (isset($_SESSION['hy'])) {
        $hy = $_SESSION['hy'];
    }
}


//echo '<pre>';print_r($_SESSION);die;
$used = '';
if (checkGroup(20)) {
    $dels = array();
    foreach ($mtypes as $m) {
        //$dels[] = '`deleted`='.$m;
        ########### convert or clause itno in clause #############
        $dels[] = $m;
    }
    if (count($dels)>0) {
        //$used .= ' AND ('.implode(' OR ',$dels).')';
        ########### convert or clause itno in clause #############
        $used .= ' AND deleted in('.implode(',', $dels).')';
    }
    if ($noread) {
        $used .= ' AND `email_read`<>1';
    }
    if (count($panelist_core_search)>0) {
        $used .= " AND (panelist_core='".implode("' OR panelist_core='", $panelist_core_search)."')";
    }
} else {
    $used .= ' AND `deleted`=0 AND `panelist_score`>0';
}

$join = '';
if ($searchtext!='') {
    $searchtext_like = mysqlLike($searchtext);
    $swhere = '';
    if ($searchid) {
        if ($swhere!='') {
            $swhere .= ' OR ';
        }
        $swhere .= "ce.`muid`='".$DRW->real_escape_string($searchtext)."'";
    }

    if ($searchsender) {
        $vs = explode(',', $searchtext);
        $ors = array();
        foreach ($vs as $v) {
            $v = trim($v);
            if (!empty($v)) {
                if ($swhere!='') {
                    $swhere .= ' OR ';
                }
                $v = mysqlLike($v);
                $swhere .= "`email_from_one` LIKE '".$v."%'";
            }
        }
    }
    if ($searchbody || $searchsubj) {
        $search_id = session_id();
        $count_save_sql = "SELECT SQL_NO_CACHE COUNT(*) FROM cscan_search_email WHERE sid='$search_id' AND uid={$AUTH_DATA['userID']}";
        $rs = $DRW->query($count_save_sql, $DRW_read2);
        $data = $DRW->fetch_row($rs);
        $numrow = (int) $data[0];
        if ($numrow==0 && !empty($SPHINX_name)) {
            $s = startSphinx('cetactive');
            if (!empty($hy)) {
                $inds = 'base_index_'.$SPHINX_name.'_e'.$hy;
            } else {
                $inds = 'base_index_'.$SPHINX_name.'_e,delta_index_'.$SPHINX_name.'_e';
            }
            $ps = parseSphinx($s, $searchtext);
            if (trim($ps)!='') {
                $currcount = 0;
                $step = $total = 20000;
                $s->setLimits(0, 1, 1);
                if ($searchbody && !$searchsubj) {
                    $ps = '@cettext '.$ps;
                }
                if (!$searchbody && $searchsubj) {
                    $ps = '@email_subject '.$ps;
                }
                $result = $s->query($ps, $inds);
                // $result['total_found'];
                // echo $result['total_found'].'total result';exit;
                if (isset($result['matches'])) {
                    $total = (float)$result['total_found'];
                    $count = 0;
                    $minID = 0;
                    $count_save_sql = "SELECT MAX(cetid) FROM cscan_email_text$hy";
                    $rs = $DRW->query($count_save_sql, $DRW_read2);
                    $data = $DRW->fetch_row($rs);
                    $maxID = $data[0];
                    for ($offset=0;$offset<=$maxID;$offset+=$step) {
                        $s = startSphinx('cetactive');
                        $s->setLimits(0, $step, $step);
                        $s->setIDRange($minID+1, $maxID);
                        $result = $s->query($ps, $inds);
                        //echo"<pre>";
                        //print_r($result['matches']);exit;
                        if (isset($result['matches'])) {
                            foreach ($result['matches'] as $dts_id=>$match) {
                                $query = "REPLACE INTO cscan_search_email (uid,sid,muid) VALUES ({$AUTH_DATA['userID']},'$search_id',{$match['attrs']['muid']})";
                                $DRW->query($query, $DRW_main);

                                $minID = $dts_id;
                                $currcount++;
                            }
                            if ($currcount>=$total) {
                                break;
                            }
                        }
                        $err = $s->getLastError();
                        $war = $s->getLastWarning();

                        if (!empty($err) || !empty($war)) {
                            break;
                        }
                        // note that total_found using setLimits(0,1,1) is not always the same as without limits (bug in Sphinx?)
                        if (!isset($result['matches'])) {
                            break;
                        }
                    }
                }
            }
        }
        $join .= ' JOIN cscan_search_email se ON(se.sid=\''.$search_id.'\' AND se.uid='.$AUTH_DATA['userID'].' AND ce.muid=se.muid)';
    }
    if ($swhere!='') {
        $cwhere .= " AND ($swhere)";
    }
}
if (!empty($searchcountry)) {
    $countryStates = '';
    $sqlc = "SELECT stateID FROM cscan_state WHERE countryCode='".$DRW->real_escape_string($searchcountry)."'";
    $rsc = $DRW->query($sqlc, $DRW_read2);
    ########### convert or clause itno in clause #############
    $stateArray =   array();
    while ($rowc = $DRW->fetch_row($rsc)) {
        $countryStates .= " OR ce.email_stateID=".$rowc[0]."";
        ########### convert or clause itno in clause #############
        $stateArray[]   =   $rowc[0];
    }
    ########### convert or clause itno in clause #############
    if (!empty($stateArray)) {
        $cwhere .=" AND ce.email_stateID in( ". implode(",", $stateArray). ")";
    }

    //	if($countryStates!=''){
//		$cwhere .= " AND (".substr($countryStates,4).")";
//	}
}
if ($searchstate!=0) {
    $cwhere .= " AND ce.email_stateID=$searchstate";
}
if ($searchownbiz!=-1 || !empty($panelist_ids)) {
    $join .= ' JOIN cscan_panelists pp ON(ce.panelist_id=pp.panelist_id)';
    if ($searchownbiz!=-1) {
        $cwhere .= " AND ownbiz=$searchownbiz";
    }
    if (!empty($panelist_ids)) {
        $vs = explode(',', $panelist_ids);
        $ors = array();
        foreach ($vs as $v) {
            $v = trim($v);
            if (!empty($v)) {
                $v = $DRW->real_escape_string($v);
                $ors[] = "(competi_id LIKE '".$v."%')";
            }
        }
        $cwhere .= " AND (".implode(' OR ', $ors).")";
    }
}
if ($start_y!='0000') {
    $cwhere .= " AND `email_date`>='".$DRW->real_escape_string("$start_y-$start_m-$start_d")." 00:00:00'";
    $cwhere .= " AND `email_date`<='".$DRW->real_escape_string("$end_y-$end_m-$end_d")." 23:59:59'";
}

?>
<div style="float:right;"><form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="changeadmin" style="display:inline;"><strong class="bodytext">Assigned User:</strong> <select class="combo_box" name="e_assigned_admin_userID" onchange="document.changeadmin.submit();"><option value="0">All</option>
<?php
$useroptions = array();
$sql = "select userID,userName,is_email_assign_queue,is_email_assign_queue2 from cscan_admin_users WHERE user_status=1 ORDER BY userName";
$rs = $DRW->query($sql, $DRW_read2);
while ($row = $DRW->fetch_row($rs)) {
    print "<option value = \"$row[0]\"";
    if ($row[0]==$e_assigned_admin_userID) {
        print " selected=\"selected\"";
    }
    print ">";
    if ($row[2]) {
        print '(p) ';
    }
    if ($row[3]) {
        print '(c) ';
    }
    print "$row[1]</option>";
    $useroptions[$row[0]] = $row[1];
}
?></select>
</form>
</div>
</div>
<div style="clear:both;height:5px;">&nbsp;</div>
<?php

print "<form name=\"masser\" method=\"post\" action=\"{$_SERVER['PHP_SELF']}?ctype={$ctype}\" onsubmit=\"do_mupdate(); return false;\"><input type=\"hidden\" name=\"ctype\" value=\"{$ctype}\" />
<div style=\"margin:0px;padding:0px;border:solid 1px #0055E3;\">
<table width=\"100%\" border=\"0\" cellpadding=\"4\" cellspacing=\"0\" class=\"likeresults\">";
print "<tr><td style=\"background:#0055E3;color:#ffffff;\" class=\"text\" valign=\"bottom\"><input type=\"checkbox\" name=\"mark_all\" value=\"1\" onclick=\"mark_all_click();\" /></td><td style=\"background:#0055E3;color:#ffffff;\" class=\"text\" valign=\"bottom\">";
if ($sort!=7) {
    print " <a href=\"{$_SERVER['PHP_SELF']}?sort=7\" class=\"topLinks\">ID</a>";
} else {
    print '<span style="text-decoration:underline;" class="topLinks">ID</span>';
    $orderby = ' ORDER BY `muid` DESC';
}
print "</td>";
print "<td style=\"background:#0055E3;color:#ffffff;\" class=\"text\" valign=\"bottom\"><span class=\"topLinks\">Status</span></td><td style=\"background:#0055E3;color:#ffffff;\" class=\"text\" valign=\"bottom\">";
if ($sort!=2) {
    print " <a href=\"{$_SERVER['PHP_SELF']}?sort=2\" class=\"topLinks\">Subject</a>";
} else {
    print '<span style="text-decoration:underline;" class="topLinks">Subject</span>';
    $orderby = ' ORDER BY `email_subject` ASC,`email_date` DESC';
}
print '</td><td style="background:#0055E3;color:#ffffff;" class="text" valign="bottom"><span class="topLinks">Files</span></td><td style="background:#0055E3;color:#ffffff;" class="text" valign="bottom">';
if ($sort!=3) {
    print " <a href=\"{$_SERVER['PHP_SELF']}?sort=3\" class=\"topLinks\">Sender Email</a>";
} else {
    print '<span style="text-decoration:underline;" class="topLinks">Sender Email</span>';
    $orderby = ' ORDER BY `email_from_one` ASC,`email_date` DESC';
}
print "</td><td style=\"background:#0055E3;color:#ffffff;\" class=\"text\" valign=\"bottom\">";
if ($sort!=9) {
    print " <a href=\"{$_SERVER['PHP_SELF']}?sort=9\" class=\"topLinks\">Flag</a>";
} else {
    print '<span style="text-decoration:underline;" class="topLinks">Flag</span>';
    $orderby = ' ORDER BY `panelist_core` DESC,`email_date` DESC';
}
print "</td><td style=\"background:#0055E3;color:#ffffff;\" class=\"text\" valign=\"bottom\">";
if ($sort!=4) {
    print " <a href=\"{$_SERVER['PHP_SELF']}?sort=4\" class=\"topLinks\">Points</a>";
} else {
    print '<span style="text-decoration:underline;" class="topLinks">Points</span>';
    $orderby = ' ORDER BY `panelist_score` DESC,`email_date` DESC';
}
print "</td><td style=\"background:#0055E3;color:#ffffff;\" class=\"text\" valign=\"bottom\">";
if ($sort!=5) {
    print " <a href=\"{$_SERVER['PHP_SELF']}?sort=5\" class=\"topLinks\">Date</a>";
} else {
    print '<span style="text-decoration:underline;" class="topLinks">Date</span>';
    $orderby = ' ORDER BY `email_date` DESC';
}
print "</td><td style=\"background:#0055E3;color:#ffffff;\" class=\"text\" valign=\"bottom\">";
if ($sort!=8) {
    print " <a href=\"{$_SERVER['PHP_SELF']}?sort=8\" class=\"topLinks\">Mark</a>";
} else {
    print '<span style="text-decoration:underline;" class="topLinks">Mark</span>';
    $orderby = ' ORDER BY `email_read` ASC,`email_date` DESC';
}
print '</td></tr>';
/* added $join2 by pradeep */
//$join2=" JOIN cscan_email_text$hy et ON (ce.muid=et.muid) ";
if ($hy=='') {
    $cwhere .= ' AND is_text_file=1 ';
}
 $q = " FROM `cscan_email$hy` ce$join WHERE 1=1$used$cwhere";
 //echo "SELECT COUNT(DISTINCT ce.`muid`) $q";
 //echo "SELECT DISTINCT ce.`muid`,DATE_FORMAT(`email_date`,'%m/%d/%y<br />%l:%i %p'),`email_to`,`email_from`,`email_subject`,`contact_type_m_c`,`deleted`,`panelist_score`,`email_read`,`email_from_one`,ce.`panelist_id`,panelist_core $q$orderby";// SQL_NO_CACHE
//exit;
$count_result = $DRW->query("SELECT COUNT(DISTINCT ce.`muid`) $q", $DRW_read2);// SQL_NO_CACHE
$data = $DRW->fetch_row($count_result);
$rows = $data[0];
list($limittext, $pagingtext) = showPaging($_SERVER['PHP_SELF'], $rows, $page, $limshow);

$muids = array();
$hiddentext = '';
if ($rows>0) {
    $query = "SELECT DISTINCT ce.`muid`,DATE_FORMAT(`email_date`,'%m/%d/%y<br />%l:%i %p'),`email_to`,`email_from`,`email_subject`,`contact_type_m_c`,`deleted`,`panelist_score`,`email_read`,`email_from_one`,ce.`panelist_id`,panelist_core $q$orderby$limittext";// SQL_NO_CACHE
    $query_result = $DRW->query($query, $DRW_read2);
    while ($data = $DRW->fetch_row($query_result)) {
        $muid = $data[0];

        /* check that email_text table contains this muid
         * Changes by Pradeep
         *  */
        //echo "SELECT COUNT(*) from cscan_email_text$hy WHERE muid='".$DRW->real_escape_string($muid)."'";
        $query2 = $DRW->query("SELECT COUNT(*) from cscan_email_text$hy WHERE muid='".$DRW->real_escape_string($muid)."'", $DRW_read2);
        $data2 = $DRW->fetch_row($query2);
        $found_count = $data2[0];
        $query3 = $DRW->query("SELECT COUNT(*) from cscan_email_file$hy WHERE muid='".$DRW->real_escape_string($muid)."'", $DRW_read2);
        $data3 = $DRW->fetch_row($query3);
        $found_count2 = $data3[0];
        if ($found_count>0 || $found_count2>0 || $hy==201501) {

       /* Changes by Pradeep  End */
            $email_date = $data[1];
            $email_to = $data[2];
            $email_from = $data[3];
            $email_subject = $data[4];
            $contact_type_m_c = $data[5];
            $deleted = $data[6];
            $panelist_score = $data[7];
            $email_read = $data[8];
            $email_from_one = $data[9];
            $panelist_id = $data[10];
            $panelist_core = $data[11];

            $result = $DRW->query("SELECT SQL_NO_CACHE COUNT(*) FROM cscan_email_file$hy WHERE muid=$muid", $DRW_read2);
            $data2 = $DRW->fetch_row($result);
            $count = $data2[0];

            $result = $DRW->query("SELECT SQL_NO_CACHE COUNT(*) FROM cscan_product_email WHERE muid=$muid AND isTmp=0", $DRW_read2);
            $data2 = $DRW->fetch_row($result);
            $countp = $data2[0];

            if ($count>0) {
                $attachment = 'Yes';
            } else {
                $attachment = 'No';
            }

            if ($panelist_id!=0) {
                $result = $DRW->query("SELECT sugar_id,first_name,last_name,competi_id,email,alt_email,panelist_id
				FROM cscan_panelists WHERE panelist_id=$panelist_id", $DRW_read2);
                $data2 = $DRW->fetch_row($result);
                if ($data2[0]!='') {
                    $id = $data2[0];
                    $first_name = $data2[1];
                    $last_name = $data2[2];
                    $competi_id = $data2[3];
                    $email1 = trim($data2[4]);
                    $email2 = trim($data2[5]);
                    if(isset($_SESSION['ctype']) && $_SESSION['ctype']=='2'){
                        $email_from = $competi_id;
                    }else{
                        $email_from = $first_name.' '.$last_name;
                        if ($competi_id!='') {
                            $email_from .= ' ('.$competi_id.')';
                        }
                    }
                    
                    if (checkGroup(20)) {
                        $email1 = "<a href=\"http://crm.competiscan.com/index.php?action=DetailView&amp;module=Contacts&amp;record=$id\" class=\"bluelink\" target=\"_blank\">$email1</a>";
                    }
                    $email_from .= ' &lt;'.$email1.'&gt;';
                } else {
                    $email_from = htmlspecialchars($email_from);
                }
            } else {
                $email_from = htmlspecialchars($email_from);
            }

            print "<tr><td valign=\"top\" class=\"bodytext\">";
            if ($deleted!=2 && $deleted!=4) {
                print "<input type=\"checkbox\" name=\"marked[]\" value=\"$muid\" />";
            } else {
                print '&nbsp;';
            }
            print "</td><td valign=\"top\" class=\"bodytext\">";
            if ($deleted==2) {
                //print "<a href=\"#\" onclick=\"document.forms.masser.copies_id.value='$muid'; return false;\" class=\"bluelink\">$muid</a>";
                $check_entry = "SELECT pd.entryId FROM cscan_product_email pe INNER JOIN cscan_product_detail pd on(pd.productID=pe.productID) where pe.muid=".$muid." AND isTmp=0 limit 1";
                $check_entry = $DRW->query($check_entry, $DRW_read2);
                $data_entry = $DRW->fetch_row($check_entry);
                $entryID_dup = $data_entry[0];
                if ($entryID_dup!='' and $entryID_dup!=0) {
                    $showMuid=$entryID_dup;
                } else {
                    $showMuid=$muid;
                }

                print "<a href=\"#\" onclick=\"document.forms.masser.copies_id.value='$showMuid'; return false;\" class=\"bluelink\">$muid</a>";
            } else {
                print $muid;
            }
            print "</td><td valign=\"top\" class=\"bodytext\" nowrap=\"nowrap\">";
            $muids[] = $muid;
            if ($countp==0) {
                print "<select name=\"processed$muid\" size=\"1\">";
                foreach ($messageTypes as $key=>$value) {
                    if ($value=='') {
                        $value = '&nbsp;';
                    }
                    print "<option value=\"$key\"";
                    if ($deleted==$key) {
                        print ' selected="selected"';
                    }
                    print ">$value</option>";
                }
                print "</select><a href=\"#\" onclick=\"do_mupdate(); return false;\" title=\"Update\"><img src=\"images/arrow.gif\" border=\"0\" alt=\"Update\" width=\"13\" height=\"13\" /></a>";
                $hiddentext .= "<input type=\"hidden\" name=\"old_processed$muid\" value=\"".htmlspecialchars($deleted, ENT_QUOTES)."\" />";
            } else {
                print 'Used';
            }
            print "</td><td valign=\"top\" class=\"bodytext\">";
            if (!$email_read) {
                print '<strong>';
            }
            print "<a href=\"email.php?muid=$muid&amp;hy=$hy\" class=\"bluelink\" name=\"muid$muid\">".htmlspecialchars($email_subject)."</a>";
            if (!$email_read) {
                print '</strong>';
            }
            print "&nbsp; <em>[<a href=\"#\" onclick=\"winPopMessage('showallmessage.php?muid=$muid&amp;hy=$hy'); return false;\" class=\"bluelink\">Peek</a>]</em>";
            print "</td><td valign=\"top\" class=\"bodytext\">$attachment</td><td valign=\"top\" class=\"bodytext\">$email_from</td>
		<td valign=\"top\" class=\"bodytext\" nowrap=\"nowrap\"><select name=\"panelist_core$muid\" size=\"1\"><option value=\"\">&nbsp;</option>";
            foreach ($panelist_core_options as $val=>$option) {
                print "<option value=\"$val\"";
                if ($val==$panelist_core) {
                    print ' selected="selected"';
                }
                print ">$option</option>";
            }
            $hiddentext .= "<input type=\"hidden\" name=\"old_panelist_core$muid\" value=\"".$panelist_core."\" />";
            print "</select></td>
		<td valign=\"top\" class=\"bodytext\" nowrap=\"nowrap\"><select name=\"panelist_score$muid\" size=\"1\" onchange=\"mark_points_changed();\">";
            //also change in panelist_score.php, panelist_report_iframe_month.phpand alter table cscan_crm_contacts_data
        if ($contact_type_m_c=='cons_panelist') {
            $options = array('0'=>'0','0.5'=>'0.5','1'=>'1','2'=>'2','3'=>'3','5'=>'5','10'=>'10');
        } //,'50'=>'50'
        else {
            $options = array('0'=>'0','1'=>'1');
        }
            foreach ($options as $val=>$option) {
                print "<option value=\"$val\"";
                if ($val==$panelist_score) {
                    print ' selected="selected"';
                }
                print ">$option</option>";
            }
            $hiddentext .= "<input type=\"hidden\" name=\"old_panelist_score$muid\" value=\"".intval($panelist_score)."\" /><input type=\"hidden\" name=\"panelist_id$muid\" value=\"".$panelist_id."\" />";
            print "</select></td><td valign=\"top\" class=\"bodytext\">$email_date</td>";
            print "<td valign=\"top\" class=\"bodytext\" nowrap=\"nowrap\">";
            print "<select name=\"read$muid\" size=\"1\">";
            foreach ($readTypes as $key=>$value) {
                print "<option value=\"$key\"";
                if ($email_read==$key) {
                    print ' selected="selected"';
                }
                print ">$value</option>";
            }
            print "</select><a href=\"#\" onclick=\"do_mupdate(); return false;\" title=\"Update\"><img src=\"images/arrow.gif\" border=\"0\" alt=\"Update\" width=\"13\" height=\"13\" /></a></td>";
            $hiddentext .= "<input type=\"hidden\" name=\"old_read$muid\" value=\"".htmlspecialchars($email_read, ENT_QUOTES)."\" />";
            print "</tr>";
        }
    }
} else {
    print "<tr><td colspan=\"10\" class=\"bodytext\"><i>None</i></td></tr>";
}
print "</table>
</div>";
if (count($muids)>0) {
    print "<div style=\"margin-top:4px;\" class=\"bodytext\">Assign Copies ID<input type=\"text\" name=\"copies_id\" id=\"copies_id\" size=\"10\" /> &nbsp;
    <input class=\"button\" type=\"submit\" name=\"update\" value=\"Update\" /></div>";
}
print "$hiddentext<input type=\"hidden\" name=\"sendmass\" value=\"1\" /><input type=\"hidden\" name=\"muids\" value=\"".implode(',', $muids)."\" /></form>";

print "<div style=\"margin-top:4px;\">$pagingtext</div>";

if (checkGroup(20)) {
    print "<div style=\"margin-top:4px;padding:2px;background-color:#E8E8FF;position:relative;\">";

    $searchQueryString = ['page' => 0];
    if (isset($_GET['ctype'])) {
        $searchQueryString['ctype'] = $_GET['ctype'];
    }
    if (isset($_GET['ssid'])) {
        $searchQueryString['ssid'] = $_GET['ssid'];
    }
    if (isset($_GET['rid'])) {
        $searchQueryString['rid'] = $_GET['rid'];
    }
    $queryString = http_build_query($searchQueryString);
    print "<form name=\"searcher\" method=\"post\" action=\"{$_SERVER['PHP_SELF']}?{$queryString}\"><table border=\"0\" cellpadding=\"4\" cellspacing=\"2\">";

    ###### for remove the ocr search options #####################
    //if(in_array($_SESSION['_auth_COMPETI']['data']['userID'],$show_search_user)){
    $searchQueryString['lstSrch'] = 1;
    $queryString = http_build_query($searchQueryString);
    print "<tr><td class=\"bodytext\"><strong>Search</strong></td><td><input type=\"text\" name=\"searchtext\" value=\"".htmlspecialchars($searchtext, ENT_QUOTES)."\" size=\"60\" /> <input class=\"button\" type=\"submit\" name=\"search\" value=\"Search\" /> <input class=\"button\" type=\"submit\" name=\"clear\" value=\"Clear\" />&nbsp;<a href=\"{$_SERVER['PHP_SELF']}?{$queryString}\" class=\"button last-search\">Last Search</a></td></tr>";
//         }else{
//            print "<tr><td class=\"bodytext\"><strong>Search</strong></td><td> <input class=\"button\" type=\"submit\" name=\"search\" value=\"Search\" /> <input class=\"button\" type=\"submit\" name=\"clear\" value=\"Clear\" /></td></tr>";
//         }

    print "<tr><td>&nbsp;</td><td class=\"bodytext\"><label><input type=\"checkbox\" name=\"searchid\" value=\"1\"";
    if ($searchid) {
        print ' checked="checked"';
    }
    print " />ID</label> &nbsp; <label><input type=\"checkbox\" name=\"searchsubj\" value=\"1\"";
    if ($searchsubj) {
        print ' checked="checked"';
    }
    print " />Subject</label> &nbsp; <label><input type=\"checkbox\" name=\"searchbody\" value=\"1\"";
    if ($searchbody) {
        print ' checked="checked"';
    }
    print " />Body</label> &nbsp; <label><input type=\"checkbox\" name=\"searchsender\" value=\"1\"";
    if ($searchsender) {
        print ' checked="checked"';
    }
    print " />Sender Email</label></td></tr>";
    print "<tr><td>&nbsp;</td><td class=\"bodytext\"><table border=\"0\" cellpadding=\"4\" cellspacing=\"2\">";
    print "<tr><td class=\"bodytext\">From</td><td><select name=\"start_m\" size=\"1\" class=\"textinput\" onchange=\"checkStart();\"><option value=\"00\">&nbsp;</option>";
    $month_name = array('01'=>"January",'02'=>"February",'03'=>"March",'04'=>"April",'05'=>"May",'06'=>"June",'07'=>"July",'08'=>"August",'09'=>"September",'10'=>"October",'11'=>"November",'12'=>"December");
    foreach ($month_name as $key=>$value) {
        print "<option value=\"$key\"";
        if ($key==$start_m) {
            print " selected=\"selected\"";
        }
        print ">$value ($key)</option>";
    }
    print "</select> <select name=\"start_d\" size=\"1\" class=\"textinput\" onchange=\"checkStart();\"><option value=\"00\">&nbsp;</option>";
    for ($i=1;$i<=31;$i++) {
        $day = str_pad($i, 2, '0', STR_PAD_LEFT);
        print "<option value=\"$day\"";
        if ($day==$start_d) {
            print " selected=\"selected\"";
        }
        print ">$day</option>";
    }
    $start_year = 2015;
    $to_year = (int)date('Y');
    print "</select> <select name=\"start_y\" size=\"1\" class=\"textinput\" onchange=\"checkStart();\"><option value=\"0000\">&nbsp;</option>";
    for ($i=$start_year;$i<=$to_year;$i++) {
        print "<option value=\"$i\"";
        if ($i==$start_y) {
            print " selected=\"selected\"";
        }
        print ">$i</option>";
    }
    print "</select></td></tr>
	<tr><td class=\"bodytext\">To</td><td><select name=\"end_m\" size=\"1\" class=\"textinput\" onchange=\"checkStart();\"><option value=\"00\">&nbsp;</option>";
    foreach ($month_name as $key=>$value) {
        print "<option value=\"$key\"";
        if ($key==$end_m) {
            print " selected=\"selected\"";
        }
        print ">$value ($key)</option>";
    }
    print "</select> <select name=\"end_d\" size=\"1\" class=\"textinput\" onchange=\"checkStart();\"><option value=\"00\">&nbsp;</option>";
    for ($i=1;$i<=31;$i++) {
        $day = str_pad($i, 2, '0', STR_PAD_LEFT);
        print "<option value=\"$day\"";
        if ($day==$end_d) {
            print " selected=\"selected\"";
        }
        print ">$day</option>";
    }
    print "</select> <select name=\"end_y\" size=\"1\" class=\"textinput\" onchange=\"checkStart();\"><option value=\"0000\">&nbsp;</option>";
    for ($i=$start_year;$i<=$to_year;$i++) {
        print "<option value=\"$i\"";
        if ($i==$end_y) {
            print " selected=\"selected\"";
        }
        print ">$i</option>";
    }
    print "</select></td></tr>";
    print "</table></td></tr>";
    print "<tr><td>&nbsp;</td><td class=\"bodytext\">";
    foreach ($messageTypes as $k=>$v) {
        if ($v=='') {
            $v = 'Blank';
        }
        print "<label><input type=\"checkbox\" name=\"mtypes[]\" value=\"$k\" ";
        if (in_array($k, $mtypes)) {
            print ' checked="checked"';
        }
        print " />Show $v</label> &nbsp; ";
    }
    print "</td></tr>";
    print "<tr><td>&nbsp;</td><td class=\"bodytext\"><label><input type=\"checkbox\" name=\"noread\" value=\"1\" ";
    if ($noread) {
        print ' checked="checked"';
    }
    print " />Hide Marked Read</label></td></tr>";
    print "<tr><td>&nbsp;</td><td class=\"bodytext\">Flag:";
    foreach ($panelist_core_options as $val=>$option) {
        print " &nbsp; <label><input type=\"checkbox\" name=\"$val\" value=\"1\" ";
        if (in_array($val, $panelist_core_search)) {
            print ' checked="checked"';
        }
        print " />$option</label>";
    }
    print "</td></tr>";
    print '<tr><td>&nbsp;</td><td class="bodytext">Panelists <input type="text" name="panelist_ids" size="70" value="'.htmlspecialchars($panelist_ids, ENT_COMPAT).'" /></td></tr>';
    print "<tr><td>&nbsp;</td><td class=\"bodytext\"><select name=\"searchstate\"><option value=\"0\">State/Province</option>";
    getstates($searchstate);
    print '</select> &nbsp; <label><input type="radio" name="searchcountry" value=""';
    if (empty($searchcountry)) {
        echo " checked=\"checked\"";
    }
    print ' />All</label>';
    $sql = "SELECT DISTINCT countryCode,country FROM cscan_state JOIN ISO31661_alpha2code ON (code=countryCode) ORDER BY country";
    $rs = $DRW->query($sql, $DRW_read2);
    while ($row = $DRW->fetch_row($rs)) {
        print ' <label><input type="radio" name="searchcountry" value="'.$row[0].'"';
        if ($searchcountry==$row[0]) {
            echo " checked=\"checked\"";
        }
        print ' />'.htmlspecialchars($row[1]).'</label>';
    }
    print '</td></tr>';
    print '<tr><td>&nbsp;</td><td class="bodytext"><label><input type="radio" name="searchownbiz" value="1"';
    if ($searchownbiz==1) {
        echo " checked=\"checked\"";
    }
    print ' />Business Owner</label> <label><input type="radio" name="searchownbiz" value="0"';
    if ($searchownbiz==0) {
        echo " checked=\"checked\"";
    }
    print ' />Non-Business Owner</label> <label><input type="radio" name="searchownbiz" value="-1"';
    if ($searchownbiz==-1) {
        echo " checked=\"checked\"";
    }
    print ' />Both</label></td></tr>';
    print "<tr><td>&nbsp;</td><td class=\"bodytext\">Partition:";

    $current_year = (int)date('Y');
    $first_half_year_partition = date('Y01');
    $into_second_half_of_year_where_first_half_partition_should_have_taken_place = (date("n") > 6) ? true : false;

    for ($y = $current_year; $y >= 2015; $y--) {
        $moreArray = array();

        if ($y == $current_year) {
            $moreArray[] = '';

            if ($into_second_half_of_year_where_first_half_partition_should_have_taken_place) {
                $moreArray[] = intval($y.'01');
            }
        } else {
            if ($y >= 2013) {
                $moreArray[] = intval($y.'07');
                $moreArray[] = intval($y.'01');
            } else {
                $moreArray[] = $y;
            }
        }

        foreach ($moreArray as $v) {
            print " &nbsp; <label><input type=\"radio\" name=\"hy\" value=\"$v\" ";
            if ($v==$hy) {
                print ' checked="checked"';
            }
            print " />";
            if (empty($v)) {
                echo 'Current';
            } else {
                echo $v;
            }
            print "</label>";
        }
    }
    print "</td></tr>";
    print "</table>
	<input type=\"hidden\" name=\"sendsearch\" value=\"1\" /></form>";
  if(isset($searchResorceUserBasedUniqueMessage) && $searchResorceUserBasedUniqueMessage == 3){
    $searchResorceUserBasedUniqueMessage = 1;
    $searchQueryString['search'] = 1;
  }
    if (isset($_REQUEST['search'])) {
        $searchQueryString['search'] = 1;
        $queryString = http_build_query($searchQueryString); ?>
		<div class="bodytext" style="float: right;right:0px;position: absolute;top:2px;">
		<form name="searchNameForm" action="<?php echo "{$_SERVER['PHP_SELF']}?{$queryString}"?>" method="post" onSubmit="return chk_search_name(document.searchNameForm.searchName.value,1);">
		<strong>Enter search name</strong>
		<input type="text" class="input_box" size="20" value="<?php echo isset($_REQUEST['searchName'])?$_REQUEST['searchName']:''; ?>" maxlength="40" name="searchName" />
		<input type="submit" name="submit" value="save" class="submitbutton" />
		<?php
            if (isset($searchResorceUserBasedUniqueMessage)) {
                echo $searchMessages[$searchResorceUserBasedUniqueMessage];
            } ?>
		</form>
		</div>
		<?php
    }
    print "</div>";

    print "<div style=\"margin-top:4px;\" class=\"bodytext\"><a href=\"filter.php\" class=\"bluelink\">Edit Filter Rules</a></div>";
}
print "<script type=\"text/JavaScript\">
<!--
function checkStart(){
	var startindex_d = document.searcher.start_d.selectedIndex;
	var startindex_m = document.searcher.start_m.selectedIndex;
	var startindex_y = document.searcher.start_y.selectedIndex;
	var endindex_d = document.searcher.end_d.selectedIndex;
	var endindex_m = document.searcher.end_m.selectedIndex;
	var endindex_y = document.searcher.end_y.selectedIndex;

	if(startindex_y>endindex_y){
		document.searcher.end_y.selectedIndex = startindex_y;
		endindex_y = startindex_y;
	}
	if(startindex_m>endindex_m && startindex_y==endindex_y){
		document.searcher.end_m.selectedIndex = startindex_m;
		endindex_m = startindex_m;
	}
	if(startindex_d>endindex_d && startindex_m==endindex_m && startindex_y==endindex_y){
		document.searcher.end_d.selectedIndex = startindex_d;
	}
}
function doWinSize(){
	var wintext = '';
	var screenH = 0;
	var screenW = 0;


	if (screen){
		if (screen.width) {
			screenW = screen.width;
		}
		if (screen.height) {
			screenH = screen.height;
		}
	}
	if(screenH>0 && screenW>0){
		screenW = screenW - 40;
		screenH = (screenH*.6) - 40;
		wintext = ', width='+screenW+', height='+screenH;
	}
	return wintext;
}
function winPopScore(winloc) {
	var wind = window.open(winloc,'winpop4','left=20, top=20, scrollbars=yes, resizable=yes, width=500, height=250,toolbar=no,location=no,menubar=no,status=no');
	wind.focus();
}
function winPopMessage(winloc) {
	var addtext = doWinSize();
	var wind = window.open(winloc,'winpop2','left=0, top=0, scrollbars=yes, resizable=yes, toolbar=yes,location=yes,menubar=yes'+addtext);
	wind.focus();
}
function mark_points_changed(){
	window.onbeforeunload = function () {
		return 'Continue without updating?';
	}
}
function do_mupdate(){
	window.onbeforeunload = null;
	document.masser.submit();
}
function mark_all_click(){
	var chex = false;
	if(document.masser.mark_all.checked){
		chex = true;
	}
	for(var i=0;i<document.masser['marked[]'].length;i++){
		document.masser['marked[]'][i].checked = chex;
	}
}
function chk_search_name(searchName, isSSID) {
	if(searchName.trim()=='') {
		alert('Search Name cannot be blank');
		document.searchNameForm.searchName.focus();
		return false;
	}
	if(isSSID != undefined){
		var input = document.createElement('input'),input1 = document.createElement('input'),input2 = document.createElement('input');
		input.setAttribute('type', 'hidden');
		input.setAttribute('name', 'searchName');
		input.setAttribute('value', searchName);
		document.searcher.appendChild(input);
		input1.setAttribute('type', 'hidden');
		input1.setAttribute('name', 'sendsearch');
		input1.setAttribute('value', '1');
		document.searcher.appendChild(input1);
    input2.setAttribute('type', 'hidden');
		input2.setAttribute('name', 'saveIt');
		input2.setAttribute('value', '1');
		document.searcher.appendChild(input2);
		document.searcher.submit();
		return false;
	}
	return true;
}
//-->
</script>";

function showPaging($link, $rowcnt=0, $limstart=0, $limiter=50, $show=10)
{
    if ($rowcnt>0) {
        $paging = '<table border="0" cellspacing="2" cellpadding="4">';
        $limit = " LIMIT $limstart,$limiter";
        if (strpos($link, '?')===false) {
            $link .= '?';
        } else {
            $link .= '&amp;';
        }

        $firstlink = '[First]';
        $prevlink = '[Prev]';
        $nextlink = '[Next]';
        $lastlink = '[Last]';
        $middlelinks = '';

        //first and previous only if not on first
        if ($limstart>0) {
            if ($limstart>=$limiter) {
                $prev = $limstart - $limiter;
            } else {
                $prev = 0;
            }
            $firstlink = "[<a href=\"{$link}page=0&amp;ctype={$_SESSION['ctype']}\" class=\"bluelink\">First</a>]";
            $prevlink = "<a href=\"{$link}page={$prev}&amp;ctype={$_SESSION['ctype']}\" class=\"bluelink\">&laquo; Prev $limiter</a>";
        }
        // middle loop through total results
        $numbers = ceil($rowcnt/$limiter);
        $loopstart = ceil($limstart/$limiter);
        if ($loopstart<($show-1)) {
            $loopstart = 0;
        } // begin, do not move until 4
        if ($numbers<$show) {
            $loopend = $numbers;
        } // loopend is less than $show
        else {
            $loopend = $loopstart+$show;
        }
        if ($loopend>$numbers && $loopstart!=0) { // end, show last $show
            $loopstart = $numbers - $show;
            $loopend = $numbers;
        }
        for ($i=$loopstart; $i<$loopend; $i++) {
            $startnum = $limiter * $i;
            if ($startnum!=$limstart) {
                $middlelinks .= "<a href=\"{$link}page={$startnum}&amp;ctype={$_SESSION['ctype']}\" class=\"bluelink\">".($i+1)."</a> ";
            } else {
                $middlelinks .= ($i+1).' ';
            }
        }
        $limsum = $limstart+$limiter;
        $limsum2 = $limstart+($limiter*2);
        //next and last if not on last
        if ($limstart<$rowcnt && ($limsum2<$rowcnt || ($rowcnt - $limsum)>0)) {
            if ($limsum2 < $rowcnt) {
                $nextnum = $limiter;
            } else {
                $nextnum = $rowcnt-$limsum;
            }
            $nextlink = "<a href=\"{$link}page={$limsum}&amp;ctype={$_SESSION['ctype']}\" class=\"bluelink\">Next $nextnum &raquo;</a>";
            $lastlink = "[<a href=\"{$link}page=".(($numbers-1)*$limiter)."&amp;ctype={$_SESSION['ctype']}\" class=\"bluelink\">Last</a>]";
        }
        if ($middlelinks!='') {
            $middlelinks = "[ $middlelinks ] &nbsp;";
        }
        $paging .= "<tr><td align=\"center\" class=\"bodytext\">Showing ";
        if ($rowcnt>10) {
            $paging .= "[ ";
            for ($k=10;$k<=50;$k+=10) {
                if ($rowcnt>($k-10)) {
                    if ($limiter!=$k) {
                        $paging .= "<a href=\"{$link}page=0&amp;limshow={$k}&amp;ctype={$_SESSION['ctype']}\" class=\"bluelink\">";
                    }
                    $paging .= $k;
                    if ($limiter!=$k) {
                        $paging .= "</a>";
                    }
                    $paging .= ' ';
                }
            }
            $paging .= "] ";
        }
        $paging .= "results ".($limstart+1)." to ";
        if ($limsum < $rowcnt) {
            $paging .= $limsum;
        } else {
            $paging .= $rowcnt;
        }
        $paging .= " of $rowcnt";
        $paging .= "</td><td align=\"center\" class=\"bodytext\">$firstlink &nbsp; $prevlink &nbsp; $middlelinks $nextlink &nbsp; $lastlink</td></tr>";
        $paging .= "</table>";
    } else {
        $paging = '';
        $limit = '';
    }
    return array($limit,$paging);
}

function getEAssignment($assign_queue='')
{
    global $DRW,$DRW_read2,$DRW_main,$DRW_crm;
    if ($assign_queue==2) {
        $ct = " AND contact_type_m_c='cons_panelist' AND deleted=0 ";
    } elseif ($assign_queue==3) {
        $ct = " AND contact_type_m_c='brok_panelist' AND deleted=0 ";
    } elseif ($assign_queue==4) {
        $ct = " AND contact_type_m_c='prov_panelist' AND deleted=0 ";
    } else {
        $ct = " AND contact_type_m_c='prod_panelist' AND deleted=0 ";
    }
    $sql2 = "SELECT SQL_NO_CACHE userID,COUNT(e_assigned_admin_userID) AS emails FROM
		cscan_admin_users LEFT JOIN
		(SELECT e_assigned_admin_userID FROM cscan_email pd WHERE e_assigned_admin_userID<>0$ct) AS cpd
		ON(userID=e_assigned_admin_userID)
		WHERE is_email_assign_queue$assign_queue=1 AND user_status=1 GROUP BY userID order by emails,RAND() LIMIT 1";
    $rs2 = $DRW->query($sql2, $DRW_read2);
    $row2 = $DRW->fetch_row($rs2);
    $assigned_admin_userID = (int)$row2[0];

    return $assigned_admin_userID;
}

require_once('panelist_bottom.php');
