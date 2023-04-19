import { HandleAction } from '@metafox/framework';

export default function userItemActions(handleAction: HandleAction) {
  return {
    toggleMenu: () => handleAction('toggleMenu'),
    editProfileHeaderAvatar: () => handleAction('editProfileHeaderAvatar'),
    presentMutualFriends: () => handleAction('friend/presentMutualFriends'),
    presentFriends: () => handleAction('friend/presentFriends'),
    cancelInvitation: () => handleAction('group/cancelInvitation')
  };
}
