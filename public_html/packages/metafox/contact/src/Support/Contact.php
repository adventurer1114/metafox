<?php

namespace MetaFox\Contact\Support;

use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Mail;
use MetaFox\Contact\Contracts\Contact as ContractsContact;
use MetaFox\Contact\Mails\Contact as MailsContact;
use MetaFox\Contact\Repositories\CategoryRepositoryInterface;
use MetaFox\Platform\Facades\Settings;

/**
 * Class Contact.
 * @SuppressWarnings(PHPMD.LongVariable)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Contact implements ContractsContact
{
    public function __construct(
        protected CategoryRepositoryInterface $categoryRepository
    ) {
    }

    public function send(array $params = []): void
    {
        $fullName     = Arr::get($params, 'full_name');
        $subject      = Arr::get($params, 'subject');
        $email        = Arr::get($params, 'email');
        $message      = Arr::get($params, 'message');
        $categoryId   = Arr::get($params, 'category_id', 0);
        $category     = $this->categoryRepository->find($categoryId);
        $recipients   = array_filter(explode(',', Settings::get('contact.staff_emails')));
        $sendResponse = Settings::get('contact.enable_auto_responder', true);

        if (empty($recipients)) {
            throw new Exception('There are no configured recipients for Contact form.');
        }

        if (Arr::get($params, 'send_copy')) {
            $recipients[] = $email;
        }

        Mail::to($recipients)
            ->send(new MailsContact([
                'subject' => __p('contact::mail.contact_email_subject', [
                    'category' => $category->name,
                    'subject'  => $subject,
                ]),
                'html' => __p('contact::mail.contact_email_html', [
                    'full_name' => $fullName,
                    'email'     => $email,
                    'message'   => $message,
                ]),
            ]));

        if ($sendResponse) {
            $this->sendResponse([$email]);
        }
    }

    public function sendResponse(array $recipients): void
    {
        Mail::to($recipients)
            ->send(new MailsContact([
                'subject' => __p('contact::mail.thank_you_for_contacting_us_subject'),
                'html'    => __p('contact::mail.thank_you_for_contacting_us_html'),
            ]));
    }
}
