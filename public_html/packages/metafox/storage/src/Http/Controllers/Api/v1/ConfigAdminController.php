<?php

namespace MetaFox\Storage\Http\Controllers\Api\v1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use MetaFox\Core\Repositories\DriverRepositoryInterface;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Platform\Http\Controllers\Api\ApiController;
use MetaFox\Storage\Http\Requests\v1\Config\Admin\StoreRequest;
use MetaFox\Storage\Http\Resources\v1\Admin\SelectDiskDriverForm;

/**
 * | --------------------------------------------------------------------------
 * |  Api Controller
 * | --------------------------------------------------------------------------
 * |
 * | stub: /packages/controllers/api_controller.stub
 * | Assign this class in $controllers of
 * | @link \MetaFox\Storage\Http\Controllers\Api\DiskAdminController::$controllers.
 */

/**
 * Class DiskAdminController.
 * @codeCoverageIgnore
 * @ignore
 */
class ConfigAdminController extends ApiController
{
    public function index(): JsonResponse
    {
        $data = Settings::get('storage.filesystems.disks');

        $disks = [];

        if (is_array($data)) {
            foreach ($data as $id => $value) {
                if ($value['driver'] === 'alias') {
                    continue;
                }

                $disks[] = $this->transformDiskResource($id, $value);
            }
        }

        return $this->success($disks);
    }

    public function transformDiskResource(mixed $id, ?array $data): array
    {
        $name          = sprintf('%s', $id);
        $status        = '';
        $basePath      = '/';
        $baseUrl       = '';
        $driver        = $data['driver'] ?? null;
        $appRoot       = base_path();
        $defaultDiskId = Settings::get('storage.filesystems.default');

        try {
            $disk     = Storage::build($data);
            $basePath = $disk->path('filename');
            $baseUrl  = $disk->url('filename');
        } catch (\Exception $exception) {
            $status = $exception->getMessage();
        }

        if ($driver === 'local' && Str::startsWith($basePath, $appRoot)) {
            $basePath = '.' . Str::substr($basePath, strlen($appRoot));
        }

        $isDefault  = $name == $defaultDiskId;
        $isEditable = $name !== 'local';
        $isSystem   = in_array($name, ['local', 'public']);

        return [
            'id'          => $name,
            'name'        => $name,
            'driver'      => $driver,
            'title'       => $data['title'] ?? 'unknown',
            'base_path'   => substr($basePath, 0, -9),
            'base_url'    => substr($baseUrl, 0, -9),
            'disk_status' => $status,
            'can_edit'    => $isEditable,
            'can_delete'  => !$isSystem && !$isDefault,
            'is_system'   => $isSystem,
            'is_default'  => $isDefault,
            'links'       => [
                'edit' => sprintf('/admincp/storage/config/edit/%s/%s', $driver, $id),
            ],
        ];
    }

    /**
     * Delete item.
     *
     * @param string $disk
     *
     * @return JsonResponse
     */
    public function destroy(string $disk): JsonResponse
    {
        $name = sprintf('storage.filesystems.disks.%s', $disk);

        Settings::destroy('storage', [$name]);
        Artisan::call('cache:reset');

        // try to destroy disk
        return $this->success([
            'id' => $disk,
        ]);
    }

    public function create(): JsonResponse
    {
        return $this->success(new SelectDiskDriverForm());
    }

    public function store(StoreRequest $request): JsonResponse
    {
        $params = $request->validated();
        $driver = $params['driver'];
        $id     = $params['id'];

        return $this->success([], [
            'nextAction' => [
                'type'    => 'navigate',
                'payload' => [
                    'url'     => sprintf('/admincp/storage/config/edit/%s/%s', $driver, $id),
                    'replace' => false,
                ],
            ],
        ]);
    }

    public function updateByDisk(string $disk)
    {
        $config = Settings::get(sprintf('storage.filesystems.disks.%s', $disk));

        $driver = $config['driver'] ?? 'unknown';

        $driverClass = resolve(DriverRepositoryInterface::class)
            ->getDriver('form-storage', $driver, 'admin');

        if (!$config) {
            $config = [];
        }

        return new $driverClass([
            'id'     => $disk,
            'driver' => $driver,
            'value'  => $config,
        ]);
    }

    public function edit(string $driver, string $disk)
    {
        $config = Settings::get(sprintf('storage.filesystems.disks.%s', $disk));

        if (!$driver) {
            $driver = $config['driver'] ?? 'unknown';
        }

        $driverClass = resolve(DriverRepositoryInterface::class)
            ->getDriver('form-storage', $driver, 'admin');

        if (!$config) {
            $config = [];
        }

        return new $driverClass([
            'id'     => $disk,
            'driver' => $driver,
            'value'  => $config,
        ]);
    }

    public function update(string $driver, string $disk, Request $request): JsonResponse
    {
        $config = Settings::get(sprintf('filesystems.disks.%s', $disk));

        $driverClass = resolve(DriverRepositoryInterface::class)
            ->getDriver('form-storage', $driver, 'admin');

        if (!$config) {
            $config = [];
        }

        $form = new $driverClass([
            'id'     => $disk,
            'driver' => $driver,
            'value'  => $config,
        ]);

        if (method_exists($form, 'validated')) {
            // forward to dependency injection
            $config = app()->call([$form, 'validated'], $request->route()->parameters());
        }

        $name       = sprintf('storage.filesystems.disks.%s', $disk);
        $configName = sprintf('filesystems.disks.%s', $disk);

        Settings::updateSetting('storage', $name, $configName, '', $config, 'array', false, true);
        Artisan::call('cache:reset');

        $nextAction = [
            'type'    => 'navigate',
            'payload' => [
                'url'     => '/admincp/storage/config/browse',
                'replace' => true,
            ],
        ];

        $message = __p('core::phrase.updated_successfully');

        return $this->success($this->transformDiskResource($disk, $config), ['nextAction' => $nextAction], $message);
    }
}
