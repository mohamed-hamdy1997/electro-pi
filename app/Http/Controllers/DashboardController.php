<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class DashboardController extends Controller
{
    public function __construct(private readonly DashboardService $dashboardService) {}

    #[OA\Get(
        path: '/api/v1/dashboard',
        summary: 'Get statistics for the authenticated user',
        security: [['sanctum' => []]],
        tags: ['Dashboard'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Dashboard statistics',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'total_projects',  type: 'integer', example: 5),
                        new OA\Property(property: 'active_projects', type: 'integer', example: 3),
                        new OA\Property(property: 'total_tasks',     type: 'integer', example: 24),
                        new OA\Property(property: 'completed_tasks', type: 'integer', example: 8),
                        new OA\Property(property: 'pending_tasks',   type: 'integer', example: 12),
                        new OA\Property(property: 'overdue_tasks',   type: 'integer', example: 4),
                    ],
                ),
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ],
    )]
    public function __invoke(Request $request): JsonResponse
    {
        $stats = $this->dashboardService->getStats($request->user());

        return response()->json($stats);
    }
}
