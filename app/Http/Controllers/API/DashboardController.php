<?php

namespace App\Http\Controllers\API;

use App\Services\DashboardService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

/**
 * Class DashboardController
 * @package App\Http\Controllers\API
 */
class DashboardController extends Controller
{
    /**
     * @param DashboardService $dashboardService
     * @return JsonResponse
     */
    public function index(DashboardService $dashboardService): JsonResponse
    {
        return new JsonResponse(['data' => $dashboardService->getDashboardData()]);
    }
}
