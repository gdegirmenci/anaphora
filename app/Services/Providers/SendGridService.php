<?php

namespace App\Services\Providers;

use App\Enums\ProviderEnums;
use App\Repositories\Campaign\CampaignRepositoryInterface;
use App\ValueObjects\Email\Email;
use Illuminate\Support\Collection;

/**
 * Class SendGridService
 * @package App\Services\Providers
 */
class SendGridService extends BaseProviderService
{
    /**
     * SendGridService constructor.
     * @param CampaignRepositoryInterface $campaignRepository
     * @param Email $email
     */
    public function __construct(CampaignRepositoryInterface $campaignRepository, Email $email)
    {
        parent::__construct($campaignRepository, $email);
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return config('services.sendgrid.endpoint');
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return [
            'Authorization' => sprintf('Bearer %s', config('services.sendgrid.secret')),
            'Content-Type' => 'application/json',
        ];
    }

    /**
     * @return Collection
     */
    public function getBody(): Collection
    {
        return collect([
            'personalizations' => [['to' => $this->email->getTo(), 'subject' => $this->email->getSubject()]],
            'from' => $this->email->getFrom()->toArray(),
            'reply_to' => $this->email->getReply()->toArray(),
            'content' => [$this->email->getTemplate()->toArray()],
        ]);
    }

    /**
     * @return string
     */
    public function getProvider(): string
    {
        return ProviderEnums::SEND_GRID;
    }
}
