<?php

namespace App\Factories;

use App\Entities\CampaignEntity;
use App\Enums\ProviderEnums;
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
                return new SendGridService($campaignEntity->getEmail());
            case ProviderEnums::MAIL_JET:
                return new MailJetService($campaignEntity->getEmail());
            default:
                throw new Exception("Given provider <{$provider}> is not supported.");
        }
    }
}
