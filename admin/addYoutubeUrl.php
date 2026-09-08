<?php 
$ALLOW_GROUPS = array(9);
require_once "../auth_auth.php";
include 'top.php';
?>
<table width='100%' border="0" align='center' cellspacing='0' cellpadding='5' class="text">
  <tr>
	<td class="adminhead" align='center' colspan='2'>YOUTUBE URLS MANAGEMENT</td>
  </tr>
  <tr>
	<td colspan='2' align ='right' class="bodytext"><b><font class='error'>* required field<br><br></font></b></td>
  </tr>
  <?php
  if(isset($_GET['vid'])) $updID = $_GET['vid'];
  else $updID = '';
  $error_msg = '';
  $urlName = $projectId='';
  
  
  if(isset($_POST['submit']) && $_POST['url']!='')
  {
    $urlName = $DRW->real_escape_string(trim($_POST['url']));
    $projectId = $DRW->real_escape_string(trim($_POST['project_id']));
    if(!empty($urlName)){
        if(!empty($projectId)){
            if(strstr($urlName,'http') && strstr(strtolower($urlName),'youtube.com')){
                if($updID=='')
                {                
                    $sql = "SELECT id FROM cscan_youtube_video where youtube_url='".$urlName."'";
                    $checkS = $DRW->query($sql, $DRW_read);
                    $countS = $DRW->num_rows($checkS);        
                    if ($countS > 0) {                
                        $error_msg =  'This Url is already exist. Please enter another YouTube Url.';
                    }else{                
                        $ins_query="Insert into cscan_youtube_video (youtube_url,project_id) values('".$urlName."','".$projectId."')";
                        $checkS = $DRW->query($ins_query, $DRW_main);
                        $msg =  'YouTube url has been added successfully!';
                        if($_POST['submit'] == 'Save & Add More'){
                            ob_end_clean();
                            header("Location: addYoutubeUrl.php?a=1");
                            exit;
                        }else{
                            ob_end_clean();
                            header("Location: manageYoutubeUrls.php");
                            exit;
                        }
                    }
                }else{
                    $sql = "UPDATE cscan_youtube_video SET project_id='$projectId' WHERE id='$updID'";
                    $actMsg = 'updated';
                    $DRW->query($sql,$DRW_main);
                    ob_end_clean();
                    header("Location: manageYoutubeUrls.php");
                    exit;
                }
            }else{
                $error_msg =  'Invalid url. Please enter valid YouTube url.';
            }
        }else{
        $error_msg =  'Please select project.';        
        }    
    }else{
        $error_msg =  'YouTube url should not be blank.';        
    }
  }else
  {
    if($updID!='')
    {
      $sql = "SELECT id,project_id,youtube_url FROM cscan_youtube_video WHERE id='$updID'";
      $editSK = $DRW->query($sql,$DRW_read);
      $editSK = $DRW->fetch_array($editSK);
      $projectId= $editSK['project_id'];
      $urlName= $editSK['youtube_url'];      
    }
  }
?>
    <tr>
        <td>
            <table width='60%' style='border-collapse:collapse' rules='none' bordercolor='#14734F' border='1' align='center' cellspacing='0' cellpadding='4'>
                <tr>
                    <td class="subhead" align='center' colspan='2'>
                    <?php if($updID!='')  echo 'UPDATE'; 
                             else echo 'ADD'; ?> YOUTUBE URL
                    </td>
                </tr>
                <tr><td colspan='100%' class='error' align='center'><?php echo $error_msg ; ?><br><br></td></tr>
                <form method='post' name='frm1' onsubmit='return validate()'>
                    
                    <tr>
                        <td class="bodytext" align="right">Project<font class='error'>*</font>:</td>
                        <td><select name="project_id" class="combo_box">
                        <option value="">--Select--</option>
                        <?php
                        $sqlc = "SELECT DISTINCT id,project_name FROM cscan_youtube_projects where status=1 ORDER BY project_name";
                        $rsc = $DRW->query( $sqlc,$DRW_read );
                        while($rowc = $DRW->fetch_row($rsc) ) {
                                $id = $rowc[0];
                                $name = $rowc[1];
                                echo "<option value=\"$id\"";
                                if($id==$projectId) {
                                        echo " selected=\"selected\"";
                                }
                                echo ">".htmlspecialchars($name)."</option>";
                        }
                        ?>
                        </select></td>
                    </tr>
                    
                    
                    <tr>
                        <td class="bodytext" align='right'>
                            YouTube Url<font class='error'>*</font>:
                        </td>
                        <td>
                            <?php 
                            if($updID==''){?>
                                <input type="text" name="url" size='60' class="input_box" maxlength='150' value="<?php echo htmlspecialchars($urlName,ENT_QUOTES);?>" >
                            <?php }else{
                            echo htmlspecialchars($urlName,ENT_QUOTES); ?>
                                <input type="hidden" name="url" size='60' class="input_box" maxlength='150' value="<?php echo htmlspecialchars($urlName,ENT_QUOTES);?>" >                                
                           <?php }?>
                        </td>
                    </tr>

                    <tr><td colspan='100%'> &nbsp;</td></tr>
                    <tr>
                        <td> </td>
                        <td  align=''>
                             <?php 
                            if($updID==''){?>
                                <input class=button type='submit' name='submit' value='Save' onClick='return validate();'>
                                <input class=button type='submit' name='submit' value='Save &amp; Add More' style='width:120' onClick='return validate();'>
                                <input class=button type='reset' value='Reset'>
                            <?php }else{?>                                
                            <input class=button type='submit' name='submit' value='Update' onClick='return validate();'>&nbsp;
                            <?php }?>
                            <input class=button type='button' value='Cancel' onclick="location.href='manageYoutubeUrls.php'; return false;">                            
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
  url=document.frm1.url.value=trimspace(document.frm1.url.value);
  projectId=document.frm1.project_id.value=trimspace(document.frm1.project_id.value);
  if(projectId == '')
  {
    alert('Please select project.');
    document.frm1.project_id.focus();
    return false;
  }else if(url == '')
  {
    alert('Please enter the YouTube url.');
    document.frm1.url.focus();
    return false;
  }
}
//-->
</script>
