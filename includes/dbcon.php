<?php

if(!isset($DRW_connections)){
	require("competi_def.php");
}
if(!class_exists('databaseReadWrite')){
	require("databaseReadWrite.php");
}
$DRW = new databaseReadWrite($DRW_connections,$DRW_main,$DRW_die);
$dbh = $DRW->current_dbh;

define("MAX_RESET_POINT", "2000");
define("EMAIL_PIECE_MULTIPLIER", "0.25");
define("DIRECT_EMAIL_PIECE_MULTIPLIER", "15");
define("DIGITAL_MAIL_POINT", "200");
define("EMAIL_PIECE_MULTIPLIER_PRODUCER", "1");
#define("ANNOTATIONTOOLDATAURL","https://ml-anotation.competiscan.com/v2/muid-data/");
#define("ANNOTATIONTOOLDATAANALYSISURL","https://ml-anotation.competiscan.com/v1/data-analysis/");
#define("ANNOTATIONTOOLDATAURL","https://vat-api.competiscan.com/v3/muid-data/");
#define("ANNOTATIONTOOLDATAANALYSISURL","https://vat-api.competiscan.com/v3/get-analysis/");
define("ANNOTATIONTOOLDATAURL","https://vat.competiscan.com/display/");
define("ANNOTATIONTOOLDATAANALYSISURL","https://vat.competiscan.com/getanalysis/");
define("API_URL","https://api1.competiscan.com/elasticsearch/v1/search/");
define("API_URL_UAT","https://api1-uat.competiscan.com/elasticsearch/v1/search/");
define("API_DOWNLOADURL","https://api1.competiscan.com/elasticsearch/v1/search/download");
define("API_URL_EMAIL_ALERT","https://api1.competiscan.com/elasticsearch/v1/search/onlypids");
define("API_URL_EMAIL_ALERT_UAT","https://api1-uat.competiscan.com/elasticsearch/v1/search/onlypids");
define("RPVAPIURL_UAT","https://api1-uat.competiscan.com/vat-backend/v1/");
define("RPVAPIURL","https://api1.competiscan.com/vat-backend/v1/");


define("ELASTIC_SAVE_SEARCH_DEV","https://dev02.competiscan.com:5426/savesearch");
define("ELASTIC_SAVE_SEARCH_NAME_DEV","https://dev02.competiscan.com:5426/searchname/save");
define("ELASTIC_SAVE_SEARCH_UAT","https://api1-uat.competiscan.com/emailaleart/v1/savesearch");
define("ELASTIC_SAVE_SEARCH_NAME_UAT","https://api1-uat.competiscan.com/emailaleart/v1/searchname/save");
define("ELASTIC_SAVE_SEARCH_PROD","https://emailaleart.competiscan.com/savesearch");
define("ELASTIC_SAVE_SEARCH_NAME_PROD","https://emailaleart.competiscan.com/searchname/save");
define("CHK_DATA_BISCIENCE_SETTIME"," -1 days");
define("DOWNLOAD_MYEXCEL_UAT","https://myexcel.competiscan.com/download-myexcel-data");
define("DOWNLOAD_MYEXCEL_PROD","https://myexcel.competiscan.com/download-myexcel-data");
define("DOWNLOAD_MYEXCEL_DEV","https://dev02.competiscan.com:5428/download-myexcel-data");
define("PROGRESS_MYEXCEL_PROD","https://myexcel.competiscan.com/progress/");
define("PROGRESS_MYEXCEL_UAT","https://myexcel.competiscan.com/progress/");
//CHART API
define("APIURL_CHART_UAT","https://csv2-myexcel-uat.competiscan.com/");
define("APIURL_CHART_PROD","https://myexcel.competiscan.com/");
//https://csv2-myexcel-uat.competiscan.com/progress/
#### SSO AUTH LOGIN


define("AUTH_URL_PROD", "https://saml.competiscan.com/login?");
define("AUTH_URL_DIRECT_LINK", "https://saml.competiscan.com/oauth2/authorize?client_id=4msu5ro74sup36suf16in9lmbe&response_type=code&scope=email+openid+phone&redirect_uri=https%3A%2F%2Fcompetiscan.com%2Fsso_auth_prod.php");
define("CALLBACK_URL_PROD", "https://competiscan.com/sso_auth_prod.php");
define("ACCESS_TOKEN_URL_PROD", "https://saml.competiscan.com/oauth2/token");
define("CLIENT_ID_PROD", "4msu5ro74sup36suf16in9lmbe");
define("CLIENT_SECRET_PROD", "1vfabo7gogtf2imhg7v3gojb2fol4tmmvuig56luqmvtpnnqvhf");
define("ACCESS_API_URL_PROD", "https://api2.competiscan.com/users/v1/user-from-token");
define("SCOPE_PROD", ""); 
// optional*/

define("CALLBACK_URL_UAT", "https://competiscan.com/sso_auth.php");
define("AUTH_URL", "https://samluat.competiscan.com/login?");
define("ACCESS_TOKEN_URL", "https://samluat.competiscan.com/oauth2/token");
define("CLIENT_ID", "3ss3369kgij95ru952b8finpr4");
define("CLIENT_SECRET", "h935u6cjmsgq58seaa51e3vchbf2fitcph45mu8mhl4ci08mou");
define("ACCESS_UAT_API_URL", "https://api2.competiscan.com/users/v1/user-from-token");
define("SCOPE", ""); // optional

define("CALLBACK_URL_NMG", "https://competiscan.com/sso_auth_uat.php");
define("AUTH_URL_NMG", "https://samluat.competiscan.com/login?");
define("ACCESS_TOKEN_URL_NMG", "https://samluat.competiscan.com/oauth2/token");
define("CLIENT_ID_NMG", "6dlvlc0pl9ar5ct3sub70s00s5");
define("CLIENT_SECRET_NMG", "169ug9mikvqd6kb6vfba84c7e20pgi1189lg4t0e6rvmk2dh21jg");
define("ACCESS_UAT_API_URL_NMG", "https://api2.competiscan.com/users/v1/user-from-token");
define("SCOPE_NMG", ""); // optional


// SSO DYNAMIC URL
define("CALLBACK_URL_MAIN", "https://competiscan.com/sso_auth_main.php");
//define("AUTH_URL_MAIN", "https://nmg-test-user-pool-uat.auth.us-west-2.amazoncognito.com/login?");
//define("ACCESS_TOKEN_URL_MAIN", "https://nmg-test-user-pool-uat.auth.us-west-2.amazoncognito.com/oauth2/token");
//define("CLIENT_ID_MAIN", "5u7e0sb38523snnth4mkujpkm1");
//define("CLIENT_SECRET_MAIN", "ft48r9cnqms0g84m7qj28v724fbf25vadpferuueaopvf43khqh");
define("ACCESS_UAT_API_URL_MAIN", "https://users-uat-api.competiscan.com/user-from-token");
define("SCOPE_MAIN", ""); // optional

define("TREND_REPORT_API_DEV_URL", "https://dev06.competiscan.com:5414/");



if(!defined('ENV')){ 
	define('ENV',getenv('SERVER_NAME'));
}

if(ENV == 'localhost' || ENV == 'demo.competiscan.com'){
define("USER_LOGIN_API_URL_PROD", "https://api1-uat.competiscan.com/client-profiles/v1/");
define("USER_PERMISSION_API_URL_PROD", "https://api1-uat.competiscan.com/users/v1/");
define("SUGGESTION_API_URL_UAT", "https://api1-uat.competiscan.com/master/v1/");
define("RETRIVAL_API_URL_UAT", "https://api1-uat.competiscan.com/retrieval-service/v1/");
define("ALERT_API_URL_UAT", "https://api1-uat.competiscan.com/master/v1/");
define("DIGITAL_DASHBOARD_UAT", "https://api-pre-prod.competiscan.com/digitaldashboard/v1/");
define("RETAIL_DASHBOARD_UAT", "https://api1-uat.competiscan.com/energy-dashboard-client/v1/");
define("DIGITAL_DASHBOARD_UAT_DOWNLAOD", "https://api-pre-prod.competiscan.com/digitaldashboard-elasticsearch/v1/");
define("SENDER_DOMAIN_AUTO_UAT", "https://api1-uat.competiscan.com/product/v1/search/");
define("TREND_REPORT_API_UAT_URL", "https://api1-uat.competiscan.com/trendreport-client-portal/v1/");
define("TREND_REPORT_DOC_API_UAT_URL", "https://api-pre-prod.competiscan.com/trendreport/v1/trend_document/");

}else{
define("USER_LOGIN_API_URL_PROD", "https://api1.competiscan.com/client-profiles/v1/");
define("USER_PERMISSION_API_URL_PROD", "https://api2.competiscan.com/users/v1/");
//define("USER_PERMISSION_API_URL_PROD", "https://api-pre-prod.competiscan.com/users/v1/");
define("SUGGESTION_API_URL_UAT", "https://api-pre-prod.competiscan.com/master/v1/");
define("RETRIVAL_API_URL_UAT", "https://api2.competiscan.com/retrieval-service/v1/");
define("ALERT_API_URL_UAT", "https://api2.competiscan.com/master/v1/");
//define("ALERT_API_URL_UAT", "https://api-pre-prod.competiscan.com/master/v1/");
define("DIGITAL_DASHBOARD_UAT", "https://api2.competiscan.com/digitaldashboard/v1/");
define("RETAIL_DASHBOARD_UAT", "https://api1-uat.competiscan.com/energy-dashboard-client/v1/");
define("DIGITAL_DASHBOARD_UAT_DOWNLAOD", "https://api2.competiscan.com/digitaldashboard-elasticsearch/v1/");
define("SENDER_DOMAIN_AUTO_UAT", "https://api-pre-prod.competiscan.com/product/v1/search/");
define("TREND_REPORT_API_UAT_URL", "https://api-pre-prod.competiscan.com/trendreport-client-portal/v1/");
define("TREND_REPORT_DOC_API_UAT_URL", "https://api-pre-prod.competiscan.com/trendreport/v1/trend_document/");
}



?>
