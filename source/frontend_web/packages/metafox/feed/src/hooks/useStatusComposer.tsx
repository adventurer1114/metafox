import {
  BackgroundStatusProps,
  StatusComposerRef,
  StatusComposerState
} from '@metafox/framework';
import { isEqual, omit } from 'lodash';
import React from 'react';

const defaultState: StatusComposerState = {
  className: '',
  privacy: 0,
  attachmentType: '',
  disabled: true,
  textAlignment: 'left',
  editorStyle: {},
  tags: {},
  attachments: {},
  editing: false,
  post_as_parent: false
};

export default function useStatusComposer(
  initState?: Partial<StatusComposerState>
): [
  StatusComposerState,
  React.Dispatch<StatusComposerState>,
  React.MutableRefObject<StatusComposerRef>
] {
  const [composerState, setComposerState] = React.useState<StatusComposerState>(
    Object.assign({}, defaultState, initState)
  );

  const backgroundRef = React.useRef<BackgroundStatusProps>({
    className: initState?.className || '',
    textAlignment: initState?.textAlignment || 'left',
    item: {},
    editorStyle: initState?.editorStyle || {}
  });
  const initialState = React.useRef<StatusComposerState>(composerState);
  const composerRef = React.useRef<StatusComposerRef>({
    state: composerState,
    setState: setComposerState,
    setEditorStyle: (style: React.CSSProperties) =>
      setComposerState(prev => ({
        ...prev,
        editorStyle: style
      })),
    setTextAlignment: (value: 'left' | 'right' | 'center') =>
      setComposerState(prev => ({
        ...prev,
        textAlignment: value
      })),
    setDisabled: (value: boolean) =>
      setComposerState(prev => ({
        ...prev,
        disabled: !!value
      })),
    setBackground: ({
      className,
      textAlignment,
      item,
      editorStyle
    }: BackgroundStatusProps) => {
      backgroundRef.current = { className, textAlignment, item, editorStyle };
      setComposerState(prev => ({
        ...prev,
        attachmentType: 'backgroundStatus',
        className,
        textAlignment,
        attachments: {
          statusBackground: {
            as: null,
            value: item
          }
        },
        editorStyle
      }));
    },
    removeBackground: () => {
      backgroundRef.current = {
        className: '',
        textAlignment: 'left',
        item: {},
        editorStyle: {}
      };
      setComposerState(prev => ({
        ...prev,
        textAlignment: 'left',
        className: '',
        attachmentType: '',
        editorStyle: {},
        attachments: {
          statusBackground: {
            as: null,
            value: { id: 0 }
          }
        }
      }));
    },
    displayBackground: () => {
      setComposerState(prev => ({
        ...prev,
        className: backgroundRef.current.className,
        textAlignment: backgroundRef.current.textAlignment,
        editorStyle: backgroundRef.current.editorStyle
      }));
    },
    hideBackground: () => {
      setComposerState(prev => ({
        ...prev,
        className: '',
        textAlignment: 'left',
        editorStyle: {}
      }));
    },
    setAttachments: (type: string, name: string, value: unknown) =>
      setComposerState(prev => ({
        ...prev,
        attachmentType: type,
        attachments: {
          [name]: value as any
        }
      })),
    setPrivacy: (value: any) =>
      setComposerState(prev => ({
        ...prev,
        privacy: value
      })),
    removeAttachments: () =>
      setComposerState(prev => ({
        ...prev,
        attachmentType: '',
        attachments: {}
      })),
    setTags: (name: string, value: unknown) =>
      setComposerState(prev => ({
        ...prev,
        tags: {
          ...prev.tags,
          [name]: value as any
        }
      })),
    removeTags: (name: string) =>
      setComposerState(prev => ({
        ...prev,
        tags: omit(prev.tags, [name])
      })),
    setPostAsPage: (value: boolean) =>
      setComposerState(prev => ({
        ...prev,
        post_as_parent: value
      })),
    requestComposerUpdate: () => {}
  });

  React.useEffect(() => {
    if (composerRef.current.state !== composerState) {
      if (
        isEqual(
          omit(composerState, [
            'editing',
            'disabled',
            'extra',
            'post_as_parent',
            'tags.place.priority',
            'tags.place.value.icon',
            'tags.place.value.name',
            'tags.place.value.query'
          ]),
          omit(initialState.current, [
            'editing',
            'disabled',
            'extra',
            'post_as_parent',
            'tags.place.priority',
            'tags.place.value.icon',
            'tags.place.value.name',
            'tags.place.value.query'
          ])
        )
      ) {
        setComposerState(prev => ({
          ...prev,
          editing: false
        }));
      } else {
        setComposerState(prev => ({
          ...prev,
          editing: true
        }));
      }

      setComposerState(prev => {
        return (composerRef.current.state = { ...prev });
      });
    }
  }, [composerState]);

  return [composerState, setComposerState, composerRef];
}
