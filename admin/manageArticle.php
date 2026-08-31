<?php
$ALLOW_GROUPS = array(8);
require_once("../auth_auth.php");
include 'top.php'; 
$limit = 10 ;
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
  <tr><td class="adminhead" align='center' colspan='4'>NEWS ARTICLE MANAGEMENT</td></tr>
  <!-- search and right buttons start-->
  <tr>
    <td colspan='4'>
      <table border='0' width='100%' cellspacing="0" cellpadding="0">
        <tr valign='top'>
          <td align='right' colspan='2'>
            <table width="100%" border="0" cellspacing="0" cellpadding="0" class="text">
              <form method='post' name='frm1'>
              <tr>
                <td><b>Note</b>: Click any of the following to modify the article.</td>
                <td align='right'><input class='button' style='width:130px' type='button' value='Add News Article' onclick="location.href='addArticle.php'; return false;" ></td>
                <td align='right' width="10%"><input class='button' style='width:60px' type='submit' name='submit1' ID='delBt' value='Delete' onclick='return confirmDel();'></td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <!-- search and right buttons close-->
<?php
	#################################### Start S3 Implementation Code ####################################
	if(isset($_POST['submit']) && isset($_POST['delID'])) {
		$delID = $_POST['delID'];
		$count = count($delID);

		for($i=0;$i<count($delID);$i++){
			$delThis = $delID[$i];
			$sql_sel = "SELECT articlePDF,articleImage,articleThumbImage FROM cscan_article WHERE articleID = '$delThis'";
			$res = $DRW->query($sql_sel,$DRW_read);
			$dataR = $DRW->fetch_row($res);
		    $sql = "DELETE FROM cscan_article WHERE articleID = '$delThis'";
			$deleteSql = $DRW->query($sql,$DRW_main);
			if($deleteSql){
				foreach($dataR as $key => $deletefile){
					if($key == 0 && !empty($deletefile)){
						$result = $s3->deleteObject([
			              'Bucket' => $bucket_name,
			              'Key' => 'articlePDF/'.$deletefile,
			            ]);
					}elseif(!empty($deletefile)){
						$result = $s3->deleteObject([
			              'Bucket' => $bucket_name,
			              'Key' => 'articleImage/'.$deletefile,
			            ]);
					}
				}
			}
		}

		if($count > 0) {
			$message="<b>$count</b> News Article(s) has been deleted.";
		}
		$apc_site = $_SERVER['HTTP_HOST'];
		if(apc_exists($apc_site.'articles')){
			apc_delete($apc_site.'articles');
		}
	}
	#################################### End S3 Implementation Code ####################################
	
	$numquery = "select count(articleID) as numrows from cscan_article";
	#echo $numquery;
	$numquery = $DRW->query($numquery,$DRW_read);
	$nrow = $DRW->fetch_row($numquery);
	$numrows = $nrow[0];
	
	$sql = "SELECT * FROM cscan_article";
	$sql .= " limit $p,$limit";
	$rs = $DRW->query($sql,$DRW_read);
	$resultCount = $DRW->num_rows($rs);

?>
    <tr>
		<td width='1%' class="adminhead" height='15px'><b><input type='checkbox' name='setUnset' onclick='setAll()'></td>
		<td class="adminhead" height='15px' width = '25%'> News Article </td>
		<td class="adminhead" height='15px'> Article Description </td>
		<td class="adminhead" height='15px' width = '15%'> Posting Date </td>
	</tr>
  <tr><td colspan='4' align='center' class='error'><?php echo $message; ?></td></tr>
<?php
	if($resultCount > 0)
	{
		$className='';
		while($row = $DRW->fetch_array($rs)) {
			$ID = $row['articleID'];
			$articleTitle = $row['articleTitle'];
			$date = $row['postingDate'];
			$articleDescription = $row['articleDescription'];
			if ($className=='selected-bg') $className='white-bg';
			else $className='selected-bg';
?>
        <tr valign="top" class='<?php echo $className;?>'>
					<td><input type='checkbox' name='delID[]' value="<?php echo $ID; ?>"></td>
          <td><a class='hlinks' href='addArticle.php?id=<?php echo $ID; ?>' title='Click here to edit.'><b><?php echo $articleTitle;?></b></a></td>
					<td class='bodytext'><?php echo $articleDescription;?></td>
					<td class='bodytext'><?php echo $date;?></td>
        </tr>
<?php
		}
		echo "<input type='hidden' name='submit' value='1'></form>";
	}
	else {
		echo "<tr><td colspan='4' class='error' align=center>No News Article found.</td></tr>";
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
			print "<td align='right' style= 'margin-right:5px;'><a href='manageArticle.php?p=$prevs' class='sidehead'>&laquo; Prev $limit</a></td>";
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
			echo "<td  style='margin-left:10px;'><a href='manageArticle.php?p=$news' class=''>Next $limit &raquo;</a></td>";
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
