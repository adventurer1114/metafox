<?php

namespace MetaFox\HealthCheck\Checks;

use MetaFox\Platform\HealthCheck\Checker;
use MetaFox\Platform\HealthCheck\Result;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class CheckFilesystemPermission extends Checker
{

    public function check(): Result
    {
        $result = $this->makeResult();
        $this->checkWriableDirectory($result);
        $this->checkPermissions($result);
        $this->checkUnxpectedDirectories($result);

        if ($result->okay()) {
            $result->success('Filesystem is writable');
        }

        return $result;
    }

    public function checkWriableDirectory(Result $result)
    {
        $directories = [
            './storage',
            './storage/logs',
            './bootstrap/cache',
            './public',
            './public/install',
            './storage/app/web',
            './storage/app/public',
            './storage/framework',
        ];

        foreach ($directories as $dir) {
            $path = realpath(base_path($dir));

            if (!$path || !is_dir($path)) {
                continue;
            }

            if (!is_writable($path)) {
                $result->error(sprintf("Directory %s is not writable", $dir));
            }
        }
    }

    public function checkPermissions(Result $result)
    {
        $directories = [
            './app',
            './config',
            './database',
            './packages',
            './public',
            './resources',
            './routes',
            './storage/app/logs',
        ];


        foreach ($directories as $dir) {
            $this->checkFilePermissions($result, $dir, '0644', '0755');
        }

        if ($result->okay()) {
            $result->success('Checked file/directory perrmisions');
        }

        return $result;
    }


    public function checkUnxpectedDirectories(Result $result)
    {
        $directories = [
            'public/install'
        ];

        foreach ($directories as $dir) {
            $path = base_path($dir);
            if (is_dir($path)) {
                $result->error(sprintf('Unexpected directory "%s" exists.', $dir));
            }
        }
    }

    public function getName()
    {
        return 'File Permisions';
    }

    private function checkFilePermissions(Result $result, string $dir, $filePermission, $dirPermission)
    {
        $path = realpath(base_path($dir));

        if (!$path || !is_dir($path)) {
            return;
        }

        $flags = \FilesystemIterator::SKIP_DOTS | \FilesystemIterator::UNIX_PATHS;
        $directoryIterator = new RecursiveDirectoryIterator($path, $flags);
        /** @var \SplFileInfo[] $iterator */
        $iterator = new RecursiveIteratorIterator($directoryIterator);

        $prefix = strlen(base_path(''));
        $limit = 3;
        $failedCount = 0;
        $found = [];

        foreach ($iterator as $file) {
            $pathname = $file->getPathname();
            $perms = substr(sprintf('%o', $file->getPerms()), -4);

            if ($file->isFile() &&
                $file->getExtension() === 'php'
                && $perms != $filePermission) {
                if (++$failedCount < $limit) {
                    $found[] = sprintf('Expected %s permission is %s but actually is %s', substr($pathname, $prefix),
                        $filePermission, $perms);
                }
            } elseif ($file->isDir() && $perms != $dirPermission) {
                if (++$failedCount < $limit) {
                    $found[] = sprintf('Expected %s permission is %s but actually is %s', substr($pathname, $prefix),
                        $dirPermission, $perms);
                }
            }
        }

        if (!$failedCount) {
            return;
        }

        $result->warn(sprintf("Failed checking file permission: %s", $path));

        foreach ($found as $item) {
            $result->warn($item);
        }

        if ($failedCount > $limit) {
            $result->warn(sprintf('and %d others', $failedCount - $limit));
        }
    }
}