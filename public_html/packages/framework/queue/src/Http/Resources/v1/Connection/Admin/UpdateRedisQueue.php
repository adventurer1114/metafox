<?php

namespace MetaFox\Queue\Http\Resources\v1\Connection\Admin;

use Illuminate\Http\Request;
use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Yup\Yup;

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
class UpdateRedisQueue extends AbstractForm
{
    protected function prepare(): void
    {
        $res    = $this->resource ?? [];
        $action = sprintf('admincp/queue/connection/edit/%s/%s', $res['driver'] ?? 'redis', $res['name'] ?? 'redis');
        $value  = $res['value'] ?? [];

        $this->title(__p('queue::redis.form_title'))
            ->description('queue:redis.form_desc')
            ->action($action)
            ->asPost()
            ->setValue($value);
    }

    protected function initialize(): void
    {
        $this->addBasic()
            ->addFields(
                Builder::text('connection')
                    ->label(__p('queue::redis.connection_label'))
                    ->description(__p('queue::redis.connection_desc'))
                    ->yup(Yup::string()->required()),
                Builder::text('queue')
                    ->label(__p('queue::redis.queue_label'))
                    ->description(__p('queue::redis.queue_desc'))
                    ->yup(Yup::string()->required()),
                Builder::text('retry_after')
                    ->label(__p('queue::phrase.retry_after_label'))
                    ->description(__p('queue::phrase.retry_after_desc'))
                    ->yup(Yup::number()->unint()->optional()->nullable()),
                Builder::text('block_for')
                    ->label(__p('queue::phrase.block_for_label'))
                    ->description(__p('queue::phrase.block_for_desc'))
                    ->yup(Yup::string()->optional()->nullable()),
            );

        $this->addDefaultFooter(true);
    }

    public function boot(string $name)
    {
        $value          = Settings::get('queue.connections.' . $name);
        $this->resource = [
            'value'  => $value,
            'name'   => $name,
            'driver' => 'redis',
        ];
    }

    public function validated(Request $request): array
    {
        $data = $request->validate([
            'connection'  => 'required|string',
            'queue'       => 'required|string',
            'block_for'   => 'sometimes|int|nullable',
            'retry_after' => 'sometimes|number|nullable',
        ]);

        $data['driver'] = 'redis';

        return $data;
    }
}
