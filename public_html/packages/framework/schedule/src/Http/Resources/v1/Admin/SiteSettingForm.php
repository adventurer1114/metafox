<?php

namespace MetaFox\Schedule\Http\Resources\v1\Admin;

use Illuminate\Support\Arr;
use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder;
use MetaFox\Platform\Facades\Settings;

/**
 * | --------------------------------------------------------------------------
 * | Form Configuration
 * | --------------------------------------------------------------------------
 * | stub: src/Http/Resources/v1/Admin/SiteSettingForm.stub.
 */

/**
 * Class SiteSettingForm.
 * @codeCoverageIgnore
 * @ignore
 * @driverType form-settings
 * @driverName schedule
 */
class SiteSettingForm extends AbstractForm
{
    protected function prepare(): void
    {
        /** @var string[] $vars */
        $vars = [];

        $value = [
            'command' => $this->getScheduleCommand(),
        ];

        foreach ($vars as $var) {
            Arr::set($value, $var, Settings::get($var));
        }

        $this->title(__p('schedule::phrase.schedule_settings'))
            ->action('admincp/setting/schedule')
            ->asPost()
            ->setValue($value);
    }

    /**
     * @return string|null
     */
    private function getPhpPath(): ?string
    {
        $pathToPhp = resolve(\Symfony\Component\Process\PhpExecutableFinder::class)->find();

        if ($pathToPhp && is_executable($pathToPhp)) {
            return $pathToPhp;
        }

        return null;
    }

    private function getScheduleCommand(): string
    {
        return sprintf(
            '* * * * *  %s %s/artisan schedule:run',
            $this->getPhpPath(),
            base_path()
        );
    }

    protected function initialize(): void
    {
        $this->addBasic()
            ->addFields(
                Builder::text('command')
                    ->description(__p('schedule::phrase.schedule_command_guide', ['command' => $this->getScheduleCommand()]))
            );

        $this->addDefaultFooter(true);
    }
}
