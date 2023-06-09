<?php

/* this is auto generated file */
return [
    [
        'showWhen' => [
            'and',
            ['falsy', 'item.is_saved'],
            ['falsy', 'item.is_draft'],
            ['truthy', 'item.extra.can_save_item'],
        ],
        'menu'     => 'blog.blog.detailActionMenu',
        'name'     => 'save',
        'label'    => 'blog::phrase.save',
        'ordering' => 11,
        'value'    => 'saveItemDetail',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.is_saved'],
            ['truthy', 'item.extra.can_save_item'],
        ],
        'menu'     => 'blog.blog.detailActionMenu',
        'name'     => 'un-save',
        'label'    => 'blog::phrase.remove_from_saved_list',
        'ordering' => 12,
        'value'    => 'undoSaveItemDetail',
    ],
    [
        'showWhen' => [
            'and',
            ['falsy', 'item.is_saved'],
            ['falsy', 'item.is_draft'],
            ['truthy', 'item.extra.can_save_item'],
        ],
        'menu'     => 'blog.blog.itemActionMenu',
        'name'     => 'save',
        'label'    => 'blog::phrase.save',
        'ordering' => 11,
        'value'    => 'saveItem',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.is_saved'],
            ['truthy', 'item.extra.can_save_item'],
        ],
        'menu'     => 'blog.blog.itemActionMenu',
        'name'     => 'un-save',
        'label'    => 'blog::phrase.remove_from_saved_list',
        'ordering' => 12,
        'value'    => 'undoSaveItem',
    ],
    [
        'showWhen' => [
            'and',
            ['falsy', 'item.is_saved'],
            ['truthy', 'item.extra.can_save_item'],
        ],
        'menu'     => 'event.event.itemActionMenu',
        'name'     => 'save',
        'label'    => 'event::phrase.save',
        'ordering' => 15,
        'value'    => 'saveItem',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.is_saved'],
            ['truthy', 'item.extra.can_save_item'],
        ],
        'menu'     => 'event.event.itemActionMenu',
        'name'     => 'un-save',
        'label'    => 'event::phrase.remove_from_saved_list',
        'ordering' => 15,
        'value'    => 'undoSaveItem',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_save_item'],
            ['falsy', 'item.is_saved'],
            ['eq', 'item.is_pending', 0],
        ],
        'menu'     => 'feed.feed.itemActionMenu',
        'name'     => 'save',
        'label'    => 'activity::phrase.save_post',
        'ordering' => 17,
        'value'    => 'saveItem',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.is_saved'],
            ['truthy', 'item.extra.can_save_item'],
        ],
        'menu'     => 'feed.feed.itemActionMenu',
        'name'     => 'un-save',
        'label'    => 'activity::phrase.remove_from_saved_list',
        'ordering' => 18,
        'value'    => 'undoSaveItem',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_save_item'],
            ['falsy', 'item.is_saved'],
        ],
        'menu'     => 'forum.forum_post.detailActionMenu',
        'name'     => 'save',
        'label'    => 'core::phrase.save',
        'ordering' => 4,
        'value'    => 'saveItemDetail',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_save_item'],
            ['truthy', 'item.is_saved'],
        ],
        'menu'     => 'forum.forum_post.detailActionMenu',
        'name'     => 'unsave',
        'label'    => 'forum::phrase.forum_phrase_item_action_remove_from_saved_list',
        'ordering' => 5,
        'value'    => 'undoSaveItemDetail',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_save_item'],
            ['falsy', 'item.is_saved'],
        ],
        'menu'     => 'forum.forum_post.itemActionMenu',
        'name'     => 'save',
        'label'    => 'core::phrase.save',
        'ordering' => 4,
        'value'    => 'saveItem',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_save_item'],
            ['truthy', 'item.is_saved'],
        ],
        'menu'     => 'forum.forum_post.itemActionMenu',
        'name'     => 'unsave',
        'label'    => 'forum::phrase.forum_phrase_item_action_remove_from_saved_list',
        'ordering' => 5,
        'value'    => 'undoSaveItem',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_save_item'],
            ['falsy', 'item.is_saved'],
        ],
        'menu'     => 'forum.forum_thread.detailActionMenu',
        'name'     => 'save',
        'label'    => 'core::phrase.save',
        'ordering' => 19,
        'value'    => 'saveItemDetail',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_save_item'],
            ['truthy', 'item.is_saved'],
        ],
        'menu'     => 'forum.forum_thread.detailActionMenu',
        'name'     => 'unsave',
        'label'    => 'forum::phrase.forum_phrase_item_action_remove_from_saved_list',
        'ordering' => 20,
        'value'    => 'undoSaveItemDetail',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_save_item'],
            ['falsy', 'item.is_saved'],
        ],
        'menu'     => 'forum.forum_thread.itemActionMenu',
        'name'     => 'save',
        'label'    => 'core::phrase.save',
        'ordering' => 19,
        'value'    => 'saveItem',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_save_item'],
            ['truthy', 'item.is_saved'],
        ],
        'menu'     => 'forum.forum_thread.itemActionMenu',
        'name'     => 'unsave',
        'label'    => 'forum::phrase.forum_phrase_item_action_remove_from_saved_list',
        'ordering' => 20,
        'value'    => 'undoSaveItem',
    ],
    [
        'showWhen' => [
            'and',
            ['falsy', 'item.is_saved'],
            ['falsy', 'item.is_pending'],
        ],
        'params' => [
            'module_name'   => 'group',
            'resource_name' => 'group',
        ],
        'menu'     => 'group.group.itemActionMenu',
        'name'     => 'save',
        'label'    => 'group::phrase.save',
        'ordering' => 14,
        'value'    => 'saveItem',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.is_saved'],
        ],
        'params' => [
            'module_name'   => 'group',
            'resource_name' => 'group',
        ],
        'menu'     => 'group.group.itemActionMenu',
        'name'     => 'un-save',
        'label'    => 'group::phrase.remove_from_saved_list',
        'ordering' => 15,
        'value'    => 'undoSaveItem',
    ],
    [
        'showWhen' => [
            'and',
            ['falsy', 'item.is_saved'],
            ['falsy', 'item.is_pending'],
            ['truthy', 'item.extra.can_save_item'],
        ],
        'menu'     => 'marketplace.marketplace.detailActionMenu',
        'name'     => 'save',
        'label'    => 'marketplace::phrase.save',
        'ordering' => 10,
        'value'    => 'saveItem',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.is_saved'],
            ['truthy', 'item.extra.can_save_item'],
        ],
        'menu'     => 'marketplace.marketplace.detailActionMenu',
        'name'     => 'un-save',
        'label'    => 'marketplace::phrase.remove_from_saved_list',
        'ordering' => 11,
        'value'    => 'undoSaveItem',
    ],
    [
        'showWhen' => [
            'and',
            ['falsy', 'item.is_saved'],
            ['falsy', 'item.is_pending'],
            ['truthy', 'item.extra.can_save_item'],
        ],
        'menu'     => 'marketplace.marketplace.itemActionMenu',
        'name'     => 'save',
        'label'    => 'marketplace::phrase.save',
        'ordering' => 13,
        'value'    => 'saveItem',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.is_saved'],
            ['truthy', 'item.extra.can_save_item'],
        ],
        'menu'     => 'marketplace.marketplace.itemActionMenu',
        'name'     => 'un-save',
        'label'    => 'marketplace::phrase.remove_from_saved_list',
        'ordering' => 14,
        'value'    => 'undoSaveItem',
    ],
    [
        'showWhen' => [
            'and',
            ['eq', 'item.is_saved', 0],
        ],
        'menu'     => 'music.music_album.itemActionMenu',
        'name'     => 'save',
        'label'    => 'music::phrase.save',
        'ordering' => 14,
        'value'    => 'saveItem',
    ],
    [
        'showWhen' => [
            'and',
            ['eq', 'item.is_saved', 1],
        ],
        'menu'     => 'music.music_album.itemActionMenu',
        'name'     => 'un-save',
        'label'    => 'music::phrase.remove_from_saved_list',
        'ordering' => 15,
        'value'    => 'undoSaveItem',
    ],
    [
        'showWhen' => [
            'and',
            ['eq', 'item.is_saved', 0],
        ],
        'menu'     => 'music.music_album.detailActionMenu',
        'name'     => 'save',
        'label'    => 'music::phrase.save',
        'ordering' => 14,
        'value'    => 'saveItemDetail',
    ],
    [
        'showWhen' => [
            'and',
            ['eq', 'item.is_saved', 1],
        ],
        'menu'     => 'music.music_album.detailActionMenu',
        'name'     => 'un-save',
        'label'    => 'music::phrase.remove_from_saved_list',
        'ordering' => 15,
        'value'    => 'undoSaveItemDetail',
    ],
    [
        'showWhen' => [
            'and',
            ['eq', 'item.is_saved', 0],
        ],
        'menu'     => 'music.music_playlist.itemActionMenu',
        'name'     => 'save',
        'label'    => 'music::phrase.save',
        'ordering' => 14,
        'value'    => 'saveItem',
    ],
    [
        'showWhen' => [
            'and',
            ['eq', 'item.is_saved', 1],
        ],
        'menu'     => 'music.music_playlist.itemActionMenu',
        'name'     => 'un-save',
        'label'    => 'music::phrase.remove_from_saved_list',
        'ordering' => 15,
        'value'    => 'undoSaveItem',
    ],
    [
        'showWhen' => [
            'and',
            ['eq', 'item.is_saved', 0],
        ],
        'menu'     => 'music.music_playlist.detailActionMenu',
        'name'     => 'save',
        'label'    => 'music::phrase.save',
        'ordering' => 14,
        'value'    => 'saveItemDetail',
    ],
    [
        'showWhen' => [
            'and',
            ['eq', 'item.is_saved', 1],
        ],
        'menu'     => 'music.music_playlist.detailActionMenu',
        'name'     => 'un-save',
        'label'    => 'music::phrase.remove_from_saved_list',
        'ordering' => 15,
        'value'    => 'undoSaveItemDetail',
    ],
    [
        'showWhen' => [
            'and',
            ['eq', 'item.is_saved', 0],
        ],
        'menu'     => 'music.music_song.itemActionMenu',
        'name'     => 'save',
        'label'    => 'music::phrase.save',
        'ordering' => 14,
        'value'    => 'saveItem',
    ],
    [
        'showWhen' => [
            'and',
            ['eq', 'item.is_saved', 1],
        ],
        'menu'     => 'music.music_song.itemActionMenu',
        'name'     => 'un-save',
        'label'    => 'music::phrase.remove_from_saved_list',
        'ordering' => 15,
        'value'    => 'undoSaveItem',
    ],
    [
        'showWhen' => [
            'and',
            ['eq', 'item.is_saved', 0],
        ],
        'menu'     => 'music.music_song.detailActionMenu',
        'name'     => 'save',
        'label'    => 'music::phrase.save',
        'ordering' => 14,
        'value'    => 'saveItemDetail',
    ],
    [
        'showWhen' => [
            'and',
            ['eq', 'item.is_saved', 1],
        ],
        'menu'     => 'music.music_song.detailActionMenu',
        'name'     => 'un-save',
        'label'    => 'music::phrase.remove_from_saved_list',
        'ordering' => 15,
        'value'    => 'undoSaveItemDetail',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_save_item'],
            ['falsy', 'item.is_saved'],
        ],
        'menu'     => 'photo.photo.itemActionMenu',
        'name'     => 'save',
        'label'    => 'photo::phrase.save',
        'ordering' => 5,
        'value'    => 'saveItem',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.is_saved'],
        ],
        'menu'     => 'photo.photo.itemActionMenu',
        'name'     => 'un-save',
        'label'    => 'photo::phrase.remove_from_saved_list',
        'ordering' => 6,
        'value'    => 'undoSaveItem',
    ],
    [
        'showWhen' => [
            'and',
            ['eq', 'item.is_saved', 0],
            ['truthy', 'item.extra.can_save_item'],
        ],
        'menu'     => 'photo.photo_album.itemActionMenu',
        'name'     => 'save',
        'label'    => 'photo::phrase.save',
        'ordering' => 9,
        'value'    => 'saveItem',
    ],
    [
        'showWhen' => [
            'and',
            ['eq', 'item.is_saved', 1],
            ['truthy', 'item.extra.can_save_item'],
        ],
        'menu'     => 'photo.photo_album.itemActionMenu',
        'name'     => 'un-save',
        'label'    => 'photo::phrase.remove_from_saved_list',
        'ordering' => 10,
        'value'    => 'undoSaveItem',
    ],
    [
        'showWhen' => [
            'and',
            ['eq', 'item.is_saved', 0],
            ['truthy', 'item.extra.can_save_item'],
        ],
        'menu'     => 'poll.poll.itemActionMenu',
        'name'     => 'save',
        'label'    => 'poll::phrase.save',
        'ordering' => 9,
        'value'    => 'saveItem',
    ],
    [
        'showWhen' => [
            'and',
            ['eq', 'item.is_saved', 1],
            ['truthy', 'item.extra.can_save_item'],
        ],
        'menu'     => 'poll.poll.itemActionMenu',
        'name'     => 'un-save',
        'label'    => 'poll::phrase.remove_from_saved_list',
        'ordering' => 10,
        'value'    => 'undoSaveItem',
    ],
    [
        'showWhen' => [
            'and',
            ['eq', 'item.is_saved', 0],
            ['falsy', 'item.is_pending'],
            ['truthy', 'item.extra.can_save_item'],
        ],
        'menu'     => 'quiz.quiz.itemActionMenu',
        'name'     => 'save',
        'label'    => 'quiz::phrase.save',
        'ordering' => 9,
        'value'    => 'saveItemDetail',
    ],
    [
        'showWhen' => [
            'and',
            ['eq', 'item.is_saved', 1],
            ['falsy', 'item.is_pending'],
            ['truthy', 'item.extra.can_save_item'],
        ],
        'menu'     => 'quiz.quiz.itemActionMenu',
        'name'     => 'un-save',
        'label'    => 'quiz::phrase.remove_from_saved_list',
        'ordering' => 10,
        'value'    => 'undoSaveItemDetail',
    ],
    [
        'showWhen' => [],
        'menu'     => 'saved.saved.itemActionMenu',
        'name'     => 'unsave',
        'label'    => 'saved::phrase.remove_from_saved_list',
        'ordering' => 2,
        'value'    => 'deleteItem',
    ],
    [
        'showWhen' => [
            'and',
            ['falsy', 'item.is_opened'],
        ],
        'menu'     => 'saved.saved.itemActionMenu',
        'name'     => 'mark_as_opened',
        'label'    => 'saved::phrase.mark_as_opened',
        'ordering' => 3,
        'value'    => 'saved/markAsOpened',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.is_opened'],
        ],
        'menu'     => 'saved.saved.itemActionMenu',
        'name'     => 'mark_as_un_opened',
        'label'    => 'saved::phrase.mark_as_unopened',
        'ordering' => 4,
        'value'    => 'saved/markAsUnOpened',
    ],
    [
        'menu'     => 'saved.saved.itemActionMenu',
        'name'     => 'add_to_collection',
        'label'    => 'saved::phrase.add_to_collection',
        'ordering' => 5,
        'value'    => 'saved/addToCollection',
    ],
    [

        'showWhen' => [
            'and',
            ['truthy', 'acl.saved.saved_list.update'],
            ['truthy', 'item.extra.is_owner'],
        ],
        'menu'     => 'saved.saved_list.itemActionMenu',
        'name'     => 'edit',
        'label'    => 'saved::phrase.edit',
        'ordering' => 1,
        'value'    => 'editItem',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.is_owner'],
        ],
        'menu'     => 'saved.saved_list.itemActionMenu',
        'name'     => 'add_friend',
        'label'    => 'saved::phrase.add_friends',
        'ordering' => 2,
        'value'    => 'saved_list/addFriend',
    ],
    [
        'menu'     => 'saved.saved_list.itemActionMenu',
        'name'     => 'view_friend',
        'label'    => 'saved::phrase.view_members_list',
        'ordering' => 3,
        'value'    => 'saved_list/viewFriend',
    ],
    [
        'showWhen' => [
            'and',
            ['falsy', 'item.extra.is_owner'],
        ],
        'menu'     => 'saved.saved_list.itemActionMenu',
        'name'     => 'leave_collection',
        'label'    => 'saved::phrase.leave_collection',
        'ordering' => 4,
        'value'    => 'saved_list/leaveCollection',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'acl.saved.saved_list.delete'],
            ['truthy', 'item.extra.is_owner'],
        ],
        'style'    => 'danger',
        'menu'     => 'saved.saved_list.itemActionMenu',
        'name'     => 'delete',
        'label'    => 'saved::phrase.delete',
        'ordering' => 10,
        'value'    => 'deleteItem',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'acl.saved.saved_list.update'],
            ['truthy', 'item.extra.is_owner'],
        ],
        'menu'     => 'saved.saved_list.detailActionMenu',
        'name'     => 'edit',
        'label'    => 'saved::phrase.edit',
        'ordering' => 1,
        'value'    => 'editItem',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.is_owner'],
        ],
        'menu'     => 'saved.saved_list.detailActionMenu',
        'name'     => 'add_friend',
        'label'    => 'saved::phrase.add_friends',
        'ordering' => 2,
        'value'    => 'saved_list/addFriend',
    ],
    [
        'menu'     => 'saved.saved_list.detailActionMenu',
        'name'     => 'view_friend',
        'label'    => 'saved::phrase.view_friends',
        'ordering' => 3,
        'value'    => 'saved_list/viewFriend',
    ],
    [
        'showWhen' => [
            'and',
            ['falsy', 'item.extra.is_owner'],
        ],
        'menu'     => 'saved.saved_list.detailActionMenu',
        'name'     => 'leave_collection',
        'label'    => 'saved::phrase.leave_collection',
        'ordering' => 4,
        'value'    => 'saved_list/leaveCollection',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'acl.saved.saved_list.delete'],
            ['truthy', 'item.extra.is_owner'],
        ],
        'style'    => 'danger',
        'menu'     => 'saved.saved_list.detailActionMenu',
        'name'     => 'delete',
        'label'    => 'saved::phrase.delete',
        'ordering' => 10,
        'value'    => 'deleteItem',
    ],
    [
        'showWhen' => [
            'and',
            ['eq', 'item.is_saved', 0],
            ['falsy', 'item.is_processing'],
            ['truthy', 'item.extra.can_save_item'],
        ],
        'menu'     => 'video.video.itemActionMenu',
        'name'     => 'save',
        'label'    => 'video::phrase.save',
        'ordering' => 9,
        'value'    => 'saveItem',
    ],
    [
        'showWhen' => [
            'and',
            ['eq', 'item.is_saved', 1],
            ['truthy', 'item.extra.can_save_item'],
        ],
        'menu'     => 'video.video.itemActionMenu',
        'name'     => 'un-save',
        'label'    => 'video::phrase.remove_from_saved_list',
        'ordering' => 10,
        'value'    => 'undoSaveItem',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_save_item'],
            ['falsy', 'item.is_saved'],
        ],
        'menu'     => 'feed.feed.itemActionMenuForProfile',
        'name'     => 'save',
        'label'    => 'activity::phrase.save_post',
        'ordering' => 5,
        'value'    => 'saveItem',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.is_saved'],
        ],
        'menu'     => 'feed.feed.itemActionMenuForProfile',
        'name'     => 'un-save',
        'label'    => 'activity::phrase.remove_from_saved_list',
        'ordering' => 6,
        'value'    => 'undoSaveItem',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_save_item'],
            ['falsy', 'item.is_saved'],
            ['falsy', 'item.is_pending'],
        ],
        'menu'     => 'page.page.profileActionMenu',
        'name'     => 'save',
        'label'    => 'page::phrase.save',
        'ordering' => 12,
        'value'    => 'saveItem',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_save_item'],
            ['truthy', 'item.is_saved'],
        ],
        'menu'     => 'page.page.profileActionMenu',
        'name'     => 'un-save',
        'label'    => 'page::phrase.remove_from_saved_list',
        'ordering' => 13,
        'value'    => 'undoSaveItem',
    ],
    [
        'showWhen' => [
            'and',
            ['falsy', 'item.is_saved'],
            ['falsy', 'item.is_pending'],
            ['truthy', 'item.extra.can_save_item'],
        ],
        'menu'     => 'event.event.detailActionMenu',
        'name'     => 'save',
        'label'    => 'event::phrase.save',
        'ordering' => 15,
        'value'    => 'saveItem',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.is_saved'],
            ['falsy', 'item.is_pending'],
        ],
        'menu'     => 'event.event.detailActionMenu',
        'name'     => 'un-save',
        'label'    => 'event::phrase.remove_from_saved_list',
        'ordering' => 15,
        'value'    => 'undoSaveItem',
    ],
    [
        'showWhen' => [
            'and',
            ['eq', 'item.is_saved', 0],
            ['falsy', 'item.is_processing'],
            ['truthy', 'item.extra.can_save_item'],
        ],
        'menu'     => 'video.video.detailActionMenu',
        'name'     => 'save',
        'label'    => 'video::phrase.save',
        'ordering' => 9,
        'value'    => 'saveItem',
    ],
    [
        'showWhen' => [
            'and',
            ['eq', 'item.is_saved', 1],
            ['truthy', 'item.extra.can_save_item'],
        ],
        'menu'     => 'video.video.detailActionMenu',
        'name'     => 'un-save',
        'label'    => 'video::phrase.remove_from_saved_list',
        'ordering' => 10,
        'value'    => 'undoSaveItem',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_save_item'],
            ['falsy', 'item.is_saved'],
        ],
        'menu'     => 'photo.photo.detailActionMenu',
        'name'     => 'save',
        'label'    => 'photo::phrase.save',
        'ordering' => 5,
        'value'    => 'saveItem',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.is_saved'],
        ],
        'menu'     => 'photo.photo.detailActionMenu',
        'name'     => 'un-save',
        'label'    => 'photo::phrase.remove_from_saved_list',
        'ordering' => 6,
        'value'    => 'undoSaveItem',
    ],
    [
        'showWhen' => [
            'and',
            ['eq', 'item.is_saved', 0],
            ['truthy', 'item.extra.can_save_item'],
        ],
        'menu'     => 'photo.photo_album.detailActionMenu',
        'name'     => 'save',
        'label'    => 'photo::phrase.save',
        'ordering' => 9,
        'value'    => 'saveItem',
    ],
    [
        'showWhen' => [
            'and',
            ['eq', 'item.is_saved', 1],
            ['truthy', 'item.extra.can_save_item'],
        ],
        'menu'     => 'photo.photo_album.detailActionMenu',
        'name'     => 'un-save',
        'label'    => 'photo::phrase.remove_from_saved_list',
        'ordering' => 10,
        'value'    => 'undoSaveItem',
    ],
    [
        'showWhen' => [
            'and',
            ['eq', 'item.is_saved', 0],
            ['truthy', 'item.extra.can_save_item'],
        ],
        'menu'     => 'poll.poll.detailActionMenu',
        'name'     => 'save',
        'label'    => 'poll::phrase.save',
        'ordering' => 9,
        'value'    => 'saveItem',
    ],
    [
        'showWhen' => [
            'and',
            ['eq', 'item.is_saved', 1],
            ['truthy', 'item.extra.can_save_item'],
        ],
        'menu'     => 'poll.poll.detailActionMenu',
        'name'     => 'un-save',
        'label'    => 'poll::phrase.remove_from_saved_list',
        'ordering' => 10,
        'value'    => 'undoSaveItem',
    ],
    [
        'showWhen' => [
            'and',
            ['eq', 'item.is_saved', 0],
            ['falsy', 'item.is_pending'],
            ['truthy', 'item.extra.can_save_item'],
        ],
        'menu'     => 'quiz.quiz.detailActionMenu',
        'name'     => 'save',
        'label'    => 'quiz::phrase.save',
        'ordering' => 9,
        'value'    => 'saveItemDetail',
    ],
    [
        'showWhen' => [
            'and',
            ['eq', 'item.is_saved', 1],
            ['falsy', 'item.is_pending'],
            ['truthy', 'item.extra.can_save_item'],
        ],
        'menu'     => 'quiz.quiz.detailActionMenu',
        'name'     => 'un-save',
        'label'    => 'quiz::phrase.remove_from_saved_list',
        'ordering' => 10,
        'value'    => 'undoSaveItemDetail',
    ],
    [
        'showWhen' => [
            'and',
            ['falsy', 'item.is_saved'],
        ],
        'params' => [
            'module_name'   => 'group',
            'resource_name' => 'group',
        ],
        'menu'     => 'group.group.detailActionMenu',
        'name'     => 'save',
        'label'    => 'group::phrase.save',
        'ordering' => 14,
        'value'    => 'saveItem',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.is_saved'],
        ],
        'params' => [
            'module_name'   => 'group',
            'resource_name' => 'group',
        ],
        'menu'     => 'group.group.detailActionMenu',
        'name'     => 'un-save',
        'label'    => 'group::phrase.remove_from_saved_list',
        'ordering' => 15,
        'value'    => 'undoSaveItem',
    ],
    [
        'menu'   => 'saved.sidebarMenu',
        'name'   => 'all',
        'params' => [
            'module_name'   => 'saved',
            'resource_name' => 'saved_list',
        ],
        'asModal'  => true,
        'label'    => 'saved::phrase.all_saved_items',
        'ordering' => 1,
        'value'    => 'viewAll',
        'icon'     => '',
        'to'       => '/saved/all',
        'tab'      => 'all',
    ],
    [
        'showWhen' => [],
        'menu'     => 'saved.sidebarMenu',
        'name'     => 'my',
        'asModal'  => true,
        'params'   => [
            'module_name'   => 'saved',
            'resource_name' => 'saved_list',
        ],
        'label'    => 'saved::phrase.my_collections',
        'ordering' => 2,
        'value'    => 'viewMyCollections',
        'icon'     => '',
        'to'       => '/saved/my',
        'tab'      => 'my',
    ],
    [
        'showWhen'  => [],
        'menu'      => 'core.bodyMenu',
        'name'      => 'saved_items',
        'label'     => 'saved::phrase.saved_items',
        'ordering'  => 16,
        'value'     => '',
        'to'        => '/saved',
        'as'        => 'item',
        'icon'      => 'bookmark-o',
        'iconColor' => '#ff564a',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_delete'],
        ],
        'menu'     => 'saved.saved.itemActionWithinListMenu',
        'name'     => 'unsave',
        'label'    => 'saved::phrase.remove_from_saved_list',
        'ordering' => 2,
        'value'    => 'saved/deleteItem',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_remove'],
        ],
        'menu'     => 'saved.saved.itemActionWithinListMenu',
        'name'     => 'remove_from_collection',
        'label'    => 'saved::phrase.remove_from_collection',
        'ordering' => 3,
        'value'    => 'saved/removeCollectionItem',
    ],
    [
        'showWhen' => [
            'and',
            ['falsy', 'item.is_opened'],
        ],
        'menu'     => 'saved.saved.itemActionWithinListMenu',
        'name'     => 'mark_as_opened',
        'label'    => 'saved::phrase.mark_as_opened',
        'ordering' => 4,
        'value'    => 'saved/markAsOpened',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.is_opened'],
        ],
        'menu'     => 'saved.saved.itemActionWithinListMenu',
        'name'     => 'mark_as_un_opened',
        'label'    => 'saved::phrase.mark_as_unopened',
        'ordering' => 5,
        'value'    => 'saved/markAsUnOpened',
    ],
    [
        'showWhen' => [
            'and',
            ['falsy', 'item.is_saved'],
            ['truthy', 'item.extra.can_save_item'],
        ],
        'menu'     => 'livestreaming.live_video.detailActionMenu',
        'name'     => 'save',
        'label'    => 'livestreaming::phrase.save',
        'ordering' => 12,
        'value'    => 'saveItemDetail',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.is_saved'],
            ['truthy', 'item.extra.can_save_item'],
        ],
        'menu'     => 'livestreaming.live_video.detailActionMenu',
        'name'     => 'un-save',
        'label'    => 'livestreaming::phrase.remove_from_saved_list',
        'ordering' => 13,
        'value'    => 'undoSaveItemDetail',
    ],
    [
        'showWhen' => [
            'and',
            ['falsy', 'item.is_saved'],
            ['truthy', 'item.extra.can_save_item'],
        ],
        'menu'     => 'livestreaming.live_video.itemActionMenu',
        'name'     => 'save',
        'label'    => 'livestreaming::phrase.save',
        'ordering' => 12,
        'value'    => 'saveItem',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.is_saved'],
            ['truthy', 'item.extra.can_save_item'],
        ],
        'menu'     => 'livestreaming.live_video.itemActionMenu',
        'name'     => 'un-save',
        'label'    => 'livestreaming::phrase.remove_from_saved_list',
        'ordering' => 13,
        'value'    => 'undoSaveItem',
    ],
];
