<?php
require_once('includes/globalSession.php');
if(!isset($_SESSION['public_admin_access'])){
	require_once('includes/checklogin.php');
}

$cs_id 		= (isset($_REQUEST['cs_id']) ? (float)$_REQUEST['cs_id'] : 0);
$productID	= (isset($_REQUEST['productID']) ? (float)$_REQUEST['productID'] : 0);

$cs_ids         = array($cs_id);
$caption	= "";
$questions_html	= "";

if (!empty($productID)) {
	$cs_ids = array();
	
	$surveys_by_product = "SELECT cs_id from cscan_survey WHERE productID=$productID ORDER BY cs_id DESC"; //effectively, by date
	$cs_id_rows = $DRW->query($surveys_by_product,$DRW_read);
	while($cs_res = $DRW->fetch_row($cs_id_rows)) {
		$cs_ids[] = $cs_res[0];
	}
}
for($i=0; $i<sizeof($cs_ids); $i++) {
	$cs_id = $cs_ids[$i];
	$caption = ($i == 0 ? buildCaption($cs_id) : $caption);

	$questionQuery 	= "SELECT cs_title, DATE_FORMAT(cs_date,'%m/%d/%Y') as survey_date, cp.competi_id as panelistID, questions "
			. "FROM cscan_survey cs LEFT JOIN cscan_panelists cp ON (cp.panelist_id=cs.panelistID) WHERE cs_id=$cs_id";
	$questionQuery 	= $DRW->query($questionQuery, $DRW_read);
	$questionsRs 	= $DRW->fetch_array($questionQuery);
	$title		= $questionsRs['cs_title'];
	$date		= $questionsRs['survey_date'];
	$panelistID	= $questionsRs['panelistID'];
	$questions      = $questionsRs['questions'];
	
	//temp file, so we can use fgetcsv & handle newlines inside the answer
	$tempcsv 	= tmpfile();
	$csv_length 	= strlen($questions);
	fwrite($tempcsv, $questions);
	rewind($tempcsv);
	 
	$questions_html .= "<div class='left'><strong>Date:</strong> $date<br /><strong>Panelist ID:</strong> $panelistID<br /><strong>Title</strong>: $title</div>";
	$questions_html .= "
		<div class='right'>
			<table width='100%' cellpadding='8' cellspacing='0'>
				<tr>
					<td style='border-bottom: 1px solid #EEE; font-weight: bold;' width='50%'>Questions</td>
					<td style='border-bottom: 1px solid #EEE; font-weight: bold;' width='50%'>Answers</td>
				</tr>";

	while($row = fgetcsv($tempcsv, $csv_length, ",")) {
		$question = nl2br($row[0]);
		$answer = nl2br(sizeof($row) > 1 ? $row[1] : "");
                $questions_html .= "<tr><td>$question</td><td>$answer</td></tr>";
        }

	$questions_html .= "	
			</table>
		</div><div class='clear'>&nbsp;</div>";
	
}

function buildCaption($cs_id) {
	global $DRW;
	global $DRW_read;
	//
	$surveyQuery  	= "SELECT productID FROM cscan_survey WHERE cs_id=$cs_id";
	$surveyQuery 	= $DRW->query($surveyQuery,$DRW_read);
	$surveyRs 	= $DRW->fetch_array($surveyQuery);
        $productID      = $surveyRs['productID'];
	
	$productQuery   = "SELECT companyName,entryID "
                        . "FROM cscan_product_detail pd,cscan_company_product pp,cscan_company pa "
                        . "WHERE pd.productID=$productID AND pd.productID=pp.productID AND pa.companyID=pp.companyID AND primary_co=1";
	$productQuery 	= $DRW->query($productQuery,$DRW_read);
	$productRs 	= $DRW->fetch_array($productQuery);
	$companyName	= $productRs['companyName'];
	$entryID	= $productRs['entryID'];

	$caption 	=($companyName == "" ? "" : "$companyName, ");
	$caption	.="Entry ID: $entryID";
	return $caption;
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Competiscan</title>
<link href="includes/competiscan_stylesheet.css?v=20100318" rel="stylesheet" type="text/css" />
<style>
	body { font-family: Verdana, Arial, Helvetica, Sans-Serif; font-size: 80%; }
	.caption { font-weight: bold; font-size: 1.2em; margin-bottom: 20px;}
	.left { float: left; width: 15%; font-size: 0.9em; padding-top: 8px; }
	.right { float: right; width: 85%; text-align: left; }
	.clear { clear: both; height: 10px; }
</style>
</head>
<body>
	<div class="caption"><?php echo $caption; ?></div>

	<?php echo $questions_html; ?>	

<?php 
	//echo $questions_html; 
?>
</body>
</html>
