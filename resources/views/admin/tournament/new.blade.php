@extends('template.template')

@section('title', 'Создание нового турнира')

@section('content')

	<div class="g-padding"></div>
	<a href="{{route('/')}}/admin" class="btn btn-default">Вернуться</a><br>
	<hr>

	<div class="b-title">
		<h1 class="h1">Создание нового турнира</h1>
	</div>

	@if (session('message'))
		<p class="b-message">{{session('message')}}</p>
	@endif

    @php if (isset($message)) { @endphp
        <p class="b-message">{{$message}}</p>
    @php } @endphp

	<form action="{{ route('admin_tournament_create') }}" method="post">
		<div class="row">
			<div class="col-lg-6">

				<div class="row">
					<div class="col-xl-5"><label for="inputTitle">Название турнира</label></div>
					<div class="col-xl-7">
						<input type="text" name="title" placeholder="Название турнира" id="inputTitle" class="form-control">
					</div>
				</div>
				<br>

                <div class="row">
                    <div class="col-xl-5"><label for="datepicker">Начало регистрации</label></div>
                    <div class="col-xl-7">
                        <input type="text" name="start" placeholder="Начало регистрации" class="form-control datepicker">
                    </div>
                </div>
                <br>

                <div class="row">
                    <div class="col-xl-5"><label for="datepicker">Конец регистрации</label></div>
                    <div class="col-xl-7">
                        <input type="text" name="end" placeholder="Конец регистрации" class="form-control datepicker">
                    </div>
                </div>
                <br>

				{{ csrf_field() }}

				<button type="submit" id="inputTitle" class="btn btn-primary">Создать турнир</button>
				<br>

			</div>
		</div>

	</form>

    <hr>

@endsection