@extends('template.template')

@section('title', 'Список команд')

@section('content')

    <div class="g-padding"></div>

    <div class="b-title">
        <h1 class="h1">Команды
            @if (Auth::guest() || !Auth::guest() && !Auth::user()->command)
            <a href="{{ route('/') }}/commands/create" class="btn btn-default btn-right">Зарегистрировать команду</a>
            @endif
        </h1>
    </div>

    @if (session('message'))
        <p class="b-message">{{session('message')}}</p>
        <hr>
    @endif

    <div class="row block-with-bg long-bg">
        <div class="col b-padding">
            <div class="b-padding__header">
                <div class="row">
                    <!--<div class="col">
                        <label for="country">Страна</label>
                        <select class="form-control" name="country" id="country" placeholder="Выберите страну">
                            <option>Россия</option>
                            <option>Беларуссия</option>
                            <option>Казахстан</option>
                        </select>
                    </div>-->
                    <div class="col">
                        <label for="players">Игроков</label>
                        <select class="form-control" name="players" id="players">
                            <option value="0">Любое</option>
                            <option value="6">Ровно 6</option>
                            <option value="5">Ровно 5</option>
                            <option value="4">Ровно 4</option>
                            <option value="3">Ровно 3</option>
                            <option value="2">Ровно 2</option>
                            <option value="1">Ровно 1</option>
                        </select>
                    </div>
                    <div class="col">
                        <label for="sort">Сортировать:</label>
                        <select class="form-control" name="sort" id="sort">
                            <option value="0">По дате регистрации</option>
                            <option value="1">По названию</option>
                            <option value="2">По количеству игроков</option>
                        </select>
                    </div>
                    <div class="col">
                        <label for="sort">Название команды:</label>
                        <input type="text" class="form-control" name="name" id="name" placeholder="Введите название" />

                    </div>
                </div>
            </div>
        </div>
    </div>

    <hr>

    <ul class="b-commands-list">
        @foreach ($commands as $command)
            <li class="b-commands-list__item">
                <a href="{{route('/')}}/commands/{{ $command->id }}">
                    <div class="b-list-item-wrapper">
                        <div class="b-list-item__avatar" style="background-image: url({{route('/')}}/storage/{{ $command->avatar }});"></div>
                    </div>
                    <div class="b-commands-info">
                        <h3 title="{{ $command->name }}">{{ $command->name }}</h3>
                        <!--<p class="b-commands-info__country b-commands-info__country--russia"><i></i> Россия</p>-->
                        <p class="b-commands-info__players"><i></i> {{ $command->members }}</p>
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

    <div class="b-center">
        <button data-page="2" type="submit" class="btn btn-default js-get-commands">Показать еще</button>
    </div>




    <ul class="g-hidden b-commands-list-template">
        <li class="b-commands-list__item">
            <a href="{{route('/')}}/commands/COMMANDID">
                <div class="b-list-item-wrapper">
                    <div class="b-list-item__avatar" style="background-image: url({{route('/')}}/storage/COMMANDAVATAR);"></div>
                </div>

                <div class="b-commands-info">
                    <h3 title="COMMANDTITLE">COMMANDTITLE</h3>
                    <!--<p class="b-commands-info__country b-commands-info__country--russia"><i></i> Россия</p>-->
                    <p class="b-commands-info__players"><i></i> COMMANDMEMBERS</p>
                </div>

                <div class="row b-commands-statistics no-col-padding">
                    <div class="col-sm b-commands-statistics__rating"><i></i> COMMANDRATING <span>Рейтинг команды</span></div>
                    <!--<div class="col-sm b-commands-statistics__fights"><i></i> 124 <span>Количество боев</span></div>
                    <div class="col-sm b-commands-statistics__win"><i></i> 45% <span>Процент побед</span></div>-->
                </div>
            </a>
        </li>
    </ul>

    <hr>
    <hr>

@endsection