<?php

namespace App\Services\Providers;

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
     * @return array
     */
    public function getBody(): array;

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
     * @return string
     */
    public function switchProvider(): string;
}
