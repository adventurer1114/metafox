<?php

namespace MetaFox\Queue\Http\Resources\v1\Connection\Admin;

use Illuminate\Http\Request;
use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Yup\Yup;
use PhpAmqpLib\Connection\AMQPLazyConnection;
use VladimirYuldashev\LaravelQueueRabbitMQ\Queue\Jobs\RabbitMQJob;

/**
 * | --------------------------------------------------------------------------
 * | Form Configuration
 * | --------------------------------------------------------------------------
 * | stub: src/Http/Resources/v1/Admin/SiteSettingForm.stub.
 */

/**
 * Class SiteSettingForm.
 * @codeCoverageIgnore
 * @ignore
 */
class UpdateRabbitMQQueue extends AbstractForm
{
    protected function prepare(): void
    {
        $res    = $this->resource ?? [];
        $action = sprintf('admincp/queue/connection/edit/%s/%s', $res['driver'] ?? 'rabbitmq', $res['name'] ?? 'rabbitmq');
        $value  = $res['value'] ?? [];

        $this->title(__p('queue::rabbitmq.form_title'))
            ->description('queue:sqs.form_desc')
            ->action($action)
            ->asPost()
            ->setValue($value);
    }

    protected function initialize(): void
    {
        $this->addBasic()
            ->addFields(
                Builder::text('queue')
                    ->label(__p('queue::rabbitmq.queue_label'))
                    ->description(__p('queue::rabbitmq.queue_desc'))
                    ->yup(Yup::string()->required()),
                Builder::text('hosts.0.host')
                    ->label(__p('queue::rabbitmq.host_label'))
                    ->description(__p('queue::rabbitmq.host_desc'))
                    ->yup(Yup::string()->required()),
                Builder::text('hosts.0.port')
                    ->label(__p('queue::rabbitmq.port_label'))
                    ->description(__p('queue::rabbitmq.port_desc'))
                    ->yup(Yup::string()->required()),
                Builder::text('hosts.0.user')
                    ->label(__p('queue::rabbitmq.user_label'))
                    ->description(__p('queue::rabbitmq.user_desc'))
                    ->yup(Yup::string()->required()),
                Builder::text('hosts.0.password')
                    ->label(__p('queue::rabbitmq.password_label'))
                    ->description(__p('queue::rabbitmq.password_desc'))
                    ->yup(Yup::string()->required()),
                Builder::text('hosts.0.vhost')
                    ->label(__p('queue::rabbitmq.vhost_label'))
                    ->description(__p('queue::rabbitmq.vhost_desc'))
                    ->yup(Yup::string()->optional()->nullable())
            );

        $this->addDefaultFooter(true);
    }

    public function boot(string $name)
    {
        $value          = Settings::get('queue.connections.' . $name);
        $this->resource = [
            'value'  => $value,
            'name'   => $name,
            'driver' => 'rabbitmq',
        ];
    }

    public function validated(Request $request): array
    {
        $data = $request->validate([
            'queue'            => 'required|string',
            'suffix'           => 'sometimes|string|nullable',
            'hosts.*.host'     => 'required|string',
            'hosts.*.port'     => 'required|number',
            'hosts.*.user'     => 'required|string',
            'hosts.*.password' => 'required|string',
            'hosts.*.vhost'    => 'required|string',
        ]);

        $data['options']    = config('queue.connections.rabbitmq.options');
        $data['driver']     = 'rabbitmq';
        $data['connection'] = AMQPLazyConnection::class;
        $data['worker']     = config('queue.connections.rabbitmq.worker');

        return $data;
    }
}
