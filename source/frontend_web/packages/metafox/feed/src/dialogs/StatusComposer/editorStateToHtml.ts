import { EditorState } from 'draft-js';
import { stateToHTML } from 'draft-js-export-html';

export default function editorStateToText(editorState: EditorState): string {
  return stateToHTML(editorState.getCurrentContent());
}
