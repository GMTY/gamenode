@extends('template.template')

@section('title', 'Страница турнира')

@section('content')

	@include('tournament.banner')

	<div class="b-title">
		<h1 class="h1">{{ $info->title }}<a href="{{ route('/') }}/rules" class="btn btn-default btn-right">Правила турнира</a></h1>
    </div>

	<p>Турнир будет проходить раз в 4 дня, на протяжении 3 дней команды играют между собой по системе single elimination. Все игры проходят в формате best of 1 кроме полуфинала и финала best of 3 и best of 5 соответственно.</p>
	<p>Призовой фонд формируется взносами команд ({{env('TOURNAMENT_COST')}} рублей с команды / {{env('TOURNAMENT_COST') / 5}} рублей с человека), максимальное количество команд 128.</p>
	<p>Главной особенностью турнира является то, что вы отбиваете деньги после двух побед на турнире. Все призовые выплачиваются капитанам команд по мере выбывания из турнира. Дополнительную информацию о турнире вы можете найти в разделе <a href="{{route('rules')}}">"Описание турнира"</a></p>

	<hr>

	<div class="row">
		<!--<div class="col-lg-5">
			<div class="b-title">
				<h2 class="h2 g-strong">Сыгранные игры</h2>
			</div>

			<div class="b-past-games">
				<div class="row">
					<div class="col-5">
						<div class="b-list-item-wrapper">
							<div class="b-list-item__avatar" style="background-image: url(../img/logo.png);"></div>
						</div>
						<h4>Team_Kaliber</h4>
					</div>
					<div class="col-2 b-past-games__VS">VS</div>
					<div class="col-5">
						<div class="b-list-item-wrapper">
							<div class="b-list-item__avatar" style="background-image: url(../img/logo.png);"></div>
						</div>
						<h4>Team_Kaliber</h4>
					</div>
				</div>
				<div class="b-past-games__result">
					1 : 0
				</div>
			</div>

		</div>
		-->
		@if ($enemy_command)
			<div class="col-lg-5">
				<div class="b-title">
					@if ($time > 0)
						<h2 class="h2 g-strong">Вам предстоит игра с командой</h2>
					@else
						<h2 class="h2 g-strong">Началась игра с командой</h2>
					@endif
				</div>

				<div class="b-next-game">
					<div class="b-next-game__info">
						<div class="b-list-item-wrapper">
							<div class="b-list-item__avatar" style="background-image: url(../img/logo.png);"></div>
						</div>

						<div class="b-commands-info">
							<h2 class="b-commands-info__name"><a href="{{route('/')}}/commands/{{$enemy_command[0]->id}}">{{$enemy_command[0]->name}}</a></h2>
							<p class="b-commands-info__composition">Состав команды:</p>
							<p class="b-commands-info__composition-list">
								@foreach($enemy_command as $player)
									<a href="{{route('/')}}/profile/{{$player->id}}">{{$player->username}}</a>
								@endforeach
							</p>
						</div>
						<div class="g-clear"></div>
					</div>
					<div class="b-next-game__timer">
						<div class="row">
							<div class="col">До начала<br>игры осталось:</div>
							<div class="g-hidden" id="untilTime">{{ $time }}</div>
							<div class="col" id="counter"></div>
						</div>
					</div>
				</div>
			</div>
		@endif
	</div>

	<hr>

	@if ($commands)
	<div class="js-max-stage g-hidden">{{count($stages)}}</div>

	<div class="b-title">
		<h2 class="h2 g-strong">Турнирная сетка</h2>
	</div>

	<div class="row block-with-bg long-bg">
		<div class="col b-padding">
			<div class="b-padding__header">
				<div class="b-tournament-grid b-tournament-grid--header">

					<div class="arrow-left js-tournament-grid--left"></div>
					<div class="arrow-right js-tournament-grid--right"></div>
					@foreach($stages as $stage)
					<div class="b-tournament-grid__col js-tournament-grid-header g-hidden @if ($stage->stage == $currentStage) active @endif" id="js-tournament-grid-header-{{$stage->stage}}" data-stage="{{$stage->stage}}">
						{{$stage->title}}<br>
						Начало: {{ Carbon\Carbon::parse($stage->date)->format('d.m.Y') }}
						в {{ Carbon\Carbon::parse($stage->date)->format('H:i') }}
					</div>
					@endforeach
					<div class="g-clear"></div>
				</div>
			</div>
			<div class="b-padding__body">

				<div class="b-tournament-grid">

					@for ($i = 0; $i < count($commands); $i++)
					<div class="b-tournament-grid__col js-tournament-grid-column g-hidden" id="js-tournament-grid-column-{{$i + 1}}" data-stage="{{$i + 1}}">

						@foreach($commands[$i] as $command)

							@php
							if ($command->order % 2 == 0) {
							@endphp
							<div class="b-tournament-group">

								<a @if ($command->id) href="{{route('/')}}/commands/{{$command->id}}" @endif class="b-tournament-group__item" data-command="{{$command->id}}">

									<div class="b-tournament-command @if ($command->result < 1) fail @endif">
										<div class="b-tournament-command__img">
											<div class="b-tournament-command-img-wrapper" style="@if ($command->avatar) background-image: url({{route('/')}}/storage/{{$command->avatar}}); @endif"></div>
										</div>
										<div class="b-tournament-command__title">{{$command->name}}</div>
										<div class="b-tournament-command__status">@if ($command->result > 0) {{$command->result}} @else 0 @endif</div>
									</div>

								</a>
							@php
							}
							else {
							@endphp
								<a @if ($command->id) href="{{route('/')}}/commands/{{$command->id}}" @endif class="b-tournament-group__item" data-command="{{$command->id}}">

									<div class="b-tournament-command @if ($command->result < 1) fail @endif">
										<div class="b-tournament-command__img">
											<div class="b-tournament-command-img-wrapper" style="@if ($command->avatar) background-image: url({{route('/')}}/storage/{{$command->avatar}});); @endif"></div>
										</div>
										<div class="b-tournament-command__title">{{$command->name}}</div>
										<div class="b-tournament-command__status">@if ($command->result > 0) {{$command->result}} @else 0 @endif</div>
									</div>

								</a>

							</div>
							@php
							}

							@endphp

						@endforeach


					</div>
					@endfor

					<!-- образец столбца сетки
					<div class="b-tournament-grid__col">


						<div class="b-tournament-group">

							<div class="b-tournament-group__item">

								<div class="b-tournament-command">
									<div class="b-tournament-command__img">
										<div class="b-tournament-command-img-wrapper" style="background-image: url({{route('/')}}/img/logo.png);"></div>
									</div>
									<div class="b-tournament-command__title">Название</div>
									<div class="b-tournament-command__status">1</div>
								</div>

							</div>

							<div class="b-tournament-group__item">

								<div class="b-tournament-command">
									<div class="b-tournament-command__img">
										<div class="b-tournament-command-img-wrapper" style="background-image: url({{route('/')}}/img/logo.png);"></div>
									</div>
									<div class="b-tournament-command__title">Название</div>
									<div class="b-tournament-command__status">1</div>
								</div>

							</div>

						</div>


					</div>
					-->

				</div>
			</div>
		</div>
	</div>
	@endif


	<hr>
	<hr>
@endsection