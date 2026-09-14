<?php
function pagination($count,$getstring){
 	if(isset($_GET['searchstart']) && $_GET['searchstart'] != '') $limstart = $_GET['searchstart'];
 	else $limstart = 0;
 	if(isset($_GET['searchmax']) && $_GET['searchmax'] != '') $limiter = $_GET['searchmax'];
 	else $limiter = 30;
 	$pagelink="searchstart=$limstart&searchmax=$limiter";
 	$limittxt = " LIMIT $limstart,$limiter";
 	$limitline ="<span class='bold'>";
 	//1 -  51  Displayed  ( First Prev  1  2  3  4  5  Next Last )
 	if($count>$limiter){
 		$firstlink = 'First';
 		$prevlink = 'Prev';
 		$nextlink = 'Next';
 		$lastlink = 'Last';
 		$middlelinks = '';
 		//first and previous only if not on first
 		if($limstart>0){
 			if($limstart>=$limiter) $prev = $limstart - $limiter;
 			else $prev = 0;
 			$firstlink = "<a href='{$_SERVER['PHP_SELF']}?searchstart=0$getstring&searchmax=$limiter'>First</a>";
 			$prevlink = "<a href='{$_SERVER['PHP_SELF']}?searchstart=$prev$getstring&searchmax=$limiter'>Prev</a>";
 		}
 		// middle loop through total results
 		$numbers = ceil($count/$limiter);
 		$loopstart = ceil($limstart/$limiter);
 		if($loopstart<4) $loopstart = 0; // begin, do not move until 4
 		if($numbers<5) $loopend = $numbers; // loopend is less than 5
 		else $loopend = $loopstart+5;
 		if($loopend>$numbers && $loopstart!=0) { // end, show last 5
 			$loopstart = $numbers - 5;
 			$loopend = $numbers;
 		}
 		for($i=$loopstart; $i<$loopend; $i++){
 			$startnum = $limiter * $i;
 			if($startnum!=$limstart) $middlelinks .= "<a href='{$_SERVER['PHP_SELF']}?searchstart=$startnum$getstring&searchmax=$limiter'>".($i+1)."</a> ";
 			else $middlelinks .= ($i+1).' ';
 		}
 		//next and last if not on last
 		if($limstart<$count && (($limstart+($limiter*2))<$count || ($count - ($limstart + $limiter))>0)){
 			$next = $limstart + $limiter;
 			$nextlink = "<a href='{$_SERVER['PHP_SELF']}?searchstart=$next$getstring&searchmax=$limiter'>Next</a>";
 			$lastlink = "<a href='{$_SERVER['PHP_SELF']}?searchstart=".(($numbers-1)*$limiter)."$getstring&searchmax=$limiter'>Last</a>";
 		}
 		$limitline .= ($limstart+1) . "-";
 		if($limstart+$limiter < $count) $limitline .= ($limstart+$limiter);
 		else $limitline .= $count;
 		$limitline .= "</span> Displayed of $count";
 		$limitline .= " ($firstlink $prevlink $middlelinks $nextlink $lastlink)";
 	}
 	else {
 		if($count>=1) $limitline .= "1-$count";
 		else  $limitline .= "0";
 		$limitline .= "</span> Displayed of $count";
 	}
 	//$limitline .= "</td></tr></table>";
 	//	if($count > 0 ) $ret.= "<tr><td>$limitline</td></tr>";
 	$limitarray=array();
 	$limitarray[0]=$limittxt;
 	$limitarray[1]=$limitline;
 	return  $limitarray;
 }
?>