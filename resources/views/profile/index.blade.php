@extends('template.template')

@section('title', 'Профиль игрока')

@section('content')

    <div class="g-padding"></div>

    <div class="b-page-wrapper">

        <div class="row">
            <div class="col">
                <div class="b-profile">
                    <div class="b-profile__img" style="background-image: url({{ $avatar }})"></div>
                    <div class="b-profile__info">
                        <h2>{{ $username }}
                            <a href="http://steamcommunity.com/profiles/{{ $steamid }}" target="_blank" class="steam"><i></i></a>
                        </h2>
                        <p class="command">Команда: <strong>@if($command) <a href="{{route('/')}}/commands/{{$commandID}}">{{ $command }}</a> @else Нет команды @endif</strong></p>
                        <!--<p class="online-status">Online</p>-->
                        @if (!$hide)
                        <div class="b-list-contacts">
                            <span class="g-hidden">
                                @if($skype) {{ $skype }} @endif
                                @if($phone) {{ $phone }}@endif
                                @if($telegram) {{ $telegram }}@endif
                                @if($vk) {{ $vk }}@endif
                                @if($fb) {{ $fb }}@endif
                            </span>
                            @if($skype)
                                <a title="Skype" href="skype:{{$skype}}" class="b-list-contacts__item b-list-contacts__item--skype"></a>
                            @endif
                            @if($telegram)
                                <a title="Telegram" class="b-list-contacts__item b-list-contacts__item--telegram" href="tg://resolve?domain={{$telegram}}"></a>
                            @endif
                            @if($vk)
                                <a title="VK" target="_blank" href="{{$vk}}" class="b-list-contacts__item b-list-contacts__item--vk"></a>
                            @endif
                            @if($discord)
                                <a title="{{$discord}}" target="_blank" href="#" class="b-list-contacts__item b-list-contacts__item--discord"></a>
                            @endif
                        </div>
                        @endif
                        <!--<span class="short-stat">Сыграно игр: <strong>33</strong></span> <span class="short-stat">Побед: <strong>57%</strong></span>-->
                        <span class="short-stat">Рейтинг: <strong>{{$rating}}</strong></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="b-edit-btn">
            @if ($auth)
                <a href="{{route('/')}}/profile/edit" class="btn btn-default">Редактировать профиль</a>
            @endif
        </div>

        @if ($hide)
            <div class="row">
                <div class="col">
                    <h2 class="g-hidden-info h2">Информация о пользователе скрыта</h2>
                </div>
            </div>

        @else

            <hr>
            <div class="b-tournament-container">
                <div class="row block-with-bg long-bg no-col-padding">
                    <div class="col b-padding">
                        <div class="b-padding__header">
                            <h1 class="h1">История платежей</h1>
                        </div>
                        @if (count($payments))
                        <div class="b-padding__body b-tournament-schedule">
                            <div class="row no-col-padding b-tournament-schedule__item b-tournament-schedule__head">
                                <div class="col-md-3">Дата</div>
                                <div class="col-md-3">Время</div>
                                <div class="col-md-2">Сумма (руб.)</div>
                                <div class="col-md-4">Назначение платежа</div>
                            </div>

                            @foreach ($payments as $payment)
                                <div class="row no-col-padding b-tournament-schedule__item">
                                    <div class="col-md-3">{{ Carbon\Carbon::parse($payment->date)->format('d.m.Y') }}</div>
                                    <div class="col-md-3">{{ Carbon\Carbon::parse($payment->date)->format('H:i') }}</div>
                                    <div class="col-md-2"><strong>{{ $payment->amount }}</strong></div>
                                    <div class="col-md-4">Пополнение баланса команды {{ $payment->command_name }}</div>
                                </div>
                            @endforeach
                        </div>
                        @else
                        <div class="b-padding__body b-tournament-schedule-schedule">
                            <p>Вы еще не совершали платежи</p>
                        </div>
                        @endif

                    </div>
                </div>
            </div>

        @endif


        <!--<div class="row block-with-bg long-bg">
            <div class="col b-padding">
                <div class="b-padding__header">
                    <h2 class="h2">Последние турниры игрока</h2>
                </div>
                <div class="b-padding__body">
                    <p>Пока еще нет сыгранных турниров</p>
                </div>
            </div>
        </div>-->

    </div>

@endsection