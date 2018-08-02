<?php

namespace App\Http\Controllers;

use App\Task;
use App\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function index(){

        $tasks = Task::all();

        return view('task.index')->with('tasks', $tasks);
    }

    public function edit($id){

        if(isset($id)){
            $task = Task::find($id);
        }
        return view('task.edit')->with('task', $task);
    }

    public function create(Request $request){

        $user_id = Auth::id();

        $this->validate($request, [
            'title'=>'string|required|max:255|min:5',
            'desc'=>'string|required',
            'date'=>'string|required|max:150|min:5'
        ]);

        if(!empty($request->id)){
/////EDIT
            $task = Task::find($request->id);


            if(Gate::denies('update-task',$task)){
                return redirect()->back()->with('erorr',"У вас не прав");
            }

            $task->title = $request->input('title');
            $task->desc = $request->input('desc');
            $task->date = $request->input('date');
            $task->user_id = $user_id;
            $task->save();

            return redirect()->back()->with('massege','данные обновлены');
        }

        $task = new Task();
        $task->title = $request->input('title');
        $task->desc = $request->input('desc');
        $task->date = $request->input('date');
        $task->user_id = $user_id;
        $task->save();

        return redirect()->back()->with('massege','данные сохранены');



    }


    public function delete($id)
    {
        $task = Task::findOrFail($id);

        if(Gate::denies('update-task',$task)){
            return redirect()->back()->with('erorr',"У вас не прав");
        }

        if($task->delete()) {
            return redirect()->back()->with('erorr','данные удалены');
        };
    }

}
