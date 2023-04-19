import Emoji from 'emojione';

export const emojiCategories = {
  people: {
    id: 'people',
    label: 'Smileys & People',
    icon: 'icon icon-people',
    items: []
  },
  smiley: {
    id: 'smiley',
    label: 'Animals & Nature',
    icon: 'icon icon-nature',
    items: []
  },
  food: {
    id: 'food',
    label: 'Food & Drink',
    icon: 'icon icon-food',
    items: []
  },
  activity: {
    id: 'activity',
    label: 'Activity',
    icon: 'icon icon-activity',
    items: []
  },
  travel: {
    id: 'travel',
    label: 'Travel & Places',
    icon: 'icon icon-travel',
    items: []
  },
  objects: {
    id: 'objects',
    label: 'Objects',
    icon: 'icon icon-objects',
    items: []
  },
  symbols: {
    id: 'symbols',
    label: 'Symbols',
    icon: 'icon icon-symbols',
    items: []
  },
  flags: { id: 'flags', label: 'Flags', icon: 'icon icon-flags', items: [] }
};

const excludesRegexp = /tone/i;

Object.keys(Emoji.emojioneList).forEach(k => {
  const { category: c } = Emoji.emojioneList[k];

  if (emojiCategories[c] && !excludesRegexp.test(k)) {
    emojiCategories[c].items.push({ name: k });
  }
});
