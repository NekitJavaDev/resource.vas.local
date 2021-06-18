<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Система учёта ресурсов</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- Bootstrap css. Font-awesome + main.css --}}
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    {{-- Fancybox css --}}
    <link rel="stylesheet" href="/css/jquery.fancybox.min.css">
    {{-- Icons --}}
    <link rel="icon" href="{{ asset('img/favicon.png') }}" type="image/x-icon">
    <link rel="shortcut icon" href="{{ asset('img/favicon.png') }}" type="image/x-icon">
    {{-- Additional libraries or css code --}}
    @yield('libraries')
</head>

<body class="hold-transition sidebar-collapse skin-blue sidebar-mini">
    <div class="wrapper">
        @include('layouts.navbar-top')
        @if (Auth::check())
            @include('layouts.aside')
        @endif
        
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">

            {{-- Main content --}}
            @yield('content')

        </div>
        <!-- /.content-wrapper -->
        <footer class="main-footer">
            <div class="pull-right hidden-xs">
                <b>Версия</b> 0.5.0
            </div>
            <strong>2021 Военная Академия Связи</strong>
            <div class="callout callout-info" style="display: none;" id="hidden-content">
                <div class="center">
                    <p>Старший оператор научной роты: <b>ефрейтор Шадрин Д.М.</b></p>
                    <p>Старший оператор научной роты: <b>ефрейтор Мишин А.А.</b></p>
                    <p>Старший оператор научной роты: <b>ефрейтор Янак А.Ф.</b></p>
                    <p>Старший оператор научной роты: <b>ефрейтор Исаев И.А.</b></p>
                    <p>Оператор научной роты: <b>рядовой Иващенко Н.А.</b></p>
                    <p>Старший оператор научной роты <b>ефрейтор Русин И.С.</b></p>
                    <p>Старший оператор научной роты: <b>рядовой Хмыров Н.А.</b></p>
                    <p>Оператор научной роты: <b>рядовой Лещинский И.Г.</b></p>
                </div>
            </div>
                <a data-fancybox data-src="#hidden-content" href="javascript:;">
                    Разработчики ©
                </a>
        </footer>
    </div>
    <!-- ./wrapper -->

    {{-- jQuery --}}
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    {{-- Chart js --}}
    <script src="{{ asset('js/Chart.min.js') }}"></script>
    {{-- Fancybox. Bootstrap js. AdminLTI --}}
    <script src="{{ asset('js/app.js') }}"></script>
    {{-- Additional scripts or js libraries --}}
    
    <!-- {{-- Общий файл для js --}}
    <script src="{{ asset('js/main.js') }}"></script> -->
    @yield('scripts')
</body>
</html>