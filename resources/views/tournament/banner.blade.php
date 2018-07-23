<div class="b-tournament">
	<div class="b-tournament__cover"></div>

	<div class="b-tournament__info">

		<div class="b-tournament-info">
			<h2 class="b-tournament-info__title">Найди команду своей мечты</h2>

			<div class="row no-col-padding">
				<div class="col-md">
					<!-- <p class="b-tournament-info__prize">Призовой фонд:
						<span class="b-tournament-info__prize--money">{{ round($prize) }} руб.</span>
					</p> -->
				</div>
				<div class="col-md">
					<div class="b-tournament__button">
						<div class="b-tournament-button">
							<form method="GET" action="/commands">
								{{ csrf_field() }}
								<button type="submit" class="b-tournament-button__btn btn btn-primary">Найти команду</button>
							</form>
							<p class="b-tournament-button__error"><a href="" style="color: #dfdfdf;">Создай свою комманду</a></p>
						</div>
					</div>
				</div>
			</div>
		</div>

	</div>
</div>

<hr />