<?php

namespace App\Factories;

use App\Entities\CampaignEntity;
use App\Enums\ProviderEnums;
use App\Repositories\Campaign\CampaignRepositoryInterface;
use App\Services\Providers\MailJetService;
use App\Services\Providers\ProviderServiceInterface;
use App\Services\Providers\SendGridService;
use Exception;

/**
 * Class ProviderServiceFactory
 * @package App\Factories
 */
class ProviderServiceFactory
{
    /** @var CampaignRepositoryInterface */
    private $campaignRepository;

    /**
     * ProviderServiceFactory constructor.
     * @param CampaignRepositoryInterface $campaignRepository
     */
    public function __construct(CampaignRepositoryInterface $campaignRepository)
    {
        $this->campaignRepository = $campaignRepository;
    }

    /**
     * @param CampaignEntity $campaignEntity
     * @param string $provider
     * @throws Exception
     * @return ProviderServiceInterface
     */
    public function make(CampaignEntity $campaignEntity, string $provider): ProviderServiceInterface
    {
        switch ($provider) {
            case ProviderEnums::SEND_GRID:
                return new SendGridService($this->campaignRepository, $campaignEntity->getEmail());
            case ProviderEnums::MAIL_JET:
                return new MailJetService($this->campaignRepository, $campaignEntity->getEmail());
            default:
                throw new Exception("Given provider <{$provider}> is not supported.");
        }
    }
}
