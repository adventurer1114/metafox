/* eslint-disable @typescript-eslint/no-unused-vars */

import { InputNotched } from '@metafox/ui';
import { stripTags } from '@metafox/utils';
import { FormControl, InputLabel, Theme } from '@mui/material';
import { createStyles, makeStyles } from '@mui/styles';
import { alpha } from '@mui/system/colorManipulator';
import clsx from 'clsx';
import { ContentState, convertToRaw, EditorState } from 'draft-js';
import draftToHtml from 'draftjs-to-html';
import { useField } from 'formik';
import htmlToDraft from 'html-to-draftjs';
import { camelCase } from 'lodash';
import React from 'react';
import { Editor } from 'react-draft-wysiwyg';
import { FormFieldProps } from '@metafox/form';
import ErrorMessage from '../ErrorMessage';
import './react-draft-wysiwyg.css';
import { useGlobal, useTraceUpdate } from '@metafox/framework';
import ImagePickerControl from './ImagePicker/ImagePickerControl';

const toolbarCustomButtons = [<ImagePickerControl key="imagePicker" />];

const editorToolbar = {
  options: ['inline', 'fontSize', 'list', 'link', 'history'],
  inline: {
    inDropdown: false
  }
};

const editorStyles = {
  minHeight: '100px'
};
const toolbarStyle = {
  border: 'none'
};

const useStyles = makeStyles((theme: Theme) =>
  createStyles({
    formLabel: {
      paddingLeft: '4px !important'
    },
    'formLabel-outlined': {
      backgroundColor: theme.palette.background.paper,
      paddingRight: theme.spacing(0.5),
      paddingLeft: theme.spacing(0.5),
      marginLeft: theme.spacing(-0.5)
    },
    RDW: {
      position: 'relative',
      padding: 12,
      '& #mui-rte-toolbar': {},
      '& #mui-rte-editor': {},
      '& .rdw-option-wrapper.rdw-option-active': {
        borderColor: alpha(theme.palette.primary.main, 0.5),
        boxShadow: 'none',
        backgroundColor: alpha(theme.palette.primary.main, 0.5),
        '&:hover': {
          boxShadow: 'none'
        }
      },
      '& .rdw-dropdown-optionwrapper': {
        background:
          theme.palette.mode === 'light'
            ? '#fff'
            : theme.palette.background.paper,
        '& .rdw-dropdownoption-highlighted': {
          background:
            theme.palette.mode === 'light'
              ? '#f1f1f1'
              : theme.palette.background.default
        }
      },
      '& .rdw-link-modal, & .rdw-image-modal': {
        background:
          theme.palette.mode === 'light'
            ? '#fff'
            : theme.palette.background.paper,
        boxShadow:
          theme.palette.mode === 'light'
            ? '3px 3px 5px #bfbdbd'
            : `3px 3px 5px ${theme.palette.background.paper}`
      },
      '& .rdw-image-alignment-options-popup': {
        color: '#050505',
        background:
          theme.palette.mode === 'light'
            ? '#fff'
            : theme.palette.background.paper
      }
    },
    'RDW-outlined': {
      '&:hover': {
        '& > fieldset': {
          borderColor: theme.palette.text.primary
        }
      }
    },
    'RDW-outlined-focused': {
      '& > fieldset': {
        borderColor: `${theme.palette.primary.main} !important`,
        border: '2px solid'
      },
      '& > label': {
        color: theme.palette.primary.main
      }
    },
    'RDW-filled': {},
    'RDW-standard': {
      padding: theme.spacing(1, 0, 0, 0),
      borderWidth: '0 0 1px 0'
    },
    'RDW-error': {
      '& > fieldset': {
        borderColor: `${theme.palette.error.main} !important`
      },
      '& > label': {
        color: theme.palette.error.main
      }
    },
    hidePlaceholder: {
      '& .public-DraftEditorPlaceholder-root': {
        display: 'none'
      }
    },
    hiddenToolbar: {
      display: 'none !important'
    }
  })
);

const regexImg = /<img([\w\W]+?)>/m;
const regexStartRule = /.*?<p/m;

const RichTextEditorField = ({
  config,
  name,
  disabled: forceDisabled,
  formik
}: FormFieldProps) => {
  const {
    label,
    variant,
    color,
    required,
    fullWidth = true,
    disabled,
    placeholder,
    margin = 'normal'
  } = config;

  const classes = useStyles();
  const [focused, setFocused] = React.useState<boolean>(false);
  const [field, meta, { setValue }] = useField(name ?? 'RichTextEditorField');
  const [stateValue, setStateValue] = React.useState(field?.value);
  const { useIsMobile } = useGlobal();
  const isMobile = useIsMobile();

  const initialized = React.useRef(false);

  const initValue = React.useRef(
    EditorState.createWithContent(ContentState.createFromText(''))
  );

  const [editorState, setEditorState] = React.useState(initValue.current);

  React.useEffect(() => {
    // field.value !== stateValue check when has change from outside component, ex: reset data form
    if (!initialized.current || field.value !== stateValue) {
      const blocksFromHtml = htmlToDraft(field.value ? field.value : '');
      const { contentBlocks, entityMap } = blocksFromHtml;
      const contentState = ContentState.createFromBlockArray(
        contentBlocks,
        entityMap
      );
      initValue.current = EditorState.createWithContent(contentState);
      setEditorState(initValue.current);
    }

    initialized.current = true;
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [field.value]);

  const lastValue = React.useRef('');

  const handleChange = React.useCallback(
    (state: EditorState) => {
      setEditorState(state);
      let value = draftToHtml(convertToRaw(state.getCurrentContent()));
      // hot fix https://github.com/jpuri/react-draft-wysiwyg/issues/609#issuecomment-1282192676
      // crash site if start without tag <p>
      value = value.replace(regexStartRule, '<p');

      let result = '';

      if (stripTags(value).trim() || regexImg.test(value)) {
        result = value;
      }

      if (lastValue.current !== result) {
        lastValue.current = value;
        setStateValue(result);
      }
    },
    // eslint-disable-next-line react-hooks/exhaustive-deps
    []
  );

  React.useEffect(() => {
    // stateValue save local value for compare if has change field.value from outside
    setValue(stateValue);
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [stateValue]);

  const handlePastedText = React.useCallback(props => {
    return false;
  }, []);

  const handleFocus = React.useCallback(() => {
    setFocused(true);
  }, []);

  const handleBlur = React.useCallback(() => {
    setFocused(false);
  }, []);

  const haveError: boolean = !!(
    meta.error &&
    (meta.touched || formik.submitCount)
  );

  const editorRef = React.useRef<Editor>();

  const handleControlClick = React.useCallback(
    (evt: React.MouseEvent<HTMLDivElement>) => {
      if (evt && editorRef.current) {
        editorRef.current.focusEditor();
      }
    },
    []
  );

  const contentState = editorState.getCurrentContent();
  let forceHidePlaceholder = false;

  if (
    !contentState.hasText() &&
    contentState.getBlockMap().first().getType() !== 'unstyled'
  ) {
    forceHidePlaceholder = true;
  }

  return (
    <FormControl
      margin={margin}
      required={required}
      disabled={disabled || forceDisabled || formik.isSubmitting}
      fullWidth={fullWidth}
      data-testid={camelCase(`field ${name}`)}
    >
      <div
        onClick={handleControlClick}
        className={clsx(
          classes.RDW,
          classes[`RDW-${variant}`],
          focused && classes[`RDW-${variant}-focused`],
          haveError && classes['RDW-error'],
          {
            [classes.hidePlaceholder]: forceHidePlaceholder
          }
        )}
      >
        <InputLabel
          required={required}
          shrink
          variant={variant as any}
          data-testid={camelCase(`input ${name}`)}
          className={clsx(
            classes.formLabel,
            classes[`formLabel-${variant}`],
            focused && classes[`formLabel-${variant}-focused`]
          )}
          disabled={disabled || forceDisabled || formik.isSubmitting}
          color={color}
        >
          {label}
        </InputLabel>
        <Editor
          ref={editorRef}
          onFocus={handleFocus}
          onBlur={handleBlur}
          readOnly={disabled || forceDisabled || formik.isSubmitting}
          editorState={editorState}
          onEditorStateChange={handleChange}
          placeholder={placeholder}
          editorStyle={editorStyles}
          handlePastedText={handlePastedText}
          data-testid={camelCase(`input ${name}`)}
          toolbarStyle={toolbarStyle}
          toolbar={editorToolbar}
          toolbarCustomButtons={toolbarCustomButtons}
          toolbarClassName={isMobile && classes.hiddenToolbar}
        />
        <InputNotched children={label} variant={variant} />
      </div>
      {haveError ? <ErrorMessage error={meta.error} /> : null}
    </FormControl>
  );
};

export default RichTextEditorField;
