<?php

namespace MetaFox\Event\Mails;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;

/**
 * stub: packages/mails/mail.stub.
 */

/**
 * Class EventMail.
 *
 * @ignore
 * @codeCoverageIgnore
 */
class EventMail extends Mailable implements ShouldQueue
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
        $this->config = $config;
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
