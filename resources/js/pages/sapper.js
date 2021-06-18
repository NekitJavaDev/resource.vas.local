var minesTotal, fieldRows, fieldCols,
    flagsSet = 0,
    mines = [],
    checkedCells = [],
    queueForCheck = []

$(document).ready(function () {
    // чтение настроек из куки
    var settings = $.parseJSON(getCookieValue("settings"));
    $("#minesTotal").val(settings['minesTotal']);
    $("#height").val(settings['height']);
    $("#width").val(settings['width']);
});

// чтение и поиск куки по имени
function getCookieValue(name) {
    var value = document.cookie.match('(^|;)\\s*' + name + '\\s*=\\s*([^;]+)');
    return value ? value.pop() : undefined;
}

// навешивание слушателей на клики кнопок и форму
$("#generateField").click(startGame);
$(".playAgain").click(startGame);
$("#settingsForm").submit(function (event) {
    event.preventDefault;
    startGame();
    return false;
});

// считывание инпутов и запуск генерации
function startGame() {
    // интерфейсный сброс
    console.log("Новая игра");
    $("#settingsOverlay").slideDown('slow', function () {
        updateStats();
        $(".modal").modal("hide");
        $("#generateField").text('Начать заново');
        // сброс накопителей
        checkedCells = [];
        queueForCheck = [];
        mines = [];
        flagsSet = 0;
        // считывание инпутов
        minesTotal = $("#minesTotal").val();
        fieldRows = $("#height").val();
        fieldCols = $("#width").val();
        // запись последних использованных параметров
        document.cookie = 'settings={"minesTotal": ' + minesTotal + ',"height": ' + fieldRows + ',"width": ' + fieldCols + '}';
        // генерация
        generateField(minesTotal, fieldRows, fieldCols);
    });
}

// генерация поля, расстановка мин, рассчёт цифер
function generateField(minesCount, fieldHeight, fieldWidth) {
    console.log("Начата генерация поля...")
    var index = 1;
    $('#mineField').empty();
    for (var i = 0; i < fieldHeight; i++) {
        $('#mineField').append($('<tr>'));
        for (var j = 0; j < fieldWidth; j++) {
            $('#mineField tr:last').append(
                $('<td>', {
                    "data-row": i,
                    "data-col": j,
                    "data-code": index++
                })
            );
        }
    }
    // вешаем обработчик на правый клик
    $('#mineField td').on('contextmenu', function (event) {
        event.preventDefault();
        toggleFlag(event)
        return false;
    });
    // вешаем обработчик на любой клик
    $('#mineField td').on('mousedown', function (event) {
        event.preventDefault();
        // смотрим какая кнопка нажата
        switch (event.which) {
            // нажата левая
            case 1:
                leftClickHandler(event);
                break;

            // нажата средняя
            case 2:
                toggleQuestion(event);
                break;
        }
        return false;
    });
    console.log("Генерация поля выполнена.");
    console.log("Начата расстановка мин...");
    console.groupCollapsed("Координаты мин");
    var minesLeft = 0;
    if (minesCount > Math.floor(0.9 * fieldWidth * fieldHeight)) {
        minesCount = Math.floor(0.9 * fieldWidth * fieldHeight);
        alert("Вы выбрали слишком много мин. Количество уменьшено до " + minesCount)
    }
    while (minesLeft < minesCount) {
        var row = Math.floor(fieldHeight * Math.random());
        var col = Math.floor(fieldWidth * Math.random());
        console.log("Ряд " + row, " столбец " + col);
        var element = $("#mineField tr:eq(" + row + ") td:eq(" + col + ")");
        if (!element.hasClass('hasMine')) {
            element.addClass('hasMine');
            // element.html('<i class="fa fa-bomb"></i>')
            minesLeft++;
            mines.push(element.data("code"))
        }
    }
    console.groupEnd("Координаты мин");
    console.log('Расстановка мин выполнена. Мин заложено: ' + $('.hasMine').length)
    console.groupCollapsed("Вычисление мин вокруг клеток");
    var cells = $('#mineField td');
    cells.each(function (index, el) {
        var row = $(el).data('row'),
            col = $(el).data('col'),
            minesAround = 0
        console.groupCollapsed("Проверяется соседи клетке " + row + " " + col);
        for (var i = row - 1; i <= row + 1; i++) {
            for (var j = col - 1; j <= col + 1; j++) {
                if ((i < 0) || (j < 0) || (i >= fieldHeight) || (j >= fieldWidth) || ((i == row) && (j == col))) {
                } else {
                    if ($("#mineField tr:eq(" + i + ") td:eq(" + j + ")").hasClass('hasMine')) {
                        minesAround++;
                        console.log("Мина обнаружена в клетке", i, j)
                    }
                }
            }
        }
        console.groupEnd("Проверяется соседи клетке " + row + " " + col);
        el.dataset["mines"] = minesAround;
        // $(el).text(minesAround);
    });
    console.groupEnd("Вычисление мин вокруг клеток");
    console.log("Количество мин подсчитано.")
    $("#settingsOverlay").slideUp("slow");
}

// обработчик левого клика
function leftClickHandler(event) {
    var cell = $(event.currentTarget),
        row = event.currentTarget.dataset["row"],
        col = event.currentTarget.dataset["col"],
        code = event.currentTarget.dataset["code"]
    // console.log("Выполнено нажатие на клетку");
    // если попал на мину и флага не было
    if ((cell.hasClass('hasMine')) && !(cell.hasClass('flagged'))) {
        cell.toggleClass('active');
        endGame(false);
    } else {
        // если нет мины, нет флага и не отмечалась ранее
        if (!cell.hasClass('flagged')) {
            if (!cell.hasClass('clear')) {
                cell.addClass('active');
                setTimeout(function () {
                    cell.removeClass('active');
                }, 1000)
                if (cell.data('mines') == 0) {
                    console.groupCollapsed("Открытие группы пустых ячеек");
                    openNeighbours(code);
                    console.groupEnd("Открытие группы пустых ячеек");
                } else {
                    console.log("Ячейка проверена")
                    setCellClear(row, col);
                }
            } else {
                highlightNeigrbours(1 * row, 1 * col)
            }
        } else console.log("Ничего не сделано. Ячейка имеет флаг.")
    }
    updateStats();
    checkForVictory();
}

// обновление видимой статистики
function updateStats() {
    $("#minesLeft").text(minesTotal - flagsSet);
    $("#minesSetPercent").text(Math.ceil(100 * flagsSet / minesTotal));
    $("#minesSetProgressbar").css("width", Math.ceil(100 * flagsSet / minesTotal) + "%");
    $("#cellsChecked").text(flagsSet + $(".clear").length);
    $("#cellsCheckedPercent").text(Math.ceil(100 * (flagsSet + $(".clear").length) / (fieldRows * fieldCols)));
    $("#cellsCheckedProgressbar").css("width", Math.ceil(100 * (flagsSet + $(".clear").length) / (fieldRows * fieldCols)) + "%");
}

// проверка условия на победу
function checkForVictory() {
    if (flagsSet == minesTotal) {
        console.log("Установлено достаточно флажков. Проверяется условие победы.")
        var allFlagsSetCorrectly = true;
        $(".flagged").each(function (index, el) {
            var code = 1 * el.dataset["code"];
            if ($.inArray(code, mines) == (-1)) {
                allFlagsSetCorrectly = false;

            }
        });
        if (allFlagsSetCorrectly) {
            endGame(true)
        } else console.warn("Есть неправильно установленные флаги");
    }
}

// открытие "чистой" ячейки
function setCellClear(row, col) {
    var cell = $("#mineField tr:eq(" + row + ") td:eq(" + col + ")");
    cell.addClass('clear');
    if (cell.data('mines') != 0) {
        cell.text(cell.data('mines'))
    }
    checkedCells.push(cell.data("code"));
}

// переключение наличия флага
function toggleFlag(event) {
    var cell = $(event.currentTarget);
    if (cell.hasClass('flagged')) {
        cell.removeClass('flagged');
        cell.empty();
        flagsSet--;
        console.log("Флаг снят")
    } else {
        if (!cell.hasClass('clear')) {
            cell.addClass('flagged');
            cell.removeClass('question');
            cell.html('<i class="fa fa-flag"></i')
            flagsSet++;
            console.log("Флаг установлен")
        } else highlightNeigrbours(cell.data("row"), cell.data("col"))
    }
    updateStats();
    checkForVictory();
}

// переключение наличия знака вопроса
function toggleQuestion(event) {
    var cell = $(event.currentTarget);
    if (cell.hasClass('question')) {
        cell.removeClass('question');
        cell.empty();
        console.log("Знак вопроса снят")
    } else {
        if (!cell.hasClass('flagged')) {
            if (!cell.hasClass('clear')) {
                cell.addClass('question');
                cell.html('<i class="fa fa-question-circle"></i');
                console.log("Знак вопроса установлен");
            } else {
                highlightNeigrbours(cell.data("row"), cell.data("col"));
            }
        } else console.log("Знак вопроса на отмеченные флагом не ставится")
    }
    updateStats();
    checkForVictory();
}

function highlightNeigrbours(row, col) {
    console.log("Подсветка зоны");
    var neighbours = [];
    for (var i = row - 1; i <= row + 1; i++) {
        for (var j = col - 1; j <= col + 1; j++) {
            if ((i < 0) || (j < 0) || (i >= fieldRows) || (j >= fieldCols) || ((i == row) && (j == col))) {
            } else {
                neighbours.push($("#mineField tr:eq(" + i + ") td:eq(" + j + ")"));
            }
        }
    }
    $.map(neighbours, function (item, index) {
        $(item).addClass('active');
        setTimeout(function () {
            $(item).removeClass('active');
        }, 500);
    });
}

// открытие непрерывной группы чистыъ клеток
function openNeighbours(code) {
    console.log("Поиск соседей ячейки", code);
    $("#settingsOverlay").slideDown('slow', function () {
        var cell = $('#mineField td[data-code=' + code + ']');
        var row = 1 * cell.data('row'),
            col = 1 * cell.data('col')
        // отметить ячейку чистой
        setCellClear(row, col);
        // определить 4 соседей (снизу, справа, сверху, слева)
        var neighbours = [];
        for (var i = row - 1; i <= row + 1; i++) {
            for (var j = col - 1; j <= col + 1; j++) {
                if ((i < 0) || (j < 0) || (i >= fieldRows) || (j >= fieldCols) || ((i == row) && (j == col))) {
                } else {
                    neighbours.push($("#mineField tr:eq(" + (i) + ") td:eq(" + (j) + ")").data("code"));
                }
            }
        }
        // для каждого соседа
        $.each(neighbours, function (index, code) {
            var cell = $('#mineField td[data-code=' + code + ']'),
                row = cell.data("row"),
                col = cell.data("col"),
                minesAround = cell.data("mines"),
                hasMine = cell.hasClass('hasMine')
            hasChecked = cell.hasClass('clear')
            // если клетка граничная и не имеет мины
            if (minesAround != 0 && !hasMine) {
                setCellClear(row, col);
            } else {
                // если клетки не граничная, не отмечена и не стоит в очереди на проверку
                if (minesAround == 0 && !hasChecked && $.inArray(code, queueForCheck) == (-1)) {
                    // добавляем в очередь на проверку
                    queueForCheck.push(code);
                }
            }
        });
        console.log("Ещё в очереди", queueForCheck)
        // если очередь не пуста, идёт рекурсивное повторение
        if (queueForCheck.length != 0) {
            // извлекаем из очереди крайнего и повторяем цепочку для него
            openNeighbours(queueForCheck.pop())
        } else {
            $("#settingsOverlay").slideUp("slow");
            updateStats();
        }
    });
}

// заканчиваем игру и оповещаем игрока
function endGame(victory) {
    $("#mineField td").unbind('click');
    $("#mineField td").unbind('contextmenu');
    if (victory) {
        $('#mineField td[class!=hasMine][class!=clear][class!=flagged]').each(function (index, el) {
            setCellClear(el.dataset["row"], el.dataset["col"]);
        });
        setTimeout(function () {
            $("#modal-success").modal("show");
            $("#modal-success .playAgain").focus();
        }, 800)
    } else {
        $('#mineField td.hasMine').html('<i class="fa fa-bomb"></i>').addClass('explode');
        setTimeout(function () {
            $("#modal-failure").modal("show")
            $("#modal-failure .playAgain").focus();
        }, 800)
    }
}

// чит для просмотра всех мин
$("cite").click(function () {
    $('#mineField td.hasMine').addClass('explode');
    setTimeout(function () {
        $('#mineField td.hasMine').removeClass('explode');
    }, 1000)
})