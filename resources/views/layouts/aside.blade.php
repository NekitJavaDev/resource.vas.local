<aside class="main-sidebar">
<!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu" data-widget="tree">
            <li class="header">Главное меню</li>
            <li>
                <a href="{{ url('/') }}">
                    <i class="fa fa-files-o"></i> <span>Общая сводка</span>
                </a>
            </li>
            <li>
                <a href="{{ url('/observing') }}">
                    <i class="fa fa-files-o"></i> <span>Мониторинг</span>
                </a>
            </li>
            <li>
                <a href="{{ url('/observing_night') }}">
                    <i class="fa fa-files-o"></i> <span>Ночной мониторинг</span>
                </a>
            </li>
            <li class="treeview">
                <a href="construction">
                    <i class="fa fa-edit"></i> <span>Отчёты</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li><a href="{{ url('/meters/1') }}"><i class="fa fa-flash"></i> Электроэнергия</a></li>
                    <li><a href="{{ url('/meters/7') }}"><i class="fa fa-tint"></i> Холодная вода</a></li>
                    <li><a href="{{ url('/meters/25') }}"><i class="fa fa-fire"></i> Тепловая энергия</a></li>
                </ul>
            </li>
            <li class="treeview">
                <a href="academy">
                    <i class="fa  fa-code-fork"></i> <span>Подразделения</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>

                <ul class="treeview-menu">
                    <li class="treeview">
                        <a href="city"><i class="fa fa-bank"></i> в/г №123
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li class="treeview">
                                <a href="#"><i class="fa fa-building-o"></i> ГП-1
                                    <span class="pull-right-container">
                                        <i class="fa fa-angle-left pull-right"></i>
                                    </span>
                                </a>
                                <ul class="treeview-menu">
                                    <li><a href="{{ url('/buildings/1') }}"><i class="fa fa-circle-o"></i> Сводка по зданию</a></li>
                                    <li><a href="{{ url('/buildings/1') }}"><i class="fa fa-circle-o"></i> Отчёт по зданию</a></li>
                                </ul>
                            </li>
                            <li class="treeview">
                                <a href="#"><i class="fa fa-building-o"></i> ГП-2
                                    <span class="pull-right-container">
                                        <i class="fa fa-angle-left pull-right"></i>
                                    </span>
                                </a>
                                <ul class="treeview-menu">
                                    <li><a href="{{ url('/buildings/1') }}"><i class="fa fa-circle-o"></i> Сводка по зданию</a></li>
                                    <li><a href="{{ url('/buildings/1') }}"><i class="fa fa-circle-o"></i> Отчёт по зданию</a></li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                    <li class="treeview">
                        <a href="city"><i class="fa fa-bank"></i> в/г №5
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li class="treeview">
                                <a href="#"><i class="fa fa-building-o"></i> ТЗ
                                    <span class="pull-right-container">
                                        <i class="fa fa-angle-left pull-right"></i>
                                    </span>
                                </a>
                                <ul class="treeview-menu">
                                    <li><a href="{{ url('/buildings/18') }}"><i class="fa fa-circle-o"></i> Сводка по зданию</a></li>
                                    <li><a href="{{ url('/buildings/18') }}"><i class="fa fa-circle-o"></i> Отчёт по зданию</a></li>
                                </ul>
                            </li>
                            <li class="treeview">
                                <a href="#"><i class="fa fa-building-o"></i> БОКС №1
                                    <span class="pull-right-container">
                                        <i class="fa fa-angle-left pull-right"></i>
                                    </span>
                                </a>
                                <ul class="treeview-menu">
                                    <li><a href="{{ url('/buildings/19') }}"><i class="fa fa-circle-o"></i> Сводка по зданию</a></li>
                                    <li><a href="{{ url('/buildings/19') }}"><i class="fa fa-circle-o"></i> Отчёт по зданию</a></li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                    <li class="treeview">
                        <a href="city">
                            <i class="fa fa-bank"></i> в/г №6
                        </a>
                    </li>
                    <li class="treeview">
                        <a href="city">
                            <i class="fa fa-bank"></i> в/г №84
                        </a>
                    </li>
                    <li class="treeview">
                        <a href="city">
                            <i class="fa fa-bank"></i> Учебный центр
                        </a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="{{ url('/info') }}">
                    <i class="fa fa-book"></i> 
                    <span>О системе</span>
                </a>
            </li>
             @if (Auth::user()->role_id == "1")
                <li class="treeview">
                    <a href="construction">
                        <i class="fa  fa-gear"></i> <span>Служебные модули</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        <li>
                            <a href="#">
                            <i class="fa fa-fire text-blue"></i>Логика СПТ941
                            </a>
                        </li>
                        <li>
                            <a href="#">
                            <i class="fa fa-fire text-blue"></i>Логика СПТ943
                            </a>
                        </li>
                        <li>
                            <a href="#">
                            <i class="fa fa-flash text-blue"></i>Меркурий 230
                            </a>
                        </li>
                        <li>
                            <a href="#">
                            <i class="fa fa-flash text-blue"></i>Меркурий 230 мониторинг
                            </a>
                        </li>
                        <li>
                            <a href="#">
                            <i class="fa fa-tint text-blue"></i>Овен СИ8
                            </a>
                        </li>
                        <li>
                            <a href="#">
                            <i class="fa fa-tint text-blue"></i>Пульсар 2М
                            </a>
                        </li>
                    </ul>
                </li>
            @endif
        </ul>
    </section>
<!-- /.sidebar -->
</aside>