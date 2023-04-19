import { useDraftEditorConfig, useGlobal } from '@metafox/framework';
import { DialogActions } from '@metafox/dialog';
import composerConfig from '@metafox/feed/composerConfig';
import useComposerContext from '@metafox/feed/hooks/useComposerContext';
import React from 'react';
import Control from './Control';
import MuiButton from '@mui/lab/LoadingButton';

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
      <div style={{ flex: 1 }}>
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
      </div>
    </DialogActions>
  );
};

export default ComposerAction;
