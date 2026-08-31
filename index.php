<?php
 /**
 * Legacy app handler to deal with pre-existing links and resources
 *
 */
if(!defined('ENV')){
define('ENV',getenv('SERVER_NAME'));
}
if (isset($_SESSION['sess_username']) and $_SESSION['sess_username']!='') {
    if (isset($_GET['product']) && $_GET['product']!='') {
        ############## Start: Email Tracking ################
        $tracking_id = (!empty($_GET['trmsg']))?trim($_GET['trmsg']):'';
        if(!empty($tracking_id)){
            $redirect_to_resource = "productDetail.php?id=".(float)$_GET['product'].'&trmsg='.$tracking_id;
        }else{
            $redirect_to_resource = "productDetail.php?id=".(float)$_GET['product'];
        }
        ################ End: Email Tracking ###############
    }

    if (isset($_GET['trend_id']) && $_GET['trend_id']!='') {
        $redirect_to_resource = "trend_reports.php?trend_id=".(int)$_GET['trend_id'];
        //############### ADD ENCODE TREND ID############
        /*if(ENV == 'localhost' || ENV == 'demo.competiscan.com'){
        $redirect_to_resource = "trend_reports.php?trend_id=".trim($_GET['trend_id']);
        }*/
    }

    if (isset($_GET['document']) && $_GET['document']!='') {
        $redirect_to_resource = "productDocuments.php?id=".(float)$_GET['document'];
    }

    if (isset($_GET['download']) && $_GET['download']!='') {
        $redirect_to_resource = "downloads.php?id=".preg_replace('/\\W+/','',$_GET['download']);
    }

    header("Location: $redirect_to_resource");
    exit;
} elseif (isset($_GET['product']) || isset($_GET['trend_id']) || isset($_GET['document']) || isset($_GET['download'])) {
    $redirect_to_login = str_replace('index.php', 'login.php', $_SERVER['REQUEST_URI']);
    header("Location: $redirect_to_login");
    exit;
}

/**
 * Front to the WordPress application. This file doesn't do anything, but loads
 * wp-blog-header.php which does and tells WordPress to load the theme.
 *
 * @package WordPress
 */

/**
 * Tells WordPress to load the WordPress theme and output it.
 *
 * @var bool
 */
define('WP_USE_THEMES', true);

/** Loads the WordPress Environment and Template */
require( dirname( __FILE__ ) . '/wordpress/wp-blog-header.php' );

