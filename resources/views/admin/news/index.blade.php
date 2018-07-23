@extends('template.template')

@section('title', 'Административная панель')

@section('content')

	<div class="g-padding"></div>

	<a class="btn btn-default" href="{{route('/')}}/admin">Назад</a><br><br>

	<div class="b-title">
		<h1 class="h1">Новости проекта</h1>
	</div>

	<a href="{{route('/')}}/admin/news/create" class="btn btn-default">Добавить новость</a><br>


	<hr>
	<table class="table table-bordered table-hover b-table">

		<thead>
		<tr>
			<th>Новость</th>
			<th>Дата</th>
			<th>Управление</th>
		</tr>
		</thead>

		@foreach($news as $news)
			<tr>
				<td>{{ $news->content }}</td>
				<td>{{ Carbon\Carbon::parse($news->date)->format('d.m.Y') }}</td>
				<td>
					<a href="{{route('/')}}/admin/news/{{ $news->id }}/edit" class="btn btn-default btn-sm">Редактировать</a><br><br>
					<form action="news/{{ $news->id }}" method="POST">
						<input type="hidden" name="_method"  value="DELETE">
						<input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
						<input type="submit" class="btn btn-default btn-danger btn-sm" value="Удалить">
					</form>
				</td>
			</tr>
		@endforeach

	</table>

	<hr>

@endsection