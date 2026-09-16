<?php
////$output = shell_exec('mysql -h10.0.0.190 -uroot -p"root@20165" competi_competidblatest -e"SELECT * FROM cscan_product_detail limit 1000000" > /tmp/product.xls');
////echo "<pre>$output</pre>";
////die;
//$file = fopen("/tmp/directdb.csv","r");
////$data=fgetcsv($file);
//$num=0;
//while(! feof($file))
//{
//    
//    echo "<br/>".$num++;
//    echo "<pre>";
//     print_r(fgetcsv($file));
//     
//     if($num>4000)
//         break;
//}
////echo count($data);
////echo "<pre>";
////print_r($data[3]);
//echo $num;
//die;

?>

<?php 
$start_time = microtime(true);
require_once('includes/globalSession.php');
require_once('sphinxapi2.php');
//require_once('includes/checklogin.php');
//require_once('includes/paginator.php');       //paginator class. 
//require_once('includes/paginator_html.php');  //paginator_html class.
function startSphinxmew($filter = 'dts_active', $filterval = array(1)) {
    global $SPHINX_server, $SPHINX_port;
    if (empty($SPHINX_server)) {
        $SPHINX_server = 'localhost';
    }
    if (empty($SPHINX_port)) {
        $SPHINX_port = 9312;
    }
    if (!$s = new SphinxClient()) {
        sphinxErr(__LINE__, $s, 'SphinxClient');
    }
    if (!$s->setServer($SPHINX_server, $SPHINX_port)) {
        sphinxErr(__LINE__, $s, 'setServer');
    }
    if (!$s->setMatchMode(SPH_MATCH_PHRASE)) {
        sphinxErr(__LINE__, $s, 'setMatchMode');
    }
    if (!$s->setRankingMode(SPH_RANK_NONE)) {
        sphinxErr(__LINE__, $s, 'setRankingMode');
    }
    if (!$s->setFilter($filter, $filterval)) {
        sphinxErr(__LINE__, $s, 'setFilter');
    }
    if (!$s->setSortMode(SPH_SORT_EXTENDED, '@id ASC')) {
        sphinxErr(__LINE__, $s, 'setSortMode');
    }
    //$s->setGroupBy('productID',SPH_GROUPBY_ATTR);
    return $s;
}
$sk = 'credit card';
if(isset($_REQUEST['key']) && $_REQUEST['key']!=''){
    $sk=$_REQUEST['key'];
}
                      #  $s = startSphinx();
 
#            $result = $s->Query("Commentary: Nov 2021 Holiday Shopping Insights", "base_index_prod_trendreport_fulltext");
 
#            echo "<pre>.okok";
#            print_r($result);
#            echo "</pre>";die;
 
                        $s = startSphinxmew();
                        
                        //$s->SetSelect ( "productID" );
                        
						//$SPHINX_name='prod_digitalreport';
						$SPHINX_name='prod';
                        $inds = 'base_index_' . $SPHINX_name ;
                        $inds = 'base_index_prod_trendreport_fulltext' ;
                        $ps = parseSphinx($s, $sk);
			print_r($ps);die;
			$maxID='400000';
                        $step='200000';
                        if(isset($_REQUEST['k']) && $_REQUEST['k']!=''){
                        //$count_save_sql = "SELECT MAX(dts_id) FROM cscan_document_text_search";
                        $count_save_sql = "SELECT MAX(id) FROM cscan_digital_processed_title";
                        $rs = $DRW->query($count_save_sql, $DRW_read);
                        $data = $DRW->fetch_row($rs);
                        $maxID = $data[0];
                        }
                        
                        $num=1;
                        $data=array();
                        for ($offset = 0; $offset <= $maxID; $offset+=$step) {
                            $s->setLimits(0, $step, $step);
                            if (!$result = $s->query($ps, $inds)) {
                                sphinxErr(__LINE__, $s, 'query', $ps);
                            }
			
                       /*echo"<pre>"; 
                       print_r($result);exit;
                            foreach ($result['matches'] as $key=>$keydata){        
                                  //  echo $num."==>".$keydata['attrs']['productid'];
                                  //  echo "<br/>";
                                $data[]=$keydata['attrs']['productid'];
                             $num++;       
                            }*/
                            
                            		
                        
                        }
                         echo  $num."==>".$result['total'].'==='.$result['total_found'].'time'.$result['time'];
			echo"<br>";
                        
                        
                        
                        echo count(array_unique($data));
                         
                        ?>
