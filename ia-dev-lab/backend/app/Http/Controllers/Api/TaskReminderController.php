<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TaskResource;
use App\Services\ListDueTomorrowRemindersService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TaskReminderController extends Controller
{
    public function index(Request $request, ListDueTomorrowRemindersService $service): AnonymousResourceCollection
    {
        return TaskResource::collection($service->handle($request->user()->id));
    }
}
