<div class="row">
    <div class="col-xs-12 col-md-4">
        <div class="box box-solid">
            <div class="box-header with-border">
                <i class="fa fa-info-circle"></i>
                <h3 class="box-title">Сведения о приборе</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <dl class="dl-horizontal">
                    <dt>Тип прибора</dt>
                    <dd>
                        {{ $meter->type_device_name() }}
                    </dd>
                    <dt>Модель</dt>
                    <dd>
                        {{ $meter->model_full_name }}
                    </dd>
                    <dt>Интерфейс</dt>
                    <dd>
                        {{ $meter->rus_driver_name() }}
                    </dd>
                    <dt>Серийный номер</dt>
                    <dd>
                        {{ $meter->serial_number }}
                    </dd>
                    <dt>Паспорт</dt>
                    <dd>
                        <a data-fancybox="gallery"
                            href="{{ asset('img/passports/'.$meter->serial_number.'.jpg') }}">
                            Открыть
                        </a>
                    </dd>
                    <dt>Дата поверки</dt>
                    @if ($verification_date = $meter->verification_date)
                        <dd>
                            {{ $verification_date->format('d.m.y') }}
                            @if (Carbon\Carbon::now()->diffInDays($verification_date, false) < 30)
                                <i style="color: red;" class="fa fa-exclamation-circle"></i>
                            @endif
                        </dd>
                    @else
                        <dd>
                            {{ 'Неизвестна' }}
                        </dd>
                    @endif
                </dl>
            </div>
            <!-- /.box-body -->
        </div>
        <!-- /.box -->
    </div>
    <div class="col-md-2 hidden-xs hidden-sm">
        <div class="box box-solid">
            <div class="box-body text-center">
                @php
                    //< $meter_model = $meter->model;
                    $meter_model = $meter->model_full_name;
                    $meter_image_name = str_replace(' ', '_', $meter_model).'.jpg';

                    $file_path = asset('img/meters/' . $meter_image_name);
                @endphp
                <img src="{{ $file_path }}" 
                    alt="Изображение счетчика" style="max-height: 180px;">
            </div>
        </div>
    </div>
    <div class="col-xs-12 col-md-6">
        <div id="statusPanel" class="alert alert-success disabled" style="height: 200px;">
            <h4 id="statusHeading"><i class="icon fa fa-info-circle"></i>
             Сведения об актуальности
            </h4>
            </br>
            <p id="statusText">
            Последние показания поступили <span id="timeFromSuccess"></span>
            </p>
            <button type="button" id="getFreshData" class="btn btn-block btn-default"><i class="fa fa-refresh"></i>
                <span>Запросить новые показания</span>
            </button>
            </br>
            <p><label for="autoRefresh"><input type="checkbox" id="autoRefresh">
             Обновлять показания в реальном времени
            </label></p>
        </div> <!-- alert -->
    </div> <!-- col -->
</div>