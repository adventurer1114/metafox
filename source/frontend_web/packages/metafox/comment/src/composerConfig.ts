const composerConfig = {
  editorPlugins: [
    { as: 'statusComposer.plugin.mention' },
    { as: 'statusComposer.plugin.hashtag' }
  ],
  editorControls: [
    {
      as: 'commentComposer.control.attachPhoto',
      showWhen: [
        'and',
        ['falsy', 'hasExtraContent'],
        ['truthy', 'setting.enable_photo']
      ]
    },
    {
      as: 'commentComposer.control.attachEmoji',
      showWhen: ['truthy', 'setting.enable_emoticon']
    },
    {
      as: 'commentComposer.control.attachSticker',
      showWhen: [
        'and',
        ['falsy', 'hasExtraContent'],
        ['truthy', 'setting.enable_sticker']
      ]
    }
  ]
};
export default composerConfig;
