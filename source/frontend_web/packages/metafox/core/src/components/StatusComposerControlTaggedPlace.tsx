/**
 * @type: ui
 * name: StatusComposerControlTaggedPlace
 */
import { StatusComposerControlProps, useGlobal } from '@metafox/framework';
import React from 'react';

export default function StatusComposerControlTaggedPlace(
  props: StatusComposerControlProps
) {
  const { i18n, dialogBackend } = useGlobal();
  const { value } = props;

  if (!value) {
    return null;
  }

  const onClick = () => {
    dialogBackend
      .present({
        component: 'core.dialog.PlacePickerDialog',
        props: {
          defaultValue: value
        }
      })
      .then(value => {
        if (value === false) {
          const { setTags } = props.composerRef.current;

          setTags('place', {
            as: 'StatusComposerControlTaggedPlace',
            priority: 3,
            value: undefined
          });
        } else if (value) {
          const { setTags } = props.composerRef.current;

          setTags('place', {
            as: 'StatusComposerControlTaggedPlace',
            priority: 3,
            value
          });
        }
      });
  };

  return (
    <span onClick={onClick} data-place="tagPlace">
      {i18n.formatMessage(
        { id: 'at_tagged_place' },
        {
          name: value.name
        }
      )}
    </span>
  );
}
