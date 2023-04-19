const composerConfig = {
  editorControls: [
    { as: 'commentComposer.control.attachEmoji' },
    {
      as: 'commentComposer.control.attachFile',
      showWhen: ['and', ['falsy', 'editing'], ['falsy', 'previewFiles']]
    },
    {
      as: 'chatComposer.control.buttonSubmit'
    }
  ]
};
export default composerConfig;
