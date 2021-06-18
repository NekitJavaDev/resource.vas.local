// import { id } from "postcss-selector-parser";
function setCurrentMonthAndYearInReport(currentYear, currentMonth) {
    const yearSelector = document.getElementById('year');
    const monthSelector = document.getElementById('month');

    yearSelector.selectedIndex = currentYear - 2020;
    monthSelector.selectedIndex = currentMonth;
}

$(document).ready(function(){
    const currentDate = new Date();
    const currentYear = currentDate.getFullYear();
    const currentMonthIndex = new Date().getMonth();

    $('.form-horizontal').on('submit', function (e) {
        var inputYear = $('#year').val();
        var inputMonth = $('#month').val();
        var currentMonth = currentMonthIndex + 1;

        console.log("Input year = ", inputYear, ', current year = ', currentYear);
        console.log("Input month = ", inputMonth, ', current month = ', currentMonth);

        if(inputYear > currentYear){
            alert(`На этот год данных о расходе ресурсов ещё нет.`);
            e.preventDefault();
        }else if(inputYear==currentYear){
            if (inputMonth > currentMonth){
                alert(`На этот месяц данных о расходе ресурсов ещё нет.`);
                e.preventDefault();
            }
        }
    });

    setCurrentMonthAndYearInReport(currentYear, currentMonthIndex);
});