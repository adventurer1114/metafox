<?php

/* this is auto generated file */
return [
    [
        'driver'     => 'MetaFox\\Music\\Http\\Resources\\v1\\Genre\\Admin\\DataGrid',
        'type'       => 'data-grid',
        'name'       => 'music.genre',
        'version'    => 'v1',
        'resolution' => 'admin',
    ],
    [
        'driver'     => 'MetaFox\\Music\\Http\\Resources\\v1\\Genre\\Admin\\DestroyGenreForm',
        'type'       => 'form',
        'name'       => 'music.music_genre.destroy',
        'version'    => 'v1',
        'resolution' => 'admin',
    ],
    [
        'driver'     => 'MetaFox\\Music\\Http\\Resources\\v1\\Genre\\Admin\\StoreGenreForm',
        'type'       => 'form',
        'name'       => 'music.music_genre.store',
        'version'    => 'v1',
        'resolution' => 'admin',
    ],
    [
        'driver'     => 'MetaFox\\Music\\Http\\Resources\\v1\\Genre\\Admin\\UpdateGenreForm',
        'type'       => 'form',
        'name'       => 'music.music_genre.update',
        'version'    => 'v1',
        'resolution' => 'admin',
    ],
    [
        'driver'     => 'MetaFox\\Music\\Http\\Resources\\v1\\Playlist\\AddSongForm',
        'type'       => 'form',
        'name'       => 'music.music_song.add_to_playlist',
        'version'    => 'v1',
        'resolution' => 'web',
    ],
    [
        'driver'     => 'MetaFox\\Music\\Http\\Resources\\v1\\Playlist\\AddSongMobileForm',
        'type'       => 'form',
        'name'       => 'music.music_song.add_to_playlist',
        'version'    => 'v1',
        'resolution' => 'mobile',
    ],
    [
        'driver'     => 'MetaFox\\Music\\Models\\Album',
        'type'       => 'entity',
        'name'       => 'music_album',
        'is_active'  => true,
        'is_preload' => false,
        'title'      => 'Music Albums',
    ],
    [
        'driver'     => 'MetaFox\\Music\\Models\\Playlist',
        'type'       => 'entity',
        'name'       => 'music_playlist',
        'is_active'  => true,
        'is_preload' => false,
        'title'      => 'Music Playlists',
    ],
    [
        'driver'     => 'MetaFox\\Music\\Models\\PlaylistData',
        'type'       => 'entity',
        'name'       => 'music_playlist_data',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Music\\Models\\GenreData',
        'type'       => 'entity',
        'name'       => 'music_genre_data',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Music\\Models\\Song',
        'type'       => 'entity',
        'name'       => 'music_song',
        'is_active'  => true,
        'is_preload' => false,
        'title'      => 'Music Songs',
    ],
    [
        'driver'     => 'MetaFox\\Music\\Models\\Album',
        'type'       => 'entity-content',
        'name'       => 'music_album',
        'is_active'  => true,
        'is_preload' => false,
        'title'      => 'Music Albums',
    ],
    [
        'driver'     => 'MetaFox\\Music\\Models\\Playlist',
        'type'       => 'entity-content',
        'name'       => 'music_playlist',
        'is_active'  => true,
        'is_preload' => false,
        'title'      => 'Music Playlists',
    ],
    [
        'driver'     => 'MetaFox\\Music\\Models\\Song',
        'type'       => 'entity-content',
        'name'       => 'music_song',
        'is_active'  => true,
        'is_preload' => false,
        'title'      => 'Music Songs',
    ],
    [
        'driver'     => 'MetaFox\\Music\\Policies\\SongPolicy',
        'type'       => 'policy-resource',
        'name'       => 'MetaFox\\Music\\Models\\Song',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Music\\Policies\\AlbumPolicy',
        'type'       => 'policy-resource',
        'name'       => 'MetaFox\\Music\\Models\\Album',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Music\\Policies\\PlaylistPolicy',
        'type'       => 'policy-resource',
        'name'       => 'MetaFox\\Music\\Models\\Playlist',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Music\\Models\\AlbumText',
        'type'       => 'entity',
        'name'       => 'music_album_text',
        'is_active'  => true,
        'is_preload' => false,
        'title'      => 'Music Albums',
    ],
    [
        'driver'     => 'MetaFox\\Music\\Http\\Resources\\v1\\Admin\\SiteSettingForm',
        'type'       => 'form-settings',
        'name'       => 'music',
        'version'    => 'v1',
        'resolution' => 'admin',
        'is_active'  => true,
        'is_preload' => false,
        'title'      => 'core::phrase.settings',
        'url'        => '/admincp/music/setting',
    ],
    [
        'driver'     => 'MetaFox\\Music\\Http\\Resources\\v1\\Music\\MobileSetting',
        'type'       => 'package-mobile',
        'name'       => 'music',
        'version'    => 'v1',
        'resolution' => 'mobile',
        'is_preload' => 1,
    ],
    [
        'driver'     => 'MetaFox\\Music\\Http\\Resources\\v1\\PackageSetting',
        'type'       => 'package-setting',
        'name'       => 'music',
        'version'    => 'v1',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Music\\Http\\Resources\\v1\\Music\\WebSetting',
        'type'       => 'resource-web',
        'name'       => 'music',
        'version'    => 'v1',
        'resolution' => 'web',
        'is_preload' => 1,
    ],
    [
        'driver'     => 'MetaFox\\Music\\Http\\Resources\\v1\\Song\\WebSetting',
        'type'       => 'resource-web',
        'name'       => 'music_song',
        'version'    => 'v1',
        'resolution' => 'web',
        'is_preload' => 1,
    ],
    [
        'driver'     => 'MetaFox\\Music\\Http\\Resources\\v1\\Song\\MobileSetting',
        'type'       => 'resource-mobile',
        'name'       => 'music_song',
        'version'    => 'v1',
        'resolution' => 'mobile',
    ],
    [
        'driver'     => 'MetaFox\\Music\\Http\\Resources\\v1\\Album\\WebSetting',
        'type'       => 'resource-web',
        'name'       => 'music_album',
        'version'    => 'v1',
        'resolution' => 'web',
        'is_preload' => 1,
    ],
    [
        'driver'     => 'MetaFox\\Music\\Http\\Resources\\v1\\Album\\MobileSetting',
        'type'       => 'resource-mobile',
        'name'       => 'music_album',
        'version'    => 'v1',
        'resolution' => 'mobile',
    ],

    [
        'driver'     => 'MetaFox\\Music\\Http\\Resources\\v1\\Playlist\\WebSetting',
        'type'       => 'resource-web',
        'name'       => 'music_playlist',
        'version'    => 'v1',
        'resolution' => 'web',
        'is_preload' => 1,
    ],
    [
        'driver'     => 'MetaFox\\Music\\Http\\Resources\\v1\\Playlist\\MobileSetting',
        'type'       => 'resource-mobile',
        'name'       => 'music_playlist',
        'version'    => 'v1',
        'resolution' => 'mobile',
    ],
    [
        'driver'     => 'MetaFox\\Music\\Http\\Resources\\v1\\Song\\StoreSongForm',
        'type'       => 'form',
        'name'       => 'music_song.store',
        'version'    => 'v1',
        'resolution' => 'web',
    ],
    [
        'driver'     => 'MetaFox\\Music\\Http\\Resources\\v1\\Song\\UpdateSongForm',
        'type'       => 'form',
        'name'       => 'music_song.update',
        'version'    => 'v1',
        'resolution' => 'web',
    ],
    [
        'driver'     => 'MetaFox\\Music\\Http\\Resources\\v1\\Song\\StoreSongMobileForm',
        'type'       => 'form',
        'name'       => 'music.music_song.store',
        'version'    => 'v1',
        'resolution' => 'mobile',
    ],
    [
        'driver'     => 'MetaFox\\Music\\Http\\Resources\\v1\\Song\\UpdateSongMobileForm',
        'type'       => 'form',
        'name'       => 'music.music_song.update',
        'version'    => 'v1',
        'resolution' => 'mobile',
    ],
    [
        'driver'     => 'MetaFox\\Music\\Http\\Resources\\v1\\Album\\StoreAlbumForm',
        'type'       => 'form',
        'name'       => 'music_album.store',
        'version'    => 'v1',
        'resolution' => 'web',
    ],
    [
        'driver'     => 'MetaFox\\Music\\Http\\Resources\\v1\\Album\\UpdateAlbumForm',
        'type'       => 'form',
        'name'       => 'music_album.update',
        'version'    => 'v1',
        'resolution' => 'web',
    ],
    [
        'driver'     => 'MetaFox\\Music\\Http\\Resources\\v1\\Album\\UpdateAlbumMobileForm',
        'type'       => 'form',
        'name'       => 'music.music_album.update',
        'version'    => 'v1',
        'resolution' => 'mobile',
    ],
    [
        'driver'     => 'MetaFox\\Music\\Http\\Resources\\v1\\Playlist\\StorePlaylistForm',
        'type'       => 'form',
        'name'       => 'music_playlist.store',
        'version'    => 'v1',
        'resolution' => 'web',
    ],
    [
        'driver'     => 'MetaFox\\Music\\Http\\Resources\\v1\\Playlist\\UpdatePlaylistForm',
        'type'       => 'form',
        'name'       => 'music_playlist.update',
        'version'    => 'v1',
        'resolution' => 'web',
    ],
    [
        'driver'     => 'MetaFox\\Music\\Http\\Resources\\v1\\Playlist\\StorePlaylistMobileForm',
        'type'       => 'form',
        'name'       => 'music.music_playlist.store',
        'version'    => 'v1',
        'resolution' => 'mobile',
    ],
    [
        'driver'     => 'MetaFox\\Music\\Http\\Resources\\v1\\Playlist\\UpdatePlaylistMobileForm',
        'type'       => 'form',
        'name'       => 'music.music_playlist.update',
        'version'    => 'v1',
        'resolution' => 'mobile',
    ],
    [
        'driver'     => 'MetaFox\\Music\\Http\\Resources\\v1\\Music\\SearchMusicMobileForm',
        'type'       => 'form',
        'name'       => 'music_song.search',
        'version'    => 'v1',
        'resolution' => 'mobile',
        'is_preload' => 1,
    ],
    [
        'driver'     => 'MetaFox\\Music\\Http\\Resources\\v1\\Music\\SearchMusicMobileForm',
        'type'       => 'form',
        'name'       => 'music_album.search',
        'version'    => 'v1',
        'resolution' => 'mobile',
        'is_preload' => 1,
    ],
    [
        'driver'     => 'MetaFox\\Music\\Http\\Resources\\v1\\Music\\SearchMusicMobileForm',
        'type'       => 'form',
        'name'       => 'music_playlist.search',
        'version'    => 'v1',
        'resolution' => 'mobile',
        'is_preload' => 1,
    ],
    [
        'driver'     => 'MetaFox\\Music\\Http\\Resources\\v1\\Song\\SongEmbedCollection',
        'type'       => 'json-collection',
        'name'       => 'music_song.embed',
        'version'    => 'v1',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Music\\Http\\Resources\\v1\\Song\\SongEmbed',
        'type'       => 'json-resource',
        'name'       => 'music_song.embed',
        'version'    => 'v1',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Music\\Http\\Resources\\v1\\Song\\SongItemCollection',
        'type'       => 'json-collection',
        'name'       => 'music_song.item',
        'version'    => 'v1',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Music\\Http\\Resources\\v1\\Song\\SongItem',
        'type'       => 'json-resource',
        'name'       => 'music_song.item',
        'version'    => 'v1',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Music\\Http\\Resources\\v1\\Song\\SongDetail',
        'type'       => 'json-resource',
        'name'       => 'music_song.detail',
        'version'    => 'v1',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Music\\Http\\Resources\\v1\\Album\\AlbumEmbedCollection',
        'type'       => 'json-collection',
        'name'       => 'music_album.embed',
        'version'    => 'v1',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Music\\Http\\Resources\\v1\\Album\\AlbumEmbed',
        'type'       => 'json-resource',
        'name'       => 'music_album.embed',
        'version'    => 'v1',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Music\\Http\\Resources\\v1\\Album\\AlbumItemCollection',
        'type'       => 'json-collection',
        'name'       => 'music_album.item',
        'version'    => 'v1',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Music\\Http\\Resources\\v1\\Album\\AlbumItem',
        'type'       => 'json-resource',
        'name'       => 'music_album.item',
        'version'    => 'v1',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Music\\Http\\Resources\\v1\\Album\\AlbumDetail',
        'type'       => 'json-resource',
        'name'       => 'music_album.detail',
        'version'    => 'v1',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Music\\Http\\Resources\\v1\\Playlist\\PlaylistEmbedCollection',
        'type'       => 'json-collection',
        'name'       => 'music_playlist.embed',
        'version'    => 'v1',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Music\\Http\\Resources\\v1\\Playlist\\PlaylistEmbed',
        'type'       => 'json-resource',
        'name'       => 'music_playlist.embed',
        'version'    => 'v1',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Music\\Http\\Resources\\v1\\Playlist\\PlaylistItemCollection',
        'type'       => 'json-collection',
        'name'       => 'music_playlist.item',
        'version'    => 'v1',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Music\\Http\\Resources\\v1\\Playlist\\PlaylistItem',
        'type'       => 'json-resource',
        'name'       => 'music_playlist.item',
        'version'    => 'v1',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Music\\Http\\Resources\\v1\\Playlist\\PlaylistDetail',
        'type'       => 'json-resource',
        'name'       => 'music_playlist.detail',
        'version'    => 'v1',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Music\\Http\\Resources\\v1\\Genre\\GenreItemCollection',
        'type'       => 'json-collection',
        'name'       => 'music_genre.item',
        'version'    => 'v1',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Music\\Http\\Resources\\v1\\Genre\\GenreItem',
        'type'       => 'json-resource',
        'name'       => 'music_genre.item',
        'version'    => 'v1',
        'is_active'  => true,
        'is_preload' => false,
    ],
];
