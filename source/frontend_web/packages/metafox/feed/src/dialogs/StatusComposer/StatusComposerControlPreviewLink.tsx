/**
 * @type: ui
 * name: StatusComposerControlPreviewLink
 */

import {
  LinkShape,
  StatusComposerControlProps,
  useGlobal
} from '@metafox/framework';
import { StyledIconButton } from '@metafox/ui';
import { Theme } from '@mui/material';
import { createStyles, makeStyles } from '@mui/styles';
import { get } from 'lodash';
import React from 'react';

const useStyles = makeStyles(
  (theme: Theme) =>
    createStyles({
      root: {
        position: 'relative',
        margin: theme.spacing(1, 2, 0, 2)
      },
      removeButtonWrapper: {
        position: 'absolute',
        top: 10,
        right: 10
      }
    }),
  { name: 'previewLink' }
);

export default function PreviewLink({
  composerRef,
  isEdit
}: StatusComposerControlProps) {
  const classes = useStyles();
  const { i18n, jsxBackend } = useGlobal();
  const item: LinkShape = get(
    composerRef.current.state,
    'attachments.link.value'
  );

  const ItemView = jsxBackend.get('feedArticle.view.list.embedItem');

  React.useEffect(() => {
    if (item?.is_preview_hidden && isEdit) {
      composerRef.current.setAttachments('link', 'link', {
        as: 'StatusComposerControlPreviewLink',
        value: { ...item, is_preview_hidden: false }
      });
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  const handleRemoveAll = () => {
    composerRef.current.setAttachments('link', 'link', {
      as: 'StatusComposerControlPreviewLink',
      value: { ...item, is_preview_hidden: true }
    });
  };

  if (item?.is_preview_hidden) return null;

  return (
    <div>
      <div className={classes.root}>
        <ItemView {...item} widthImage="180px" />
        <div className={classes.removeButtonWrapper}>
          <StyledIconButton
            color="inherit"
            icon="ico-close"
            size="small"
            title={i18n.formatMessage({ id: 'remove_all' })}
            onClick={handleRemoveAll}
          />
        </div>
      </div>
    </div>
  );
}
