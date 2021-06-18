import app_consts from '../constants';

var { base_url } = app_consts;
var meterId = document.getElementsByClassName('meter_id')[0].value;
var url = base_url + '/meters/' + meterId + '/params';
var timer;

$(document).ready(function () {
  $("#startMonitoring").click(startMonitoring);
  $("#stopMonitoring").click(stopMonitoring);
  startMonitoring();
});

function startMonitoring() {
  performUpdate();
  timer = setInterval(performUpdate, 1000);
  setStatusPending();
}

function stopMonitoring() {
  clearInterval(timer);
  setStatusStop();
}

function setStatusRun() {
  $("#text-status").text('запущен')
  $("#box-status").removeClass();
  $("#box-status").addClass('bg-green');
  $("#box-status").addClass('box-header with-border');
}

function setStatusPending() {
  $("#text-status").text('выполняется обновление')
}

function setStatusError() {
  console.error("Ошибка обновления")
  $("#text-status").text('ошибка обновления')
  $("#box-status").removeClass();
  $("#box-status").addClass('bg-red-active');
  $("#box-status").addClass('box-header with-border');

}
function setStatusStop() {
  $("#text-status").text('остановлен')
  $("#box-status").removeClass();
  $("#box-status").addClass('box-header with-border');
}

function performUpdate() {
  $.ajax({
    url: url,
    type: 'GET',
    dataType: 'json',
    beforeSend: setStatusPending
  })
    .done(renderData)
    .fail(function (data) {
      console.log(data.responseText);
    })
    .always();
}


// make param name upper case or make everythin lowercase on back
function renderData(data) {
  setStatusRun();
  renderParam(data, "consumption", "consumption_amount");
  console.log("Обновление выполнено");
}

function renderParam(data, holderId, paramName) {
  var param = data[paramName];

  var fixedParam = param.toFixed(2);

  $("#" + holderId).text(fixedParam);
}