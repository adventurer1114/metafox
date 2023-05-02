<?php

namespace MetaFox\Notification\Messages;

use Illuminate\Notifications\Messages\MailMessage as BaseMessage;
use Illuminate\Support\Str;
use MetaFox\Platform\Facades\Settings;

class MailMessage extends BaseMessage
{
    private ?string $locale = null;

    public function __construct()
    {
        $signature = Settings::get('mail.signature');
        $this->salutation(Str::of(nl2br($signature))->toHtmlString());
    }

    public function line($line)
    {
        $htmlString = Str::of($line)->toHtmlString();

        return parent::line($htmlString);
    }

    public function locale(?string $locale = null): self
    {
        $this->locale   = $locale;
        $this->viewData = array_merge($this->viewData, ['locale' => $this->locale]);

        return $this;
    }
}
