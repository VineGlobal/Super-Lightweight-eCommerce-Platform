<?php      

/* ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
*/
include("main_oauth.php");

?>
<!doctype html>  
<html ng-app="YourStore">
  <head>
    <title><?php echo _l('store_title');?></title>
    <meta name="description" content="<?php echo _l('store_description');?>">
    <meta name="keywords" content="<?php echo _l('store_keywords');?>">
    <meta name="author" content="<?php echo _l('store_author');?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0"/>
    <!-- jQuery, Angular -->
    <script src="https://code.jquery.com/jquery-1.10.2.min.js" type="text/javascript"></script>
    
    <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.3.6/angular.min.js"></script>
    <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.3.6/angular-route.min.js"></script>
    <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.3.6/angular-animate.min.js"></script>
    <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.3.6/angular-aria.min.js"></script>
    <script src="js/ui-bootstrap-tpls-0.13.0.js"></script>          
     <link href='https://fonts.googleapis.com/css?family=Dosis:200,300,400,500,600,700,800' rel='stylesheet' type='text/css'/>
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,400,700,600,300' rel='stylesheet' type='text/css'>
 
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">


    <!-- AngularStore app -->
    <script src="js/list/modernizr.custom.js" type="text/javascript"></script>
    <script src="js/app" type="text/javascript"></script>
    <script src="js/store" type="text/javascript"></script>
    <script src="js/cart" type="text/javascript"></script> 
    <script src="js/controller" type="text/javascript"></script>
    
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">  
    <link href="css/component.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="css/style_cube.css"/>
  </head>
  <body>
            <div class="container">
              <div class="container-shadow">
                    <a class="logo" href="#/store">
                            <img src="<?php echo _m('store_logo');?>"  alt="logo" height="44px"/>
                        </a>
                    <h1 class="well">
                        <?php echo _l('store_title');?> 
                    </h1>
                    <div ng-view ></div>
                    <div class="content-footer">
                      <div class="col-md-12" style="background:#fff;padding:20px;text-align:center;">
                      		<p><?php echo _l('copyright');?> <?php echo date('Y'); ?> <?php echo _l('store_title');?> <?php echo _l('rights_reserved');?></p>
                      		<p><a href='#/customerService'><?php echo _l('customer_service');?></a></p>
                      </div>
                      		
                    </div>
               </div>
            </div> 
            <script>
			  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
			  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
			  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
			
			  ga('create', '<?php echo _m('google_analytics_tracking_code');?>', 'auto');
			  ga('send', 'pageview'); 
			</script>
            
            <div id="cookieAcceptBar" class="cookieAcceptBar">
            <b>Welcome to the LandedCost.io Demo Site.</b><br/> <br/>
            To view the landed cost calculation in action:<br/> 
            1. Click on any of the product images on the home page<br/>
            2. Click on the Add to Cart button<br/>
            3. Click on the Proceed to Cart button<br/>
            4. Select Ship From and Ship To countries on the cart page<br/>
            5. Click on the Calculate Landed Cost button<br/>
            6. Done. The landed cost calculation will appear.
             <br> <button id="cookieAcceptBarConfirm" class="btn btn-success">Close</button>
</div>
     <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.min.js" type="text/javascript"></script>        
	<script src="js/cookiepolicy.js" type="text/javascript"></script>		  
    
    <script src="js/validator.min.js"></script>
    <script src="js/contact-form.js"></script>
    
  </body>
</html>