import { EditorState } from 'draft-js';

export default function editorStateToText(text: string): EditorState {
  return EditorState.createEmpty();
}
