<?php

namespace App\Http\Controllers;

use App\Http\Requests\Project\ProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use App\Services\ProjectService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use OpenApi\Attributes as OA;

class ProjectController extends Controller
{
    public function __construct(private readonly ProjectService $projectService) {}

    #[OA\Get(
        path: '/api/v1/projects',
        summary: 'List all projects for the authenticated user',
        security: [['sanctum' => []]],
        tags: ['Projects'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Paginated list of projects',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data',  type: 'array', items: new OA\Items(ref: '#/components/schemas/ProjectResource')),
                        new OA\Property(property: 'links', type: 'object'),
                        new OA\Property(property: 'meta',  type: 'object'),
                    ],
                ),
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ],
    )]
    public function index(Request $request): AnonymousResourceCollection
    {
        $projects = $this->projectService->index($request->user());

        return ProjectResource::collection($projects);
    }

    #[OA\Post(
        path: '/api/v1/projects',
        summary: 'Create a new project',
        security: [['sanctum' => []]],
        tags: ['Projects'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name'],
                properties: [
                    new OA\Property(property: 'name',        type: 'string', example: 'My Project'),
                    new OA\Property(property: 'description', type: 'string', nullable: true, example: 'Optional description'),
                    new OA\Property(property: 'status',      type: 'string', enum: ['active', 'completed', 'archived'], example: 'active'),
                ],
            ),
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Project created',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Project created successfully.'),
                        new OA\Property(property: 'project', ref: '#/components/schemas/ProjectResource'),
                    ],
                ),
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 422, description: 'Validation error', ref: '#/components/schemas/ValidationError'),
        ],
    )]
    public function store(ProjectRequest $request): JsonResponse
    {
        $project = $this->projectService->store($request->user(), $request->validated());

        return response()->json([
            'message' => 'Project created successfully.',
            'project' => new ProjectResource($project),
        ], 201);
    }

    #[OA\Get(
        path: '/api/v1/projects/{id}',
        summary: 'Get a single project',
        security: [['sanctum' => []]],
        tags: ['Projects'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Project details', content: new OA\JsonContent(ref: '#/components/schemas/ProjectResource')),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 403, description: 'Forbidden'),
            new OA\Response(response: 404, description: 'Not found'),
        ],
    )]
    public function show(Project $project): JsonResponse
    {
        $this->authorize('view', $project);

        return response()->json(new ProjectResource($project->loadCount('tasks')));
    }

    #[OA\Put(
        path: '/api/v1/projects/{id}',
        summary: 'Update a project',
        security: [['sanctum' => []]],
        tags: ['Projects'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'name',        type: 'string', example: 'Updated Name'),
                    new OA\Property(property: 'description', type: 'string', nullable: true),
                    new OA\Property(property: 'status',      type: 'string', enum: ['active', 'completed', 'archived']),
                ],
            ),
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Project updated',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Project updated successfully.'),
                        new OA\Property(property: 'project', ref: '#/components/schemas/ProjectResource'),
                    ],
                ),
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 403, description: 'Forbidden'),
            new OA\Response(response: 404, description: 'Not found'),
            new OA\Response(response: 422, description: 'Validation error', ref: '#/components/schemas/ValidationError'),
        ],
    )]
    public function update(ProjectRequest $request, Project $project): JsonResponse
    {
        $this->authorize('update', $project);

        $project = $this->projectService->update($project, $request->validated());

        return response()->json([
            'message' => 'Project updated successfully.',
            'project' => new ProjectResource($project),
        ]);
    }

    #[OA\Delete(
        path: '/api/v1/projects/{id}',
        summary: 'Delete a project (soft delete)',
        security: [['sanctum' => []]],
        tags: ['Projects'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Project deleted',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Project deleted successfully.'),
                    ],
                ),
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 403, description: 'Forbidden'),
            new OA\Response(response: 404, description: 'Not found'),
        ],
    )]
    public function destroy(Project $project): JsonResponse
    {
        $this->authorize('delete', $project);

        $this->projectService->destroy($project);

        return response()->json(['message' => 'Project deleted successfully.']);
    }
}
