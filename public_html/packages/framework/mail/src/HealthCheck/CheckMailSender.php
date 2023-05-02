<?php

namespace MetaFox\Mail\HealthCheck;

use Illuminate\Support\Facades\Mail;
use MetaFox\Mail\Mails\HealthCheck;
use MetaFox\Platform\HealthCheck\Checker;
use MetaFox\Platform\HealthCheck\Result;

class CheckMailSender extends Checker
{
    public function check(): Result
    {
        $result = $this->makeResult();

        try {
            $sender = config('mail.default');

            $result->debug(sprintf('Send mail method "%s"', config('mail.default')));

            if ($sender === 'log') {
                $result->error(sprintf('Current mail sender is [%s], configure mail sender please.', $sender));
            }

            if (!config('mail.from.address') || !config('mail.from.name')) {
                $result->error('Missed mail <a target="_blank" href="/admincp/mail/setting">settings configuration</a>');
            } else {
                $result->debug(sprintf('Send mail from name: %s, email: %s', config('mail.from.name'),
                    config('mail.from.address')));
            }

            if (!config('mail.from.address')) {
                $result->error(sprintf('Mising mail.from.address'));
            }
            if (!config('mail.from.name')) {
                $result->error(sprintf('Mising mail.from.name'));
            }

            Mail::to(config('app.site_email'))->send(new HealthCheck());
        } catch (\Exception $exception) {
            $result->error(sprintf($exception->getMessage()));
        }
        // try to send mail directly.
        return $result;
    }

    public function getName()
    {
        return 'Send Mail Methods';
    }

}
