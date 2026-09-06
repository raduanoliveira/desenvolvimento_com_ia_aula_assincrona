<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ListTasksRequest;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskTitleRequest;
use App\Http\Resources\TaskResource;
use App\Services\ArchiveTaskService;
use App\Services\CreateTaskService;
use App\Services\DeleteTaskService;
use App\Services\ListTasksService;
use App\Services\ToggleTaskService;
use App\Services\UpdateTaskTitleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TaskController extends Controller
{
    public function index(ListTasksRequest $request, ListTasksService $service): AnonymousResourceCollection
    {
        $status = $request->validated('status') ?? 'all';

        return TaskResource::collection($service->handle($request->user()->id, $status));
    }

    public function store(StoreTaskRequest $request, CreateTaskService $service): JsonResponse
    {
        $task = $service->handle($request->validated(), $request->user()->id);

        return (new TaskResource($task))
            ->response()
            ->setStatusCode(201);
    }

    public function updateTitle(UpdateTaskTitleRequest $request, int $task, UpdateTaskTitleService $service): TaskResource
    {
        return new TaskResource($service->handle($task, $request->user()->id, $request->validated('title')));
    }

    public function toggle(Request $request, int $task, ToggleTaskService $service): TaskResource
    {
        return new TaskResource($service->handle($task, $request->user()->id));
    }

    public function archive(Request $request, int $task, ArchiveTaskService $service): TaskResource
    {
        return new TaskResource($service->handle($task, $request->user()->id));
    }

    public function destroy(Request $request, int $task, DeleteTaskService $service): JsonResponse
    {
        $service->handle($task, $request->user()->id);

        return response()->json(status: 204);
    }
}
