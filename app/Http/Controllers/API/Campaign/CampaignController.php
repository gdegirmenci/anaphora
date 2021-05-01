<?php

namespace App\Http\Controllers\API\Campaign;

use App\Http\Requests\API\Campaign\CreateRequest;
use App\Http\Requests\API\Campaign\PaginateRequest;
use App\Http\Resources\CampaignResource;
use App\Repositories\Campaign\CampaignRepositoryInterface;
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
    ): AnonymousResourceCollection {
        return CampaignResource::collection($campaignRepository->paginate($request->perPage()));
    }

    /**
     * @param CreateRequest $request
     * @return JsonResponse
     */
    public function create(CreateRequest $request, CampaignRepositoryInterface $campaignRepository): JsonResponse
    {
        $campaignRepository->create($request->toArray());

        return $this->success();
    }
}
