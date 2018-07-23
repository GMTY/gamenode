@extends('template.template')

@section('title', 'Административная панель')

@section('content')

	<div class="g-padding"></div>

	<div class="b-title">
		<h1 class="h1">Новости проекта</h1>
	</div>

	<a href="{{route('/')}}/admin">Назад</a><br><br>

	@if (session('message'))
        <p class="b-message">{{session('message')}}</p>
    @endif

	<hr>

@endsection