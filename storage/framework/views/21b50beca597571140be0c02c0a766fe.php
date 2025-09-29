

<?php $__env->startSection('content'); ?>
    <style type="text/css">
        .iframe-container {
            overflow: hidden;
            padding-top: 56.25% !important;
            position: relative;
        }

        .iframe-container iframe {
            border: 0;
            height: 100%;
            left: 0;
            position: absolute;
            top: 0;
            width: 100%;
        }
    </style>

    <style type="text/css">
        #loading {
            background: url('<?php echo e(URL::asset('admin_assets/images/LoaderIcon.gif')); ?>') no-repeat center center;
            position: absolute;
            top: 0;
            left: 0;
            height: 50%;
            width: 100%;
            z-index: 9999999;
            opacity: 1;
        }

        .payment_loading {
            opacity: 0.5;
        }
    </style>

    <div id="loading" style="display: none;"></div>


    <div class="content-page">
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card-box">

                            <div class="row">
                                <div class="col-sm-6">
                                    <a href="<?php echo e(URL::to('admin/movies')); ?>">
                                        <h4 class="header-title m-t-0 m-b-30 text-primary pull-left"
                                            style="font-size: 20px;"><i class="fa fa-arrow-left"></i>
                                            <?php echo e(trans('words.back')); ?></h4>
                                    </a>
                                </div>
                                <?php if(isset($movie->id)): ?>
                                    <div class="col-sm-6">
                                        <a href="<?php echo e(URL::to('movies/watch/' . $movie->video_slug . '/' . $movie->id)); ?>"
                                            target="_blank">
                                            <h4 class="header-title m-t-0 m-b-30 text-primary pull-right"
                                                style="font-size: 20px;"><?php echo e(trans('words.preview')); ?> <i
                                                    class="fa fa-eye"></i></h4>
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>




                            <?php echo Form::open([
                                'url' => ['admin/movies/add_edit_movie'],
                                'class' => 'form-horizontal',
                                'name' => 'movie_form',
                                'id' => 'movie_form',
                                'role' => 'form',
                                'enctype' => 'multipart/form-data',
                            ]); ?>


                            <input type="hidden" name="id" value="<?php echo e(isset($movie->id) ? $movie->id : null); ?>">


                            <div class="row">

                                <div class="col-md-6">
                                    <h4 class="m-t-0 m-b-30 header-title" style="font-size: 20px;">
                                        <?php echo e(trans('words.movie_info')); ?></h4>

                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label"><?php echo e(trans('words.movie_name')); ?>*</label>
                                        <div class="col-sm-8">
                                            <input type="text" name="video_title" id="video_title"
                                                value="<?php echo e(isset($movie->video_title) ? stripslashes($movie->video_title) : old('video_title')); ?>"
                                                class="form-control">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="webSite" class="col-sm-12 col-form-label">
                                            <?php echo e(trans('words.description')); ?></label>
                                        <div class="col-sm-12">
                                            <div class="card-box pl-0 description_box">

                                                <textarea id="elm1" name="video_description"><?php echo e(isset($movie->video_description) ? stripslashes($movie->video_description) : old('video_description')); ?></textarea>

                                            </div>
                                        </div>
                                    </div>

                                    <hr />
                                </div>
                                <div class="col-md-6">
                                    <h4 class="m-t-0 m-b-30 header-title" style="font-size: 20px;">
                                        <?php echo e(trans('words.movie_poster_thumb_video')); ?></h4>
                                    <?php if(isset($movie->video_image_thumb)): ?>
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">&nbsp;</label>
                                            <div class="col-sm-8">
                                                <img src="<?php echo e(url($movie->video_image_thumb)); ?>" alt="video thumb"
                                                    class="img-thumbnail" width="110">
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label"><?php echo e(trans('words.genres_text')); ?>*</label>
                                        <div class="col-sm-8 ml-1">
                                            <select name="genres[]" class="select2 select2-multiple" multiple="multiple"
                                                multiple id="movie_genre_id" data-placeholder="Select Genres...">
                                                <?php $__currentLoopData = $genre_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $genre_data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($genre_data->id); ?>"
                                                        <?php if(isset($movie->id) && in_array($genre_data->id, explode(',', $movie->movie_genre_id))): ?> selected <?php endif; ?>>
                                                        <?php echo e($genre_data->genre_name); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">License Price</label>
                                        <div class="col-sm-8">
                                            <input type="number" name="license_price" id="license_price"
                                                value="<?php echo e(isset($movie->license_price) ? stripslashes($movie->license_price) : old('license_price')); ?>"
                                                class="form-control">
                                        </div>

                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Webpage URL</label>
                                        <div class="col-sm-8">
                                            <input type="text" name="webpage_url" id="webpage_url"
                                                value="<?php echo e(isset($movie->webpage_url) ? stripslashes($movie->webpage_url) : old('webpage_url')); ?>"
                                                class="form-control">
                                        </div>

                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Funding URL</label>
                                        <div class="col-sm-8">
                                            <input type="text" name="funding_url" id="funding_url"
                                                value="<?php echo e(isset($movie->funding_url) ? stripslashes($movie->funding_url) : old('funding_url')); ?>"
                                                class="form-control">
                                        </div>

                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Google Drive Video URL</label>
                                        <div class="col-sm-8">
                                            <input type="text" name="video_url" id="video_url"
                                                value="<?php echo e(isset($movie->video_url) ? stripslashes($movie->video_url) : old('video_url')); ?>"
                                                class="form-control">
                                                <small id="emailHelp" class="form-text text-muted">
                                                    Supported Format: Google Drive Public Access URL.<br>
                                                    For instructions on how to upload a video to Google Drive and obtain a
                                                    public link, <a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS"
                                                        target="_blank">click here</a>.<br>
                                                    Please ensure the link is in the correct format; incorrect formats will
                                                    not be accepted.
                                                </small>
                                        </div>

                                    </div>


                                    <?php if(auth()->user()->usertype != 'Admin'): ?>
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label"><?php echo e(trans('words.status')); ?></label>
                                            <div class="col-sm-8">
                                                <select class="form-control" name="status_fake"
                                                    <?php if(auth()->user()->usertype == 'Sub_Admin'): ?> disabled
                                                    style="cursor: not-allowed;"
                                                    title="Video will be approved by super admin review." <?php endif; ?>>
                                                    <option value="1"
                                                        <?php if(isset($movie['status_fake']) && $movie['status_fake'] == 1 && auth()->user()->usertype != 'Sub_Admin'): ?> selected <?php endif; ?>>
                                                        <?php echo e(trans('words.inactive')); ?>

                                                    </option>

                                                </select>
                                                <?php if(auth()->user()->usertype == 'Sub_Admin'): ?>
                                                    <small class="form-text text-muted">Video will be approved by super
                                                        admin
                                                        review.</small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <?php if(auth()->user()->usertype == 'Admin'): ?>
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label"><?php echo e(trans('words.status')); ?></label>
                                            <div class="col-sm-8">
                                                <select class="form-control" name="status">
                                                    <option value="1"
                                                        <?php if(isset($movie->status) and $movie->status == 1): ?> selected <?php endif; ?>>
                                                        <?php echo e(trans('words.active')); ?></option>
                                                    <option value="0"
                                                        <?php if(isset($movie->status) and $movie->status == 0): ?> selected <?php endif; ?>>
                                                        <?php echo e(trans('words.inactive')); ?></option>
                                                </select>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <div class="form-group">
                                        <div class="offset-sm-9 col-sm-9">
                                            <button type="submit" id="add_btn_id"
                                                class="btn btn-primary waves-effect waves-light"><i class="fa fa-save"></i>
                                                <?php echo e(trans('words.save')); ?> </button>
                                        </div>
                                    </div>
                                </div>

                            </div>



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
        // function to update the file selected by elfinder
        function processSelectedFile(filePath, requestingField) {

            //alert(requestingField);

            var elfinderUrl = "<?php echo e(URL::to('/')); ?>/";

            if (requestingField == "video_image_thumb") {
                $('#thumb_link').val('');

                var target_preview = $('#thumb_image_holder');
                target_preview.html('');
                target_preview.append(
                    $('<img>').css('height', '5rem').attr('src', elfinderUrl + filePath.replace(/\\/g, "/"))
                );
                target_preview.trigger('change');
            }

            if (requestingField == "video_image") {
                var target_preview = $('#video_poster_holder');
                target_preview.html('');
                target_preview.append(
                    $('<img>').css('height', '5rem').attr('src', elfinderUrl + filePath.replace(/\\/g, "/"))
                );
                target_preview.trigger('change');
            }

            //$('#' + requestingField).val(filePath.split('\\').pop()).trigger('change'); //For only filename
            $('#' + requestingField).val(filePath.replace(/\\/g, "/")).trigger('change');

        }
    </script>

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

<?php echo $__env->make('admin.admin_app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u273790872/domains/cineworm.org/public_html/stock/resources/views/admin/pages/movies/addedit.blade.php ENDPATH**/ ?>