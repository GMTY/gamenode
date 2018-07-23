@extends('template.template')

@section('title', 'Создание команды')

@section('content')

    <div class="g-padding"></div>

    <div class="b-title">
        <h1 class="h1">Создание команды</h1>
    </div>


    <div class="b-warning">
        <p><span>Внимание!</span> Каждый игрок может быть только в одной команде.</p>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="post" action="{{route('/')}}/commands/save" enctype="multipart/form-data">

        <div class="row">
            <div class="col-lg-6">

                <div class="row">
                    <div class="col-xl-5"><label for="inputSkype">Имя команды</label></div>
                    <div class="col-xl-7"><input type="text" class="form-control" id="inputName" placeholder="Имя команды" name="name" value="{{ old('name')}}">
                        <label class="input-advice">От {{env('MIN_COMMANDNAME_LENGTH')}} до {{env('MAX_COMMANDNAME_LENGTH')}} символов. Допустимые символы: А-Я, A-Z, цифры 0-9</label>
                    </div>
                </div>
                <br>

                <div class="row">
                    <div class="col-xl-5"><label for="inputPhone">Аватар команды</label></div>
                    <div class="col-xl-7"><input type="file" class="form-control" id="inputAvatar" name="avatar" value="">
                        <label class="input-advice">Размер изображения: от {{env('MIN_FILE_HEIGHT')}} до {{env('MAX_FILE_WIDTH')}} пикселей</label>
                    </div>
                </div>
                <br>

                <div class="row">
                    <div class="col-xl-5"><label for="inputPhone">Приветствие команды</label></div>
                    <div class="col-xl-7">
                        <textarea name="greeting" class="form-control" placeholder="Приветствие">{{ old('greeting')}}</textarea>
                    </div>
                </div>
                <br>

                <div class="row">
                    <div class="col-xl-5"><label for="inputPhone">Номер телефона</label></div>
                    <div class="col-xl-7"><input type="text" class="form-control" id="inputQiwi" placeholder="5498980987124" name="qiwi" value="{{ old('qiwi')}}">
                        <label class="input-advice">Самостоятельно поле нельзя изменить! <a href="{{route('/')}}/faq">Подробнее</a></label>
                    </div>
                </div>
                <br>


                <div class="row">
                    <div class="col">
                        <h2 class="h2">Контакты капитана</h2>
                    </div>
                </div>
                <br>

                <div class="row">
                    <div class="col-xl-5"><label for="inputPhone">Discord</label></div>
                    <div class="col-xl-7"><input type="text" class="form-control" id="inputDiscord" name="discord" placeholder="Discord" value="@if(Auth::user()->contacts){{ $discord }}@endif{{ old('discord')}}"></div>
                </div>
                <br>

                <div class="row">
                    <div class="col-xl-5"><label for="inputPhone">Skype</label></div>
                    <div class="col-xl-7"><input type="text" class="form-control" id="inputSkype" name="skype" placeholder="Skype" value="@if(Auth::user()->contacts){{ $skype }}@endif{{ old('skype')}}"></div>
                </div>
                <br>

                <div class="row">
                    <div class="col-xl-5"><label for="inputPhone">Телефон</label></div>
                    <div class="col-xl-7"><input type="text" class="form-control" id="inputPhone" name="phone" placeholder="+7-967-1234567" value="@if(Auth::user()->contacts){{ $phone }}@endif{{ old('phone')}}"></div>
                </div>
                <br>

                <div class="row">
                    <div class="col-xl-5"><label for="inputPhone">Telegram</label></div>
                    <div class="col-xl-7"><input type="text" class="form-control" id="inputTelegram" name="telegram" placeholder="+7-967-1234567" value="@if(Auth::user()->contacts){{ $telegram }}@endif{{ old('telegram')}}"></div>
                </div>
                <br>

                <div class="row">
                    <div class="col-xl-5"><label for="inputPhone">Ссылка VK</label></div>
                    <div class="col-xl-7"><input type="text" class="form-control" id="inputVk" name="vk" placeholder="vk.com" value="@if(Auth::user()->contacts){{ $vk }}@endif{{ old('vk')}}"></div>
                </div>
                <br>

                <div class="row">
                    <div class="col-xl-5"><label for="inputPhone">Ссылка FB</label></div>
                    <div class="col-xl-7"><input type="text" class="form-control" id="inputFb" name="fb" placeholder="facebook.com" value="@if(Auth::user()->contacts){{ $fb }}@endif{{ old('fb')}}"></div>
                </div>

            </div>

            <div class="col-lg-6">
                <!-- здесь будет изменение аватара профиля или команды -->
            </div>
        </div>

        <hr>

        <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
        <button type="submit" class="btn btn-primary">Сохранить</button>
    </form>

    <hr>
    <hr>

@endsection