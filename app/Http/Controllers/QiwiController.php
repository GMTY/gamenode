<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;

use DB;
use Auth;

class QiwiController extends Controller
{
    //

    /**
     *
     */
    public function payment()
    {
        $id = Auth::user()->id;
        $command_id = Auth::user()->command;
        $command = [];

        if ($command_id) {
            $command = DB::table('commands')
                ->where('id', $command_id)
                ->get();
        }

        if (count($command)) {
            return view('payment',
                [
                    'command' => $command[0]
                ]
            );
        }
        else {
            return redirect('/');
        }

    }

    public function make_transaction(Request $request)
    {
        $id = Auth::user()->id;
        $command_id = Auth::user()->command;
        $command = '';
        $amount = $request->input('amount');
        $trn_id = $id .'-'. time();

        if ($command_id) {
            $command = DB::table('commands')
                ->where('id', $command_id)
                ->get();
        }

        if (count($command)) {

            DB::table('payments_history')
                ->insert(
                    [
                        'trn_id' => $trn_id,
                        'user_id' => $id,
                        'command_id' => $command_id,
                        'amount' => $amount
                    ]
                );

            return redirect(
                "https://bill.qiwi.com/order/external/create.action?txn_id=".$trn_id."&from=".env('QIWI_PROVIDER_ID')."&summ=".$amount."&currency=".env('QIWI_CURRENCY')."&comm=Пополнение счета команды ".$command[0]->name
            );

        }
        else {
            return redirect('/');
        }
    }

    /**
     * Эмулирование посылки уведомления от QIWI на наш сайт,
     * передаем authorization, Basic-авторизацию
     */
    public function test()
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, env('APP_URL') . "/api/qiwi");
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, env('QIWI_REST_PROTOCOL_LOGIN').':'.env('QIWI_REST_PROTOCOL_PASS'));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,
            "bill_id=1-1515597650&status=paid&pay_date=2017%3A11%3A16T11%3A00%3A15&amount=11.00&user=tel%3A%2B79031811737&prv_name=TEST&ccy=RUB&comment=test&command=bill");

        // receive server response ...
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec ($ch);
        echo $server_output;

        curl_close ($ch);
    }

    public function result(Request $request)
    {

        // логин и пароль в QIWI-кабинете
        $login = env('QIWI_REST_PROTOCOL_LOGIN');
        $password = env('QIWI_REST_PROTOCOL_PASS');

        $str = $login.":".$password;
        $credentials = base64_encode($str);
        $credentials = "Basic " . $credentials;

        $basicAuth = $request->header('authorization');

        if ($credentials == $basicAuth) {
            // успешная basic-авторизация

            $amount = $request->input('amount');
            $status = $request->input('status');
            $date = $request->input('pay_date');
            $trn_id = $request->input('bill_id');

            DB::table('payments_history')
                ->where('trn_id', $trn_id)
                ->update([
                    'amount' => $amount,
                    'date' => $date,
                    'status' => $status
                ]);

            if ($status == "paid") {

                $command = DB::table('payments_history')
                    ->where('trn_id', $trn_id)
                    ->select('command_id')
                    ->get();

                DB::table('commands')
                    ->where('id', $command[0]->command_id)
                    ->increment('balance', $amount);

            }

            $content = View::make('qiwixml');

            // возвращаем XML ответ для QIWI-server
            return Response::make($content, '200')->header('Content-Type', 'text/xml');
        }
        else {

            // ошибка в авторизации
            $content = View::make('qiwixmlerror');

            // возвращаем XML ответ для QIWI-server
            return Response::make($content, '200')->header('Content-Type', 'text/xml');
        }

    }


}
