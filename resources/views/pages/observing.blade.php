@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1 align="center">Мониторинг работоспособности приборов учета</h1>
    </section>
    <section class="obs-controll-panel">
        <form action="" id="filter" class="obs-sorting">
            <div class="obs-sorting__item">
                <div class="form-floating">
                    <select id="meterStatusSelect" name="" class="form-select" placeholder="Статус">
                        <option value="null" selected>Все</option>
                        <option value="{{ $METER_STATUS['worked'] }}">Активные</option>
                        <option value="{{ $METER_STATUS['active'] }}">Подключенные</option>
                        <option value="{{ $METER_STATUS['notActive'] }}">Неактивные</option>
                        <option value="{{ $METER_STATUS['errorConnect'] }}">Ошибка соединения</option>
                    </select>
                    <label for="meterStatus">Статус</label>
                </div>
                <div class="form-floating">
                    <select class="form-select" id="meterTypeSelect" aria-label="Floating label select example">
                        <option value="null" selected>Все</option>
                         @foreach ($meterTypes as $meterType)
                            <option value="{{ $meterType->id }}">{{ $meterType->name_ru }}</option>
                         @endforeach
                    </select>
                    <label class="form-label" for="floatingSelect">Тип устройства</label>
                </div>
                <div class="form-floating">
                    <select class="form-select" id="sectorSelect" aria-label="Floating label select example">
                        <option value="null" selected>Все</option>
                         @foreach ($sectors as $sector)
                            <option value="{{ $sector->id }}">{{ $sector->name}}</option>
                         @endforeach
                    </select>
                    <label for="floatingSelect">Военный городок</label>
                </div>
            </div>
            <div class="button-group">
                <button
                    type="reset"
                    class="btn"
                    id="btn_reset_filter"
                    data-url="{{ route('setup-filter') }}"
                >
                    СБРОСИТЬ
                </button>
                <button 
                    class="btn btn-primary" 
                    id="btn_save_filter"
                    data-url="{{ route('setup-filter') }}"
                    onclick="return false;"
                >
                    ФИЛЬТРОВАТЬ
                </button>
            </div>
        </form>
    </section>
    <section class="buildings-list"></section>
    <section class="obs-legend">
        <header class="obs-building__item-header">
            <div class="obs-building__item-title">
                <div class="obs-legend-item-indicator obs-legend-item-indicator--active"></div> Подключенный
            </div>
        </header>
        <header class="obs-building__item-header">
            <div class="obs-building__item-title">
                <div class="obs-legend-item-indicator obs-legend-item-indicator--worked"></div> Активный
            </div>
        </header>
        <header class="obs-building__item-header">
            <div class="obs-building__item-title">
                <div class="obs-legend-item-indicator obs-legend-item-indicator--is_not_active"></div> Неактивный
            </div>
        </header>
        <header class="obs-building__item-header">
            <div class="obs-building__item-title">
                <div class="obs-legend-item-indicator obs-legend-item-indicator--is_error_connect"></div> Ошибка подключения
            </div>
        </header>
    </section>
@endsection

@section('scripts')
    <script src="{{ asset('js/pages/observing.js') }}"></script>
@endsection