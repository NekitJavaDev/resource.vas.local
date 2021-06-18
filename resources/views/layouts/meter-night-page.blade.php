@extends('layouts.app')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <input type="hidden" class="meter_id" name="meter_id" value="{{ $meter->id }}">
            {{ $meter->name }}
            <small>
                <a href="{{ $meter->path() . "/monitoring" }}">перейти к мониторингу</a>
            </small>
        </h1>
    </section>

    <!-- Main content -->
    <section class="content">
		<!-- Cockpit -->
        @include('meters.includes.cockpit-night')

        @yield('meter-info')
    </section>

@endsection

@section('scripts')
  <script src="{{ asset('js/pages/night_water.js') }}"></script>
@endsection