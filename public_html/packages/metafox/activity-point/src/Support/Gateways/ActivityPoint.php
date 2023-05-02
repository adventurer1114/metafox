<?php

namespace MetaFox\ActivityPoint\Support\Gateways;

use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use MetaFox\ActivityPoint\Support\Facade\ActivityPoint as ActivityPointFacade;
use MetaFox\Payment\Contracts\HasSupportSubscription;
use MetaFox\Payment\Contracts\HasSupportWebhook;
use MetaFox\Payment\Models\Order;
use MetaFox\Payment\Support\AbstractPaymentGateway;
use MetaFox\Payment\Support\Facades\Payment;
use MetaFox\Payment\Support\Traits\HasSupportSubscriptionTrait;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\MetaFox;
use RuntimeException;

/**
 * Class ActivityPoint.
 *
 * @ignore
 * @codeCoverageIgnore
 */
class ActivityPoint extends AbstractPaymentGateway implements HasSupportSubscription, HasSupportWebhook
{
    use HasSupportSubscriptionTrait;

    public const GATEWAY_SERVICE_NAME = 'activitypoint';

    private mixed $provider;

    protected array $billingFrequency = [
        //        SupportPayment::BILLING_DAILY => 'DAY',
        //        SupportPayment::BILLING_WEEKLY => 'WEEK',
        //        SupportPayment::BILLING_MONTHLY => 'MONTH',
        //        SupportPayment::BILLING_ANNUALLY => ' YEAR',
    ];

    public static function getGatewayServiceName(): string
    {
        return self::GATEWAY_SERVICE_NAME;
    }

    public function createGatewaySubscription(Order $order, array $params = []): array
    {
        return [];
    }

    public function createGatewayOrder(Order $order, array $params = []): array
    {
        $data = $order->toGatewayOrder();

        if (!$data) {
            throw new RuntimeException('Invalid order.');
        }

        $transaction = [
            'id'       => $order->itemId(),
            'currency' => $order->currency,
            'amount'   => $order->total,
            'status'   => $order->status,
            'raw_data' => [],
        ];

        $returnUrl = Arr::get($params, 'return_url');
        $cancelUrl = Arr::get($params, 'cancel_url');

        // Handle process buy item with activity points
        $result = ActivityPointFacade::proceedPayment($order);

        if ($result) {
            Payment::onPaymentSuccess($order, $transaction);
        }

        $redirectUrl = $result ? $returnUrl : $cancelUrl;

        // Return data based on result of previous step
        return [
            'status'               => $result,
            'gateway_order_id'     => Arr::get($data, 'id'),
            'gateway_redirect_url' => $this->toRedirectUrl($redirectUrl),
        ];
    }

    protected function toRedirectUrl(?string $url = null): ?string
    {
        if (null === $url) {
            return null;
        }

        if (MetaFox::isMobile()) {
            return url_utility()->convertUrlToLink($url, true);
        }

        return $url;
    }

    public function cancelGatewaySubscription(Order $order): array
    {
        return [];
    }

    /**
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    public function getGatewaySubscription(string $gatewaySubscriptionId): ?array
    {
        return null;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getGatewayTransaction(string $gatewayTransactionId): ?array
    {
        // to be implemented later
        return [];
    }

    public function getGatewayOrder(string $gatewayOrderId): ?array
    {
        return null;
    }

    public function getWebhookUrl(): string
    {
        return '';
    }

    public function verifyWebhook(array $payload): bool
    {
        return true;
    }

    public function handleWebhook(array $payload): bool
    {
        if (!$this->verifyWebhook($payload)) {
            throw new RuntimeException('Invalid request');
        }

        $eventType    = Str::lower(Arr::get($payload, 'event_type'));
        $handleMethod = Str::camel(Str::replace('.', ' ', 'handle.' . $eventType));
        if (method_exists($this, $handleMethod)) {
            try {
                Payment::onWebhook($payload);

                $this->$handleMethod($payload);

                return true;
            } catch (Exception $e) {
                Log::channel('payment')->error("Exception when handling webhook: {$e->getMessage()}", $payload);
            }
        }

        return false;
    }

    public function hasAccess(User $context, array $params): bool
    {
        if (!parent::hasAccess($context, $params)) {
            return false;
        }

        if (!$context->hasPermissionTo('activitypoint.can_purchase_with_activity_points')) {
            return false;
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function isDisabled(User $context, array $params): bool
    {
        $price = Arr::get($params, 'price', 0);

        if (!$price) {
            return true;
        }

        [$current, $cost] = $this->handlePrice($context, $price);

        if ($cost == -1 || $current < $cost) {
            return true;
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function describe(User $context, array $params): ?string
    {
        $price            = Arr::get($params, 'price', 0);
        [$current, $cost] = $this->handlePrice($context, $price);

        return __p('activitypoint::phrase.you_have_point_to_buy', compact('current', 'cost'));
    }

    /**
     * @param  User       $context
     * @param  float      $price
     * @return array<int>
     */
    protected function handlePrice(User $context, float $price): array
    {
        $currency      = app('currency')->getUserCurrencyId($context);
        $currentPoints = ActivityPointFacade::getTotalActivityPoints($context);
        $total         = ActivityPointFacade::convertPointFromPrice($currency, $price);

        return [$currentPoints, $total];
    }
}
