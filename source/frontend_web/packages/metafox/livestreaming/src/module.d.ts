import '@metafox/framework/Manager';
import { AppState } from './types';

declare module '@metafox/framework/Manager' {
  interface Manager {
    // add more services
    CommentItemViewLiveStreaming?: React.FC<{
      identity: string;
      itemLive?: Record<string, any>;
      setParentReply?: () => void;
    }>;
  }

  interface GlobalState {
    livestreaming?: AppState;
  }
}
