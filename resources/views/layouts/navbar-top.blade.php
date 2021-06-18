<header class="main-header">
<!-- Logo -->
<a href="{{ url('/') }}" class="logo">
    <!-- mini logo for sidebar mini 50x50 pixels -->
    <span class="logo-mini">
        <img src="{{ asset('img/icons/favicon-white.svg') }}" style="height: 30px;" alt="">
    </span>
    <!-- logo for regular state and mobile devices -->
    <span class="logo-lg">
        <img src="{{ asset('img/icons/favicon-white.svg') }}" style="height: 30px;" alt=""> 
        Ресурс
    </span>
</a>
<!-- Header Navbar: style can be found in header.less -->
<nav class="navbar navbar-static-top">
    <!-- Sidebar toggle button-->
    @if (Auth::check())
        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
            <span class="sr-only">Переключатель навигации</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </a>
    @endif

    <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
            @if (Auth::check())
            <li>
                <a href="{{ url('/') }}" title="Ресурс-РФ">
                    <i class="fa fa-star"></i>
                </a>
            </li>
            <li>
                <a href="{{ url('/districts/1') }}" title="Ресурс-Округ">
                    <i class="fa fa-bank"></i>
                </a>
            </li>
            <li>
                <a href="{{ url('/objects/1') }}" title="Ресурс-ВЧ">
                    <i class="fa fa-flag"></i>
                </a>
            </li>
            <li>
                <a href="{{ url('/sectors/1') }}" title="Ресурс-ВГ">
                    <i class="fa fa-sitemap"></i>
                </a>
            </li>
            <li>
                <a href="{{ url('/buildings/1') }}" title="Ресурс-ГП">
                    <i class="fa fa-industry"></i>
                </a>
            </li>
            <!-- Notifications: style can be found in dropdown.less -->
            <li class="dropdown notifications-menu">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" title="Ресурс-Прибор">
                    <i class="icon fa fa-tachometer"></i>
                </a>
                <ul class="dropdown-menu">
                    <li>
                        <!-- inner menu: contains the actual data -->
                        <ul class="menu">
                            <li>
                                <a href="{{ url('/meters/4') }}">
                                    <i class="fa fa-flash text-blue"></i>Счётчик электроэнергии
                                </a>
                            </li>
                            <li>
                                <a href="{{ url('/meters/4/monitoring') }}">
                                    <i class="fa fa-flash text-blue"></i>Счётчик электроэнергии (мониторинг)
                                </a>
                            </li>
                            <li>
                                <a href="{{ url('/meters/9') }}">
                                    <i class="fa fa-flash text-blue"></i>Счётчик ХВС
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </li>
            @endif

            <li class="dropdown user user-menu">
                @if (Auth::check())
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    <img src="{{ asset('img/users/' . Auth::user()->id . '.jpg') }}" class="user-image" alt="User Image">
                    
                    <span>{{ Auth::user()->user_name}}</span>
                </a>
                @else
                    <a href="/login" class="btn">Войти</a>
				@endif

                @if (Auth::check())
                    <ul class="dropdown-menu">
                        <li class="user-header">
                            <img src="{{ asset('img/users/' . Auth::user()->id . '.jpg') }}" class="img-circle" alt="User Image">
                            <p>
                                <span class="small">{{ Auth::user()->role->ru_name }}</span><br>
                                <span>{{ Auth::user()->userInfo->fullName() }}</span><br>
                                <span class="small">{{ Auth::user()->userInfo->position }}</span>
                            </p>
                        </li>
                        <li class="user-footer">
                            <div class="pull-left">
                                <a href="{{ url('/profile') }}" class="btn btn-default btn-flat">Профиль</a>
                            </div>
                            <div class="pull-right">
                                <a href="{{ url('/logout') }}" class="btn btn-default btn-flat">Выйти</a>
                            </div>
                        </li>
                    </ul>
                @endif
            </li>
        </ul>
    </div>
</nav>
</header>