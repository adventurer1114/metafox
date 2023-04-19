import { AppResource } from '@metafox/framework';

const state: AppResource = {
  actions: {},
  menus: {
    itemActionMenu: {
      items: [
        {
          icon: 'ico-search-o',
          value: 'chat/room/toggleSearching',
          testid: 'toggleSearching',
          label: 'Toggle search'
        },
        {
          icon: 'ico-comment-square-dots-o',
          value: 'chat/room/openInMessenger',
          testid: 'openInMessenger',
          label: 'Open In Messenger'
        },

        {
          label: 'Delete',
          icon: 'ico-trash-o',
          value: 'closeMenu, chat/room/deleteRoom',
          testid: 'deleteRoom'
        },

        {
          label: 'More',
          icon: 'ico-dottedmore-vertical-o',
          testid: 'more',
          behavior: 'more'
        },
        {
          icon: 'ico-minus',
          value: 'closeMenu, chat/room/toggle',
          testid: 'minimize',
          label: 'Minimize',
          behavior: 'close'
        },
        {
          label: 'Close',
          icon: 'ico-close',
          value: 'closeMenu, chat/closePanel',
          testid: 'closePanel',
          behavior: 'close'
        }
      ]
    }
  }
};

export default state;
