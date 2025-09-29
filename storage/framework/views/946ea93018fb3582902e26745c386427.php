<?php $__env->startSection('head_title', trans('words.payment_method').' | '.getcong('site_name') ); ?>

<?php $__env->startSection('head_url', Request::url()); ?>

<?php $__env->startSection('content'); ?>

<style type="text/css">
#loading {
    background: url('<?php echo e(URL::asset('site_assets/images/LoaderIcon.gif')); ?>') no-repeat center center;
    position: absolute;
    top: 0;
    left: 0;
    height: 100%;
    width: 100%;
    z-index: 9999999;
    opacity: 1;
}
.payment_loading{
  opacity: 0.5;
}
</style>

<div id="loading" style="display: none;"></div>


<!-- Start Breadcrumb -->
<div class="breadcrumb-section bg-xs" style="background-image: url('<?php echo e(URL::asset('site_assets/images/breadcrum-bg.jpg')); ?>')">
    <div class="container-fluid">
      <div class="row">

        <div class="col-xl-12">

        <h2><?php echo e(trans('words.payment_method')); ?></h2>
        <nav id="breadcrumbs">
            <ul>
              <li><a href="<?php echo e(URL::to('/')); ?>" title="<?php echo e(trans('words.home')); ?>"><?php echo e(trans('words.home')); ?></a></li>
              <li><a href="<?php echo e(URL::to('/dashboard')); ?>" title="<?php echo e(trans('words.dashboard_text')); ?>"><?php echo e(trans('words.dashboard_text')); ?></a></li>
              <li><?php echo e(trans('words.payment_method')); ?></li>
            </ul>
          </nav>
     </div>
      </div>
    </div>
  </div>
<!-- End Breadcrumb -->

<!-- Start Payment Method -->
<div class="vfx-item-ptb vfx-item-info pb-3">
  <div class="container-fluid">
   <div class="row">

    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

       <?php if(Session::has('flash_message')): ?>
          <div class="alert alert-success">

              <?php echo e(Session::get('flash_message')); ?>

          </div>
        <?php endif; ?>


    <?php if(Session::has('error_flash_message')): ?>
      <div class="alert alert-danger">

          <?php echo e(Session::get('error_flash_message')); ?>

      </div>
    <?php endif; ?>
    <?php if($message = Session::get('error')): ?>
    <div class="custom-alerts alert alert-danger fade in">

        <?php echo $message; ?>

    </div>
    <?php Session::forget('error');?>
    <?php endif; ?>

      <div class="payment-details-area">
        <h3><?php echo e(trans('words.payment_method')); ?></h3>
        <div class="select-plan-text"><?php echo e(trans('words.you_are_buying')); ?><span><?php echo e($video_info->video_title); ?></span></div>
        <p><?php echo e(trans('words.you_are_logged')); ?> <a href="#" title="user_email"><?php echo e(Auth::User()->email); ?></a> <?php echo e(trans('words.if_you_would_like')); ?> <?php echo e(trans('words.different_account_subscription')); ?>, <a href="<?php echo e(URL::to('logout')); ?>" title="logout"><?php echo e(trans('words.logout')); ?></a> <?php echo e(trans('words.now')); ?>.</p>
        <div class="mt-3"><a href="<?php echo e(URL::to('membership_plan')); ?>" class="vfx-item-btn-danger text-uppercase"><?php echo e(trans('words.change_plan')); ?></a></div>
      </div>
    </div>
   </div>
     <div class="row membership_plan_block">
    <?php if(Auth::User()->phone!=''): ?>

      <?php if(getPaymentGatewayInfo(1)->status): ?>
        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
            <div class="select-payment-method">
            <h1><?php echo e(getPaymentGatewayInfo(1)->gateway_name); ?></h1>
            <h4><?php echo e(getPaymentGatewayInfo(1)->gateway_short_info); ?></h4>
               <?php echo Form::open(array('url' => 'paypal/pay','class'=>'','id'=>'','role'=>'form','method'=>'POST')); ?>

                 <input id="video_id" type="hidden" class="form-control" name="video_id" value="<?php echo e($video_info->id); ?>">
                <button type="submit" class="vfx-item-btn-danger text-uppercase"><?php echo e(trans('words.pay_now')); ?>(<?php echo e($license_price); ?>)</button>
                <?php echo Form::close(); ?>

            </div>
        </div>
      <?php endif; ?>

    <?php if(getPaymentGatewayInfo(2)->status): ?>
      <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
          <div class="select-payment-method">
          <h1><?php echo e(getPaymentGatewayInfo(2)->gateway_name); ?></h1>
          <h4><?php echo e(getPaymentGatewayInfo(2)->gateway_short_info); ?></h4>
          <a href="<?php echo e(URL::to('stripe/pay')); ?>" class="vfx-item-btn-danger text-uppercase" title="<?php echo e(trans('words.pay_now')); ?>"><?php echo e(trans('words.pay_now')); ?></a>
          <!-- <button type="button" class="vfx-item-btn-danger text-uppercase" data-bs-toggle="modal" data-bs-target="#stripeModal"><?php echo e(trans('words.pay_now')); ?></button> -->
          </div>
      </div>
    <?php endif; ?>
    <?php if(getPaymentGatewayInfo(3)->status): ?>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
        <div class="select-payment-method">
        <h1><?php echo e(getPaymentGatewayInfo(3)->gateway_name); ?></h1>
        <h4><?php echo e(getPaymentGatewayInfo(3)->gateway_short_info); ?></h4>

        <button type="button" id="razorpayId" class="vfx-item-btn-danger text-uppercase" data-bs-toggle="modal"><?php echo e(trans('words.pay_now')); ?></button>

        </div>
    </div>
    <?php endif; ?>
    <?php if(getPaymentGatewayInfo(4)->status): ?>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
        <div class="select-payment-method">
          <h1><?php echo e(getPaymentGatewayInfo(4)->gateway_name); ?></h1>
          <h4><?php echo e(getPaymentGatewayInfo(4)->gateway_short_info); ?></h4>
          <?php echo Form::open(array('url' => 'pay','class'=>'','id'=>'','role'=>'form','method'=>'POST')); ?>

          <input type="hidden" name="amount" value="<?php echo e(number_format($video_info->plan_price - $discount_price_less,2)); ?>">

          <button type="submit" class="vfx-item-btn-danger text-uppercase"><?php echo e(trans('words.pay_now')); ?></button>
          <?php echo Form::close(); ?>

        </div>
    </div>
    <?php endif; ?>

    <?php if(getPaymentGatewayInfo(5)->status): ?>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
        <div class="select-payment-method">
          <h1><?php echo e(getPaymentGatewayInfo(5)->gateway_name); ?></h1>
          <h4><?php echo e(getPaymentGatewayInfo(5)->gateway_short_info); ?></h4>
          <?php echo Form::open(array('url' => 'instamojo/pay','class'=>'','id'=>'','role'=>'form','method'=>'POST')); ?>


          <button type="submit" class="vfx-item-btn-danger text-uppercase"><?php echo e(trans('words.pay_now')); ?></button>
          <?php echo Form::close(); ?>

        </div>
    </div>
    <?php endif; ?>

    <?php if(getPaymentGatewayInfo(6)->status): ?>

    <?php
    $payu_mode=getPaymentGatewayInfo(6,'mode');

    $key=getPaymentGatewayInfo(6,'payu_key'); //posted merchant key from client
    $salt=getPaymentGatewayInfo(6,'payu_salt'); // add salt here from your credentials in payUMoney dashboard
    $txnId=substr(hash('sha256', mt_rand() . microtime()), 0, 20); //posted txnid from client
    $amount=number_format($video_info->plan_price - $discount_price_less,2);
    $productName=$video_info->plan_name;
    $firstName=Auth::User()->name;
    $email=Auth::User()->email;


    /***************** USER DEFINED VARIABLES GOES HERE ***********************/
    //all varibles posted from client
    $udf1="";
    $udf2="";
    $udf3="";
    $udf4="";
    $udf5="";

    /***************** DO NOT EDIT ***********************/
    $payhash_str = $key . '|' . $txnId . '|' .$amount  . '|' .$productName  . '|' . $firstName . '|' . $email . '|' . $udf1 . '|' . $udf2 . '|' . $udf3 . '|' . $udf4 . '|' . $udf5 . '||||||'. $salt;


    $hash = strtolower(hash('sha512', $payhash_str));
    /***************** DO NOT EDIT ***********************/

    if($payu_mode=="live")
    {
      $payu_url="https://secure.payu.in/_payment";
    }
    else
    {
      $payu_url="https://test.payu.in/_payment";
    }

    ?>

    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
        <div class="select-payment-method">
          <h1><?php echo e(getPaymentGatewayInfo(6)->gateway_name); ?></h1>
          <h4><?php echo e(getPaymentGatewayInfo(6)->gateway_short_info); ?></h4>
          <?php echo Form::open(array('url' => $payu_url,'class'=>'','id'=>'payu_form','role'=>'form','method'=>'POST')); ?>


          <input type="hidden" name="key" value="<?php echo $key;?>" />
          <input type="hidden" name="txnid" value="<?php echo $txnId;?>" />
          <input type="hidden" name="productinfo" value="<?php echo $productName;?>" />
          <input type="hidden" name="amount" value="<?php echo $amount;?>" />
          <input type="hidden" name="email" value="<?php echo $email;?>" />
          <input type="hidden" name="firstname" value="<?php echo $firstName;?>" />

          <input type="hidden" name="udf1" value="" />
          <input type="hidden" name="udf2" value="" />
          <input type="hidden" name="udf3" value="" />
          <input type="hidden" name="udf4" value="" />
          <input type="hidden" name="udf5" value="" />

          <input type="hidden" name="surl" value="<?php echo e(\URL::to('payu_success/')); ?>" />
          <input type="hidden" name="furl" value="<?php echo e(\URL::to('payu_fail/')); ?>" />
          <input type="hidden" name="phone" value="<?php echo e(Auth::User()->phone); ?>" />
          <input type="hidden" name="hash" value="<?php echo $hash;?>"/>


          <button type="submit" class="vfx-item-btn-danger text-uppercase"><?php echo e(trans('words.pay_now')); ?></button>
          <?php echo Form::close(); ?>

        </div>
    </div>
    <?php endif; ?>


    <?php if(getPaymentGatewayInfo(7)->status): ?>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
        <div class="select-payment-method">
          <h1><?php echo e(getPaymentGatewayInfo(7)->gateway_name); ?></h1>
          <h4><?php echo e(getPaymentGatewayInfo(7)->gateway_short_info); ?></h4>
          <?php echo Form::open(array('url' => 'mollie/pay','class'=>'','id'=>'','role'=>'form','method'=>'POST')); ?>


          <button type="submit" class="vfx-item-btn-danger text-uppercase"><?php echo e(trans('words.pay_now')); ?></button>
          <?php echo Form::close(); ?>

        </div>
    </div>
    <?php endif; ?>

    <?php if(getPaymentGatewayInfo(8)->status): ?>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
        <div class="select-payment-method">
          <h1><?php echo e(getPaymentGatewayInfo(8)->gateway_name); ?></h1>
          <h4><?php echo e(getPaymentGatewayInfo(8)->gateway_short_info); ?></h4>
          <?php echo Form::open(array('url' => 'flutterwave/pay','class'=>'','id'=>'','role'=>'form','method'=>'POST')); ?>


          <button type="submit" class="vfx-item-btn-danger text-uppercase"><?php echo e(trans('words.pay_now')); ?></button>
          <?php echo Form::close(); ?>

        </div>
    </div>
    <?php endif; ?>

    <?php if(getPaymentGatewayInfo(9)->status): ?>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
        <div class="select-payment-method">
          <h1><?php echo e(getPaymentGatewayInfo(9)->gateway_name); ?></h1>
          <h4><?php echo e(getPaymentGatewayInfo(9)->gateway_short_info); ?></h4>

          <a href="<?php echo e(URL::to('paytm/pay')); ?>" class="vfx-item-btn-danger text-uppercase" title="<?php echo e(trans('words.pay_now')); ?>"><?php echo e(trans('words.pay_now')); ?></a>

        </div>
    </div>
    <?php endif; ?>

    <?php if(getPaymentGatewayInfo(10)->status): ?>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
        <div class="select-payment-method">
          <h1><?php echo e(getPaymentGatewayInfo(10)->gateway_name); ?></h1>
          <h4><?php echo e(getPaymentGatewayInfo(10)->gateway_short_info); ?></h4>

          <a href="#" id="cashfree_pay" class="vfx-item-btn-danger text-uppercase" title="<?php echo e(trans('words.pay_now')); ?>"><?php echo e(trans('words.pay_now')); ?></a>

        </div>
    </div>
    <?php endif; ?>

    <?php if(getPaymentGatewayInfo(11)->status): ?>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
        <div class="select-payment-method">
          <h1><?php echo e(getPaymentGatewayInfo(11)->gateway_name); ?></h1>
          <h4><?php echo e(getPaymentGatewayInfo(11)->gateway_short_info); ?></h4>

          <a href="<?php echo e(URL::to('coingate/pay')); ?>" class="vfx-item-btn-danger text-uppercase" title="<?php echo e(trans('words.pay_now')); ?>"><?php echo e(trans('words.pay_now')); ?></a>

        </div>
    </div>
    <?php endif; ?>

    <?php if(getPaymentGatewayInfo(12)->status): ?>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
        <div class="select-payment-method">
          <h1><?php echo e(getPaymentGatewayInfo(12)->gateway_name); ?></h1>
          <h4><?php echo e(getPaymentGatewayInfo(12)->gateway_short_info); ?></h4>

          <a href="Javascript:void(0);" data-bs-toggle="modal" data-bs-target="#bank_transfer_info" class="vfx-item-btn-danger text-uppercase" title="<?php echo e(trans('words.pay_now')); ?>"><?php echo e(trans('words.pay_now')); ?></a>

        </div>
    </div>
    <?php endif; ?>


  <?php else: ?>
    <?php if(getPaymentGatewayInfo(1)->status): ?>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
        <div class="select-payment-method">
        <h1><?php echo e(getPaymentGatewayInfo(1)->gateway_name); ?></h1>
        <h4><?php echo e(getPaymentGatewayInfo(1)->gateway_short_info); ?></h4>

        <a href="Javascript:void(0);" data-bs-toggle="modal" data-bs-target="#phone_update" class="vfx-item-btn-danger text-uppercase" title="<?php echo e(trans('words.pay_now')); ?>"><?php echo e(trans('words.pay_now')); ?></a>

        </div>
    </div>
    <?php endif; ?>

    <?php if(getPaymentGatewayInfo(2)->status): ?>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
        <div class="select-payment-method">
        <h1><?php echo e(getPaymentGatewayInfo(2)->gateway_name); ?></h1>
        <h4><?php echo e(getPaymentGatewayInfo(2)->gateway_short_info); ?></h4>

        <a href="Javascript:void(0);" data-bs-toggle="modal" data-bs-target="#phone_update" class="vfx-item-btn-danger text-uppercase" title="<?php echo e(trans('words.pay_now')); ?>"><?php echo e(trans('words.pay_now')); ?></a>

        </div>
    </div>
    <?php endif; ?>

    <?php if(getPaymentGatewayInfo(3)->status): ?>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
        <div class="select-payment-method">
        <h1><?php echo e(getPaymentGatewayInfo(3)->gateway_name); ?></h1>
        <h4><?php echo e(getPaymentGatewayInfo(3)->gateway_short_info); ?></h4>

        <a href="Javascript:void(0);" data-bs-toggle="modal" data-bs-target="#phone_update" class="vfx-item-btn-danger text-uppercase" title="<?php echo e(trans('words.pay_now')); ?>"><?php echo e(trans('words.pay_now')); ?></a>

        </div>
    </div>
    <?php endif; ?>

    <?php if(getPaymentGatewayInfo(4)->status): ?>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
        <div class="select-payment-method">
        <h1><?php echo e(getPaymentGatewayInfo(4)->gateway_name); ?></h1>
        <h4><?php echo e(getPaymentGatewayInfo(4)->gateway_short_info); ?></h4>

        <a href="Javascript:void(0);" data-bs-toggle="modal" data-bs-target="#phone_update" class="vfx-item-btn-danger text-uppercase" title="<?php echo e(trans('words.pay_now')); ?>"><?php echo e(trans('words.pay_now')); ?></a>

        </div>
    </div>
    <?php endif; ?>

    <?php if(getPaymentGatewayInfo(5)->status): ?>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
        <div class="select-payment-method">
        <h1><?php echo e(getPaymentGatewayInfo(5)->gateway_name); ?></h1>
        <h4><?php echo e(getPaymentGatewayInfo(5)->gateway_short_info); ?></h4>

        <a href="Javascript:void(0);" data-bs-toggle="modal" data-bs-target="#phone_update" class="vfx-item-btn-danger text-uppercase" title="<?php echo e(trans('words.pay_now')); ?>"><?php echo e(trans('words.pay_now')); ?></a>

        </div>
    </div>
    <?php endif; ?>

    <?php if(getPaymentGatewayInfo(6)->status): ?>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
        <div class="select-payment-method">
        <h1><?php echo e(getPaymentGatewayInfo(6)->gateway_name); ?></h1>
        <h4><?php echo e(getPaymentGatewayInfo(6)->gateway_short_info); ?></h4>

        <a href="Javascript:void(0);" data-bs-toggle="modal" data-bs-target="#phone_update" class="vfx-item-btn-danger text-uppercase" title="<?php echo e(trans('words.pay_now')); ?>"><?php echo e(trans('words.pay_now')); ?></a>

        </div>
    </div>
    <?php endif; ?>

    <?php if(getPaymentGatewayInfo(7)->status): ?>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
        <div class="select-payment-method">
        <h1><?php echo e(getPaymentGatewayInfo(7)->gateway_name); ?></h1>
        <h4><?php echo e(getPaymentGatewayInfo(7)->gateway_short_info); ?></h4>

        <a href="Javascript:void(0);" data-bs-toggle="modal" data-bs-target="#phone_update" class="vfx-item-btn-danger text-uppercase" title="<?php echo e(trans('words.pay_now')); ?>"><?php echo e(trans('words.pay_now')); ?></a>

        </div>
    </div>
    <?php endif; ?>

    <?php if(getPaymentGatewayInfo(8)->status): ?>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
        <div class="select-payment-method">
        <h1><?php echo e(getPaymentGatewayInfo(8)->gateway_name); ?></h1>
        <h4><?php echo e(getPaymentGatewayInfo(8)->gateway_short_info); ?></h4>

        <a href="Javascript:void(0);" data-bs-toggle="modal" data-bs-target="#phone_update" class="vfx-item-btn-danger text-uppercase" title="<?php echo e(trans('words.pay_now')); ?>"><?php echo e(trans('words.pay_now')); ?></a>

        </div>
    </div>
    <?php endif; ?>

    <?php if(getPaymentGatewayInfo(9)->status): ?>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
        <div class="select-payment-method">
        <h1><?php echo e(getPaymentGatewayInfo(9)->gateway_name); ?></h1>
        <h4><?php echo e(getPaymentGatewayInfo(9)->gateway_short_info); ?></h4>

        <a href="Javascript:void(0);" data-bs-toggle="modal" data-bs-target="#phone_update" class="vfx-item-btn-danger text-uppercase" title="<?php echo e(trans('words.pay_now')); ?>"><?php echo e(trans('words.pay_now')); ?></a>

        </div>
    </div>
    <?php endif; ?>

    <?php if(getPaymentGatewayInfo(10)->status): ?>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
        <div class="select-payment-method">
        <h1><?php echo e(getPaymentGatewayInfo(10)->gateway_name); ?></h1>
        <h4><?php echo e(getPaymentGatewayInfo(10)->gateway_short_info); ?></h4>

        <a href="Javascript:void(0);" data-bs-toggle="modal" data-bs-target="#phone_update" class="vfx-item-btn-danger text-uppercase" title="<?php echo e(trans('words.pay_now')); ?>"><?php echo e(trans('words.pay_now')); ?></a>

        </div>
    </div>
    <?php endif; ?>

    <?php if(getPaymentGatewayInfo(11)->status): ?>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
        <div class="select-payment-method">
          <h1><?php echo e(getPaymentGatewayInfo(11)->gateway_name); ?></h1>
          <h4><?php echo e(getPaymentGatewayInfo(11)->gateway_short_info); ?></h4>

          <a href="Javascript:void(0);" data-bs-toggle="modal" data-bs-target="#phone_update" class="vfx-item-btn-danger text-uppercase" title="<?php echo e(trans('words.pay_now')); ?>"><?php echo e(trans('words.pay_now')); ?></a>

        </div>
    </div>
    <?php endif; ?>

    <?php if(getPaymentGatewayInfo(12)->status): ?>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
        <div class="select-payment-method">
          <h1><?php echo e(getPaymentGatewayInfo(12)->gateway_name); ?></h1>
          <h4><?php echo e(getPaymentGatewayInfo(12)->gateway_short_info); ?></h4>

          <a href="Javascript:void(0);" data-bs-toggle="modal" data-bs-target="#bank_transfer_info" class="vfx-item-btn-danger text-uppercase" title="<?php echo e(trans('words.pay_now')); ?>"><?php echo e(trans('words.pay_now')); ?></a>

        </div>
    </div>
    <?php endif; ?>



  <?php endif; ?>

  </div>
  </div>
</div>
<!-- End Payment Method -->


<?php if(getPaymentGatewayInfo(12)->status): ?>
<div id="bank_transfer_info" class="modal fade stripe-payment-block" role="dialog" aria-labelledby="bank_transfer_info" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">

        <div class="modal-content">
        <div class="modal-header">
           <h4 class="modal-title"><?php echo e(trans('words.bank_transfer_info')); ?></h4>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="close_trailer_pop"><i class="fa fa-times"></i></button>
        </div>
        <div class="modal-body">
            <div class="edit-profile-form">
               <?php echo json_decode(getPaymentGatewayInfo(12)->gateway_info)->banktransfer_info; ?>

            </div>

        </div>

      </div>

    </div>

  </div>
<?php endif; ?>


  <div id="phone_update" class="modal fade stripe-payment-block" role="dialog" aria-labelledby="phone_update" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">

        <div class="modal-content">
        <div class="modal-header">
           <h4 class="modal-title"><?php echo e(trans('words.update')); ?> <?php echo e(trans('words.phone')); ?></h4>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="close_trailer_pop"><i class="fa fa-times"></i></button>
        </div>
        <div class="modal-body">
            <div class="edit-profile-form">
              <?php if(count($errors) > 0): ?>
                <div class="alert alert-danger">
                     <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <ul>
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
                <?php endif; ?>
                <?php if(Session::has('flash_message')): ?>
                      <div class="alert alert-success">
                      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                          <?php echo e(Session::get('flash_message')); ?>

                      </div>
                <?php endif; ?>

            <?php echo Form::open(array('url' => 'phone_update','class'=>'row"','name'=>'profile_form','id'=>'user_form','role'=>'form','enctype' => 'multipart/form-data')); ?>

              <input name="" value="" type="hidden">

              <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="form-group mb-4">
                  <label><?php echo e(trans('words.phone')); ?></label>
                  <input type="number" name="phone" id="phone" value="" class="form-control" placeholder="" required>
                </div>
              </div>

              <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="form-group d-flex align-items-end flex-column mt-30">
                  <button type="submit" class="vfx-item-btn-danger text-uppercase"><?php echo e(trans('words.update')); ?></button>
                </div>
              </div>

            <?php echo Form::close(); ?>


          </div>

        </div>

      </div>

    </div>

  </div>

<script src="<?php echo e(URL::asset('site_assets/js/jquery-3.3.1.min.js')); ?>"></script>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>


<script type="text/javascript">
  $("#razorpayId").click(function(e) {
    e.preventDefault();

    $('.vfx-item-ptb').addClass('payment_loading');
    $("#loading").show();

    $.ajax({
        type: "POST",
        url: "<?php echo e(URL::to('razorpay_get_order_id')); ?>",
        data: {
            id: $(this).val(), // < note use of 'this' here
            _token: "<?php echo e(csrf_token()); ?>"
        },
        success: function(result) {
            //$('#paymentWidget').attr("data-order_id",'111');

            //alert(result);
            $('.vfx-item-ptb').removeClass('payment_loading');
            $("#loading").hide();

            var options = {
                      "key": "<?php echo e(getcong('razorpay_key')); ?>", // Enter the Key ID generated from the Dashboard
                      "amount": "<?php echo e($video_info->plan_price); ?>", // Amount is in currency subunits. Default currency is INR. Hence, 50000 refers to 50000 paise
                      "currency": "INR",
                      "name": "<?php echo e(getcong('site_name')); ?>",
                      "description": "<?php echo e($video_info->plan_name); ?>",
                      "image": "<?php echo e(URL::asset('/'.getcong('site_logo'))); ?>",
                      "order_id": result, //This is a sample Order ID. Pass the `id` obtained in the response of Step 1
                      "callback_url": "<?php echo e(URL::to('razorpay-success')); ?>",
                      "prefill": {
                          "name": "<?php echo e(Auth::user()->name); ?>",
                          "email": "<?php echo e(Auth::user()->email); ?>",
                          "contact": "<?php echo e(Auth::user()->phone); ?>"
                      },
                      "theme": {
                          "color": "#3399cc"
                      }
                  };

            var rzp1 = new Razorpay(options);

            rzp1.open();

            //alert(result);
        },
        error: function(result) {
            alert('error');
        }
    });
});
</script>

<?php if(getPaymentGatewayInfo(10,'mode')=="sandbox"): ?>
<script src="https://sdk.cashfree.com/js/ui/2.0.0/cashfree.sandbox.js"></script>
<?php else: ?>
<script src="https://sdk.cashfree.com/js/ui/2.0.0/cashfree.prod.js"></script>
<?php endif; ?>

<script type="text/javascript">
  $("#cashfree_pay").click(function(e) {
    e.preventDefault();

    $('.membership_plan_block').addClass('payment_loading');
    $("#loading").show();

    $.ajax({
        type: "POST",
        url: "<?php echo e(URL::to('cashfree/get_cashfree_session_id')); ?>",
        data: {
            id: $(this).val(), // < note use of 'this' here
            _token: "<?php echo e(csrf_token()); ?>"
        },
        success: function(result) {

            //alert(result);
            $('.membership_plan_block').removeClass('payment_loading');
            $("#loading").hide();

            const cf = new Cashfree(result);
            cf.redirect();

         },
        error: function(result) {
            alert('error');
        }
    });
});
</script>

<script type="text/javascript">

 $('#open_phone_update').on('click', function(e) {
    $('#phone_update').modal('show');
 });

</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('site_app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\stock_new\resources\views/pages/payment/payment_method.blade.php ENDPATH**/ ?>