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
          label: 'Toggle search',
          showWhen: ['and', ['truthy', 'isMobile']]
        },
        {
          label: 'Delete',
          icon: 'ico-trash-o',
          value: 'chat/room/deleteRoom',
          testid: 'deleteRoom'
        },
        {
          label: 'More',
          icon: 'ico-dottedmore-vertical-o',
          testid: 'more',
          behavior: 'more'
        }
      ]
    }
  }
};

export default state;
