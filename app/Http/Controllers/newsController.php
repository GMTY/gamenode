<?php

namespace App\Http\Controllers;

use App\News;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Carbon\Carbon;


class NewsController extends Controller
{

    /**
     * @return view news.index
     * Возвращает страницу с новостями
    */
    public function index()
    {
        $data = [
            'news' => DB::table('news')
                ->where('status','1')
                ->orderBy('id','desc')
                ->get()
            ];
        return view('admin.news.index', $data);
    }

    /**
     * @return view news.create
     * Возвращает страницу с созданием новости
     */
    public function create()
    {
        return view('admin.news.create');
    }

    /**
     * @param  content - содержимое новости, date - дата новости
     * @return view news.index
     * Сохраняет новость в БД
     */
    public function store(Request $request)
    {

        $content = $request->input('content');

        if($content === NULL){
            return redirect('admin/news/create')->with('message', 'Не введен текст новости');
        }

        $date = $request->input('date-publish');

        if($date === NULL){
            $date = Carbon::now();
        }

        $DB = News::create([
            'content' => $content,
            'date' => $date
        ]);

        if($DB) {
            return redirect('admin/news');
        }

        return 'Ошибка!';
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
    }

    /**
     * @param  int  $id - новости которую нужно редактировать
     * @return view news.index
     * Возвращает вид с редактированием новости
     */
    public function edit($id)
    {
        $news = News::where('id',$id)
            ->first();
        $data = [
            'news' => $news
        ];
        //return var_dump($data['news']);
        return view('admin.news.edit', $data);
    }

    /**
     * @param  content - содержимое новости, date - дата новости
     * @return view news.index
     * Обновляет новость по id в БД
     */
    public function update(Request $request, $id)
    {

        $content = $request->input('content');

        if($content === NULL){
            return redirect('admin/news/create')->with('message', 'Не введен текст новости');
        }
        
        $date = $request->input('date-publish');

        if($date === NULL){
            $date = Carbon::now();
        }

        $DB = News::where('id', $id)
            ->update([
            'content' => $content,
            'date' => $date
        ]);
        return redirect('admin/news');
    }

    /**
     * @param  int  $id - новости
     * Присваивает новости по id -  status 0 
     */
    public function destroy($id)
    {
        News::where('id',$id)
            ->update([
                'status' => 0
            ]);
        return redirect('admin/news');
    }
}
