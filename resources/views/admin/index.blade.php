@extends('template.template')

@section('title', 'Административная панель')

@section('content')

	<div class="g-padding"></div>

	<div class="b-title">
		<h1 class="h1">Административная панель</h1>
	</div>

    @if (session('message'))
        <p class="b-message">{{session('message')}}</p>
    @endif

	<div>
		<a class="btn btn-default" href="{{route('/')}}/admin/tournaments">Управление турнирами</a>
		<a class="btn btn-default" href="{{route('/')}}/admin/payments">Выплаты командам</a>
		<a class="btn btn-default" href="{{route('/')}}/admin/commands">Управление командами</a>
		<!--<a class="btn btn-default" href="{{route('/')}}/admin/users">Управление пользователями</a>-->
		<a class="btn btn-default" href="{{route('/')}}/admin/news">Управление новостями</a>
	</div>

	<hr>

@endsection