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
    
                        <h3 class="box-title">Сводная информация по сумме фаз</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="data-hightlights">
                                    <div class="info-box bg-yellow">
                                        <span class="info-box-icon ">P</span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">активаня мощность</span>
                                            <span class="info-box-number" id="p">-</span>
                                            <span class="info-box-comment"> ватт</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="data-hightlights">
                                    <div class="info-box bg-red">
                                        <span class="info-box-icon ">Q</span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">реактивная мощность</span>
                                            <span class="info-box-number" id="q">-</span>
                                            <span class="info-box-comment"> вольт-ампер реактивн.</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="data-hightlights">
                                    <div class="info-box bg-purple">
                                        <span class="info-box-icon ">S</span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">полная мощность</span>
                                            <span class="info-box-number" id="s">-</span>
                                            <span class="info-box-comment"> вольт-ампер</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="data-hightlights">
                                    <div class="info-box bg-light-blue">
                                        <span class="info-box-icon ">f</span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">частота сети</span>
                                            <span class="info-box-number" id="f">-</span>
                                            <span class="info-box-comment"> герц</span>
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
                                <button type="button" class="btn btn-primary btn-block" id="startMonitoring">
                                    Пуск
                                </button>
                            </div>
                            <div class="col-sm-6">
                                <button type="button" class="btn btn-danger btn-block" id="stopMonitoring">
                                    Стоп
                                </button>
                            </div>
                        </div>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
            <!-- ./col -->
        </div>
    
        <div class="row">
            @foreach ($phases = [1, 2, 3] as $phase)
                <div class="col-md-4">
                    <div class="box box-solid">
                        <div class="box-header with-border">
                            <i class="fa fa-bolt"></i>
        
                            <h3 class="box-title">Фаза {{ $phase }}</h3>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <div class="data-hightlights">
                                <div class="info-box bg-green">
                                    <span class="info-box-icon ">U</span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">напряжение</span>
                                        <span class="info-box-number" id="u{{ $phase }}">-</span>
                                        <span class="info-box-comment"> вольт</span>
                                    </div>
                                </div>
                            </div>
                            <div class="data-hightlights">
                                <div class="info-box bg-teal">
                                    <span class="info-box-icon ">I</span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">ток</span>
                                        <span class="info-box-number" id="i{{ $phase }}">-</span>
                                        <span class="info-box-comment"> ампер</span>
                                    </div>
                                </div>
                            </div>
                            <div class="data-hightlights">
                                <div class="info-box bg-yellow">
                                    <span class="info-box-icon">P</span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">активная мощность</span>
                                        <span class="info-box-number" id="p{{ $phase }}">-</span>
                                        <span class="info-box-comment"> ватт</span>
                                    </div>
                                </div>
                            </div>
                            <div class="data-hightlights">
                                <div class="info-box bg-red">
                                    <span class="info-box-icon">Q</span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">реактивная мощность</span>
                                        <span class="info-box-number" id="q{{ $phase }}">-</span>
                                        <span class="info-box-comment"> вольт-ампер реактивн.</span>
                                    </div>
                                </div>
                            </div>
                            <div class="data-hightlights">
                                <div class="info-box bg-purple">
                                    <span class="info-box-icon">S</span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">полная мощность</span>
                                        <span class="info-box-number" id="s{{ $phase }}">-</span>
                                        <span class="info-box-comment"> вольт-ампер</span>
                                    </div>
                                </div>
                            </div>
                            <div class="data-hightlights">
                                <div class="info-box bg-gray">
                                    <span class="info-box-icon">φ</span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">коэффициент мощности</span>
                                        <span class="info-box-number" id="phi{{ $phase }}">-</span>
                                        <span class="info-box-comment"> </span>
                                    </div>
                                </div>
                            </div>
        
                        </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- /.box -->
                </div>
                <!-- ./col --> 
            @endforeach
        </div>
    </section>
    <!-- /.content -->
@endsection

@section('scripts')
    <script src="{{ asset('js/pages/mercury.js') }}"></script>
@endsection