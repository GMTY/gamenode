/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, {
/******/ 				configurable: false,
/******/ 				enumerable: true,
/******/ 				get: getter
/******/ 			});
/******/ 		}
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 0);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(1);
module.exports = __webpack_require__(2);


/***/ }),
/* 1 */
/***/ (function(module, exports) {


/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

$(function () {

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.datepicker.regional['ru'] = {
        closeText: 'Закрыть',
        prevText: '<Пред',
        nextText: 'След>',
        currentText: 'Сегодня',
        monthNames: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
        monthNamesShort: ['Янв', 'Фев', 'Мар', 'Апр', 'Май', 'Июн', 'Июл', 'Авг', 'Сен', 'Окт', 'Ноя', 'Дек'],
        dayNames: ['воскресенье', 'понедельник', 'вторник', 'среда', 'четверг', 'пятница', 'суббота'],
        dayNamesShort: ['вск', 'пнд', 'втр', 'срд', 'чтв', 'птн', 'сбт'],
        dayNamesMin: ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'],
        weekHeader: 'Не',
        dateFormat: 'yy-mm-dd',
        firstDay: 1,
        isRTL: false,
        showMonthAfterYear: false,
        yearSuffix: '',
        minDate: new Date()
    };
    $.datepicker.setDefaults($.datepicker.regional['ru']);

    $.timepicker.regional['ru'] = {
        timeOnlyTitle: 'Выберите время',
        timeText: 'Время',
        hourText: 'Часы',
        minuteText: 'Минуты',
        secondText: 'Секунды',
        millisecText: 'Миллисекунды',
        timezoneText: 'Часовой пояс',
        currentText: 'Сейчас',
        closeText: 'Закрыть',
        timeFormat: 'HH:mm',
        amNames: ['AM', 'A'],
        pmNames: ['PM', 'P'],
        isRTL: false
    };
    $.timepicker.setDefaults($.timepicker.regional['ru']);

    $(".datepicker").datetimepicker({});

    //$('.js-show-modal-tournament-stage-result').click();
    $('#modal-tournament-stage-result').modal();

    $('.js-tournament-stage-result-btn').click(function (e) {
        e.preventDefault();

        var btn = $(this);
        btn.attr('disabled', 'disabled');

        var id = $('#modal-tournament-stage-result input[name="game-id"]').val();

        $.ajax({
            type: 'POST',
            url: '/tournament/add_game_id',
            data: {
                'id': id
            },
            dataType: 'json',
            success: function success(data) {

                if (data.result == "W" || data.result == "L") {
                    $('.js-tournament-stage-result-error').text('');
                    $('#modal-tournament-stage-result .close').click();
                } else {
                    $('.js-tournament-stage-result-error').text(data.result);
                }
                btn.removeAttr('disabled');
            },
            error: function error(data) {
                $('.js-tournament-stage-result-error').text('Возникла проблема. Попробуйте позже.');
                console.log(data);
                btn.removeAttr('disabled');
            }
        });
    });

    $('.js-get-commands').click(function (e) {
        e.preventDefault();

        var btn = $(this),
            page = btn.data('page'),
            typeSort = $('#sort').val(),
            players = $('#players').val(),
            name = $('#name').val();

        $.ajax({
            type: 'GET',
            url: '/commands/get',
            data: {
                'page': page,
                'players': players,
                'name': name,
                'sort': typeSort
            },
            dataType: 'json',
            success: function success(data) {
                console.log(data);
                var i,
                    template = $('.b-commands-list-template').html(),
                    currentTemplate,
                    commands = data.message.commands.data,
                    html = '';

                if (commands.length == 0) {
                    $('.js-get-commands').attr('disabled', 'disabled');
                    return;
                }

                for (i = 0; i < commands.length; i++) {

                    currentTemplate = template;
                    currentTemplate = currentTemplate.replace(/COMMANDID/gi, commands[i].id);
                    currentTemplate = currentTemplate.replace(/COMMANDAVATAR/gi, commands[i].avatar);
                    currentTemplate = currentTemplate.replace(/COMMANDTITLE/gi, commands[i].name);
                    currentTemplate = currentTemplate.replace(/COMMANDMEMBERS/gi, commands[i].members);
                    currentTemplate = currentTemplate.replace(/COMMANDRATING/gi, commands[i].rating);

                    console.log(currentTemplate);
                    html += currentTemplate;
                }

                $('.b-commands-list').append(html);

                btn.data('page', btn.data('page') + 1);
            },
            error: function error(data) {
                console.log('Error:', data);
            }
        });
    });

    $('#sort, #players, #name').change(function () {
        $('.b-commands-list').html('');
        $('.js-get-commands').data('page', 1).click();
    });

    $('.js-get-profiles').click(function (e) {
        e.preventDefault();
        var btn = $(this);

        var page = btn.data('page');

        $.ajax({
            type: 'GET',
            url: '/profiles/get',
            data: {
                'page': page
            },
            dataType: 'json',
            success: function success(data) {
                console.log(data);
                var i;
                var template = $('.b-users-list-template').html();
                var currentTemplate;
                var users = data.message.users.data;
                var html = '';

                if (users.length == 0) {
                    $('.js-get-profiles').attr('disabled', 'disabled');
                    return;
                }

                for (i = 0; i < users.length; i++) {

                    currentTemplate = template;
                    currentTemplate = currentTemplate.replace(/PROFILEID/gi, users[i].id);
                    currentTemplate = currentTemplate.replace(/PROFILEAVATAR/gi, users[i].avatar);
                    currentTemplate = currentTemplate.replace(/PROFILENAME/gi, users[i].username);

                    console.log(currentTemplate);
                    html += currentTemplate;
                }

                $('.b-users-list').append(html);

                btn.data('page', btn.data('page') + 1);
            },
            error: function error(data) {
                console.log('Error:', data);
            }
        });
    });

    /* подсветка команд в секте */
    $('.b-tournament-group__item').hover(function (e) {
        var id = $(this).data('command');

        if (id) {
            $('.b-tournament-group__item').removeClass('active');
            $('.b-tournament-group__item[data-command=' + id + ']').addClass('active');
        }
    });

    var showColumn = 1;
    var maxColumn = $('.js-max-stage').text();

    /**
     *
     * @param direction
     * @param count - сколько показать колонок
     */
    function moveGrid(count) {

        if (showColumn > maxColumn - 2) {
            showColumn = maxColumn - 2;
        }

        if (showColumn < 1) {
            showColumn = 1;
        }

        $('.js-tournament-grid-header').addClass('g-hidden');
        $('.js-tournament-grid-column').addClass('g-hidden');

        var j = 1;

        for (var i = showColumn; i < showColumn + count; i++) {
            $('.js-tournament-grid-header[data-stage=' + i + ']').removeClass('g-hidden').removeClass('column-1').removeClass('column-2').removeClass('column-3').addClass('column-' + j);

            $('.js-tournament-grid-column[data-stage=' + i + ']').removeClass('g-hidden').removeClass('column-1').removeClass('column-2').removeClass('column-3').addClass('column-' + j);

            j++;
        }
    }

    moveGrid(3);

    $('.js-tournament-grid--left').click(function () {
        showColumn--;
        moveGrid(3);
    });
    $('.js-tournament-grid--right').click(function () {

        showColumn++;
        moveGrid(3);
    });

    $('.b-sidebar-slim-slider').slimscroll({
        height: 'auto'
    });

    $.countdown.setDefaults($.countdown.regionalOptions['ru']);
    $('#counter').countdown({
        until: +$('#untilTime').text(),
        compact: true,
        format: 'dHMS'
    });
});

/***/ }),
/* 2 */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ })
/******/ ]);