import { HandleAction } from '@metafox/framework';

export default function inviteItemActions(handleAction: HandleAction) {
  return {
    approveCoHostInvite: () => handleAction('event/approveCoHostInvite'),
    denyCoHostInvite: () => handleAction('event/denyCoHostInvite'),
    cancelInvitation: () => handleAction('event/cancelInvitation'),
    interestedEvent: () => handleAction('interestedEvent'),
    notInterestedEvent: () => handleAction('notInterestedEvent'),
    joinEvent: () => handleAction('joinEvent'),
    cancelHostInvitation: () => handleAction('cancelHostInvitation'),
    removeHost: () => handleAction('event/removeHost'),
    removeMemberGuest: () => handleAction('event_member/removeMember')
  };
}
