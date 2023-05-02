<?php

namespace MetaFox\ActivityPoint\Support;

use MetaFox\ActivityPoint\Repositories\PointSettingRepositoryInterface;
use MetaFox\App\Repositories\PackageRepositoryInterface;

class PointSetting implements \MetaFox\ActivityPoint\Contracts\Support\PointSetting
{
    private PackageRepositoryInterface $moduleRepository;

    private PointSettingRepositoryInterface $settingRepository;

    public function __construct(
        PackageRepositoryInterface $moduleRepository,
        PointSettingRepositoryInterface $settingRepository
    ) {
        $this->moduleRepository  = $moduleRepository;
        $this->settingRepository = $settingRepository;
    }
}
