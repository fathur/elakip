@extends('private.layout')

@section('content')

    <div class="header-content">
        <h2><i class="fa fa-home"></i>Error 403</h2>
    </div>

    <div class="body-content animated fadeIn">

        <div class="row">

            <div class="col-md-12">
                <div class="panel rounded shadow">

                   {{-- <div class="panel-heading">
                        <div class="pull-left">
                            <h3 class="panel-title">Detail</h3>
                        </div>
                        <div class="clearfix"></div>
                    </div>--}}

                    <div class="panel-body">
                        <span style="font-size: 40px;color:red;">
                            <i class="fa fa-exclamation-triangle"></i>
                        </span>
                        <h2>Maaf Anda tidak berwenang melihat halaman ini</h2>
                    </div>
                </div>
            </div>
        </div>

    </div>

@stop

