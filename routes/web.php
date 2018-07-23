    <?php
/*Profile route*/
Route::get('/', 'TournamentController@main')->name('/');
Route::get('/profile', 'profileController@index')->name('profile');
Route::get('/profile/edit', 'profileController@edit')->name('/profiles/edit');
Route::get('/profiles/get', 'profileController@getProfiles')->name('getProfiles');
Route::get('/profiles/list', 'profileController@list')->name('/profiles/list');
Route::get('/profile/{id}', ['uses' => 'profileController@index', 'middleware' => 'checkID'])->name('find');

Route::post('/profile/save', 'profileController@save');

Route::get('/commands/test', 'CommandController@filter');

Route::get('/command', 'CommandController@index')->name('command');
Route::get('/commands', 'CommandController@commands')->name('commands');
Route::get('/commands/get', 'CommandController@getCommands')->name('getCommands');

Route::get('/commands/create', 'CommandController@create')->name('command.create');
Route::get('/commands/edit', 'CommandController@edit');
Route::get('commands/add/{token}', ['uses' => 'CommandController@add_teammate']);
Route::get('/commands/exit', ['uses' => 'CommandController@commandExit']);
Route::get('/commands/remove', ['uses' => 'CommandController@commandRemove']);
Route::get('/commands/{id}', ['uses' => 'CommandController@index', 'middleware' => 'checkID']);

Route::post('/commands/edit', 'CommandController@edit_command');
Route::post('/commands/save', 'CommandController@save');

//Route::post('/tournament/add_game_id', 'TournamentController@add_game_id')->name('add_game_id');

Route::get('/tournament/', 'TournamentController@index')->name('tours');
Route::get('/tournament/schedule', 'TournamentController@schedule')->name('schedule');
Route::post('/tournament/join/', 'TournamentController@join')->name('tournament');

Route::get('/tournament/date', 'TournamentController@date');
Route::get('/tournament/{id}', ['uses' => 'TournamentController@index', 'middleware' => 'checkID']);
Route::get('/tournament/{id}/schedule', ['uses' => 'TournamentController@schedule', 'middleware' => 'checkID']);


Route::get('/payment/', ['uses' => 'QiwiController@payment'])->name('payment');
Route::post('/payment/make', ['uses' => 'QiwiController@make_transaction']);


//Создание сетки турниров
Route::get('/tournament/grid_next', 'TournamentController@grid_next');

//Route::post('/tournament/add_game_id', 'TournamentController@add_game_id')->name('add_game_id');


Route::get('/users/list', function () {
    return view('profile/list');
});

Route::group(['prefix'=>'admin', 'middleware' => ['isAdmin', 'auth']],   function() {

    Route::get('/', 'AdminController@index')->name('admin');
    Route::get('/tournaments', 'AdminController@tournaments');
    Route::get('/tournament/new', 'AdminController@tournament_new')->name('admin_tournament_new');
    Route::post('/tournament/create', 'AdminController@tournament_create')->name('admin_tournament_create');
    Route::post('/tournament/save', 'AdminController@tournament_save'); //TODO на всю админку добавить middleware на проверку доступа (админ из базы админов)
    Route::post('/tournament/save_stages', 'AdminController@tournament_save_stages');
    Route::post('/tournament/command/{id}/add', 'AdminController@tournament_add_command')->middleware('checkID');

    Route::get('/tournament/{id}', ['uses' => 'AdminController@tournament_edit', 'middleware' => 'checkID'])->name('admin_tournament_edit');
    Route::get('/grid', 'AdminController@make_grid')->name('make_grid');

    Route::get('/users', 'AdminController@profiles')->name('admin_profiles');
    Route::get('/commands', 'AdminController@commands')->name('admin_commands');

    Route::get('/payments', 'AdminController@payments')->name('admin_payments');
    Route::get('/payments/got', 'AdminController@listPaymentsDone');
    Route::get('/payments/process', 'PaymentsController@paymentsProcess');

    Route::resource('news','newsController');

    Route::post('balance', 'AdminController@balance');

    Route::post('/ban', 'AdminController@ban')->name('ban');
    Route::post('/unban', 'AdminController@unban')->name('unban');

    Route::post('/promote', 'AdminController@promote')->name('promote');

    Route::post('/changeqiwi', 'AdminController@changeQiwi');

});

Route::get('auth/steam', 'AuthController@login')->name('login');
Route::get('logout', 'AuthController@logout')->name('logout');

Route::get('/about', function () {
    return view('about');
})->name('about');

Route::get('/faq', function () {
    return view('faq');
})->name('faq');

Route::get('/rules', function () {
    return view('rules');
})->name('rules');

Route::get('/qiwitest', 'QiwiController@test');

Route::get('/getReq', function() {
    if( $curl = curl_init() ) {
    $headers[] = 'Content-Type: application/xml';
    curl_setopt($curl, CURLOPT_URL, 'http://localhost:8087/api/program/method');
    curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, "address=8CF68B5FBCE70A4601817566BE77CB927754136DD681E57651C153C718EF4F73&method=test2&args=test2");
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

    $out = curl_exec($curl);
    echo $out;
    curl_close($curl);
  }
  return "Good!";
});

Route::get('/getReqTest', function() {
    if( $curl = curl_init() ) {
    //$headers[] = 'Content-Type: application/xml';
    curl_setopt($curl, CURLOPT_URL, 'http://phpserv.loc/');
    curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, "data=nones");
    //curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

    $out = curl_exec($curl);
    var_dump($out);
    curl_close($curl);
  }
  return "Good!";
});