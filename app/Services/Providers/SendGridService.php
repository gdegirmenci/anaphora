<?php

namespace App\Services\Providers;

use App\Enums\ProviderEnums;
use App\ValueObjects\Email\Email;

/**
 * Class SendGridService
 * @package App\Services\Providers
 */
class SendGridService extends BaseProviderService
{
    /**
     * SendGridService constructor.
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
     * @return array
     */
    public function getBody(): array
    {
        return [
            'personalizations' => [['to' => $this->email->getTo(), 'subject' => $this->email->getSubject()]],
            'from' => $this->email->getFrom()->toArray(),
            'reply_to' => $this->email->getReply()->toArray(),
            'content' => $this->email->getReply()->toArray(),
        ];
    }

    /**
     * @return string
     */
    public function getProvider(): string
    {
        return ProviderEnums::SEND_GRID;
    }
}
