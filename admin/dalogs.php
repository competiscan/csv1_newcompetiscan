<?php
$ALLOW_GROUPS = array(56);
require_once("../auth_auth.php");
function pr($str){
    echo '<pre>';print_r($str);
}
$order = 0;
$sort = '';
$order_by = 'SORT_ASC';
if(isset($_GET['p'])) $p = trim($_GET['p']);
else $p = 0;
include 'top.php';
if(!empty($_REQUEST)){
    if(!empty($_REQUEST['sort']))
        $sort = trim($_REQUEST['sort']);
    if(!empty($_REQUEST['order']))
        $order = trim($_REQUEST['order']);
}
function doSort($sort, $order, $dosort, $p, $spacer = '</br>') {
    if(empty($order)){
        $order = 1;
    }else{ 
        $order = 0;
    }
    if ($sort == ($dosort * -1) || $sort != $dosort) {
        print "$spacer<a href=\"" . $_SERVER['PHP_SELF'] . "?sort=$dosort&order=$order&p=$p\" class=\"blue\">sort</a>";
    } else {
        print "$spacer<a href=\"" . $_SERVER['PHP_SELF'] . "?sort=$dosort&order=$order&p=$p\" class=\"blue\">sort</a>";
    }
}
$index_inputData=[];
$year = '';
$month = '';
if(!empty($_GET['year']) && !empty($_GET['month'])){
    $year = trim($_GET['year']);
    $month = trim($_GET['month']);
    $ym = $year.'-'.str_pad($month, 2, '0', STR_PAD_LEFT);
}else{
    $year = date('Y');
    $month = date('m');
    $ym = date('Y-m');
}
$numrows = 0;


$csvpath=dirname(__FILE__)."/../dacsv/".$ym."_daLog.csv";
//$csvpath=dirname(__FILE__)."/".$ym."_daLog.csv";
if(file_exists($csvpath)){ 
    if (($index_input = fopen($csvpath, "r")) !== FALSE) {
        $a = 0;
        while (($index_data = fgetcsv($index_input, 1000, ",")) !== FALSE) {
            //excude headlines first & grab other rows
            if(!empty($sort)){
                if(count($index_data[0])>0){
                    $index_inputData[] = $index_data;
                }
            }else{
                if(count($index_data[0])>0){
                    if($a==0){
                        $index_inputData[1] = $index_data;
                    }else{
                        $time = strtotime($index_data[2]);
                        $date = strtotime(date("Y-m-d", strtotime($index_data[2])));
                        $index_inputData[$date][$time] = $index_data;
                    }
                }
            }
            $a++;
        }
        fclose($index_input);
    }
    $numrows = count($index_inputData);
}
?>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<table width='100%' border='0' cellspacing='0' cellpadding='8' class='text' rules='none' bordercolor='#B9B9B9' align='center' style='border-collapse:collapse'>
    <tr>
        <td class="adminhead" align='center'>
            <a href="dalogs.php" style="color: #ffffff;">DA's Logs</a>
        </td>
    </tr>
    <!-- search and right buttons start-->
    <tr>
        <td class="bodyText" colspan="7">
            <table width='100%' border="0" cellspacing="0" cellpadding="1" class="text">
                <tr>
                    <form method="get" name="rangeForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" style="display:inline;">
                        <td align="left" width='30%'><strong>Filter By:</strong></td>
                        <td align="right" width='10'><strong>Year:</strong></td>
                        <td align="left" width='5%'>
                            <select name="year" class="input_box">
                                <option>-- Year --</option>
                                <?php
                                $end = 2015;
                                $start = date('Y');                                
                                while($start >= $end){
                                    $selected = ($start == $year)?'selected="selected"':'';
                                    echo '<option value="'.$start.'" '.$selected.'>'.$start.'</option>';
                                    $start--;
                                }
                                ?>                                
                            </select>
                        </td>
                        <td align="right" width='5%'><strong>Month:</strong></td>
                        <td>
                            <select name="month" class="input_box">
                                <option>-- Month --</option>   
                                <?php
                                $formattedMonthArray = array(
                                    "1" => "January", "2" => "February", "3" => "March", "4" => "April",
                                    "5" => "May", "6" => "June", "7" => "July", "8" => "August",
                                    "9" => "September", "10" => "October", "11" => "November", "12" => "December",
                                );
                                foreach ($formattedMonthArray as $key=>$m) {
                                    $selected = ($key == $month) ? 'selected' : '';
                                    echo '<option '.$selected.' value="'.$key.'">'.$m.'</option>';
                                }
                                ?>
                            </select>
                        </td>
                        <td align="left">
                            <input class="button" style="width:60px;" type="submit" name="search" value="Search" />
                        </td>
                    </form>
                </tr>
            </table>
        </td>
    </tr>
    <?php if(!empty($numrows)){?>
    <tr>
        <td>
            <table width='100%' border='0' cellspacing='0' cellpadding='8' class='text' rules='none' bordercolor='#B9B9B9' align='center' style='border-collapse:collapse'>
                
                <tr>
                    <?php if(!empty($sort)){?>
                        <?php $i=1;foreach($index_inputData[0] as $heading):?>
                         <?php if($i==5){?>
                    <td align="left" class="adminhead" height='15px' ><b>Time Taken<br>(Min)</b></td>                             
                        <?php } ?>    
                            <td align="left" class="adminhead" height='15px' ><b><?=$heading;?></b><?php //if($i<6)doSort($sort,$order,$i,$p); ?></td>
                        <?php $i++;endforeach;?>
                        <?php
                        $className='';
                        array_shift($index_inputData);
                        //pr($index_inputData);die;
                        $sorting = array();
                        foreach ($index_inputData as $key => $row){//pr($row);
                            $sorting[$key] = strtolower($row[$sort-1]);
                        }
                        if($order==1){
                            array_multisort($sorting, SORT_DESC, $index_inputData);
                        }else{
                            array_multisort($sorting, SORT_ASC, $index_inputData);
                        }
                        //pr($index_inputData); //die;
                        foreach($index_inputData as $logs):
                            if ($className=='selected-bg') $className='white-bg';
                            else $className='selected-bg';
                            if(!isset($logs[5]))$logs[5]='-';
                        ?>
                        <tr valign=top class="<?php echo $className; ?>">
                            <td><?=$logs[0];?></td>
                            <td><?=$logs[1];?></td>
                            <td><?=date("M j, y | H:i:s A",strtotime($logs[2]))?></td>
                            <td><?=date("M j, y | H:i:s A",strtotime($logs[3]));?></td>
                            
                            <td><?php if($logs[0]=='Indexing(b)'){                                           
                                          echo round(abs(strtotime($logs[1][2]) - strtotime($log[2])) / 60,2);
                                        } else{                                           
                                            echo round(abs(strtotime($logs[3]) - strtotime($logs[2])) / 60,2);
                                        }?></td>
                            <td><?=$logs[4];?></td>
                            <td><?=$logs[5];?></td>
                        </tr>
                        <?php endforeach;?> 
                    <?php }else{$sort=1;?>
                        <?php $i=0;foreach($index_inputData[1] as $heading):?>
                        <?php if($i==4){?>
                                <td align="left" class="adminhead" height='15px' ><b>Time Taken<br>(Min)</b></td>                             
                        <?php } ?>
                            <td align="left" class="adminhead" height='15px' ><b><?=$heading;?></b><?php //if($i<6)doSort($sort,$order,$i,$p); ?></td>
                        <?php $i++;endforeach;?>
                        <?php
                        $className='';
                        krsort($index_inputData);
                        array_pop($index_inputData);
                        //pr($index_inputData);die;
                        foreach($index_inputData as $logs):
                            $sorting = array();
                            foreach ($logs as $key => $row){
                                $sorting[$key] = strtolower($row[2]);
                            }
                            array_multisort($sorting, SORT_ASC, $logs);
                            
                            if ($className=='selected-bg') $className='white-bg';
                            else $className='selected-bg';                   
                        ?>
                            <?php //pr($logs); //die; ?>
                            <?php foreach($logs as $log): if(!isset($log[5]))$log[5]='-';?>
                            <tr valign=top class="<?php echo $className; ?>">
                                <td><?=$log[0];?></td>
                                <td><?=$log[1];?></td>
                                <td><?=date("M j, y | H:i:s A",strtotime($log[2]));?></td>
                                <td><?=date("M j, y | H:i:s A",strtotime($log[3]));?></td>
                                <td><?php if($log[0]=='Indexing(b)'){                                           
                                          echo round(abs(strtotime($logs[1][2]) - strtotime($log[2])) / 60,2);
                                        } else{                                           
                                            echo round(abs(strtotime($log[3]) - strtotime($log[2])) / 60,2);
                                        }?>
                                </td>
                                <td><?=$log[4];?></td>
                                <td><?=$log[5];?></td>
                            </tr>
                            <?php endforeach;?>

                        <?php endforeach;?>
                    <?php }?>
                </tr>
            </table>
        </td>
    </tr>   
    <?php 
    }else{?>
        <tr><td colspan='4' align='center' class='error'>No record(s) found.</td></tr>
<?php }?>
</table>
<?php include 'bottom.php';?>