<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <link rel="stylesheet" type="text/css" media="all" href="{{ route('/') }}/css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" media="all" href="{{ route('/') }}/css/jquery-ui.min.css">
        <link rel="stylesheet" type="text/css" media="all" href="{{ route('/') }}/css/jquery-ui-timepicker-addon.css">
        <link rel="stylesheet" type="text/css" media="all" href="{{ route('/') }}/css/app.css">

        <meta property="og:url" content="{{env('APP_URL')}}"/>
        <meta property="og:title" content="Dota2Battles Командный сайт" />
        <meta property="og:description" content="Dota2Battles Командный сайт" />
        <meta property="og:type" content="website" />
        <meta property="og:image" content="{{env('APP_URL')}}/img/vk-img.png" />


        <link rel="shortcut icon" href="{{ route('/') }}/img/favicon.ico" type="image/x-icon">
        <link rel="icon" href="{{ route('/') }}/img/favicon.ico" type="image/x-icon">

        <title>@yield('title') | Dota2Battles.ru</title>

    </head>
    <body>
        
        <div class="wrapper">

            <section class="b-sidebar">
                <div class="b-sidebar-slim-slider">
                <div class="b-sidebar__trigger js-sidebar-trigger"></div>

                <a class="b-sidebar__logo" href="{{route('/')}}">
                    <img style="width: 206px; height: 68px; margin-left: 14%;" src="{{route('/')}}/img/logo.png">
                </a>

                @section('menu')
                <nav class="b-sidebar__menu">
                    <ul class="b-menu">
                        <li class="b-menu__item @if (Request::url() == route('/') ) active @endif">
                            <a href="{{ route ('/') }}">Главная</a>
                        </li>
                        <!-- <li class="b-menu__item @if (Request::url() == route('tours') ) active @endif">
                            <a href="{{ route ('/') }}/tournament">Турнир</a>

                            <ul class="b-submenu">
                                <li class="b-submenu__item">
                                    <a href="{{ route ('/') }}/tournament/schedule">Расписание турнира</a>
                                </li>
                            </ul>

                        </li> -->
                        <li class="b-menu__item @if (Request::url() == route('/profiles/list') ) active @endif">
                            <a href="{{ route ('/') }}/profiles/list">Игроки</a>
                        </li>
                        <li class="b-menu__item @if (Request::url() == route('commands') ) active @endif">
                            <a href="{{ route ('/') }}/commands">Команды</a>

                            <ul class="b-submenu">
                                @if (!Auth::guest())
                                    @if (Auth::user()->command)
                                    <li class="b-submenu__item @if (Request::url() == route('command') ) active @endif">
                                        <a href="{{ route ('/') }}/command">Моя команда</a>
                                    </li>
                                    @else
                                    <li class="b-submenu__item @if (Request::url() == route('command.create') ) active @endif">
                                        <a href="{{ route ('/') }}/commands/create">Создать команду</a>
                                    </li>
                                    @endif
                                @else
                                    <li class="b-submenu__item @if (Request::url() == route('command.create') ) active @endif">
                                        <a href="{{ route ('/') }}/commands/create">Создать команду</a>
                                    </li>
                                @endif

                            </ul>

                        </li>
                        <li class="b-menu__item @if (Request::url() == route('profile') ) active @endif">
                            <a href="{{ route ('/') }}/profile">Профиль</a>
                        </li>
                        <!-- А -->
                        <!-- <li class="b-menu__item @if (Request::url() == route('faq') ) active @endif">
                            <a href="{{ route ('/') }}/faq">F.A.Q.</a>
                        </li>
                        <li class="b-menu__item @if (Request::url() == route('about') ) active @endif">
                            <a href="{{ route ('/') }}/about">Контакты</a>
                        </li> -->
                    </ul>
                </nav>
                @show


                <div class="b-sidebar__footer">
                    <ul class="soc-icons">
                        <li class="soc-icons__item">
                            <a href="#" class="soc-icons__item--vk"></a>
                        </li>
                        <li class="soc-icons__item">
                            <a href="#" class="soc-icons__item--inst"></a>
                        </li>
                    </ul>

                    <!--<div class="b-pay-ways">
                        <span>Мы принимаем оплату через ONPAY <span class="b-pay-ways__onpay"></span></span>
                    </div>-->

                </div>
                </div>
            </section>

            <section class="b-main-content">
                @section('login')
                    <div class="b-login">

                        @if (Auth::guest())
                            <a class="btn btn-primary" href="{{ route ('login') }}">Войти через Steam</a>
                        @else
                            <div class="b-login-user">
                                <div class="b-login-user__avatar" style="background-image: url('{{ Auth::user()->avatar }}');"></div>

                                <div class="b-login-user__name">
                                    {{ Auth::user()->username }}
                                    <p class="b-login-user__name--status">online</p>
                                </div>

                                <a href="{{route('/')}}/logout" title="Выйти" class="b-login-user__menu"></a>

                            </div>
                        @endif
                    </div>
                @show

                <div class="container">

                    @yield('content')

                </div>
            </section>

        </div>

        <!-- @if(isset(Auth::user()->id) && isset($popup) && $popup === true)
        @section('tournament-stage-result') -->
        <!--<button type="button" class="js-show-modal-tournament-stage-result btn btn-primary" data-toggle="modal" data-target="#modal-tournament-stage-result">Open</button>-->


        <!-- <div class="modal fade" id="modal-tournament-stage-result" tabindex="-1" role="dialog" aria-labelledby="modal-tournament-stage-result" aria-hidden="false">
            <div class="modal-dialog" role="document">

                <div class="row block-with-bg">
                    <div class="col b-padding">
                        <div class="b-padding__header">
                            <h2 class="h2">Закончился очередной этап турнира. Укажите ID вашей игры!</h2>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="b-padding__body">
                            <form method="POST" action="{{ route('add_game_id')}}">
                                {{ csrf_field() }}
                                <div class="form-group">
                                    <input type="text"  class="form-control" name="game-id" placeholder="SteamID вашей игры" >
                                </div>
                                <div class="modal-footer center">
                                    <button type="submit" class="btn btn-primary js-tournament-stage-result-btn">Отправить SteamID</button>
                                </div>
                                <label class="js-tournament-stage-result-error"></label>
                            </form>

                            <form method="POST" action="{{ route('/')}}/tournament/technical_defeat">
                                {{ csrf_field() }}
                                <div class="modal-footer center">
                                    <button type="submit" class="btn btn-default btn-danger btn-sm">Матч не состоялся</button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div> -->
        <!-- @show
        @endif -->

        <script type="text/javascript" src="{{route('/')}}/js/jquery-3.2.1.min.js"></script>
        <script type="text/javascript" src="{{route('/')}}/js/jquery-ui.min.js"></script>
        <script type="text/javascript" src="{{route('/')}}/js/jquery-ui-timepicker-addon.js"></script>
        <script type="text/javascript" src="{{route('/')}}/js/popper.min.js"></script>
        <script type="text/javascript" src="{{route('/')}}/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="{{route('/')}}/js/jquery.slimscroll.js"></script>
        <script type="text/javascript" src="{{route('/')}}/js/jquery.plugin.min.js"></script>
        <script type="text/javascript" src="{{route('/')}}/js/jquery.countdown.min.js"></script>
        <script type="text/javascript" src="{{route('/')}}/js/jquery.countdown-ru.js"></script>
        <script type="text/javascript" src="{{route('/')}}/js/app.js"></script>
        <script type="text/javascript" src="{{route('/')}}/js/script.js"></script>
    </body>
</html>
