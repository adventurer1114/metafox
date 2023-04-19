import { AppResource } from '@metafox/framework';

const initialState: AppResource = {
  actions: {},
  menus: {
    itemActionMenu: {
      items: [
        {
          label: 'Reply',
          icon: 'ico-reply-o',
          value: 'closeMenu, chat/replyMessage',
          testid: 'replyMessage'
        },
        {
          label: 'Copy',
          icon: 'ico-copy-o',
          value: 'closeMenu, chat/copyMessage',
          testid: 'copyMessage',
          showWhen: ['truthy', 'item.message']
        },
        {
          label: 'Edit',
          icon: 'ico-pencilline-o',
          value: 'closeMenu, chat/editMessage',
          testid: 'editMessage',
          showWhen: ['and', ['truthy', 'allowEdit'], ['falsy', 'isSearch']]
        },

        {
          label: 'Delete',
          icon: 'ico-trash-o',
          className: 'item-delete',
          value: 'closeMenu, chat/deleteMessage',
          testid: 'deleteMessage',
          showWhen: ['and', ['truthy', 'canDelete'], ['falsy', 'isSearch']]
        }
      ]
    }
  }
};

export default initialState;
