 <?php ob_start();
 function exportOrderList($sql,$sess_userID,$file_choice,$localhost) { global $_adminConfig;
              
              if($file_choice==2){
				  $extnsn='.csv';
			  }else if($file_choice==3){
				  //$extnsn='.xlsx';
				  $extnsn='.csv';
				  
			  }else{
				  //$extnsn='.xls';
				  $extnsn='.csv';
			  }
 
        $_adminConfig['exportCsvPath']='exportexcel/';
        $exportFileName = "Competiscan_Export_".$sess_userID."_".date("Y-m-d").$extnsn;
        

       // $exportQuery = substr($sql, 0, stripos($sql, 'limit'));
      //  $exportQuery = str_ireplace(' desc', ' asc', $exportQuery);
	 //   $exportQuery = str_ireplace(substr($exportQuery, 0, stripos($exportQuery, 'from')), ' ', $exportQuery);                      
               
      
     if($localhost!=''){ 
        $host = '10.0.0.190';        
        $uname = 'root';
        $dbpass = 'root@20165';
        $dbname = 'competi_competidblatest';
	 }else{
                /*
		$host = 'prod-competiscan-aurora-rdscluster-dvaq91kfqg1r.cluster-cyvqwrvzthv4.us-east-1.rds.amazonaws.com';        
                $uname = 'prodadmin';
                $dbpass = 'Xohv3iewotezu8ah';
                */
                $host = '52.44.133.155';        
                $uname = 'app_readuser';
                $dbpass = 'Ano@11SDFLH@13NMldrf';
                
                $dbname = 'competi_competidb';
	 } 	               
                 
        $export = "mysql -h {$host} -u {$uname} -p{$dbpass} {$dbname} -e \"".$sql.";\" | sed  's/\\t/\",\"/g;s/^/\"/;s/$/\"/g' > ".$_adminConfig['exportCsvPath'].$exportFileName;
        //echo $export; exit;
        system($export);     
       
        exit;
    }    ?>
