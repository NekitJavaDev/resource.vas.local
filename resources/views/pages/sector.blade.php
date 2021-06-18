@extends('layouts.app')

@section('content')
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
                    <form class="form-horizontal" id="reportForm" action="/report" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="year" class="col-sm-2 control-label">Год</label>
                            <div class="col-sm-10">
                                <select class="form-control" name="year" id="year">
                                    <option value="2021">2021</option>
                                    <option value="2020" selected>2020</option>
                                    <!-- <option value="2019">2019</option>
                                    <option value="2018">2018</option> -->
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
                                    <option value="9">Сентябрь</option>
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
                        <input type="hidden" name="divisionName" value="{{ $sector->object->name }}">
                        <input type="hidden" name="cityName" value="{{ $sector->name }}">
                        <input type="hidden" name="sector_id" value="{{ $sector->id }}">
                    </form>
                </div>
                <!-- /.box-body -->
            </div>
        </div>
    </div>

    <div class="row" id="city">
        @foreach ($sector->buildings as $building)
        <div class="col-md-6 col-lg-4 buildingColumn">
            <div class="panel panel-default">
                <!-- Default panel contents -->
                <a class="buildingUrl" href="{{ url('/buildings/'.$building->id) }}">
                    <div class="panel-heading buildingName">
                        {{ $building->short_name . ' ' . $building->name }}
                    </div>
                    <div class="panel-photo buildingPicture" style="background-image: url('{{ url('img/buildings/'.$building->id.'.jpg') }}');">
                        <!-- Тут картинка -->
                    </div>
                </a>
                <div class="panel-body">
                    <p class="buildingDescription">
                        {{ $building->description }}
                    </p>
                    <dl class="dl-horizontal">
                        <dt>Ввод в эксплуатацию</dt>
                        <dd>
                            <span class="buildingConstruction">
                                {{ $building->created_at }}
                            </span>
                        </dd>

                        <dt>Реставрация</dt>
                        <dd>
                            <span class="buildingRepair">
                                {{
                                            $building->updated_at ? 
                                                $building->updated_at :
                                                'Не реставрировалось' 
                                        }}
                            </span>
                        </dd>
                        <dt>Общая площадь</dt>
                        <dd>
                            <span class="buildingSpace">
                                {{ $building->area ?? "Данные отсутствуют" }}
                            </span>
                        </dd>
                        <dt>Этажность</dt>
                        <dd>
                            <span class="buildingSpace">
                                {{ $building->floors ?? "Данные отсутствуют" }}
                            </span>
                        </dd>
                    </dl>
                </div>
                <!-- List group -->
                <ul class="list-group">
                    <li class="list-group-item list-group-item-success">
                        <i class="icon fa fa-tachometer"></i>
                        Приборов учёта:
                        <span class="buildingMetersCount">
                            {{ $building->meters()->active()->count() }}
                        </span>
                    </li>
                    <li class="list-group-item list-group-item-info">
                        <i class="icon fa fa-tint"></i> Расход воды:
                        <span class="buildingWaterConsumption">
                            {{ $building->consumption('water') }}
                        </span> м<sup>3</sup>
                    </li>
                    <li class="list-group-item list-group-item-danger">
                        <i class="icon fa fa-fire"></i> Расход тепла:
                        <span class="buildingHeatConsumption">
                            -
                        </span> ГКал
                    </li>
                    <li class="list-group-item list-group-item-warning">
                        <i class="icon fa fa-flash"></i> Расход энергии:
                        <span class="buildingEnergyConsumption">
                            {{ $building->consumption('electricity') }}
                        </span>
                        кВт-ч
                    </li>
                </ul>
            </div>
        </div>
        @endforeach
    </div>
</section>
<!-- /.content -->
@endsection

@section('scripts')
    <script src="{{ asset('js/pages/object.js') }}"></script>
@endsection