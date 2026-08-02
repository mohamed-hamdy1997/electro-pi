<?php

namespace App\Http\Controllers;

use App\Http\Requests\Task\TaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Project;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use OpenApi\Attributes as OA;

class TaskController extends Controller
{
    public function __construct(private readonly TaskService $taskService) {}

    #[OA\Get(
        path: '/api/v1/projects/{project}/tasks',
        summary: 'List tasks for a project',
        security: [['sanctum' => []]],
        tags: ['Tasks'],
        parameters: [
            new OA\Parameter(name: 'project',  in: 'path',  required: true,  description: 'Project ID', schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'status',   in: 'query', required: false, schema: new OA\Schema(type: 'string', enum: ['todo', 'in_progress', 'done'])),
            new OA\Parameter(name: 'priority', in: 'query', required: false, schema: new OA\Schema(type: 'string', enum: ['low', 'medium', 'high'])),
            new OA\Parameter(name: 'search',   in: 'query', required: false, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Paginated task list',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data',  type: 'array', items: new OA\Items(ref: '#/components/schemas/TaskResource')),
                        new OA\Property(property: 'links', type: 'object'),
                        new OA\Property(property: 'meta',  type: 'object'),
                    ],
                ),
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 403, description: 'Forbidden'),
            new OA\Response(response: 404, description: 'Project not found'),
        ],
    )]
    public function index(Request $request, Project $project): AnonymousResourceCollection
    {
        $this->authorize('view', $project);

        $tasks = $this->taskService->index($project, $request->only('status', 'priority', 'search'));

        return TaskResource::collection($tasks);
    }

    #[OA\Post(
        path: '/api/v1/projects/{project}/tasks',
        summary: 'Create a task inside a project',
        security: [['sanctum' => []]],
        tags: ['Tasks'],
        parameters: [
            new OA\Parameter(name: 'project', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['title'],
                properties: [
                    new OA\Property(property: 'title',       type: 'string',  example: 'Fix login bug'),
                    new OA\Property(property: 'description', type: 'string',  nullable: true),
                    new OA\Property(property: 'priority',    type: 'string',  enum: ['low', 'medium', 'high'], example: 'high'),
                    new OA\Property(property: 'status',      type: 'string',  enum: ['todo', 'in_progress', 'done'], example: 'todo'),
                    new OA\Property(property: 'due_date',    type: 'string',  format: 'date', example: '2026-08-10'),
                ],
            ),
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Task created',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Task created successfully.'),
                        new OA\Property(property: 'task',    ref: '#/components/schemas/TaskResource'),
                    ],
                ),
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 403, description: 'Forbidden'),
            new OA\Response(response: 404, description: 'Project not found'),
            new OA\Response(response: 422, description: 'Validation error', ref: '#/components/schemas/ValidationError'),
        ],
    )]
    public function store(TaskRequest $request, Project $project): JsonResponse
    {
        $this->authorize('view', $project);

        $task = $this->taskService->store($project, $request->validated());

        return response()->json([
            'message' => 'Task created successfully.',
            'task'    => new TaskResource($task),
        ], 201);
    }

    #[OA\Get(
        path: '/api/v1/projects/{project}/tasks/{task}',
        summary: 'Get a single task',
        security: [['sanctum' => []]],
        tags: ['Tasks'],
        parameters: [
            new OA\Parameter(name: 'project', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'task',    in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Task details', content: new OA\JsonContent(ref: '#/components/schemas/TaskResource')),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 403, description: 'Forbidden'),
            new OA\Response(response: 404, description: 'Not found'),
        ],
    )]
    public function show(Project $project, Task $task): JsonResponse
    {
        $this->authorize('view', $task);

        return response()->json(new TaskResource($task));
    }

    #[OA\Put(
        path: '/api/v1/projects/{project}/tasks/{task}',
        summary: 'Update a task',
        security: [['sanctum' => []]],
        tags: ['Tasks'],
        parameters: [
            new OA\Parameter(name: 'project', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'task',    in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'title',       type: 'string'),
                    new OA\Property(property: 'description', type: 'string', nullable: true),
                    new OA\Property(property: 'priority',    type: 'string', enum: ['low', 'medium', 'high']),
                    new OA\Property(property: 'status',      type: 'string', enum: ['todo', 'in_progress', 'done']),
                    new OA\Property(property: 'due_date',    type: 'string', format: 'date'),
                ],
            ),
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Task updated',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Task updated successfully.'),
                        new OA\Property(property: 'task',    ref: '#/components/schemas/TaskResource'),
                    ],
                ),
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 403, description: 'Forbidden'),
            new OA\Response(response: 404, description: 'Not found'),
            new OA\Response(response: 422, description: 'Validation error', ref: '#/components/schemas/ValidationError'),
        ],
    )]
    public function update(TaskRequest $request, Project $project, Task $task): JsonResponse
    {
        $this->authorize('update', $task);

        $task = $this->taskService->update($task, $request->validated());

        return response()->json([
            'message' => 'Task updated successfully.',
            'task'    => new TaskResource($task),
        ]);
    }

    #[OA\Delete(
        path: '/api/v1/projects/{project}/tasks/{task}',
        summary: 'Delete a task (soft delete)',
        security: [['sanctum' => []]],
        tags: ['Tasks'],
        parameters: [
            new OA\Parameter(name: 'project', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'task',    in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Task deleted',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Task deleted successfully.'),
                    ],
                ),
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 403, description: 'Forbidden'),
            new OA\Response(response: 404, description: 'Not found'),
        ],
    )]
    public function destroy(Project $project, Task $task): JsonResponse
    {
        $this->authorize('delete', $task);

        $this->taskService->destroy($task);

        return response()->json(['message' => 'Task deleted successfully.']);
    }
}
