@extends('layouts.meter-page')

@section('meter-info')

<div class="row">
    <div class="col-sm-6 col-lg-3 consumption-hightlights">
        <div class="info-box">
            <span class="info-box-icon bg-light-blue">А1</span>
            <div class="info-box-content">
                <span class="info-box-text">Активная (тариф 1)</span>

                <span class="info-box-number" id="a1">
                </span>

                <span class="info-box-comment"> кВт-ч</span>
            </div>
            <!-- /.info-box-content -->
        </div>
    </div> <!-- col -->
    <div class="col-sm-6 col-lg-3 consumption-hightlights">
        <div class="info-box">
            <span class="info-box-icon bg-teal">R1</span>
            <div class="info-box-content">
                <span class="info-box-text">Реактивная (тариф 1)</span>

                <span class="info-box-number" id="r1">
                </span>

                <span class="info-box-comment"> кВАр-ч</span>
            </div>
            <!-- /.info-box-content -->
        </div>
    </div> <!-- col -->
    <div class="col-sm-6 col-lg-3 consumption-hightlights">
        <div class="info-box">
            <span class="info-box-icon bg-light-blue">A2</span>
            <div class="info-box-content">
                <span class="info-box-text">Активная (тариф 2)</span>

                <span class="info-box-number" id="a2">
                </span>

                <span class="info-box-comment"> кВт-ч</span>
            </div>
            <!-- /.info-box-content -->
        </div>
    </div> <!-- col -->
    <div class="col-sm-6 col-lg-3 consumption-hightlights">
        <div class="info-box">
            <span class="info-box-icon bg-teal">R2</span>
            <div class="info-box-content">
                <span class="info-box-text">Реактивная (тариф 2)</span>

                <span class="info-box-number" id="r2">
                </span>
                <span class="info-box-comment"> кВАр-ч</span>
            </div>
            <!-- /.info-box-content -->
        </div>
    </div> <!-- col -->
</div>

<div class="box box-default collapsed-box">
    <div class="box-header with-border">
        <i class="fa fa-flash"></i>
        <h3 class="box-title">Подробные показания по всем тарифам</h3>

        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
            </button>
        </div>
        <!-- /.box-tools -->
    </div>
    <!-- /.box-header -->
</div> 

{{-- График --}}
<div class="box">
    <div class="box-header">
        <i class="fa fa-line-chart"></i>
        <h3 class="box-title">Диаграмма расхода за месяц</h3>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
        <div class="chart">
            <canvas id="barChart" style="height: 255px; width: 419px;" width="838" height="205"></canvas>
        </div>
    </div>
    <!-- /.box-body -->
</div>

{{-- Расход за месяц и История показаний за месяц --}}
<div class="box box-default collapsed-box">
    <div class="box-header with-border">
        <i class="fa fa-hourglass-start"></i>
        <h3 class="box-title">Расход за месяц</h3>
        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
            </button>
        </div>
        <!-- /.box-tools -->
    </div>
    <!-- /.box-header -->
    <div class="box-body table-responsive">
        <table class="table table-bordered table-hover table-striped">
            <thead>
            <tr>
                <th>Дата</th>
                <th>Расход за период</th>
                <th>Показания начало</th>
                <th>Показания конец</th>
            </tr>
            </thead>
            <tbody class='month-consumption-table'>

            </tbody>
        </table>
    </div>
    <!-- /.box-body -->
</div>


{{--
<div class="box box-default collapsed-box">
    <div class="box-header with-border">
        <i class="fa fa-history"></i>
        <h3 class="box-title">История показаний за месяц</h3>

        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
            </button>
        </div>
        <!-- /.box-tools -->
    </div>
    <!-- /.box-header -->
</div> 
--}}

@endsection