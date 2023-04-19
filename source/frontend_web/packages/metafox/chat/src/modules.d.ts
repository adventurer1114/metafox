import '@metafox/framework/Manager';
import ChatBackend from './services/ChatBackend';

declare module '@metafox/framework/Manager' {
  interface Manager {
    chatBackend?: ChatBackend;
  }

  interface ManagerConfig {
    chat?: Partial<ChatConfig>;
  }
}
