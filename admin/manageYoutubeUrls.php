<?php $ALLOW_GROUPS = array(91);
require_once("../auth_auth.php");
include 'top.php';
$limit = 20;
if(isset($_GET['sort'])) $sort = (int) $_GET['sort'];
else $sort = 0;
if(isset($_GET['p'])) $p = $_GET['p'];
else $p = 0;
$message = (!empty($_GET['msg'])) ? trim(($_GET['msg'])) : '';
$fdt = '';
$tdt = '';
$_SESSION['search_project'] = array();

if(isset($_REQUEST['show_All'])) {
    
    $_SESSION['search_status'] = '';
    $_SESSION['search_fromdt'] = '';
    $_SESSION['search_todt'] = '';
    $_SESSION['search_project'] = array();
    $fdt = "";
    $tdt ="";
}
if(!isset($_SESSION['search_status']) ){    
    $_SESSION['search_status']='';
}
if(!isset($_SESSION['search_project']) ){    
    $_SESSION['search_project']='';
}
if(!isset($_SESSION['search_fromdt']) ){    
    $_SESSION['search_fromdt']='';
}
if(!isset($_SESSION['search_todt']) ){    
    $_SESSION['search_todt']='';
}
if(isset($_REQUEST['search_status'])){     
   $search_status=$_POST['search_status'];
   $_SESSION['search_status']= $search_status;  
}
if(isset($_REQUEST['search_project'])){     
   $search_project=$_POST['search_project'];
   $_SESSION['search_project']= $search_project;  
}
$search_status=$_SESSION['search_status'];
$search_project=$_SESSION['search_project'];
$processed='';
$unprocessed='';
if($search_status==3){
   $processed=' selected="selected" ';
}elseif($search_status=='0'){   
   $unprocessed=' selected="selected" ';
}
if(isset($_REQUEST['fdt'])){
    $fdt = trim($_REQUEST['fdt']);
    $_SESSION['search_fromdt']= $fdt;  
}
if(isset($_REQUEST['tdt'])){
    $tdt = trim($_REQUEST['tdt']);
    $_SESSION['search_todt']= $tdt;  
}

    $message='';
    $track_delete_data=array();
    if((isset($_POST['submit1']) && isset($_POST['delID'])) || (isset($_POST['delurlid']) && $_POST['delurlid']!=''))
    { 
      $delID = $_POST['delID'];
      if(isset($_POST['delurlid']) && $_POST['delurlid']!=''){
          $delID=array($_POST['delurlid']);
      }
      $emailData = [];
      for($i=0;$i<count($delID);$i++)
        {
            $delThis = $delID[$i];
            if($delThis!='' AND $delThis>0){
                $sql_sel = "SELECT * FROM cscan_youtube_video WHERE id = '$delThis'";
                $rs = $DRW->query($sql_sel,$DRW_read);
                $result = $DRW->fetch_array($rs);

                $video_path=$result['video_path'];
                $video_name=$result['video_name'];            

                $sql = "DELETE FROM cscan_youtube_video WHERE id = '$delThis'";

                /* Add for track on delete operation */
                $track_delete_data=array();
                $track_delete_data = [
                        'auth_id' => $GLOBALS['AUTH_DATA']['userID'],
                        'auth_name' => authUserName($GLOBALS['AUTH_DATA']['userID']),
                        'deleted_id' => (int)$delThis,
                        'sql_query' => $sql,
                        'ip_address' => ipAddress(),
                        'user_agent' => $_SERVER['HTTP_USER_AGENT'],
                        'delete_type' => 'Manage YouTube Urls',
                        'is_mobile' => isMobile(),
                        'insert_date' => date("Y-m-d H:i:s")
                    ];
                trackDelete($track_delete_data);
                $emailData[] = $track_delete_data;
                /* END Add for track on delete operation */
                $DRW->query($sql,$DRW_main);
                $track_delete_data=array();
                $DRW->query($sql,$DRW_main);
                $sql2 = "DELETE FROM cscan_youtube_video_frame WHERE video_id = '$delThis'";
                $sql3 = "DELETE FROM cscan_youtube_sentiment WHERE video_id = '$delThis'";
                $sql4 = "DELETE FROM cscan_youtube_logos_match WHERE video_id = '$delThis'";
                $sql5 = "DELETE FROM cscan_youtube_keywords_match WHERE video_id = '$delThis'";
                $sql6 = "DELETE FROM cscan_youtube_audio_text WHERE video_id = '$delThis'";            
                $DRW->query($sql2,$DRW_main); 
                $DRW->query($sql3,$DRW_main);
                $DRW->query($sql4,$DRW_main);
                $DRW->query($sql5,$DRW_main);
                $DRW->query($sql6,$DRW_main);

                if (!empty($video_path) AND !empty($video_name) AND file_exists('../video-tool/'.$video_path.'/'.$video_name)) 
                { 
                    unlink('../video-tool/'.$video_path.'/'.$video_name);
                }
            }
        }
        if(count($emailData)>0){
            $html = '<table width="100%" border="1">';
            $html .= '<tr><td>Auth ID</td><td>User Name</td><td>Deleted ID</td><td>SQL Query</td><td>IP Address</td><td>User Agent</td><td>Module</td><td>Is Mobile</td><td>Deleted Date (CST)</td></tr>';

            foreach($emailData as $tr){
                if(is_array($tr) && count($tr)>0){
                   $html .= '<tr>';
                   foreach($tr as $td){
                       $html .= '<td>'.$td.'</td>'; 
                   }
                   $html .= '</tr>';
                }
            }                    
            $html .= '</table>';

            sendDevAlert('Caution! Data Deleted From Manage YouTube Urls ',$html);
        }
      if($i > 0)
      {
        $message="<b>$i</b> YouTube Url(s) has been deleted.";
      }
    }
    $where='';
    if($search_status==3){
        $where=' where vd.status='.$search_status.' AND audio_text_status>0 ';
    }elseif($search_status=='0'){
         $where=' where vd.status<3';            
    }
    if($fdt!="") {
        if($where!=''){		
            $where .= " and DATE_FORMAT(vd.created_date, '%Y-%m-%d') >= '".$fdt."'";
        }else{
            $where = " where DATE_FORMAT(vd.created_date, '%Y-%m-%d') >= '".$fdt."'";
        }        
    }

    if($tdt!="") {
        if($where!=''){		
            $where .= " and DATE_FORMAT(vd.created_date, '%Y-%m-%d') <= '".$tdt."'";
        }else{
            $where = " where DATE_FORMAT(vd.created_date, '%Y-%m-%d') <= '".$tdt."'";
        }
    }
    
    if(!empty($search_project)) {        
        $search_project_list=implode(',',$search_project);
        if(!empty($search_project_list)){
            if($where!=''){		
                $where .= " and vd.project_id in('".$search_project_list."')";
            }else{
                $where = " where vd.project_id in('".$search_project_list."')";
            }
        }
    }
    $sql = "SELECT vd.id,vd.project_id,vd.youtube_url,vd.video_name,vd.video_path,vd.status,vd.audio_text_status,vd.created_date,yp.project_name FROM cscan_youtube_video vd join cscan_youtube_projects yp on(yp.id=vd.project_id)  ".$where." order by vd.id desc ";
    $numquery = $DRW->query($sql,$DRW_read);       
    $numrows=$DRW->num_rows($numquery);    
    $sql .= "LIMIT $p,$limit"; 
    $rs = $DRW->query($sql,$DRW_read);
    $resultCount = $DRW->num_rows($rs); ?>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<table width='100%' border='0' cellspacing='0' cellpadding='5' class='text' rules='none' bordercolor='#B9B9B9' align='center' style='border-collapse:collapse'>
  <tr><td class="adminhead" align='center' colspan='7'>YOUTUBE URLS MANAGEMENT</td></tr> 
  <tr>
    <td colspan='7'>
      <table border='0' width='100%' cellspacing="0" cellpadding="0">
        <tr valign='top'>
          <td align='right' colspan='2'>
            <table width="100%" border="0" cellspacing="0" cellpadding="0" class="text">                          
              <tr>                    
                <td align="left" valign="top" colspan="3" >
                    <form method="post" name="frm2" action="<?php echo $_SERVER['PHP_SELF'];?>" style="display:inline;">                
                        <strong>Search by Project:</strong>
                        <select style="width:160px;" class="input_box" name="search_project[]" >
                        <option value="">--Select--</option>
                        <?php
                        $sqlc = "SELECT DISTINCT id,project_name FROM cscan_youtube_projects where status=1 ORDER BY project_name";
                        $rsc = $DRW->query( $sqlc,$DRW_read );
                        while($rowc = $DRW->fetch_row($rsc) ) {
                                $id = $rowc[0];
                                $name = $rowc[1];
                                echo "<option value=\"$id\"";
                                if(in_array($id,$search_project)) {
                                        echo " selected=\"selected\"";
                                }
                                echo ">".htmlspecialchars($name)."</option>";
                        }
                        ?>
                        </select>
                         &nbsp;&nbsp;&nbsp;&nbsp;
                        <strong>Search by Status:</strong>
                        <select style="width:160px;" class="input_box" name="search_status">
                            <option value="">-- Any --</option>
                            <option value="3" <?php echo $processed;?>> Processed </option>
                            <option value="0" <?php echo $unprocessed;?>> Unprocessed </option>
                        </select>                      
                        &nbsp;&nbsp;<br />
                        <strong> From Date:</strong>
                        <input type="text" id="fdt" readonly='true' name="fdt" size="20" maxlength="10" class="input_box" value="<?php echo $fdt; ?>" />
                        &nbsp;&nbsp;<strong>To Date:</strong>
                         <input style="margin-top: 12px;" type="text" id="tdt" readonly='true' name="tdt" size="20" maxlength="10" class="input_box" value="<?php echo $tdt; ?>" /> 
                         &nbsp;&nbsp;
                         <input class="button" style="width:60px;" type="submit" name="search_Submit" value="Search" /> 
                    </form>
                    &nbsp;&nbsp;
                    <form method="post" name="frm3" action="<?php echo $_SERVER['PHP_SELF'];?>" style="display:inline;"> 
                        <input class="button" style="width:70px" type="submit" name="show_All" value="Show All" />
                        <input type="hidden" name="show_All" value="1" /><input type="hidden" name="p" value="0" />                    
                    </form> 
                </td>                
                
            </tr>
            <tr>
                <td colspan="3">&nbsp;</td>                
            </tr>
            <tr>
                <td align="right" colspan="3">
                    <input height="50px;" style='width:140px' class="button" onclick="window.location.href='export-youtube-video.php'" type="button" id="export_record" value=" Export All Records "> &nbsp;&nbsp;
                </td>
            </tr>
            <tr>
                <td colspan="3">&nbsp;</td>                
            </tr>
            <tr>
                <td align='right' width="75%">&nbsp;<input class='button' style='width:80px' type='button' value='Export' onclick="exportSelectedRecords();" >&nbsp;&nbsp;</td>
                <td align='right'><input class='button' style='width:130px' type='button' value='Add YouTube Url' onclick="location.href='addYoutubeUrl.php'; return false;" ></td>
                <td align='right' width="10%">
                <?php if(checkGroup(92)){?>
                    <form method="post" id="form1" name="frm1" action="<?php echo $_SERVER['PHP_SELF'];?>">
                        <input class='button' style='width:60px' type='submit' name='submit1' ID='delBt' value='Delete' onclick=' return confirmDel()'>
                     
                <?php } ?>
                </td>
            </tr>
            </table>
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <!-- search and right buttons close-->

    <tr>
        <td width='1%' class="adminhead" height='15px'>
        <?php  if(checkGroup(92)){?>   
          <input type='checkbox' name='setUnset' onclick='setAll()'>
        <?php }?>
        </td>
        <td align="center" width='28%' class="adminhead" height='15px' ><b>Video</td>
        <td align="center" width='20%' class="adminhead" height='15px' ><b>YouTube Url</td>
        <td align="center" width='12%' class="adminhead" height='15px' ><b>Project</td>
        <td align="center" width='5%' class="adminhead" height='15px' ><b>Status</td>
        <td align="center" width='20%' class="adminhead" height='15px' ><b>Action</td>
        <td align="center" width='14%' class="adminhead" height='15px' ><b>Created Date</td>
    </tr>
    <tr><td colspan='7' align='center' class='error'><?php echo $message; ?></td></tr>
<?php
    if($resultCount > 0)
    {
        $className='';
        while($row = $DRW->fetch_array($rs))
        {
            if ($row['status'] == 3 && $row['audio_text_status']>0) {
                $status = 'Processed';
            } else {
                $status = 'Unprocessed';
            }
            preg_match('/[\\?\\&]v=([^\\?\\&]+)/', $row['youtube_url'], $matches);
            if (count($matches) > 0)
                $videoid = $matches[1];
            else
                $videoid = '';

            $width = '380px';
            $height = '180px';  
            $ID = $row['id'];
            $youtube_url = $row['youtube_url'];
            $video_name= $row['video_name'];
            $video_path= $row['video_path'];
            $project_name = $row['project_name'];
            if ($className=='selected-bg') $className='white-bg';
            else $className='selected-bg'; 
           
            ?>
            <tr valign=top class="<?php echo $className; ?>" >
                <td>
                <?php if(checkGroup(92)){?>  
                    <input id="check_<?php echo $ID; ?>" type='checkbox' name='delID[]' value='<?php echo $ID; ?>'>
                <?php } ?>
                </td>
                <td>
                    <iframe style="margin-top:0px;margin-bottom:2px;" id="ytplayer" type="text/html" width="<?php echo $width;?>" height="<?php echo $height;?>"
                       src="https://www.youtube.com/embed/<?php echo $videoid;?>?rel=0&showinfo=0&color=white&iv_load_policy=3"
                       frameborder="0" allowfullscreen>
                    </iframe>
                    &nbsp;
                 </td>
                <td>&nbsp;<a href="<?php echo $row['youtube_url'];?>" target="_blank"><?php echo $row['youtube_url'];?></a></td>
                 <td align="center">&nbsp;<?php echo $project_name;?></td>
                <td align="center">&nbsp;<?php echo $status;?></td>
                <td align="center">&nbsp;&nbsp;<a  href="video-detail.php?vid=<?php echo $row['id'];?>"> Detail </a>&nbsp;&nbsp;<a  href="addYoutubeUrl.php?vid=<?php echo $row['id'];?>"> Edit </a>&nbsp;&nbsp;<a href="javascript:void(0)" onclick="DeleteSingleRecord('<?php echo $row['id'];?>')"> Delete </a> </td>
                <td align="center">&nbsp;<?php echo date("m/d/Y h:i:s", strtotime($row['created_date']));?> </td>                  

            </tr> 
        <?php
        }
            echo "<input type='hidden' name='submit' value='1'></form>";
    }
    else
    {
      echo "<tr><td colspan=7 class='error' align=center>There are no record found.</td></tr>";
      echo "<script>el = document.getElementById('delBt'); el.style.display='none';</script>";
    }?>
                
    <form name="delform" id="delform" method="post">
        <input id="delurlid"type="hidden" name="delurlid" value="">
    </form> 
    <form name="exportform" id="exportform" method="post" action="export-youtube-video.php">
        <input id="exportids"type="hidden" name="exportids" value="">
    </form>         
            
    <tr>
	<td colspan="7">
            <table border="0" width="100%" cellspacing="0"  cellpadding="5">
                <tr>
                        <td>&nbsp; </td>
                </tr>
                <?php
                if($resultCount > 0)
                {
                    if($sort>0) $sorttext = '&sort='.$_GET['sort'];
                    else $sorttext = '';
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
                    if($limstart>0){
                            if($limstart>=$limiter) $prev = $limstart - $limiter;
                            else $prev = 0;
                            $firstlink = "[<a href=\"{$_SERVER['PHP_SELF']}?p=0$sorttext\">First</a>]";
                            $prevlink = "<a href=\"{$_SERVER['PHP_SELF']}?p=$prev$sorttext\">&laquo; Prev $limiter</a>";
                    }
                    // middle loop through total results
                    $numbers = ceil($rowcnt/$limiter);
                    $loopstart = ceil($limstart/$limiter);
                    if($loopstart<($show-1)) $loopstart = 0; // begin, do not move until 4
                    if($numbers<$show) $loopend = $numbers; // loopend is less than $show
                    else $loopend = $loopstart+$show;
                    if($loopend>$numbers && $loopstart!=0) { // end, show last $show
                            $loopstart = $numbers - $show;
                            $loopend = $numbers;
                    }
                    for($i=$loopstart; $i<$loopend; $i++){
                            $startnum = $limiter * $i;
                            if($startnum!=$limstart) {
                                    $middlelinks .= "<a href=\"{$_SERVER['PHP_SELF']}?p=$startnum$sorttext\">".($i+1)."</a> ";
                            }
                            else $middlelinks .= ($i+1).' ';
                    }
                    //next and last if not on last
                    if($limstart<$rowcnt && (($limstart+($limiter*2))<$rowcnt || ($rowcnt - ($limstart + $limiter))>0)){
                            $next = $limstart + $limiter;
                            $nextlink = "<a href=\"{$_SERVER['PHP_SELF']}?p=$next$sorttext\">Next $limiter &raquo;</a>";
                            $lastlink = "[<a href=\"{$_SERVER['PHP_SELF']}?p=".(($numbers-1)*$limiter)."$sorttext\">Last</a>]";
                    }

                    if($middlelinks!='') $middlelinks = "[ $middlelinks ] &nbsp;";
                    print "<tr><td align=\"center\" class=\"bodytext\">$firstlink &nbsp; $prevlink &nbsp; $middlelinks $nextlink &nbsp; $lastlink</td></tr>";
                    print "<tr><td align=\"center\" class=\"bodytext\">Showing results ".($limstart+1)." to ";
                    if($limstart+$limiter < $rowcnt) print ($limstart+$limiter);
                    else print $rowcnt;
                    print " of $rowcnt</td></tr>";
                    
                }?>
            </table>
	</td>
	</tr> 
</table>


<?php include 'bottom.php'; ?>
<script type="text/JavaScript">
<!--
function confirmDel()
{
  goAheadFlag = 0;
  for(i=0;i<document.frm1.elements.length;i++)
  {
    if(document.frm1.elements[i].checked == true)
    {
      goAheadFlag = 1;
    }
  }
  if(goAheadFlag)
  {
    if(confirm("Are you sure to delete ?"))
    {
      return true;
    }
    else
    {
      return false;
    }
  }
  else
  {
    alert('Please select at least one record to delete !!!');
    return false;
  }
}

function setAll()
{
  if(document.frm1.setUnset.value == 'on')
  {
    for(i=1;i<document.frm1.elements.length;i++)
    {
      document.frm1.elements[i].checked = true;
    }
    document.frm1.setUnset.value = '';
  }
  else
  {
    for(i=1;i<document.frm1.elements.length;i++)
    {
      document.frm1.getElementbyId.checked = false;
    }
    document.frm1.setUnset.value = 'on';
  }
}
function DeleteSingleRecord(id)
{
 if(confirm("Are you sure, you want to delete it?"))
    {
      document.getElementById("delurlid").value=id;
      document.forms["delform"].submit();
    }else{
      document.getElementById("delurlid").value='';  
    }
}

function exportSelectedRecords()
{
  goAheadFlag = 0;
  var checkedElement=[];
  
  for(i=0;i<document.frm1.elements.length;i++)
  {
    if(document.frm1.elements[i].checked == true)
    {
      goAheadFlag = 1;
      checkedElement.push(document.frm1.elements[i].value);
    }
  }
  if(goAheadFlag)
  {
    //alert(checkedElement);
    document.getElementById("exportids").value=checkedElement;
    document.getElementById("exportform").submit();     
    return false;
  }
  else
  {
    alert('Please select at least one record to export !!!');
    return false;
  }
}



$( function() {
    $("#fdt").datepicker({
        dateFormat: 'yy-mm-dd',
        showOn: "button",
        buttonImage: "../images/calendar.gif",
        buttonImageOnly: true,
        buttonText: "Select from date",
        //maxDate: new Date()
    });

   $("#tdt").datepicker({
        dateFormat: 'yy-mm-dd',
        showOn: "button",
        buttonImage: "../images/calendar.gif",
        buttonImageOnly: true,
        buttonText: "Select to date",
        //maxDate: new Date()
    });
});
//-->
</script>
