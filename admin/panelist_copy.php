<?php
$ALLOW_GROUPS = array(4);
require_once("../auth_auth.php");
require_once '../includes/functions.php';
header('Content-Type: text/html; charset=iso-8859-1');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Competiscan: Enhance your competitive skill</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../includes/styleSheet.css" rel="stylesheet" type="text/css" />
<script type="text/javascript">
<!--
function fixPage() { 	
	if(!window.opener.closed){
		if(window.opener.location.href.indexOf('manageproduct.php')>=0){
			window.opener.location.href = 'manageproduct.php';
		}
		else{
			window.opener.location.reload();
		}
	}
	self.close();
}
function fixPage2() { 	
	if(!window.opener.closed){
		window.opener.location.href = 'manageproduct.php';
	}
	self.close();
}
//-->
</script>
</head>
<?php
if(isset($_REQUEST['productID'])) $productID = (int)$_REQUEST['productID'];
else $productID = 0;
if(isset($_REQUEST['del'])) $del = (int)$_REQUEST['del'];
else $del = 0;
if(isset($_REQUEST['change'])) $change = (int)$_REQUEST['change'];
else $change = 0;

if($change>0){
	sleep(1);
	$check = "SELECT UNIX_TIMESTAMP(actual_addedToDatabase) FROM cscan_product_detail WHERE productID=$productID";
	$check = $DRW->query($check,$DRW_read);
	$data = $DRW->fetch_row($check);
	$time = time();
	$time -= 60;
	if(!empty($data[0]) && $data[0]<$time && $change<5){
		$change++;
		@ob_end_clean();
		header("Location: {$_SERVER['PHP_SELF']}?productID=$productID&del=$del&change=$change");
		exit;
	}
}
if($productID!=0){
	if(isset($_POST['entryID']) && trim($_POST['entryID'])!=''){
		$check = "SELECT productID,gender,age,incomeID,state,OfferExpiryDate,lastSeen,mChannelID,is_state_specific FROM cscan_product_detail WHERE entryID='".$DRW->real_escape_string($_POST['entryID'])."'";
		$check = $DRW->query($check,$DRW_read);
		$data = $DRW->fetch_row($check);
		$newproductID = $data[0];
		$gender = $data[1];
		if(!empty($data[2])){
			$age = explode(',',$data[2]);
		}
		else {
			$age = array();
		}
		if(!empty($data[3])){
			$incomeID = explode(',',$data[3]);
		}
		else {
			$incomeID = array();
		}
		if(!empty($data[4])){
			$state = explode(',',$data[4]);
		}
		else {
			$state = array();
		}
		$OfferExpiryDate = $data[5];
		$lastSeen = $data[6];
		$mChannelID = $data[7];
		$is_state_specific = $data[8];
		$state_diff = array();
		$dates = array();
		if(!empty($newproductID) && $newproductID!=$productID){
			$check = "SELECT gender,age,incomeID,state,is_state_specific FROM cscan_product_detail WHERE productID=$productID";
			$check = $DRW->query($check,$DRW_read);
			$data = $DRW->fetch_row($check);
			$oldgender = $data[0];
			if(!empty($data[1])){
				$oldage = explode(',',$data[1]);
			}
			else {
				$oldage = array();
			}
			if(!empty($data[2])){
				$oldincomeID = explode(',',$data[2]);
			}
			else {
				$oldincomeID = array();
			}
			if(!empty($data[3])){
				$oldstate = explode(',',$data[3]);
			}
			else {
				$oldstate = array();
			}
			$oldis_state_specific = $data[4];
			
			if($is_state_specific || $oldis_state_specific){
				$state_diff = array_diff($state,$oldstate);
			}
			
			$tmpoldgender = array();
			$ageArray = array();
			$sql = "SELECT age_pID,age_pmin,age_pmax FROM cscan_age_product ORDER BY age_psort";
			$result = $DRW->query( $sql,$DRW_read );
			while( $row = $DRW->fetch_row( $result ) ) {
				$ageArray[$row[0]] = array($row[1],$row[2]);
			}
			$check = "SELECT gender,DATEDIFF(CURDATE(),birthdate) as age,incomeID,stateID,birthdate,ppdate FROM cscan_panelists_product pp join cscan_panelists cp on (pp.panelist_id=cp.panelist_id) WHERE productID=$productID";
			$check = $DRW->query($check,$DRW_read);
			while($data = $DRW->fetch_row($check)){
				$pgender = strtoupper(substr($data[0],0,1));
				$page = floor($data[1]/365);
				$pincomeID = $data[2];
				$pstate = $data[3];
				$birthdate = $data[4];
				$dates[] = substr($data[5],0,10);
				$ageID = 0;
				if($birthdate!='0000-00-00'){
					foreach($ageArray as $aID=>$a_array){
						if($page>=$a_array[0] && $page<=$a_array[1]){
							$ageID = $aID;
							break;
						}
					}
				}
				if($pgender!='M' && $pgender!='F'){
					$pgender = 'N';
				}
				
				if($pgender!=$gender){
					if($gender=='N'){
						$gender = $pgender;
					}
					elseif($pgender!='N'){
						$gender = 'B';
					}
				}
				if(!in_array($pgender,$tmpoldgender) && $pgender!='N'){
					$tmpoldgender[] = $pgender;
				}
				if(!empty($ageID)){
					if(!in_array($ageID,$age)){
						$age[] = $ageID;
					}
					$ind = array_search($ageID,$oldage);
					if($ind!==false){
						unset($oldage[$ind]);
					}
				}
				if(!empty($pincomeID)){
					if(!in_array($pincomeID,$incomeID)){
						$incomeID[] = $pincomeID;
					}
					$ind = array_search($pincomeID,$oldincomeID);
					if($ind!==false){
						unset($oldincomeID[$ind]);
					}
				}
				if(!empty($pstate)){
					if(!in_array($pstate,$state)){
						$state[] = $pstate;
					}
					if(count($oldstate)>1){
						$ind = array_search($pstate,$oldstate);
						if($ind!==false){
							unset($oldstate[$ind]);
						}
					}
				}
			}
			if(count($tmpoldgender)>1){
				$oldgender = 'B';
			}
			elseif(count($tmpoldgender)==1){
				$oldgender = $tmpoldgender[0];
			}
			if(count($oldage)==0){
				$oldage[] = '0';
			}
			if(count($oldincomeID)==0){
				$oldincomeID[] = '0';
			}
			if(count($oldstate)==0){
				$oldstate[] = '0';
			}
			if(count($age)==0){
				$age[] = '0';
			}
			if(count($incomeID)==0){
				$incomeID[] = '0';
			}
			if(count($state)==0){
				$state[] = '0';
			}
		}
		if(!empty($newproductID) && $newproductID!=$productID && !empty($_POST['confirm'])){
			$onload = 'fixPage();';
			$sqlU = "UPDATE IGNORE cscan_panelists_product SET productID=$newproductID WHERE productID=$productID";
			$DRW->query($sqlU,$DRW_main);
			
			$sqlU = "DELETE FROM cscan_panelists_product WHERE productID=$productID";
			$DRW->query($sqlU,$DRW_main);
			
			foreach($dates as $d){
				if($d>$lastSeen){
					$lastSeen = $d;
				}
			}
			$sqlU = "UPDATE cscan_product_detail SET gender='".$DRW->real_escape_string($gender)."',
				age='".$DRW->real_escape_string(implode(',',$age))."',incomeID='".$DRW->real_escape_string(implode(',',$incomeID))."',
				state='".$DRW->real_escape_string(implode(',',$state))."',lastSeen='$lastSeen' WHERE productID=$newproductID";
			$DRW->query($sqlU,$DRW_main);
			
			if($del==1) {
				$tmp = array($productID);
				$message = deleteProduct($tmp);
				$onload = 'fixPage2();';
			}
			else{
				$sqlU = "UPDATE cscan_product_detail SET gender='".$DRW->real_escape_string($oldgender)."',
					age='".$DRW->real_escape_string(implode(',',$oldage))."',incomeID='".$DRW->real_escape_string(implode(',',$oldincomeID))."',
					state='".$DRW->real_escape_string(implode(',',$oldstate))."' WHERE productID=$productID";
				$DRW->query($sqlU,$DRW_main);
				updateStateLookup($productID);
			}
			updateStateLookup($newproductID);
			
			echo '<body style="background:#FAF6D2;margin:10px;" onload="'.$onload.'"><div><a href="#" onclick="self.close(); return false;">close</a></div>';
		}
		else{
			echo '<body style="background:#FAF6D2;margin:10px;"><div>&nbsp;</div><div class="bodytext" style="font-weight:bold;">Move Panelists To';
			if($del==1) echo ' (&amp; Delete Product)';
			echo '</div>';
			?>
			<form name="panform" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
			<span class="bodytext">Entry ID:</span> <?php 
			if(!empty($newproductID) && $newproductID!=$productID){
				$error = '';
				$edate = substr($_POST['entryID'],0,10);
				if(!empty($OfferExpiryDate) && $OfferExpiryDate!='0000-00-00'){
					if($mChannelID==2 || $mChannelID==3){
						$off = 0;
					}
					else{
						$off = 259200;//86400*3;
					}
					$OfferExpiryDate_3 = date('Y-m-d',strtotime($OfferExpiryDate) - $off);
				}
				else{
					$OfferExpiryDate_3 = '';
				}
				foreach($dates as $d){
					if($d<$edate){
						$error = "Entry ID ($edate) First Seen ($d) error!";
						break;
					}
					elseif(!empty($OfferExpiryDate_3) && $d>$OfferExpiryDate_3){
						$error = "Offer Expiry ($OfferExpiryDate) Last Seen ($d) error!";
						break;
					}
				}
				echo '<em>'.$_POST['entryID'].'</em>';
				if(empty($error)){
					if(count($state_diff)>0){
						echo '<br /><strong>Correct Panelist State ('.stateName(implode(',',$state_diff)).')?</strong><br />';
					}
					echo ' &nbsp; <input class="button" type="submit" name="rep" value="Move" /> &nbsp; ';
					$confirm = 1;
					$entryID = $_POST['entryID'];
				}
				else{
					echo '<br /><strong>'.$error.'</strong> &nbsp; ';
					$confirm = 0;
					$entryID = '';
				}
			}
			else{
				echo '<em>Not Found</em> &nbsp; <input class="button" type="submit" name="back" value="Back" /> &nbsp; ';
				$confirm = 0;
				$entryID = '';
			}
			?>
			<input class="button" type="submit" name="canc" value="Cancel" onclick="self.close(); return false;" />
			<input type="hidden" name="confirm" value="<?php echo $confirm; ?>" />
			<input type="hidden" name="entryID" value="<?php echo $entryID; ?>" />
			<input type="hidden" name="productID" value="<?php echo $productID; ?>" />
			<input type="hidden" name="del" value="<?php echo $del; ?>" />
			</form>
			<?php
		}
	}
	else{
		echo '<body style="background:#FAF6D2;margin:10px;"><div>&nbsp;</div><div class="bodytext" style="font-weight:bold;">Move Panelists To';
		if($del==1) {
			echo ' (&amp; Delete Product)';
		}
		echo '</div>';
		?>
		<form name="panform" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
		<span class="bodytext">Entry ID:</span> <input type="text" name="entryID" id="entryid" class="input_box" size="30" maxlength="200"  />
		<input class="button" type="submit" name="rep" value="Next" /> &nbsp; <input class="button" type="submit" name="canc" value="Cancel" onclick="self.close(); return false;" />
		<input type="hidden" name="productID" value="<?php echo $productID; ?>" />
		<input type="hidden" name="del" value="<?php echo $del; ?>" />
		</form>
                 <div style="float:left;margin:11px; width:100%; padding-left:150px; font-weight:bold;"> OR </div>
	        <div style="margin-top:15px; margin-bottom:3px;">
		<span class="bodytext">Product ID:</span> <input type="text" id="fetchentryid" name="fetchentryid" class="input_box" size="30" maxlength="200"  />
		<input class="button" type="button" onclick="FetchEntryId()" name="rep2" value="Search" /> &nbsp;		
                <span id="notfound" style="color:red; margin-left:0px; font-weight:10pt; border:0px;" class="input_box"></span> 
                </div>
		<?php
	}
}
?>
                 <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>


 <script type="text/javascript">   
 function FetchEntryId (){ 
            //var pidval=(document.getElementById('fetchentryid').value).trim();
            var pidval = jQuery("#fetchentryid").val();
            if(pidval){
                jQuery.ajax({
            url: "ajaxfetchentryid.php",
            type: "post", //send it through get method
            data:{id:pidval},
            success: function(result) {
             if (result.indexOf("EntryID Not exist") >= 0){
                    jQuery("#notfound").html(result);                 
             }else{
              jQuery("#notfound").html('');
               jQuery("#entryid").val(result);
               }
              }  
        });
           }

    }   
  </script>  
</body>
</html>