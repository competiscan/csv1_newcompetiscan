<?php
//require_once("includes/ehLog_set.php");
require_once ("includes/dbcon.php");
$DRW->databaseReadWrite_die = 1;
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set("memory_limit", "-1");
set_time_limit(0);
echo 'Start: '.date("Y-m-d H:i:s").'</br></br>';
if(!defined('ENV')){
    define('ENV',getenv('SERVER_NAME'));
}
//Start CSV1 DB connection
$conn_csv1 = mysqli_connect("10.0.0.19","root","root@20165","competi_demo");

// Check connection
if (mysqli_connect_errno()) {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  exit();
}
/*$conn_csv1 = mysqli_connect('10.0.0.19', 'root', 'root@20165');
if (!$conn_csv1) {
    die('Could not connect: ' . mysqli_error());
}
$mydb=mysqli_select_db('competi_demo', $conn_csv1);
if (!$mydb) {
    die('Could not select db: ' . mysql_error());
} */ 

$tableArray=Array('cscan_product_detail');
//$tableArray=Array('cscan_age','cscan_product_detail','cscan_credit_access_checks','cscan_img');
echo "<pre>";
print_r($tableArray); 
foreach($tableArray as $tableName){
    //echo $check_structure=show_table($tableName); die;
    //where  report_modify_date>='2023-04-10'
    $sqlQuery="select * from $tableName"; 
    $result = $DRW->query($sqlQuery,$DRW_read2);
    if($DRW->num_rows($result)>0){
        while($row = $DRW->fetch_assoc($result)){
        //echo implode(',',array_keys($row)); 
        //$cols=implode(',',array_keys($row)); 
        //$value=implode(',',array_values($row)); 
        $InsertQuery= "INSERT INTO $tableName (".implode(", ",array_keys($row)).") VALUES ('".implode("', '",array_values($row))."')";
        //echo $insertQuery="insert into $tableName($cols) values ('".$value."')"; die;
        //$result = $DRW->query($InsertQuery,$DRW_main);
        if ($conn_csv1->query($InsertQuery) === TRUE) {
            echo "Record added successfully!";
          } else {
            echo "Error: " . $InsertQuery . "<br>" . $conn_csv1->error;
          }
        }
    }   
}

function show_table($tabela)
{
    global $DRW, $DRW_read, $DRW_main, $DRW_crm;
    $sql = "DESCRIBE $tabela";
    $result = $DRW->query($sql,$DRW_read);
    //$result = $DRW->query($sql,$DRW_read);
    while ($coluna = $DRW->fetch_assoc($result)) 
    {
        echo "<p>".$coluna['Field']." - ";
        echo $coluna['Type']."</p>";
    }
}
echo 'completed: ';
echo 'End: '.date("Y-m-d H:i:s").'</br></br>';
die;
?>