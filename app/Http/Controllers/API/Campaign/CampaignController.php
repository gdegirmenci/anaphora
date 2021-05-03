<?php

namespace App\Http\Controllers\API\Campaign;

use App\Entities\CampaignEntity;
use App\Http\Requests\API\Campaign\CreateRequest;
use App\Http\Requests\API\Campaign\PaginateRequest;
use App\Http\Resources\CampaignResource;
use App\Repositories\Campaign\CampaignRepositoryInterface;
use App\Services\CampaignService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Class CampaignController
 * @package App\Http\Controllers\API\Campaign
 */
class CampaignController extends Controller
{
    /**
     * @param PaginateRequest $request
     * @param CampaignRepositoryInterface $campaignRepository
     * @return AnonymousResourceCollection
     */
    public function index(
        PaginateRequest $request,
        CampaignRepositoryInterface $campaignRepository
    ) {
        return CampaignResource::collection($campaignRepository->paginate($request->perPage()));
    }

    /**
     * @param CreateRequest $request
     * @param CampaignService $campaignService
     * @return JsonResponse
     */
    public function create(CreateRequest $request, CampaignService $campaignService): JsonResponse
    {
        $campaignEntity = new CampaignEntity($request->toArray());

        return new JsonResponse(['data' => $campaignService->create($campaignEntity)]);
    }
}
