@extends('template.template')

@section('title', 'Изменение профиля')

@section('content')

    <div class="g-padding"></div>

    <div class="b-title">
        <h1 class="h1">Изменение моих контактов</h1>
    </div>

    <form method="post" action="{{route('/')}}/profile/save" >

        <div class="row">
            <div class="col-lg-6">

                <div class="row">
                    <div class="col-xl-5"><label for="inputSkype">Skype</label></div>
                    <div class="col-xl-7"><input type="text" class="form-control" name="skype" placeholder="Skype" value="@if(Auth::user()->contacts && !$errors->any()){{ $skype }}@endif{{old('skype')}}"></div>
                </div>
                <br>

                <div class="row">
                    <div class="col-xl-5"><label for="inputPhone">Телефон</label></div>
                    <div class="col-xl-7"><input type="text" class="form-control" name="phone" placeholder="+7-967-1234567" value="@if(Auth::user()->contacts && !$errors->any()){{ $phone }}@endif{{old('phone')}}"></div>
                </div>
                <br>

                <div class="row">
                    <div class="col-xl-5"><label for="inputPhone">Telegram</label></div>
                    <div class="col-xl-7"><input type="text" class="form-control" name="telegram" placeholder="+7-967-1234567" value="@if(Auth::user()->contacts && !$errors->any()){{ $telegram }}@endif{{old('telegram')}}"></div>
                </div>
                <br>

                <div class="row">
                    <div class="col-xl-5"><label for="inputPhone">Ссылка VK</label></div>
                    <div class="col-xl-7"><input type="text" class="form-control" name="vk" placeholder="+7-967-1234567" value="@if(Auth::user()->contacts && !$errors->any()){{ $vk }}@endif{{old('vk')}}"></div>
                </div>
                <br>

                <div class="row">
                    <div class="col-xl-5"><label for="inputPhone">Ссылка FB</label></div>
                    <div class="col-xl-7"><input type="text" class="form-control" name="fb" placeholder="facebook.com" value="@if(Auth::user()->contacts && !$errors->any()){{ $fb }}@endif{{old('fb')}}"></div>
                </div>
                <br>

                <div class="row">
                    <div class="col-xl-5"><label for="inputPhone">Discord</label></div>
                    <div class="col-xl-7"><input type="text" class="form-control" name="discord" placeholder="discord" value="@if(Auth::user()->contacts && !$errors->any()){{ $discord }}@endif{{old('discord')}}"></div>
                </div>
                <br>

                <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />

                <button type="submit" class="btn btn-primary">Сохранить</button>

            </div>

            <div class="col-lg-6">
                <!-- здесь будет изменение аватара профиля или команды -->
            </div>
        </div>

    </form>
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
@endsection