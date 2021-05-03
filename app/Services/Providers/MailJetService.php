<?php

namespace App\Services\Providers;

use App\Enums\ProviderEnums;
use App\ValueObjects\Email\Email;
use Illuminate\Support\Arr;

/**
 * Class MailJetService
 * @package App\Services\Providers
 */
class MailJetService extends BaseProviderService
{
    /**
     * MailJetService constructor.
     * @param Email $email
     */
    public function __construct(Email $email)
    {
        parent::__construct($email);
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
     * @return array
     */
    public function getBody(): array
    {
        return [
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
                    'TextPart' => $this->email->getTemplate(),
                ],
            ],
        ];
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
