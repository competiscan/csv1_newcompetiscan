
					<li class="menu-item"><a href="https://demo1.competiscan.com/about-us/">About Us</a></li>
                                        <?php //$actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                                        //if(strstr($actual_link,'demo.competiscan.com')){ ?>
                                        <li class="menu-item"><a href="/services/">Services</a></li>
                                        <?php // }?>
					<li class="menu-item"><a href="https://demo1.competiscan.com/careers/">Careers</a></li>
					<li class="menu-item"><a href="https://demo1.competiscan.com/insights/">Articles</a></li>
					<li class="menu-item"><a href="https://demo1.competiscan.com/#contact">Contact Us</a></li>
<?php
if(empty($_SESSION['sess_username'])) { // not logged in
?>
					<li class="menu-item"><a href="/login.php">Login</a></li>
<?php }
else { 	// logged in ?>
					<li class="menu-item"><a href="/logout.php">Logout</a></li>	
<?php } ?>
 
                                         
<?php 
if(!defined('ENV')){
    define('ENV',getenv('SERVER_NAME'));
} 
//if(ENV == 'localhost' || ENV == 'demo.competiscan.com'){  
 if(!isset($_SESSION['sess_username'])){ ?>
<!-- Calendly badge widget begin -->
<link href="https://assets.calendly.com/assets/external/widget.css" rel="stylesheet">
<script src="https://assets.calendly.com/assets/external/widget.js" type="text/javascript"></script>
<script type="text/javascript">Calendly.initBadgeWidget({url: 'https://calendly.com/competiscan/competiscan-demo', text: 'Schedule a Demo', color: '#00a2ff', branding: false});</script>
<!-- Calendly badge widget end -->
<!-- for the VisualVisitor code -->
<script type="text/javascript"> 
var fesdpid = '9RjaQhnbyu'; 
var fesdp_BaseURL = (("https:" == document.location.protocol) ? "https://fe.sitedataprocessing.com/fewv1/" : "http://fe.sitedataprocessing.com/fewv1/");
(function () { 
var va = document.createElement('script'); 
va.type = 'text/javascript'; 
va.async = true; 
va.src = fesdp_BaseURL + 'Scripts/fewliveasync.js'; 
var sv = document.getElementsByTagName('script')[0]; 
sv.parentNode.insertBefore(va, sv); 
})(); 
</script> 
<!-- end for the VisualVisitor code -->

<?php }
 //}
?>
