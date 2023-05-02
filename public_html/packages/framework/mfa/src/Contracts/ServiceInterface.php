<?php

/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Mfa\Contracts;

use MetaFox\Form\AbstractForm;
use MetaFox\Mfa\Models\UserAuthToken;
use MetaFox\Mfa\Models\UserService;
use MetaFox\Platform\Contracts\User;

/**
 * Interface ServiceInterface.
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
interface ServiceInterface
{
    /**
     * to the service title.
     *
     * @return string
     */
    public function toTitle(): string;

    /**
     * to the service description.
     *
     * @return string
     */
    public function toDescription(): string;

    /**
     * to the service icon.
     * @param  string $resolution
     * @return string
     */
    public function toIcon(string $resolution = 'web'): string;

    /**
     * setup the service for user.
     *
     * @param  User         $user
     * @return array<mixed>
     */
    public function setup(User $user): array;

    /**
     * load the setup form.
     *
     * @param  UserService  $userService
     * @param  string       $resolution
     * @return AbstractForm
     */
    public function setupForm(UserService $userService, ?string $resolution = 'web'): AbstractForm;

    /**
     * load the authenticate form.
     *
     * @param  UserAuthToken $userAuthToken
     * @param  string        $resolution
     * @return AbstractForm
     */
    public function authForm(UserAuthToken $userAuthToken, ?string $resolution = 'web'): AbstractForm;

    /**
     * verify in the authentication process.
     *
     * @param  UserService  $userService
     * @param  array<mixed> $params
     * @return bool
     */
    public function verifyAuth(UserService $userService, array $params = []): bool;

    /**
     * verify in the activation process.
     *
     * @param  UserService  $userService
     * @param  array<mixed> $params
     * @return bool
     */
    public function verifyActivation(UserService $userService, array $params = []): bool;
}
