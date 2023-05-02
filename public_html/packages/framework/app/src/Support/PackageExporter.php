<?php

namespace MetaFox\App\Support;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Platform\PackageManager;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use ZipArchive;

/**
 * Class Export.
 */
class PackageExporter
{
    /**
     * @param  string $name
     * @param  string $version
     * @param  bool   $local
     * @return string
     */
    public function getExportPath(string $name, string $version, bool $local): string
    {
        if ($local) {
            $suffix = date('ymd');

            return sprintf(
                '%s/%s-%s-%s.zip',
                'exports',
                str_replace('/', '-', $name),
                $version,
                $suffix
            );
        }

        return sprintf(
            '%s-%s.zip',
            str_replace('/', '-', $name),
            $version
        );
    }

    /**
     * Execute the console command.
     *
     * @param  string    $package
     * @param  bool|null $release
     * @param  string    $channel
     * @return string
     */
    public function export(string $package, bool $release, string $channel): string
    {
        $json          = PackageManager::getComposerJson($package);
        $version       = Arr::get($json, 'version');
        $dir           = Arr::get($json, 'extra.metafox.path');
        $frontendPaths = Arr::get($json, 'extra.metafox.frontendPaths');
        $frontendRoot  = config('app.mfox_frontend_root');

        $root = base_path();

        $archive = new ZipArchive();
        $tmp     = tempnam(sys_get_temp_dir(), 'bundle'); // good

        if (file_exists($tmp)) {
            @unlink($tmp);
        }

        if (!$archive->open($tmp, ZipArchive::CREATE)) {
            throw new InvalidArgumentException(sprintf('Could not create archive at %s', $tmp));
        }

        if (!$dir) {
            throw new \InvalidArgumentException('Missing path for ' . $package);
        }

        $dir = base_path($dir);

        $this->addDirectory($archive, $dir, $root, MetaFoxConstant::BACKEND_WRAP_NAME);

        if (file_exists($tmp)) {
            @unlink($tmp);
        }

        if ($frontendRoot && !is_dir($frontendRoot)) {
            throw new \RuntimeException('Failed finding ' . $frontendRoot);
        }

        if ($frontendRoot && is_array($frontendPaths)) {
            $this->log('Checking ', $frontendPaths);
            foreach ($frontendPaths as $frontendPath) {
                $frontendDir = realpath($frontendRoot . '/' . $frontendPath);
                if (!is_dir($frontendDir)) {
                    throw new \RuntimeException('Failed getting ' . $frontendDir);
                }
                $this->addDirectory($archive, $frontendDir, $frontendRoot, MetaFoxConstant::FRONTEND_WRAP_NAME);
            }
        }

        if (!$archive->getFromName(MetaFoxConstant::FRONTEND_WRAP_NAME)) {
            // add frontend directory
            $archive->addEmptyDir(MetaFoxConstant::FRONTEND_WRAP_NAME);
        }

        $numFiles = $archive->numFiles;
        $checksum = $this->calculatePackageChecksum($archive);

        $archive->close();
        $name = str_replace('/', '-', $package) . '-' . $version . '.zip';

        $this->log(sprintf('bundled %s %d -> %s', $tmp, $numFiles, $name));
        $this->log(sprintf('checksum %s', $checksum));

        // test order checksum.
        if ($release) {
            app(MetaFoxStore::class)->publishToStore($package, $version, $name, $tmp, $channel);
            $this->log(sprintf('Uploaded %s to MetaFox store', $name));
        }

        return $tmp;
    }

    public function calculatePackageChecksum(ZipArchive $archive): string
    {
        $sum = [];

        for ($index = 0; $index < $archive->numFiles; $index++) {
            if (($content  = $archive->getFromIndex($index))) {
                $sum[] = sha1($content);
            }
        }
        $checksum = sha1(implode(PHP_EOL, $sum));

        return $checksum;
    }

    private function addDirectory(ZipArchive $archive, string $dir, string $root, string $prefix): void
    {
        if (app('files')->exists($dir)) {
            $this->log(sprintf('Adding path %s', $dir));
        } else {
            $this->log(sprintf('Path not found "%s"', $dir));

            return;
        }

        /** @var SplFileInfo[] $rii */
        $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));

        foreach ($rii as $file) {
            if ($file->isDir()) {
                continue;
            } elseif ($file->isLink()) {
                continue;
            } else {
                $from = $file->getPathname();
                $to   = sprintf('%s/%s', $prefix, substr($from, strlen($root) + 1));
                $this->log('Added file ' . $to);
                $result = $archive->addFile($from, $to);

                if (!$result) {
                    $this->log(sprintf('Could not archive %s', $from));
                }
            }
        }
    }

    public function log(string $text, array $context = []): void
    {
        Log::channel('dev')->info($text, $context);
    }
}
