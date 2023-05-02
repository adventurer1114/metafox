<?php

namespace MetaFox\Contact\Mails;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use MetaFox\Platform\Facades\Settings;

/**
 * stub: packages/mails/mail.stub.
 */

/**
 * Class Contact.
 *
 * @ignore
 * @codeCoverageIgnore
 */
class Contact extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    /** @var array<mixed> */
    private array $config = [];

    /**
     * @param array<mixed> $config
     */
    public function __construct(array $config)
    {
        $this->config = array_merge($config, [
            'from' => Settings::get('mail.from.address'),
        ]);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject(Arr::get($this->config, 'subject'))
            ->from(Arr::get($this->config, 'from'))
            ->html(Arr::get($this->config, 'html'));
    }
}
