<?php

/* this is auto generated file */
return [
    [
        'driver'     => 'MetaFox\\Storage\\Http\\Resources\\v1\\Disk\\Admin\\DataGrid',
        'type'       => 'data-grid',
        'name'       => 'storage',
        'version'    => 'v1',
        'resolution' => 'admin',
    ],
    [
        'driver'     => 'MetaFox\\Storage\\Http\\Resources\\v1\\Asset\\Admin\\DataGrid',
        'type'       => 'data-grid',
        'name'       => 'storage.asset',
        'version'    => 'v1',
        'resolution' => 'admin',
    ],
    [
        'driver'     => 'MetaFox\\Storage\\Http\\Resources\\v1\\Config\\Admin\\DataGrid',
        'type'       => 'data-grid',
        'name'       => 'storage.config',
        'version'    => 'v1',
        'resolution' => 'admin',
        'title'      => 'Manage Configuration',
        'url'        => '/admincp/storage/config/browse',
    ],
    [
        'driver'     => 'MetaFox\\Storage\\Http\\Resources\\v1\\Disk\\Admin\\DataGrid',
        'type'       => 'data-grid',
        'name'       => 'storage.disk',
        'version'    => 'v1',
        'resolution' => 'admin',
        'title'      => 'Manage Disks',
        'url'        => '/admincp/storage/config/browse',
    ],
    [
        'driver'     => 'MetaFox\\Storage\\Http\\Resources\\v1\\Disk\\Admin\\DataGrid',
        'type'       => 'data-grid',
        'name'       => 'storage.disk.mapping',
        'version'    => 'v1',
        'resolution' => 'admin',
    ],
    [
        'driver'     => 'MetaFox\\Storage\\Http\\Resources\\v1\\Admin\\SelectDiskDriverForm',
        'type'       => 'form',
        'name'       => 'storage.disk.store',
        'version'    => 'v1',
        'resolution' => 'admin',
        'title'      => 'Select disk driver',
    ],
    [
        'driver'     => 'MetaFox\\Storage\\Support\\SelectStorageIdField',
        'type'       => 'form-field',
        'name'       => 'selectStorageId',
        'version'    => 'v1',
        'resolution' => 'web',
    ],
    [
        'driver'     => 'MetaFox\\Storage\\Http\\Resources\\v1\\Admin\\SiteSettingForm',
        'type'       => 'form-settings',
        'name'       => 'storage',
        'version'    => 'v1',
        'resolution' => 'admin',
        'title'      => 'core::phrase.settings',
        'url'        => '/admincp/storage/setting',
    ],
    [
        'driver'     => 'MetaFox\\Storage\\Http\\Resources\\v1\\Admin\\UpdateFtpDiskForm',
        'type'       => 'form-storage',
        'name'       => 'ftp',
        'version'    => 'v1',
        'resolution' => 'admin',
        'title'      => 'FTP Storage',
        'is_active'  => 0,
    ],
    [
        'driver'     => 'MetaFox\\Storage\\Http\\Resources\\v1\\Admin\\UpdateLocalDiskForm',
        'type'       => 'form-storage',
        'name'       => 'local',
        'version'    => 'v1',
        'resolution' => 'admin',
        'title'      => 'Local Storage',
    ],
    [
        'driver'     => 'MetaFox\\Storage\\Http\\Resources\\v1\\Admin\\UpdateS3DiskForm',
        'type'       => 'form-storage',
        'name'       => 's3',
        'version'    => 'v1',
        'resolution' => 'admin',
        'title'      => 'S3, DigitalOcean Space or S3 compatible storage system',
    ],
    [
        'driver'     => 'MetaFox\\Storage\\Http\\Resources\\v1\\Admin\\UpdateSftpDiskForm',
        'type'       => 'form-storage',
        'name'       => 'sftp',
        'version'    => 'v1',
        'resolution' => 'admin',
        'title'      => 'SFTP Storage',
        'is_active'  => 0,
    ],
    [
        'driver'  => 'MetaFox\\Storage\\Http\\Resources\\v1\\PackageSetting',
        'type'    => 'package-setting',
        'name'    => 'storage',
        'version' => 'v1',
    ],
];
