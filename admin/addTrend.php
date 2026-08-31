<?php 
$ALLOW_GROUPS = array(22);
require_once "../auth_auth.php";
require_once '../includes/functions.php';
include 'top.php';
require_once '../class/Trend.php';
require_once '../class/officetophp.php';
if(!defined('ENV')){
define('ENV',getenv('SERVER_NAME'));
}
$pdftext =''; 
$doonload = '';
$audience_id = array();
$country_id ="0";
$trendname = '';
$trendlink = 'https://files.competiscan.com/downloads';
$category = 0;
$trenddate_y = date('Y');
$trenddate_m = date('m');
$trenddate_d = date('d');
$comboIDs=array();
$trend_file_name="";

?>
<table width="100%" border="0" cellspacing="0" cellpadding="5" class="text">
  <tr>
	<td class="adminhead" align="center">CHANGE TREND INFORMATION</td>
  </tr>
  <tr>
	<td align ="right" class="bodytext"><span class="error" style="font-weight:bold;">* required field</span></td>
  </tr>
<?php
    if(isset($_REQUEST['id'])) $updID = $_REQUEST['id'];
	else $updID = '';
       if (isset($_POST['scsc_comboIDs'])){
            $comboIDs = trim($_POST['scsc_comboIDs']);
       } else {
            $comboIDs = '';
       }
    if(isset($_POST['submity'],$_POST['submit']) && ($_POST['submit']=='Save' || $_POST['submit']=='Save & Add More')){
            $addData = addTrend($_POST);
            if($addData==2){
                $msg="Required parameter missing";
                echo "<tr><td align=\"center\">$msg</td></tr>";
            }elseif($addData== 5){
                $actMsg="Added";
                echo "<tr><td align=\"center\">Trend has been $actMsg sucessfully.</td></tr>";
            }
            if($_POST['submit'] == 'Save & Add More'){
                     ob_end_clean();
                     header("Location: addTrend.php?a=1");
                     exit;
             }
                
    }     
    if(isset($_POST['submity'],$_POST['submit']) && $_POST['submit']=='Update'){           
        $updateData = updateTrend($_POST);
         if($updateData==2){
                $msg="Required parameter missing";
                echo "<tr><td align=\"center\">$msg</td></tr>";
                ob_end_clean();
                header("Location: addTrend.php?id=$updID");
                exit;
            }elseif($updateData== 5){
                $actMsg="Update";
                 echo "<tr><td align=\"center\">Trend has been $actMsg sucessfully.</td></tr>";
                ob_end_clean();
                header("Location: manageTrends.php");
                exit;
               
            }
                         
    }
    if($updID!='') {
        $editRS = getTrendById($updID);
        //print_r($editRS);
        $trendname = $editRS['trend_name'];
        $trendlink = $editRS['trend_link'];
        $audience_id = explode(',', $editRS['audience_id']);
        $country_id = $editRS['country_id']; 
        $trenddate_y = substr($editRS['trend_date'],0,4);
        $trenddate_m = substr($editRS['trend_date'],5,2);
        $trenddate_d = substr($editRS['trend_date'],8,2);
        $comboIDs = getAllCategoryByTrendId($updID);
        $trend_file_path = $editRS['file_path'];
        $trend_file_name = $editRS['file_name'];
            if($trend_file_path!='' && $trend_file_name!=''){
              //$pdftext = 'Current Document: [<a href="../trendDocument.php?id='.$updID.'" target="_blank">'.$trend_file_name.'</a>]';
              //############### ADD ENCODE TREND ID############
                //if(ENV == 'localhost' || ENV == 'demo.competiscan.com'){
                    $pdftext = 'Current Document: [<a href="../trendDocument.php?id='.$editRS['rndtrend_id'].'" target="_blank">'.$trend_file_name.'</a>]';
               // }
            }       
	}
?>
    <tr>
                
        <td align="center">
        <form method="post" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" onsubmit="return validate();" enctype="multipart/form-data" >
        <table border="0" cellspacing="0" cellpadding="0"> <tr><td style="border:solid 1px #14734F;">
        <table border="0" cellspacing="0" cellpadding="4">
                <tr>
                        <td class="subhead" align="center" colspan="2">
                        <?php if($updID!='')  echo 'UPDATE'; 
                                  else echo 'ADD'; ?> TREND INFO</td>
                </tr>
                <tr>
                <td colspan="2">&nbsp;</td>
                 </tr>
                <tr>
                        <td class="bodytext" align="right" valign="top"><span class='error'>*</span> Trend Title:</td>
                        <td>
                            <input type="text" name="trendname" size="60" class="input_box" maxlength="200" value="<?php echo htmlspecialchars($trendname,ENT_QUOTES);?>" />
                        </td>

                </tr>
                <!--<tr>
                        <td class="bodytext" align="right"><span class="error">*</span> Trend URL:</td>
                        <td><input type="text" name="url" size="60" class="input_box" maxlength="200" value="<?php echo htmlspecialchars($trendlink, ENT_QUOTES); ?>"/></td>
                </tr>
                <tr>
                    <td class="bodytext" colspan="2" align="center">OR</td>
                 </tr> -->
                <tr>
                        <td class="bodytext" align="right" valign="top"><span class="error">*</span> Document:</td>
                        <td>
                            <input type="file" name="trend_document"  size="60" class="input_file" onchange="check_file_ext(this);"  /><br/>
                            <span class="error">Hint: Only allowed extension(.pdf,.pptx,.docx,.csv).</span>
                            <input type="hidden" name="trend_document_hidden" value="<?php if($trend_file_name!="" && $updID!="") {echo $trend_file_name;} ?>"/>
                        </td>
                </tr>
                <tr>
                    <td class="bodytext" colspan="2" align="center" ><?php echo $pdftext; ?></td>
                </tr>
                <tr>
                        <td class="bodytext" align="right" valign="top"><span class="error">*</span> Audience:</td>
                        <td>
                            <select name ="audience_id[]" id ="audience_validate" multiple="multiple" size="3" class="combo_box"><option <?php if(empty($audience_id)){ echo "selected";} ?> value="">--audience--</option>
                                    <?php 
                                    $mailing_panel = getMailingPanel();
                                   // print_r($audience_id);die;
                                    foreach($mailing_panel as $mid=>$name){ 
                                        /*if(!in_array($mid,$_SESSION['sess_mpanel'])){
                                                continue;
                                        }*/
                                        ?>
                                    <option  <?php if(in_array($mid,$audience_id)) { echo "selected"; } ?> value="<?php echo $mid;?>" ><?php echo htmlspecialchars($name, ENT_QUOTES); ?></option>
                                    <?php }  ?>
                                </select>
                        </td>
                </tr>
                <tr>
                    <td class="bodytext" align="right" valign="top"><span class='error'>*</span>Sector / Category / Sub Category:</td>
                <td class ="bodytext">
                <?php
                $coreArray = array();
                $sql = "SELECT sectorID FROM cscan_sector WHERE sectorSearchActive=1 AND parentID=0 AND is_core=1";
                $rs = $DRW->query($sql,$DRW_read);
                while($row = $DRW->fetch_array($rs)) {
                        //$javascript .= "coreArray[coreArray.length] = $row[0];\n";
                        $coreArray[] = $row[0];
                }
               ?>
                <div id="scsc_combos">
                    <?php  
                        if(!empty($comboIDs)) {
                        $comboIDs_split = @explode('|',$comboIDs);
                      foreach($comboIDs_split as $scsc_combo){ ?>
                          <div id="combo<?php echo $scsc_combo;?>" style="font-weight: bold; margin-bottom: 2px;">
                               <?php  if(!empty($scsc_combo)){
                                        list($s,$c,$sc,$ssc) = explode('_',$scsc_combo);
                                        if(!empty($s) || !empty($c) || !empty($sc) || !empty($ssc)){
                                                $scsc_combo_text = sectorName($s).' / '.sectorName($c).' / '.sectorName($sc).' / '.sectorName($ssc);
                                                echo $scsc_combo_text;
                                                ?>
                                                <a onclick ="removeSCSC('<?php echo $scsc_combo; ?>');" href="#">Remove</a> 
                                      <?php   }
                                } ?>
                          </div>
                        <?php   } } ?>   

                </div>
                <div style="margin:4px;">
                <div style="float:left;padding:4px;border: dashed 1px #000000;">
                <div id="sectorID_div">
                Sector
                <select name="combo_sid" id="combo_sid" class="combo_box" onchange="clearSCSC();" style="display:block;"><option value="0">&nbsp;</option>
                <?php 
                $sector = getSector();
                foreach($sector as $id=>$name){
                    /*if(!in_array($id,$_SESSION['sess_sector'])){
                                continue;
                            }*/
                        if(checkSector($id)){ ?>
                            <option  <?php //if(in_array($id,$audience_id)) { echo "selected"; } ?> value="<?php echo $id;?>" ><?php echo htmlspecialchars($name, ENT_QUOTES); ?></option> 
               <?php }
                }
                ?>
                </select>
                </div>
                <div id="categoryID_div" style="margin-top:5px;">
                Category
                 <select name="combo_cid" id="combo_cid" class="combo_box" onchange="do_SCSC(document.frm1.combo_cid,'cid',document.frm1.combo_scid,true);" style="display:none;">
                     <option value="0">&nbsp;</option>
                 </select>
                </div>
                <div id="subCategoryID_div" style="margin-top:5px;">
                Sub Category
                <select name="combo_scid" id="combo_scid" class="combo_box" style="display:none;" onchange="do_SCSC(document.frm1.combo_scid,'scid',document.frm1.combo_sscid,true);">
                    <option value="0">&nbsp;</option>
                </select>
                </div>
                <div id="subSubCategoryID_div" style="margin-top:5px;">
                Sub Sub Category
                <select name="combo_sscid" id="combo_sscid" class="combo_box" style="display:none;"><option value="0">&nbsp;</option></select>
                </div>
                </div>
                <div style="padding:4px;clear:left;">
                <a href="#" onclick="add_SCSC(); return false;" id="add_SCSC_link">Add</a>
                </div>
                </div>
                <input type="hidden" name="scsc_comboIDs" id="scsc_comboIDs" value="<?php if($comboIDs!='0_0_0' && $comboIDs!='0_0_0_0' && 
                        !empty($comboIDs)){ echo $comboIDs;
                                                       $c1 = @explode('|',$comboIDs);
                                                        foreach($c1 as $c){
                                                                $c2 = @explode('_',$c);
                                                                if(count($c2)>=3 && (!checkSector($c2[0]) || !checkCategory($c2[1]) || !checkSubCategory($c2[2]))){
                                                                        $nopermission = true;
                                                                }					
                                                        }} ?>"/>
               <!-- <input type="hidden" name="co_comboIDs" value="<?php //echo $co_comboIDs; ?>" />-->
                <input type="hidden" name="scsc_combo_edit" value="" />
                </td>
               </tr>
                <tr>
                        <td class="bodytext" align="right" valign="top"><span class="error">*</span>Country:</td>
                        <td class ="bodytext">
                            <input type="radio" name="country" size="60" class="input_box"  <?php if($country_id!='' && $country_id==1){ echo "checked"; }?> value="1"/>UNITED STATES
                            <input type="radio" name="country" size="60" class="input_radio" <?php if($country_id!='' && $country_id==3){ echo "checked"; }?>  value="3"/>CANADA
                            <input type="radio" name="country" size="60" class="input_box"   <?php if($country_id=='0'){ echo "checked"; } ?> value="0"/>BOTH

                        </td>
                </tr>
                <tr>
                        <td class="bodytext" align="right">Date Sort:</td>
                        <td class="bodytext"><?php 
                        $start_year = 2005;
                        $to_year = (int)date('Y');
                        print "<select name=\"trenddate_y\" size=\"1\" class=\"input_box\"><option value=\"00\"></option>";
                        for($i=$start_year;$i<=$to_year;$i++){
                                print "<option value=\"$i\"";
                                if($i==$trenddate_y) print " selected";
                                print ">$i";
                        }
                        print "</select>
                        <select name=\"trenddate_m\" size=\"1\" class=\"input_box\"><option value=\"00\"></option>";
                        $month_name = array('01'=>"January",'02'=>"February",'03'=>"March",'04'=>"April",'05'=>"May",'06'=>"June",'07'=>"July",'08'=>"August",'09'=>"September",'10'=>"October",'11'=>"November",'12'=>"December");
                        foreach($month_name as $key=>$value){
                                print "<option value=\"$key\"";
                                if($key==$trenddate_m) print " selected";
                                print ">$value ($key)";
                        }
                        print "</select> <select name=\"trenddate_d\" size=\"1\" class=\"input_box\"><option value=\"00\"></option>";
                        for($i=1;$i<=31;$i++){
                                $day = str_pad($i,2,'0',STR_PAD_LEFT);
                                print "<option value=\"$day\"";
                                if($day==$trenddate_d) print " selected";
                                print ">$day";
                        }
                        print "</select>";
                        ?>
                        </td>
                </tr>
                <tr>
                <td colspan="2">&nbsp;</td>
                 </tr>
            <tr>
                <td>&nbsp;</td>
                <td>
                <?php if($updID == ''){?>
                <input class="button" type="submit" name="submit" value="Save" />
                <input class="button" type="submit" name="submit" value="Save &amp; Add More" />
                <?php } else{ ?>
                <input class="button" type="submit" name="submit" value="Update" />
                <input type="hidden" name="id" value="<?php echo $updID; ?>" />
                <?php }?>
                <input class="button" type="button" value="Cancel" onclick="location.href='manageTrends.php'; return false;" />
                </td>
            </tr>
	</table>
	</td></tr>
	</table>
	<input type="hidden" name="submity" value="1" />
     </form>
    </td></tr>
</table>
<script type="text/JavaScript"> 
var _validFileExtensions = [".pdf", ".xlsx", ".csv", ".docx", ".pptx"];    
function check_file_ext(oInput) {
    if (oInput.type == "file") {
        var sFileName = oInput.value;
         if (sFileName.length > 0) {
            var blnValid = false;
            for (var j = 0; j < _validFileExtensions.length; j++) {
                var sCurExtension = _validFileExtensions[j];
                if (sFileName.substr(sFileName.length - sCurExtension.length, sCurExtension.length).toLowerCase() == sCurExtension.toLowerCase()) {
                    blnValid = true;
                    break;
                }
            }
             
            if (!blnValid) {
                alert("Sorry, " + sFileName + " is invalid, allowed extensions are: " + _validFileExtensions.join(", "));
                oInput.value = "";
                return false;
            }
        }
    }
    return true;
}
function validate()
{
    var trendname=document.frm1.trendname.value=trimspace(document.frm1.trendname.value);
    var trend_document=document.forms["frm1"]["trend_document"].value;
    var trend_document_hidden=document.forms["frm1"]["trend_document_hidden"].value;
    var audienceSelection=document.forms["frm1"]["audience_id[]"].value;
    var sectorSelection=document.forms["frm1"]["combo_sid"].value;
    var catgorySelection=document.forms["frm1"]["combo_cid"].value;
    var scsc_comboIDs=document.forms["frm1"]["scsc_comboIDs"].value;
    alert(scsc_comboIDs);
  if(trendname == '')
    {
            alert('Please enter a trend name.');
            document.frm1.trendname.focus();
            return false;
    }
   
   if(trend_document== '' && trend_document_hidden=='')
    {
            alert('Please upload document.');
            document.frm1.trend_document.focus();
            return false;
    }
   
   if(audienceSelection==0 ||audienceSelection=='')
      {
          alert("Please select at least one audience.");
          return false;
      }
    if((sectorSelection==0 ||sectorSelection=='') && scsc_comboIDs=='')
      {
          alert("Please select at least one sector212122.");
          return false;
      }
    /*if((catgorySelection==0 ||catgorySelection=='') && scsc_comboIDs=='')
    {
        alert("Please select at least one catgory.");
        return false;
    }*/
}

function in_array(val,ar){
	for(var i=0;i<ar.length;i++){
		if(val==ar[i]){
			return i;
		}
	}
	return -1;
}

function do_SCSC(obj,type,obj_to,asy){
       
	var tid = 0;
	for(var j=0;j<obj.options.length;j++){
		if(obj.options[j].selected){
			tid = obj.options[j].value;
			break;
		}
	}
	obj_to.selectedIndex = 0;
	obj_to.options.length = 1;
	obj_to.style.display = 'none';
	processajax('<?php echo '../admin/'; ?>scsc_info_trend.php', asy, 'POST', type+'='+tid, obj_to, 'doInnerSelect');
}

function doInnerSelect(response, obj){
	if(response.length>0){
		//obj.innerHTML = response;
		var opt = 1;
		var lines = response.split("\n");
		for(var i=0;i<lines.length;i++){
			var line = lines[i].split("\t");
			if(line.length==2){
				obj.options[opt] = new Option(line[1], line[0], false, false);
				opt = opt + 1;
			}
		}
		obj.style.display = 'block';
	}
}

function add_SCSC(){
	var scsc_names = new Array('combo_sid','combo_cid','combo_scid','combo_sscid');
	var scsc_values = new Array();
	var scsc_text = new Array();
        //var category_is_selected = false;
        var scsc_comboIDsval    =   document.getElementById('scsc_comboIDs').value;
        var scsc_comboIDsedit   =  trimspace(document.getElementsByName('scsc_combo_edit')[0].value);
        var primarysector       =  scsc_comboIDsval.split('|');
   //alert(scsc_comboIDsedit);
     //if( (trimspace(scsc_comboIDsval)==''||trimspace(primarysector[0])==scsc_comboIDsedit) && (document.forms.frm1.combo_sid.selectedIndex)){
        //add_digital_headlines();
     //} 
     /* ##############end for mobile digital headline automatic writing ################# */
	for (var j = 0; j < scsc_names.length; j++) {
		scsc_values[j] = '0';
		scsc_text[j] = '';
		var obj = document.frm1[scsc_names[j]];

		for (var k = 0; k < obj.options.length; k++) {
			if (obj.options[k].selected && obj.options[k].value.length > 0) {
				scsc_values[j] = obj.options[k].value;
				scsc_text[j] = obj.options[k].text;
				break;
			}
		}

		obj.selectedIndex = 0;

		if (scsc_names[j]!='combo_sid') {
			obj.options.length = 1;
			obj.style.display = 'none';
		}
	}

    if (scsc_values[1] != '0' || window.location.href.indexOf('addTrend') != -1) {
        //category_is_selected = true; // Skip category requirement on mass update tool
    }

	if (scsc_values[0] != '0') {
		var scsc_combo = '';
		var scsc_combo_text = '';
		for(var j=0;j<scsc_values.length;j++){
			if(scsc_combo.length>0){
				scsc_combo = scsc_combo + '_';
				scsc_combo_text = scsc_combo_text + ' / ';
			}
			scsc_combo = scsc_combo + scsc_values[j];
			scsc_combo_text = scsc_combo_text + scsc_text[j];
			getBlock(scsc_values[j],true);
		}
		var exists = 0;
		var scsc_comboIDs_val = document.frm1.scsc_comboIDs.value;
		var valArray = scsc_comboIDs_val.split('|');
		var sortorder = valArray.length;
		for(var i=0;i<sortorder;i++){
			if(valArray[i]==scsc_combo){
				exists = 1;
			}
		}
		if(!exists){
			if(scsc_comboIDs_val.length>0){
				scsc_comboIDs_val = scsc_comboIDs_val + '|';
			}
			scsc_comboIDs_val = scsc_comboIDs_val + scsc_combo;
			document.frm1.scsc_comboIDs.value = scsc_comboIDs_val;
			displaySCSC(scsc_combo,scsc_combo_text);
		}
		var scsc_combo_edit = document.frm1.scsc_combo_edit.value;
		document.frm1.scsc_combo_edit.value = '';
		add_SCSC_link_text();
		if(scsc_combo_edit!='' && scsc_combo_edit!=scsc_combo){
			var sortorder_edit = removeSCSC(scsc_combo_edit);
			var newsort = sortorder_edit - (sortorder - 1);
			if(newsort<0){
				sortSCSC(scsc_combo,newsort);
			}
		}
		else{
			afterSCSC();
		}
		//checkCompanyFields('scsc_comboIDs');
		//return true;
	}
	else{
		clearSCSC();
		alert('Sector are required');
		return false;
	}
}

function displaySCSC(scsc_combo,scsc_combo_text){
	var newobj = document.getElementById('scsc_combos');
	if(newobj){
		var newnode = document.createElement('div');
		newnode.id = 'combo'+scsc_combo;
		newnode.style.fontWeight = 'bold';
		newnode.style.marginBottom = '2px';
		newnode.appendChild(document.createTextNode(scsc_combo_text+' '));
			var newnode2 = document.createElement('a');
			newnode2.href = '#';
			
			/*newnode2.onclick = new Function("sortSCSC('"+scsc_combo+"',-1); return false;");
			newnode2.appendChild(document.createTextNode('Up'));
			newnode.appendChild(newnode2);
			newnode.appendChild(document.createTextNode(' '));
			newnode2 = document.createElement('a');
			newnode2.href = '#';
			
			newnode2.onclick = new Function("sortSCSC('"+scsc_combo+"',1); return false;");
			newnode2.appendChild(document.createTextNode('Down'));
			newnode.appendChild(newnode2);
			newnode.appendChild(document.createTextNode(' '));
			newnode2 = document.createElement('a');
			newnode2.href = '#';
			
			newnode2.onclick = new Function("editSCSC('"+scsc_combo+"'); return false;");
			newnode2.appendChild(document.createTextNode('Edit'));
			newnode.appendChild(newnode2);
			newnode.appendChild(document.createTextNode(' '));
			newnode2 = document.createElement('a');
			newnode2.href = '#';
			*/
			newnode2.onclick = new Function("removeSCSC('"+scsc_combo+"'); return false;");
			newnode2.appendChild(document.createTextNode('Remove'));
			newnode.appendChild(newnode2);
		
		newobj.appendChild(newnode);
	}
}

function clearSCSC(){
	do_SCSC(document.frm1.combo_sid,'sid',document.frm1.combo_cid,true);
	do_SCSC(document.frm1.combo_cid,'cid',document.frm1.combo_scid,true);
	do_SCSC(document.frm1.combo_scid,'scid',document.frm1.combo_sscid,true);
	document.frm1.scsc_combo_edit.value = '';
	add_SCSC_link_text();
}

function add_SCSC_link_text(){
	var lobj = document.getElementById('add_SCSC_link');
	if(lobj){
		if(document.frm1.scsc_combo_edit.value==''){
			my_innerHTML_text(lobj,'Add');
		}
		else{
			my_innerHTML_text(lobj,'Update');
		}
	}
}
function afterSCSC(){
	//dependsSector();
	//checkDeps_s();
        checkSectorWV(3);
}

function removeSCSC(scsc_combo){
	var scsc_comboIDs_val = document.frm1.scsc_comboIDs.value;
	var valArray = scsc_comboIDs_val.split('|');
	if(valArray.length==1){
		if(!confirm('This will clear all Sector/Category/Sub Category dependent fields.\nContinue?')){
			return -1;
		}
	}
	var sortorder = 0;
	var obj = document.getElementById('combo'+scsc_combo);
	if(obj){
		obj.parentNode.removeChild(obj);
		var scsc_comboIDs_val_new = '';
		for(var i=0;i<valArray.length;i++){
			if(valArray[i]!=scsc_combo){
				if(scsc_comboIDs_val_new.length>0){
					scsc_comboIDs_val_new = scsc_comboIDs_val_new + '|';
				}
				scsc_comboIDs_val_new = scsc_comboIDs_val_new + valArray[i];
			}
			else{
				sortorder = i;
				var valArray2 = valArray[i].split('_');
				getBlock(valArray2[0],false);
				getBlock(valArray2[1],false);
				getBlock(valArray2[2],false);
			}
		}
		document.frm1.scsc_comboIDs.value = scsc_comboIDs_val_new;
		var selectedSArray = returnSCSC(0);
		for(var m=0;m<selectedSArray.length;m++){
			getBlock(selectedSArray[m],true);
		}
	}
	afterSCSC();
        /* ############## for mobile digital headline automatic writing ################# */
      /*  var scsc_comboIDs_val = document.frm1.scsc_comboIDs.value;
	if(scsc_comboIDs_val!=''){
            var valArray = scsc_comboIDs_val.split('|');
            if(valArray.length==1){
                var scsc_comboIDsval    =   document.getElementById('scsc_comboIDs').value;
                editSCSC(scsc_comboIDsval);
                //add_digital_headlines();
            }
        }*/
        /* ##############end for mobile digital headline automatic writing ################# */
	return sortorder;
}

/*function sortSCSC(idval,sort){
	var newval = '';
        
        var cmp_idsval= document.getElementsByName('cmp_ids')[0].value;
         if(cmp_idsval==''){
            alert('Please add the company.');
                    return false;
         }
      
    
	var obj = document.getElementById('scsc_combos');
	var obj2 = document.frm1.scsc_comboIDs;
	var idsval = obj2.value;
	var valArray = idsval.split('|');
	for(var i=0;i<valArray.length;i++){
		if(valArray[i]==idval){
			if(valArray[i+sort]){
				var tmpidval = valArray[i];
				valArray[i] = valArray[i+sort];
				valArray[i+sort] = tmpidval;
				var movenode = obj.removeChild(obj.childNodes[i]);
				if(i+sort>=obj.childNodes.length){
					obj.appendChild(movenode);
				}
				else{
					obj.insertBefore(movenode,obj.childNodes[i+sort]);
				}
				break;
			}
		}
	}
	for(var i=0;i<valArray.length;i++){
		if(newval.length>0){
			newval = newval + '|';
		}
		newval = newval + valArray[i];
	}
	obj2.value = newval;
      // alert(newval);
	checkProductName();
        var scsc_comboIDsval = obj2.value.split('|');
        //editSCSC(scsc_comboIDsval[0]);

}*/

function checkSCSC(idval,typei){ //0,1,2,3,4
	var valArray = document.frm1.scsc_comboIDs.value.split('|');
	for(var i=0;i<valArray.length;i++){
		var valArray2 = valArray[i].split('_');
		if(typei==0){
			for(var j=0;j<valArray2.length;j++){
				if(valArray2[j]==idval){
					return i+1;
				}
			}
		}
		else{
			if(valArray2[typei-1]==idval){
				return i+1;
			}
		}
	}
	return 0;
}

function returnSCSC(typei){ //0,1,2,3,4
	var valArray = document.frm1.scsc_comboIDs.value.split('|');
	var outArray = new Array();
	for(var i=0;i<valArray.length;i++){
		var valArray2 = valArray[i].split('_');
		if(typei==0){
			for(var j=0;j<valArray2.length;j++){
				outArray[outArray.length] = valArray2[j];
			}
		}
		else{
			outArray[i] = valArray2[typei-1];
		}
	}
	return outArray;
}

function getBlock(sid,dis){
	var obj = false;
	obj = MM_findObj("div_"+sid);
	if(obj){
		if(dis){
			obj.style.display = 'block';
			document.frm1['part_'+sid].value = 1;
		}
		else{
			obj.style.display = 'none';
			document.frm1['part_'+sid].value = 0;
		}
	}
}

function depends(fields,eval_bool){
	var fieldsArray = fields.split(',');
	for(var field in fieldsArray){
		var obj = document.frm1[fieldsArray[field]];
		if(obj){
			obj.disabled = eval(eval_bool);
		}
	}
}
function dependsSector(){
	var selectedArray = returnSCSC(2);
	var selectedSubCatArray = returnSCSC(3);
       // alert(selectedArray); 
	//alert(selectedSubCatArray); 
	var eval_bool = '';
	if(in_array(88,selectedSubCatArray)!=-1){
		eval_bool = 'false';
	}
	else{
		eval_bool = 'true';
	}
       
	depends('FreeChecking,Checking_APR,Checking_APY',eval_bool);
	
	if(in_array(89,selectedSubCatArray)!=-1){
		eval_bool = 'false';
	}
	else{
		eval_bool = 'true';
	}
	depends('Savings_APR,Savings_APY',eval_bool);//SavingsInterestRate
	
	if(in_array(100,selectedSubCatArray)!=-1){
		eval_bool = 'false';
	}
	else{
		eval_bool = 'true';
	}
	depends('MoneyMarket_APR,MoneyMarket_APY',eval_bool);
	
	if(in_array(189,selectedSubCatArray)!=-1){
		eval_bool = 'false';
	}
	else{
		eval_bool = 'true';
	}
	depends('CD_APR,CD_APY',eval_bool);
	
	if(in_array(186,selectedArray)!=-1 || in_array(94,selectedArray)!=-1 || in_array(187,selectedArray)!=-1 || in_array(185,selectedArray)!=-1){
		eval_bool = 'false';
	}
	else{
		eval_bool = 'true';
	}
	depends('InstallationCharge',eval_bool);
	
	if(in_array(94,selectedArray)!=-1){
		eval_bool = 'false';
	}
	else{
		eval_bool = 'true';
	}
	depends('LocalCallingMonthlyCost,LongDistanceMonthlyCost',eval_bool);
	
	if(in_array(103,selectedSubCatArray)!=-1){
		eval_bool = 'false';
	}
	else{
		eval_bool = 'true';
	}
	depends('Reloadable',eval_bool);
} 
function checkDeps_s()
{ 
    return true;
 } 
 
 var sectorWV = new Array();
<?php
	$sql = "SELECT sectorID FROM cscan_sector WHERE sectorWorksiteVoluntary=1";
	$rs = $DRW->query($sql,$DRW_read);
	while($row = $DRW->fetch_array($rs)) {
		echo "sectorWV[sectorWV.length] = '".$row[0]."';\n";
	}
?>
function checkSectorWV(fieldname){
	var selectedSubCatArray = returnSCSC(fieldname);
	for(var j=0;j<selectedSubCatArray.length;j++){
		if(in_array(selectedSubCatArray[j],sectorWV)!=-1){
			if(!document.frm1.worksiteVoluntary.checked && confirm('Select Worksite/Voluntary?')){
				document.frm1.worksiteVoluntary.checked = true;
			}
		}
	}
}
function validate()
{
    var trendname=document.frm1.trendname.value=trimspace(document.frm1.trendname.value);
    var trend_document=document.forms["frm1"]["trend_document"].value;
    var trend_document_hidden=document.forms["frm1"]["trend_document_hidden"].value;
    var audienceSelection=document.forms["frm1"]["audience_id[]"].value;
    var sectorSelection=document.forms["frm1"]["combo_sid"].value;
    var catgorySelection=document.forms["frm1"]["combo_cid"].value;
    var scsc_comboIDs=document.forms["frm1"]["scsc_comboIDs"].value;
    //rteurn true;
  if(trendname == '')
    {
            alert('Please enter a trend name.');
            document.frm1.trendname.focus();
            return false;
    }
   
   if(trend_document== '' && trend_document_hidden=='')
    {
            alert('Please upload document.');
            document.frm1.trend_document.focus();
            return false;
    }
   
   if(audienceSelection==0 ||audienceSelection=='')
      {
          alert("Please select at least one audience.");
          return false;
      }
    if(((sectorSelection==0 || sectorSelection=='') && scsc_comboIDs=='') || scsc_comboIDs=='')
      {
          alert("Please select at least one sector.");
         
          return false;
      }
       //add_SCSC();
    /*if((catgorySelection==0 ||catgorySelection=='') && scsc_comboIDs=='')
    {
        alert("Please select at least one catgory.");
        return false;
    }*/
}
</script> 
<?php include 'bottom.php'; ?>