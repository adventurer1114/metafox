<?php

namespace MetaFox\Platform\Traits\Http\Request;

use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Platform\Rules\AllowInRule;
use MetaFox\Platform\Rules\AllowMaxFilesRule;

/**
 * Trait HasFeedParam.
 * @property Content $resource
 */
trait AttachmentRequestTrait
{
    /**
     * @param  array<string, mixed> $rules
     * @return array<string, mixed>
     */
    protected function applyAttachmentRules(array $rules): array
    {
        $maxFiles = Settings::get('core.attachment.maximum_number_of_attachments_that_can_be_uploaded', 5);

        return array_merge($rules, [
            'attachments'          => ['sometimes', 'array', new AllowMaxFilesRule($maxFiles)],
            'attachments.*.id'     => ['sometimes', 'numeric', 'exists:core_attachments,id'],
            'attachments.*.status' => ['sometimes', 'string', new AllowInRule(['create', 'new', 'remove'])],
        ]);
    }
}
