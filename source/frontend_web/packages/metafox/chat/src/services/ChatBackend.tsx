/**
 * @type: service
 * name: chatBackend
 */
import { Manager } from '@metafox/framework';

class ChatBackend {
  /**
   *
   */
  private manager: Manager;

  public bootstrap(manager: Manager) {
    this.manager = manager;

    return this;
  }

  public listenRoomNotify(user_id: string | number) {
    const { echoBackend, dispatch } = this.manager;

    echoBackend.channel(`user.${user_id}`).listen('.UserMessage', (e: any) => {
      dispatch({ type: 'chat/addMessage', payload: e });
    });

    echoBackend
      .channel(`user.${user_id}`)
      .listen('.MessageUpdate', (e: any) => {
        dispatch({ type: 'chat/updateMessage', payload: e });
      });

    echoBackend.channel(`user.${user_id}`).listen('.RoomUpdated', (e: any) => {
      dispatch({ type: 'chat/updateRoom', payload: e });
    });
  }
}

export default ChatBackend;
