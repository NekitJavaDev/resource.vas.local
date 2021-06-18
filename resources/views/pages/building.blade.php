@extends('layouts.app')

@php
    $electricity_meters = $building->special_meters('electricity')->active()->get();
    $is_electricity_meters_exists = $building->special_meters('electricity')->active()->exists();

    $heat_meters = $building->special_meters('heat')->active()->get();
    $is_heat_meters_exists = $building->special_meters('heat')->active()->exists();

    $water_meters = $building->special_meters('water')->active()->get();
    $is_water_meters_exists = $building->special_meters('water')->active()->exists();

    $total_electricity_comsumption_per_month = 0;
    $total_electricity_comsumption_per_day = 0;

    foreach ($electricity_meters as $electricity_meter) {
        $total_electricity_comsumption_per_month += $electricity_meter->diff_consumption(30);                    
        $total_electricity_comsumption_per_day += $electricity_meter->last_consumption()->t1DirectActive
            + $electricity_meter->last_consumption()->t2DirectActive;
    }

    $total_heat_comsumption_per_month = 0;
    $total_heat_comsumption_per_day = 0;

    foreach ($heat_meters as $heat_meter) {
        $total_heat_comsumption_per_month += $heat_meter->diff_consumption(30);                    
        $total_heat_comsumption_per_day += $heat_meter->last_consumption()['consumption_amount'];
    }

    $total_water_comsumption_per_month = 0;
    $total_water_comsumption_per_day = 0;

    foreach ($water_meters as $water_meter) {
        $total_water_comsumption_per_month += $water_meter->diff_consumption(30);                    
        $total_water_comsumption_per_day += $water_meter->last_consumption()['consumption_amount'];
    }



    // electricity_meters_month_values = 

    //                         <li class="list-group-item">Расход за месяц:
    //                             {{ $electricity_meter->diff_consumption(30) }}
    //                         <li class="list-group-item">Показания (день):
    //                             {{ $electricity_meter->last_consumption()->t1DirectActive }}
    //                         <li class="list-group-item">Показания (ночь):
    //                             {{ $electricity_meter->last_consumption()->t2DirectActive }}
    //                         <li class="list-group-item">Модель:
    //                             {{ $electricity_meter->model_full_name }}
    $selected_meters = $electricity_meters;
@endphp

@section('content')
    <section class="content-header">
        <div class="text-center building-page-building-name">
            {{ $building->short_name . ' ' . $building->name }}
        </div>
        <div class="box box-solid buildingInfo">
            <!-- /.box-header -->
            <div class="box-body">
                <div class="row">
                    <div class="col-md-3 image">
                        <img src="{{ asset('/img/buildings/'.$building->id.'.jpg') }}" alt="">
                    </div>
                    <div class="col-md-4">
                        <div class="total-comsumption-title">
                            Общий расход за месяц
                        </div>
                        <div class="grouped-list">
                            <div class="grouped-list-item">
                                <span class="grouped-list-item-title">Электроэнергия</span>
                                <span class="grouped-list-item-value">
                                    {{ $total_electricity_comsumption_per_month }}
                                </span>
                            </div>
                            <div class="grouped-list-item">
                                <span class="grouped-list-item-title">Тепло</span>
                                <span class="grouped-list-item-value">
                                    {{ $total_heat_comsumption_per_month }}
                                </span>
                            </div>
                            <div class="grouped-list-item">
                                <span class="grouped-list-item-title">Холодная вода</span>
                                <span class="grouped-list-item-value">
                                    {{ $total_water_comsumption_per_month }}
                                </span>
                            </div>
                        </div> 
                    </div>
                    <div class="col-md-4">
                        <div class="total-comsumption-title">
                            Информация о здании
                        </div>
                        <div class="grouped-list">
                            <div class="grouped-list-item">
                            <span class="grouped-list-item-title">Ввод в эксплуатацию</span>
                            <span class="grouped-list-item-value">
                                {{-- <span class="buildingConstruction"> --}}
                                    {{ "$building->created_at г." }}
                                {{-- </span> --}}
                            </span>
                            </div>
                            <div class="grouped-list-item">
                            <span class="grouped-list-item-title">Реставрация (кап. ремонт)</span>
                            <span class="grouped-list-item-value">
                                {{-- <span class="buildingRepair"> --}}
                                    {{ 
                                        $building->updated_at ? 
                                            "$building->updated_at г." :
                                            'Не реставрировалось' 
                                    }}
                                {{-- </span> --}}
                            </span>
                            </div>

                            <div class="grouped-list-item">
                            <span class="grouped-list-item-title">Общая площадь</span>
                            <span class="grouped-list-item-value">
                                {{-- <span class="buildingSpace"> --}}
                                    @if ($building->area)
                                        {{ $building->area }}
                                        м<sup>2</sup>
                                    @else
                                        {{"Данные отсутствуют"}}
                                    @endif
                                {{-- </span> --}}
                            </span>
                            </div>

                            <div class="grouped-list-item">
                            <span class="grouped-list-item-title">Этажность</span>
                            <span class="grouped-list-item-value">
                                {{-- <span class="buildingSpace"> --}}
                                    <!-- {{ $building->floors . " этажей" ?? "Данные отсутствуют" }} -->
                                    {{ $building->floors ?? "Данные отсутствуют" }}
                                {{-- </span> --}}
                            </span>
                            </div>

                            <div class="grouped-list-item">
                            <span class="grouped-list-item-title">Приборов учёта</span>
                            <span class="grouped-list-item-value">
                                {{ $building->meters()->active()->count() }} шт
                            </span>
                            </div>

                            <div class="grouped-list-item">
                            <span class="grouped-list-item-title">Максимальная выделенная мощность</span>
                            <span class="grouped-list-item-value">
                                    @if ($building->max_emit_power)
                                        {{ $building->max_emit_power . " КВт" }}
                                    @else
                                        {{"Данные отсутствуют"}}
                                    @endif
                            </span>
                            </div>

                            <div class="grouped-list-item">
                            <span class="grouped-list-item-title">Имеющаяся резервная мощность</span>
                            <span class="grouped-list-item-value">
                                    @if ($building->max_reserve_power)
                                        {{ $building->max_reserve_power . " КВт" }}
                                    @else
                                        {{"Данные отсутствуют"}}
                                    @endif
                            </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.box-body -->
        </div>
    </section>
    
    <!-- Main content -->
    <section class="content building-page">
        <script>
            function setActivePanel(panelId) {
                let tabPanels = document.querySelectorAll('.tab-pane');
                if (tabPanels) {
                    Array.from(tabPanels).forEach((x) => {
                        x.classList.remove('active')
                    })
                }
                let electricityTabPanel = document.querySelector(`.tab-pane#${panelId}`);
                if (electricityTabPanel) {
                    electricityTabPanel.classList.add('active');
                }
            }
        </script>
        <div class="row">
            <div class="col-12" >
                <div class="btn-group" role="group" >
                    <input type="radio" class="btn-check" name="btnradio" id="electricity" checked
                        onclick="setActivePanel('electricity')"
                    >
                    <label class="btn btn-outline-primary" for="electricity">
                        <i class="fa fa-flash"></i> Приборы учёта электроэнергии
                    </label>
                    
                    <input type="radio" class="btn-check" name="btnradio" id="heat" autocomplete="off"
                        onclick="setActivePanel('heat')"
                    >
                    <label class="btn btn-outline-primary" for="heat">
                        <i class="fa fa-fire"></i> Приборы учёта тепловой энергии
                    </label>

                    <input type="radio" class="btn-check" name="btnradio" id="water" autocomplete="off"
                        onclick="setActivePanel('water')"
                    >
                    <label class="btn btn-outline-primary" for="water">
                        <i class="fa fa-tint"></i> Приборы учёта холодной воды
                    </label>
                </div>
                <div class="tab-content">
                    <div class="tab-pane active " id="electricity">
                        
                            <div class="meters">
                                    @if ($is_electricity_meters_exists)
                                        @foreach ($electricity_meters as $electricity_meter)
                                            <div class="meter_item">
                                                <div class="box box-warning box-solid">
                                                    <div class="box-header with-border">
                                                        <a href="{{ $electricity_meter->path() }}">
                                                            <font size="6">
                                                                {{ $electricity_meter->name }}
                                                            </font>
                                                        </a>
                                                    </div>
                                                    <div class="panel-body">
                                                        <p>
                                                            {{ $electricity_meter->description }}
                                                        </p>
                                                    </div>
                                                    <ul class="list-group">
                                                        <li class="list-group-item">Расход за месяц:
                                                            {{ $electricity_meter->diff_consumption(30) }}
                                                        <li class="list-group-item">Показания (день):
                                                            {{ $electricity_meter->last_consumption()->t1DirectActive }}
                                                        <li class="list-group-item">Показания (ночь):
                                                            {{ $electricity_meter->last_consumption()->t2DirectActive }}
                                                        <li class="list-group-item">Модель:
                                                            {{ $electricity_meter->model_full_name }}
                                                        </li>
                                                    </ul>
                                                    <div class="panel-footer">Сведения получены
                                                        <!-- {{ $electricity_meter->last_consumption()->created_at->format('h:m d-m-Y') }} -->
                                                        {{ $electricity_meter->last_consumption()->created_at->format('d.m.Y h:m') }}
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <p>В выбранном здании нет подключённых счётчиков данного типа ресурсов</p>
                                    @endif
                            </div>
                    
                    </div>
                    <div class="tab-pane" id="heat">
                        
                            <div class="meters">
                                    @if ($is_heat_meters_exists)
                                        @foreach ($heat_meters as $heat_meter)
                                            <div class="meter_item">
                                                <div class="box box-danger box-solid">
                                                    <div class="box-header with-border">
                                                        <a href="{{ $heat_meter->path() }}">
                                                            <font size="6">
                                                                {{ $heat_meter->name }}
                                                            </font>
                                                        </a>
                                                    </div>
                                                    <div class="panel-body">
                                                        <p>
                                                            {{ $heat_meter->description }}
                                                        </p>
                                                    </div>
                                                    <ul class="list-group">
                                                    <li class="list-group-item">Расход за месяц:
                                                            {{ $heat_meter->diff_consumption(30) }}
                                                        <li class="list-group-item">Показания:
                                                            {{ $heat_meter->last_consumption()['consumption_amount'] }}
                                                        <li class="list-group-item">Модель:
                                                            {{ $heat_meter->model_full_name }}
                                                        </li>
                                                    </ul>
                                                    <div class="panel-footer">Сведения получены
                                                        <!-- {{ $heat_meter->last_consumption()->created_at->format('h:m d-m-Y') }} -->
                                                        {{ $heat_meter->last_consumption()->created_at }}
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <p>В выбранном здании нет подключённых счётчиков данного типа ресурсов</p>
                                    @endif
                            </div>
                      
                        <!-- /.tab-pane -->
                    </div>
                    <div class="tab-pane" id="water">
                                <div class="meters">
                                    @if ($is_water_meters_exists)
                                        @foreach ($water_meters as $water_meter)
                                            <div class="meter_item">
                                                <div class="box box-info box-solid">
                                                    <div class="box-header with-border">
                                                        <a href="{{ $water_meter->path() }}">
                                                            <font size="6">
                                                                {{ $water_meter->name }}
                                                            </font>
                                                        </a>
                                                    </div>
                                                    <div class="panel-body">
                                                        <p>
                                                            {{ $water_meter->description }}
                                                        </p>
                                                    </div>
                                                    <ul class="list-group">
                                                        <li class="list-group-item">Расход за месяц:
                                                            {{ $water_meter->diff_consumption(30) }}
                                                        <li class="list-group-item">Показания:
                                                            {{ $water_meter->last_consumption()['consumption_amount'] }}
                                                        <li class="list-group-item">Модель:
                                                            {{ $water_meter->model_full_name }}
                                                        </li>
                                                    </ul>
                                                    <div class="panel-footer">Сведения получены
                                                        <!-- {{ $water_meter->last_consumption()->created_at->format('H:m:s d-m-Y') }} -->
                                                            {{ $water_meter->last_consumption()->created_at }}
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <p>В выбранном здании нет подключённых счётчиков данного типа ресурсов</p>
                                    @endif
                            </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection