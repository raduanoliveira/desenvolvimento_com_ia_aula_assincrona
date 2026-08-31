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
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TaskController extends Controller
{
    public function index(ListTasksService $service): AnonymousResourceCollection
    {
        return TaskResource::collection($service->handle());
    }

    public function store(StoreTaskRequest $request, CreateTaskService $service): JsonResponse
    {
        $task = $service->handle($request->validated());

        return (new TaskResource($task))
            ->response()
            ->setStatusCode(201);
    }

    public function toggle(int $task, ToggleTaskService $service): TaskResource
    {
        return new TaskResource($service->handle($task));
    }

    public function archive(int $task, ArchiveTaskService $service): TaskResource
    {
        return new TaskResource($service->handle($task));
    }

    public function destroy(int $task, DeleteTaskService $service): JsonResponse
    {
        $service->handle($task);

        return response()->json(status: 204);
    }
}
