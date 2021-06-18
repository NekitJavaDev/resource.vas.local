@extends('layouts.app')

@section('content')
<section class="content-header">
    <h1>
        {{ $meter->name }}
        <input type="hidden" class="meter_id" name="meter_id" value="{{ $meter->id }}">
        <small>
            <a href="{{ $meter->path() }}">перейти к потреблению</a>
        </small>
        <div class="hidden" id="meter_id">
            {{ $meter->id }}
        </div>
    </h1>
</section>

<section class="content">
    <div class="row">
        <div class="col-sm-8">
            <div class="box box-solid">
                <div class="box-header with-border">
                    <i class="fa fa-bolt"></i>

                    <h3 class="box-title">Информация о потреблении</h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-6 box-center">
                            <div class="data-hightlights">
                                <div class="info-box bg-yellow">
                                    <span class="info-box-icon ">Q</span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Тепловая энерния</span>
                                        <span class="info-box-number" id="q">-</span>
                                        <span class="info-box-comment">ГКал</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 box-center">
                            <div class="data-hightlights">
                                <div class="info-box bg-green">
                                    <span class="info-box-icon ">m</span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Масса</span>
                                        <span class="info-box-number" id="m">-</span>
                                        <span class="info-box-comment">т</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 box-center">
                            <div class="data-hightlights">
                                <div class="info-box bg-red">
                                    <span class="info-box-icon ">t1</span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Температура 1</span>
                                        <span class="info-box-number" id="t1">-</span>
                                        <span class="info-box-comment">град.</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 box-center">
                            <div class="data-hightlights">
                                <div class="info-box bg-red">
                                    <span class="info-box-icon ">t2</span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Температура 2</span>
                                        <span class="info-box-number" id="t2">-</span>
                                        <span class="info-box-comment">град.</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 box-center">
                            <div class="data-hightlights">
                                <div class="info-box bg-blue">
                                    <span class="info-box-icon ">V</span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Объем</span>
                                        <span class="info-box-number" id="v">-</span>
                                        <span class="info-box-comment">м<sup>3</sup></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
        <!-- ./col -->
        <div class="col-sm-4">
            <div class="box box-solid">
                <div class="box-header with-border" id="box-status">
                    <i class="fa fa-check"></i>

                    <h3 class="box-title">Мониторинг</h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <p><strong>Статус: </strong><span id="text-status">процесс готов к запуску</span></p>
                    <div class="row">
                        <div class="col-sm-6">
                            <button type="button" class="btn btn-primary btn-block" id="startMonitoring">Пуск</button>
                        </div>
                        <div class="col-sm-6">
                            <button type="button" class="btn btn-danger btn-block" id="stopMonitoring">Стоп</button>
                        </div>
                    </div>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
        <!-- ./col -->
    </div>
</section>
<!-- /.content -->
@endsection

@section('scripts')
<script src="{{ asset('js/pages/logika_941.js') }}"></script>
@endsection