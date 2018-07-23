@extends('template.template')

@section('title', 'Редактирование команды')

@section('content')

    <div class="g-padding"></div>

    <div class="b-title">
        <h1 class="h1">Редактирование команды</h1>
    </div>

    <form method="POST" action="{{route('/')}}/commands/edit" enctype="multipart/form-data">

        <div class="row">
            <div class="col-lg-6">

                <div class="row">
                    <div class="col-xl-5"><label for="inputSkype">Имя команды</label></div>
                    <div class="col-xl-7"><input name="name" type="text" class="form-control" id="inputName" placeholder="Имя команды" value="@if(!$errors->any()){{ $name }}@endif{{old('name')}}" >
                        <label class="input-advice">От {{env('MIN_COMMANDNAME_LENGTH')}} до {{env('MAX_COMMANDNAME_LENGTH')}} символов. Допустимые символы: А-Я, A-Z, цифры 0-9</label>
                    </div>
                </div>
                <br>

                <div class="row">
                    <div class="col-xl-5"><label for="inputPhone">Аватар команды</label></div>
                    <div class="col-xl-7"><input name="avatar" type="file" class="form-control" id="inputAvatar" >
                        <label class="input-advice">Размер изображения: от {{env('MIN_FILE_HEIGHT')}} до {{env('MAX_FILE_WIDTH')}} пикселей</label>
                    </div>
                </div>
                <br>

                <div class="row">
                    <div class="col-xl-5"><label for="inputPhone">Приветствие команды</label></div>
                    <div class="col-xl-7">
                        <textarea rows="7" name="greeting" class="form-control" placeholder="Приветствие">@if(!$errors->any()){{ $greeting }}@endif{{old('greeting')}}</textarea>
                    </div>
                </div>
                <br>

                <div class="row">
                    <div class="col-xl-5"><label for="inputPhone">Номер телефона</label></div>
                    <div class="col-xl-7"><input name="qiwi" type="text" disabled="disabled" class="form-control" id="inputMotto" placeholder="Qiwi" value="{{ $qiwi }}" >
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
                    <div class="col-xl-5"><label for="inputSkype">Discord</label></div>
                    <div class="col-xl-7"><input name="discord" type="text" class="form-control" id="inputDiscord" placeholder="Discord" value="{{ $discord }}"></div>
                </div>
                <br>

                <div class="row">
                    <div class="col-xl-5"><label for="inputSkype">Skype</label></div>
                    <div class="col-xl-7"><input name="skype" type="text" class="form-control" id="inputSkype" placeholder="Skype" value="{{ $skype }}"></div>
                </div>
                <br>

                <div class="row">
                    <div class="col-xl-5"><label for="inputPhone">Телефон</label></div>
                    <div class="col-xl-7"><input name="phone" type="text" class="form-control" id="inputPhone" placeholder="+7-967-1234567" value="{{ $phone }}"></div>
                </div>
                <br>

                <div class="row">
                    <div class="col-xl-5"><label for="inputPhone">Telegram</label></div>
                    <div class="col-xl-7"><input name="telegram" type="text" class="form-control" id="inputTelegram" placeholder="+7-967-1234567" value="{{ $telegram }}"></div>
                </div>
                <br>

                <div class="row">
                    <div class="col-xl-5"><label for="inputPhone">Ссылка VK</label></div>
                    <div class="col-xl-7"><input name="vk" type="text" class="form-control" id="inputVk" placeholder="vk.com" value="{{ $vk }}"></div>
                </div>
                <br>

                <div class="row">
                    <div class="col-xl-5"><label for="inputPhone">Ссылка FB</label></div>
                    <div class="col-xl-7"><input name="fb" type="text" class="form-control" id="inputFb" placeholder="facebook.com" value="{{ $fb }}"></div>
                </div>

            </div>

            <div class="col-lg-6">
                <!-- здесь будет изменение аватара профиля или команды -->
            </div>
        </div>

        <div class="g-padding"></div>

        <div class="row">
            <div class="col b-padding">
                <div class="b-padding__header">
                    <h2 class="h2">Состав команды ({{ $count }}/{{env('MAX_COMMAND_POPULATION')}})</h2>
                </div>
                <div class="b-padding__body">
                    <ul class="b-users-list">
                        @foreach($teammates as $teammate)
                        <li class="b-users-list__item">
                            <div class="b-list-item-wrapper">
                                <div class="b-list-item__avatar" style="background-image: url({{ $teammate->avatar}})"></div>
                            </div>
                            <h3>{{ $teammate->username}}</h3>
                            @if($teammate->id != $capitan)
                                <label>Сделать капитаном? <input type="radio" name="capitan" value="{{ $teammate->id }}"></label><br>
                                <label>Выгнать? <input type="checkbox" name="delete[]" value="{{ $teammate->id }}"></label>
                            @endif
                        </li>    
                        @endforeach
                    </ul>
                 </div>
            </div>
        </div>

        <hr>

        <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
        <button type="submit" class="btn btn-primary">Сохранить</button>
        <hr>

        <div class="row">
            <div class="col">
                <small>Вы можете расформировать команду. При этом все игроки будут удалены из команды, а команда будет заброшена.</small><br><br>
                <a href="{{route('/')}}/commands/remove" class="btn btn-default btn-sm">Расформировать команду</a>
            </div>
        </div>
        <hr>
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