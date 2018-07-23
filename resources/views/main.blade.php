@extends('template.template')

@section('title', 'Главная страница')

@section ('content')

    @include('tournament.banner')
    

    <div class="row block-with-bg long-bg">
        <div class="col b-padding b-tournament-description">
            <div class="b-padding__header">
                <h1 class="h1">Описание</h1>
            </div>
            <div class="b-padding__body">
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Blanditiis ipsam, iste sed dolores, vel mollitia ducimus facilis, explicabo at laboriosam enim animi debitis dolor quidem distinctio consectetur eius inventore aliquam?</p>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Blanditiis ipsam, iste sed dolores, vel mollitia ducimus facilis, explicabo at laboriosam enim animi debitis dolor quidem distinctio consectetur eius inventore aliquam?</p>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Blanditiis ipsam, iste sed dolores, vel mollitia ducimus facilis, explicabo at laboriosam enim animi debitis dolor quidem distinctio consectetur eius inventore aliquam?</p>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Blanditiis ipsam, iste sed dolores, vel mollitia ducimus facilis, explicabo at laboriosam enim animi debitis dolor quidem distinctio consectetur eius inventore aliquam?</p>
            </div>
            <div class="b-tournament-description__bg--fire"></div>
            <div class="b-tournament-description__bg"></div>
        </div>
    </div>

    <hr />

    <div class="row">
        <div class="col-xxl-6 b-padding">
            <div class="b-padding__header">
                <h2>Описание сайта</h2>
            </div>
            <div class="b-padding__body">
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Blanditiis ipsam, iste sed dolores, vel mollitia ducimus facilis, explicabo at laboriosam enim animi debitis dolor quidem distinctio consectetur eius inventore aliquam?</p>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Blanditiis ipsam, iste sed dolores, vel mollitia ducimus facilis, explicabo at laboriosam enim animi debitis dolor quidem distinctio consectetur eius inventore aliquam?</p>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Blanditiis ipsam, iste sed dolores, vel mollitia ducimus facilis, explicabo at laboriosam enim animi debitis dolor quidem distinctio consectetur eius inventore aliquam?</p>
            </div>
        </div>
        <div class="col-xxl-6 b-padding block-with-bg b-main-news">
            <div class="b-padding__header">
                <h2>Новости</h2>
            </div>
            <div class="b-padding__body">
                <ul class="b-news">
                    @foreach($news as $news)
                        <li class="b-news__item">  
                        <time>{{ \Carbon\Carbon::parse($news->date)->format('d.m.Y') }}</time>
                        <p>{{ $news->content }}</p>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    <hr/>
    <hr/>

    <div class="row b-site-advantages">
        <div class="col-xxl-7">
            <div class="b-site-advantages__bg"></div>
        </div>
        <div class="col-xxl-5">
            <ul class="b-advantages">
                <li class="b-advantages__item b-advantages__item--1">
                    <i></i>Найди свою команду и победи
                </li>
                <li class="b-advantages__item b-advantages__item--2">
                    <i></i>Развивай свой рейтинг
                </li>
                <li class="b-advantages__item b-advantages__item--3">
                    <i></i>Стань лучше и смотри за лучшими
                </li>
            </ul>

            <div class="b-tournament">
                <div class="b-tournament-button">
                    <form method="GET" action="/commands">
                        <button type="submit" class="b-tournament-button__btn btn btn-primary">Найти команду</button>
                    </form>
                    <!-- <p class="b-tournament-button__error"><a href="" style="color: #dfdfdf;">Создай свою комманду</a></p> -->
                </div>
            </div>
        </div>
    </div>
    <!-- Button trigger modal -->
<!-- <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
  Open something!
</button> -->


<!-- Modal -->

<div style="margin-top: 100px;" class="modal fade show" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Поздравляем!</h5>
        
        <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button> -->
      </div>
      <div class="modal-body">
        <style>
            .prize-img {
                width: 100px;
                height: 100px;
                position: absolute;
            }

            .prize-text {
                display: inline-block;
                margin-top: 25px;
                margin-left: 120px;
            }
        </style>
        <img class="prize-img" src="{{env('APP_URL')}}/img/prize.png" alt="">
        <p class="prize-text" style="display: inline-block;" >Вы получаете награду за ежедневный вход +10 рейтинга<br>
            <u>Рейтинг записан в блокчейн expload</u></p>
        @if(isset($openPopUp) && $openPopUp === true)
            <p id="open-popup"></p>
        @endif
      </div>
      <div class="modal-footer" style="padding-bottom: 0;">
        <button type="button" class="btn btn-success" data-dismiss="modal">Круто!</button>
      </div>
    </div>
  </div>
</div>
@endsection

