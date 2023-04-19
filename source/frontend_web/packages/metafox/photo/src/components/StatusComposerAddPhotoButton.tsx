/**
 * @type: ui
 * name: statusComposer.control.StatusUploadPhotoButton
 */
import {
  StatusComposerControlProps,
  useGlobal,
  useGetItem,
  ItemViewBaseProps
} from '@metafox/framework';
import { get, has } from 'lodash';
import React from 'react';
import useAddPhotoToStatusComposerHandler from '../hooks/useAddPhotoToStatusComposerHandler';

const checkCanUploadPhoto = ({
  setting,
  acl,
  forceAcceptVideo,
  profileSettings
}) => {
  // check permission profileSettings if have (default true)

  return (
    !forceAcceptVideo &&
    get(acl, 'photo.photo.create') &&
    get(setting, 'feed.types.photo_set.can_create_feed') &&
    (profileSettings && has(profileSettings, 'photo_share_photos')
      ? profileSettings.photo_share_photos
      : true)
  );
};

const checkCanUploadVideo = ({ setting, acl, profileSettings }) => {
  // check permission profileSettings if have (default true)

  return (
    get(acl, 'video.video.create') &&
    get(setting, 'feed.types.photo_set.can_create_feed') &&
    (profileSettings && has(profileSettings, 'video_share_videos')
      ? profileSettings.video_share_videos
      : true)
  );
};

const filterAcceptType = ({
  setting,
  acl,
  forceAcceptVideo,
  profileSettings
}) => {
  const acceptTypes = [];
  const canUploadTypes: Record<string, any> = {};

  if (
    checkCanUploadPhoto({
      setting,
      acl,
      forceAcceptVideo,
      profileSettings
    })
  ) {
    acceptTypes.push('image/*');
    canUploadTypes.photo = true;
  }

  if (
    checkCanUploadVideo({
      setting,
      acl,
      profileSettings
    })
  ) {
    acceptTypes.push('video/*');
    canUploadTypes.video = true;
  }

  return { acceptTypes: acceptTypes.join(', '), canUploadTypes };
};

export default function StatusComposerControlUploadPhotoButton(
  props: StatusComposerControlProps & {
    forceAcceptVideo?: boolean;
    label: string;
    subject?: ItemViewBaseProps;
  }
) {
  const { i18n, getSetting, getAcl } = useGlobal();
  const setting = getSetting();
  const acl = getAcl();
  const {
    control: Control,
    disabled,
    label,
    forceAcceptVideo = false,
    subject,
    parentIdentity
  } = props;
  const parentItem = useGetItem(parentIdentity);

  const inputRef = React.useRef<HTMLInputElement>();

  const handleResetValue = (
    event: React.MouseEvent<HTMLInputElement, MouseEvent>
  ) => {
    event.currentTarget.value = null;
  };

  const { acceptTypes, canUploadTypes } = filterAcceptType({
    setting,
    acl,
    forceAcceptVideo,
    profileSettings: subject?.profile_settings || parentItem?.profile_settings
  });

  const [handleChange, onClick] = useAddPhotoToStatusComposerHandler(
    props.composerRef,
    inputRef,
    canUploadTypes
  );

  if (!acceptTypes.length) return;

  return (
    <>
      <Control
        disabled={disabled}
        testid="attachPhoto"
        onClick={onClick}
        icon="ico-photos-alt-o"
        label={label}
        title={i18n.formatMessage({
          id: disabled ? 'this_cant_be_combined' : 'upload_media'
        })}
        canUploadTypes={canUploadTypes}
      />
      <input
        onChange={handleChange}
        multiple
        onClick={handleResetValue}
        data-testid="inputAttachPhoto"
        ref={inputRef}
        className="srOnly"
        type="file"
        accept={acceptTypes}
      />
    </>
  );
}
