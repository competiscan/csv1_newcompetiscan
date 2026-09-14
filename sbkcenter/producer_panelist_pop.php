<?php
error_reporting(0);
ob_start();

require_once('sbkc_def.php');
$dbh = mysql_connect($conn_hostname, $conn_username, $conn_password) or die ("Unable to connect to MySQL");
$selected = mysql_select_db($conn_database,$dbh) or die ("Could not select database");

$stateIDArray = array();
$countries = array('US');
$sqlc = "SELECT DISTINCT countryCode FROM cscan_state JOIN ISO31661_alpha2code ON (code=countryCode) WHERE countryCode<>'US' ORDER BY country";
$rsc = mysql_query($sqlc);
while($rowc = mysql_fetch_row($rsc)) {
	$countries[] = $rowc[0];
}
foreach($countries as $country){
	$sql = "select stateCode,panelist_stateID from cscan_state WHERE countryCode='".$country."' ORDER BY stateName";
	$result = mysql_query($sql);
	while($row = mysql_fetch_row($result)){
		$stateIDArray[$row[0]] = $row[1];
	}
}
$healthArray = array(
	'Group Health',
	'Individual Health',
	'Group and Individual',
	'Neither',
);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Welcome To Small Business Knowledge Center</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet" type="text/css" />
<link href="consumerform.css" rel="stylesheet" type="text/css">
<script type="text/javascript">
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-33612694-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
</script>
<script src="jquery.min.js" type="text/javascript"></script>
<script type="text/javascript">
var pages = 4;
var page_required = new Array();
page_required[2] = new Array('first_name','last_name','email','address','city','state','zip','telephone');
page_required[3] = new Array('carriers','sell_pc','sell_health','sell_life','sell_annuities','series7','sell_medicare','sell_401k','sell_worksite');
var field_validation = new Array();
field_validation[2] = new Array();
field_validation[2]['email'] = /^[A-Za-z0-9._%-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}$/;
field_validation[2]['telephone'] = /^[0-9().-]+$/;

$(document).ready(function() {
	$( "#page" ).on( "change", function() {
		var page = $( "#page" ).val();
		for(var p=1;p<=pages;p++){
			if(p==page){
				$( "#page"+p ).show();
			}
			else{
				$( "#page"+p ).hide();
			}
		}
	});
	$( "#page" ).trigger( "change" );
	$( "input[name='sell_worksite']" ).on( "click", function() {
		if($( "#sell_worksite_Yes" ).prop("checked")){
			$( "#worksite_carriers_div" ).show();
		}
		else{
			$( "#worksite_carriers_div" ).hide();
		}
	});
	$( "#page_form" ).on( "submit", function() {
		var page = $( "#page" ).val();
		$( "#next"+page ).hide();
		$( "#errors" ).hide();
		page = parseInt(page);
		var error_field = false;
		if(field_validation[page]){
			for (var f in field_validation[page]){
				var regx = field_validation[page][f];
				var form_field = $( "#"+f );
				var form_val = form_field.val();
				if(!regx.test(form_val)){
					error_field = form_field;
					break;
				}
			}
		}
		if(page_required[page]){
			for(var i=0;i<page_required[page].length;i++){
				var form_field = $( "input[name='"+page_required[page][i]+"']" );
				if(!form_field || form_field.length==0){
					form_field = $( "#"+page_required[page][i] );
				}
				var form_val = form_field.val();
				var field_type = form_field.attr('type');
				var selectedIndex = form_field.prop('selectedIndex');
				var is_error = false;
				if(field_type=='text'){
					if(!form_val){
						is_error = true;
					}
				}
				else if(field_type=='radio'){
					is_error = true;
					form_field.each(function( index ) {
						if($( this ).prop('checked')){
							is_error = false;
							return;
						}
					});
				}
				else if(selectedIndex!=undefined){
					//var mult = form_field.prop('multiple');
					if(selectedIndex<1){
						is_error = true;
					}
				}
				if(is_error){
					error_field = form_field;
					break;
				}
			}
		}
		if(error_field!==false){
			$( "#errors" ).html('We are sorry, but there appears to be a problem with the form you submitted.');
			$( "#errors" ).show();
			error_field.focus();
			$( "#next"+page ).show();
			return false;
		}
		page = page + 1;
		if(page==pages){
			var form_vals = $("#page_form").serialize();
			$.ajax({
				type: "POST",
				dataType: "text",
				url: "send_form_producer_new.php",
				data: form_vals,
				beforeSend: function(jqXHR,settings){
					//show wait
				},
				complete: function(jqXHR,textStatus){
					//end wait
				},
				success: function(data,textStatus,jqXHR) {
					$( "#page" ).val(page);
					$( "#page" ).trigger( "change" );
					//$( "#page"+page ).append('<img src="http://forwardrocketlaunch.com/p.ashx?o=3347&f=img&t=TRANSACTION_ID" width="1" height="1" border="0" />');
					var pixel  = new Image(1,1);
					pixel.style.border = 'none';
					pixel.src = 'https://forwardrocketlaunch.com/p.ashx?o=3347&f=img&t=TRANSACTION_ID';
					document.body.appendChild(pixel);
				}
			});
		}
		else{
			$( "#page" ).val(page);
			$( "#page" ).trigger( "change" );
		}
		return false;
	});
});
</script>
<style type="text/css">
.main-table {
	background-color: #FFFFFF;
}
</style>
</head>
<body>
<table width="533" border="0" align="center" cellpadding="0" cellspacing="0" class="main-table">
	<tr> 
		<td class="border1">&nbsp;</td>
	</tr>
	<tr> 
		<td class="border">
			<table width="498" border="0" align="center" cellpadding="0" cellspacing="0">
				<tr>
					<td height="23" class="sbkbar">
						<p>&nbsp;</p>
						<center><img src="images/sbkclogo.gif" width="250" height="61"></center></td>
				</tr>
				<tr>
					<td valign="bottom" bgcolor="#FFFFFF"><br> 
						<table width="469" border="0" align="center" cellpadding="0" cellspacing="0">
							<tr> 
								<td valign="top" align="center">
									<h2><strong>Get Compensated For Your<br />Direct Mail &amp; Email!</strong></h2>
								</td>
							</tr>
							<tr>
								<td>
									<p>&nbsp;</p>
									<table width="100%" border="0" cellspacing="0" cellpadding="0">
										<tr valign="top"> 
											<td>
												<form name="page_form" id="page_form" method="post" action="send_form_producer_new.php">
												<div id="errors" style="display:none;text-align:center;" class="mandatory"></div>
												<div id="page1">
													<table width="100%" border="0" cellpadding="0" cellspacing="10" class="formtable">
														<tr>
															<td>
																<p align="justify">Thank you for your interest in our direct marketing study for insurance agents and financial advisors. </p>
<p align="justify"><strong><u>How it Works</u></strong>&nbsp;</p>
																<p align="justify">Each month we ask that you send us the email and/or direct mail you receive as an insurance agent or financial advisor and in return we will reward you with Visa Prepaid Cards. You will receive<strong> $15</strong> for email and <strong>$15</strong> for direct mail each month you participate - that means $30 altogether! Additionally, based on your confidential panelist profile we will  send you monthly bonus and survey opportunities for even more  rewards.&nbsp;</p>
																<p align="justify"><u><strong>Materials of Interest</strong></u></p>
																<p align="justify">We are looking for materials you would receive from insurance carriers or financial companies. Materials from independent marketing organizations or lead generators would not count towards your participation. Below are some examples of the materials we are  interested in.																</p>
																<div align="center">
																  <table border="0" cellspacing="5" cellpadding="0" width="300">
																    <tbody>
																      <tr>
																        <td><p><strong>Newsletters</strong></p></td>
																        <td><p><strong>Contests/Incentives</strong></p></td>
															          </tr>
																      <tr>
																        <td><p><strong>Postcards</strong></p></td>
																        <td><p><strong>Sales Kits</strong></p></td>
															          </tr>
																      <tr>
																        <td><p><strong>Announcements</strong></p></td>
																        <td><p><strong>CD-ROMs</strong></p></td>
															          </tr>
																      <tr>
																        <td><p><strong>Bulletins</strong></p></td>
																        <td><p><strong>Rates</strong></p></td>
															          </tr>
																      <tr>
																        <td><p><strong>Agent Guides</strong></p></td>
																        <td><p><strong>Presentations</strong></p></td>
															          </tr>
																      <tr>
																        <td><p><strong>Recruitment Letters</strong></p></td>
																        <td><p><strong>Commissions</strong></p></td>
															          </tr>
																      <tr>
																        <td><p><strong>Underwriting Guides</strong></p></td>
																        <td><p><strong>Summary of Benefits</strong></p></td>
															          </tr>
																      <tr>
																        <td><p><strong>Webinars/Seminars</strong></p></td>
																        <td><p><strong>Brochures</strong></p></td>
															          </tr>
															        </tbody>
															      </table>
															  </div>
															  <p><strong><u>We Respect Your Privacy:</u></strong></p>
																<ul type="disc">
																<li>All personal information is deleted from materials prior to use in our study.</li>
																<li>We shred and recycle everything we receive and your privacy is 100% protected.</li>
																<li>Participation is free, and there is no commitment - you can opt out at any time.</li>
																<li>No one will sell you anything.</li>
																<li>We will not share your email address or any personal information with anyone.</li>
															  </ul>
														  </td>
														</tr>
														<tr>
															<td>By filling out your confidential Panelist Profile, you are agreeing to participate in our market research study. Please click "Next" to create your Profile.															  <input type="submit" name="next1" id="next1" value="Next"></td>
														</tr>
													</table>
												</div>
												<div id="page2" style="display:none;">
													<table width="100%" border="0" cellpadding="0" cellspacing="10" class="formtable">
														<tr>
															<td width="30%" valign="top"><strong>First Name</strong><span class="mandatory">*</span></td>
															<td width="70%"><input name="first_name" id="first_name" type="text" class="input" size="40" value=""></td>
														</tr>
														<tr>
															<td width="30%" valign="top"><strong>Last Name</strong><span class="mandatory">*</span></td>
															<td width="70%"><input name="last_name" id="last_name" type="text" class="input" size="40" value=""></td>
														</tr>
														<tr>
															<td width="30%" valign="top"><strong>Email Address</strong><span class="mandatory">*</span></td>
															<td width="70%"><input name="email" id="email" type="text" class="input" size="40" value=""></td>
														</tr>
														<tr>
															<td width="30%" valign="top"><strong>Company</strong></td>
															<td width="70%"><input name="company" id="company" type="text" class="input" size="40" value=""></td>
														</tr>
														<tr>
															<td width="30%" valign="top"><strong>Street Address</strong><span class="mandatory">*</span></td>
															<td width="70%"><input name="address" id="address" type="text" class="input" size="40" value=""></td>
														</tr>
														<tr>
															<td width="30%" valign="top"><strong>City</strong><span class="mandatory">*</span></td>
															<td width="70%"><input name="city" id="city" type="text" class="input" size="40" value=""></td>
														</tr>
														<tr>
															<td width="30%" valign="top"><strong>State/Province</strong><span class="mandatory">*</span></td>
															<td width="70%"><select name="state" id="state"><option value="">&nbsp;</option><?php 
															foreach($stateIDArray as $state=>$ID){
																echo '<option value="'.htmlspecialchars($state,ENT_QUOTES).'">'.htmlspecialchars($state).'</option>';
															}
															?></select></td>
														</tr>
														<tr>
															<td width="30%" valign="top"><strong>Zip/Postal Code</strong><span class="mandatory">*</span></td>
															<td width="70%"><input name="zip" id="zip" type="text" class="input" size="40" value=""></td>
														</tr>
														<tr>
															<td width="30%" valign="top"><strong>Telephone Number</strong><span class="mandatory">*</span></td>
															<td width="70%"><input name="telephone" id="telephone" type="text" class="input" size="40" value=""></td>
														</tr>
														<tr>
															<td width="30%" valign="top"><strong>Referred by</strong></td>
															<td width="70%"><input name="referral" id="referral" type="text" class="input" size="40" value=""></td>
														</tr>
														<tr>
															<td>&nbsp;</td><td><input type="submit" name="next2" id="next2" value="Next"></td>
														</tr>
													</table>
												</div>
												<div id="page3" style="display:none;">
													<table width="100%" border="0" cellpadding="0" cellspacing="10" class="formtable">
														<tr>
															<td width="1%" valign="top"><strong>1.)</strong></td>
															<td valign="top"><strong>Please name 3 Major Carriers you write with:</strong><br />
															<input name="carriers" id="carriers" type="text" class="input" size="40" value=""></td>
														</tr>
														<tr>
															<td width="1%" valign="top"><strong>2.)</strong></td>
															<td valign="top"><strong>Do you sell Property and Casualty insurance?</strong><br />
															<label><input type="radio" name="sell_pc" id="sell_pc_Yes" value="Yes">Yes</label>&nbsp;<label><input type="radio" name="sell_pc" id="sell_pc_No" value="No">No</label></td>
														</tr>
														<tr>
															<td width="1%" valign="top"><strong>3.)</strong></td>
															<td valign="top"><strong>Do you sell Health insurance and if so what size:</strong><br />
															<select name="sell_health" id="sell_health"><option value="">&nbsp;</option><?php 
															foreach($healthArray as $hname){
																echo '<option value="'.htmlspecialchars($hname,ENT_QUOTES).'">'.htmlspecialchars($hname).'</option>';
															}
															?></select></td>
														</tr>
														<tr>
															<td width="1%" valign="top"><strong>4.)</strong></td>
															<td valign="top"><strong>Do you sell Life Insurance?</strong><br />
															<label><input type="radio" name="sell_life" id="sell_life_Yes" value="Yes">Yes</label>&nbsp;<label><input type="radio" name="sell_life" id="sell_life_No" value="No">No</label></td>
														</tr>
														<tr>
															<td width="1%" valign="top"><strong>5.)</strong></td>
															<td valign="top"><strong>Do you sell Annuities?</strong><br />
															<label><input type="radio" name="sell_annuities" id="sell_annuities_Yes" value="Yes">Yes</label>&nbsp;<label><input type="radio" name="sell_annuities" id="sell_annuities_No" value="No">No</label></td>
														</tr>
														<tr>
															<td width="1%" valign="top"><strong>6.)</strong></td>
															<td valign="top"><strong>Are there any other states/provinces you write business with other than the state you reside in?</strong><br />
															<select name="business_state[]" id="business_state" size="4" multiple="multiple"><?php 
															foreach($stateIDArray as $state=>$ID){
																echo '<option value="'.htmlspecialchars($state,ENT_QUOTES).'">'.htmlspecialchars($state).'</option>';
															}
															?></select> <em>Hold ctrl key for multiple selection</em></td>
														</tr>
														<tr>
															<td width="1%" valign="top"><strong>7.)</strong></td>
															<td valign="top"><strong>Do you hold a Series 7 license or are you a Licensed Financial Professional?</strong><br />
															<label><input type="radio" name="series7" id="series7_Yes" value="Yes">Yes</label>&nbsp;<label><input type="radio" name="series7" id="series7_No" value="No">No</label></td>
														</tr>
														<tr>
															<td width="1%" valign="top"><strong>8.)</strong></td>
															<td valign="top"><strong>Do you sell Medicare products? (SELECT NO IF LOCATED IN CANADA)</strong><br />
															<label><input type="radio" name="sell_medicare" id="sell_medicare_Yes" value="Yes">Yes</label>&nbsp;<label><input type="radio" name="sell_medicare" id="sell_medicare_No" value="No">No</label></td>
														</tr>
														<tr>
															<td width="1%" valign="top"><strong>9.)</strong></td>
															<td valign="top"><strong>Do you sell Retirement Plans?</strong><br />
															<label><input type="radio" name="sell_401k" id="sell_401k_Yes" value="Yes">Yes</label>&nbsp;<label><input type="radio" name="sell_401k" id="sell_401k_No" value="No">No</label></td>
														</tr>
														<tr>
															<td width="1%" valign="top"><strong>10.)</strong></td>
															<td valign="top"><strong>Do you sell Worksite Products?</strong><br />
															<label><input type="radio" name="sell_worksite" id="sell_worksite_Yes" value="Yes">Yes</label>&nbsp;<label><input type="radio" name="sell_worksite" id="sell_worksite_No" value="No">No</label>
															<div style="margin-top:6px;display:none;" id="worksite_carriers_div"><strong>Which carriers?</strong><br><input name="worksite_carriers" id="worksite_carriers" type="text" class="input" size="40" value=""></div>
															</td>
														</tr>
														<tr>
															<td>&nbsp;</td><td><input type="submit" name="next3" id="next3" value="Next"></td>
														</tr>
													</table>
												</div>
												<div id="page4" style="display:none;">
													<table width="100%" border="0" cellpadding="0" cellspacing="10" class="formtable">
														<tr>
															<td>
																<p>Thank you for your interest in the SBKC producer panel. We will email you instructions on how to participate in the panel within the next 24 hours. If you have any questions in the meantime feel free to reach out to us at 773-227-7454 or email us at <a href="mailto:producers@sbkcenter.com">Producers@sbkcenter.com</a>.</p>
																<p>Thank you and we look forward to working with you!</p>
																<p>
																Jessica Eccles<br>
																Project Manager<br>
																Small Business Knowledge Center
																</p>
															</td>
														</tr>
													</table>
												</div>
												<input type="hidden" name="page" id="page" value="1"></form>
											</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
						<p>&nbsp;</p>
					</td>
				</tr>
				<tr> 
					<td height="23" class="sbkbar">
						<center>
						<p><a target="_blank" id="bbblink" class="sehzbus" href="https://www.bbb.org/chicago/business-reviews/marketing-programs-and-services/small-business-knowledge-center-in-chicago-il-88346842#bbblogo" title="Small Business Knowledge Center, Marketing Programs &amp; Services, Chicago, IL" style="display: block;position: relative;overflow: hidden; width: 100px; height: 38px; margin: 0px; padding: 0px;"><img style="padding: 0px; border: none;" id="bbblinkimg" src="https://seal-chicago.bbb.org/logo/sehzbus/small-business-knowledge-center-88346842.png" width="200" height="38" alt="Small Business Knowledge Center, Marketing Programs &amp; Services, Chicago, IL" /></a><br
                                                 <p>
                                                    <a href="SBKC_Terms_and_Conditions.pdf" target="_blank">Terms & Conditions</a>
                                                </p> 
						All rights reserved 2006 - <?php echo date("Y"); ?> Small Business Knowledge Center<br />
						<a href="https://www.sbkcenter.com/">www.sbkcenter.com</a></p>
						</center>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td class="border2">&nbsp;</td>
	</tr>
</table>
</body>
</html>