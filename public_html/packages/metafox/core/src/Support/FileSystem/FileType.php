<?php

namespace MetaFox\Core\Support\FileSystem;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use MetaFox\Platform\Contracts\MetaFoxFileTypeInterface;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Platform\MetaFoxFileType;

class FileType implements MetaFoxFileTypeInterface
{
    public const PHOTO_MIMES_TYPES = 'image/*';
    public const VIDEO_MIMES_TYPES = 'video/*';
    public const AUDIO_MIMES_TYPES = 'audio/mp3';

    public const PHOTO_MIME_TYPE_REGEX = '/^image\/([a-zA-Z0-9\.\-\+]+)$/m';
    public const VIDEO_MIME_TYPE_REGEX = '/^video\/([a-zA-Z0-9\.\-\+]+)$/m';
    public const AUDIO_MIME_TYPE_REGEX = '/^audio\/([a-zA-Z0-9\.\-\+]+)$/m';

    /**
     * @var array<string>
     */
    public static array $types = [
        MetaFoxFileType::PHOTO_TYPE => self::PHOTO_MIMES_TYPES,
        MetaFoxFileType::VIDEO_TYPE => self::VIDEO_MIMES_TYPES,
        MetaFoxFileType::AUDIO_TYPE => self::AUDIO_MIMES_TYPES,
    ];

    public function isAllowType(string $type): bool
    {
        return array_key_exists($type, self::$types);
    }

    public function getMimeTypeFromType(string $type): string
    {
        if (!$this->isAllowType($type)) {
            return '';
        }

        return self::$types[$type];
    }

    public function verifyMime(UploadedFile $file, string $type): bool
    {
        if (!$this->isAllowType($type)) {
            return false;
        }

        $fileMimeType = $file->getMimeType();

        return $this->verifyMimeTypeByType($fileMimeType, $type);
    }

    /**
     * @inheritDoc
     */
    public function getFilesizePerType(string $type): int
    {
        $filesize    = Settings::get('storage.filesystems.max_upload_filesize', []);

        $defaultSize = Arr::get($filesize, 'other', 8 * 1024 * 1024);

        return Arr::get($filesize, $type, $defaultSize);
    }

    public function getFilesizeInMegabytes(string $type): float
    {
        $fileSize = $this->getFilesizePerType($type);

        if (0 == $fileSize) {
            return 0;
        }

        $megabytes = 1024 * 1024;

        return round($fileSize / $megabytes, 2);
    }

    /**
     * @inheritDoc
     */
    public function getTypeByMime(?string $mimeType): ?string
    {
        if (null === $mimeType) {
            return null;
        }

        if (preg_match(self::PHOTO_MIME_TYPE_REGEX, $mimeType)) {
            return MetaFoxFileType::PHOTO_TYPE;
        }

        if (preg_match(self::VIDEO_MIME_TYPE_REGEX, $mimeType)) {
            return MetaFoxFileType::VIDEO_TYPE;
        }
        if (preg_match(self::AUDIO_MIME_TYPE_REGEX, $mimeType)) {
            return MetaFoxFileType::AUDIO_TYPE;
        }

        return null;
    }

    public function verifyMimeTypeByType(?string $mimeType, string $fileType = 'photo'): bool
    {
        if (!$mimeType) {
            return false;
        }

        //@todo: expose risk to pypass
        return match ($fileType) {
            MetaFoxFileType::VIDEO_TYPE => (bool) preg_match(self::VIDEO_MIME_TYPE_REGEX, $mimeType),
            MetaFoxFileType::AUDIO_TYPE => (bool) preg_match(self::AUDIO_MIME_TYPE_REGEX, $mimeType),
            default                     => (bool) preg_match(self::PHOTO_MIME_TYPE_REGEX, $mimeType),
        };
    }

    /**
     * @param  string $fileType
     * @return string
     */
    public function transformFileType(string $fileType): string
    {
        $type = $this->getTypeByMime($fileType);
        if ($type) {
            return $type;
        }

        if (!$this->isAllowType($fileType)) {
            return MetaFoxFileType::PHOTO_TYPE;
        }

        return $fileType;
    }

    /**
     * @inheritDoc
     */
    public function getFilesizeReadableString(int $bytes): string
    {
        if ($bytes == 0) {
            return (string) $bytes;
        }

        if ($bytes < 1024) {
            return $bytes . 'B';
        }

        $kiloBytes = round($bytes / 1024, 2);
        if ($kiloBytes > 1 && $kiloBytes < 1024) {
            return $kiloBytes . ' KB';
        }

        $asMegabytes = round($kiloBytes / 1024, 2);
        if ($asMegabytes > 1 && $asMegabytes < 1024) {
            return $asMegabytes . ' MB';
        }

        $asGigabytes = round($asMegabytes / 1024, 2);
        if ($asGigabytes > 1 && $asGigabytes < 1024) {
            return $asGigabytes . ' GB';
        }

        $asTerabytes = round($asGigabytes / 1024, 2);
        if ($asTerabytes > 1 && $asTerabytes < 1024) {
            return $asTerabytes . ' TB';
        }

        return round($asTerabytes / 1024, 2) . 'PB';
    }
}
