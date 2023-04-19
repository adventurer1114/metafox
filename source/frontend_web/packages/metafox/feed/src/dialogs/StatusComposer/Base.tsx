/**
 * @type: dialog
 * name: feed.status.statusComposerDialog
 * chunkName: draftjs
 */
import Editor from '@draft-js-plugins/editor';
import { Dialog } from '@metafox/dialog';
import {
  StatusComposerState,
  useGlobal,
  useLocation
} from '@metafox/framework';
import { editorStateToText, htmlToText, textToRaw } from '@metafox/utils';
import { Box } from '@mui/material';
import { convertFromRaw, EditorState } from 'draft-js';
import { isEmpty } from 'lodash';
import React, { useCallback, useEffect, useRef, useState } from 'react';
import useStatusComposer from '../../hooks/useStatusComposer';
import ComposerAction from './ComposerAction';
import ComposerContent from './ComposerContent';
import ComposerHeader from './ComposerHeader';
import useStyles from './styles';

export const ComposerContext = React.createContext(undefined);

export type StatusComposerDialogProps = {
  data: Partial<StatusComposerState>;
  editor?: {
    status_text?: string;
    status_background_id?: string;
  };
  id?: string;
  isEdit?: boolean;
  parentIdentity?: string;
  title?: string;
  parentType?: string;
  viewTypePrivacy?: boolean;
  hidePrivacy?: boolean;
  disabledPrivacy?: boolean;
};

const strategy = 'dialog';

const StatusComposerDialog = ({
  data = {},
  editor,
  id,
  isEdit,
  parentIdentity,
  parentType,
  viewTypePrivacy = false,
  hidePrivacy,
  disabledPrivacy,
  title = 'create_post',
  pageParams
}: StatusComposerDialogProps) => {
  const classes = useStyles();
  const location = useLocation();
  const {
    useDialog,
    dispatch,
    i18n,
    getSetting,
    getAcl,
    setNavigationConfirm,
    useSession
  } = useGlobal();
  const { user: authUser } = useSession();
  const parentId = parentIdentity ? parentIdentity.split('.')[3] : '';
  const isUserProfileOther =
    parentType === 'user' && parentId && authUser.id !== parseInt(parentId);
  const [composerState, , composerRef] = useStatusComposer(data);
  const { dialogProps, setUserConfirm, forceClose } = useDialog();

  const editorRef = useRef<Editor>();
  const isFirstRun = useRef<boolean>(true);

  const [disabledSubmit, setDisabled] = useState<boolean>();
  const [submitting, setSubmitting] = useState<boolean>(false);
  const [asPage, setAsPage] = React.useState<boolean>(false);

  const [editorState, setEditorState] = useState(() =>
    EditorState.createWithContent(
      convertFromRaw(textToRaw(htmlToText(editor?.status_text || '')))
    )
  );

  const setting = getSetting() as Object;
  const acl = getAcl() as Object;

  const condition = React.useMemo(() => {
    return {
      strategy,
      attachmentType: composerState.attachmentType,
      lengthText: editorStateToText(editorState).length,
      parentType,
      isEdit,
      data,
      setting,
      acl,
      isUserProfileOther
    };
  }, [
    composerState.attachmentType,
    editorState,
    parentType,
    isEdit,
    data,
    setting,
    acl,
    isUserProfileOther
  ]);

  const leaveConfirm = React.useMemo(() => {
    return {
      message: i18n.formatMessage({
        id: 'you_did_not_share_your_post'
      }),
      title: i18n.formatMessage({
        id: 'leave_page'
      }),
      negativeButton: {
        label: i18n.formatMessage({
          id: 'keep_editing'
        })
      },
      positiveButton: {
        label: i18n.formatMessage({
          id: 'leave'
        })
      }
    };
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  const handleAfterForceClose = useCallback(() => {
    forceClose();
    dispatch({
      type: 'formValues/onDestroy',
      payload: {
        formName: 'dialogStatusComposer'
      }
    });
  }, [dispatch, forceClose]);

  useEffect(() => {
    setNavigationConfirm(!disabledSubmit, leaveConfirm, () => {
      setEditorState(
        EditorState.createWithContent(convertFromRaw(textToRaw(htmlToText(''))))
      );
      handleAfterForceClose();
    });

    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [disabledSubmit]);

  useEffect(() => {
    if (isFirstRun.current) {
      isFirstRun.current = false;

      return;
    }

    forceClose();
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [location]);
  useEffect(() => {
    let disabled =
      editorStateToText(editorState).trim() === '' &&
      (!Object.keys(composerState.attachments).length ||
        !!composerState.attachments['statusBackground']);
    const isDirty =
      composerState.editing ||
      editorStateToText(editorState) !== (editor?.status_text || '');

    if (
      !isEmpty(composerState.tags?.place?.value) ||
      !isEmpty(composerState.tags?.friends?.value)
    ) {
      disabled = false;
    }

    if (isEdit) {
      disabled = isDirty ? false : true;
    }

    if (composerState.disabled !== disabled) {
      composerRef.current.setDisabled(disabled);
    }

    setDisabled(submitting || disabled);

    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [submitting, editorState, composerState, composerRef, editor]);

  const handleSubmit = useCallback(() => {
    setSubmitting(true);
    dispatch({
      type: 'statusComposer/SUBMIT',
      payload: {
        initValue: data,
        composerState: composerRef.current.state,
        text: editorStateToText(editorRef.current.props.editorState),
        isEdit,
        id,
        parentIdentity,
        parentUser: data?.parentUser
      },
      meta: {
        onSuccess: handleAfterForceClose,
        onFailure: () => setSubmitting(false)
      }
    });
  }, [
    dispatch,
    data,
    composerRef,
    isEdit,
    id,
    parentIdentity,
    handleAfterForceClose
  ]);

  setUserConfirm(() => {
    if (!disabledSubmit && isEdit) {
      return {
        message: i18n.formatMessage({
          id: 'the_change_you_made_will_not_be_saved'
        }),
        title: i18n.formatMessage({
          id: 'unsaved_changes'
        })
      };
    }
  });

  const handleClose = (e, reason) => {
    // update form value to reducers

    !isEdit &&
      dispatch({
        type: 'formValues/onChange',
        payload: {
          formName: 'dialogStatusComposer',
          data: editorStateToText(editorRef.current.props.editorState)
        }
      });

    dialogProps.onClose && dialogProps.onClose(e, reason);
  };

  return (
    <Dialog
      {...dialogProps}
      data-testid="dialogStatusComposer"
      maxWidth="sm"
      fullWidth
      onClose={handleClose}
    >
      <Box className="dialogStatusComposer">
        <ComposerContext.Provider
          value={{
            data,
            classes,
            composerState,
            composerRef,
            editorState,
            condition,
            setEditorState,
            isEdit,
            strategy,
            editor,
            editorRef,
            pageParams,
            asPage,
            setAsPage
          }}
        >
          <ComposerHeader title={title} closeDialog={handleClose} />
          <ComposerContent
            viewTypePrivacy={viewTypePrivacy}
            hidePrivacy={hidePrivacy}
            parentIdentity={parentIdentity}
            parentType={parentType}
            disabledPrivacy={disabledPrivacy}
          />
          <ComposerAction
            submitting={submitting}
            onSubmit={handleSubmit}
            disabledSubmit={disabledSubmit}
            parentIdentity={parentIdentity}
            parentType={parentType}
          />
        </ComposerContext.Provider>
      </Box>
    </Dialog>
  );
};

export default StatusComposerDialog;
