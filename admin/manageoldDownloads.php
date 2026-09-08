<?php
$ALLOW_GROUPS = array(47);
require_once("../auth_auth.php");
include 'top.php';
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
$limit = 50;
$msg = '';
/* $d = dir("../downloads/");              

  while (false !== ($entry = $d->read())) {

  if($entry!='' and $entry!='.' and $entry!='..'){
  $file_path='downloads/'.$entry;
  $sql = "INSERT INTO cscan_olddownloads(file_path) VALUES ('" . $file_path . "')";
  $insert = $DRW->query($sql, $DRW_main);
  }
  } die; */
if (isset($_GET['sort']))
    $sort = (int) $_GET['sort'];
else
    $sort = 0;

if (isset($_GET['p']))
    $p = $_GET['p'];
else
    $p = 0;
$className = '';
$q = '';
if (isset($_POST['search_name']) and $_POST['search_name'] != '') {
    $search_name = trim($_POST['search_name']);
    $q = "where file_path like '%" . $search_name . "%'";
} else {
    $search_name = '';
    $q = '';
}


if (isset($_REQUEST['ajaxfor']) && $_REQUEST['ajaxfor'] == 'deletedfile' && $_REQUEST['id'] != '') {
    $id = $_REQUEST['id'];
    $sql = "SELECT file_path FROM cscan_olddownloads where id=$id";
    $result = $DRW->query($sql, $DRW_read);
    $rs = $DRW->fetch_array($result);
    $file_path = $rs['file_path'];
    $expolde_file = explode("/", $file_path);
    $filename = $expolde_file[1];
    $sqlDelete = "Delete FROM cscan_olddownloads where id=$id";

    //$fullPath=$s3URL.$file_path;
    //$fullPath=$displays3URL.$file_path;
    $data = [
        'auth_id' => $GLOBALS['AUTH_DATA']['userID'],
        'auth_name' => authUserName($GLOBALS['AUTH_DATA']['userID']),
        'deleted_id' => 0,
        'sql_query' => $sqlDelete,
        'ip_address' => ipAddress(),
        'user_agent' => $_SERVER['HTTP_USER_AGENT'],
        'delete_type' => 'Manage Old Downloads',
        'is_mobile' => isMobile(),
        'insert_date' => date("Y-m-d H:i:s")
    ];
    trackDelete($data);
    $emailData[] = $data;
    /*     * *##################### Start S3 Delete Object ####################### */
    // Delete an object from the bucket.
    $result = $s3->deleteObject([
        'Bucket' => $bucket_name,
        'Key' => $file_path
    ]);
    //echo "<pre>";
    //print_r($result); exit;
    $DRW->query($sqlDelete, $DRW_main);
    /*     * *##################### End S3 Delete Object ####################### */
    //unlink($fullPath);
    if (count($emailData) > 0) {
        $html = '<table width="100%" border="1">';
        $html .= '<tr><td>Auth ID</td><td>User Name</td><td>Deleted ID</td><td>Filde Path</td><td>IP Address</td><td>User Agent</td><td>Module</td><td>Is Mobile</td><td>Deleted Date (CST)</td></tr>';
        foreach ($emailData as $tr) {
            if (is_array($tr) && count($tr) > 0) {
                $html .= '<tr>';
                foreach ($tr as $td) {
                    $html .= '<td>' . $td . '</td>';
                }
                $html .= '</tr>';
            }
        }
        $html .= '</table>';
        sendDevAlert('Caution! Data Deleted From Manage Old Downloads', $html);
    }
    ob_end_clean();
    header("Location: manageoldDownloads.php");
    exit;
}
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<table width="100%" border="0" cellspacing="0" cellpadding="5" class="text">
    <tr><td class="adminhead" align="center" colspan="3">Manage Old Downloads</td></tr>
</table>

<table align="left" cellspacing="10" width="100%" cellpadding="2" class="text">
    <tr>
        <td align="left" colspan="3">
            <form method="post" name="frm2" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                Search <input type="text" name="search_name" size="40" maxlength="100" class="input_box" value="<?php echo $search_name; ?>" /> <input class="button" style="width:60px;" type="submit" name="search_Submit" value="Search" onclick="document.location.href = '<?php echo "{$_SERVER['PHP_SELF']}"; ?>' return false;" />
                &nbsp;&nbsp;&nbsp;&nbsp;
                <input class="button" style="width:70px" type="submit" name="show_All" value="Show All" onclick="document.location.href = '<?php echo "{$_SERVER['PHP_SELF']}"; ?>';
                        return false;" />
            </form></td>
    </tr>
</table>

<div id="filelist">
    <table border="0" cellspacing="0" width="100%" cellpadding="5" class="text">
        <tr>
            <td colspan="7">&nbsp</td>

        </tr>
        <tr>        <td class="adminhead">&nbsp;</td>
            <td class="adminhead">Sr. No.</td>
            <td class="adminhead">URL</td>                        
            <td class="adminhead">Download</td>
            <?php if (checkGroup(81)) { ?>
                <td class="adminhead">Action</td>
            <?php } ?>

        </tr>
        <?php
        $sql = "SELECT * FROM cscan_olddownloads $q";
        $rs = $DRW->query($sql, $DRW_read);
        $numquery = "SELECT COUNT(id) as numrows FROM cscan_olddownloads";
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
                $file_path = $row['file_path'];
                ?>
                <tr class="<?php echo $className; ?>">
                    <td class="bodytext">&nbsp;</td>
                    <td class="bodytext" valign="top"><?php echo $i++; //$displays3URL ?></td>
                    <td class="bodytext" valign="top"><?php echo $displays3URL . $file_path; ?></td>

                    <td class="bodytext" valign="top"><a href="javascript:void(0);" onclick="return downloadFile('<?php echo urlencode($ID); ?>');">Download</a></td>
                    <?php if (checkGroup(81)) { ?>
                        <td class="bodytext" valign="top"><a href="javascript:void(0);" onclick="if (confirm('Are you sure you want to delete this record?'))
                                    return deleteFile('<?php echo $ID; ?>');">Delete</a></td>
                <?php } ?>
                </tr>
                <?php
                //$p++;                      
            }
        } else {
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
</div>
<input type="hidden" name="send" value="1" />

<!--</form>-->
<script type="text/javascript">
<!--
    function showHelp(file) {
        var win = window.open(file, 'fulltext', 'top=0,left=0,height=650,width=600,resizable=1,scrollbars=yes');
        win.focus();
    }
//-->
</script>
<?php
include 'bottom.php';
$adminsiteurl = $site_urls . 'admin/';
$frontsiteurl = $site_urls . '/';
?>
    <script type="text/javascript">
    function downloadFile(id) {
        // alert(id);
        location.href = '<?php echo $adminsiteurl; ?>ajaxolddownloadfile.php?id=' + id;
    }
    function deleteFile(id) {
        // jQuery("#filelist").html('<img style="margin: 30px 300px;" src="<?php echo $frontsiteurl; ?>images/loader.gif">');  
        location.href = '<?php echo $adminsiteurl; ?>manageoldDownloads.php?ajaxfor=deletedfile&id=' + id;

    }
    </script> 

