<?php

namespace MetaFox\Mux\Listeners;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use MetaFox\LiveStreaming\Repositories\LiveVideoRepositoryInterface;
use MetaFox\Mux\Support\Providers\Mux;

class LiveStreamingMuxCallback
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

        $muxService = resolve(Mux::class, ['moduleId' => 'livestreaming', 'handler' => LiveVideoRepositoryInterface::class]);

        return $muxService->handleWebhook($request);
    }
}
