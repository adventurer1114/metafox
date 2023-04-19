import { EditorState, Modifier, SelectionState } from 'draft-js';

export default function insertText(text: string, editorState: EditorState) {
  const currentContent = editorState.getCurrentContent();
  const currentSelection: SelectionState = editorState.getSelection();

  const newContent = Modifier.insertText(
    currentContent,
    currentSelection,
    text
  );

  const newEditorState = EditorState.push(
    editorState,
    newContent,
    'insert-characters'
  );

  return EditorState.forceSelection(
    newEditorState,
    newContent.getSelectionAfter()
  );
}
