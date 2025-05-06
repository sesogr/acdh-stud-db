<!DOCTYPE html>
<!--[if IE 8 ]><html class="ie ie8" lang="en"> <![endif]-->
<!--[if IE 9 ]><html class="ie ie9" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!-->
<html lang="en"> <!--<![endif]-->
<head>
  
  <meta charset="utf-8"/>  
  <title>DEMOS &ndash; Datenbank zur Erforschung der Musik in &Ouml;sterreich &ndash;</title>
  <meta name="Description"  content="DEMOS &ndash; Datenbank zur Erforschung der Musik in &Ouml;sterreich, &Ouml;sterreichische Akademie der Wissenschaften, Wien, Austria" />
  <meta name="Keywords" content="DEMOS &ndash; Datenbank zur Erforschung der Musik in &Ouml;sterreich, &Ouml;sterreichische Akademie der Wissenschaften, Wien, Austria" />
  <meta name="author" content="Österreichische Akademie der Wissenschaften">

  <!-- Mobile Specific Metas -->
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">   

  <!-- Main Style -->
  <link rel="stylesheet" href="/css/style.css"> 
  
  <!-- Color Style -->
  <link rel="stylesheet" href="/css/skins/colors/red.css" name="colors">
  
  <!-- Layout Style -->
  <link rel="stylesheet" href="/css/layout/wide.css" name="layout">
  
  <!--[if lt IE 9]>
      <script src="../js/html5.js"></script>
  <![endif]-->
  
  <!-- Favicons -->
  <link rel="shortcut icon" href="/images/favicon.ico">  
  <link rel="stylesheet" href="/css/custom.css">
  <script src="/js/jquery-1.9.1.min.js"></script> <!-- jQuery library -->    
  <script src="/js/loader.js"></script>
  <script src="/js/showhidetoggle.js"></script>
</head>
<body>

  <div id="wrap" class="boxed">
  
    <!-- HEADER -->
    <header id="header-wrapper"></header>
    <!-- <<< End Header >>> -->   
   
   
   <!-- Start main content -->
   <div class="container main-content clearfix">     
        <div class="three-thirds bottom-3">   
        <h3>Studierende Musikwissenschaft Universität Wien</h3> 

        
                <?php
                    if (isset($_POST['action']) && $_POST['action'] === 'search' || !empty($_GET['token'])):
                        require_once __DIR__ . '/studenten/src/search.php';
                    elseif (isset($_GET['id'])):
                        require_once __DIR__ . '/studenten/src/show.php';
                    else:
                        require_once __DIR__ . '/studenten/src/form.php';
                    endif
                ?>
                     

        </div><!-- End Column-->       
   </div><!-- <<< End Container >>> -->
   
    <!-- FOOTER -->
    <footer id="footer-wrapper"></footer>
    <!-- <<< End Footer >>> -->
  
  </div><!-- End wrap -->
  
  <!-- Start JavaScript --> 
  <script src="/js/jquery.easing.1.3.min.js"></script> <!-- jQuery Easing --> 
  <script src="/js/jquery-ui/jquery.ui.core.js"></script> <!-- jQuery Ui Core-->
  <script src="/js/jquery-ui/jquery.ui.widget.js"></script> <!-- jQuery Ui Widget -->
  <script src="/js/jquery-ui/jquery.ui.accordion.js"></script> <!-- jQuery Ui accordion-->  
  <script src="/js/jquery-cookie.js"></script> <!-- jQuery cookie -->   
  <script src="/js/jquery.flexslider.js"></script> <!-- Flex Slider  -->
  <script src="/js/colortip.js"></script> <!-- Colortip Tooltip Plugin  -->
  <script src="/js/tytabs.js"></script> <!-- jQuery Plugin tytabs  -->
  <script src="/js/jquery.ui.totop.js"></script> <!-- UItoTop plugin  -->
  <script src="/js/carousel.js"></script> <!-- jQuery Carousel  -->
  <script src="/js/jquery.isotope.min.js"></script> <!-- Isotope Filtering  -->   
  <script src="/js/doubletaptogo.js"></script> <!-- Touch-friendly Script  -->
  <script src="/js/fancybox/jquery.fancybox.js"></script> <!-- jQuery FancyBox -->
  <script src="/js/jquery.sticky.js"></script> <!-- jQuery Sticky -->  <!-- End JavaScript -->
</body>
</html>