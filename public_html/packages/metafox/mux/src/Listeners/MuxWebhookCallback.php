<?php

namespace MetaFox\Mux\Listeners;

use Illuminate\Http\Request;
use MetaFox\Video\Contracts\ProviderManagerInterface;
use MetaFox\Mux\Support\Providers\Mux;

class MuxWebhookCallback
{
    /**
     * @param  Request   $request
     * @param  string    $provider
     * @return bool|null
     */
    public function handle(Request $request, string $provider): ?bool
    {
        if ($provider !== Mux::PROVIDER_TYPE) {
            return null;
        }

        $manager    = resolve(ProviderManagerInterface::class);
        $muxService = $manager->getVideoServiceClassByDriver(Mux::PROVIDER_TYPE);

        return $muxService->handleWebhook($request);
    }
}
