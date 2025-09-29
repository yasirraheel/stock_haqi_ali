<?php $__env->startSection('content'); ?>
    <div class="content-page">
        <div class="content">
            <div class="container-fluid">
                <div class="row">


                    <div class="col-12">
                        <div class="row mb-4">
                            <div class="col-xl-3 col-md-6">
                                <a href="#">
                                    <div class="card-box widget-user">
                                        <div class="text-center">
                                            <h2 class="text-primary" data-plugin="counterup"><?php echo e(number_format($allTransactions->sum('license_price'), 2)); ?> <?php echo e($currency_code); ?></h2>
                                            <h5 style="color: #f9f9f9;">Total Sold Amount</h5>
                                        </div>
                                    </div>
                                </a>
                            </div>

                            <div class="col-xl-3 col-md-6">
                                <a href="#">
                                    <div class="card-box widget-user">
                                        <div class="text-center">
                                            <h2 class="text-warning" data-plugin="counterup"><?php echo e(number_format($allTransactions->sum('amount_paid_to_author'), 2)); ?> <?php echo e($currency_code); ?></h2>
                                            <h5 style="color: #f9f9f9;">Total Earned by Author</h5>
                                        </div>
                                    </div>
                                </a>
                            </div>

                            <div class="col-xl-3 col-md-6">
                                <a href="#">
                                    <div class="card-box widget-user">
                                        <div class="text-center">
                                            <h2 class="text-success" data-plugin="counterup"><?php echo e(number_format($allTransactions->sum('admin_commission'), 2)); ?> <?php echo e($currency_code); ?></h2>
                                            <h5 style="color: #f9f9f9;">Total Earned by Admin</h5>
                                        </div>
                                    </div>
                                </a>
                            </div>

                            <div class="col-xl-3 col-md-6">
                                <a href="#">
                                    <div class="card-box widget-user">
                                        <div class="text-center">
                                            <h2 class="text-info" data-plugin="counterup"><?php echo e($allTransactions->count()); ?></h2>
                                            <h5 style="color: #f9f9f9;">Total Sales</h5>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>


                        </div>

                        <div class="card-box table-responsive">



                            <?php if(Session::has('flash_message')): ?>
                                <div class="alert alert-success">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span></button>
                                    <?php echo e(Session::get('flash_message')); ?>

                                </div>
                            <?php endif; ?>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Buyer Email</th>
                                            <th>Author PayPal Email</th>
                                            <th>License Price</th>
                                            <th>Amount Paid to Author</th>
                                            <th>Admin Commission</th>
                                            <th>Payment ID</th>
                                            
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $allTransactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr id="transaction_id_<?php echo e($transaction->id); ?>">
                                                <td><?php echo e($transaction->buyer_email); ?></td>
                                                <td><?php echo e($transaction->author_paypal_email); ?></td>
                                                <td>$<?php echo e(number_format($transaction->license_price, 2)); ?></td>
                                                <td>$<?php echo e(number_format($transaction->amount_paid_to_author, 2)); ?></td>
                                                <td>$<?php echo e(number_format($transaction->admin_commission, 2)); ?></td>
                                                <td><?php echo e($transaction->payment_id); ?></td>
                                                
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>


                            </div>
                            <nav class="paging_simple_numbers">
                                <?php echo $__env->make('admin.pagination', ['paginator' => $allTransactions], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                            </nav>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php echo $__env->make('admin.copyright', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </div>

    <script src="<?php echo e(URL::asset('admin_assets/js/jquery.min.js')); ?>"></script>

    <script type="text/javascript">
        $(".data_remove").click(function() {

            var post_id = $(this).data("id");
            var action_name = 'user_delete';

            Swal.fire({
                title: '<?php echo e(trans('words.dlt_warning')); ?>',
                text: "<?php echo e(trans('words.dlt_warning_text')); ?>",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: '<?php echo e(trans('words.dlt_confirm')); ?>',
                cancelButtonText: "<?php echo e(trans('words.btn_cancel')); ?>",
                background: "#1a2234",
                color: "#fff"

            }).then((result) => {

                //alert(post_id);

                //alert(JSON.stringify(result));

                if (result.isConfirmed) {

                    $.ajax({
                        type: 'post',
                        url: "<?php echo e(URL::to('admin/ajax_delete')); ?>",
                        dataType: 'json',
                        data: {
                            "_token": "<?php echo e(csrf_token()); ?>",
                            id: post_id,
                            action_for: action_name
                        },
                        success: function(res) {

                            if (res.status == '1') {

                                var selector = "#card_box_id_" + post_id;
                                $(selector).fadeOut(1000);
                                setTimeout(function() {
                                    $(selector).remove()
                                }, 1000);

                                Swal.fire({
                                    position: 'center',
                                    icon: 'success',
                                    title: '<?php echo e(trans('words.deleted')); ?>!',
                                    showConfirmButton: true,
                                    confirmButtonColor: '#10c469',
                                    background: "#1a2234",
                                    color: "#fff"
                                })

                            } else {
                                Swal.fire({
                                    position: 'center',
                                    icon: 'error',
                                    title: 'Something went wrong!',
                                    showConfirmButton: true,
                                    confirmButtonColor: '#10c469',
                                    background: "#1a2234",
                                    color: "#fff"
                                })
                            }

                        }
                    });
                }

            })

        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.admin_app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\stock_new\resources\views/admin/paypal/sold_licenses.blade.php ENDPATH**/ ?>