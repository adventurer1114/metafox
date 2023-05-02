<?php

namespace MetaFox\Storage\Rules;

use Illuminate\Contracts\Validation\Rule as RuleContract;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;

class MaxFileUpload implements RuleContract
{
    private int $maxUploadSize = 0;

    /**
     * @inheritDoc
     * @throws \Illuminate\Validation\ValidationException
     */
    public function passes($attribute, $value): bool
    {
        if (!$value instanceof UploadedFile) {
            return false;
        }

        $maxUpload = $this->getMaxUploadSize($value);

        // Unlimited case
        if ($maxUpload == 0) {
            return true;
        }

        $validator = Validator::make(
            [$attribute => $value],
            [$attribute => ['max:' . $maxUpload]],
        );

        return $validator->passes();
    }

    /**
     * @inheritDoc
     */
    public function message(): string
    {
        return __p('storage::validation.uploaded_file_exceed_limit', ['limit' => $this->maxUploadSize]);
    }

    public function getMaxUploadSize(UploadedFile $file): int
    {
        $fileType            = file_type()->getTypeByMime($file->getMimeType());
        $this->maxUploadSize = (int) round(file_type()->getFilesizePerType($fileType ?? '') / 1024);

        return $this->maxUploadSize;
    }
}
