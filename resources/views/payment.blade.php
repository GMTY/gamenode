@extends('template.template')

@section('title', 'Правила турнира')

@section ('content')

    <div class="g-padding"></div>

    <div class="b-title">
        <h1 class="h1">Пополнение кошелька команды {{$command->name}}</h1>
    </div>

    <form action="{{route('/')}}/payment/make" method="POST">
        {{ csrf_field() }}

        <div class="row">

            <div class="col-lg-6">

                <div class="row">
                    <div class="col-xl-5"><label>Сумма для пополнения:</label></div>
                    <div class="col-xl-7"><input class="form-control" type="number" name="amount" value="400" placeholder="Сумма"></div>
                </div>

            </div>
        </div>
        <hr>
        <p>Вы будете перенаправлены на страницу оплаты через QIWI-кассу</p>
        <button type="submit" name="submit" class="btn btn-primary">Пополнить</button>
    </form>


@endsection