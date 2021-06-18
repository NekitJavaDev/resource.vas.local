@extends('layouts.app')

@php
    $tabPanes = [
        [
            'id' => 'tab_1',
            'title' => 'Южный военный округ',
        ],
        [
            'id' => 'tab_2',
            'title' => 'Западный военный округ',
        ],
        [
            'id' => 'tab_3',
            'title' => 'Центральный военный округ',
        ],
        [
            'id' => 'tab_4',
            'title' => 'Восточный военный округ',
        ],
        [
            'id' => 'tab_6',
            'title' => 'Северный флот',
        ]
    ];
@endphp

@section('content')
    <div class="tab-content">
        <div class="tab-pane active" id="tab_5">
            <div class="row">
            <div class="col-xs-4">
                <div class="info-box bg-aqua-gradient">
                <span class="info-box-icon"><i class="fa fa-tint"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">Холодная вода</span>
                    <span class="info-box-number"><span class="random"></span> м<sup>3</sup></span>

                    <div class="progress">
                    <div class="progress-bar" style="width: 12%"></div>
                    </div>
                        <span class="progress-description">
                        расход с 01.05.2018
                        </span>
                </div>
                <!-- /.info-box-content -->
                </div>
                <div class="row">
                <div class="col-xs-12">
                    <div class="chart-responsive">
                    <canvas id="pieChartWater" height="310" width="976" style="width: 488px; height: 155px;"></canvas>
                    </div>
                    <!-- ./chart-responsive -->
                </div>
                <!-- /.col -->
                <div class="col-xs-12">
                    <ul class="chart-legend clearfix text-center">
                    <li><i class="fa fa-circle-o text-red"></i> Южный военный округ</li>
                    <li><i class="fa fa-circle-o text-green"></i> Западный военный округ</li>
                    <li><i class="fa fa-circle-o text-yellow"></i> Центральный военный округ</li>
                    <li><i class="fa fa-circle-o text-aqua"></i> Восточный военный округ</li>
                    <li><i class="fa fa-circle-o text-blue"></i> Северный флот</li>
                    </ul>
                </div>
                <!-- /.col -->
                </div>                                    
            </div>
            <div class="col-xs-4">
                <div class="info-box bg-yellow-gradient">
                <span class="info-box-icon"><i class="fa fa-flash"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">Электроэнергия</span>
                    <span class="info-box-number"><span class="random"></span> кВт-ч</span>

                    <div class="progress">
                    <div class="progress-bar" style="width: 12%"></div>
                    </div>
                        <span class="progress-description">
                        расход с 01.05.2018
                        </span>
                </div>
                <!-- /.info-box-content -->
                </div>                
                <div class="row">
                <div class="col-xs-12">
                    <div class="chart-responsive">
                    <canvas id="pieChartElectro" height="310" width="976" style="width: 488px; height: 155px;"></canvas>
                    </div>
                    <!-- ./chart-responsive -->
                </div>
                <!-- /.col -->
                <div class="col-xs-12">
                    <ul class="chart-legend clearfix text-center">
                    <li><i class="fa fa-circle-o text-red"></i> Южный военный округ</li>
                    <li><i class="fa fa-circle-o text-green"></i> Западный военный округ</li>
                    <li><i class="fa fa-circle-o text-yellow"></i> Центральный военный округ</li>
                    <li><i class="fa fa-circle-o text-aqua"></i> Восточный военный округ</li>
                    <li><i class="fa fa-circle-o text-blue"></i> Северный флот</li>
                    </ul>
                </div>
                <!-- /.col -->
                </div>                                    
            </div>
            <div class="col-xs-4">
                <div class="info-box bg-red-gradient">
                <span class="info-box-icon"><i class="fa fa-fire"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">Тепловая энергия</span>
                    <span class="info-box-number"><span class="random"></span> ГКал</span>

                    <div class="progress">
                    <div class="progress-bar" style="width: 12%"></div>
                    </div>
                        <span class="progress-description">
                        расход с 01.05.2018
                        </span>
                </div>
                <!-- /.info-box-content -->
                </div>                
                <div class="row">
                <div class="col-xs-12">
                    <div class="chart-responsive">
                    <canvas id="pieChartHeat" height="310" width="976" style="width: 488px; height: 155px;"></canvas>
                    </div>
                    <!-- ./chart-responsive -->
                </div>
                <!-- /.col -->
                <div class="col-xs-12">
                    <ul class="chart-legend clearfix text-center">
                    <li><i class="fa fa-circle-o text-red"></i> Южный военный округ</li>
                    <li><i class="fa fa-circle-o text-green"></i> Западный военный округ</li>
                    <li><i class="fa fa-circle-o text-yellow"></i> Центральный военный округ</li>
                    <li><i class="fa fa-circle-o text-aqua"></i> Восточный военный округ</li>
                    <li><i class="fa fa-circle-o text-blue"></i> Северный флот</li>
                    </ul>
                </div>
                <!-- /.col -->
                </div>                                    
            </div>
            <!-- /колонка ресурса -->
            </div>
        </div>
        @foreach ($tabPanes as $tabPane)
            <div class="tab-pane" id="{{ $tabPane['id'] }}">
                <h3>{{ $tabPane['title'] }}</h3>
                <div class="row">
                    <div class="col-xs-4">
                        <div class="info-box bg-aqua-gradient">
                        <span class="info-box-icon"><i class="fa fa-tint"></i></span>
        
                        <div class="info-box-content">
                            <span class="info-box-text">Холодная вода</span>
                            <span class="info-box-number"><span class="random"></span> м<sup>3</sup></span>
        
                            <div class="progress">
                            <div class="progress-bar" style="width: 12%"></div>
                            </div>
                                <span class="progress-description">
                                расход с 01.05.2018
                                </span>
                        </div>
                        <!-- /.info-box-content -->
                        </div>                
                    </div>
                    <div class="col-xs-4">
                        <div class="info-box bg-yellow-gradient">
                        <span class="info-box-icon"><i class="fa fa-flash"></i></span>
        
                        <div class="info-box-content">
                            <span class="info-box-text">Электроэнергия</span>
                            <span class="info-box-number"><span class="random"></span> кВт-ч</span>
        
                            <div class="progress">
                            <div class="progress-bar" style="width: 12%"></div>
                            </div>
                                <span class="progress-description">
                                расход с 01.05.2018
                                </span>
                        </div>
                        <!-- /.info-box-content -->
                        </div>                
                    </div>
                    <div class="col-xs-4">
                        <div class="info-box bg-red-gradient">
                        <span class="info-box-icon"><i class="fa fa-fire"></i></span>
        
                        <div class="info-box-content">
                            <span class="info-box-text">Тепловая энергия</span>
                            <span class="info-box-number"><span class="random"></span> ГКал</span>
        
                            <div class="progress">
                            <div class="progress-bar" style="width: 12%"></div>
                            </div>
                                <span class="progress-description">
                                расход с 01.05.2018
                                </span>
                        </div>
                        <!-- /.info-box-content -->
                        </div>                
                    </div>
                <!-- /колонка ресурса -->
                </div>
            </div>
        @endforeach
    </div>
@endsection

@section('scripts')
  <script src="{{ asset('js/pages/home.js') }}"></script>
@endsection

@section('name')
    
@endsection

{{-- 
    Water => 

        <div class="col-xs-4">
            <div class="info-box bg-aqua-gradient">
            <span class="info-box-icon"><i class="fa fa-tint"></i></span>

            <div class="info-box-content">
                <span class="info-box-text">Холодная вода</span>
                <span class="info-box-number"><span class="random"></span> м<sup>3</sup></span>

                <div class="progress">
                <div class="progress-bar" style="width: 12%"></div>
                </div>
                    <span class="progress-description">
                    расход с 01.05.2018
                    </span>
            </div>
            <!-- /.info-box-content -->
            </div>
            <div class="row">
            <div class="col-xs-12">
                <div class="chart-responsive">
                <canvas id="pieChartWater" height="310" width="976" style="width: 488px; height: 155px;"></canvas>
                </div>
                <!-- ./chart-responsive -->
            </div>
            <!-- /.col -->
            <div class="col-xs-12">
                <ul class="chart-legend clearfix text-center">
                <li><i class="fa fa-circle-o text-red"></i> Южный военный округ</li>
                <li><i class="fa fa-circle-o text-green"></i> Западный военный округ</li>
                <li><i class="fa fa-circle-o text-yellow"></i> Центральный военный округ</li>
                <li><i class="fa fa-circle-o text-aqua"></i> Восточный военный округ</li>
                <li><i class="fa fa-circle-o text-blue"></i> Северный флот</li>
                </ul>
            </div>
            <!-- /.col -->
            </div>                                    
        </div>

    Electricity =>
        <div class="col-xs-4">
            <div class="info-box bg-yellow-gradient">
            <span class="info-box-icon"><i class="fa fa-flash"></i></span>

            <div class="info-box-content">
                <span class="info-box-text">Электроэнергия</span>
                <span class="info-box-number"><span class="random"></span> кВт-ч</span>

                <div class="progress">
                <div class="progress-bar" style="width: 12%"></div>
                </div>
                    <span class="progress-description">
                    расход с 01.05.2018
                    </span>
            </div>
            <!-- /.info-box-content -->
            </div>                
            <div class="row">
            <div class="col-xs-12">
                <div class="chart-responsive">
                <canvas id="pieChartElectro" height="310" width="976" style="width: 488px; height: 155px;"></canvas>
                </div>
                <!-- ./chart-responsive -->
            </div>
            <!-- /.col -->
            <div class="col-xs-12">
                <ul class="chart-legend clearfix text-center">
                <li><i class="fa fa-circle-o text-red"></i> Южный военный округ</li>
                <li><i class="fa fa-circle-o text-green"></i> Западный военный округ</li>
                <li><i class="fa fa-circle-o text-yellow"></i> Центральный военный округ</li>
                <li><i class="fa fa-circle-o text-aqua"></i> Восточный военный округ</li>
                <li><i class="fa fa-circle-o text-blue"></i> Северный флот</li>
                </ul>
            </div>
            <!-- /.col -->
            </div>                                    
        </div>
    
    Heat =>
        <div class="col-xs-4">
            <div class="info-box bg-red-gradient">
            <span class="info-box-icon"><i class="fa fa-fire"></i></span>

            <div class="info-box-content">
                <span class="info-box-text">Тепловая энергия</span>
                <span class="info-box-number"><span class="random"></span> ГКал</span>

                <div class="progress">
                <div class="progress-bar" style="width: 12%"></div>
                </div>
                    <span class="progress-description">
                    расход с 01.05.2018
                    </span>
            </div>
            <!-- /.info-box-content -->
            </div>                
            <div class="row">
            <div class="col-xs-12">
                <div class="chart-responsive">
                <canvas id="pieChartHeat" height="310" width="976" style="width: 488px; height: 155px;"></canvas>
                </div>
                <!-- ./chart-responsive -->
            </div>
            <!-- /.col -->
            <div class="col-xs-12">
                <ul class="chart-legend clearfix text-center">
                <li><i class="fa fa-circle-o text-red"></i> Южный военный округ</li>
                <li><i class="fa fa-circle-o text-green"></i> Западный военный округ</li>
                <li><i class="fa fa-circle-o text-yellow"></i> Центральный военный округ</li>
                <li><i class="fa fa-circle-o text-aqua"></i> Восточный военный округ</li>
                <li><i class="fa fa-circle-o text-blue"></i> Северный флот</li>
                </ul>
            </div>
            <!-- /.col -->
            </div>                                    
        </div>
--}}