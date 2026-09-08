<?php
require_once('includes/globalSession.php');
require_once 'product_doc_tracker.php';
track_user();
@ob_clean();
$client_ip_address=get_client_ip_address(); 
//############# Start User visits Count ##################/
$today_date = date('Y-m-d');
if(isset($_SESSION['sess_userID'])){
 $loggin_user_id =  $_SESSION['sess_userID'];
  $session_user_id  ='';
  $andQuery_vist = " and loggin_user_id ='".$loggin_user_id."'";
}else{
    $loggin_user_id=''; 
    $session_user_id  = session_id();
    $andQuery_vist = " AND client_ip_address='".$client_ip_address."'";
} 
$sql_visit = "SELECT id,session_user_id,loggin_user_id,visit_count FROM cscan_visitor_counter where  DATE_FORMAT(visit_date,'%Y-%m-%d') = '".$today_date ."' $andQuery_vist";
$result_visit = $DRW->query($sql_visit, $DRW_read);
$num_row=$DRW->num_rows($result_visit);
if($num_row > 0){
} else{
    $sql = "INSERT INTO cscan_visitor_counter SET session_user_id='".$session_user_id."', loggin_user_id='" . $loggin_user_id. "',visit_count='1',client_ip_address='".$client_ip_address."'";
    $DRW->query($sql, $DRW_main);    
}
if($loggin_user_id!=''){
	$sql_del_guest = "DELETE FROM cscan_visitor_counter where  DATE_FORMAT(visit_date,'%Y-%m-%d') = '".$today_date ."' and loggin_user_id<1 AND client_ip_address='".$client_ip_address."'";
	$result_visit = $DRW->query($sql_del_guest, $DRW_main);
}
function get_client_ip_address() {
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
       $ipaddress = getenv('HTTP_FORWARDED');
    else if(getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = 'UNKNOWN';
    return trim($ipaddress);
}

//############# End User visits Count ##################/
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Competiscan">
	<title><?php echo isset($TITLE) && $TITLE ? $TITLE : 'Competiscan' ?></title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/custom.css" rel="stylesheet">
	<link href='https://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,400,300,600,700|Open+Sans+Condensed:300,700,300italic' rel='stylesheet' type='text/css'>
	<link rel="shortcut icon" href="/favicon.ico" />
	<link href="includes/competiscan_stylesheet.css?v=20100318" rel="stylesheet" type="text/css" />
	<script src="includes/jsFunctions.js?v=20090601"></script>
	<script src="includes/ajax.js?v=20090601"></script>
	<script>window.jQuery || document.write('<script src="js/jquery.js">\x3C/script>')</script>
	<?php echo isset($HEAD) ? $HEAD : '' ?>
</head>
<body <?php echo isset($BODYTAG) ? $BODYTAG : '' ?>>
<div class="loader" style="display:none;"></div>
<!-- Navigation -->
<nav class="navbar navbar-default navbar-fixed-top" role="navigation">
    <div class="container">
        <div class="navbar-header page-scroll">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand page-scroll" id="logo" href="/">
				<img src="/images/competiscan-logo.png" alt="Competiscan logo">
            </a>
        </div>
        <div class="collapse navbar-collapse navbar-ex1-collapse">
            <ul class="nav navbar-nav">
<?php include('./nav-common.php') ?>
            </ul>
        </div>
        <!-- /.navbar-collapse -->
    </div>
    <!-- /.container -->
</nav>

<div id="titlebar">
	<div class="container">
		<h1><?php echo isset($PAGE_HEADING) ? $PAGE_HEADING : '' ?></h1>
		<div id="breadcrumbs">
			<span><a href="/">Competiscan</a></span>
			<span class="separator">/</span>
			<span class="current"><?php echo isset($PAGE_HEADING) ? $PAGE_HEADING : '' ?></span>
		</div>
	</div>
</div>

<div id="content" class="container">
<?php 
if(!empty($_SESSION['sess_username'])) {
        $show_header_top=true;
    
    /*######## Start for Page permission ########*/ 
    if(!defined('ENV')){
        define('ENV',getenv('SERVER_NAME'));
    }  
        
    $page_permissions = getPagePermission();  
    if(!empty($_SESSION['sess_search_page_permission'])){
        $page_permissions=$_SESSION['sess_search_page_permission'];
    }      
    if(!empty($page_permissions) AND in_array('power_search',$page_permissions)){
        $show_header_top=true;
    }else{
        $show_header_top=false;
    }
    /*######## End for Page permission ########*/
    if($show_header_top){    
    ?>
	<table cellspacing="0" class="searchMenutab">
            <tr> 
                <td><a href="emailAlerts.php" title="Your Search Settings">Email Alerts</a></td>
                <td><a href="savedsearch.php" title="Your Saved Search">Saved Searches</a></td>
                <td><a href="baskets.php" title="Export Baskets">Export Baskets</a></td>
                <td><a href="fullsearch.php?searchview=2" title="Power Search">Power Search</a></td>
                <td><a href="lastSearch.php" title="View the last search performed by you">Last Search</a></td>
                <td><a href="lastResult.php" title="View results of the last search performed by you">Last Results</a></td>
            </tr>
	</table>
<?php }
 }?>
<div id="page">
