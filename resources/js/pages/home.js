// Map
// should be devided into another module

/*! Image Map Resizer (imageMapResizer.min.js ) - v1.0.3 - 2016-06-16
 *  Desc: Resize HTML imageMap to scaled image.
 *  Copyright: (c) 2016 David J. Bradshaw - dave@bradshaw.net
 *  License: MIT
 */
!function(){
    "use strict";
    function a(){function a(){function a(a,c){function d(a){var c=1===(e=1-e)?"width":"height";return Math.floor(Number(a)*b[c])}var e=0;i[c].coords=a.split(",").map(d).join(",")}var b={width:k.width/k.naturalWidth,height:k.height/k.naturalHeight};j.forEach(a)}function b(a){return a.coords.replace(/ *, */g,",").replace(/ +/g,",")}function c(){clearTimeout(l),l=setTimeout(a,250)}function d(){(k.width!==k.naturalWidth||k.height!==k.naturalHeight)&&a()}function e(){k.addEventListener("load",a,!1),window.addEventListener("focus",a,!1),window.addEventListener("resize",c,!1),window.addEventListener("readystatechange",a,!1),document.addEventListener("fullscreenchange",a,!1)}function f(){return"function"==typeof h._resize}function g(){i=h.getElementsByTagName("area"),j=Array.prototype.map.call(i,b),k=document.querySelector('img[usemap="#'+h.name+'"]'),h._resize=a}var h=this,i=null,j=null,k=null,l=null;f()?h._resize():(g(),e(),d())}function b(){function b(a){if(!a.tagName)throw new TypeError("Object is not a valid DOM element");if("MAP"!==a.tagName.toUpperCase())throw new TypeError("Expected <MAP> tag, found <"+a.tagName+">.")}function c(c){c&&(b(c),a.call(c),d.push(c))}var d;return function(a){switch(d=[],typeof a){case"undefined":case"string":Array.prototype.forEach.call(document.querySelectorAll(a||"map"),c);break;case"object":c(a);break;default:throw new TypeError("Unexpected data type ("+typeof a+").")}return d}}"function"==typeof define&&define.amd?define([],b):"object"==typeof module&&"object"==typeof module.exports?module.exports=b():window.imageMapResize=b(),"jQuery"in window&&(jQuery.fn.imageMapResize=function(){return this.filter("map").each(a).end()})
  }();
  
  var clicks = 0;
  $(document).ready(function() {
    // резиновая карта
    $('map').imageMapResize();
    // рандомные значения расхода
    $("span.random").each(function(index, el) {
      $(el).text(randomInt(10000));
    });
    // рандомные прогресбары расходов
    $(".progress-bar").each(function(index, el) {
      $(el).css('width',randomInt(100));
    });
  
    $('area')
      // прибытие на округ
      .on('mouseenter', function(e) {
        e.preventDefault();
        $("#russiaMapImg").attr('src', $(this).data("img"));
      })
      // убытие из округа
      .on('mouseleave', function(e) {
        e.preventDefault();
        $("#russiaMapImg").attr('src', "./img/maps/map0.png");
      })
      // клик на округ
      .on('click', function(e) {
        // e.preventDefault();
        $('area').off('mouseleave');
        $('area').off('mouseenter');
        $("#russiaMapImg").attr('src', $(this).data("img"));
        $(".region-logo img").attr('src', $(this).data("icon"));
        activaTab($(this).data("tab"))
        // clicks++;
        // if(clicks === 1) {
        //   setTimeout(function() {
        //     clicks = 0;  //after action performed, reset counter
        //   }, 400);
        // } else {
        // }
      })
      // двойной клик
      .on('dblclick', function(event) {
        if ($(this).data("url")){
          window.location.href = $(this).data("url");
        } else {
          window.location.href = "/districts/5";
        }
      });
    // клик на пустоту вне округов
    $('#russiaMapImg').on('click', function(event) {
      event.preventDefault();
      if (event.target.nodeName != "AREA"){
        $("#russiaMapImg").attr('src',"./img/maps/map0.png");
        $(".region-logo img").attr('src',"./img/gerb.png");
        activaTab("tab_5")
      }
    });
    // клик на вкладки
    $(".nav-tabs a").on('click', function(e) {
      $("#russiaMapImg").attr('src', $(this).data("img"));
      $(".region-logo img").attr('src', $(this).data("icon"));
    });
    // клик на вкладки
    $(".iconButton")
      .on('click', function(e) {
        $("#russiaMapImg").attr('src', $(this).data("img"));
        $(".region-logo img").attr('src', $(this).attr("src"));
        activaTab($(this).data("tab"))
      })
      .on('dblclick', function(event) {
        if ($(this).data("url")){
          window.location.href = $(this).data("url");
        } else {
          window.location.href = "/districts/5";
        }
      });
    $('a[data-toggle="tab"]').on('show.bs.tab', function (e) {
      $('.tab-content').fadeOut(0, function() {
        $(this).fadeIn('slow')
      });
    })
  });
  
  // активация вкладки по имени
  function activaTab(tab){
    $('.nav-tabs a[href="#' + tab + '"]').tab('show');
  };
  
  // генерация случайного целого числа в указанном пределе
  function randomInt(maximum) {
    return Math.ceil(maximum*0.1+maximum*0.8*Math.random());
  }

  // Pie Chart

  // -------------
  // - PIE CHART -
  // -------------
  var chartIDs=["pieChartWater","pieChartElectro","pieChartHeat"];
  var charts=[];
  var pieOptions     = {
    // Boolean - Whether we should show a stroke on each segment
    segmentShowStroke    : true,
    // String - The colour of each segment stroke
    segmentStrokeColor   : '#fff',
    // Number - The width of each segment stroke
    segmentStrokeWidth   : 1,
    // Number - The percentage of the chart that we cut out of the middle
    percentageInnerCutout: 50, // This is 0 for Pie chartIDs
    // Number - Amount of animation steps
    animationSteps       : 100,
    // String - Animation easing effect
    animationEasing      : 'easeOut',
    // Boolean - Whether we animate the rotation of the Doughnut
    animateRotate        : true,
    // Boolean - Whether we animate scaling the Doughnut from the centre
    animateScale         : false,
    // Boolean - whether to make the chart responsive to window resizing
    responsive           : true,
    // Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
    maintainAspectRatio  : false,
    // String - A legend template
    legendTemplate       : '<ul class=\'<%=name.toLowerCase()%>-legend\'><% for (var i=0; i<segments.length; i++){%><li><span style=\'background-color:<%=segments[i].fillColor%>\'></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>',
    // String - A tooltip template
    tooltipTemplate      : '<%=value %> доля <%=label%> в общем расходе'
  };
  // Get context with jQuery - using jQuery's .get() method.
  $.each(chartIDs, function(index, chartId) {
    var pieChartCanvas = $('#'+chartId).get(0).getContext('2d');
    var pieChart       = new Chart(pieChartCanvas);
    var PieData        = [
      {
        value    : randomInt(1000),
        color    : '#f56954',
        highlight: '#f56954',
        label    : 'ЮВО'
      },
      {
        value    : randomInt(1000),
        color    : '#00a65a',
        highlight: '#00a65a',
        label    : 'ЗВО'
      },
      {
        value    : randomInt(1000),
        color    : '#f39c12',
        highlight: '#f39c12',
        label    : 'ЦВО'
      },
      {
        value    : randomInt(1000),
        color    : '#00c0ef',
        highlight: '#00c0ef',
        label    : 'ВВО'
      }
    ];
    // Create pie or douhnut chart
    // You can switch between pie and douhnut using the method below.
    charts.push(pieChart.Doughnut(PieData, pieOptions));
  });

  // setInterval(function refreshDiagrams() {
  //   $.each(charts, function(index,chart) {
  //      $.each(chart.segments, function(index, segment) {
  //        segment.value=randomInt(1000);
  //      });
  //      chart.update();
  //   });
  // },10000)
  // -----------------
  // - END PIE CHART -
  // -----------------