@extends('template.template')

@section('title', 'Редактирование текущего турнира')

@section('content')

	<div class="g-padding"></div>
	<a href="{{route('/')}}/admin" class="btn btn-default">Вернуться</a><br>
	<hr>

	<div class="b-title">
		<h1 class="h2">Редактирование текущего турнира</h1>
	</div>

	@if ($status)
		<p>{{$status}}</p>
	@endif

	@if (session('message'))
		<p class="b-message">{{session('message')}}</p>
	@endif

	<form action="{{ route('/') }}/admin/tournament/save" method="post">
		<div class="row">
			<div class="col-lg-6">

				<input type="hidden" name="id" value="{{$tournament->id}}">

				<div class="row">
					<div class="col-xl-5"><label for="inputTitle">Название турнира</label></div>
					<div class="col-xl-7">
						<input type="text" name="title" value="{{$tournament->title}}" placeholder="Название турнира" id="inputTitle" class="form-control">
					</div>
				</div>
				<br>

				<div class="row">
					<div class="col-xl-5"><label for="input_start">Начало регистрации на турнир</label></div>
					<div class="col-xl-7">
						<input type="text" id="input_start" name="start" value="{{$tournament->start}}" placeholder="Номер этапа турнира" class="form-control datepicker">
					</div>
				</div>
				<br>

				<div class="row">
					<div class="col-xl-5"><label for="input_end">Окончание регистрации на турнир</label></div>
					<div class="col-xl-7">
						<input type="text" id="input_end" name="end" value="{{$tournament->end}}" placeholder="Дата этапа" class="form-control datepicker">
					</div>
				</div>
				<br>

				{{ csrf_field() }}

				<button type="submit" id="inputTitle" class="btn btn-primary">Изменить турнир</button>
				<br>

			</div>
		</div>

	</form>

    <hr>


	<div class="b-title">
		<h1 class="h2">Редактирование этапов турнира</h1>
	</div>

	@if (count($stages))

	<form action="{{ route('/') }}/admin/tournament/save_stages" method="post">
		<div class="row">
			<div class="col-lg-6">

				<input type="hidden" name="id" value="{{$tournament->id}}">

				@foreach ($stages as $stage)
					<div class="row">
						<div class="col-xl-5"><label for="input_start[{{$stage->stage}}]">Дата {{$stage->stage}} этапа</label></div>
						<div class="col-xl-7">
							<input type="text" id="input_start[{{$stage->stage}}]" name="date[{{$stage->stage}}]" value="{{$stage->date}}" placeholder="Дата {{$stage->stage}} этапа" class="form-control datepicker">
						</div>
					</div>
					<br>

					<div class="row">
						<div class="col-xl-5"><label for="inputTitle[{{$stage->stage}}]">Название {{$stage->stage}} этапа</label></div>
						<div class="col-xl-7">
							<input type="text" id="inputTitle[{{$stage->stage}}]" name="title[{{$stage->stage}}]" value="{{$stage->title}}" placeholder="Пример: 1/8 финала, полуфинал" id="inputTitle" class="form-control">
							<label class="input-advice">Максимальная длина {{env('MAX_STAGE_TITLE_LENGTH')}} символов</label>
						</div>
					</div>
					<br>

				@endforeach

				{{ csrf_field() }}

				<button type="submit" id="inputTitle" class="btn btn-primary">Изменить этапы турнира</button>
				<br>

			</div>
		</div>

	</form>

	@else
		<p>Этапы турнира формируются</p>
	@endif

	<hr>

	@if (env('APP_DEBUG'))
	<p>Кнопка ТОЛЬКО для разработчиков!</p>
	<form action="{{ route('/') }}/api/stage/{{env ('CRON_TOURNAMENTS_NEXT_STAGE_PASSWORD')}}" method="POST">
		<button class="btn btn-primary" href="">Создать следующий этап</button><br><br>
	</form>
	@endif
@endsection