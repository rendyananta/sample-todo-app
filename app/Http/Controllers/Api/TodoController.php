<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Response\BaseResponse;
use App\Models\Todo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class TodoController extends Controller
{
    public function index(): BaseResponse
    {
        $query = (array) Todo::query()->paginate(20);

        return new BaseResponse(
            $query
        );
    }

    public function store(Request $request): BaseResponse
    {
        $this->validate($request, [
            'name' => 'required|max:255',
            'description' => '',
            'due' => 'datetime|Y-m-d H:i:s'
        ]);

        $todo = new Todo();
        $todo->fill($request->only('name', 'description', 'due'));

        return $todo->save()
            ? new BaseResponse($todo->toArray())
            : new BaseResponse(null, false, "Failed to create todo");
    }

    public function update(Request $request, Todo $todo): BaseResponse
    {
        $this->validate($request, [
            'name' => 'required|max:255',
            'description' => '',
            'due' => 'datetime|Y-m-d H:i:s'
        ]);

        $todo->fill($request->only('name', 'description', 'due'));

        return $todo->save()
            ? new BaseResponse($todo->toArray())
            : new BaseResponse(null, false, "Failed to update todo");
    }

    public function show(Todo $todo): BaseResponse
    {
        return new BaseResponse($todo->toArray());
    }

    public function destroy(Todo $todo): BaseResponse
    {
        return $todo->delete()
            ? new BaseResponse($todo->toArray())
            : new BaseResponse(null, false, "Failed to update todo");
    }

    public function completed(Request $request, Todo $todo): BaseResponse
    {
        $this->validate($request, [
            'completed' => 'required|boolean'
        ]);

        $todo->setAttribute('completed', $request->input('completed'));

        return $todo->save()
            ? new BaseResponse($todo->toArray())
            : new BaseResponse(null, false, "Failed update new todo complete status");
    }
}
