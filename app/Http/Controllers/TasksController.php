<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Task;

class TasksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     //getでtasksにアクセスされた場合の「一覧表示」
    public function index()
    {

        $data = [];
        if (\Auth::check()) { // 認証済みの場合
            // 認証済みユーザを取得
            $user = \Auth::user();
            // ユーザの投稿の一覧を作成日時の降順で取得
            $tasks = $user->tasks()->orderBy('created_at', 'desc')->paginate(10);
            $data = [
                'user' => $user,
                'tasks' => $tasks,
            ];
            
            //indexビューで表示
            return view('tasks.index', $data);
        }
        
        //dashboardビューを表示
        return view("dashboard");
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
     //getでtasks/createにアクセスした場合の「タスク追加画面表示処理」
    public function create()
    {

        $task = new Task;
        
        //タスク追加ビューを表示
        return view("tasks.create",[
            "task" => $task,
        ]);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
     //postでtasksにアクセスされた場合の「タスク追加処理」
    public function store(Request $request)
    {
        
        $user = \Auth::user();
        
        //バリデーション
        $request->validate([
            "content" => "required",
            "status" => "required|max:10",
        ]);
        
        // 認証済みユーザ（閲覧者）の投稿として作成（リクエストされた値をもとに作成）
        $request->user()->tasks()->create([
            'content' => $request->content,
            'status' => $request->status,
        ]);
        
        //トップページへリダイレクトさせる
        return redirect("/");

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
     //getでtasks/idにアクセスされた場合の「取得表示処理」
    public function show($id)
    {

        // idの値で投稿を検索して取得
        $task = \App\Models\Task::findOrFail($id);
        
        // 認証済みユーザ（閲覧者）がその投稿の所有者である場合は投稿を表示
        if (\Auth::id() === $task->user_id) {
            //タスク詳細ビューで表示
            return view("tasks.show",[
                "task" => $task,
            ]);
        }

        //トップページへリダイレクトさせる
        return redirect("/");
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
     //getでtasks/id/editにアクセスされた場合の「更新画面表示処理」
    public function edit($id)
    {

        //idの値でタスクを検索して取得
        $task = Task::findOrFail($id);
        
        // 認証済みユーザ（閲覧者）がその投稿の所有者である場合は更新画面を表示
        if (\Auth::id() === $task->user_id) {
            //タスク編集ビューで表示
            return view("tasks.edit",[
                "task"=>$task,
            ]);
        }

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
     //putまたはpatchでtasks/idにアクセスされた場合の「更新処理」
    public function update(Request $request, $id)
    {
        
        //バリデーション
        $request->validate([
            "content" => "required",
            "status" => "required|max:10",
        ]);
        
        //idの値でタスクを検索して取得
        $task = Task::findOrFail($id);
        
        // 認証済みユーザ（閲覧者）がその投稿の所有者である場合は投稿を更新
        if (\Auth::id() === $task->user_id) {
            //タスク更新
            $task->content = $request->content;
            $task->status = $request->status;
            $task->save();
        }

        //トップページへリダイレクトさせる
        return redirect("/");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
     //deleteでtasks/idにアクセスされた場合の「削除処理」
    public function destroy($id)
    {

        // idの値で投稿を検索して取得
        $task = \App\Models\Task::findOrFail($id);
        
        // 認証済みユーザ（閲覧者）がその投稿の所有者である場合は投稿を削除
        if (\Auth::id() === $task->user_id) {
            $task->delete();
            //トップページへリダイレクトさせる
            return redirect("/");
        }

    }
}
