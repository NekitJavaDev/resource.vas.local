@php
    extract($report);
@endphp

<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Система учёта ресурсов</title>
</head>
<style>
    @page {
        size: A4;
        margin: 0cm;
    }

    /*печатные стили*/
    @media print {

        html,
        body {
            width: 210mm;
            /*height: 297mm;*/
        }

        .container {
            margin: 1.5cm;
        }

        .control {
            display: none;
        }

        /* ... the rest of the rules ... */
    }

    /*экранные стили*/
    @media screen {
        body {
            background: #eee;
            margin: 0;
        }

        .control {
            background: #aaa;
            text-align: center;
            padding: 1.5cm;
        }

        .control button {
            padding: 0.5cm;
            font-size: 14pt;
        }

        .container {
            background: white;
            margin: 1.5cm auto;
            width: 210mm;
            /*height: 297mm;*/
            padding: 1.5cm;
            overflow: hidden;
        }
    }

    /*общие стили*/
    h1 {
        text-align: center;
        font-size: 14pt;
        font-weight: normal;
    }

    h2  {
        font-size: 12pt;
        text-align: center;
    }

    span.field {
        border-bottom: 1px solid black;
        padding: 0 1ex;
        font-weight: bold;
    }

    td {
        font-size: 12pt;
        border: 1px solid black;
        text-align: center;
        vertical-align: middle;
        padding: 1ex;
    }

    thead td {
        font-weight: bold;
    }

    table {
        border-collapse: collapse;
    }
</style>

<body>
    <div class="control">
        <a href="javascript: history.back()" style="position: fixed; left: 15px; top: 15px; padding: 15px 15px; color: white; background: #ccc;">Отставить</a>
        <button type="button" onclick="window.print()"> Отправить на печать </button>
    </div>
    <div class="container">
        <h1><b>СПРАВКА</b><br>о расходе энергоресурсов воинской части<br>"<span class="field">{{ $object->name }}</span>"<br>за <span class="field">{{ $monthName }}</span> месяц <span class="field">{{ $year }}</span> года</h1>
        
        @foreach ($object->sectors as $sector)
            <h2 class="field">{{ $sector->name }}</h2>
            <h2 class="field">{{ $sector->address }}</h2>

            <table>
                <thead>
                    <tr>
                        <td>Наименование ресурса</td>
                        <td>Единица измерения</td>
                        <td>Показания на начало периода</td>
                        <td>Показания на конец периода</td>
                        <td>Расход</td>
                        <td>Тариф</td>
                        <td>Начислено, руб</td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Электроэнергия</td>
                        <td>кВт/ч</td>
                        <td>{{ $sector->report['electricity']['start'] }}</td>
                        <td>{{ $sector->report['electricity']['end'] }}</td>
                        <td>{{ $sector->report['electricity']['diff'] }}</td>
                        <td>{{ $sector->report['electricity']['tarif'] }}</td>
                        <td>{{ $sector->report['electricity']['cost_str'] }}</td>
                    </tr>
                    <tr>
                        <td>Холодная вода</td>
                        <td>м<sup>3</sup></td>
                        <td>{{ $sector->report['water']['start'] }}</td>
                        <td>{{ $sector->report['water']['end'] }}</td>
                        <td>{{ $sector->report['water']['diff'] }}</td>
                        <td>{{ $sector->report['water']['tarif'] }}</td>
                        <td>{{ $sector->report['water']['cost_str'] }}</td>
                    </tr>
                    <tr>
                        <td>Тепловая энергия</td>
                        <td>ГКалл</td>
                        <td>{{ $sector->report['heat']['start'] }}</td>
                        <td>{{ $sector->report['heat']['end'] }}</td>
                        <td>{{ $sector->report['heat']['diff'] }}</td>
                        <td>{{ $sector->report['heat']['tarif'] }}</td>
                        <td>{{ $sector->report['heat']['cost_str'] }}</td>
                    </tr>
                </tbody>
            </table>
            <p><strong>ИТОГО за городок:</strong> {{ $sector->report['total_str'] }}</p>
            <br>
        @endforeach

        <p><strong>ИТОГО:</strong> {{ $total_str }}</p>
    </div>
</body>

</html>