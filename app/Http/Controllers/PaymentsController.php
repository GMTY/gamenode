<?php
/**
 * Контроллер занимается исключительно добавлением команды в таблицу для выплат
 * Отмечает команды, которым совершены выплаты
 */
namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;

class PaymentsController extends Controller
{

    /**
     * Функция добавляет команду в таблицу для ожидания выплаты вознаграждения
     * @param int $id - id команды
     * @param $tournament_id - id турнира
     * @param $stage_id - id этапа турнира
     * @return bool - результат добавления в таблицу
     */
    public function addToWaitPayments($id, $tournament_id, $stage_id)
    {

        DB::table('tournaments_payments')
            ->insert([
                'tournament_id' => $tournament_id,
                'stage_id' => $stage_id,
                'command_id' => $id
            ]);

        return true;
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * Устанавливает статус выплаты команде за победы
     */
    public function paymentsProcess(Request $request)
    {
        $data = $request->all();

        DB::table('tournaments_payments')
            ->where('tournament_id', $data['tournament_id'])
            ->where('stage_id', $data['stage_id'])
            ->where('command_id', $data['command_id'])
            ->update([
                'is_paid' => $data['status']
            ]);

        if ($data['status'] == 1) {
            return redirect('admin/payments')->with('message', 'Выплата отмечена');
        }
        else {
            return redirect('admin/payments/got')->with('message', 'Выплата отменена');
        }
    }

}
