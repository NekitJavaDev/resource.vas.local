@extends('layouts.app')

@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        {{ $district->name }}
    </h1>
</section>
<section class="content">
    <div class="row district">
        @foreach ($district->objects as $object)
        <div class="col-md-6 col-lg-4 widget-user">
            <!-- Add the bg color to the header using any of the bg-* classes -->
            <a href="{{ url("/objects/".$object->id) }}">
                <div class="widget-user-header bg-black" style="background-image: url({{ asset('img/objects/bg/'.$object->id.'.jpg') }});">
                    <h3 class="widget-user-username" style="text-shadow: 2px 1px 3px rgba(0, 0, 0, 0.88);">
                        {{ $object->name }}
                    </h3>
                    <h5 class="widget-user-desc" style="text-shadow: 2px 1px 3px rgba(0, 0, 0, 0.88);">
                        {{ $object->address }}
                    </h5>
                </div>
            </a>

            <div class="box-footer">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="description-block">
                            <h5 class="description-header">
                                <span class="buildingsTotal">{{ $object->buildings()->count() }}</span>
                            </h5>
                            <span class="description-text">ОБЪЕКТОВ</span>
                        </div>
                        <!-- /.description-block -->
                    </div>
                    <!-- /.col -->
                    <div class="col-sm-6">
                        <div class="description-block">
                            <h5 class="description-header">
                                <span class="metersTotal">{{ $object->meters_count() }}</span>
                            </h5>
                            <span class="description-text">СЧЁТЧИКОВ</span>
                        </div>
                        <!-- /.description-block -->
                    </div>
                    <!-- /.col -->
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <div class="info-box bg-aqua-gradient">
                            <span class="info-box-icon"><i class="fa fa-tint"></i></span>

                            <div class="info-box-content">
                                <span class="info-box-text">Холодная вода</span>

                                <span class="info-box-number">
                                    {{ $object->consumption('water') }} м<sup>3</sup>
                                </span>

                                <div class="progress">
                                    <div class="progress-bar" style="width: 12%"></div>
                                </div>
                                <span class="progress-description">
                                    расход с 01.06.2018, <span class="comparison"></span>
                                </span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <div class="info-box bg-yellow-gradient">
                            <span class="info-box-icon"><i class="fa fa-flash"></i></span>

                            <div class="info-box-content">
                                <span class="info-box-text">Электроэнергия</span>

                                <span class="info-box-number">
                                    {{ round($object->consumption('electricity'), 1) }} кВт-ч
                                </span>

                                <div class="progress">
                                    <div class="progress-bar" style="width: 12%"></div>
                                </div>
                                <span class="progress-description">
                                    расход с 01.06.2018, <span class="comparison"></span>
                                </span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <div class="info-box bg-red-gradient">
                            <span class="info-box-icon"><i class="fa fa-fire"></i></span>

                            <div class="info-box-content">
                                <span class="info-box-text">Тепловая энергия</span>
                                <span class="info-box-number"><span class="random"></span> ГКал</span>

                                <div class="progress">
                                    <div class="progress-bar" style="width: 12%"></div>
                                </div>
                                <span class="progress-description">
                                    расход с 01.06.2018, <span class="comparison"></span>
                                </span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                    </div>
                    <!-- /колонка ресурса -->
                </div>
                <!-- /ряд ресурсов -->
            </div>
        </div>
        @endforeach
    </div>
</section>
@endsection