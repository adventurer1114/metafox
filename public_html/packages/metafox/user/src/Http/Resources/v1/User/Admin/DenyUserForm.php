<?php

namespace MetaFox\User\Http\Resources\v1\User\Admin;

use Illuminate\Http\Request;
use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder;
use MetaFox\User\Models\User as Model;
use MetaFox\User\Support\Facades\UserEntity;
use MetaFox\Yup\Yup;

/**
 * Class DenyUserForm.
 * @property Model $resource
 * @driverType form
 * @driverName user.deny_user
 */
class DenyUserForm extends AbstractForm
{
    private int $userId;
    private string $userName;

    public function boot(Request $request, ?int $id): void
    {
        $this->userId   = $id;
        $this->userName = UserEntity::getById($this->userId)->name;
    }

    protected function prepare(): void
    {
        $this->action("admincp/user/deny-user/{$this->userId}")
            ->asPatch()
            ->setValue([]);
    }

    public function initialize(): void
    {
        $this->title(__p('user::phrase.deny_user'));

        $basic = $this->addBasic();

        $basic->addFields(
            Builder::typography()
                ->plainText(__p('user::mail.send_email_description_when_deny_user', ['username' => $this->userName]))
                ->color('text.secondary'),
            Builder::text('subject')
                ->required()
                ->label(__p('user::mail.subject'))
                ->placeholder(__p('user::mail.subject'))
                ->yup(
                    Yup::string()
                        ->required()
                ),
            Builder::textArea('message')
                ->required()
                ->label(__p('core::phrase.message'))
                ->placeholder(__p('core::phrase.message'))
                ->yup(
                    Yup::string()
                        ->required()
                ),
        );

        $this->addDefaultFooter();
    }
}
