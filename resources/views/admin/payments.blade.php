@extends('template.template')

@section('title', 'Административная панель | Выплаты')

@section('content')

	<div class="g-padding"></div>

	<a class="btn btn-default" href="{{route('/')}}/admin">Назад</a><br><br>

	<div class="b-title">
		<h1 class="h1">Выплаты командам</h1>
	</div>

	<a class="btn btn-default" href="{{route('admin')}}/payments">Ожидают выплаты</a>
	<a class="btn btn-default" href="{{route('admin')}}/payments/got">Выплачено</a><br><br>


	@if (session('message'))
        <p class="b-message">{{session('message')}}</p>
    @endif

	<table class="table table-bordered table-hover b-table table-striped">

		<thead>
		<tr>
			<th>№</th>
			<th>Команда</th>
			<th class="hidden-sm-down">Турнир</th>
			<th>Кол-во побед</th>
			<th>Действие</th>
		</tr>
		</thead>

		@foreach ($payments as $payment)
			<tr>
				<td>{{ $payment->id }}</td>
				<td><a href="{{route('/')}}/commands/{{ $payment->command_id }}">{{ $payment->name }}</a></td>
				<td class="hidden-sm-down"><a href="{{route('/')}}/tournament/{{ $payment->tournament_id }}">{{ $payment->title }}</a></td>
				<td>{{ $payment->stage_id - 1}}</td>
				<td>
					@if ($type == 'wait')
						@if ($payment->is_paid)
							Выплачено
						@else
							<form action="{{route('admin')}}/payments/process" method="GET">
								<input type="hidden" value="1" name="status">
								<input type="hidden" value="{{ $payment->command_id}}" name="command_id">
								<input type="hidden" value="{{ $payment->tournament_id}}" name="tournament_id">
								<input type="hidden" value="{{ $payment->stage_id}}" name="stage_id">
								<button type="submit" href="#" class="btn btn-default btn-sm">Отметить: оплачено</button>
							</form>
						@endif
					@endif

					@if ($type == 'done')
						<form action="{{route('admin')}}/payments/process" method="GET">
							<input type="hidden" value="0" name="status">
							<input type="hidden" value="{{ $payment->command_id}}" name="command_id">
							<input type="hidden" value="{{ $payment->tournament_id}}" name="tournament_id">
							<input type="hidden" value="{{ $payment->stage_id}}" name="stage_id">
							<button type="submit" href="#" class="btn btn-default btn-sm">Отменить выплату</button>
						</form>
					@endif
				</td>
			</tr>
		@endforeach

	</table>


	{{ $payments->links() }}

	<hr>

@endsection