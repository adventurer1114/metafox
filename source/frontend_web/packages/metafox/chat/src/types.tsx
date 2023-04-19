import { HandleAction } from '@metafox/framework';
import { UserItemShape } from '@metafox/user';

export type ReactMode = 'no_react' | 'edit' | 'reply';

export interface ChatDate {
  $date: number;
}
export interface MsgItemShape {
  id: string;
  message_id?: string;
  _identity: string;
  room_id: string;
  module_name: string;
  resource_name: string;
  mentions?: string[];
  reactions?: {
    [key: string]: UserItemShape[];
  };
  message: string;
  user: UserItemShape;
  user_id: string;
  attachments?: any[];
  file?: any;
  type: string;
  msgType: string;
  updated_at: string;
  created_at: string;
  system?: boolean;
  groupable?: boolean;
  extra?: MsgItemShape;
  filtered?: boolean;
}

export interface RoomItemShape {
  id: string;
  module_name: string;
  resource_name: string;
  type?: 'd' | 'c' | 'p';
  is_archived?: number | boolean;
  is_readonly?: number | boolean;
  name: string;
  last_message: MsgItemShape;
  updated_at: string;
  created_at: string;
  _identity?: string;
  other_members?: string[] | UserItemShape[];
  total_unseen?: number;
}
export interface ChatComposerProps {
  rid: string;
  user?: any;
  msgId?: string;
  focus?: boolean;
  text?: string;
  reactMode?: ReactMode;
  extra_data?: Record<string, any>;
  margin?: 'dense' | 'normal';
  onCancel?: () => void;
  onSuccess?: () => void;
  onFailure?: () => void;
  onMarkAsRead?: () => void;
  subscription?: any;
  previewRef?: any;
  variant?: 'buddy' | 'page';
}

export interface ChatMsgPassProps {
  disableReact: boolean;
  room?: RoomItemShape;
  handleAction?: HandleAction;
}

export interface MsgGroupShape {
  ts: string; // last ts
  t0: string;
  t1: string;
  items: MsgSetShape[];
}

export interface MsgSetShape {
  user: UserItemShape;
  user_id?: string;
  ts: string; // date from
  system?: boolean;
  groupable?: boolean;
  type?: string;
  items: string[];
}

type TypeFileRoom = {
  files: any[];
  count: number;
  total: number;
};

export interface RoomFiles {
  rid: string;
  media: TypeFileRoom;
  other: TypeFileRoom;
}

export interface OpenRoomShape {
  rid: string;
  collapsed: boolean;
}

export interface AppState {
  entities: {
    room: Record<string, RoomItemShape>;
    message: Record<string, MsgItemShape>;
    filterMessages: Record<string, MsgItemShape>;
  };
  openRooms: {
    values: OpenRoomShape[];
    active?: string;
    newChatRoom?: boolean;
  };
  chatRooms: Record<string, ChatRoomShape>;
  roomFiles: Record<string, RoomFiles>;
  newChatRoom: {
    searchText: string;
    collapsed: boolean;
    searching: boolean;
    results: {
      users: UserItemShape[];
    };
  };
}

export interface ChatRoomShape {
  room?: RoomItemShape;
  groups: Record<string, MsgGroupShape>;
  groupIds: string[];
  msgCount?: number;
  msgNewest?: string;
  oldest?: string | number;
  newest?: string | number;
  hasMore?: boolean;
  searching: boolean;
  searchText: string;
  collapsed?: boolean;
  messageFilter?: any;
  roomProgress?: any;
}

export type TooltipPosition = 'left' | 'right' | 'top' | 'bottom';

export interface MsgContentProps {
  message: MsgItemShape;
  createdDate: string;
  isOwner: boolean;
  tooltipPosition?: TooltipPosition;
  [key: string]: any;
}

export interface PreviewUploadFileHandle {
  attachFiles: (files: FileList) => void;
  clear?: () => void;
  checkIsLoading?: () => boolean;
}

export type ChatRoomType = 'p' | 'd' | 'c' | 'u';

export enum RoomType {
  Direct = 'd',
  Private = 'p',
  Public = 'c',
  OnlyUser = 'u'
}
