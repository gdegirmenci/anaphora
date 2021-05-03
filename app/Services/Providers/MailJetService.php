<?php

namespace App\Services\Providers;

use App\Enums\ProviderEnums;
use App\Repositories\Campaign\CampaignRepository;
use App\ValueObjects\Email\Email;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

/**
 * Class MailJetService
 * @package App\Services\Providers
 */
class MailJetService extends BaseProviderService
{
    /**
     * MailJetService constructor.
     * @param CampaignRepository $campaignRepository
     * @param Email $email
     */
    public function __construct(CampaignRepository $campaignRepository, Email $email)
    {
        parent::__construct($campaignRepository, $email);
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return config('services.mailjet.endpoint');
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return [
            'Authorization' => sprintf('Basic %s', config('services.mailjet.secret')),
            'Content-Type' => 'application/json',
        ];
    }

    /**
     * @return Collection
     */
    public function getBody(): Collection
    {
        return collect([
            'Messages' => [
                [
                    'From' => [
                        'Email' => $this->email->getFrom()->getEmail(),
                        'Name' => $this->email->getFrom()->getName(),
                    ],
                    'ReplyTo' => [
                        'Email' => $this->email->getReply()->getEmail(),
                        'Name' => $this->email->getReply()->getName(),
                    ],
                    'To' => $this->getRecipients(),
                    'Subject' => $this->email->getSubject(),
                    'TextPart' => $this->email->getTemplate()->getContent(),
                    'HTMLPart' => '',
                ],
            ],
        ]);
    }

    /**
     * @return string
     */
    public function getProvider(): string
    {
        return ProviderEnums::MAIL_JET;
    }

    /**
     * @return array
     */
    public function getRecipients(): array
    {
        return collect($this->email->getTo())
            ->map(function (array $to) {
                return ['Email' => Arr::get($to, 'email'), 'Name' => Arr::get($to, 'name')];
            })
            ->toArray();
    }
}
