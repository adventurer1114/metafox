import { AtomicBlockUtils, EditorState, SelectionState } from 'draft-js';
import { uniqueId } from 'lodash';
import React from 'react';
import ImagePickerModal from './ImagePickerModal';

const icon =
  // eslint-disable-next-line max-len
  'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTUiIGhlaWdodD0iMTQiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGcgZmlsbD0iIzAwMCIgZmlsbC1ydWxlPSJldmVub2RkIj48cGF0aCBkPSJNMTQuNzQxIDBILjI2Qy4xMTYgMCAwIC4xMzYgMCAuMzA0djEzLjM5MmMwIC4xNjguMTE2LjMwNC4yNTkuMzA0SDE0Ljc0Yy4xNDMgMCAuMjU5LS4xMzYuMjU5LS4zMDRWLjMwNEMxNSAuMTM2IDE0Ljg4NCAwIDE0Ljc0MSAwem0tLjI1OCAxMy4zOTFILjUxN1YuNjFoMTMuOTY2VjEzLjM5eiIvPjxwYXRoIGQ9Ik00LjEzOCA2LjczOGMuNzk0IDAgMS40NC0uNzYgMS40NC0xLjY5NXMtLjY0Ni0xLjY5NS0xLjQ0LTEuNjk1Yy0uNzk0IDAtMS40NC43Ni0xLjQ0IDEuNjk1IDAgLjkzNC42NDYgMS42OTUgMS40NCAxLjY5NXptMC0yLjc4MWMuNTA5IDAgLjkyMy40ODcuOTIzIDEuMDg2IDAgLjU5OC0uNDE0IDEuMDg2LS45MjMgMS4wODYtLjUwOSAwLS45MjMtLjQ4Ny0uOTIzLTEuMDg2IDAtLjU5OS40MTQtMS4wODYuOTIzLTEuMDg2ek0xLjgxIDEyLjE3NGMuMDYgMCAuMTIyLS4wMjUuMTcxLS4wNzZMNi4yIDcuNzI4bDIuNjY0IDMuMTM0YS4yMzIuMjMyIDAgMCAwIC4zNjYgMCAuMzQzLjM0MyAwIDAgMCAwLS40M0w3Ljk4NyA4Ljk2OWwyLjM3NC0zLjA2IDIuOTEyIDMuMTQyYy4xMDYuMTEzLjI3LjEwNS4zNjYtLjAyYS4zNDMuMzQzIDAgMCAwLS4wMTYtLjQzbC0zLjEwNC0zLjM0N2EuMjQ0LjI0NCAwIDAgMC0uMTg2LS4wOC4yNDUuMjQ1IDAgMCAwLS4xOC4xTDcuNjIyIDguNTM3IDYuMzk0IDcuMDk0YS4yMzIuMjMyIDAgMCAwLS4zNTQtLjAxM2wtNC40IDQuNTZhLjM0My4zNDMgMCAwIDAtLjAyNC40My4yNDMuMjQzIDAgMCAwIC4xOTQuMTAzeiIvPjwvZz48L3N2Zz4=';

interface Props {
  onChange?: (data: EditorState) => void;
  editorState?: EditorState;
}

interface ImageShape {
  src: string;
  width?: unknown;
  height?: unknown;
  align?: unknown;
}

export function ImageModal() {
  const stopEvent = evt => {
    evt.preventDefault();
    evt.stopPropagation();
  };

  return <div className="rdw-image-modal" onClick={stopEvent}></div>;
}

function ImagePicker(props: Props) {
  const [expanded, setExpaned] = React.useState<string>();
  const { editorState, onChange } = props;
  const [data, setData] = React.useState<ImageShape>();
  const [sel, setSel] = React.useState<SelectionState>();

  const handleExited = React.useCallback(() => {
    if (!data?.src) {
      return;
    }

    const state = EditorState.forceSelection(editorState, sel);

    const entityKey = state
      .getCurrentContent()
      .createEntity('IMAGE', 'IMMUTABLE', data)
      .getLastCreatedEntityKey();

    const newEditorState = AtomicBlockUtils.insertAtomicBlock(
      state,
      entityKey,
      ' '
    );
    onChange(newEditorState);
    setExpaned(undefined);
  }, [data, editorState, onChange, sel]);

  const handleOpen = () => {
    setSel(editorState.getSelection());
    setExpaned(uniqueId('pick image'));
  };

  return (
    <div
      aria-haspopup="true"
      aria-expanded={expanded}
      aria-label="rdw-image-control"
      className="rdw-image-wrapper"
    >
      <div onClick={handleOpen} className="rdw-option-wrapper" title="Image">
        <img src={icon} alt="alt" />
      </div>
      {expanded ? (
        <ImagePickerModal
          key={expanded}
          onExited={handleExited}
          onChange={setData}
        />
      ) : null}
    </div>
  );
}
export default ImagePicker;
