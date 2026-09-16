<?php
$ALLOW_GROUPS = array(85);
require_once("../auth_auth.php");
include 'top.php';
require_once("../includes/functions.php");

if (!defined('ENV')) {
    define('ENV', getenv('SERVER_NAME'));
}
if (ENV == 'localhost') {
    $site_urls = 'http://localhost/competiscan.com/';
} elseif (ENV == 'demo.competiscan.com') {
    $site_urls = 'http://demo.competiscan.com/';
} else {
    $site_urls = 'https://competiscan.com/';
}
$limit = 20;
$msg = '';

if (isset($_GET['sort']))
    $sort = (int) $_GET['sort'];
else
    $sort = 0;

if (isset($_GET['p']))
    $p = $_GET['p'];
else
    $p = 0;

if (isset($_REQUEST['chkstatus']) && $_REQUEST['chkstatus'] == 1 && isset($_REQUEST['status']) && isset($_REQUEST['id'])) {
    $id = trim($_REQUEST['id']);
    $status = trim($_REQUEST['status']);
    if ($status == '1') {
        $UpdateSql = "update cscan_sent_imapcsv_link set status='0' where id = $id ";
    } else {
        $UpdateSql = "update cscan_sent_imapcsv_link set status='1' where id = $id ";
    }
    //echo $UpdateSql; die;
    $Update = $DRW->query($UpdateSql, $DRW_main);
    ob_end_clean();
    header("Location: manage-imap-csv.php");
    exit;
}
if (isset($_REQUEST['deletebut']) && $_REQUEST['deletebut'] == 1 && isset($_REQUEST['delID'])) {
    $delID = $_REQUEST['delID'];
    if (is_array($delID)) {
        foreach ($delID as $id) {
            $sql = "DELETE FROM cscan_sent_imapcsv_link where id =$id";
            $DRW->query($sql, $DRW_main);
        }
    } else {
        $sql = "DELETE FROM cscan_sent_imapcsv_link where id =$delID";
        $DRW->query($sql, $DRW_main);
    }
   // ob_end_clean();
    //header("Location: manage-imap-csv.php");
    //exit;
}
$message="";
if (isset($_REQUEST['sendEmail']) && $_REQUEST['sendEmail'] == 1 && isset($_REQUEST['sendID'])) {
    $sendID = $_REQUEST['sendID'];
        $SelectSql = "SELECT email_id FROM cscan_sent_imapcsv_link WHERE status='1' AND id = $sendID";
        //echo $SelectSql; die;
        $query = $DRW->query($SelectSql,$DRW_read);
        while ($row = $DRW->fetch_assoc($query)) {
        $email_id = $row['email_id'];
        $today_date = date('Y-m-d');
        if (!empty($_REQUEST['date'])) {
            $today_date = $_REQUEST['date'];
        }
        $previous_date = date('Y-m-d', strtotime($today_date . " - 1 day"));
        $sqlQuery = "SELECT id,document_path,file_name FROM cscan_imapcsv where  DATE_FORMAT(created_on,'%Y-%m-%d') = '" . $today_date . "' ORDER BY id DESC ";
       // echo $sqlQuery; die;
        $result = $DRW->query($sqlQuery, $DRW_read);
        $rs = $DRW->fetch_array($result);
        $document_path = $rs['document_path'];
        $filename = $rs['file_name'];
         //$s3_link='https://csbucket007.s3.amazonaws.com/'.$document_path.$filename;
        $s3_link = $displays3URL.$document_path.$filename;
       // $to = "devendra.tiwari@nmgtechnologies.com,arvind.chaurasia@nmgtechnologies.com";
        $to=$email_id;
        $subject = "Today's email data form competiscan";
        $html = '<table width="100%" border="0">';
        $html .= '<tr><td>Hi,<br/><br/></td></tr>';
        $html .= '<tr><td>Please find the daily imap email data in csv format which link is as belows. <br/><br/></td></tr>';
        $html .= '<tr><td>URL : ' . $s3_link . '</td></tr>';
        $html .= '</table>';
       //echo $html; die;
       $sendEmail= sendDevAlert($subject, $html, $to);
       //$sendEmail=1;
       if($sendEmail){
           $message = "You have send mail successfully!";
       }else{
          $message = 'You have not send mail successfully!'; 
       }
    }
   // ob_end_clean();
   //header("Location: manage-imap-csv.php");
    //exit;
}

if (isset($_REQUEST['sendemailbut']) && $_REQUEST['sendemailbut'] == 1 && isset($_REQUEST['delID'])) {
    $delID = $_REQUEST['delID'];
    if (is_array($delID)) {
        foreach ($delID as $id) {
         $sql = "Select email_id FROM cscan_sent_imapcsv_link where status=1 and id =$id";
         $query = $DRW->query($sql,$DRW_read);
                while ($row = $DRW->fetch_assoc($query)) {
                $email_id = $row['email_id'];
                $today_date = date('Y-m-d');
                if (!empty($_REQUEST['date'])) {
                    $today_date = $_REQUEST['date'];
                }
                $previous_date = date('Y-m-d', strtotime($today_date . " - 1 day"));
                $sqlQuery = "SELECT id,document_path,file_name FROM cscan_imapcsv where  DATE_FORMAT(created_on,'%Y-%m-%d') = '" . $today_date . "' ORDER BY id DESC ";
               // echo $sqlQuery; die;
                $result = $DRW->query($sqlQuery, $DRW_read);
                $rs = $DRW->fetch_array($result);
                $document_path = $rs['document_path'];
                $filename = $rs['file_name'];
                 //$s3_link='https://csbucket007.s3.amazonaws.com/'.$document_path.$filename;
                $s3_link = $displays3URL.$document_path.$filename;
                $to=$email_id;
                $subject = "Today's email data form competiscan";
                $html = '<table width="100%" border="0">';
                $html .= '<tr><td>Hi,<br/><br/></td></tr>';
                $html .= '<tr><td>Please find the daily imap email data in csv format which link is as belows. <br/><br/></td></tr>';
                $html .= '<tr><td>URL : ' . $s3_link . '</td></tr>';
                $html .= '</table>';
                //echo $html; 
                sendDevAlert($subject, $html, $to);
               //$sendEmail= sendDevAlert($subject, $html, $to);
               //$sendEmail=1;
               /*if($sendEmail){
                   $message = "You have send mail successfully!";
               }else{
                  $message = 'You have not send mail successfully!'; 
               }*/
            }
        }
        //$message = "You have send mail successfully!";
    } 
   
}

?>
<script type="text/javascript" src="https://www.competiscan.com/admin/jquery.min.js"></script>

<?php
if (isset($_REQUEST["submit"], $_REQUEST["email_id"]) && $_REQUEST["email_id"] != '' && $_REQUEST["submit"] == 'Add') {
    $email_id = trim($_REQUEST["email_id"]);
    $SelectSql = "SELECT email_id FROM cscan_sent_imapcsv_link where email_id = '" . $email_id . "'";
    //echo $SelectSql; die;
    $QueryResult = $DRW->query($SelectSql, $DRW_read);
    $num = $DRW->num_rows($QueryResult);
    if ($num == '0') {
        $sql = "INSERT INTO cscan_sent_imapcsv_link(email_id) VALUES ('" . $email_id . "')";
        $insert = $DRW->query($sql, $DRW_main);
        if ($insert) {
            $message = "E-mail added successfully!";
        } else {
            $message = 'Some things error, please try again!';
        }
    } else {
        $message = "This email id already exists!";
    }
}
?>
<?php
    echo '<script type="text/javascript">
			$(document).ready(function(){
				setTimeout(function(){
					$("#displayMessage").hide()
				}, 5000);
			});
		</script>';
    echo "<p id='displayMessage' style='text-align: center;margin: 10px 0px 10px 0px; color:green'>" . $message . "</p>";
?>
<table width="100%" border="0" cellspacing="0" cellpadding="5" class="text">
    <tr><td class="adminhead" align="center">Manage Imap Csv</td></tr>
    <tr>
        <td>
            <form enctype="multipart/form-data" method="post" action="">
                <label><b>Email Id </b>: <input type="email" name="email_id" size="40" class="input_box" value="" /></label>
                <input class="button" style="width:60px" type="submit" name="submit" value="Add" style="margin:15px 0px 10px 0px;"/>
            </form>
        </td>
    </tr>

    <tr>
        <td>
            <form method="post" name="imapForm" action="manage-imap-csv.php">
                <table width="100%" border="0" cellspacing="0" cellpadding="0" class="text">
                    <tr>
                    <!--td><strong>Note:</strong> Click to upload zip file.</td-->
                        <td align="right">
                           <input class="button" style="width:80px" type="button" name="send_email" value="Send Email" id="delBt" onclick="EmailCheck(); return false;" />
                            <input class="button" style="width:60px" type="button" name="delete1" value="Delete" id="delBt" onclick="deleteCheck(); return false;" />
                        </td>
                    </tr>
                </table>
            </form>
        </td>
    </tr>
</table>

<form action="manage-imap-csv.php" method="post" name="deleteform">
    <table width="100%" border="0" cellspacing="0" cellpadding="5" class="text">
        <tr>
            <td width="5%" class="adminhead" height="15"><input type="checkbox" name="setUnset" onclick="setAll();" /></td>
            <td width="5%" class="adminhead" height="15"><strong>Sr No.</strong></td>
            <td width="20%" class="adminhead" height="15"><strong>Email Id</strong><?php if ($sort != 1) print " <a href=\"" . $_SERVER['PHP_SELF'] . "?sort=1&p=0\" class=\"blue\">sort</a>"; ?></td>
            <td width="5%" class="adminhead" height="15"><strong>Status</strong></td>
            <td width="5%" class="adminhead" height="15" align="center"><strong>Action</strong></td> 
        </tr>
        <tr>
            <td colspan="5" class="error" align="center"><?php echo $msg; ?></td>
        </tr> 
        <?php
        $sql = "SELECT * FROM cscan_sent_imapcsv_link";
        $rs = $DRW->query($sql, $DRW_read);
        $numquery = "SELECT COUNT(id) as numrows FROM cscan_sent_imapcsv_link";
        $numquery = $DRW->query($numquery, $DRW_read);
        $nrow = $DRW->fetch_array($numquery);
        $numrows = $nrow[0];

        switch ($sort) {
            case 1:
                $sql .= " ORDER BY id ";
                break;
            default:
                $sql .= " ORDER BY id ";
        }
        $sql .= "LIMIT $p,$limit";

        $rs = $DRW->query($sql, $DRW_read);
        $resultCount = $DRW->num_rows($rs);
        if ($resultCount > 0) {
            $className = '';
            $i = 1;
            while ($row = $DRW->fetch_assoc($rs)) {
                $ID = $row['id'];
                $email_id = $row['email_id'];
                $status = $row['status'];
                ?>
                <tr valign="top" class="white-bg">
                    <td><input type='checkbox' name='delID[]' class='chkd_all' value='<?php echo $ID; ?>'></td>
                    <td><?php echo $i++; ?></td>
                    <td><?php echo $email_id; ?></td>
                    <td><input type='checkbox' data-chk_id='<?php echo $ID; ?>' <?php if ($status == '1') echo "checked='checked'"; ?> class='check_uncheck_box' name='check_email' value='<?php echo $status; ?>'></td>
                    <td align="center">
                          <?php if($status=='1'){ ?>
                            <a class="hlinks sendMail" target="_blank" href="javascript:void(0);" title="Send Mail" value="<?php echo $ID; ?>">Send Mail</a>
                            <span style="font-family: Tahoma;font-size: 12px;color: #14734F;text-decoration: none;">/</span> 
                          <?php } ?>
                        <a class="hlinks deleteFile" href="javascript:void(0)" title="Delete" value="<?php echo $ID; ?>">Delete</a>
                    </td>
                </tr>
                <?php
            }
        }
        else {
            ?>
            <tr>
                <td colspan="6" class="error" align="center">No record found.</td>
            </tr>
            <?php
        }
        ?>
        <tr>
            <td colspan="5">
                <table border="0" width="100%" cellspacing="0"  cellpadding="5">
                    <tr>
                        <td>&nbsp; </td>
                    </tr>
                    <?php
                    if ($sort > 0)
                        $sorttext = '&sort=' . $_GET['sort'];
                    else
                        $sorttext = '';
                    $firstlink = '[First]';
                    $prevlink = '[Prev]';
                    $nextlink = '[Next]';
                    $lastlink = '[Last]';
                    $middlelinks = '';
                    $limstart = $p;
                    $limiter = $limit;
                    $rowcnt = $numrows;
                    $show = 10;
                    //first and previous only if not on first
                    if ($limstart > 0) {
                        if ($limstart >= $limiter)
                            $prev = $limstart - $limiter;
                        else
                            $prev = 0;
                        $firstlink = "[<a href=\"{$_SERVER['PHP_SELF']}?p=0$sorttext\">First</a>]";
                        $prevlink = "<a href=\"{$_SERVER['PHP_SELF']}?p=$prev$sorttext\">&laquo; Prev $limiter</a>";
                    }
                    // middle loop through total results
                    $numbers = ceil($rowcnt / $limiter);
                    $loopstart = ceil($limstart / $limiter);
                    if ($loopstart < ($show - 1))
                        $loopstart = 0; // begin, do not move until 4
                    if ($numbers < $show)
                        $loopend = $numbers; // loopend is less than $show
                    else
                        $loopend = $loopstart + $show;
                    if ($loopend > $numbers && $loopstart != 0) { // end, show last $show
                        $loopstart = $numbers - $show;
                        $loopend = $numbers;
                    }
                    for ($i = $loopstart; $i < $loopend; $i++) {
                        $startnum = $limiter * $i;
                        if ($startnum != $limstart) {
                            $middlelinks .= "<a href=\"{$_SERVER['PHP_SELF']}?p=$startnum$sorttext\">" . ($i + 1) . "</a> ";
                        } else
                            $middlelinks .= ($i + 1) . ' ';
                    }
                    //next and last if not on last
                    if ($limstart < $rowcnt && (($limstart + ($limiter * 2)) < $rowcnt || ($rowcnt - ($limstart + $limiter)) > 0)) {
                        $next = $limstart + $limiter;
                        $nextlink = "<a href=\"{$_SERVER['PHP_SELF']}?p=$next$sorttext\">Next $limiter &raquo;</a>";
                        $lastlink = "[<a href=\"{$_SERVER['PHP_SELF']}?p=" . (($numbers - 1) * $limiter) . "$sorttext\">Last</a>]";
                    }

                    if ($middlelinks != '')
                        $middlelinks = "[ $middlelinks ] &nbsp;";
                    print "<tr><td align=\"center\" class=\"bodytext\">$firstlink &nbsp; $prevlink &nbsp; $middlelinks $nextlink &nbsp; $lastlink</td></tr>";
                    print "<tr><td align=\"center\" class=\"bodytext\">Showing results " . ($limstart + 1) . " to ";
                    if ($limstart + $limiter < $rowcnt)
                        print ($limstart + $limiter);
                    else
                        print $rowcnt;
                    print " of $rowcnt</td></tr>";
                    ?>
                </table>
            </td>
        </tr>
    </table>
    <input type="hidden" name="active" value="0" />
    <input type="hidden" name="sendemailbut" value="0" />
    <input type="hidden" name="deletebut" value="0" />
</form>
<script type="text/javascript">
    function setAll() {
        var items=document.getElementsByName('delID[]');
        //alert(items.length);
        //alert(items);
        if (document.deleteform.setUnset.value == 'on') {
            for (i = 0; i< items.length; i++) {
              items[i].checked = true;
            }
            document.deleteform.setUnset.value = '';
        } else {
            for (i = 0; i < items.length; i++) {
                items[i].checked = false;
               // document.deleteform.elements[i].checked = false;
            }
            document.deleteform.setUnset.value = 'on';
        }
    }

    function deleteCheck() {
        var x = 0;
        for (var i = 0; i < document.deleteform.elements.length; i++) {
            if (document.deleteform.elements[i].checked) {
                x = 1;
                break;
            }
        }
        if (x == 0) {
            alert("Please select at least one record to delete.");
        } else {
            if (confirm('Are you sure you want to delete?')) {
                document.deleteform.deletebut.value = 1;
                document.deleteform.submit();
            }
        }
    }
    
    function EmailCheck() {
        var x = 0;
        for (var i = 0; i < document.deleteform.elements.length; i++) {
            if (document.deleteform.elements[i].checked) {
                x = 1;
                break;
            }
        }
        if (x == 0) {
            alert("Please select at least one record to send e-mail.");
        } else {
            if (confirm('Are you sure you want to send e-mail?')) {
                document.deleteform.sendemailbut.value = 1;
                document.deleteform.submit();
            }
        }
    }

    $(document).ready(function () {
        $('.deleteFile').on('click', function () {
            var ID = $(this).attr('value');
            if (confirm('Are you sure you want to delete?')) {
                window.location.href = "<?php echo $_SERVER['PHP_SELF']; ?>" + '?deletebut=1&delID=' + ID;
            }
        });

        $('.sendMail').on('click', function () {
            var ID = $(this).attr('value');
           // alert(ID); die;
            //if (confirm('Are you sure you want to delete?')) {
                window.location.href = "<?php echo $_SERVER['PHP_SELF']; ?>" + '?sendEmail=1&sendID=' + ID;
           // }
        });

        $('.check_uncheck_box').on('click', function () {
            var ID = $(this).attr('data-chk_id');
            //alert(ID); return false;
            var status = $(this).attr('value');
            //alert(ID);// return false;
            //if(confirm('Are you sure you want to check?')){
            window.location.href = "<?php echo $_SERVER['PHP_SELF']; ?>" + '?chkstatus=1&id=' + ID + '&status=' + status;
            //}
        });
    })
    /*function check_searchform(){
     var search = document.searchForm.search_text.value = trimspace(document.searchForm.search_text.value);
     if(search == "") {
     alert("Please enter some value to search");
     document.searchForm.search_text.focus();
     return false;
     }
     return true;
     }*/
</script>
<?php
include 'bottom.php';
?>