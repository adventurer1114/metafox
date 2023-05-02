<?php

namespace MetaFox\Activity\Http\Requests\v1\Feed;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use MetaFox\Activity\Traits\HasCheckinTrait;
use MetaFox\Activity\Traits\HasTaggedFriendTrait;
use MetaFox\Core\Constants;
use MetaFox\Core\Repositories\DriverRepositoryInterface;
use MetaFox\Platform\Facades\PolicyGate;
use MetaFox\Platform\MetaFoxPrivacy;
use MetaFox\Platform\Rules\AllowInRule;
use MetaFox\Platform\Rules\PrivacyRule;
use MetaFox\User\Support\Facades\UserEntity;

/**
 * --------------------------------------------------------------------------
 *  Http request for api version v1
 * --------------------------------------------------------------------------.
 *
 * This class is used by automatic dependency injection:
 *
 * @link \MetaFox\Activity\Http\Controllers\Api\v1\FeedController::store;
 * stub: api_action_request.stub
 */

/**
 * Class StoreRequest.
 */
class StoreRequest extends FormRequest
{
    use HasTaggedFriendTrait;
    use HasCheckinTrait;

    /***********************************************************************
     *
     *  {
     * "user_status" : "New status ",
     * "post_type" : "activity_post",
     * // "tagged_friends" : {{friend_id}},
     * "location" : {
     * "address" : "Notre Dame Cathedral of Saigon 2",
     * "lat" : 10.7797908,
     * "lng" : 106.6968302
     * },
     * "parent_item_type":"",
     * "parent_item_id":"",
     * "post_as_parent":0,
     * "privacy" : 0
     * }
     *
     *
     *
     */

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function rules(): array
    {
        $rules = [
            'post_type'            => ['required'],
            'privacy'              => ['sometimes', new PrivacyRule()],
            'status_background_id' => ['sometimes', 'numeric', ' min:1'],

            // Post to.
            'parent_item_id' => ['sometimes', 'exists:user_entities,id'],
            // Post as parent
            'post_as_parent' => ['sometimes', new AllowInRule([true, false])],
            'user_status'    => ['sometimes'],
        ];

        $rules = $this->applyLocationRules($rules);

        $rules = $this->applyTaggedFriendsRules($rules);

        return $rules;
    }

    /**
     * @throws AuthenticationException
     */
    public function validated($key = null, $default = null)
    {
        $data = parent::validated($key, $default);

        $postType = Arr::get($data, 'post_type');

        $formData = $this->validatedPostType($postType);

        if (count($formData)) {
            $data = array_merge($data, $formData);
        }

        return $this->transform($data);
    }

    protected function validatedPostType(string $postType): array
    {
        $driver = resolve(DriverRepositoryInterface::class)
            ->getDriver(Constants::DRIVER_TYPE_FORM, $postType . '.feed_form', 'web');

        $form = app()->make($driver, [
            'resource' => null,
            'isEdit'   => $this->isEdit(),
        ]);

        $response = [];

        if (is_object($form) && method_exists($form, 'validated')) {
            $formData = app()->call([$form, 'validated']);

            if (is_array($formData)) {
                $response = $formData;
            }
        }

        return $response;
    }

    protected function isEdit(): bool
    {
        return false;
    }

    /**
     * @param array<string,           mixed> $data
     *
     * @return array<string,           mixed>
     * @throws AuthenticationException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function transform(array $data): array
    {
        $data = $this->transformPostAsParent($data);

        if (!Arr::has($data, 'privacy')) {
            Arr::set($data, 'privacy', MetaFoxPrivacy::EVERYONE);
        }

        $data['list'] = [];

        if (is_array($data['privacy'])) {
            $data = array_merge($data, [
                'list'    => Arr::get($data, 'privacy'),
                'privacy' => MetaFoxPrivacy::CUSTOM,
            ]);
        }

        if ($this->isEnableCheckin()) {
            $data['location_name']      = Arr::get($data, 'location.address');
            $data['location_latitude']  = Arr::get($data, 'location.lat');
            $data['location_longitude'] = Arr::get($data, 'location.lng');
        }

        unset($data['location']);

        if ($this->isEnableTagFriends()) {
            $data['tagged_friends'] = $this->handleTaggedFriend($data);
        }

        $userStatus = Arr::get($data, 'user_status');

        if (null === $userStatus) {
            Arr::set($data, 'user_status', '');
        }

        return $data;
    }

    protected function transformPostAsParent(array $data): array
    {
        $user = $owner = user();

        Arr::set($data, 'owner', $owner);

        Arr::set($data, 'user', $user);

        if (!Arr::has($data, 'parent_item_id')) {
            return $data;
        }

        // Login as page
        if ($data['parent_item_id'] == $user->entityId()) {
            return $data;
        }

        if ($this->isEdit()) {
            unset($data['post_as_parent']);

            return $data;
        }

        $owner = UserEntity::getById($data['parent_item_id'])->detail;

        Arr::set($data, 'owner', $owner);

        $postAsParent = Arr::get($data, 'post_as_parent', 0);

        if (!$postAsParent) {
            return $data;
        }

        $policy = PolicyGate::getPolicyFor(get_class($owner));

        if (null === $policy) {
            return $data;
        }

        if (!method_exists($policy, 'postAsParent')) {
            return $data;
        }

        policy_authorize(get_class($policy), 'postAsParent', $user, $owner);

        Arr::set($data, 'user', $owner);

        unset($data['post_as_parent']);

        return $data;
    }
}
