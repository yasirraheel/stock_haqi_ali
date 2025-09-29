<!DOCTYPE html>
<html lang="<?php echo e(getcong('default_language')); ?>">
<head>
<meta name="theme-color" content="#ff0015">  
<meta http-equiv="Content-Type" content="text/html;charset=UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="author" content="">
<title><?php echo $__env->yieldContent('head_title', getcong('site_name')); ?></title>
<meta name="description" content="<?php echo $__env->yieldContent('head_description', getcong('site_description')); ?>" />
<meta name="keywords" content="<?php echo $__env->yieldContent('head_keywords', getcong('site_keywords')); ?>" />
<link rel="canonical" href="<?php echo $__env->yieldContent('head_url', url('/')); ?>">

<meta property="og:type" content="movie" />
<meta property="og:title" content="<?php echo $__env->yieldContent('head_title',  getcong('site_name')); ?>" />
<meta property="og:description" content="<?php echo $__env->yieldContent('head_description', getcong('site_description')); ?>" />
<meta property="og:image" content="<?php echo $__env->yieldContent('head_image', URL::asset('/'.getcong('site_logo'))); ?>" />
<meta property="og:url" content="<?php echo $__env->yieldContent('head_url', url('/')); ?>" />
<meta property="og:image:width" content="1024" />
<meta property="og:image:height" content="1024" />
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:image" content="<?php echo $__env->yieldContent('head_image', URL::asset('/'.getcong('site_logo'))); ?>">
<link rel="image_src" href="<?php echo $__env->yieldContent('head_image', URL::asset('/'.getcong('site_logo'))); ?>">

<!-- Favicon -->
<link rel="icon" href="<?php echo e(URL::asset('/'.getcong('site_favicon'))); ?>">

  
<!-- LOAD LOCAL CSS -->
<link rel="stylesheet" href="<?php echo e(URL::asset('site_assets/css/bootstrap.min.css')); ?>">
<link rel="stylesheet" href="<?php echo e(URL::asset('site_assets/css/owl.carousel.min.css')); ?>">
 
<link rel="stylesheet" href="<?php echo e(URL::asset('site_assets/css/megamenu.css')); ?>">
<link rel="stylesheet" href="<?php echo e(URL::asset('site_assets/css/ionicons.css')); ?>">
<link rel="stylesheet" href="<?php echo e(URL::asset('site_assets/css/font-awesome.min.css')); ?>">
 
   
<link rel="stylesheet" href="<?php echo e(URL::asset('site_assets/css/color-style/'.getcong('styling').'.css')); ?>" id="theme">

<link rel="stylesheet" href="<?php echo e(URL::asset('site_assets/css/responsive.css')); ?>">

<!-- Splide Slider CSS -->
<link rel="stylesheet" href="<?php echo e(URL::asset('site_assets/css/splide.min.css')); ?>">

<link rel="stylesheet" href="<?php echo e(URL::asset('site_assets/css/jquery-eu-cookie-law-popup.css')); ?>">

<!-- SweetAlert2 -->
<script src="<?php echo e(URL::asset('site_assets/js/sweetalert2@11.js')); ?>"></script>

<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css?family=Nunito:300,400,600,700,800&display=swap" rel="stylesheet">

<?php if(getcong('site_header_code')): ?>
    <?php echo stripslashes(getcong('site_header_code')); ?>

 <?php endif; ?>
 
 <?php if(getcong('styling')=="style-one"): ?>
 
    <?php $search_bg="#22134e";?>

 <?php elseif(getcong('styling')=="style-two"): ?>

    <?php $search_bg="#0d0620";?>

 <?php elseif(getcong('styling')=="style-three"): ?>

    <?php $search_bg="#0d071e";?>

<?php elseif(getcong('styling')=="style-four"): ?>

    <?php $search_bg="#0d0620";?>

<?php elseif(getcong('styling')=="style-five"): ?>

    <?php $search_bg="#0f0823";?>   

 <?php else: ?>

  <?php $search_bg="#000000";?>

 <?php endif; ?>
 
 <style type="text/css">
      .search .search-input input[type=text]::placeholder, .search .search-input input[type=text].focus {
          background: <?php echo e($search_bg); ?> !important; 
      }
 </style>

</head>
<body>
  

<?php if(!classActivePathSite('login') AND !classActivePathSite('signup') AND !classActivePathSite('password')): ?>

    <?php echo $__env->make("_particles.header", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?> 

<?php endif; ?>

    <?php echo $__env->yieldContent("content"); ?>   

<?php if(!classActivePathSite('login') AND !classActivePathSite('signup') AND !classActivePathSite('password')): ?>

    <?php echo $__env->make("_particles.footer", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php endif; ?>

<div id="popup1" class="popup-view popup-overlay">
  <div class="search">
    <div class="search-container has-results"><span class="title"><?php echo e(trans('words.search')); ?></span>
      <div class="search-input">
        <input type="text" name="s" id="search_box" class="search-container-input" placeholder="<?php echo e(trans('words.title')); ?>" onkeyup="showSuggestions(this.value)" style="background: <?php echo e($search_bg); ?>;">
      </div>
    </div>
    <div class="search-results mt-4" id="search_output">
        
 
    </div>
  </div>
  <a class="close" href="#" title="close"><i class="ion-close-round"></i></a>
</div>     
 
<div class="eupopup eupopup-bottom"></div>
 
  
  <!-- Load Local JS --> 
<script src="<?php echo e(URL::asset('site_assets/js/jquery-3.3.1.min.js')); ?>"></script> 
<script src="<?php echo e(URL::asset('site_assets/js/jquery.easing.min.js')); ?>"></script> 
<script src="<?php echo e(URL::asset('site_assets/js/bootstrap.min.js')); ?>"></script> 
<script src="<?php echo e(URL::asset('site_assets/js/owl.carousel.min.js')); ?>"></script>
<script src="<?php echo e(URL::asset('site_assets/js/jquery.nice-select.min.js')); ?>"></script>  
<script src="<?php echo e(URL::asset('site_assets/js/megamenu.js')); ?>"></script> 

 
<!-- Splide Slider JS -->
<script src="<?php echo e(URL::asset('site_assets/js/splide.min.js')); ?>"></script>

<!-- Custom Main JS -->
<script src="<?php echo e(URL::asset('site_assets/js/custom-main.js')); ?>"></script>


<script src="<?php echo e(URL::asset('site_assets/js/jquery-eu-cookie-law-popup.js')); ?>"></script> 

<script type="text/javascript">
  
<?php if(getcong('gdpr_cookie_on_off')): ?>
  $(document).ready( function() {
  if ($(".eupopup").length > 0) {
    $(document).euCookieLawPopup().init({
       'cookiePolicyUrl' : '<?php echo e(stripslashes(getcong('gdpr_cookie_url'))); ?>',
       'buttonContinueTitle' : '<?php echo e(trans('words.gdpr_continue')); ?>',
       'buttonLearnmoreTitle' : '<?php echo e(trans('words.gdpr_learn_more')); ?>',
       'popupPosition' : 'bottom',
       'colorStyle' : 'default',
       'compactStyle' : false,
       'popupTitle' : '<?php echo e(stripslashes(getcong('gdpr_cookie_title'))); ?>',
       'popupText' : '<?php echo e(stripslashes(getcong('gdpr_cookie_text'))); ?>'
    });
  }
});
<?php endif; ?>

function showSuggestions(inputString) {
  if(inputString.length <= 1){
    //document.getElementById('search_output').innerHTML = 'Search field empty!';
    document.getElementById('search_output').innerHTML = '';
  }else{
    $.ajax({
      url: "<?php echo e(URL::to('search_elastic')); ?>",
      method:"GET",
      data: { 's' : inputString},
      dataType:'text',
      beforeSend: function(){
      $("#search_box").css("background","<?php echo e($search_bg); ?> url(<?php echo e(URL::asset('site_assets/images/LoaderIcon.gif')); ?>) no-repeat 100%");
      },
      success: function(result){
        //alert(result);
          //$("#search_output").html = result;
          $("#search_output").html(result);
          $("#search_box").css("background","<?php echo e($search_bg); ?>");
        }
    });
  }
}  

 
</script>

<script type="text/javascript">
  
  $("li[data-path]").click(function() {
 
    $("head link#theme").attr("href", $(this).data("path"));
});

</script>

<?php if(Auth::check()): ?>

<?php if(Auth::user()->usertype!="Admin" AND Auth::user()->usertype!="Sub_Admin"): ?>
  <?php if(user_device_limit_reached(Auth::user()->id,Auth::user()->plan_id)): ?>
  <script type="text/javascript">
       //alert(<?php echo e(Auth::user()->id); ?>);
    $(document).ready( function() {
      $('#user_device_list').modal('show');
 
    });
  </script>
  <?php endif; ?>
<?php endif; ?>

<?php if(Auth::user()->usertype!="Admin" AND Auth::user()->usertype!="Sub_Admin"): ?> 
 
<script type="text/javascript">
  
  function executeQuery() {
  $.ajax({
    url: "<?php echo e(url('check_user_remotely_logout_or_not/'.Session::getId())); ?>",
    success: function(data) {
      
      if(data=="false")
      {
         jQuery('#logout_remotly').modal('show');

         var timer = setTimeout(function() {
                  window.location="<?php echo e(URL::to('/')); ?>"
              }, 5000);
      }
       
    }
  });
  setTimeout(executeQuery, 10000); // you could choose not to continue on failure...
}

$(document).ready(function() {
  // run the first time; all subsequent calls will take care of themselves
  setTimeout(executeQuery, 10000);
});

</script>
 
<?php endif; ?>


<?php endif; ?>
   

<?php if(getcong('site_footer_code')): ?>
    <?php echo stripslashes(getcong('site_footer_code')); ?>

<?php endif; ?>

 

</body>
</html><?php /**PATH /home/u273790872/domains/cineworm.org/public_html/stock/resources/views/site_app.blade.php ENDPATH**/ ?>