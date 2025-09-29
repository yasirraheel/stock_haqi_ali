@extends('admin.admin_app')

@section('content')
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
                                            <h2 class="text-primary" data-plugin="counterup">{{ number_format($allTransactions->sum('license_price'), 2) }} {{ $currency_code }}</h2>
                                            <h5 style="color: #f9f9f9;">Total Sold Amount</h5>
                                        </div>
                                    </div>
                                </a>
                            </div>

                            <div class="col-xl-3 col-md-6">
                                <a href="#">
                                    <div class="card-box widget-user">
                                        <div class="text-center">
                                            <h2 class="text-warning" data-plugin="counterup">{{ number_format($allTransactions->sum('amount_paid_to_author'), 2) }} {{ $currency_code }}</h2>
                                            <h5 style="color: #f9f9f9;">Total Earned by Author</h5>
                                        </div>
                                    </div>
                                </a>
                            </div>

                            <div class="col-xl-3 col-md-6">
                                <a href="#">
                                    <div class="card-box widget-user">
                                        <div class="text-center">
                                            <h2 class="text-success" data-plugin="counterup">{{ number_format($allTransactions->sum('admin_commission'), 2) }} {{ $currency_code }}</h2>
                                            <h5 style="color: #f9f9f9;">Total Earned by Admin</h5>
                                        </div>
                                    </div>
                                </a>
                            </div>

                            <div class="col-xl-3 col-md-6">
                                <a href="#">
                                    <div class="card-box widget-user">
                                        <div class="text-center">
                                            <h2 class="text-info" data-plugin="counterup">{{ $allTransactions->count() }}</h2>
                                            <h5 style="color: #f9f9f9;">Total Sales</h5>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>


                        </div>

                        <div class="card-box table-responsive">



                            @if (Session::has('flash_message'))
                                <div class="alert alert-success">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span></button>
                                    {{ Session::get('flash_message') }}
                                </div>
                            @endif
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
                                            {{-- <th>Action</th> --}}
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($allTransactions as $transaction)
                                            <tr id="transaction_id_{{ $transaction->id }}">
                                                <td>{{ $transaction->buyer_email }}</td>
                                                <td>{{ $transaction->author_paypal_email }}</td>
                                                <td>${{ number_format($transaction->license_price, 2) }}</td>
                                                <td>${{ number_format($transaction->amount_paid_to_author, 2) }}</td>
                                                <td>${{ number_format($transaction->admin_commission, 2) }}</td>
                                                <td>{{ $transaction->payment_id }}</td>
                                                {{-- <td>
                                                    <a href="{{ url('admin/transactions/edit/' . $transaction->id) }}"
                                                        class="btn btn-icon waves-effect waves-light btn-success m-b-5 m-r-5"
                                                        data-toggle="tooltip" title="Edit"> <i class="fa fa-edit"></i>
                                                    </a>
                                                    <a href="#"
                                                        class="btn btn-icon waves-effect waves-light btn-danger m-b-5 data_remove"
                                                        data-toggle="tooltip" title="Remove"
                                                        data-id="{{ $transaction->id }}"> <i class="fa fa-remove"></i> </a>
                                                </td> --}}
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>


                            </div>
                            <nav class="paging_simple_numbers">
                                @include('admin.pagination', ['paginator' => $allTransactions])
                            </nav>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('admin.copyright')
    </div>

    <script src="{{ URL::asset('admin_assets/js/jquery.min.js') }}"></script>

    <script type="text/javascript">
        $(".data_remove").click(function() {

            var post_id = $(this).data("id");
            var action_name = 'user_delete';

            Swal.fire({
                title: '{{ trans('words.dlt_warning') }}',
                text: "{{ trans('words.dlt_warning_text') }}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: '{{ trans('words.dlt_confirm') }}',
                cancelButtonText: "{{ trans('words.btn_cancel') }}",
                background: "#1a2234",
                color: "#fff"

            }).then((result) => {

                //alert(post_id);

                //alert(JSON.stringify(result));

                if (result.isConfirmed) {

                    $.ajax({
                        type: 'post',
                        url: "{{ URL::to('admin/ajax_delete') }}",
                        dataType: 'json',
                        data: {
                            "_token": "{{ csrf_token() }}",
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
                                    title: '{{ trans('words.deleted') }}!',
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
@endsection
