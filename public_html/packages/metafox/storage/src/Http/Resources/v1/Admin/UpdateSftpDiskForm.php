<?php

namespace MetaFox\Storage\Http\Resources\v1\Admin;

use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder;
use MetaFox\Storage\Http\Requests\v1\Disk\Admin\UpdateSftpDiskRequest as Request;
use MetaFox\Storage\Support\SelectDiskVisibility;
use MetaFox\Storage\Support\StorageDiskValidator;
use MetaFox\Yup\Yup;

/**
 * --------------------------------------------------------------------------
 * Form Configuration
 * --------------------------------------------------------------------------
 * stub: /packages/resources/edit_form.stub.
 */

/**
 * Class UpdateSftpForm.
 * @ignore
 * @codeCoverageIgnore
 */
class UpdateSftpDiskForm extends AbstractForm
{
    protected function prepare(): void
    {
        $resource = $this->resource;
        $action   = apiUrl('admin.storage.config.update', ['driver' => $resource['driver'], 'disk' => $resource['id']]);
        $value    = $resource['value'] ?? [];
        $value    = array_merge(['visibility' => 'public', 'driver' => 'sftp'], $value);

        $this->title(__p('storage::phrase.update_sfpt_title'))
            ->action($action)
            ->asPut()
            ->setValue($value);
    }

    protected function initialize(): void
    {
        $this->addBasic()
            ->addFields(
                Builder::text('host')
                    ->required()
                    ->label(__p('storage::phrase.sftp_host_name'))
                    ->yup(Yup::string()->required()),
                Builder::text('port')
                    ->label(__p('storage::phrase.sftp_port'))
                    ->description('Port (optional, default: 22)')
                    ->yup(Yup::number()->unint()->nullable()),
                Builder::text('timeout')
                    ->label(__p('storage::phrase.sftp_timeout'))
                    ->description('Default: 30 seconds')
                    ->yup(Yup::number()->unint()->min(5)->nullable()),
                Builder::text('username')
                    ->required()
                    ->label(__p('storage::phrase.sftp_username'))
                    ->yup(Yup::string()->required()),
                Builder::text('password')
                    ->label(__p('storage::phrase.sftp_password'))
                    ->description('password (optional, default: null) set to null if privateKey is used')
                    ->yup(Yup::string()->nullable()),
                // Optional SFTP Settings...
                Builder::text('privateKey')
                    ->label(__p('storage::phrase.sftp_private_key'))
                    ->description('private key (optional, default: null) can be used instead of password, set to null if password is set')
                    ->yup(Yup::string()->nullable()),
                Builder::text('maxTries')
                    ->label(__p('storage::phrase.sftp_max_retries'))
                    ->yup(Yup::number()->unint()->nullable()),
                Builder::text('passphrase')
                    ->label(__p('storage::phrase.sftp_passphrase'))
                    ->description('passphrase (optional, default: null), set to null if privateKey is not used or has no passphrase')
                    ->yup(Yup::string()->nullable()),
                Builder::text('hostFingerprint')
                    ->label(__p('storage::phrase.sftp_finger_print'))
                    ->description('passphrase (optional, default: null), set to null if privateKey is not used or has no passphrase')
                    ->yup(Yup::number()->unint()->nullable()),
                Builder::text('root')
                    ->label(__p('storage::phrase.sftp_root'))
                    ->yup(Yup::string()->optional()->nullable()),
                Builder::checkbox('useAgent')
                    ->label(__p('storage::phrase.sftp_user_agent'))
                    ->description('use agent (optional, default: false)'),
                Builder::checkbox('throw')
                    ->required()
                    ->label(__p('storage::phrase.storage_throws')),
                new SelectDiskVisibility(),
                Builder::hidden('driver'),
            );

        $this->addDefaultFooter(true);
    }

    /**
     * @param  Request $request
     * @return array
     */
    public function validated(Request $request): array
    {
        $data = $request->validated();

        StorageDiskValidator::isValid($data);

        return $data;
    }
}
