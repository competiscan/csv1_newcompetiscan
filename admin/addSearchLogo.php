<?php 
$ALLOW_GROUPS = array(9);
require_once "../auth_auth.php";
include 'top.php';
?>
<link rel="stylesheet" type="text/css" href="../video-tool/assets/jquery.simple-lightbox.css">
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="../video-tool/assets/jquery.simple-lightbox.js"></script>

<table width='100%' border="0" align='center' cellspacing='0' cellpadding='5' class="text">
  <tr>
	<td class="adminhead" align='center' colspan='2'>SEARCH LOGOS MANAGEMENT</td>
  </tr>
  <tr>
	<td colspan='2' align ='right' class="bodytext"><b><font class='error'>* required field<br><br></font></b></td>
  </tr>
  <?php   
    if(isset($_GET['id'])) $updID = $_GET['id'];
    else $updID = '';
    $error_msg = '';
    $msg='';
    $logo_title = '';
    $logo_name='';
    $root = dirname(__FILE__); 
    $root=str_replace('admin','',$root).'video-tool';
    $folder='search-logo';
    $dir = $root.'/'.$folder;
    if (!is_dir($dir)) {
        mkdir($dir, 0777);
    }
  
    if(isset($_POST['submit']))
    {
        $logo_title= $DRW->real_escape_string($_POST['logo_title']);

        $chk = "SELECT * FROM cscan_youtube_search_logos WHERE logo_title = '$logo_title' AND id!='$updID'";
        $rs = $DRW->query($chk,$DRW_read);
        if($DRW->num_rows($rs) == 0)
        { 
            if($updID == '')
            {
              $sql = "INSERT INTO cscan_youtube_search_logos SET logo_title='$logo_title'";
              $actMsg = 'added';
              $DRW->query($sql,$DRW_main);
              $updID = $DRW->insert_id($DRW_main);
            }
            else
            {
              $sql = "UPDATE cscan_youtube_search_logos SET logo_title='$logo_title' WHERE id='$updID'";
              $actMsg = 'updated';
              $DRW->query($sql,$DRW_main);
            }
            if(isset($_FILES['search_logo']) AND $_FILES['search_logo']['name']!=''){
                echo $updID.' kkkkkk';
                $errors= array();      
                $file_name =rand(10,1000).str_replace(' ','',trim(strtolower($_FILES['search_logo']['name'])));
                $file_size =$_FILES['search_logo']['size'];
                $file_tmp =$_FILES['search_logo']['tmp_name'];
                $file_type=$_FILES['search_logo']['type'];

                $value = explode(".", $file_name);
                $file_ext = strtolower(array_pop($value));
                $extensions= array("jpeg","jpg","png");

                if(in_array($file_ext,$extensions)=== false){
                   $errors[]="extension not allowed, please choose a JPG or PNG file.";
                }
                if($file_size > 2097152){
                   $errors[]='File size must be less than 2 MB';
                }
                if(empty($errors)==true){
                    if(move_uploaded_file($file_tmp,$dir."/".$file_name)){
                       $updt_query="UPDATE cscan_youtube_search_logos SET logo_name='".$file_name."',logo_path='".$folder."' where id='$updID'";
                       $checkS = $DRW->query($updt_query, $DRW_main);
                       $errors[] =  'Search logo has been added successfully!';     

                    }else{
                        $errors[] =  'something went wrong. Please try again!';
                    }        
                }
            }          
      
            if($_POST['submit'] == 'Save & Add More'){
                ob_end_clean();
                header("Location: addSearchLogo.php?a=1");
                exit;
            }
            else{
                ob_end_clean();
                header("Location: manageSearchLogos.php");
                exit;
            }
        }
        else
        { 
          $errors[] =  'Search logo title already exist.';
        }
    }else{
        if($updID!='')
        {
          $sql = "SELECT * FROM cscan_youtube_search_logos WHERE id='$updID'";
          $editSL = $DRW->query($sql,$DRW_read);
          $editSL = $DRW->fetch_array($editSL);
          $logo_title = $editSL['logo_title'];
          $logo_name= $editSL['logo_name'];
          $logo_path= $editSL['logo_path'];          
        }
    }
   
    if(!empty($errors)){
        $msg=implode(',',$errors);
    }
                
?>
    <tr>
        <td>
            <table width='60%' style='border-collapse:collapse' rules='none' bordercolor='#14734F' border='1' align='center' cellspacing='0' cellpadding='4'>
                <tr>
                    <td class="subhead" align='center' colspan='2'>
                    <?php if($updID!='')  echo 'UPDATE'; 
                              else echo 'ADD'; ?> SEARCH LOGO
                    </td>
                </tr>
                <tr><td colspan='100%' class='error' align='center'><?php echo $error_msg ; ?><br><br></td></tr>
                <form method='post' name='frm1' enctype="multipart/form-data" onsubmit='return validate()'>
                    <?php if($msg!=''){?>
                        <tr>
                            <td colspan="2"class="bodytext" align='center'><?php echo $msg;?>                            
                            </td>                        
                        </tr>                        
                    <?php }?>
                    <tr>
                        <td class="bodytext" align='right'>
                            <font class='error'>*</font>Search Logo Title:
                        </td>
                        <td>
                            <input type="text" name="logo_title" size='40' class="input_box" maxlength='100' value="<?php echo htmlspecialchars($logo_title,ENT_QUOTES);?>" >
                        

                        </td>
                    </tr>
                    <tr>
                        <td class="bodytext" align="right" valign="top"><span class="error">*</span> Logo:</td>
                        <td>
                            <input type="file" name="search_logo" size="60" class="input_file" accept=".png,.jpg,.gpeg,.PNG" />
                            
                            <input type="hidden" name="search_logo_hidden" value="<?php if($logo_name!="" && $updID!="") {echo $logo_name;} ?>"/>
                            <?php if(!empty($logo_name)){ ?>
                            </br></br> <a href="javascript:void(0);"><img style="max-width:250px; max-height:200px;" src="../video-tool/<?php echo $logo_path.'/'.$logo_name;?>"></a>
                          <?php  }?>
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
                            <input class=button type='button' value='Cancel' onclick="location.href='manageSearchLogos.php'; return false;">
                            <?php }else{ ?>
                            <input class=button type='submit' name='submit' value='Update' onClick='return validate();'>
                            <input type='hidden' name='id' value="<?php echo $updID; ?>" >
                            <input class=button type='button' value='Cancel' onclick="location.href='manageSearchLogos.php'; return false;">
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
  var logo_title=document.frm1.logo_title.value=trimspace(document.frm1.logo_title.value);
  var search_logo=document.forms["frm1"]["search_logo"].value;
  var search_logo_hidden=document.forms["frm1"]["search_logo_hidden"].value;
  //alert(search_logo); return false;
  if(logo_title == '')
  {
    alert('Please enter the search logo title.');
    document.frm1.logo_title.focus();
    return false;
  }
  if(search_logo== '' && search_logo_hidden=='')
    {
        alert('Please upload logo.');
        document.frm1.search_logo.focus();
        return false;
    }
}
$(function() {
    	$('body').simpleLightbox();
    });
//-->
</script>
