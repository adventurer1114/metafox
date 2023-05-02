<?php

/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Payment\Support\Facades;

use Illuminate\Support\Facades\Facade;
use MetaFox\Payment\Contracts\GatewayManagerInterface;
use MetaFox\Payment\Contracts\IsBillable;
use MetaFox\Payment\Models\Order;
use MetaFox\Payment\Support\Payment as SupportPayment;

/**
 * Class Payment.
 * @method static GatewayManagerInterface getManager();
 * @method static ?GatewayForm getGatewayAdminFormById(int $gatewayId);
 * @method static ?GatewayForm getGatewayAdminFormByName(string $formName);
 * @method static Order initOrder(IsBillable $billable);
 * @method static array placeOrder(Order $order, int $gatewayId, array $params = []);
 * @method static array cancelSubscription(Order $order): array;
 * @method static void  onSubscriptionActivated(Order $order, ?array $data = []);
 * @method static void  onSubscriptionExpired(Order $order, ?array $data = []);
 * @method static void  onSubscriptionCancelled(Order $order, ?array $data = []);
 * @method static void  onRecurringPaymentFailure(Order $order, ?array $data = []);
 * @method static void  onPaymentSuccess(Order $order, array $transactionData = [], ?array $data = []);
 * @method static void  onPaymentPending(Order $order, ?array $transactionData = [], ?array $data = []);
 * @method static void  onPaymentFailure(Order $order, ?array $transactionData = [], ?array $data = []);
 * @method static void  onWebhook(array $payload)
 * @method static string|null  getHandler(string $type)
 * @link   MetaFox\Payment\Support\Payment;
 */
class Payment extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return SupportPayment::class;
    }
}
