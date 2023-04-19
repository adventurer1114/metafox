import { EditorState } from 'draft-js';
import { stateToHTML } from 'draft-js-export-html';

const mentionReg = /<a href="@mention\/(\w+)\/(\d+)">([^<]+)<\/a>/gm;

export default function editorStateToText(editorState: EditorState): string {
  let text = stateToHTML(editorState.getCurrentContent(), {
    entityStyleFn: entity => {
      const entityType = entity.getType().toUpperCase();
      const data = entity.getData();

      switch (entityType) {
        case 'MENTION':
          return {
            element: 'a',
            attributes: {
              href: data.mention.link
            }
          };
        case 'IMAGE':
          return {
            element: 'img',
            attributes: {
              src: data.src
            }
          };
      }
    }
  });

  text = text
    .replace(mentionReg, '[$1=$2]$3[/$1]')
    .replace(/&nbsp;/gm, ' ')
    .replace('&amp;', '&')
    .replace(/(<([^>]+)>)/gi, '');

  return text;
}
