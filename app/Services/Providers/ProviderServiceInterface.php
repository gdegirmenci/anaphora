<?php

namespace App\Services\Providers;

use Illuminate\Support\Collection;
use Psr\Http\Message\RequestInterface;

/**
 * Interface ProviderServiceInterface
 * @package App\Services\Providers
 */
interface ProviderServiceInterface
{
    /**
     * @return string
     */
    public function getUrl(): string;

    /**
     * @return string
     */
    public function getMethod(): string;

    /**
     * @return array
     */
    public function getHeaders(): array;

    /**
     * @return Collection
     */
    public function getBody(): Collection;

    /**
     * @return string
     */
    public function getProvider(): string;

    /**
     * @return RequestInterface
     */
    public function getRequest(): RequestInterface;

    /**
     * @return array
     */
    public function getRecipients(): array;

    /**
     * @param int $campaignId
     * @return string|null
     */
    public function switchProvider(int $campaignId): ?string;
}
