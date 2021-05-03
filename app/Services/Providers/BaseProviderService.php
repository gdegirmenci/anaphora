<?php

namespace App\Services\Providers;

use App\Enums\ProviderEnums;
use App\ValueObjects\Email\Email;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestInterface;

/**
 * Class BaseProviderService
 * @package App\Services\Providers
 */
abstract class BaseProviderService implements ProviderServiceInterface
{
    const METHOD = 'POST';
    const PROVIDERS = [ProviderEnums::SEND_GRID, ProviderEnums::MAIL_JET];

    /** @var Email */
    protected $email;

    /**
     * BaseProviderService constructor.
     * @param Email $email
     */
    public function __construct(Email $email)
    {
        $this->email = $email;
    }

    /**
     * @return RequestInterface
     */
    public function getRequest(): RequestInterface
    {
        return new Request($this->getMethod(), $this->getUrl(), $this->getHeaders(), json_encode($this->getBody()));
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return self::METHOD;
    }

    /**
     * @return array
     */
    public function getRecipients(): array
    {
        return $this->email->getTo();
    }

    /**
     * @return string
     */
    public function switchProvider(): string
    {
        $currentProvider = $this->getProvider();

        return collect(self::PROVIDERS)
            ->filter(function (string $provider) use ($currentProvider) {
                return $provider !== $currentProvider;
            })
            ->first();
    }
}
