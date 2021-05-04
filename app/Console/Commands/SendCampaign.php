<?php

namespace App\Console\Commands;

use App\Entities\CampaignEntity;
use App\Http\Requests\API\Campaign\CreateRequest;
use App\Services\CampaignService;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

/**
 * Class SendCampaign
 * @package App\Console\Commands
 */
class SendCampaign extends Command
{
    /** @var string */
    protected $signature = 'campaign:send {payload : JSON structure, for more detail please check README.}';
    /** @var string */
    protected $description = 'Sending campaign with given JSON.';

    /**
     * @param CampaignService $campaignService
     * @return void
     */
    public function handle(CampaignService $campaignService): void
    {
        if (Arr::get($this->checkPayload(), 'failed')) {
            $this->warn(Arr::get($this->checkPayload(), 'message'));

            return;
        }

        $campaignService->create($this->campaignEntity());

        $this->info('Campaign is sent.');
    }

    /**
     * @return array
     */
    protected function rules(): array
    {
        return (new CreateRequest())->rules();
    }

    /**
     * @return array
     */
    protected function checkPayload(): array
    {
        $payload = $this->payload();

        if (!$payload) {
            return ['failed' => true, 'message' => 'Given payload has syntax error.'];
        }

        $validator = Validator::make($payload, $this->rules());

        return ['failed' => $validator->fails(), 'message' => $validator->errors()->first()];
    }

    /**
     * @return array
     */
    protected function payload(): ?array
    {
        return json_decode(json_encode(json_decode($this->argument('payload'))), true) ?? null;
    }

    /**
     * @return CampaignEntity
     */
    protected function campaignEntity(): CampaignEntity
    {
        return new CampaignEntity($this->payload());
    }
}
