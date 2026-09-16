<?php 
$TITLE = 'Competiscan Email';
require_once('panelist_top.php');
require_once('includes/clean.php');
require('simple_html_dom.php');
error_reporting(E_ERROR | E_PARSE);
if(isset($_GET['muid'])) $muid = (int)$_GET['muid'];
else $muid = 0;
$hy=$_REQUEST['hy'];
$html = 0;
$query = "SELECT `muid` FROM `cscan_email_save$hy` WHERE `muid`='".$DRW->real_escape_string($muid)."'"; 
$result = $DRW->query($query, $DRW_read2);
    if ($DRW->num_rows($result) > 0) {
    $query2 = "SELECT `esproduct`,`muid` FROM `cscan_email_save$hy` WHERE `muid`='".$DRW->real_escape_string($muid)."'";    
    }else{
    $query2 = "SELECT `cettext`,`cetid` FROM `cscan_email_text$hy` WHERE `cettype`='text/html' AND `muid`='".$DRW->real_escape_string($muid)."' ORDER BY ABS(`cetpart`) ASC";
    }
    $query_result2 = $DRW->query($query2,$DRW_read2);
$messagetext = '';
while($data2 = $DRW->fetch_row($query_result2)){ 
     // echo "ofofo"; die;
    	$cettext = $data2[0];
	$cetid = $data2[1];
	
	$html++;
	//if($edit_cetid==$cetid){
		$messagetext = cleanHTML($cettext);
	//}
}
$messagetext = preg_replace('/<\\/?zzz[^>]*>/i','',$messagetext);
$messagetext = preg_replace('/<([^>\\s]*@[^>\\s]*)>/','&lt;$1&gt;',$messagetext);
$srchd=array("â","¢","Â","Ã","Â","Â","€","Â€","","'");
$repstr=array("","","","","","","","","","");
$messagetext =  str_replace($srchd, $repstr , $messagetext);
$dom = new DomDocument();
$dom->loadHTML($messagetext); 
$output = array();
foreach ($dom->getElementsByTagName('a') as $item) {
   // $imageTags = $item->getElementsByTagName('img');
   // print_r($imageTags);
  //echo $src = $imageTags-> getAttribute('src'); 
      $output[] = array (
      'str' => htmlentities($dom->saveHTML($item)),
      'href' => $item->getAttribute('href'),
      'anchorText' => $item->nodeValue,
        'img' =>  $item->getElementsByTagName('img')
   );
      
      
      
} 
$output_data=$output;


if (isset($_REQUEST['deletebut']) && $_REQUEST['deletebut'] == 1) {
   // echo"sjdsd"; 
   $find_href = $_REQUEST['delID']; 
   if(!empty($_REQUEST['remove_check'])){
     $find_href = $_REQUEST['remove_check'];
   }
  // echo "test".$find_href; 
    if (is_array($find_href)) {
    $replace_str =  str_replace($find_href, '#', html_entity_decode($messagetext));
    } else {
       $replace_str= str_replace($find_href, '#', html_entity_decode($messagetext));
    }
      $sql = "REPLACE INTO `cscan_email_save$hy` SET `muid`='".$DRW->real_escape_string($muid)."',
              `esproduct`='".$DRW->real_escape_string($replace_str)."'"; 

        $DRW->query($sql,$DRW_main);
        ob_end_clean();
        header("Location: show_email_link.php?muid=$muid&hy=$hy");
        exit;
}

?>
<script type="text/javascript" src="https://www.competiscan.com/admin/jquery.min.js"></script>
<table width="100%" border="0" cellspacing="0" cellpadding="5" class="text">
	<tr>
		<td>
		<form method="post" name="communicationForm" action="show_email_link.php?muid=<?php echo $muid; ?>&hy1=<?php echo $hy; ?>">
			<table width="100%" border="0" cellspacing="0" cellpadding="0" class="text">
			<tr>
			<td align="right">
			<input class="button" style="width:80px" type="button" name="delete1" value="Remove Link" id="delBt" onclick="deleteCheck(); return false;" />
	        </td>
			</tr>
			</table>
		</form>
		</td>
	</tr>
</table>
  
<form action="show_email_link.php?muid=<?php echo $muid; ?>&hy=<?php echo $hy; ?>" method="post" name="deleteform">
<table width="100%" border="0" cellspacing="0" cellpadding="5" class="text">
  <tr>
    <td width="5%" class="adminhead" height="15"><input type="checkbox" name="setUnset" onclick="setAll();" /></td>
    <td width="5%" class="adminhead" height="15"><strong>Sr No.</strong></td>
    <td width="20%" class="adminhead" height="15"><strong>Link</strong></td>
	<td width="5%" class="adminhead" height="15" align="center"><strong>Action</strong></td> 
  </tr>
  <!--<tr>
	<td colspan="5" class="error" align="center"><?php echo $msg; ?></td>
  </tr> -->
<?php
$i=1;
foreach ($output as $value) {
   //echo $value['href']; 
?>
      <tr valign="top" class="white-bg">
          <!--<input type="hidden" name="delID" value="<?php echo $value['href']; ?>" />-->
      	<td><input type='checkbox' name='delID[]' value='<?php echo htmlentities(addslashes($value['href'])); ?>'></td>
      	<td><?php echo $i++; ?></td>
        <td><?php echo $value['anchorText']; 
        
        foreach ($value['img'] as $element) { 
          
        // Extracting value of src attribute of 
        // the current image object 
       $src= $element-> getAttribute('src');
          
        // Extracting value of alt attribute of 
        // the current image object 
        $alt = $element -> getAttribute('alt'); 
          
        // Extracting value of height attribute 
        // of the current image object 
        $height = 100; 
          
        // Extracting value of width attribute of 
        // the current image object 
        $width = 100; 
          
        // Given Output as image with extracted attribute, 
        // you can print value of those attributes also 
        echo '<img src="'.$src.'" alt="'.$alt.'" height="'
                . $height.'" width="'.$width.'"/>'; 
    } 
        
        
        ?></td>
		<td align="center">
                        <?php if($value['href']=="#"){ ?>
                        Removed
                        <?php } else { ?>
                        <a class="hlinks deleteFile" href="javascript:void(0)" title="Remove" value="<?php echo $value['href']; ?>">Remove Link</a>
                        <?php } ?>
		</td>
	  </tr>
      <?php
}
	?>
</table>
<input type="hidden" name="remove_check" value="0" />
<input type="hidden" name="deletebut" value="0" />
</form>
<script type="text/javascript">
function setAll(){
	if(document.deleteform.setUnset.value == 'on'){
		for(i=1;i<document.deleteform.elements.length;i++){
			document.deleteform.elements[i].checked = true;
		}
		document.deleteform.setUnset.value = '';
	}
	else{
		for(i=1;i<document.deleteform.elements.length;i++){
			document.deleteform.elements[i].checked = false;
		}
		document.deleteform.setUnset.value = 'on';
	}
}

function deleteCheck(){
	var x = 0;
	for(var i=0; i<document.deleteform.elements.length;i++) {
		if(document.deleteform.elements[i].checked) {
			x = 1;
			break;
		}
	}
	if(x==0) {
		alert("Please select at least one record to remove link.");
	}
	else {
		if(confirm('Are you sure you want to remove?')){
			document.deleteform.deletebut.value = 1;
			document.deleteform.submit();
		}
	}
}

$(document).ready(function(){
	$('.deleteFile').on('click', function(){
            var ID = $(this).attr('value');
            //alert(ID);
            if(confirm('Are you sure you want to remove link?')){
            document.deleteform.deletebut.value = 1;
            document.deleteform.remove_check.value =ID;
            document.deleteform.submit();
            }
		/*var ID = $(this).attr('value');
                alert(ID); 
		if(confirm('Are you sure you want to remove link?')){
			window.location.href = "<?php echo $_SERVER['PHP_SELF'];?>?muid=<?php echo $muid;?>&hy=<?php echo $hy;?>"+'&deletebut=1&delID='+ID;
		}*/
	});
})
</script>

