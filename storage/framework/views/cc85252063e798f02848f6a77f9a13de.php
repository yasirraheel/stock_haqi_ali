

<?php $__env->startSection('content'); ?>
    <div class="content-page">
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card-box">
                            <?php echo Form::open([
                                'url' => ['admin/web_ads_settings'],
                                'class' => 'form-horizontal',
                                'name' => 'settings_form',
                                'id' => 'settings_form',
                                'role' => 'form',
                                'enctype' => 'multipart/form-data',
                            ]); ?>


                            <input type="hidden" name="id" value="<?php echo e(isset($settings->id) ? $settings->id : null); ?>">

                            <h5 class="mb-4" style="color:#f9f9f9"><i class="fa fa-buysellads pr-2"></i> <b>Banner Ads</b></h5>

                            <div class="alert alert-info"><b>Note:</b> Leave empty if not want to display</div>

                            <!-- Home Top Section -->
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Home</label>
                                <div class="col-sm-8">
                                    <div class="form-group row">
                                        <div class="col-sm-8">
                                            <div class="input-group">
                                                <input type="text" name="Home_top" id="Home_top"
                                                       value="<?php echo e(isset($movie->Home_top) ? $movie->Home_top : null); ?>"
                                                       class="form-control" readonly>
                                                <div class="input-group-append">
                                                    <button type="button"
                                                            class="btn btn-dark waves-effect waves-light popup_selector"
                                                            data-input="Home_top" data-preview="holder_thumb"
                                                            data-inputid="Home_top">Select
                                                    </button>
                                                </div>
                                            </div>
                                            <small id="emailHelp" class="form-text text-muted">
                                                (<?php echo e(trans('words.recommended_resolution')); ?> : 728x90)
                                            </small>
                                            <div class="vid-item-ptb banner_ads_item">
                                                <div class="container-fluid">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <?php echo stripslashes(get_web_banner('home_top')); ?>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="home_top_holder" style="margin-top:5px;max-height:100px;"></div>
                                        </div>
                                        <div class="col-sm-8">
                                            <div class="input-group">
                                                <input type="text" name="ad_url" id="ad_url_top"
                                                       value="<?php echo e(isset($movie->ad_url) ? $movie->ad_url : null); ?>"
                                                       class="form-control">
                                            </div>
                                            <small id="emailHelp" class="form-text text-muted">Enter ad URL</small>
                                        </div>
                                    </div>

                                    <textarea name="home_top_text" id="home_top_text" class="form-control">
                                        <?php echo e(isset($settings->home_top) ? stripslashes($settings->home_top) : ''); ?>

                                    </textarea>
                                </div>
                            </div>

                            <!-- Home Bottom Section -->
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Movie List</label>
                                <div class="col-sm-8">
                                    <div class="form-group row">
                                        <div class="col-sm-8">
                                            <div class="input-group">
                                                <input type="text" name="home_bottom" id="home_bottom"
                                                       value="<?php echo e(isset($movie->home_bottom) ? $movie->home_bottom : null); ?>"
                                                       class="form-control" readonly>
                                                <div class="input-group-append">
                                                    <button type="button"
                                                            class="btn btn-dark waves-effect waves-light popup_selector"
                                                            data-input="home_bottom" data-preview="home_bottom_holder"
                                                            data-inputid="home_bottom">Select
                                                    </button>
                                                </div>
                                            </div>
                                            <small id="emailHelp" class="form-text text-muted">
                                                (<?php echo e(trans('words.recommended_resolution')); ?> : 728x90)
                                            </small>
                                            <div class="vid-item-ptb banner_ads_item">
                                                <div class="container-fluid">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <?php echo stripslashes(get_web_banner('list_top')); ?>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="home_bottom_holder" style="margin-top:5px;max-height:100px;"></div>
                                        </div>
                                        <div class="col-sm-8">
                                            <div class="input-group">
                                                <input type="text" name="ad_url_bottom" id="ad_url_bottom"
                                                       value="<?php echo e(isset($movie->ad_url_bottom) ? $movie->ad_url_bottom : null); ?>"
                                                       class="form-control">
                                            </div>
                                            <small id="emailHelp" class="form-text text-muted">Enter ad URL</small>
                                        </div>
                                    </div>

                                    <textarea name="home_bottom_text" id="home_bottom_text" class="form-control">
                                        <?php echo e(isset($settings->home_bottom) ? stripslashes($settings->home_bottom) : ''); ?>

                                    </textarea>
                                </div>
                            </div>

                            <!-- Movie Detail Section -->
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Movie Detail</label>
                                <div class="col-sm-8">
                                    <div class="form-group row">
                                        <div class="col-sm-8">
                                            <div class="input-group">
                                                <input type="text" name="list_top" id="list_top"
                                                       value="<?php echo e(isset($movie->list_top) ? $movie->list_top : null); ?>"
                                                       class="form-control" readonly>
                                                <div class="input-group-append">
                                                    <button type="button"
                                                            class="btn btn-dark waves-effect waves-light popup_selector"
                                                            data-input="list_top" data-preview="list_top_holder"
                                                            data-inputid="list_top">Select
                                                    </button>
                                                </div>
                                            </div>
                                            <small id="emailHelp" class="form-text text-muted">
                                                (<?php echo e(trans('words.recommended_resolution')); ?> : 728x90)
                                            </small>
                                            <div id="list_top_holder" style="margin-top:5px;max-height:100px;"></div>
                                        </div>
                                        <div class="col-sm-8">
                                            <div class="input-group">
                                                <input type="text" name="ad_url_list" id="ad_url_list"
                                                       value="<?php echo e(isset($movie->ad_url_list) ? $movie->ad_url_list : null); ?>"
                                                       class="form-control">
                                            </div>
                                            <div class="vid-item-ptb banner_ads_item">
                                                <div class="container-fluid">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <?php echo stripslashes(get_web_banner('details_top')); ?>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <small id="emailHelp" class="form-text text-muted">Enter ad URL</small>
                                        </div>
                                    </div>

                                    <textarea name="list_top_text" id="list_top_text" class="form-control">
                                        <?php echo e(isset($settings->list_top) ? stripslashes($settings->list_top) : ''); ?>

                                    </textarea>
                                </div>
                            </div>

                            <!-- Script to handle file selection and textarea updates -->
                            <script type="text/javascript">
                                function processSelectedFile(filePath, requestingField) {
                                    var baseUrl = "<?php echo e(URL::to('/')); ?>/"; // Get the base URL of your application

                                    // Construct the full URL by appending the base URL to the file path
                                    var fullUrl = baseUrl + filePath.replace(/\\/g, "/");

                                    // Display the image preview based on the requesting field
                                    if (requestingField === "Home_top") {
                                        var target_preview = $('#home_top_holder');
                                        target_preview.html('').append(
                                            $('<img>').css('height', '5rem').attr('src', fullUrl)
                                        ).trigger('change');
                                    } else if (requestingField === "home_bottom") {
                                        var target_preview = $('#home_bottom_holder');
                                        target_preview.html('').append(
                                            $('<img>').css('height', '5rem').attr('src', fullUrl)
                                        ).trigger('change');
                                    } else if (requestingField === "list_top") {
                                        var target_preview = $('#list_top_holder');
                                        target_preview.html('').append(
                                            $('<img>').css('height', '5rem').attr('src', fullUrl)
                                        ).trigger('change');
                                    }

                                    // Set the full URL in the input field
                                    $('#' + requestingField).val(fullUrl).trigger('change');
                                    updateTextarea(requestingField);
                                }

                                function updateTextarea(requestingField) {
                                    var adUrl, targetTextarea;

                                    if (requestingField === "Home_top") {
                                        adUrl = document.getElementById('ad_url_top').value;
                                        targetTextarea = 'home_top_text';
                                    } else if (requestingField === "home_bottom") {
                                        adUrl = document.getElementById('ad_url_bottom').value;
                                        targetTextarea = 'home_bottom_text';
                                    } else if (requestingField === "list_top") {
                                        adUrl = document.getElementById('ad_url_list').value;
                                        targetTextarea = 'list_top_text';
                                    }

                                    var imageUrl = document.getElementById(requestingField).value;

                                    if (imageUrl) {
                                        var htmlContent = '<a href="' + adUrl + '" target="_blank">' +
                                            '<img src="' + imageUrl + '" alt="banner_ads" title="Banner Ads">' +
                                            '</a>';
                                        document.getElementById(targetTextarea).value = htmlContent;
                                    }
                                }

                                // Trigger updateTextarea whenever ad_url changes
                                document.getElementById('ad_url_top').addEventListener('input', function() {
                                    updateTextarea('Home_top');
                                });
                                document.getElementById('ad_url_bottom').addEventListener('input', function() {
                                    updateTextarea('home_bottom');
                                });
                                document.getElementById('ad_url_list').addEventListener('input', function() {
                                    updateTextarea('list_top');
                                });
                            </script>


                            <div class="form-group">
                                <div class="offset-sm-3 col-sm-9 pl-1">
                                    <button type="submit" class="btn btn-primary waves-effect waves-light">
                                        <?php echo e(trans('words.save_settings')); ?>

                                    </button>
                                </div>
                            </div>
                            <?php echo Form::close(); ?>

                        </div>


                    </div>
                </div>
            </div>
        </div>
        <?php echo $__env->make('admin.copyright', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </div>

    <script type="text/javascript">
        <?php if(Session::has('flash_message')): ?>

            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: false,
                /*didOpen: (toast) => {
                  toast.addEventListener('mouseenter', Swal.stopTimer)
                  toast.addEventListener('mouseleave', Swal.resumeTimer)
                }*/
            })

            Toast.fire({
                icon: 'success',
                title: '<?php echo e(Session::get('flash_message')); ?>'
            })
        <?php endif; ?>

        <?php if(count($errors) > 0): ?>

            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                html: '<p><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> <?php echo e($error); ?><br/> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></p>',
                showConfirmButton: true,
                confirmButtonColor: '#10c469',
                background: "#1a2234",
                color: "#fff"
            })
        <?php endif; ?>
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.admin_app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\stock_new\resources\views/admin/pages/settings/web_ads.blade.php ENDPATH**/ ?>