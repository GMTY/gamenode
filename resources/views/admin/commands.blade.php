@extends('template.template')

@section('title', 'Управление командами')

@section('content')

	<div class="g-padding"></div>

	<a class="btn btn-default" href="{{route('/')}}/admin">Назад</a><br><br>

	<div class="b-title">
		<h1 class="h1">Управление командами</h1>
	</div>

    @if (session('message'))
        <p class="b-message">{{session('message')}}</p>
    @endif

	<p>Продвинуть <b>бесплатно</b> команду в турнир можно до тех пор, пока не закончилась регистрация на турнир.<br>
	Наложение бана команде запрещает ей подавать заявку на турнир.<br>
	Администраторы могут присваивать победу нужной команде во время проведения этапа турнира.<br>
		Администрация может менять баланс команды на любую сумму
	</p>
	<br>	

	@foreach ($errors->all() as $error)
	<div class="alert alert-danger">
		<ul>
			<li>{{ $error }}</li>
		</ul>
	</div>
	@endforeach

	<table class="table table-bordered table-hover b-table">

		<thead>
			<tr>
				<th>Команда</th>
				<th class="hidden-sm-down">Баланс</th>
				<th>Бан</th>
				<th>Участие в турнире</th>
			</tr>
		</thead>
		@foreach($data as $command)
			<tr>
				<td><a href="{{route('/')}}/commands/{{ $command->command_id_number }}" target="_blank">{{ $command->name }}</a>
					<br><br>
					<form action="{{ route('/') }}/admin/changeqiwi" method="post">
						<input name="id" type="hidden" value="{{ $command->command_id_number }}">
						<input type="text" name="qiwi" class="form-control" value="{{ $command->qiwi }}">
						<br>
						<input type="submit" class="btn btn-default btn-sm" value="Изменить QIWI">
						{{ csrf_field() }}
					</form>
				</td>
				<td class="hidden-sm-down">{{ $command->balance }} рублей
					<br><br>
					<form action="{{ route('/') }}/admin/balance" method="post">
						<input type="number" name="balance" placeholder="500" min="0" max="9999999" class="form-control">
						<input type="hidden" name="commandId" value="{{ $command->command_id_number }}">
						<br>
						<input type="submit" class="btn btn-default btn-sm" value="Установить баланс">
						{{ csrf_field() }}
					</form>
				</td>

				<td>
					@if ($command->tournament_id == NULL)
						@if($command->status === 1)
							<form action="ban" method="post">
								<button class="btn btn-default btn-sm" href="#">Забанить</button>
								<input type="hidden" name="id" value="{{ $command->command_id_number }}">
								{{ csrf_field() }}
							</form>
						@elseif($command->status === 0)
							<form action="unban" method="post">
								<button class="btn btn-default btn-sm btn-danger" href="#">Разбанить</button>
								<input type="hidden" name="id" value="{{ $command->command_id_number }}">
								{{ csrf_field() }}
							</form>
						@endif
					@endif
				</td>
				<td>
					@if ($isRegToTournamentEnded)
						@if($command->tournament_id !== NULL)

							<b>
								@if ($command->result > 0)
									@if ($game == "bo1")
										<span class="text-success">Победа в этапе.</span>
									@else
										<span class="text-success">Побед в серии {{$game}}: {{$command->result}}</span>
									@endif
								@elseif ($command->result === NULL)
									<span class="text-warning">Результата пока нет.</span>
								@else
									@if ($game == "bo1")
										<span class="text-danger">Поражение в этапе.</span>
									@else
										<span class="text-danger">Побед в серии {{$game}}: 0.</span>
									@endif
								@endif
							</b>

							@if ($command->result == env('VALUE_ABSENCE'))
								<br>
								<b class="b-danger">Игроки не пришли на матч</b>
							@endif


							<form action="{{ route('promote') }}" method="POST">
								<button type="submit" value="" name="result" class="btn btn-default btn-sm">Присвоить победу</button>
								<input type="hidden" name="id" value="{{ $command->grids_id }}">
								<input type="hidden" name="commandId" value="{{ $command->command_id_number }}">
								<input type="hidden" name="stageId" value="{{ $command->stage_id }}">
								{{ csrf_field() }}
							</form>
							<small>Команде-сопернику автоматически будет присуждено поражение в этапе</small>

						@else
							Не участвует
						@endif

					@else
						@if (!$command->registered)
							Не участвует
							<form action="{{ route('/') }}/admin/tournament/command/{{ $command->command_id_number }}/add" method="POST">
								<button type="submit" value="" name="add" class="btn btn-default btn-danger btn-sm">Добавить в турнир</button>
								{{ csrf_field() }}
							</form>
							<small>Добавить команду в турнир можно до тех пор, пока не закончилась регистрация на турнир</small>
						@else
							Зарегистрирована на турнир
						@endif
					@endif
				</td>
			</tr>
		@endforeach
	</table>

	{{ $data->links() }}

	<hr>

@endsection