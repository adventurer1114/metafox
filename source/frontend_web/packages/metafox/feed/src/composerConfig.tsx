const composerConfig = {
  editorPlugins: [
    { as: 'statusComposer.plugin.mention', testid: 'mention' },
    { as: 'statusComposer.plugin.hashtag', testid: 'hashtag' },
    {
      as: 'statusComposer.plugin.linkify',
      testid: 'linkify',
      showWhen: [
        'or',
        ['and', ['falsy', 'attachmentType'], ['truthy', 'isEdit']],
        ['falsy', 'isEdit']
      ]
    }
  ],
  editorControls: [
    {
      as: 'statusComposer.control.AttachBackgroundStatusButton',
      enabledWhen: [
        'and',
        [
          'or',
          ['and', ['falsy', 'attachmentType']],
          ['eq', 'attachmentType', 'backgroundStatus']
        ],
        ['lte', 'lengthText', 150]
      ],
      testid: 'attachBackgroundStatusButton'
    },
    {
      as: 'statusComposer.control.AttachEmojiButton',
      showWhen: ['eq', 'strategy', 'dialog'],
      testid: 'attachEmojiButton'
    }
  ],
  attachers: [
    {
      as: 'statusComposer.control.StatusTagsFriendButton',
      showWhen: [
        'and',
        ['eq', 'strategy', 'dialog'],
        ['truthy', 'setting.activity.feed.enable_tag_friends']
      ],
      testid: 'StatusTagsFriendButton'
    },
    {
      as: 'statusComposer.control.StatusUploadPhotoButton',
      showWhen: [
        'and',
        ['truthy', 'setting.feed.types.photo_set.can_create_feed'],
        [
          'or',
          ['truthy', 'acl.photo.photo.create'],
          ['truthy', 'acl.video.video.create']
        ]
      ],
      enabledWhen: [
        'or',
        ['and', ['falsy', 'attachmentType'], ['falsy', 'isEdit']],
        [
          'or',
          ['eq', 'data.attachmentType', 'photo'],
          ['eq', 'data.attachmentType', 'photo_set']
        ],
        [
          'or',
          ['eq', 'attachmentType', 'photo'],
          ['eq', 'attachmentType', 'photo_set']
        ]
      ],
      testid: 'StatusUploadPhotoButton'
    },
    {
      as: 'statusComposer.control.CheckInButton',
      showWhen: [
        'and',
        ['eq', 'strategy', 'dialog'],
        ['truthy', 'setting.activity.feed.enable_check_in']
      ],
      testid: 'checkinButton'
    },
    {
      as: 'statusComposer.StatusAddPollButton',
      enabledWhen: ['and', ['falsy', 'attachmentType'], ['falsy', 'isEdit']],
      showWhen: [
        'or',
        [
          'and',
          ['truthy', 'acl.poll.poll.create'],
          ['truthy', 'setting.feed.types.poll.can_create_feed'],
          ['eq', 'parentType', 'feed']
        ],
        [
          'and',
          ['truthy', 'item.profile_settings.poll_share_polls'],
          ['truthy', 'acl.poll.poll.create'],
          ['eq', 'parentType', 'group'],
          ['truthy', 'acl.poll.poll.create']
        ],
        [
          'and',
          ['truthy', 'item.profile_settings.poll_share_polls'],
          ['truthy', 'acl.poll.poll.create'],
          ['eq', 'parentType', 'page'],
          ['truthy', 'acl.poll.poll.create']
        ],
        [
          'and',
          ['truthy', 'acl.poll.poll.create'],
          ['truthy', 'setting.feed.types.poll.can_create_feed'],
          ['eq', 'parentType', 'user'],
          ['falsy', 'isUserProfileOther']
        ]
      ],
      testid: 'StatusAddPollButton'
    }
  ]
};
export default composerConfig;
