@extends('private.layout')

@section('content')
    <div class="header-content">
        <h2><i class="fa fa-home"></i>Evaluasi Kinerja</h2>
    </div>

    <div class="body-content animated fadeIn">

        <div class="row">
            <div class="col-md-12">
                <div class="panel rounded shadow">

                    <div class="panel-heading">
                        <div class="pull-left">
                            <h3 class="panel-title">Filter evaluasi</h3>
                        </div>
                        <div class="clearfix"></div>
                    </div>

                    <div class="panel-body">
                        @if (count($errors) > 0)
                            <div class="alert-wrapper">
                                <div class="alert alert-danger alert-dismissible fade in" role="alert" data-dismiss="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">×</span>
                                    </button>

                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{$error}}</li>
                                        @endforeach
                                    </ul>

                                </div>
                            </div>
                        @endif
                        <form action="{{route('kegiatan.evaluasi')}}" method="get">
                            <div class="form-group @if($errors->has('year')) has-error @endif">
                                <label for="year">Tahun</label>
                                {!! Form::select('year', $years, null, [
                                    'placeholder' => '-Select Year-',
                                    'class' => 'form-control',
                                    'id'=>'year']) !!}
                            </div>
                            <div class="form-group @if($errors->has('agreement')) has-error @endif">
                                <label for="agreement">Perjanjian kinerja</label>
                                <select id="agreement" name="agreement" class="form-control"></select>
                            </div>
                            {{--<div class="form-group">
                                <label for="program">Program</label>
                                <select id="program" name="program" class="form-control"></select>

                            </div>--}}

                            <button type="submit" class="btn btn-primary"> Load </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>


    </div>

    @include('private._partials.modal')
@stop

@section('scripts')
    <script src="{{asset('lib/datatables/media/js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('lib/datatables/media/js/dataTables.bootstrap.min.js')}}"></script>
    <script src="{{asset('lib/select2/dist/js/select2.min.js')}}"></script>
@stop

@section('styles')
    <link rel="stylesheet" href="{{asset('lib/datatables/media/css/dataTables.bootstrap.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('lib/select2/dist/css/select2.min.css')}}"/>
@stop

@section('script')
    <script type="text/javascript">
        $(function() {
            "use strict";

            //var $yearFilter = $('#year-filter');

            /*$yearFilter.change(function () {
                var $this = $(this),
                        value = $this.val();

                table.ajax.reload();
            });*/

            $('#year').on('change', function () {
                var $this = $(this);

                $('#agreement').html('<option>...Loading...</option>');
                /*$('#program').html('');
                $('#activity').html('');
*/
                $.get('{{route('pk.select2')}}', {
                    year: $this.find(':selected').val()
                }, function (response) {
                    $('#agreement').html(response);
                })
            });

            /*$('#agreement').on('change', function () {
                var $this = $(this);

                $('#program').html('');
                $('#activity').html('');
                $('#target').html('');

                $.get('{{route('program.select2')}}', {
                    agreement: $this.find(':selected').val()
                }, function (response) {
                    $('#program').html(response);
                })
            });*/

            /*$('#program').on('change', function () {
                var $this = $(this);

                $('#activity').html('');
                $('#target').html('');

                $.get('{{route('kegiatan.select2')}}', {
                    program: $this.find(':selected').val()
                }, function (response) {
                    $('#activity').html(response);
                })
            });*/
        });
    </script>
@stop