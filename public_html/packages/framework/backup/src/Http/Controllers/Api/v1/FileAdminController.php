<?php

namespace MetaFox\Backup\Http\Controllers\Api\v1;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use MetaFox\Backup\Http\Resources\v1\File\Admin\CreateBackupForm;
use MetaFox\Backup\Http\Resources\v1\File\Admin\FileItemCollection as ItemCollection;
use MetaFox\Platform\Http\Controllers\Api\ApiController;
use MetaFox\Platform\Support\DbTableHelper;

/**
 * | --------------------------------------------------------------------------
 * |  Api Controller
 * | --------------------------------------------------------------------------
 * |
 * | stub: /packages/controllers/api_controller.stub
 * | Assign this class in $controllers of
 * | @link \MetaFox\Backup\Http\Controllers\Api\FileAdminController::$controllers;.
 */

/**
 * Class FileAdminController.
 * @codeCoverageIgnore
 * @ignore
 */
class FileAdminController extends ApiController
{
    /**
     * Browse item.
     *
     * @return mixed
     */
    public function index(): ItemCollection
    {
        $disk = app('storage')->disk('backup');

        $files = $disk->files('metafox-backup');

        $files = array_filter($files, function ($file) {
            return preg_match('/(.*)backup-(.+)\.zip/m', $file);
        });
        $files = array_reverse($files);

        $todate = function ($filename) {
            $date = preg_replace('/(.*)backup-(.+)\.zip/m', '$2 ', $filename);
            $arr  = explode('-', $date);

            return Carbon::createFromTime($arr[3], $arr[4], $arr[5], $arr[1], $arr[2], $arr[0]);
        };

        $files = array_map(function ($file) use ($disk, $todate) {
            return [
                'id'         => base64_encode($file),
                'filename'   => basename($file),
                'filesize'   => $disk->size($file),
                'created_at' => $todate($file),
            ];
        }, $files);

        return new ItemCollection($files);
    }

    public function create(): JsonResponse
    {
        $form = new CreateBackupForm();

        return $this->success($form);
    }

    public function download(string $id)
    {
        $disk = app('storage')
            ->disk('backup');

        $filename = base64_decode($id);

        if (!$disk->exists($filename)) {
            abort(404, 'File not found');
        }

        return $disk->download($filename);
    }

    public function store(): JsonResponse
    {
        $hasError = Artisan::call('backup:run');

        $output = Artisan::output();

        if (!$hasError) {
            $this->navigate('/admincp/backup/file/browse');
        } else {
            $this->message(__p('backup::phrase.backup_successfully'));
        }
        $this->alert(['message' => $output, 'maxWidth' => 'sm']);

        if ($hasError) {
            return $this->error($output);
        }

        return $this->success([
            'output' => $output,
        ]);
    }

    /**
     * Delete item.
     *
     * @param string $id
     *
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        $disk = app('storage')->disk('backup');

        $filename = base64_decode($id);

        if ($disk->exists($filename)) {
            $disk->delete($filename);
        }

        return $this->success([
            'id' => $id,
        ]);
    }

    public function prepare()
    {
        $disk = app('storage')->disk('backup');

        if (!$disk) {
            //
        }

        $config = config('backup');

        $lines = [];

        $lines[] = 'Backup Database ' . DB::getDatabaseName();

        $lines[] = 'Backup Files';
        foreach (Arr::get($config, 'backup.source.files.include', []) as $path) {
            $lines[] = './' . substr($path, strlen(base_path()) + 1);
        }

        return $this->success([
            implode(PHP_EOL, $lines),
        ]);
    }

    public function wizard()
    {
        $backupFile = app('storage')->disk('backup')->path('/');

        $steps = [
            [
                'id'        => 'information',
                'title'     => 'Backup Contents',
                'component' => 'ui.step.info',
                'props'     => [
                    'html' => view('backup::wizard.info', [
                        'dbName'     => DB::getDatabaseName(),
                        'dbDriver'   => DB::getDriverName(),
                        'dbVersion'  => DbTableHelper::getDriverVersion(),
                        'dbSize'     => human_readable_bytes(DbTableHelper::getDatabaseSize()),
                        'backupFile' => $backupFile,
                    ])->render(),
                    'hasSubmit'   => true,
                    'submitLabel' => __p('core::phrase.continue'),
                ],
            ],
            [
                'id'        => 'processing',
                'title'     => 'Processing',
                'component' => 'ui.step.processes',
                'props'     => [
                    'hasSubmit'   => true,
                    'submitLabel' => __p('core::phrase.continue'),
                    'steps'       => [
                        [
                            'title'            => 'Process Backup',
                            'disableUserAbort' => true,
                            'dataSource'       => ['apiUrl' => '/admincp/backup/file', 'apiMethod' => 'POST'],
                        ],
                    ],
                ],
            ],
            [
                'id'        => 'done',
                'title'     => 'Done',
                'component' => 'ui.step.info',
                'props'     => [
                    'html' => view('backup::wizard.report', [])->render(),
                ],
            ],
        ];

        return $this->success([
            'title'     => 'Backup Wizard',
            'component' => 'ui.step.steppers',
            //            'description'=> __p('backup::phrase.backup_wizard_guide'),
            'props' => [
                'steps' => $steps,
            ],
        ]);
    }
}
