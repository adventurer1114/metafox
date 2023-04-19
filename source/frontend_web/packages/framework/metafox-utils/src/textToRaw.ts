import { RawDraftContentState, genKey } from 'draft-js';

const mentionReg =
  /\[(?:user|page|group)=(\d+)\]([^[]+)\[\/(?:user|page|group)\]/gm;

export default function textToRaw(text: string): RawDraftContentState {
  const listMention = text.match(mentionReg);
  const entityRanges = [];
  const entityMap = {};

  if (Array.isArray(listMention) && listMention.length > 0) {
    listMention.forEach((item, index) => {
      const offset = text.indexOf(item);
      const mentionName = item.replace(mentionReg, '$2');
      const mentionId = item.replace(mentionReg, '$1');

      text = text.replace(
        text.substring(offset, offset + item.length),
        mentionName
      );

      entityRanges.push({
        offset,
        length: mentionName.length,
        key: index
      });

      entityMap[index] = {
        type: 'mention',
        mutability: 'SEGMENTED',
        data: {
          mention: {
            name: mentionName,
            link: `@mention/user/${mentionId}`
          }
        }
      };
    });
  }

  const rawState: RawDraftContentState = {
    blocks: [
      {
        key: genKey(),
        text,
        type: 'unstyled',
        inlineStyleRanges: [],
        data: {},
        depth: 0,
        entityRanges
      }
    ],
    entityMap
  };

  return rawState;
}
