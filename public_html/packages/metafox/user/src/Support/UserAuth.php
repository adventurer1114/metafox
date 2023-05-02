<?php

namespace MetaFox\User\Support;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use MetaFox\User\Contracts\UserAuth as ContractsUserAuth;
use MetaFox\User\Repositories\Contracts\UserRepositoryInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class UserAuth implements ContractsUserAuth
{
    public function __construct(
        protected UserRepositoryInterface $userRepository
    ) {
    }

    public function login(FormRequest $request)
    {
        $params   = $request->validated();
        $username = $request->validated('username', '');
        $password = $request->validated('password', '');

        $user = $this->userRepository->findAndValidateForAuth($username, $password);

        $response = app('events')->dispatch('user.request_mfa_token', [$user], true);

        if ($response) {
            return $response;
        }

        $response = $this->authorize($request);

        app('events')->dispatch('user.signed_in', [$user, $params]);

        return $response;
    }

    public function fixApiSecret()
    {
        $apiKey    = config('app.api_key');
        $apiSecret = config('app.api_secret');
        $secret    = DB::table('oauth_clients')->where('id', $apiKey)->value('secret');

        if ($secret === $apiSecret) {
            return;
        }

        DB::table('oauth_clients')->where('id', $apiKey)->update(['secret' => $apiSecret]);
    }

    /**
     * authorize.
     *
     * @param  FormRequest $request
     * @return mixed
     */
    public function authorize(FormRequest $request)
    {
        $request->merge([
            'client_id'     => config('app.api_key'),
            'client_secret' => config('app.api_secret'),
            'grant_type'    => 'password',
            'scope'         => '*',
        ]);

        $proxy    = Request::create('oauth/token', 'POST', $request->validated());
        $response = Route::dispatch($proxy);

        if (!$response->isOk()) {
            $content = json_decode($response->getContent(), true);
            if ($error = json_decode(Arr::get($content, 'error'), true)) {
                // handle custom error'
                $content = $error;
            }

            $params = [
                'title' => __p('user::phrase.oops_login_failed'),
            ];

            if (is_array($content)) {
                $params = array_merge($content, $params);
            }

            abort(403, json_encode($params));
        }

        return $response;
    }
}
