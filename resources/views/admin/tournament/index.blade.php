@extends('template.template')

@section('title', 'Управление турнирами')

@section('content')

	<div class="g-padding"></div>

	<a class="btn btn-default" href="{{route('/')}}/admin">Назад</a><br><br>

	<div class="b-title">
		<h1 class="h1">Управление турнирами</h1>
	</div>

    @if (session('message'))
        <p class="b-message">{{session('message')}}</p>
    @endif

	<br>

	<div>
		<a class="btn btn-default" href="{{route('/')}}/admin/tournament/new">Создать турнир</a>
	</div>

	<br>
	<br>

	<div class="b-title">
		<h2 class="h2">Список всех турниров</h2>
	</div>

	<table class="table table-bordered table-hover b-table">

		@foreach ($tournaments as $tournament)
			<tr>
				<td>
					<a href="{{route('/')}}/admin/tournament/{{$tournament->id}}">{{$tournament->title}}</a>
				</td>
			</tr>
		@endforeach

	</table>


@endsection