/**
 * @type: service
 * name: echoBackend
 */

import LaravelEcho from 'laravel-echo';
import 'pusher-js';
import { Manager } from '@metafox/framework/Manager';

class EchoBackend {
  public bootstrap(manager: Manager) {
    const KEY = manager?.setting?.broadcast?.connections?.pusher?.key;
    const CLUSTER =
      manager?.setting?.broadcast?.connections?.pusher?.options?.cluster ||
      'ap1';

    return new LaravelEcho({
      broadcaster: 'pusher',
      key: KEY,
      cluster: CLUSTER,
      forceTLS: false
    });
  }
}
export default EchoBackend;
