import app_consts from '../constants';

var { base_url } = app_consts;

// var lastConsumptionDatetime;
var meterId = document.getElementsByClassName('meter_id')[0].value;
var lastNightConsumptionUrl = base_url + 'meters/' + meterId + '/last_night_water_consumption';
// var refreshLastConsumptionUrl = base_url + 'meters/refresh/' + meterId; 

var autoRefresh = $('#autoRefresh').is(':checked');
var beginSuccessTime = new Date();
var lastSuccessTime = new Date();
var errorMaxCount = 5;
var errorCount = 0;


$(document).ready(function () {
    $('.sidebar-menu').tree()
    // updateTimeFromSuccess();
    // setInterval(updateTimeFromSuccess, 1000);
    refreshData();
    showChart();
})

function refreshData() {
    $.ajax({
        url: lastNightConsumptionUrl,
        type: 'GET',
        dataType: 'json',
        beforeSend: setStatusPending()
    })
        .done(function (lastNightConsumption) {
            console.log(lastNightConsumption);
            $("#consumptionLastNightInMeters").text(lastNightConsumption[0].toFixed(2));
            $("#consumptionLastNightInLitres").text(lastNightConsumption[1].toFixed(2));

            beginSuccessTime = lastNightConsumption[2];
            lastSuccessTime = lastNightConsumption[3];

            updateNightTimeFromSuccess();
            setStatusSuccess();
        })
        .fail(function (data) {
            setStatusError();
            errorCount++;
        })
        .always(function () {
            if (autoRefresh) {
                if (errorCount <= errorMaxCount) {

                    setTimeout(refreshData, 2000);

                    if (errorCount > 0) {
                        console.info("Не выполнено запросов подряд: " + errorCount);
                    }
                } else {
                    console.error("Автоматическое обновление остановлено. Превышел лимит ошибок.")
                    $('#autoRefresh').prop("checked", false)
                    autoRefresh = false;
                }
            }
        });
}

function showChart() {
    const lastConsumptionUrl = base_url + '/meters/' + meterId + '/consumption_by_night/30';

    $.ajax({
        url: lastConsumptionUrl,
        type: 'GET',
        dataType: 'json',
    })
        .done(function (consumptionsObject) {
            console.log("consumptionsObject", consumptionsObject);
            // переделать getChartData + вытащить показание на сегодняшнее утро
            const [labels, plotData] = getChartData(consumptionsObject);

            console.log("labels", labels);
            console.log("plotData", plotData);
            loadDataToChart(labels, plotData);
        })
}

function getChartData(consumptionObject) {
    const plotData = [];
    // Each day consists two consumption (at the start and at the end)
    const days = [...Object.keys(consumptionObject)];

    const consumptionName = 'consumption_amount';

    days.forEach((day) => {
        // get dayly consumptions by subtracting max and min values
        const diff = (consumptionObject[day][1][consumptionName] -
            consumptionObject[day][0][consumptionName]).toFixed(2);

        // push that difference into the plotData
        plotData.push(diff);
    });

    return [
        days,
        plotData
    ]
}

function loadDataToChart(labels, plotData) {
    $(function () {
        /* ChartJS
          * -------
          * Here we will create a few charts using ChartJS
          */
        var areaChartData = {
            labels: labels,
            datasets: [
                {
                    label: 'Потребление воды',
                    fillColor: 'rgba(60,141,188,0.9)',
                    strokeColor: 'rgba(60,141,188,0.8)',
                    pointColor: '#3b8bba',
                    pointStrokeColor: 'rgba(60,141,188,1)',
                    pointHighlightFill: '#fff',
                    pointHighlightStroke: 'rgba(60,141,188,1)',
                    data: plotData
                }
            ]
        }

        //-------------
        //- BAR CHART -
        //-------------
        var barChartCanvas = $('#barChart').get(0).getContext('2d')
        var barChart = new Chart(barChartCanvas)
        var barChartData = areaChartData
        barChartData.datasets[0].fillColor = '#00a65a'
        barChartData.datasets[0].strokeColor = '#00a65a'
        barChartData.datasets[0].pointColor = '#00a65a'
        var barChartOptions = {
            //Boolean - Whether the scale should start at zero, or an order of magnitude down from the lowest value
            scaleBeginAtZero: true,
            //Boolean - Whether grid lines are shown across the chart
            scaleShowGridLines: true,
            //String - Colour of the grid lines
            scaleGridLineColor: 'rgba(0,0,0,.05)',
            //Number - Width of the grid lines
            scaleGridLineWidth: 1,
            //Boolean - Whether to show horizontal lines (except X axis)
            scaleShowHorizontalLines: true,
            //Boolean - Whether to show vertical lines (except Y axis)
            scaleShowVerticalLines: true,
            //Boolean - If there is a stroke on each bar
            barShowStroke: true,
            //Number - Pixel width of the bar stroke
            barStrokeWidth: 2,
            //Number - Spacing between each of the X value sets
            barValueSpacing: 5,
            //Number - Spacing between data sets within X values
            barDatasetSpacing: 1,
            //String - A legend template
            legendTemplate: '<ul class="<%=name.toLowerCase()%>-legend"><% for (var i=0; i<datasets.length; i++){%><li><span style="background-color:<%=datasets[i].fillColor%>"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>',
            //Boolean - whether to make the chart responsive
            responsive: true,
            maintainAspectRatio: true
        }

        barChartOptions.datasetFill = false
        barChart.Bar(barChartData, barChartOptions)
    })
};

/**
 * Кнопка запросить новые показания
 */
// $("#getFreshData").click(refreshData);
$("#getFreshData").click(function () {
    refreshLastConsumption();
    setTimeout(refreshData, 2000);//ждём 2 сек чтобы вытянуть из БД последние данные 
});

$('#autoRefresh').change(function () {
    autoRefresh = $('#autoRefresh').is(':checked');
    if (autoRefresh) {
        errorCount = 0;
        // refreshData();
        refreshLastConsumption();
    };
});

function setStatusPending() {
    $("#statusHeading").html('<i class="icon fa fa-spin fa-spinner"></i> Сведения обновляются...</h4>');
}

function setStatusSuccess() {
    $("#statusPanel").removeClass();
    $("#statusPanel").addClass("alert alert-success");
    $("#statusHeading").html('<i class="icon fa fa-check"></i> Сведения актуальны</h4>');
}

function setStatusError() {
    $("#statusPanel").removeClass();
    $("#statusPanel").addClass("alert alert-danger");
    $("#statusHeading").html('<i class="icon fa fa-times"></i> Сведения могут быть не актуальны</h4>')
}

// function updateTimeFromSuccess() {
//     $('#timeFromSuccess').text(successTime);
// }

function updateNightTimeFromSuccess() {
    $("#beginNightTimeFromSuccess").text(beginSuccessTime);
    $("#lastNightTimeFromSuccess").text(lastSuccessTime);
}