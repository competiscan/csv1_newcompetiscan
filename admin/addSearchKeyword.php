<?php 
$ALLOW_GROUPS = array(9);
require_once "../auth_auth.php";
include 'top.php';
?>
<table width='100%' border="0" align='center' cellspacing='0' cellpadding='5' class="text">
  <tr>
	<td class="adminhead" align='center' colspan='2'>SEARCH KEYWORDS MANAGEMENT</td>
  </tr>
  <tr>
	<td colspan='2' align ='right' class="bodytext"><b><font class='error'>* required field<br><br></font></b></td>
  </tr>
  <?php
  if(isset($_GET['id'])) $updID = $_GET['id'];
  else $updID = '';
  $error_msg = '';
  $keyword = '';
  if(isset($_POST['submit']))
  {
    $keyword = $DRW->real_escape_string($_POST['keyword']);
  
    $chk = "SELECT * FROM cscan_youtube_search_keywords WHERE keyword = '$keyword' AND id!='$updID'";
    $rs = $DRW->query($chk,$DRW_read);
    if($DRW->num_rows($rs) == 0)
    {
      if($updID == '')
      {
        $sql = "INSERT INTO cscan_youtube_search_keywords SET keyword='$keyword'";
        $actMsg = 'added';
        $DRW->query($sql,$DRW_main);
      }
      else
      {
        $sql = "UPDATE cscan_youtube_search_keywords SET keyword='$keyword' WHERE id='$updID'";
        $actMsg = 'updated';
        $DRW->query($sql,$DRW_main);
      }
      echo "<tr><td align=center colspan='2'>New search keyword has been $actMsg sucessfully.</td></tr>";
      if($_POST['submit'] == 'Save & Add More'){
            ob_end_clean();
            header("Location: addSearchKeyword.php?a=1");
            exit;
      }
      else{
            ob_end_clean();
            header("Location: manageKeywords.php");
            exit;
      }
    }
    else
    {
      $error_msg = 'Search Keyword already exist.';
    }
  }
  else
  {
    if($updID!='')
    {
      $sql = "SELECT * FROM cscan_youtube_search_keywords WHERE id='$updID'";
      $editSK = $DRW->query($sql,$DRW_read);
      $editSK = $DRW->fetch_array($editSK);
      $keyword = $editSK['keyword']; 
    }
  }
?>
    <tr>
        <td>
            <table width='60%' style='border-collapse:collapse' rules='none' bordercolor='#14734F' border='1' align='center' cellspacing='0' cellpadding='4'>
                <tr>
                    <td class="subhead" align='center' colspan='2'>
                    <?php if($updID!='')  echo 'UPDATE'; 
                              else echo 'ADD'; ?> SEARCH KEYWORD
                    </td>
                </tr>
                <tr><td colspan='100%' class='error' align='center'><?php echo $error_msg ; ?><br><br></td></tr>
                <form method='post' name='frm1' onsubmit='return validate()'>
                    <tr>
                        <td class="bodytext" align='right'>
                            Search Keyword<font class='error'>*</font>:
                        </td>
                        <td>
                            <input type="text" name="keyword" size='40' class="input_box" maxlength='40' value="<?php echo htmlspecialchars($keyword,ENT_QUOTES);?>" >
                        </td>
                    </tr>

                    <tr><td colspan='100%'> &nbsp;</td></tr>
                    <tr>
                        <td> </td>
                        <td  align=''>
                            <?php if($updID == ''){?>
                            <input class=button type='submit' name='submit' value='Save' onClick='return validate();'>
                            <input class=button type='submit' name='submit' value='Save &amp; Add More' style='width:120' onClick='return validate();'>
                            <input class=button type='reset' value='Reset'>
                            <input class=button type='button' value='Cancel' onclick="location.href='manageKeywords.php'; return false;">
                            <?php }else{ ?>
                            <input class=button type='submit' name='submit' value='Update' onClick='return validate();'>
                            <input type='hidden' name='id' value="<?php echo $updID; ?>" >
                            <input class=button type='button' value='Cancel' onclick="location.href='manageKeywords.php'; return false;">
                            <?php }?>
                        </td>
                    </tr>
                </form>
            </table>
        </td>
    </tr>
     <?php //} ?>
</table>
<?php include 'bottom.php'; ?>
<script type="text/JavaScript">
<!--
function validate()
{
  keyword=document.frm1.keyword.value=trimspace(document.frm1.keyword.value);
  if(keyword == '')
  {
    alert('Please enter the search keyword.');
    document.frm1.keyword.focus();
    return false;
  }
}
//-->
</script>
