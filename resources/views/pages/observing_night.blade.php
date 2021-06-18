@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1 align=center>Ночной мониторинг счётчиков воды</h1>
        </br>
    </section>
@endsection

@section('scripts')
    <script src="{{ asset('js/pages/observing_night.js') }}"></script>
@endsection