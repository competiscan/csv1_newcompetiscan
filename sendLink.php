<?php  
require_once('includes/globalSession.php');
require_once('includes/checklogin.php');
ob_clean();

$emailtype_link = 1;
$emailtype_qalink = 2;
$emailtype_sharesearch = 3;

$query = "SELECT firstName,lastName FROM cscan_users WHERE userID='".$_SESSION['sess_userID']."'";
$result = $DRW->query($query,$DRW_read);
$useremail = $_SESSION['sess_username'];
$row = $DRW->fetch_row( $result );
$firstName = $row[0];
$lastName = $row[1];
$name = $firstName;

if ($lastName!='') {
    $name .= " $lastName";
}

if ($name=='') {
    $name = $useremail;
}

$showsend = true;
$onload = '';

$id = (isset($_REQUEST['id'])) ? (float)$_REQUEST['id'] : 0;
$send_mode = (isset($_REQUEST['send_mode'])) ? (int)$_REQUEST['send_mode'] : $emailtype_link;

if (!empty($id)) {
    $sql = "SELECT entryID, sectorID, mPanelID FROM cscan_product_detail WHERE productID=$id";
    $result = $DRW->query( $sql,$DRW_read );
    $row = $DRW->fetch_row( $result );
    $entryID = $row[0];
    $sector = sectorName($row[1]);
    $mPanel = mediaPanelName($row[2]);
} else {
    $entryID = '';
    $sector = '';
    $mPanel = '';
}

$to = (isset($_REQUEST['to'])) ? trim($_REQUEST['to']) : '';
$cc = (isset($_REQUEST['cc'])) ? trim($_REQUEST['cc']) : '';
$bcc = (isset($_REQUEST['bcc'])) ? trim($_REQUEST['bcc']) : '';

if (($_SESSION['sess_plevel']==1 || $_SESSION['sess_plevel']==2) && $send_mode == $emailtype_qalink) { //also change in email_list.php
    $show_cc = true;
} else {
    $show_cc = false;
}

if ($send_mode == $emailtype_sharesearch) {
    if (isset($_REQUEST['bid'])) {
        $basket_id = (int) $_REQUEST['bid'];
        $sql = "SELECT basket_name,basket_created,basket_id FROM cscan_basket WHERE basket_id=$basket_id AND userID={$_SESSION['sess_userID']}";
        $result = $DRW->query($sql,$DRW_read);
        $data = $DRW->fetch_row($result);
        $searchName = $data[0];
        $basket_created = $data[1];
        $searchType = 'export basket';
    } else {
        require_once('includes/search_copy.php');//$copyArray

        $sql = "SELECT ID,emailAlert,notify,searchName,mail_format,weekday,lastSentDate,".implode(',',$copyArray)." FROM cscan_search WHERE ID='".$DRW->real_escape_string($_REQUEST['ssid'])."' AND userID='".$_SESSION['sess_userID']."'";
        $result = $DRW->query($sql,$DRW_read); 
        $data = $DRW->fetch_row($result);
        $ID = array_shift($data);
        $emailAlert = array_shift($data);
        $notify = array_shift($data);
        $searchName = array_shift($data);
        $mail_format = array_shift($data);
        $weekday = array_shift($data);
        $lastSentDate = array_shift($data);

        if (isset($_REQUEST['alert'])) {
            $searchType = 'e-mail alert';
        } else {
            $searchType = 'saved search';
        }
    }
} else {
    $ID = 0;
    $emailAlert = 0;
    $notify = '';
    $searchName = '';
    $searchType = '';
    $basket_id = 0;
    $basket_created = '';
}

if (isset($_REQUEST['subject']) && trim($_REQUEST['subject'])!='') {
    $subject = $_REQUEST['subject'];
} else {
    if ($send_mode == $emailtype_sharesearch) {
        if (isset($_REQUEST['bid'])) {
            $subject = "Your colleague $name has shared a Competiscan export basket";
        } else {
            if (isset($_REQUEST['alert'])) {
                $subject = "Your colleague $name has shared a $notify Competiscan e-mail alert";
            } else {
                $subject = "Your colleague $name has shared a Competiscan search";
            }
        }
    } elseif ($send_mode == $emailtype_qalink) {
        $subject = $entryID.' | '.$sector.' | '.$mPanel;
    } else {
        $subject = 'Link to a product of your interest';
    }
}

$message = (isset($_REQUEST['message'])) ? $_REQUEST['message'] : '';

if (isset($_REQUEST['to'])) {
    $emailsArray = array();
    $emailTypesArray = array();
    $topmessage = '';
    $top_text = '';

    if (!empty($to)) {
        $userArray = array();

        foreach (array(1=>$to,2=>$cc,3=>$bcc) as $et=>$e) {
            $emailArray = getEmailParse($e);

            foreach ($emailArray as $i_d => $arry) {
                if ($send_mode == $emailtype_sharesearch || $send_mode == $emailtype_qalink) {
                    $queryN = "SELECT userID FROM cscan_users WHERE emailAddress='".$DRW->real_escape_string($emailArray[$i_d]['address'])."'";
                    $resultN = $DRW->query($queryN,$DRW_read);
                    $dataN = $DRW->fetch_row($resultN);
                    $tuserID = $dataN[0];

                    if ($tuserID!='' && !in_array($tuserID,$userArray)) {
                        $userArray[] = $tuserID;
                        $emailsArray[] = $emailArray[$i_d]['address'];
                        $emailTypesArray[] = $et;
                    } elseif ($send_mode == $emailtype_qalink) {
                        $templateName='share-search';
                        $userArray[] = 0;
                        $emailsArray[] = $emailArray[$i_d]['address'];
                        $emailTypesArray[] = $et;
                    }
                } else {
                    $templateName ='share-link';
                    $userArray[] = 0;
                    $emailsArray[] = $emailArray[$i_d]['address'];
                    $emailTypesArray[] = $et;
                }
            }
        }
                
        if ($send_mode == $emailtype_sharesearch) {
            $templateName='share-basket';

            if (isset($_REQUEST['bid'])) {
                foreach ($userArray as $new_userID) {
                    if ($new_userID!=$_SESSION['sess_userID']) {
                        $sql1 = "SELECT basket_id FROM cscan_basket WHERE copied_BID=$basket_id AND userID=$new_userID AND copied_userID={$_SESSION['sess_userID']} AND copied_basket_created='$basket_created'";
                        $result1 = $DRW->query($sql1,$DRW_read);
                        $data1 = $DRW->fetch_row($result1);
                        $newID = $data1[0];

                        if ($newID!='') {
                            $sql = "UPDATE cscan_basket SET basket_name='".$DRW->real_escape_string($searchName)."' WHERE basket_id=$newID AND userID=$new_userID";
                            $DRW->query($sql,$DRW_main);
                        } else {
                            $sql = "INSERT INTO cscan_basket (basket_name,userID,copied_BID,copied_userID,basket_created,copied_basket_created) VALUES ('".$DRW->real_escape_string($searchName)."',$new_userID,$basket_id,{$_SESSION['sess_userID']},NOW(),'$basket_created')";
                            $DRW->query($sql,$DRW_main);
                            $newID = $DRW->insert_id($DRW_main);
                        }

                        $sql = "DELETE FROM cscan_product_basket WHERE basket_id=$newID AND userID=$new_userID";
                        $DRW->query($sql,$DRW_main);

                        $sqlb = "INSERT INTO cscan_product_basket (basket_id,userID,basket_date,productID,basket_note,b_mChannelID,b_sectorID,b_mPanelID,b_categoryID,b_subCategoryID) (
                            SELECT $newID,$new_userID,NOW(),productID,basket_note,b_mChannelID,b_sectorID,b_mPanelID,b_categoryID,b_subCategoryID FROM cscan_product_basket WHERE basket_id=$basket_id AND userID={$_SESSION['sess_userID']})";
                        $DRW->query($sqlb,$DRW_main);
                    }

                    $topmessage = "<tr><td>Your colleague $name has shared a Competiscan export basket: <strong>$searchName</strong>.</td></tr>";
                    $top_text = "Your colleague $name has shared a Competiscan export basket: $searchName.";
                }
            } else {
                foreach ($userArray as $uk=>$new_userID) {
                    if (isset($_REQUEST['alert']) && $emailAlert) {
                        $link = "https://".$_SERVER['HTTP_HOST']."/accept.php?c=$ID&u=$new_userID";

                        if (empty($_REQUEST['skip_accept'])) {
                            $topmessage = '<tr><td>Your colleague '.$name.' has shared a '.$notify.' Competiscan e-mail alert: <strong>'.$searchName.'</strong>. By accepting via the link below, you will begin to receive this same alert with the same frequency as your colleague. If you do not wish to accept this alert, no further action is necessary.</td></tr>
                            <tr><td><a href="'.$link.'">Click here</a> to accept the alert.</td></tr>';
                            $top_text = "Your colleague $name has shared a $notify Competiscan e-mail alert: $searchName. By accepting via the link below, you will begin to receive this same alert with the same frequency as your colleague. If you do not wish to accept this alert, no further action is necessary.\n\nClick (or Copy/Paste) this link to accept the alert:\n\n$link";
                        } else {
                            $topmessage = "<tr><td>Your colleague $name has shared a $notify Competiscan e-mail alert: <strong>$searchName</strong>.</td></tr>";
                            $top_text = "Your colleague $name has shared a $notify Competiscan e-mail alert: $searchName.";
                        }
                    } else {
                        $topmessage = "<tr><td>Your colleague $name has shared a Competiscan search: <strong>$searchName</strong>.</td></tr>";
                        $top_text = "Your colleague $name has shared a Competiscan search: $searchName.";
                    }

                    if (!isset($_REQUEST['alert']) || !$emailAlert || !empty($_REQUEST['skip_accept'])) {
                        if ($new_userID!=$_SESSION['sess_userID']) {
                            if (isset($_REQUEST['alert']) && $emailAlert) {
                                $sendTo = $emailsArray[$uk];
                            } else {
                                $sendTo = '';
                            }

                            $sql1 = "SELECT ID FROM cscan_search WHERE copied_ID=$ID AND userID=$new_userID";
                            $result1 = $DRW->query($sql1,$DRW_read); 
                            $data1 = $DRW->fetch_row($result1);
                            $newID = $data1[0];
                            $q = '';

                            if ($newID!='') {
                                foreach ($copyArray as $k=>$f) {
                                    $q .= ','.$f."='".$DRW->real_escape_string($data[$k])."'";
                                }

                                $sql = "UPDATE cscan_search SET searchName='".$DRW->real_escape_string($searchName)."',emailAlert=$emailAlert,notify='".$DRW->real_escape_string($notify)."',sendTo='".$DRW->real_escape_string($sendTo)."',mail_format='".$DRW->real_escape_string($mail_format)."',weekday='".$DRW->real_escape_string($weekday)."',lastSentDate='".$DRW->real_escape_string($lastSentDate)."'$q WHERE ID=$newID";
                                $DRW->query($sql,$DRW_main);
                            } else {
                                foreach ($copyArray as $k=>$f) {
                                    $q .= ",'".$DRW->real_escape_string($data[$k])."'";
                                }

                                $sql = "INSERT INTO cscan_search (copied_ID,userID,userType,saved,searchName,emailAlert,notify,sendTo,mail_format,weekday,lastSentDate,".implode(',',$copyArray).") 
                                    VALUES ('$ID','$new_userID','a',1,'".$DRW->real_escape_string($searchName)."',$emailAlert,'".$DRW->real_escape_string($notify)."','".$DRW->real_escape_string($sendTo)."','".$DRW->real_escape_string($mail_format)."','".$DRW->real_escape_string($weekday)."','".$DRW->real_escape_string($lastSentDate)."'$q)";
                                $DRW->query($sql,$DRW_main);
                            }
                        }
                    }
                }
            }
        } else {
            $site_url = $_SERVER['HTTP_HOST'];
            $topmessage = <<< MAILBODY
<tr>
<td>$name ($useremail) thought you might be interested in the following:</td>
</tr>
<tr>
<td><a href="https://$site_url/index.php?product=$id" title="Click here to view product details">$entryID</a></td>
</tr>
<tr>
<td>Note: If you cannot open the page on clicking the link above, please copy and paste <strong>https://$site_url/index.php?product=$id</strong> into the address bar of your browser.</td>
</tr>
MAILBODY;
            $top_text = "$name ($useremail) thought you might be interested in the following product: $entryID\nplease copy and paste https://$site_url/index.php?product=$id into the address bar of your browser";

        }

        $fixmessage = nl2br(htmlspecialchars($message));
        $fixtextmessage = $message;

        if ($fixmessage!='') {
            $fixmessage = "<tr><td>&nbsp;</td></tr><tr><td>Here is $name's message to you:<br /><strong>$fixmessage</strong></td></tr>";
            $fixtextmessage = "\n\nHere is $name's message to you:\n$fixtextmessage";
        }

        if ($entryID=='') {
            $entryID = 'Product';
        }

        $sendmessage = <<< MAILBODY
<html>
<body> 
<table width="70%" cellspacing="0" cellpadding="0">
<tr>
<td style="border:solid 1px #000000;">
<table border="0" width="100%" cellspacing="0" cellpadding="5" style="font-family: verdana; font-size: 12px; color: #505050; text-decoration: none; line-height: 18px;">
$topmessage
$fixmessage               
</table>
<td>
</tr>
</table>
</body>
</html>
MAILBODY;
        $text_message = $top_text.$fixtextmessage;

        require_once('Mail.php');
        require_once('Mail/mime.php');
        require_once('UnifiedMailer.php');
        require_once('admin/MailTemplateEngine.php');
        $mte  = new MailTemplateEngine();

        $crlf = "\n";
        $params = array(
            'username'=>'',
            'password'=>'',
            'persist'=>true,
        );
        $mailer = new UnifiedMailer();

        $hdrs = array('From'=>$useremail,'Subject'=>$subject);//,'Sender'=>"\"Competiscan\" <richard@competiscan.com>");

        foreach ($emailsArray as $k=>$e_mail) {
            switch ($emailTypesArray[$k]) {
                case 2:
                    $h = 'Cc';
                    $newEmails['cc'][] = $e_mail;
                    break;
                case 3:
                    $h = 'Bcc';
                    $newEmails['bcc'][] =$e_mail;
                    break;
                default: //1
                    $newEmails['to'][] =$e_mail;
                    $h = 'To';
            }

            if (!isset($hdrs[$h])) {
                $hdrs[$h] = $e_mail;
            } else {
                $hdrs[$h] .= ','.$e_mail;
            }
        }

        if (!isset($hdrs['Cc']) && !isset($hdrs['Bcc'])) {
            if ($send_mode != $emailtype_link) {
                unset($hdrs['To']);
            }

            // Sender should only see single TO recipient, despite multiple people
            foreach ($emailsArray as $email_to) {
                $mailer->send($useremail, array('to' => $email_to), $subject, $sendmessage, $name);
            }

            $newEmails = count($emailsArray);
        } else {
            $mailer->send($useremail, $newEmails, $subject, $sendmessage, $name);
        }

        if ($send_mode == $emailtype_qalink) {
            $allArray = array();
            $allArray[] = array($id,0,0);
            $sql = "SELECT muid,isTmp FROM cscan_product_email WHERE productID=$id ORDER BY addedToDatabase DESC LIMIT 1";
            $rs = $DRW->query( $sql,$DRW_read );
            $row = $DRW->fetch_row($rs);
            $old_mid = (float)$row[0];
            $old_istmp = (int)$row[1];

            if ($old_mid!=0) {
                $allArray[] = array(0,$old_mid,$old_istmp);
            }

            foreach ($allArray as $inArray) {
                list($pid,$mid,$istmp) = $inArray;
                $sql = "UPDATE cscan_admin_log SET qareport=NOW() WHERE productID=$pid AND muid=$mid AND isTmp=$istmp";
                $result = $DRW->query( $sql,$DRW_main );
            }
        }
    }

    if (isset($newEmails) && $newEmails > 0) {
        $onload = ' onload="self.close();"';
        $er_msg = 'Message Sent';
        $showsend = false;
    } else {
        $er_msg = 'Please enter at least one valid email address';
    }
}

if ($send_mode == $emailtype_sharesearch) {
    $heading = 'Share your '.$searchName.' '.$searchType.' with your colleagues';
} elseif ($send_mode == $emailtype_qalink) {
    $heading = 'Send a QA link to your colleagues';
} else {
    $heading = 'Send an E-Mail link to your colleagues';
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Competiscan Send Link</title>
<link href="includes/competiscan_stylesheet.css" rel="stylesheet" type="text/css" />
<script src="includes/ajax.js" type="text/JavaScript"></script>
<script src="includes/sendLink.js?v=20160525" type="text/javascript"></script>
</head>
<body <?php echo $onload; ?>>
<?php include_once("includes/analyticstracking.php") ?>
<!--<div style="padding-bottom:8px;"><img src="images/competiscan_logo.jpg" width="244" height="61" border="0" /></div>-->
<div style="padding-bottom:8px;"><img src="images/competiscan-logo.png" style="max-height: 50px;" border="0"></div>
<div class="headings"><?php echo $heading; ?></div>
<div>&nbsp;</div>
<div style="border-top:solid 1px #0055E3;height:5px">&nbsp;</div>
<div>&nbsp;</div>
<form name="form1" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" onsubmit="return check();">
<table width="100%" border="0" cellpadding="4" cellspacing="0" class="bodytext">
<?php 
if (isset($er_msg)) {
?>
    <tr>
    <td align="right" valign="top">&nbsp;</td>
    <td><span class="star"><?php echo $er_msg; ?></span></td>
    </tr>
<?php
}

if ($showsend) {
?>
    <tr>
    <td width="15%" align="right" valign="top" class="bodytext"><strong>To:</strong></td>
    <td><div id="emailtext"><input type="text" name="to" value="<?php echo htmlspecialchars($to,ENT_QUOTES); ?>" class="input_box" size="60" onchange="checkLookup();" autofocus /><script type="text/JavaScript">
    <!--
    if (getxmlhttp()) {
        document.write('<br />[<a href="#" onclick="showLook(); return false;" id="showhide" class="HyperLink">Show Address Lookup<\/a>] &nbsp; <span id="show_save" style="visibility:hidden;">[<a href="#" onclick="saveList(\'0\',\'\',\'\',\'\',\'\'); return false;" class="HyperLink" id="save_list_id">Save List<\/a>]<\/span>');
        document.write('<\/div><div id="emtext" style="border:solid 1px #000000;padding:4px;display:none;float:left;background-color:#E8E8FF;"><iframe name="elist" src="emails_iframe.php" width="400" height="100" frameborder="0" marginheight="0" marginwidth="0" scrolling="auto"><\/iframe>');
    }
    //-->
    </script></div></td>
    </tr>
    <?php
    if ($show_cc) {
        ?>
        <tr>
        <td width="15%" align="right" valign="top" class="bodytext"><em>Cc:</em></td>
        <td><input type="text" name="cc" value="<?php echo htmlspecialchars($cc,ENT_QUOTES); ?>" class="input_box" size="60" onchange="checkLookup();" /></td>
        </tr>
        <tr>
        <td width="15%" align="right" valign="top" class="bodytext"><em>Bcc:</em></td>
        <td><input type="text" name="bcc" value="<?php echo htmlspecialchars($bcc,ENT_QUOTES); ?>" class="input_box" size="60" onchange="checkLookup();" /></td>
        </tr>
        <?php
    }
    ?>
    <tr>
    <td align="right" valign="top"><strong>Subject:</strong></td>
    <td><input type="text" name="subject" value="<?php echo htmlspecialchars($subject,ENT_QUOTES); ?>" class="input_box" size="60" /></td>
    </tr>
    <tr>
    <td align="right" valign="top"><strong>Message:</strong></td>
    <td><textarea name="message" rows="3" cols="50" style="border:1px solid #000000;"><?php echo htmlspecialchars($message,ENT_QUOTES); ?></textarea></td>
    </tr>
    <?php
    if (isset($_REQUEST['alert']) && ($_SESSION['sess_plevel']==1 || $_SESSION['sess_plevel']==2)) {
        ?>
        <tr>
        <td align="right" valign="top">&nbsp;</td>
        <td><label><input type="checkbox" name="skip_accept" value="1"  />Skip Accept</label></td>
        </tr>
        <?php
    }
    ?>
    <tr>
    <td align="right" valign="top">&nbsp;</td>
    <td><input type="submit" name="send" value="Send" class="submitbutton" /> &nbsp; <input type="submit" name="cancel" value="Cancel" class="submitbutton" onclick="self.close(); return false;" /></td>
    </tr>
    <?php
} else {
    echo '<tr><td align="center" colspan="2"><a href="#" onclick="self.close(); return false;">close</a></td></tr>';
}
?>
</table>
<input type="hidden" name="id" value="<?php echo $id; ?>" /><input type="hidden" name="send_mode" value="<?php echo $send_mode; ?>" />
<?php 
if (!$show_cc) {
    echo '<input type="hidden" name="cc" value="" /><input type="hidden" name="bcc" value="" />';
}

if (isset($_REQUEST['alert'])) {
    echo '<input type="hidden" name="alert" value="1" />';
}

if (isset($_REQUEST['ssid'])) {
    echo '<input type="hidden" name="ssid" value="'.$_REQUEST['ssid'].'" />';
}

if (isset($_REQUEST['bid'])) {
    echo '<input type="hidden" name="bid" value="'.$_REQUEST['bid'].'" />';
}
?>
</form>
<?php
if ($showsend) {
    ?>
    <div id="showbox_save" style="display:none;position:absolute;border:solid 1px #ffffff;background:#0055E3;padding:4px;color:#ffffff;z-index:100;">
    <form name="saveform" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" onsubmit="saveName(); return false;">
    <table border="0" cellpadding="0" cellspacing="2" class="bodytext">
    <tr><td style="color:#ffffff;font-weight:bold;">To</td><td><input type="text" name="to" class="input_box" size="60" autocomplete="off" /></td></tr>
    <?php
    if ($show_cc) {
        ?>
        <tr><td style="color:#ffffff;font-weight:bold;">Cc</td><td><input type="text" name="cc" class="input_box" size="60" autocomplete="off" /></td></tr>
        <tr><td style="color:#ffffff;font-weight:bold;">Bcc</td><td><input type="text" name="bcc" class="input_box" size="60" autocomplete="off" /></td></tr>
        <?php
    }
    ?>
    <tr><td style="color:#ffffff;font-weight:bold;">List Name</td><td><input type="text" name="savename" class="input_box" size="60" maxlength="150" autocomplete="off" /></td></tr>
    <tr><td>&nbsp;</td><td><input type="submit" name="save" value="Save" class="submitbutton" /> &nbsp; <input type="submit" name="cancel" value="Cancel" class="submitbutton" onclick="cancelForm(); return false;" /></td></tr>
    </table>
    <?php
    if (!$show_cc) {
        echo '<input type="hidden" name="cc" value="" /><input type="hidden" name="bcc" value="" />';
    }
    ?>
    <input type="hidden" name="emailto_id" value="0" /></form>
    </div>
<?php
}
?>
</body>
</html>
