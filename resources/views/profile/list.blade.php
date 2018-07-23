@extends('template.template')

@section('title', 'Список игроков')

@section('content')

    <div class="g-padding"></div>

    <div class="b-title">
        <h1 class="h1">Список игроков Dota2</h1>
    </div>

    <div class="row block-with-bg long-bg" style="background-color: rgba(255, 255, 255, 0.5);">
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
                        <label for="players">Количество рейтинга</label>
                        <select class="form-control" name="players" id="players">
                            <option value="0">Любое</option>
                            <option value="6">1000 <</option>
                            <option value="5">2000 <</option>
                            <option value="4">3000 <</option>
                            <option value="3">4000 <</option>
                            <option value="2">5000 <</option>
                            <option value="1">6000 <</option>
                        </select>
                    </div>
                    <div class="col">
                        <label for="sort">Сортировать:</label>
                        <select class="form-control" name="sort" id="sort">
                            <option value="0">По дате регистрации</option>
                            <option value="1">Имени</option>
                            <option value="2">Возрасту</option>
                        </select>
                    </div>
                    <div class="col">
                        <label for="sort">Имя игрока:</label>
                        <input type="text" class="form-control" name="name" id="name" placeholder="Имя" />
                    </div>
                    <div class="col">
                        <label for="sort">Возраст от:</label>
                        <input type="text" class="form-control" name="name" id="name" placeholder="0" />
                    </div>
                    <div class="col">
                        <label for="sort">Возраст до:</label>
                        <input type="text" class="form-control" name="name" id="name" placeholder="&infin;" />
                    </div>
                </div>
            </div>
        </div>
    </div><br><br>

    <ul class="b-users-list">
        @foreach ($users as $user)
        <li class="b-users-list__item">
            <a href="{{route('/')}}/profile/{{$user->id}}">
                <div class="b-list-item-wrapper">
                    <div class="b-list-item__avatar" style="background-image: url({{$user->avatar}});"></div>
                </div>
                <h3>{{$user->username}}</h3>
                <h3>Райтинг: {{ $user->rating }}</h3>
            </a>
        </li>
        @endforeach
    </ul>

    <div class="b-center">
        <button data-page="2" type="submit" class="btn btn-default js-get-profiles">Показать еще</button>
    </div>


    <ul class="g-hidden b-users-list-template">
        <li class="b-users-list__item">
            <a href="{{route('/')}}/profile/PROFILEID">
                <div class="b-list-item-wrapper">
                    <div class="b-list-item__avatar" style="background-image: url(PROFILEAVATAR);"></div>
                </div>
                <h3>PROFILENAME</h3>
                <!--<p>Турниров: 3</p>
                <p>Процент побед игрока: 45%</p>-->
            </a>
        </li>
    </ul>

    <hr>
    <hr>

@endsection