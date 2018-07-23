@extends('template.template')

@section('title', 'Расписание турнира')

@section('content')

    @include('tournament.banner')

    <div class="b-schedule-container">
        <div class="row block-with-bg long-bg no-col-padding">
            <div class="col b-padding">
                <div class="b-padding__header">
                    <h1 class="h1">Расписание турнира
                        <a href="{{ route('/') }}/rules" class="btn btn-default btn-right">Правила турнира</a>
                    </h1>
                </div>
                <div class="b-padding__body b-tournament-schedule">
                    @if ($reg_start)
                        <div class="row no-col-padding b-tournament-schedule__item @if($current_stage == -1) selected @endif">
                            <div class="col-md-5">Начало регистрации</div>
                            <div class="col-md-5">{{ Carbon\Carbon::parse($reg_start)->format('d.m.Y') }}</div>
                            <div class="col-md-2 b-center">{{ Carbon\Carbon::parse($reg_start)->format('H:i') }}</div>
                        </div>
                    @endif
                    @if ($reg_end)
                        <div class="row no-col-padding b-tournament-schedule__item @if($current_stage == 0) selected @endif">
                            <div class="col-md-5">Конец регистрации</div>
                            <div class="col-md-5">{{ Carbon\Carbon::parse($reg_end)->format('d.m.Y') }}</div>
                            <div class="col-md-2 b-center">{{ Carbon\Carbon::parse($reg_end)->format('H:i') }}</div>
                        </div>
                    @endif

                    @foreach ($stages as $stage)
                        <div class="row no-col-padding b-tournament-schedule__item @if($current_stage == $stage->stage) selected @endif">
                            <div class="col-md-5">{{$stage->title}}</div>
                            <div class="col-md-5">{{ Carbon\Carbon::parse($stage->date)->format('d.m.Y') }}</div>
                            <div class="col-md-2 b-center">{{ Carbon\Carbon::parse($stage->date)->format('H:i') }}</div>
                        </div>
                    @endforeach
                </div>

            </div>
        </div>

        <div class="b-tournament-warning">
            <span>Время московское!</span>
            Если команды нет <span>в течение 20 минут</span> после начала турнира, то команде будет присуждено техническое поражение
        </div>

        <div class="b-tournament">
            <div class="b-center b-tournament-button">
                <form method="POST" action="/tournament/join">
                    {{ csrf_field() }}
                    <button type="submit" @if (isset($join_disabled)) {{$join_disabled}} @endif class="b-tournament-button__btn btn btn-primary">Зарегистрироваться</button>
                </form>
                <p class="b-tournament-button__error">@if (isset($join_error)) {{$join_error}} @endif</p>
            </div>
        </div>
    </div>

    <hr>
    <hr>
    
    <h2 class="h2 g-relative">Зарегистрированные команды ({{ $count }} из {{env('MAX_COUNT_COMMAND')}})</h2>
    <br>

    @if (!count($commands))
        <p>Пока что нет зарегистрированных команд.</p>
    @endif

    <ul class="b-commands-list with-bg">
        @foreach ($commands as $command)
        <li class="b-commands-list__item">
            <a href="{{route('/')}}/commands/{{ $command->id }}">
                <div class="b-list-item-wrapper">
                    <div class="b-list-item__avatar" style="background-image: url({{route('/')}}/storage/{{ $command->avatar }});"></div>
                </div>
                <div class="b-commands-info">
                    <h3 title="{{ $command->name }}">{{ $command->name }}</h3>
                    <p class="b-commands-info__country b-commands-info__country--russia"><i></i> Россия</p>
                    <p class="b-commands-info__players"><i></i> {{ $command->members }} <!--<span>online</span>--></p>
                </div>

                <div class="row b-commands-statistics no-col-padding">
                    <div class="col-sm b-commands-statistics__rating"><i></i> {{ $command->rating }} <span>Рейтинг команды</span></div>
                    <!--<div class="col-sm b-commands-statistics__fights"><i></i> 124 <span>Количество боев</span></div>
                    <div class="col-sm b-commands-statistics__win"><i></i> 45% <span>Процент побед</span></div>-->
                </div>
            </a>
        </li>
        @endforeach
    </ul>

    <hr>
    <hr>

@endsection