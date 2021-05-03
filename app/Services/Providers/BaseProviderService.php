<?php

namespace App\Services\Providers;

use App\Enums\ProviderEnums;
use App\Repositories\Campaign\CampaignRepositoryInterface;
use App\ValueObjects\CircuitBreaker\Keys;
use App\ValueObjects\CircuitBreaker\Tracker;
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
    /** @var CampaignRepositoryInterface */
    private $campaignRepository;

    /**
     * BaseProviderService constructor.
     * @param CampaignRepositoryInterface $campaignRepository
     * @param Email $email
     */
    public function __construct(CampaignRepositoryInterface $campaignRepository, Email $email)
    {
        $this->campaignRepository = $campaignRepository;
        $this->email = $email;
    }

    /**
     * @return RequestInterface
     */
    public function getRequest(): RequestInterface
    {
        return new Request($this->getMethod(), $this->getUrl(), $this->getHeaders(), $this->getBody()->toJson());
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
     * @param int $campaignId
     * @return string|null
     */
    public function switchProvider(int $campaignId): ?string
    {
        $currentProvider = $this->getProvider();

        return collect(self::PROVIDERS)
            ->filter(function (string $provider) use ($currentProvider, $campaignId) {
                $tracker = $this->getTracker($provider, $campaignId);

                return $provider !== $currentProvider &&
                    !$tracker->isOpened() &&
                    is_null($this->campaignRepository->getFailedLogByProvider($campaignId, $provider));
            })
            ->first();
    }

    /**
     * @param string $provider
     * @param int $campaignId
     * @return Tracker
     */
    protected function getTracker(string $provider, int $campaignId): Tracker
    {
        return new Tracker(new Keys($provider), $campaignId);
    }
}
