<?php require_once("../auth_auth.php");
if(isset($_REQUEST['id']) && $_REQUEST['id']!=''){
    $id=$_REQUEST['id'];
    $sql = "SELECT creative_path FROM cscan_digital_creative WHERE creative_id='".$id."'";
    $creative_query = $DRW->query($sql,$DRW_digital);
    $allresult = $DRW->fetch_row($creative_query);
    
    if(!empty($allresult)){
        $creative_path=$allresult[0];
        ?>
<video width="650" height="600" controls>
  <source src="<?php echo $creative_path; ?>" type="video/mp4">
  
Your browser does not support the video tag.
</video> 
<?php       
        
    }
     
    
}

?>
