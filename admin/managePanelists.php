<?php
$ALLOW_GROUPS = array(43);
require_once("../auth_auth.php");
//include('../includes/pagination.php');
$JQUERY = true;
include 'top.php';
$first_name     =   '';
$last_name      =   '';
$email          =   '';
$phone          =   '';
$primary_address_city =   '';
$primary_address_state=   '';
$primary_address_postalcode =   '';
$date_modified  =   '';
$income         =   '';
$birthdate      =   '';
$gender         =   '';
$rentorown      =   '';
$ownbiz         =   '';

$sql='';
require_once '../includes/paginate.php';


$limit = 20;

if(isset($_REQUEST['p'])) $p = $_SESSION['managepanelist_p'] = $_REQUEST['p'];
elseif(isset($_SESSION['managepanelist_p'])) $p = $_SESSION['managepanelist_p'];
else $p = 0;



$sort=0;
$orderby = " ORDER BY date_modified DESC ";
$stateIDArray = array();
$countries = array('US');
$sqlc = "SELECT DISTINCT countryCode FROM cscan_state JOIN ISO31661_alpha2code ON (code=countryCode) WHERE countryCode<>'US' ORDER BY country";
$rsc = $DRW->query($sqlc,$DRW_read);
while($rowc = $DRW->fetch_row($rsc)) {
	$countries[] = $rowc[0];
}
foreach($countries as $country){
    
	//$sql = "Select stateCode,panelist_stateID from cscan_state WHERE countryCode='".$country."' ORDER BY stateName ASC";
	$result = $DRW->query("Select stateCode,panelist_stateID,stateName from cscan_state WHERE countryCode='".$country."' ORDER BY stateName ASC",$DRW_read);
	while($row = $DRW->fetch_row($result)){
		$stateIDArray[$row[0]] = $row[2];
	}
}

if(isset($_REQUEST['show_All']) || !isset($_REQUEST['p'])){
    $_SESSION['searchpanelist_first_name']='';
    $_SESSION['searchpanelist_last_name']='';
    $_SESSION['searchpanelist_email']='';
    $_SESSION['searchpanelist_birthdate']='';
    $_SESSION['searchpanelist_income']='';
    $_SESSION['searchpanelist_date_modified']='';
    $_SESSION['searchpanelist_postalcode']='';
    $_SESSION['searchpanelist_state']='';
    $_SESSION['searchpanelist_city']='';
    $_SESSION['searchpanelist_phone']='';
    $_SESSION['searchpanelist_rentorown']='';
    $_SESSION['searchpanelist_gender']='';
    $_SESSION['searchpanelist_ownbiz']='';
    $_SESSION['searchpanelist_credit_score']='';
}

if(isset($_REQUEST['search_Submit']) && $_REQUEST['search_Submit']=='Search'){
    $_SESSION['searchpanelist_first_name']= $_REQUEST['first_name'];
    $_SESSION['searchpanelist_last_name']=$_REQUEST['last_name'];
    $_SESSION['searchpanelist_email']=$_REQUEST['email'];
    $_SESSION['searchpanelist_phone']=$_REQUEST['phone'];
    $_SESSION['searchpanelist_city']= $_REQUEST['primary_address_city'];
    $_SESSION['searchpanelist_state']=$_REQUEST['primary_address_state'];
    $_SESSION['searchpanelist_postalcode']= $_REQUEST['primary_address_postalcode'];
    $_SESSION['searchpanelist_date_modified']=$_REQUEST['date_modified'];
    $_SESSION['searchpanelist_income']=$_REQUEST['income'];
    $_SESSION['searchpanelist_birthdate']=$_REQUEST['birthdate'];
    $_SESSION['searchpanelist_gender']= $_REQUEST['gender'];
    $_SESSION['searchpanelist_rentorown']= $_REQUEST['rentorown'];
    $_SESSION['searchpanelist_ownbiz']= $_REQUEST['ownbiz'];
    $_SESSION['searchpanelist_credit_score']= $_REQUEST['credit_score'];
     
} 



   if(!empty($_SESSION['searchpanelist_first_name'])){
        
        $sql.=" And first_name like '".$_SESSION['searchpanelist_first_name']."%'";
    }
    
     if(!empty($_SESSION['searchpanelist_last_name'])){
        
        $sql.=" And last_name like'".$_SESSION['searchpanelist_last_name']."%'";
    }
    
    if(!empty($_SESSION['searchpanelist_email'])){
        $sql.=" And email='".$_SESSION['searchpanelist_email']."'";
    } 
    if(!empty($_SESSION['searchpanelist_phone'])){
        $sql.=" And phone='".$_SESSION['searchpanelist_phone']."'";
    } 
    if(!empty($_SESSION['searchpanelist_city'])){
       
        $sql.=" And primary_address_city='".$_SESSION['searchpanelist_city']."'";
    } 
    if(!empty($_SESSION['searchpanelist_state'])){
       // $primary_address_state= $_SESSION['searchpanelist_state']=$_REQUEST['primary_address_state'];
        $sql.=" And primary_address_state='".$_SESSION['searchpanelist_state']."'";
    } 
    if(!empty($_SESSION['searchpanelist_postalcode'])){
        //$primary_address_postalcode=$_SESSION['searchpanelist_postalcode']= $_REQUEST['primary_address_postalcode'];
        $sql.=" And primary_address_postalcode='".$_SESSION['searchpanelist_postalcode']."'";
    } 
    if(!empty($_SESSION['searchpanelist_date_modified'])){
        
        $sql.=" And date(date_modified)='".$_SESSION['searchpanelist_date_modified']."'";
    } 
    if(!empty($_SESSION['searchpanelist_income'])){
      
        $sql.=" And income='".$_SESSION['searchpanelist_income']."'";
    } 
    if(!empty($_SESSION['searchpanelist_birthdate'])){
       
        $sql.=" And date(birthdate)='".$_SESSION['searchpanelist_birthdate']."'";
    } 
    if(!empty($_SESSION['searchpanelist_gender'])){
       // $gender=$_SESSION['searchpanelist_gender']= $_REQUEST['gender'];
        $sql.=" And gender='".$_SESSION['searchpanelist_gender']."'";
    } 
    if(!empty($_SESSION['searchpanelist_rentorown'])){
       // $rentorown=$_SESSION['searchpanelist_rentorown']= $_REQUEST['rentorown'];
        $sql.=" And rentorown='".$_SESSION['searchpanelist_rentorown']."'";
    } 
    if(!empty($_SESSION['searchpanelist_ownbiz'])){
       // $ownbiz=$_SESSION['searchpanelist_ownbiz']= $_REQUEST['ownbiz'];
        $sql.=" And ownbiz='".$_SESSION['searchpanelist_ownbiz']."'";
    }
    ################ for add credit_score section #####################
     if(!empty($_SESSION['searchpanelist_credit_score'])){
         $sql.=" And credit_score='".$_SESSION['searchpanelist_credit_score']."'";
    } 
    ################ for add credit_score section #####################



  if(isset($_REQUEST['sort'])) 
      $sort = $_SESSION['manageproduct_sort'] = (int)$_REQUEST['sort']; 
  if($sort<0) {
		$ascdesc = 'DESC';
		
	}
	else {
		$ascdesc = 'ASC';
		
	}
        
      
    switch(abs($sort)){
        case 1:
                $orderby = " ORDER BY date(date_modified) $ascdesc ";
                break;
        case 2:
               $orderby = " ORDER BY first_name $ascdesc ";
                break;
        case 3:
                $orderby= " ORDER BY contact_method $ascdesc ";
                break;
        //case 4: //see default
        case 4:
                $orderby= " ORDER BY date(birthdate) $ascdesc ";
                break;
        case 5:
                $orderby= " ORDER BY gender $ascdesc";
                break;
        case 6:
                $orderby= " ORDER BY income $ascdesc";
                break;
        case 7:
                $orderby= " ORDER BY rentorown $ascdesc";
                break;
        case 8:
                $orderby= " ORDER BY ownbiz $ascdesc";
                break;
        case 9:
                $orderby= " ORDER BY credit_score $ascdesc";
                break;    
        default:
                $orderby= " ORDER BY date_modified DESC";
    }
    


if(isset($_SESSION['assigned_admin_userID']) && $_SESSION['assigned_admin_userID']!=0) {
	$assigned_admin_userID = $_SESSION['assigned_admin_userID'];
	
	$addedtext .= " AND assigned_admin_userID=".$_SESSION['assigned_admin_userID'];
}


$sqldata=" SELECT * FROM cscan_contacts_pre where (first_name != '' OR last_name != '') AND familyContactID = '0' AND imported_to_sugar = '0' ".$sql.$orderby;
$numquery = "SELECT COUNT( *) as numrows FROM cscan_contacts_pre where (first_name != '' OR last_name != '') AND familyContactID = '0' AND imported_to_sugar = '0' ".$sql;

$numquery = $DRW->query($numquery,$DRW_read);
	$nrow = $DRW->fetch_row($numquery);
	$numrows = $nrow[0];
$sqldata .= " LIMIT $p,$limit";	
$rs = $DRW->query( $sqldata,$DRW_read );
	$resultCount = $DRW->num_rows( $rs );
	$count = 1 + $p ;
	$currPage = (($p/$limit) + 1);
        
//$paginate = new Paginate();
//$paginate->paginate("SELECT * FROM cscan_contacts_pre where (first_name != '' OR last_name != '') AND familyContactID = '0' AND imported_to_sugar = '0' ".$sql.$orderby." ", 10);

//pagination and query
/*
$date_modified = '2009-09-28 00:00:00';//'0000-00-00 00:00:00';
$sel = $DRW->query("SELECT COUNT(*) FROM cscan_contacts_pre where (first_name != '' OR last_name != '') AND familyContactID = '0' AND imported_to_sugar = '0' AND date_modified>='$date_modified'",$DRW_read);
$row = $DRW->fetch_row($sel);
$count = $row[0];
list($limittxt,$limitline) = pagination($count,'');
$result = $DRW->query("SELECT * FROM cscan_contacts_pre where (first_name != '' OR last_name != '') AND familyContactID = '0' AND imported_to_sugar = '0' AND date_modified>='$date_modified' ORDER BY date_modified$limittxt",$DRW_read);
echo $limitline;
*/


function doSort($sort,$dosort,$spacer='<br />'){
    if($sort==($dosort*-1) || $sort!=$dosort) {
            print "$spacer<a href=\"".$_SERVER['PHP_SELF']."?sort=$dosort&p=0\" class=\"blue\">sort</a>";
    }
    else{
            print "$spacer<a href=\"".$_SERVER['PHP_SELF']."?sort=-$dosort&p=0\" class=\"blue\">sort</a>";
    }
}
?>
<script type = "text/javascript">
var all_selected = false;
function selectAll() {
	if (all_selected) {
		all_selected = false;
	} else {
		all_selected = true;
	}
	
	if (all_selected) {
		$("form input:checkbox").attr("checked", true);
	} else {
		$("form input:checkbox").attr("checked", false);
	}
}

function validate() {
	var errors = "";
	
	if ($("#actions").val() == "") {
		errors += "ERROR: 'With Selected' is empty!\n";
	}
	
	if (errors == "") {
		return true;
	} else {
		alert(errors);
		return false;
	}
}
</script>
<script type="text/javascript" src="js_calendar/calendar.js"></script>
<div>&nbsp;</div>
<table width="100%" border="0" cellspacing="0" cellpadding="5" class="text">
  <tr><td class="adminhead" align="center"><div id="pcontainer">PANELIST MANAGEMENT</div></td></tr>
  
  
  
  
  <!-- search and right buttons start-->
  <tr>
    <td class="bodyText">
        
     
	    <form method="post" name="prodForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" style="display:inline;" id="manage_product">
		<table border="0" cellspacing="0" cellpadding="1" class="text">
	    	<tr>
	    	<td align="right"><strong>First Name:</strong> </td>
	    	<td><input type="text" name="first_name" size="40" maxlength="255" class="input_box" value="<?php echo htmlspecialchars($_SESSION['searchpanelist_first_name'],ENT_QUOTES); ?>" /></td>
	    	<td>&nbsp;</td>
	    	
	    	<td align="right"><strong>Last Name:</strong></td>
	    	<td><input type="text" name="last_name" size="40" maxlength="255" class="input_box" value="<?php echo htmlspecialchars($_SESSION['searchpanelist_last_name'],ENT_QUOTES); ?>" /></td>
	    	<td>&nbsp;</td>
	    	</tr>
	    	<tr>
	    	<td align="right"><strong>Email Address:</strong></td>
	    	<td><input type="text" name="email" size="40" class="input_box" value="<?php echo htmlspecialchars($_SESSION['searchpanelist_email'],ENT_QUOTES); ?>" /></td>
	    	<td>&nbsp;</td>
	    	
                <td align="right"><strong>Phone Number:</strong></td>
                <td><input type="text" name="phone" size="40" maxlength="255" class="input_box" value="<?php echo htmlspecialchars($_SESSION['searchpanelist_phone'],ENT_QUOTES); ?>" /></td>
                <td>&nbsp;</td>
                </tr>

                <tr>
                <td align="right"><strong>City:</strong></td>
                <td><input type="text" name="primary_address_city" size="40" maxlength="255" class="input_box" value="<?php echo htmlspecialchars($_SESSION['searchpanelist_city'],ENT_QUOTES); ?>" /></td>
                <td>&nbsp;</td>
                
                <td align="right"><strong>State:</strong></td>
                <td>
                    <select name="primary_address_state" style="width:297px;"class="input_box">
                    <option value="">&nbsp;</option><?php 
                    foreach($stateIDArray as $stateId=>$ID){
                            echo "<option value=\"{$stateId}\"";
                            if($_SESSION['searchpanelist_state']==$stateId){
                                    echo ' selected="selected"';
                            }
                            echo ">$ID</option>";
                    }
                    ?></select>
                    
                   </td>
                <td>&nbsp;</td>
                </tr>
                        
               <tr>
                <td align="right"><strong>Zip/Postal Code:</strong></td>
                <td><input type="text" name="primary_address_postalcode" size="40" maxlength="255" class="input_box" value="<?php echo htmlspecialchars($_SESSION['searchpanelist_postalcode'],ENT_QUOTES); ?>" /></td>
                <td>&nbsp;</td>
               
                <td align="right"><strong>Date Modified:</strong></td>
                <td>
                    <input class="input_box" name="date_modified" type="text" value="<?php echo $_SESSION['searchpanelist_date_modified'];?>" size="33" readonly="readonly" /> 
                <a href="#" onclick="displayCalendar(document.prodForm.date_modified,'yyyy-mm-dd',this); return false;">
                    <img name="popcal5" src="js_calendar/images/getcal.gif" border="0" alt="" style="vertical-align:bottom;" />
                </a>    
                <a onclick="document.prodForm.date_modified.value=''; return false;" href="#">clear</a> 
                  </td>
                <td>&nbsp;</td>
                </tr> 
               <tr>
                <td align="right"><strong>Income:</strong></td>
                <td>
                <select name="income" style="width:297px;"class="input_box">
                    <option value="">choose</option>
                    <?php
                            $selArray = array("Under $25k"=>'Under $25k',"$25k-$49k"=>'$25k-$49k',"$50k-$74k"=>'$50k-$74k',"$75k-$99k"=>'$75k-$99k',"$100k-$149k"=>'$100k-$149k',"$150k+"=>'$150k+');
                            foreach($selArray as $num=>$show) {
                                    echo "\n\t\t\t\t\t\t\t\t\t\t\t\t\t<option value=\"{$num}\"";
                                    if($_SESSION['searchpanelist_income']==$num) print ' selected';
                                    echo ">$show</option>";
                            }
                    ?>
                </select>
                    
                   </td>
                <td>&nbsp;</td>
               
                <td align="right"><strong>Age:</strong></td>
                <td>
                <input class="input_box" name="birthdate" type="text" value="<?php echo $_SESSION['searchpanelist_birthdate'];?>" size="33" readonly="readonly" /> 
                <a href="#" onclick="displayCalendar(document.prodForm.birthdate,'yyyy-mm-dd',this); return false;">
                    <img name="popcal5" src="js_calendar/images/getcal.gif" border="0" alt="" style="vertical-align:bottom;" />
                </a>    
                <a onclick="document.prodForm.birthdate.value=''; return false;" href="#">clear</a>    
                  </td>
                <td>&nbsp;</td>
                </tr>    
                
                <tr>
                <td align="right"><strong>Gender:</strong></td>
                <td>
                <select name="gender" style="width:297px;"class="input_box">
                <option value="">choose</option>
                <?php
                    $selArray = array("M"=>'Male',"F"=>'Female');
                    foreach($selArray as $num=>$show) {
                            echo "\n\t\t\t\t\t\t\t\t\t\t\t\t\t<option value=\"{$num}\"";
                            if($_SESSION['searchpanelist_gender']==$num) print ' selected';
                            echo ">$show</option>";
                    }
                ?>
              </select>    
                    
                 </td>
                <td>&nbsp;</td>
               
                <td align="right"><strong>Home Ownership:</strong></td>
                <td>
                <select name="rentorown" style="width:297px;"class="input_box">
                        <option value="">choose</option>
                    <?php
                            $selArray = array("Rent"=>'Rent',"Own"=>'Own');
                            foreach($selArray as $num=>$show) {
                                    echo "\n\t\t\t\t\t\t\t\t\t\t\t\t\t<option value=\"{$num}\"";
                                    if($_SESSION['searchpanelist_rentorown']==$num) print ' selected';
                                    echo ">$show</option>";
                            }
                    ?>
                </select>
                  </td>
                <td>&nbsp;</td>
                </tr>    
                 <tr>
                <td align="right"><strong>Business Owner:</strong></td>
                <td>
                <select name="ownbiz" style="width:297px;"class="input_box">
                <option value="">choose</option>
                <?php
                        $selArray = array("Yes"=>'Yes',"No"=>'No');
                        foreach($selArray as $num=>$show) {
                                echo "\n\t\t\t\t\t\t\t\t\t\t\t\t\t<option value=\"{$num}\"";
                                if($_SESSION['searchpanelist_ownbiz']==$num) print ' selected';
                                echo ">$show</option>";
                        }
                ?>
            </select>    
               </td>
                <td>&nbsp;</td>      
               <td align="right"><strong>Credit Score:</strong></td>
                <td>
                <select name="credit_score" style="width:297px;"class="input_box">
                <option value="">choose</option>
                <?php
                                                                                                                                        
                $sql = "select id,credit_score from cscan_credit_score";
                $result = $DRW->query($sql,$DRW_read);
                while($row = $DRW->fetch_row($result)) {
                    $id             =   $row[0];
                    $credit_score   =   $row[1];
                    echo "\n\t\t\t\t\t\t\t\t\t\t\t\t\t<option value=\"{$id}\"";
                        if($_SESSION['searchpanelist_credit_score']==$id) print ' selected';
                            echo ">$credit_score</option>";     
                    }
                ?>

            </select>    
               </td>
                    <td>&nbsp;</td>           
                </tr>          
                        
                        
                        
			<tr>
                            <td align="right">&nbsp;</td>
		
			<td><input class="button" style="width:60px;" type="submit" name="search_Submit" value="Search"  />
			&nbsp;&nbsp;&nbsp;&nbsp;
			<input class="button" style="width:70px" type="submit" name="show_All" value="Show All" /></td>
			</tr>
			</table>
			<input type="hidden" name="p" value="0" />
		</form>
    </td>
   </tr>    
  
  
  
  
<tr>
<td class="bodyText">
<form action = "panelist_import.php" method = "post" onsubmit = "return validate()" >
With Selected: 
<select id = "actions" name = "actions">
	<option value="">&nbsp;</option><option value="Move to CRM">Move to CRM</option>
        <?php if(checkGroup(64)){ ?>
        <option value="Delete">Delete</option>
        <?php }?>
</select> <input type = "submit" name = "submit" value = "Update" class="button" />
<br>
<br>
<?php //$paginate->print_page_links($sort); ?>
<br>
<br>
<table width ="100%" class="text">
	<tr class="head1">
		 <?php if(checkGroup(64)){ ?>
                <th width="5%" class="adminhead"><input type = "checkbox" name = "select_all" value = "" onclick = "selectAll()"/></th>
                 <?php }?>
                <th width="12%" class="adminhead">Date Modified <?php doSort($sort,1); ?></th>
		<th width="12%" class="adminhead">Name <?php doSort($sort,2); ?></th>
		<th width="12%" class="adminhead">Contact? <?php doSort($sort,3); ?></th>
		<th width="12%" class="adminhead">Birthday <?php doSort($sort,4); ?></th>
		<th width="8%" class="adminhead">Gender <?php doSort($sort,5); ?></th>
		<!--<th>Ethnicity</th> -->
		<th width="12%" class="adminhead">Income <?php doSort($sort,6); ?></th>
                <th width="8%" class="adminhead">Home Ownership <?php doSort($sort,7); ?></th>
		<th width="8%" class="adminhead">Business Owner <?php doSort($sort,8); ?></th>
                <th width="12%" class="adminhead">Credit Score <?php doSort($sort,9); ?></th>
		<!--<th>FICO score?</th>
		<th>Health Insurance</th>
		<th>Life Insurance</th>
		<th>Dental Insurance</th>
		<th>Vision Insurance</th>
		<th>Supplemental Insurance</th>
		<th>Auto Insurance</th>
		<th>Home Owners/Renters Insurance</th>
		<th>401k</th>
		<th>Other Investments</th>
		<th>Checking or Savings Account</th>
		<th>Credit Card</th>
		<th>Mortgage</th>
		<th>Educational Loan</th>
		<th>Cell Phone</th>
		<th>Home Phone</th>
		<th>Internet Access</th>
		<th>TV Provider</th>
		<th>Imported to Sugar</th>-->
	</tr>
<?php
$back = '#ffffff';

//$rows = $paginate->get_data();

//foreach ($rows as $row) {
    
if( $resultCount > 0 ) {
		$className='';
		while( $row = $DRW->fetch_array($rs) ) {    
    
    
    
?>
	<tr style="background-color: <?php echo $back; ?>;" valign="top">
	 <?php if(checkGroup(64)){ ?>	
            <td>
			<input type = "checkbox" name = "panelists[]" value = "<?php print $row['id'] ?>" />
		</td>
         <?php }?>
		<td>
			<?php echo date('n/d/Y g:ia',strtotime($row['date_modified'])).'<br /><em>'.$row['ip_address'].'</em>'; ?>
		</td>
		<td><?php echo $row['first_name']; ?> <?php echo $row['last_name']; ?><br/>
			<?php echo $row['primary_address_street']; ?><br/>
			<?php 
			if($row['primary_address_city']) {
				echo $row['primary_address_city'].',';
			}
			?>	<?php echo $row['primary_address_state']; ?>	<?php echo $row['primary_address_postalcode']; ?><br/>
			<?php echo $row['phone']; ?><br/>
			<?php echo $row['email']; ?>
		</td>
		<td>
			<?php echo $row['contact_method']; ?>
		</td>
		<td>
			<?php echo $row['birthdate']; ?>
		</td>
		<td>
			<?php echo $row['gender']; ?>
		</td>
		<!--<td>
			<?php //echo $row['ethnicity']; ?>
		</td> -->
		<td>
			<?php echo $row['income']; ?>
		</td>
                <td>
			<?php
			if($row['rentorown']!='') {
				echo $row['rentorown'];//.'<br/><em>'.$row['HomeOwnersRentersInsurance_v'].'</em>';
			}
			else {
				echo '&nbsp;';
			}
			?>
		</td>	
		<td>
			<?php echo $row['ownbiz']; ?>
		</td>
                
                <td>
                    <?php 
                    if(!empty($row['credit_score'])){
                        $crid   =   $row['credit_score'];
                    
                        $sql = "select credit_score from cscan_credit_score where id='".$crid."'";
                        $result = $DRW->query($sql,$DRW_read);
                        $row = $DRW->fetch_row($result);
                        if(!empty($row[0])){
                            echo $row[0];
                        }
                    }else{
                        echo '-';
                    }
                    
                    ?>
		</td>
                <!--
		<td>
			<?php echo $row['FICOscore']; ?>
		</td>
		<td>
			<?php
			if($row['HealthInsurance']==1) {
				echo $row['HealthInsurance_p'].'<br/><em>'.$row['HealthInsurance_v'].'</em>';
			}
			else {
				echo '&nbsp;';
			}
			?>
		</td>
		<td>
			<?php
			if($row['LifeInsurance']==1) {
				echo $row['LifeInsurance_p'].'<br/><em>'.$row['LifeInsurance_v'].'</em>';
			}
			else {
				echo '&nbsp;';
			}
			?>
		</td>			
		<td>
			<?php
			if($row['DentalInsurance']==1) {
				echo $row['DentalInsurance_p'].'<br/><em>'.$row['DentalInsurance_v'].'</em>';
			}
			else {
				echo '&nbsp;';
			}
			?>
		</td>			
		<td>
			<?php
			if($row['VisionInsurance']==1) {
				echo $row['VisionInsurance_p'].'<br/><em>'.$row['VisionInsurance_v'].'</em>';
			}
			else {
				echo '&nbsp;';
			}
			?>
		</td>		
		<td>
			<?php
			if($row['SupplementalInsurance']==1) {
				echo $row['SupplementalInsurance_p'].'<br/><em>'.$row['SupplementalInsurance_v'].'<br/>'.$row['SupplementalInsurance_m'].'</em>';
			}
			else {
				echo '&nbsp;';
			}
			?>
		</td>		
		<td>
			<?php
			if($row['AutoInsurance']==1) {
				echo $row['AutoInsurance_p'];//.'<br/><em>'.$row['AutoInsurance_v'].'</em>';
			}
			else {
				echo '&nbsp;';
			}
			?>
		</td>		
		<td>
			<?php
			if($row['HomeOwnersRentersInsurance']==1) {
				echo $row['HomeOwnersRentersInsurance_p'];//.'<br/><em>'.$row['HomeOwnersRentersInsurance_v'].'</em>';
			}
			else {
				echo '&nbsp;';
			}
			?>
		</td>		
		<td>
			<?php
			if($row['401k']==1) {
				echo $row['401k_p'];//.'<br/><em>'.$row['401k_v'].'</em>';
			}
			else {
				echo '&nbsp;';
			}
			?>
		</td>			
		<td>
			<?php
			if($row['OtherInvestments']==1) {
				echo $row['OtherInvestments_p'].'<br/><em>'.$row['OtherInvestments_m'].'</em>';//.$row['OtherInvestments_v'].'<br/>'
			}
			else {
				echo '&nbsp;';
			}
			?>
		</td>
		<td>
			<?php
			if($row['CheckingSavingsAccount']==1) {
				echo $row['CheckingSavingsAccount_p'];//.'<br/><em>'.$row['CheckingSavingsAccount_v'].'</em>';
			}
			else {
				echo '&nbsp;';
			}
			?>
		</td>	
		<td>
			<?php
			if($row['CreditCard']==1) {
				echo $row['CreditCard_p'];//.'<br/><em>'.$row['CreditCard_v'].'</em>';
			}
			else {
				echo '&nbsp;';
			}
			?>
		</td>			
		<td>
			<?php
			if($row['Mortgage']==1) {
				echo $row['Mortgage_p'];//.'<br/><em>'.$row['Mortgage_v'].'</em>';
			}
			else {
				echo '&nbsp;';
			}
			?>
		</td>		
		<td>
			<?php
			if($row['LoanEducational']==1) {
				echo $row['LoanEducational_p'];//.'<br/><em>'.$row['LoanEducational_v'].'</em>';
			}
			else {
				echo '&nbsp;';
			}
			?>
		</td>	
		<td>
			<?php
			if($row['WirelessCellPhone']==1) {
				echo $row['WirelessCellPhone_p'];//.'<br/><em>'.$row['WirelessCellPhone_v'].'</em>';
			}
			else {
				echo '&nbsp;';
			}
			?>
		</td>				
		<td>
			<?php
			if($row['HomePhone']==1) {
				echo $row['HomePhone_p'];//.'<br/><em>'.$row['HomePhone_v'].'</em>';
			}
			else {
				echo '&nbsp;';
			}
			?>
		</td>
		<td>
			<?php
			if($row['InternetAccess']==1) {
				echo $row['InternetAccess_p'];//.'<br/><em>'.$row['InternetAccess_v'].'</em>';
			}
			else {
				echo '&nbsp;';
			}
			?>	
		<td>
			<?php
			if($row['TVProvider']==1) {
				echo $row['TVProvider_p'];//.'<br/><em>'.$row['TVProvider_v'].'</em>';
			}
			else {
				echo '&nbsp;';
			}
			?>
		</td>
		<td>
			<?php
			if($row['imported_to_sugar']==1) {
				echo 'Yes';
			}
			else {
				echo 'No';
			}
			?>
		</td>-->
	</tr>
	<?php
		if($back=='#ffffff'){
			$back='#E8E8FF';
		}
		else{
			$back='#ffffff';
		}
	}
}
else {
?>
    <tr><td colspan="9" class="error" align="center">No Product Found.
    </td></tr>
<?php
	}
?>
  <tr>
	<td colspan="9">
		<table border="0" width="100%" cellspacing = "0"  cellpadding ="5">
			<tr>
				<td colspan = "2"> &nbsp;</td>
			</tr>
<?php
		if($sort>0) $sorttext = '&sort='.$sort;
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
                
		if($rowcnt>0){
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
		}
?>
	</table></td></tr>

</table>
</form>
<div>&nbsp;</div>
<?php
//$paginate->print_page_links();
?>
</td>
</tr>
</table>
<?php
include 'bottom.php';
?>