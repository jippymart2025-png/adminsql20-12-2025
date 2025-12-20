@extends('layouts.app')

@section('content')
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{trans('lang.vendor_subscription_history_plural')}}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item active">{{trans('lang.subscription_history_table')}}</li>
            </ol>
        </div>
    </div>

    <div class="container-fluid">
        <div id="data-table_processing" class="dataTables_processing panel panel-default" style="display: none;">
            {{trans('lang.processing')}}</div>

        <div class="admin-top-section">
            <div class="row">
                <div class="col-12">
                    <div class="d-flex top-title-section pb-4 justify-content-between">
                        <div class="d-flex top-title-left align-self-center">
                            <span class="icon mr-3"><img src="{{ asset('images/subscription.png') }}"></span>
                            <h3 class="mb-0">{{trans('lang.vendor_subscription_history_plural')}}</h3>
                            <span class="counter ml-3 total_count"></span>
                        </div>
                        <div class="d-flex top-title-right align-self-center">
                            <div class="select-box pl-3">
                              
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="table-list">
            <div class="row">
                <div class="col-12">
                    <?php if ($id != '') { ?>
                    <div class="menu-tab" style="display:none">
                        <ul>
                            <li id="basic_tab"></li>
                            <li id="food_tab"> </li>
                            <li id="order_tab"></li>
                            <li id="promos_tab"></li>
                            <li id="payout_tab"></li>
                            <li id="payout_request"></li>
                            <li id="dine_in"></li>
                            <li id="restaurant_wallet"></li>
                            <li class="active" id="subscription_plan"></li>
                        </ul>
                    </div>
                    <?php } ?>
                    <div class="card border">
                        <div class="card-header d-flex justify-content-between align-items-center border-0">
                            <div class="card-header-title">
                                <h3 class="text-dark-2 mb-2 h4">{{trans('lang.subscription_history_table')}}</h3>
                                <p class="mb-0 text-dark-2">{{trans('lang.subscription_history_table_text')}}</p>
                            </div>
                            <div class="card-header-right d-flex align-items-center">
                                <div class="card-header-btn mr-3">
                                    <!-- <a class="btn-primary btn rounded-full" href="{!! route('users.create') !!}"><i class="mdi mdi-plus mr-2"></i>{{trans('lang.user_create')}}</a> -->
                                </div>

                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive m-t-10">
                                <table id="subscriptionHistoryTable"
                                    class="display nowrap table table-hover table-striped table-bordered table table-striped"
                                    cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                        <th class="delete-all"><input type="checkbox" id="is_active"><label
                                                        class="col-3 control-label" for="is_active"><a id="deleteAll"
                                                            class="do_not_delete" href="javascript:void(0)"><i
                                                                class="mdi mdi-delete"></i> {{trans('lang.all')}}</a></label>
                                                </th>
                                            <?php if ($id == '') { ?>
                                            <th>{{ trans('lang.vendor')}}</th>
                                            <?php } ?>
                                            <th>{{trans('lang.plan_name')}}</th>
                                            <th>{{trans('lang.plan_type')}}</th>
                                            <th>{{trans('lang.plan_expires_at')}}</th>
                                            <th>{{trans('lang.purchase_date')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody id="append_list1">
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    var userId = '{{$id}}';
    
    $(document).ready(function() {
        jQuery("#data-table_processing").show();
        
        var dataUrl = userId 
            ? "{{ url('/vendor/subscription-plan/history') }}/" + userId + "/data"
            : "{{ route('vendor.subscriptionPlanHistory.data.all') }}";
        
        const table = $('#subscriptionHistoryTable').DataTable({
            pageLength: 10,
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: {
                url: dataUrl,
                type: 'GET',
                dataSrc: function(json) {
                    jQuery("#data-table_processing").hide();
                    if (json.data) {
                        $('.total_count').text(json.recordsTotal);
                        return json.data;
                    } else {
                        return [];
                    }
                },
                error: function(xhr, error, code) {
                    jQuery("#data-table_processing").hide();
                    console.error('Error loading subscription history:', error);
                }
            },
            order: [[<?php echo $id == '' ? 1 : 0; ?>, 'desc']],
            columns: [
                { data: 0, orderable: false }, // Checkbox
                <?php if ($id == '') { ?>
                { 
                    data: 1,
                    render: function(data, type, row) {
                        // Render as HTML for display, plain text for other types
                        if (type === 'display') {
                            return data;
                        }
                        return data;
                    }
                }, // Vendor name (with link)
                <?php } ?>
                { data: <?php echo $id == '' ? 2 : 1; ?> }, // Plan name
                { data: <?php echo $id == '' ? 3 : 2; ?> }, // Plan type
                { data: <?php echo $id == '' ? 4 : 3; ?> }, // Expires at
                { data: <?php echo $id == '' ? 5 : 4; ?> }  // Purchase date
            ],
            language: {
                "zeroRecords": "{{ trans('lang.no_record_found') }}",
                "emptyTable": "{{ trans('lang.no_record_found') }}",
                "processing": ""
            }
        });
        
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = function() {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }
        
        $('#search-input').keyup(debounce(function() {
            table.search($(this).val()).draw();
        }, 300));
        
        <?php if ($id != '') { ?>
        $('.menu-tab').show();
        <?php } ?>
    });
    
    $("#is_active").click(function() {
        $("#subscriptionHistoryTable .is_open").prop('checked', $(this).prop('checked'));
    });
    
    $("#deleteAll").click(function() {
        if ($('#subscriptionHistoryTable .is_open:checked').length) {
            if (confirm("{{ trans('lang.selected_delete_alert') }}")) {
                jQuery("#data-table_processing").show();
                // Bulk delete not implemented for history
                alert('{{ trans("lang.delete_not_allowed") }}');
                jQuery("#data-table_processing").hide();
            }
        } else {
            alert("{{ trans('lang.select_delete_alert') }}");
        }
    });
</script>
@endsection

