@extends('private.layout')

@section('content')
    <div class="header-content">
        <h2><i class="fa fa-home"></i>Perjanjian Kinerja</h2>
    </div>

    <div class="body-content animated fadeIn">

        @if(!Gate::check('read-only'))
        <div class="row mbot-15">
            <div class="col-md-12">
                <a href="{{route('pk.create')}}" class="btn btn-success"><i class="fa fa-plus"></i> Buat baru</a>
            </div>
        </div>
        @endif

        <div class="row">

            <div class="col-md-12">
                <div class="panel rounded shadow">

                    <div class="panel-heading">
                        <div class="pull-left">
                            <h3 class="panel-title">Perjanjian kinerja</h3>
                        </div>
                        <div class="clearfix"></div>
                    </div>

                    <div class="panel-body">
                        <div class="row mbot-15">
                            <div class="col-md-6">
                                <div class="form-horizontal">
                                    <div class="form-group">
                                        <label for="year" class="col-sm-2 control-label">Tahun</label>
                                        <div class="col-sm-10">
                                            {!! Form::select('year', $years, null, [
                                                'class' => 'form-control',
                                                'id'    => 'year-filter'
                                            ]) !!}

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-horizontal">
                                    <div class="form-group">
                                        <label for="unit" class="col-sm-2 control-label">Unit</label>
                                        <div class="col-sm-10">
                                            {!! Form::select('unit', $units, null, [
                                                'class' => 'form-control',
                                                'id'    => 'unit-filter'
                                            ]) !!}

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <table class="table table-striped table-bordered" id="{{$viewId}}-datatables">
                            <thead>
                                <tr>
                                    <th>Tahun</th>
                                    <th>Unit</th>
                                    <th>Pihak 1</th>
                                    <th>Pihak 2</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')
    <script src="{{asset('lib/datatables/media/js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('lib/datatables/media/js/dataTables.bootstrap.min.js')}}"></script>
@stop

@section('styles')
    <link rel="stylesheet" href="{{asset('lib/datatables/media/css/dataTables.bootstrap.min.css')}}"/>
@stop


@section('script')
    <script type="text/javascript">
        $(function() {
            "use strict";

            var table = $('#{{$viewId}}-datatables').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{route('pk.data')}}",
                    data: function(d) {
                        d.year = $('#year-filter').find(':selected').val();
                        d.unit = $('#unit-filter').find(':selected').val();
                        return d;
                    }
                },
                columns: [
                    {data:'year',name:'year'},
                    {data:'first_position_unit_name',name:'first_position_unit_name'},
                    {data:'first_position_user_name',name:'first_position_user_name'},
                    {data:'second_position_user_name',name:'second_position_user_name'},
                    {data:'action',name:'action',orderable:false,searchable:false}
                ]
            });

            $('#unit-filter, #year-filter').on('change', function () {
                table.ajax.reload();
            });
        });
    </script>
@stop