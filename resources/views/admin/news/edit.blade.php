@extends('template.template')

@section('title', 'Административная панель')

@section('content')

	<div class="g-padding"></div>
	<a class="btn btn-default" href="{{route('/')}}/admin/news">Назад</a><br><br>

	<div class="b-title">
		<h1 class="h1">Редактирование новости</h1>
	</div>

	@if (session('message'))
		<p class="b-message">{{session('message')}}</p>
	@endif

	<form action="/admin/news/{{ $news->id }}" method="post">
		<input type="hidden" name="_method"  value="PUT">

		<div class="row">
			<div class="col-lg-6">

				<div class="row">
					<div class="col-xl-5"><label for="inputTitle">Текст новости</label></div>
					<div class="col-xl-7">
						<textarea name="content" id="inputContent" class="form-control" placeholder="Текст">{{ $news->content }}</textarea>
					</div>
				</div>
				<br>

				<div class="row">
					<div class="col-xl-5"><label for="inputContent">Дата новости</label></div>
					<div class="col-xl-7">
						<input type="text" name="date-publish" placeholder="Дата этапа" class="form-control datepicker" value="{{ $news->date }}">
					</div>
				</div>
				<br>

				{{ csrf_field() }}

				<button type="submit" class="btn btn-primary">Сохранить</button>

			</div>
		</div>

	</form>

@endsection