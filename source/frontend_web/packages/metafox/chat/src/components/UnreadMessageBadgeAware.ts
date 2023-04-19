import { useGlobal } from '@metafox/framework';
import { useRooms } from '../hooks';
import { AppState } from '@metafox/chat/types';

type IRoom = AppState['entities']['room'];

export default function UnreadMessageBadgeAware() {
  const { dispatch } = useGlobal();
  const rooms: IRoom = useRooms();

  const dataRooms = (rooms && Object.values(rooms)) || [];

  let badgeTotalUnread: any = dataRooms
    .filter((item: any) => item.total_unseen && item?.total_unseen > 0)
    .reduce((total, item): any => {
      return item?.total_unseen ? ++total : total;
    }, 0);

  badgeTotalUnread =
    badgeTotalUnread > 99 ? '99+' : parseInt(badgeTotalUnread, 10);

  dispatch({
    type: 'core/status/fulfill',
    payload: { chat_message: badgeTotalUnread }
  });

  return null;
}
