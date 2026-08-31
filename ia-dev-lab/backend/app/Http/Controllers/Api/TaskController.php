<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Resources\TaskResource;
use App\Services\ArchiveTaskService;
use App\Services\CreateTaskService;
use App\Services\DeleteTaskService;
use App\Services\ListTasksService;
use App\Services\ToggleTaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TaskController extends Controller
{
    public function index(Request $request, ListTasksService $service): AnonymousResourceCollection
    {
        return TaskResource::collection($service->handle($request->user()->id));
    }

    public function store(StoreTaskRequest $request, CreateTaskService $service): JsonResponse
    {
        $task = $service->handle($request->validated(), $request->user()->id);

        return (new TaskResource($task))
            ->response()
            ->setStatusCode(201);
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
