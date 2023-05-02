import { useDraftEditorConfig, useGlobal } from '@metafox/framework';
import { DialogActions } from '@metafox/dialog';
import composerConfig from '@metafox/feed/composerConfig';
import useComposerContext from '@metafox/feed/hooks/useComposerContext';
import React from 'react';
import Control from './Control';
import MuiButton from '@mui/lab/LoadingButton';
import { styled } from '@mui/material';

const ActionStyled = styled('div')(({ theme }) => ({
  flex: 1,
  [theme.breakpoints.down('sm')]: {
    '& span': {
      marginRight: theme.spacing(1.5)
    }
  }
}));
interface Props {
  submitting: boolean;
  onSubmit: () => void;
  disabledSubmit: boolean;
  parentIdentity?: string;
  parentType?: string;
}

const ComposerAction = ({
  submitting,
  onSubmit,
  disabledSubmit,
  parentIdentity,
  parentType
}: Props) => {
  const { jsxBackend, i18n } = useGlobal();

  const { composerRef, classes, editorRef, condition, strategy, isEdit } =
    useComposerContext();

  const [, , , attachers] = useDraftEditorConfig(composerConfig, condition);

  const updateBtnLabel = i18n.formatMessage({ id: !isEdit ? 'share' : 'save' });

  return (
    <DialogActions className={classes.dialogActions}>
      <MuiButton
        variant="contained"
        data-testid="submit"
        onClick={onSubmit}
        loading={submitting}
        disabled={disabledSubmit}
        color="primary"
        className={classes.btnShare}
      >
        {updateBtnLabel}
      </MuiButton>
      <ActionStyled>
        {attachers.map(item =>
          jsxBackend.render({
            component: item.as,
            props: {
              disabled: item.disabled,
              key: item.as,
              strategy,
              control: Control,
              composerRef,
              editorRef,
              parentIdentity,
              parentType
            }
          })
        )}
      </ActionStyled>
    </DialogActions>
  );
};

export default ComposerAction;
