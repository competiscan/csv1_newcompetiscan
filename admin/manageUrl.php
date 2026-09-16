<?php
$ALLOW_GROUPS = array(8);
require_once("../auth_auth.php");
include 'top.php'; 

 $msg="";  
if(isset($_POST["import_csv"])){
    $filename=$_FILES["file"]["tmp_name"];    
     if($_FILES["file"]["size"] > 0)
     {
        $file = fopen($filename, "r");
          while (($getData = fgetcsv($file, 10000, ",")) !== FALSE)
           {
              //echo "<pre>";
              //print_r($getData);
             $sql = "INSERT into cscan_manage_url (site_url) 
                   values ('".$getData[0]."')";
                   $result = $DRW->query($sql,$DRW_main);
             
        if(!isset($result))
        {
            $msg="Invalid File:Please Upload CSV File.";
             
        }
        else {
             $msg="CSV File has been successfully Imported.";
        }
      }
      
      fclose($file);  
     }
  } 
  $query='';
  if(isset($_REQUEST['select_box'])){
      $sltvalue=$_REQUEST['select_box']; 
      if($sltvalue=="all"){
      $query='';
      } else{
        $query="where status='$sltvalue'";  
      }
      
  }
  



$limit = 50 ;
if(isset($_REQUEST['p'])) $p = $_REQUEST['p'];  
else $p = 0;
$message='';
?>
<style type="text/css">
<!--
a.default:link { font-family: verdana; font-size: 11px; color: #000000; text-decoration: underline; }
a.default:active { font-family: verdana; font-size: 11px; color: #000000; text-decoration: underline; }
a.default:visited { font-family: verdana; font-size: 11px; color: #000000; text-decoration: underline; }
a.default:hover { font-family: verdana; font-size: 11px; color: #000000;text-decoration: none;}
-->
</style>
<table width='100%' border='0' cellspacing='0' cellpadding='5' class='text' rules='all' bordercolor='#B9B9B9' align='center' style='border-collapse:collapse'>
  <tr><td class="adminhead" align='center' colspan='5'>URL MANAGEMENT</td></tr>
  <!-- search and right buttons start-->
  <tr>
    <td colspan='5'>
      <table border='0' width='100%' cellspacing="0" cellpadding="0">
        <tr valign='top'>
          <td align='right'>
            <table width="100%" border="0" cellspacing="0" cellpadding="0" class="text">
            <form method='post' name='frmimport' action="" enctype="multipart/form-data">
                <tr><td colspan='4' align='center' class='success'><?php if(isset($msg) && $msg=''){echo $msg;} ?></td></tr>
               <tr>
                <td align='right'><input class="input_box"  name="file" type='file' accept=".csv"></td>
                <td colspan='3' style="margin-left:20px;float:left;"><input class='button'  type='submit' name='import_csv' value="Import Csv"></td>
               </tr>
             </form>
                <tr>
                   
                    <td colspan='4' align="left" style="margin-bottom: 10px;"> 
                        <form name="form1" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                       Show <select name="select_box" onchange="document.form1.submit();" class="input_box" style="width:90px">
                            <option value="all" <?php if(isset($_REQUEST['select_box']) && $_REQUEST['select_box']=="all") echo "selected"; ?> >All</option>
                            <option value="0" <?php if(isset($_REQUEST['select_box']) && $_REQUEST['select_box']=='0') echo "selected"; ?> >Unread</option>
                            <option value="1" <?php if(isset($_REQUEST['select_box'])&& $_REQUEST['select_box']=='1') echo "selected"; ?> >Read</option>
                        </select>
                        </form>
                    </td>
                </tr>
                <form method='post' name='frm1'>
              <tr>
                  <td>
                        &nbsp;
                  </td>
                <!--<td><b>Note</b>: Click any of the following to modify the article.</td>
                <td align='right'><input class='button' style='width:130px' type='button' value='Add News Article' onclick="location.href='addArticle.php'; return false;" ></td>-->
                <td colspan='3' align='right'><input class='button' type='submit' name='submit1' ID='delBt' value='Delete' onclick='return confirmDel();'></td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <!-- search and right buttons close-->
<?php
	if(isset($_POST['submit']) && isset($_POST['delID'])) {
		$delID = $_POST['delID'];
		$count = count($delID);
		$delThis = implode(",",$_POST['delID']);
		
		$sql = "DELETE FROM cscan_manage_url WHERE id IN($delThis)";
		$DRW->query($sql,$DRW_main);
		if($count > 0) {
			$message="<b>$count</b> Record(s) has been deleted.";
		}
		/*$apc_site = $_SERVER['HTTP_HOST'];
		if(apc_exists($apc_site.'articles')){
			apc_delete($apc_site.'articles');
		}*/
	}
	
	$numquery = "select count(id) as numrows from cscan_manage_url $query";
	#echo $numquery;
	$numquery = $DRW->query($numquery,$DRW_read);
	$nrow = $DRW->fetch_row($numquery);
	$numrows = $nrow[0];
	if($query!=''){
            $query2=" AND site_url !=''";
        }else{
            $query2=" where site_url !=''";
        }
        
	$sql = "SELECT * FROM cscan_manage_url $query $query2 order by id DESC";
	$sql .= " limit $p,$limit";
	$rs = $DRW->query($sql,$DRW_read);
	$resultCount = $DRW->num_rows($rs);

?>
    <tr>
		<td width='1%' class="adminhead" height='15px'><b><input type='checkbox' name='setUnset' onclick='setAll()'></td>
		<td class="adminhead" width = '49%' height='15px'> Site Url </td>
                <td class="adminhead" height='15px' width = '10%'> Status </td>
		<td class="adminhead" height='10px' width = '20%'>Created </td>
                <td class="adminhead" height='5px' width = '20%'>Updated </td>
	</tr>
        <?php if($message!=""){ ?>
        <tr><td colspan='5' align='center' class='error'><?php echo $message; ?></td></tr>
        <?php } ?>
<?php
	if($resultCount > 0)
	{
		$className='';
                $chkStatus='';
		while($row = $DRW->fetch_array($rs)) {
			$ID = $row['id'];
			$siteUrl = $row['site_url'];
                        $status = $row['status']; 
                        if($status=='1'){
                            $chkStatus="Read";
                        }else{
                            $chkStatus="Unread";
                        }
			$date = $row['created_on'];
                        $updatedate = $row['updated_date'];
			if ($className=='selected-bg') $className='white-bg';
			else $className='selected-bg';
?>
        <tr valign="top" class='<?php echo $className;?>'>
                <td><input type='checkbox' name='delID[]' value="<?php echo $ID; ?>"></td>
                <td class='bodytext'><?php echo $siteUrl;?></td>
                <td class='bodytext'><?php echo $chkStatus;?></td>
                <td class='bodytext'><?php echo $date;?></td>
                <td class='bodytext'><?php echo $updatedate;?></td>
        </tr>
<?php
		}
		echo "<input type='hidden' name='submit' value='1'></form>";
	}
	else {
		echo "<tr><td colspan='5' class='error' align=center>No result found.</td></tr>";
		echo " <script type=\"text/javascript\">
		<!--
		el = document.getElementById('delBt'); el.style.display='none';
    	//-->
		</script>";
	}
	
	if($resultCount > 0) {
?>
	<tr>
	<td colspan='5'>
		<table border="0" width='100%' cellspacing = '0'  cellpadding ='5'>
			<tr>
				<td colspan = '2'> &nbsp;</td>
			</tr>
<?php
		if ($p >= 1)     # HIDE PREV link if p is 0
		{
			$prevs=($p-$limit);
			print "<td align='right' style= 'margin-right:5px;'><a href='manageUrl.php?p=$prevs' class='sidehead'>&laquo; Prev $limit</a></td>";
		}
		else
		{
			echo "<td width='50%'>&nbsp;</td>";
		}
		## Calculate number of pages needing links
		
		$pages = intval($numrows/$limit);
		
		## $pages now contains int of pages needed unless there is a remainder from division
		
		if ($numrows%$limit)
		{    
			$pages++; ##has remainder so add one page
		}	
		##check to see if last page
		if (!((($p+$limit)/$limit) == $pages) && $pages!=1)
		{
			$news=$p+$limit; ##not last page so give NEXT link
			echo "<td  style='margin-left:10px;'><a href='manageUrl.php?p=$news' class=''>Next $limit &raquo;</a></td>";
		}
		else
		{
			echo "<td width='50%'>&nbsp;</td>";
		}
		echo "</tr>";
		$a=$p+$limit;
		if($a>=$numrows)
		$a=$numrows;
		echo "<tr><td class='bodytext' colspan='2' align='center'>Showing results ".($p+1)." to $a of $numrows</td></tr>";
?>
		</table>
	</td></tr>
<?php
	}
?>
</table>
<?php include 'bottom.php'; ?>

<script type="text/javascript">
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
      document.frm1.elements[i].checked = false;
    }
    document.frm1.setUnset.value = 'on';
  }
}
//-->
</script>
