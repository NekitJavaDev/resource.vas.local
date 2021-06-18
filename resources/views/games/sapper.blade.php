@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Учебный тренажёр миноискателя
        </h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-4">
                <div class="box box-solid">
                    <div class="box-body clearfix">
                        <blockquote class="pull-right">
                            <p>Сапёр ошибается дважды. Первый раз - при выборе профессии.</p>
                            <small><cite>Народная мудрость</cite></small>
                        </blockquote>
                    </div>
                    <!-- /.box-body -->
                </div>
                <form id="settingsForm" class="form-horizontal">
                    <div class="box box-solid">
                        <div class="box-header with-border">
                            <i class="fa fa-wrench"></i>
                            <h3 class="box-title">Установка на учения</h3>
                        </div>
                        <!-- /.box-header -->
                        <!-- form start -->
                        <div class="box-body">
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="height">Высота</label>
                                <div class="col-sm-10">
                                    <input class="form-control" id="height" autofocus value="13">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="width">Ширина</label>
                                <div class="col-sm-10">
                                    <input class="form-control" id="width" value="13">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="minesTotal">Мин</label>
                                <div class="col-sm-10">
                                    <input class="form-control" id="minesTotal" value="13">
                                </div>
                            </div>
                        </div>
                        <!-- /.box-body -->
                        <div id="settingsOverlay" class="overlay" style="display: none;">
                            <i class="fa fa-gear fa-spin"></i>
                        </div>
                        <div class="box-footer">
                            <button id="generateField" class="btn btn-primary btn-block" type="submit">Начать тренировку</button>
                        </div>
                        <!-- /.box-footer -->
                    </div>
                </form>
                <div class="box box-solid">
                    <div class="box-header with-border">
                        <i class="fa fa-question-circle"></i>
                        <h3 class="box-title">Инструкция</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <dl class="dl-horizontal">
                            <dt>Левая кнопка</dt>
                            <dd><i class="fa fa-search"></i> Открыть сектор</dd>
                            <dt>Правая кнопка</dt>
                            <dd><i class="fa fa-flag"></i> Отметить мину</dd>
                            <dt>Средняя кнопка</dt>
                            <dd><i class="fa fa-question-circle"></i> Возможно мина</dd>
                        </dl>
                    </div>
                    <!-- /.box-body -->
                </div>

            </div>
            <div class="col-md-8">
                <div class="box box-solid">
                    <div class="box-header with-border">
                        <i class="fa fa-gear"></i>
                        <h3 class="box-title">Ход тренировки</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="box-body clearfix">
                                <div class="info-box bg-yellow">
                                    <span class="info-box-icon"><i class="fa fa-bomb"></i></span>

                                    <div class="info-box-content">
                                        <span class="info-box-text">Мин осталось</span>
                                        <span id="minesLeft" class="info-box-number">?</span>

                                        <div class="progress">
                                            <div id="minesSetProgressbar" class="progress-bar" style="width: 0%"></div>
                                        </div>
                                        <span class="progress-description">
                                            <span id="minesSetPercent">0</span>% флагов установлено
                                        </span>
                                    </div>
                                    <!-- /.info-box-content -->
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="box-body clearfix">
                                <div class="info-box bg-green">
                                    <span class="info-box-icon"><i class="fa fa-flag-checkered"></i></span>

                                    <div class="info-box-content">
                                        <span class="info-box-text">Полей отмечено</span>
                                        <span id="cellsChecked" class="info-box-number">?</span>

                                        <div class="progress">
                                            <div id="cellsCheckedProgressbar" class="progress-bar" style="width: 0%"></div>
                                        </div>
                                        <span class="progress-description">
                                            <span id="cellsCheckedPercent">0</span>% от общего числа
                                        </span>
                                    </div>
                                    <!-- /.info-box-content -->
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.box-body -->
                </div>
                <table id="mineField"></table>

            </div>
        </div>

        <div class="modal modal-success fade" id="modal-success">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title"><i class="fa fa-check"></i> Красава!</h4>
                    </div>
                    <div class="modal-body">
                        <p>Поздравляем! Вы успешно выполнили курс сапёра.<br>Вы не допустили ни одной фатальной ошибки и
                            остались живы!</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline playAgain"><i class="fa fa-refresh"></i> Начать заново</button>
                        <button type="button" class="btn btn-outline pull-left" data-dismiss="modal">Закрыть</button>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->
        <div class="modal modal-danger fade" id="modal-failure">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title"><i class="fa fa-close"></i> Это фиаско, братан</h4>
                    </div>
                    <div class="modal-body">
                        <p>К сожалению, вы допустили ошибку и попытались вскрыть заминированный сектор, что привело к
                            срабатыванию мины. Вы провалили курс сапёра.<br>Вы допустили фатальную ошибку, которая может
                            стоить сапёру жизни.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline playAgain"><i class="fa fa-refresh"></i> Начать заново</button>
                        <button type="button" class="btn btn-outline pull-left" data-dismiss="modal">Закрыть</button>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->
    </section>
@endsection


@section('scripts')
  <script src="{{ asset('js/pages/sapper.js') }}"></script>
@endsection