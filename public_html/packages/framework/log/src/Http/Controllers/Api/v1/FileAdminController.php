<?php

namespace MetaFox\Log\Http\Controllers\Api\v1;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use MetaFox\Log\Support\FileLogReader;
use MetaFox\Platform\Http\Controllers\Api\ApiController;
use SplFileInfo;

/**
 * | --------------------------------------------------------------------------
 * |  Api Controller
 * | --------------------------------------------------------------------------
 * |
 * | stub: /packages/controllers/api_controller.stub
 * | Assign this class in $controllers of
 * | @link \MetaFox\Log\Http\Controllers\Api\FileAdminController::$controllers.
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
    public function index(): JsonResponse
    {
        $data = [];

        /** @var SplFileInfo[] $files */
        $files = app('files')->files(storage_path('logs'));

        usort($files, function (SplFileInfo $a, SplFileInfo $b) {
            return $a->getFilename() < $b->getFilename();
        });

        foreach ($files as $index => $file) {
            $name   = $file->getFilename();
            $key    = base64_encode($name);
            $data[] = [
                'id'          => $index + 1,
                'filename'    => $name,
                'modified_at' => Carbon::createFromTimestamp($file->getMTime())->toAtomString(),
                'filesize'    => $file->getSize(),
                'links'       => [
                    'pageUrl'     => sprintf('/admincp/log/file/%s/msg/browse', $key),
                    'downloadUrl' => sprintf('/admincp/log/file/download/:%s', $key),
                ],
            ];
        }

        return $this->success($data);
    }

    /**
     * View item.
     *
     * @return JsonResponse
     */
    public function show(Request $request): JsonResponse
    {
        $base = $request->get('file_id');
        if (!$base) {
            $base = $request->get('file');
        }
        $filename = base64_decode($base);

        $filename = storage_path('logs/' . $filename);

        $logs = (new FileLogReader())->get($filename);

        return $this->success($logs->getArrayCopy());
    }

    /**
     * Delete item.
     *
     * @param  string       $file
     * @return JsonResponse
     */
    public function destroy(string $file): JsonResponse
    {
        return $this->success([
            'id' => $file,
        ]);
    }
}
