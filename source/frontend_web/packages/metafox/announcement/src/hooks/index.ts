import { GlobalState } from '@metafox/framework';
import { get } from 'lodash';
import { useSelector } from 'react-redux';
import { createSelector } from 'reselect';
import { AnnouncementItemShape } from '../types';

export const getAnnouncements = (state: GlobalState) =>
  get(state, 'announcement.entities.announcement', {});

export const getAnnouncementsSelector = createSelector(
  getAnnouncements,
  data => data
);

export function useAnnouncements() {
  return useSelector<GlobalState, AnnouncementItemShape[]>(
    getAnnouncementsSelector
  );
}
