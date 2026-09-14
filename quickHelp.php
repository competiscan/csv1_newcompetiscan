<?php
$PAGE_HEADING = "Quick Help";
$TITLE = "Competiscan $PAGE_HEADING";
//$HEAD = '<script type="text/javascript" src="video/flowplayer/flowplayer-3.1.4.min.js"></script><link rel="stylesheet" type="text/css" href="video/flowplayer/style.css" />';
$HEAD = '<script type="text/javascript" src="video/flowplayer/flowplayer-3.1.4.min.js"></script>';
?>
<style>
   #page {
	background-color:#f9f9f9;
	width:100%;
	margin:20px 0;
	padding:20px;
	min-height:400px;
	border:2px solid #fff;
	outline:1px solid #ccc;
	text-align:left;
}
</style>
<?php
include 'header_top.php';
if(!defined('ENV')){
    define('ENV',getenv('SERVER_NAME'));
}
?>

<table width="100%" border="0" cellpadding="5" cellspacing="0" class="bodytext">
	<?php 
	if(isset($_SESSION['sess_username']) && $_SESSION['sess_username']!='') {
		if(isset($_REQUEST['vid'])){
			$vid = (int) $_REQUEST['vid'];
		}
		else{
			$vid = 0;
		}
		switch($vid){
			case 1: //http://www.competiscan.com/video/welcome-competiscan.html
				?>
				<tr><td class="bodytext">
				<div style="float:left;" class="topMenuLink">Competiscan Introductory Video</div>
				<div style="float:right;" class="topMenuLink"><a href="quickHelp.php">&lt;&lt; Back to Quick Help</a></div>
				<p class="topMenuLink">&nbsp;</p>
				<div id="page" style="text-align: center;">
                                <video  controls controlsList="nodownload" >
                                    <source src="<?= $displays3URL.'video/high.mp4'; ?>" type="video/mp4">
                                    <!-- <source src="high.flv" type="video/ogg"> -->
                                  Your browser does not support the video tag.
                                </video>    
				<?php /* ################################ Start S3 Implementation Code #################################### ?>
				<!--a href="video/high.flv"  style="display:block;width:100%px;height:400px"  id="player"></a--> 
				<a href="<?= $displays3URL.'video/high.flv'; ?>"  style="display:block;width:100%px;height:400px"  id="player"> 
				</a> 
				<?php ################################ End S3 Implementation Code #################################### ?>
				</div>
				<script type="text/javascript">
					flowplayer("player", "video/flowplayer/flowplayer-3.1.5.swf");
					flowplayer("player", "video/flowplayer/flowplayer-3.1.5.swf",{
						clip: {
							 autoPlay: true,
							 autoBuffer:true 
						 },
					   plugins: { controls :null }														
					}); 
				</script> <?php */ ?>
				</td></tr>
				<?php
				break;
			case 2: //http://www.competiscan.com/new/Competiscan_Tutorial_Revised.html
				?>
				<tr><td class="bodytext">
				<div style="float:left;" class="topMenuLink">Updated 10 Minute Tutorial on Using Competiscan</div>
				<div style="float:right;" class="topMenuLink"><a href="quickHelp.php">&lt;&lt; Back to Quick Help</a></div>
				<p class="topMenuLink">&nbsp;</p>
				<div style="border:1px solid #999999; background:#e4e4e4;padding:5px;width:905px;">
				<object classid="CLSID:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,22,0" id="IncrediFlash" width="900" height="675">
				<?php ################################ Start S3 Implementation Code #################################### ?>
				<!--param name="movie" value="video/Competiscan_Tutorial_6_10.swf" /-->
				<param name="movie" value="<?= $displays3URL.'video/Competiscan_Tutorial_6_10.swf'; ?>" />
				<?php ################################ End S3 Implementation Code #################################### ?>
				<param name="bgcolor" value="#000000" />
				<?php ################################ Start S3 Implementation Code #################################### ?>
				<!--embed name="IncrediFlash" src="video/Competiscan_Tutorial_6_10.swf" bgcolor="#000000" width="900" height="675" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer"></embed-->
				<embed name="IncrediFlash" src="<?= $displays3URL.'video/Competiscan_Tutorial_6_10.swf'; ?>" bgcolor="#000000" width="900" height="675" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer"></embed>
				<?php ################################ End S3 Implementation Code #################################### ?>
				</object>
				</div>
				</td></tr>
				<?php
				break;
			case 3: //http://www.competiscan.com/new/EMail_Alert.html
				?>
				<tr><td class="bodytext">
				<div style="float:left;" class="topMenuLink">Quick Tips: Saved Searches and Email Alerts</div>
				<div style="float:right;" class="topMenuLink"><a href="quickHelp.php">&lt;&lt; Back to Quick Help</a></div>
				<p class="topMenuLink">&nbsp;</p>
				<div style="border:1px solid #999999; background:#e4e4e4;padding:5px;width:905px;">
				<object classid="CLSID:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,22,0" ID="FlashDemoBuilder" width="900" height="580">
				<?php ################################ Start S3 Implementation Code #################################### ?>
				<!--param name="movie" value="video/EMail_Alert.swf?317338960" /-->
				<param name="movie" value="<?= $displays3URL.'video/EMail_Alert.swf?317338960'; ?>" />
				<?php ################################ End S3 Implementation Code #################################### ?>
				<param name="bgcolor" value="#000000" />
				<?php ################################ Start S3 Implementation Code #################################### ?>
				<!--embed name="FlashDemoBuilder" src="video/EMail_Alert.swf?317338960" bgcolor="#000000" width="900" height="580" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer"></embed-->
				<embed name="FlashDemoBuilder" src="<?= $displays3URL.'video/EMail_Alert.swf?317338960'; ?>" bgcolor="#000000" width="900" height="580" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer"></embed>
				<?php ################################ End S3 Implementation Code #################################### ?>
				</object></div>
				</td></tr>
				<?php
				break;
			case 4: //http://www.competiscan.com/new/Life_and_Annuities.html
				?>
				<tr><td class="bodytext">
				<div style="float:left;" class="topMenuLink">Quick Tips: Life Insurance and Annuities</div>
				<div style="float:right;" class="topMenuLink"><a href="quickHelp.php">&lt;&lt; Back to Quick Help</a></div>
				<p class="topMenuLink">&nbsp;</p>
				<div style="border:1px solid #999999; background:#e4e4e4;padding:5px;width:905px;">
				<object classid="CLSID:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,22,0" ID="FlashDemoBuilder" width="900" height="580">
				<?php ################################ Start S3 Implementation Code #################################### ?>
				<!--param name="movie" value="video/Life_and_Annuities.swf?317338960" /-->
				<param name="movie" value="<?= $displays3URL.'video/Life_and_Annuities.swf?317338960'; ?>" />
				<?php ################################ End S3 Implementation Code #################################### ?>
				<param name="bgcolor" value="#000000" />
				<?php ################################ Start S3 Implementation Code #################################### ?>
				<!--embed name="FlashDemoBuilder" src="video/Life_and_Annuities.swf?317338960" bgcolor="#000000" width="900" height="580" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer"></embed-->
				<embed name="FlashDemoBuilder" src="<?= $displays3URL.'video/Life_and_Annuities.swf?317338960'; ?>" bgcolor="#000000" width="900" height="580" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer"></embed>
				<?php ################################ End S3 Implementation Code #################################### ?>
				</object></div>
				</td></tr>
				<?php
				break;
			case 5: //http://www.competiscan.com/new/Health_Insurance.html
				?>
				<tr><td class="bodytext">
				<div style="float:left;" class="topMenuLink">Quick Tips: Health Insurance Carriers</div>
				<div style="float:right;" class="topMenuLink"><a href="quickHelp.php">&lt;&lt; Back to Quick Help</a></div>
				<p class="topMenuLink">&nbsp;</p>
				<div style="border:1px solid #999999; background:#e4e4e4;padding:5px;width:645px;">
				<object classid="CLSID:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,22,0" ID="FlashDemoBuilder" width="640" height="510">
				<?php ################################ Start S3 Implementation Code #################################### ?>
				<!--param name="movie" value="video/Health_Insurance.swf?317338960" /-->
				<param name="movie" value="<?= $displays3URL.'video/Health_Insurance.swf?317338960'; ?>" />
				<?php ################################ End S3 Implementation Code #################################### ?>
				<param name="bgcolor" value="#000000" />
				<?php ################################ Start S3 Implementation Code #################################### ?>
				<!--embed name="FlashDemoBuilder" src="video/Health_Insurance.swf?317338960" bgcolor="#000000" width="640" height="510" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer"></embed-->
				<embed name="FlashDemoBuilder" src="<?= $displays3URL.'video/Health_Insurance.swf?317338960'; ?>" bgcolor="#000000" width="640" height="510" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer"></embed>
				<?php ################################ End S3 Implementation Code #################################### ?>
				</object></div>
				</td></tr>
				<?php
				break;
                        
                        case 6: 
				?>
				<tr><td class="bodytext">
				<div style="float:left;" class="topMenuLink">Saved Searches Email Alerts (01.28)</div>
				<div style="float:right;" class="topMenuLink"><a href="quickHelp.php">&lt;&lt; Back to Quick Help</a></div>
				<p class="topMenuLink">&nbsp;</p>
				<div id="page23">
                                    <video width="100%" height="400" controls>
                                    <?php ################### Start S3 Implementation Code ######################### ?>
                                    <!--source src="https://www.competiscan.com/fileuploads/59588Saved_Searches-Email_Alerts.mp4" type="video/mp4"-->
                                    <source src="<?= $displays3URL.'fileuploads/59588Saved_Searches-Email_Alerts.mp4'; ?>" type="video/mp4">
                                    <?php ################### End S3 Implementation Code ######################### ?>
                                    Your browser does not support HTML5 video.
                                  </video>        
				</div>
				
				</td></tr>
				<?php
				break;    
                        case 7: 
				?>
				<tr><td class="bodytext">
				<div style="float:left;" class="topMenuLink">Export Baskets (01.58)</div>
				<div style="float:right;" class="topMenuLink"><a href="quickHelp.php">&lt;&lt; Back to Quick Help</a></div>
				<p class="topMenuLink">&nbsp;</p>
				<div id="page23">
                                    <video width="100%" height="400" controls>
                                    <?php ################### Start S3 Implementation Code ######################### ?>
                                    <!--source src="https://www.competiscan.com/fileuploads/61756Export_Baskets.mp4" type="video/mp4"-->
                                    <source src="<?= $displays3URL.'fileuploads/61756Export_Baskets.mp4'; ?>" type="video/mp4">
                                    <?php ################### End S3 Implementation Code ######################### ?>
                                    Your browser does not support HTML5 video.
                                  </video>        
				</div>
				
				</td></tr>
				<?php
				break; 
                        case 8: 
				?>
				<tr><td class="bodytext">
				<div style="float:left;" class="topMenuLink">Mailbox Study (01:23)</div>
				<div style="float:right;" class="topMenuLink"><a href="quickHelp.php">&lt;&lt; Back to Quick Help</a></div>
				<p class="topMenuLink">&nbsp;</p>
				<div id="page23">
                                    <video width="100%" height="400" controls>
                                    <?php ################### Start S3 Implementation Code ######################### ?>
                                    <!--source src="https://www.competiscan.com/fileuploads/48854Mailbox_Study.mp4" type="video/mp4"-->
                                    <source src="<?= $displays3URL.'fileuploads/48854Mailbox_Study.mp4'; ?>" type="video/mp4">
                                    <?php ################### End S3 Implementation Code ######################### ?>
                                    Your browser does not support HTML5 video.
                                  </video>        
				</div>
				
				</td></tr>
				<?php
				break; 
                        case 9: 
				?>
				<tr><td class="bodytext">
				<div style="float:left;" class="topMenuLink">Enhanced Search (02:45)</div>
				<div style="float:right;" class="topMenuLink"><a href="quickHelp.php">&lt;&lt; Back to Quick Help</a></div>
				<p class="topMenuLink">&nbsp;</p>
				<div id="page23">
                                    <video width="100%" height="400" controls>
                                    <?php ################### Start S3 Implementation Code ######################### ?>
                                    <!--source src="https://www.competiscan.com/fileuploads/60915Enhanced_Search.mp4" type="video/mp4"-->
                                    <source src="<?= $displays3URL.'fileuploads/60915Enhanced_Search.mp4'; ?>" type="video/mp4">
                                    <?php ################### End S3 Implementation Code ######################### ?>
                                    Your browser does not support HTML5 video.
                                  </video>        
				</div>
				
				</td></tr>
				<?php
				break; 
                        case 10: 
				?>
				<tr><td class="bodytext">
				<div style="float:left;" class="topMenuLink">Retrieval Services (0:22)</div>
				<div style="float:right;" class="topMenuLink"><a href="quickHelp.php">&lt;&lt; Back to Quick Help</a></div>
				<p class="topMenuLink">&nbsp;</p>
				<div id="page23">
                                    <video width="100%" height="400" controls>
                                    <?php ################### Start S3 Implementation Code ######################### ?>
                                    <!--source src="https://www.competiscan.com/fileuploads/11963Retrieval_Services.mp4" type="video/mp4"-->
                                    <source src="<?= $displays3URL.'fileuploads/11963Retrieval_Services.mp4'; ?>" type="video/mp4">
                                    <?php ################### End S3 Implementation Code ######################### ?>
                                    Your browser does not support HTML5 video.
                                  </video>        
				</div>
				
				</td></tr>
				<?php
				break; 
                        case 11: 
				?>
				<tr><td class="bodytext">
				<div style="float:left;" class="topMenuLink">Trend Reports (0:37)</div>
				<div style="float:right;" class="topMenuLink"><a href="quickHelp.php">&lt;&lt; Back to Quick Help</a></div>
				<p class="topMenuLink">&nbsp;</p>
				<div id="page23">
                                    <video width="100%" height="400" controls>
                                    <?php ################### Start S3 Implementation Code ######################### ?>
                                    <!--source src="https://www.competiscan.com/fileuploads/38973Trend_Reports.mp4" type="video/mp4"-->
                                    <source src="<?= $displays3URL.'fileuploads/38973Trend_Reports.mp4'; ?>" type="video/mp4">
                                    <?php ################### End S3 Implementation Code ######################### ?>
                                    Your browser does not support HTML5 video.
                                  </video>        
				</div>
				
				</td></tr>
				<?php
				break; 
                        case 12: 
				?>
				<tr><td class="bodytext">
				<div style="float:left;" class="topMenuLink">OCR-Full Text (0:52)</div>
				<div style="float:right;" class="topMenuLink"><a href="quickHelp.php">&lt;&lt; Back to Quick Help</a></div>
				<p class="topMenuLink">&nbsp;</p>
				<div id="page23">
                                    <video width="100%" height="400" controls>
                                    <?php ################### Start S3 Implementation Code ######################### ?>
                                    <!--source src="https://www.competiscan.com/fileuploads/26274OCR-Full_Text.mp4" type="video/mp4"-->
                                    <source src="<?= $displays3URL.'fileuploads/26274OCR-Full_Text.mp4'; ?>" type="video/mp4">
                                    <?php ################### End S3 Implementation Code ######################### ?>
                                    Your browser does not support HTML5 video.
                                  </video>        
				</div>
				
				</td></tr>
				<?php
				break; 
                        case 13: 
				?>
				<tr><td class="bodytext">
				<div style="float:left;" class="topMenuLink">My Excel (0:32)</div>
				<div style="float:right;" class="topMenuLink"><a href="quickHelp.php">&lt;&lt; Back to Quick Help</a></div>
				<p class="topMenuLink">&nbsp;</p>
				<div id="page23">
                                    <video width="100%" height="400" controls>
                                    <?php ################### Start S3 Implementation Code ######################### ?>
                                    <!--source src="https://www.competiscan.com/fileuploads/15466MyExcel.mp4" type="video/mp4"-->
                                    <source src="<?= $displays3URL.'fileuploads/15466MyExcel.mp4'; ?>" type="video/mp4">
                                    <?php ################### End S3 Implementation Code ######################### ?>
                                    Your browser does not support HTML5 video.
                                  </video>        
				</div>
				
				</td></tr>
				<?php
				break; 
                       
                            
			default:
				?>
				<tr><td class="bodytext">Competiscan Help Line: 312.488.1810</td></tr>
				
                                <tr><td class="bodytext"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?vid=6">Saved Searches Email Alerts (01:28)</a></td></tr>
                                <tr><td class="bodytext"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?vid=7">Export Baskets (01.58)</a></td></tr>
                                <tr><td class="bodytext"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?vid=8">Mailbox Study (01:23)</a></td></tr>
                                <tr><td class="bodytext"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?vid=9">Enhanced Search (02:45)</a></td></tr>
                                <tr><td class="bodytext"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?vid=10">Retrieval Services (0:22)</a></td></tr>
                                <tr><td class="bodytext"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?vid=11">Trend Reports (0:37)</a></td></tr>
                                <tr><td class="bodytext"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?vid=12">OCR-Full Text (0:52)</a></td></tr>
                                <tr><td class="bodytext"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?vid=13">My Excel (0:32)</a></td></tr>
                                
                                <tr><td class="bodytext">
                                <?php ################### Start S3 Implementation Code ######################### ?>
                                <!--a href="https://www.competiscan.com/fileuploads/33552Competiscan-FeaturesOverview.pdf" target="_blank">Click here for a Competiscan Overview</a-->
                                <!--<a href="<?= $displays3URL.'fileuploads/33552Competiscan-FeaturesOverview.pdf'; ?>" target="_blank">Click here for a Competiscan Overview</a>-->
                                <a href="<?= $displays3URL.'fileuploads/26414Competiscan-FeaturesOverview.pdf'; ?>" target="_blank">Click here for a Competiscan Overview</a>
                                <?php ################### End S3 Implementation Code ######################### ?>
                                </td></tr>
                               <!-- http://www.competiscan.com/fileuploads/43912Competiscan-FeaturesOverview.pdf -->
				<tr><td class="bodytext"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?vid=1">Click here for a Competiscan Introductory Video</a></td></tr>
				<!--<tr><td class="bodytext"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?vid=2">Click here for an Updated 10 Minute Tutorial on Using Competiscan</a></td></tr>-->
                <tr><td class="bodytext">
                <?php ################################ Start S3 Implementation Code ################################# ?>
                <!--a href="http://www.competiscan.com/fileuploads/94602Publication_Name.pdf" target="_blank">Click here for a list of publications tracked</a-->
                <a href="<?= $displays3URL.'fileuploads/94602Publication_Name.pdf'; ?>" target="_blank">Click here for a list of publications tracked</a>
                <?php ################################ End S3 Implementation Code ################################# ?>
                </td></tr>
				<tr><td class="bodytext">&nbsp;</td></tr>
				<!--<tr><td class="bodytext"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?vid=3">Quick Tips: Saved Searches and Email Alerts</a></td></tr>
				<tr><td class="bodytext"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?vid=4">Quick Tips: Life Insurance and Annuities</a></td></tr>
				<tr><td class="bodytext"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?vid=5">Quick Tips: Health Insurance Carriers</a></td></tr>-->
				<?php                                 
				//<tr><td class="bodytext"><a href="https://competiscan.webex.com/competiscan/lsr.php?RCID=05a1627021092ee1b08fbd51cf3785d3" target="_blank">Quick Tips: P&amp;C</a></td></tr>
		}
	}
	else {
		?>
		<tr><td class="bodytext" style="height:200px;" valign="top"><a href="contactus.php" class="bottomLinks">Contact Us</a></td></tr>
		<?php
	}
	?>
</table>
<?php
include 'footer_bottom.php';
?>