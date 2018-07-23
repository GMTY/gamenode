@extends('template.template')

@section('title', 'Страница команды')

@section('content')

    <div class="g-padding"></div>

    @if (session('message'))
        <p class="b-message">{{session('message')}}</p>
        <hr>
    @endif

    @if (!$count)
        <p class="b-message">Команда покинута</p>
        <hr>
    @endif

    <div class="b-page-wrapper">

            <div class="row">
                <div class="col">
                    <div class="b-profile">
                        <div class="b-profile__img" style="background-image: url({{route('/')}}/storage/{{ $img }})"></div>
                        <div class="b-profile__info">
                            <h2>{{ $name }}</h2>
                            <p class="command">{{ $greeting }}</p>

                            @if ($isMember)
                            <div class="b-balance">
                                <span class="b-balance__text">Баланс команды</span><br>
                                <span class="b-balance__count">{{ $balance }} руб.</span>
                                <span class="b-balance__btn">
                                    <a href="{{ route('/') }}/payment" class="btn btn-primary">Пополнить</a>
                                </span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="b-edit-btn">
                <a href="{{route('/')}}/commands" class="btn btn-default">Все команды</a>
            </div>

            <div class="row b-invitation">
                <div class="col">
                    @if($invitation)
                        <span class="b-invitation__link">Ссылка для приглашения: <span>{{ $invitation }}</span></span>
                    @endif
                </div>
            </div>
            <!-- Styles -->
<style>
#chartdiv {
    width   : 100%;
    height  : 500px;
}                                                                   
</style>

<!-- Resources -->
<script src="https://www.amcharts.com/lib/3/amcharts.js"></script>
<script src="https://www.amcharts.com/lib/3/serial.js"></script>
<script src="https://www.amcharts.com/lib/3/plugins/export/export.min.js"></script>
<link rel="stylesheet" href="https://www.amcharts.com/lib/3/plugins/export/export.css" type="text/css" media="all" />
<script src="https://www.amcharts.com/lib/3/themes/light.js"></script>

<!-- Chart code -->
<script>
var chartData = generateChartData();
var chart = AmCharts.makeChart("chartdiv", {
    "type": "serial",
    "theme": "light",
    "marginRight": 80,
    "autoMarginOffset": 20,
    "marginTop": 7,
    "dataProvider": chartData,
    "valueAxes": [{
        "axisAlpha": 0.2,
        "dashLength": 1,
        "position": "left"
    }],
    "mouseWheelZoomEnabled": true,
    "graphs": [{
        "id": "g1",
        "balloonText": "[[value]]",
        "bullet": "round",
        "bulletBorderAlpha": 1,
        "bulletColor": "#FFFFFF",
        "hideBulletsCount": 50,
        "title": "red line",
        "valueField": "visits",
        "useLineColorForBulletBorder": true,
        "balloon":{
            "drop":true
        }
    }],
    "chartCursor": {
       "limitToGraph":"g1"
    },
    "categoryField": "date",
    "categoryAxis": {
        "parseDates": true,
        "axisColor": "#ffffff",
        "dashLength": 1,
        "minorGridEnabled": true
    },
});

chart.addListener("rendered", zoomChart);
zoomChart();

// this method is called when chart is first inited as we listen for "rendered" event
function zoomChart() {
    // different zoom methods can be used - zoomToIndexes, zoomToDates, zoomToCategoryValues
    chart.zoomToIndexes(chartData.length - 40, chartData.length - 1);
}


// generate some random data, quite different range

// generate some random data, quite different range
function generateChartData() {
    var chartData = [];
    var firstDate = new Date();
    firstDate.setDate(firstDate.getDate() - 5);
    var visits = 1200;
    for (var i = 0; i < 1000; i++) {
        // we create date objects here. In your data, you can have date strings
        // and then set format of your dates using chart.dataDateFormat property,
        // however when possible, use date objects, as this will speed up chart rendering.
        var newDate = new Date(firstDate);
        newDate.setDate(newDate.getDate() + i);
        
        visits += Math.round((Math.random()<0.5?1:-1)*Math.random()*10);

        chartData.push({
            date: newDate,
            visits: visits
        });
    }
    return chartData;
}
</script>

<!-- HTML -->
<div id="chartdiv"></div>   

            @if ($isMember || $isOtherCapitan)
            <div class="row block-with-bg long-bg">
                <div class="col b-padding">
                    <div class="b-padding__header">
                        <h2 class="h2">Контакты капитана</h2>
                    </div>
                    <div class="b-padding__body">
                        <div class="b-list-contacts">
                            <div class="b-list-contacts">
                                <span class="g-hidden">
                                    @if (isset($contacts->skype)) {{ $contacts->skype }} @endif
                                    @if (isset($contacts->phone)) {{ $contacts->phone }}@endif
                                    @if (isset($contacts->telegram)) {{ $contacts->telegram }}@endif
                                    @if (isset($contacts->vk)) {{ $contacts->vk }}@endif
                                    @if (isset($contacts->fb)) {{ $contacts->fb }}@endif
                                    @if (isset($contacts->discord)) {{ $contacts->discord }}@endif
                                </span>
                                @if (isset($contacts->skype))
                                    <a title="Skype" href="skype:{{$contacts->skype}}" class="b-list-contacts__item b-list-contacts__item--skype"></a>
                                @endif
                                @if (isset($contacts->telegram))
                                    <a title="Telegram" class="b-list-contacts__item b-list-contacts__item--telegram" href="tg://resolve?domain={{$contacts->telegram}}"></a>
                                @endif
                                @if (isset($contacts->vk))
                                    <a title="VK" target="_blank" href="{{$contacts->vk}}" class="b-list-contacts__item b-list-contacts__item--vk"></a>
                                @endif
                                @if (isset($contacts->discord))
                                    <a title="{{$contacts->discord}}" target="_blank" href="#" class="b-list-contacts__item b-list-contacts__item--discord"></a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <hr>
            @endif

            @if ($count)
            <div class="row">
                <div class="col b-padding">
                    <div class="b-padding__header">
                        <h2 class="h2">Состав команды ({{ $count }}/{{ env('MAX_PLAYERS_IN_COMMAND') }})</h2>
                    </div>
                    <div class="b-padding__body">
                        <ul class="b-users-list">
                            @foreach($players as $player)
                            <li class="b-users-list__item">
                                <a href="{{route('/')}}/profile/{{ $player->id}}">
                                    <div class="b-list-item-wrapper">
                                        <div class="b-list-item__avatar" style="background-image: url({{ $player->avatar }};"></div>
                                    </div>
                                    <h3>{{ $player->username}}</h3>
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            <hr>
            @endif

            <!--<div class="row block-with-bg long-bg">
                <div class="col b-padding">
                    <div class="b-padding__header">
                        <h2 class="h2">Последние турниры команды</h2>
                    </div>
                    <div class="b-padding__body">
                        <p>Пока еще нет сыгранных турниров</p>
                    </div>
                </div>
            </div>

            <hr>-->

            @if ($isMember && $canEdit)
                <div class="row">
                    <div class="col">
                        <a href="{{route('/')}}/commands/edit" class="btn btn-primary">Редактирование команды</a>
                    </div>
                </div>
                <br><br>
            @endif

            @if ($isMember && !$canEdit)
                <div class="row">
                    <div class="col">
                        <a href="{{route('/')}}/commands/exit" class="btn btn-primary">Покинуть команду</a>
                    </div>
                </div>
            @endif

            <div class="g-padding"></div>

    </div>
    

@endsection