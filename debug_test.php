<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
echo "Step 1: PHP OK<br>";
require_once('includes/competi_def.php');
echo "Step 2: competi_def OK<br>";
require_once('includes/dbcon.php');
echo "Step 3: dbcon OK<br>";
echo "Step 4: All OK!";
?>
