@extends('layouts.app')

@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        {{ $object->name }}
    </h1>
</section>

@php
    use Carbon\Carbon;
    
    $nowDate = Carbon::now();
    $parseDate = Carbon::parse($nowDate)->format('Y');

    $years = [];
    for ($i=2020; $i <= $parseDate; $i++) { 
        array_push($years, $i);
    }
@endphp

<!-- Main content -->
<section class="content">
       <div class="row report">
        <div class="col-xs-12">
            <div class="box box-danger collapsed-box">
                <div class="box-header with-border">
                    <h3 class="box-title">Формирование отчётов</h3>

                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
                        </button>
                    </div>
                    <!-- /.box-tools -->
                </div>
                <!-- /.box-header -->
                <div class="box-body" style="display: none">
                    <form class="form-horizontal" id="reportForm" action="/report_object" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="year" class="col-sm-2 control-label">Год</label>
                            <div class="col-sm-10">
                                <select class="form-control" name="year" id="year">
                                    @foreach ($years as $year)
                                        <option value="{{ $year }}">{{ $year }}</option>    
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="month" class="col-sm-2 control-label">Месяц</label>
                            <div class="col-sm-10">
                                <select class="form-control" name="month" id="month">
                                    <option value="1">Январь</option>
                                    <option value="2">Февраль</option>
                                    <option value="3">Март</option>
                                    <option value="4">Апрель</option>
                                    <option value="5">Май</option>
                                    <option value="6">Июнь</option>
                                    <option value="7">Июль</option>
                                    <option value="8">Август</option>
                                    <option value="9" >Сентябрь</option>
                                    <option value="10" selected>Октябрь</option>
                                    <option value="11">Ноябрь</option>
                                    <option value="12">Декабрь</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <div class="checkbox">
                                    <button type="submit" id="makeReport" class="btn btn-primary">Сформировать
                                        отчёт</button>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="object_id" value="{{ $object->id }}">
                    </form>
                </div>
                <!-- /.box-body -->
            </div>
        </div>
    </div>

    <div class="row unit">
        @foreach ($object->sectors as $sector)
        <div class="col-md-6 col-lg-4">
            <!-- Widget: user widget style 1 -->
            <div class="box box-widget widget-user cityColumn">
                <!-- Add the bg color to the header using any of the bg-* classes -->
                <a class="cityUrl" href="{{ url('/sectors/'.$sector->id) }}">
                    <div class="widget-user-header bg-black cityPicture" style="background-image: url('{{ asset('img/sectors/'.$sector->id) }}.png');">
                        <h3 class="widget-user-username cityName" style="text-shadow: 2px 1px 3px rgba(0, 0, 0, 0.88);">
                            {{ $sector->name }}
                        </h3>

                        <h5 class="widget-user-desc cityAdress" style="text-shadow: 2px 1px 3px rgba(0, 0, 0, 0.88);">
                            {{ $sector->address }}
                        </h5>
                    </div>
                </a>

                <div class="box-footer">
                    <div class="row">
                        <div class="col-xs-6 border-right">
                            <div class="description-block">
                                <h5 class="description-header">
                                    <span class="cityBuildingsCount">
                                        {{ $sector->buildings()->count() }}
                                    </span>
                                </h5>
                                <span class="description-text">ОБЪЕКТОВ</span>
                            </div>
                            <!-- /.description-block -->
                        </div>
                        <!-- /.col -->
                        <div class="col-xs-6">
                            <div class="description-block">
                                <h5 class="description-header">
                                    <span class="cityMetersCount">
                                        {{ $sector->meters_count() }}
                                    </span>
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
                                    <span class="info-box-text">Холодная вода
                                        <span class="label label-default"><span class="cityWaterMetersCount">
                                                {{ $sector->meters_count('water') }}
                                            </span> счётчиков</span></span>
                                    <span class="info-box-number">
                                        <span class="cityWaterConsumption">
                                            {{ $sector->consumption('water') }}
                                        </span>м<sup>3</sup>
                                    </span>

                                    <div class="progress">
                                        <div class="progress-bar cityWaterConsumptionProgressbar" style="width: 50%">
                                        </div>
                                    </div>
                                    <span class="progress-description">
                                        расход с 01.06.2018, <span class="cityWaterComparison"></span>
                                    </span>
                                </div>
                                <!-- /.info-box-content -->
                            </div>
                        </div>
                        <div class="col-xs-12">
                            <div class="info-box bg-yellow-gradient">
                                <span class="info-box-icon"><i class="fa fa-flash"></i></span>

                                <div class="info-box-content">
                                    <span class="info-box-text">Электроэнергия
                                        <span class="label label-default"><span class="cityEnergyMetersCount">
                                                {{ $sector->meters_count('electricity') }}
                                            </span> счётчиков</span></span>
                                    <span class="info-box-number">
                                        <span class="cityEnergyConsumption">
                                            {{ $sector->consumption('electricity') }}
                                        </span>кВт-ч</span>
                                    <div class="progress">
                                        <div class="progress-bar cityEnergyConsumptionProgressbar" style="width: 50%">
                                        </div>
                                    </div>
                                    <span class="progress-description">
                                        расход с 01.06.2018, <span class="cityEnergyComparison"></span>
                                    </span>
                                </div>
                                <!-- /.info-box-content -->
                            </div>
                        </div>
                        <div class="col-xs-12">
                            <div class="info-box bg-red-gradient">
                                <span class="info-box-icon"><i class="fa fa-fire"></i></span>

                                <div class="info-box-content">
                                    <span class="info-box-text">Тепловая энергия <span class="label label-default"><span class="cityHeatMetersCount">
                                                {{ $sector->meters_count('heat') }}
                                            </span> счётчиков</span></span>
                                    <span class="info-box-number"><span class="cityHeatConsumption"></span> ГКал</span>

                                    <div class="progress">
                                        <div class="progress-bar cityHeatConsumptionProgressbar" style="width: 50%">
                                        </div>
                                    </div>
                                    <span class="progress-description">
                                        расход с 01.06.2018, <span class="cityHeatComparison"></span>
                                    </span>
                                </div>
                                <!-- /.info-box-content -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.widget-user -->
        </div>
        @endforeach
    </div>

    <style>
        .unit {
            display: block;
        }
    </style>
</section>
<!-- /.content -->
@endsection

@section('scripts')
    <script src="{{ asset('js/pages/object.js') }}"></script>
@endsection