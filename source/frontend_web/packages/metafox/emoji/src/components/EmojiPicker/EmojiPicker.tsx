import { ScrollContainer } from '@metafox/layout';
import { OnEmojiClick } from '@metafox/emoji';
import { Box } from '@mui/material';
import { styled } from '@mui/material/styles';
import { isNil } from 'lodash';
import React from 'react';
import emojiData from './EmojiData.json';
import EmojiList from './EmojiList';

const Tabs = styled('div', {
  name: 'EmojiPicker',
  slot: 'Tabs'
})(({ theme }) => ({
  display: 'flex',
  flexDirection: 'row',
  borderTop: theme.mixins.border('secondary'),
  overflowX: 'auto',
  overflowY: 'hidden'
}));

const Tab = styled('div', {
  name: 'EmojiPicker',
  slot: 'Tab',
  shouldForwardProp: prop => prop !== 'active'
})<{
  active: boolean;
}>(({ theme, active }) =>
  Object.assign(
    {
      height: 32,
      fontSize: 16,
      padding: theme.spacing(0, 0.8, 0, 0.8),
      alignItems: 'center',
      display: 'flex',
      borderTop: '2px solid',
      borderTopColor: 'transparent'
    },
    active && {
      borderTopColor: theme.palette.primary.main
    }
  )
);
export interface Props {
  onEmojiClick?: OnEmojiClick;
}

export default function EmojiPicker({ onEmojiClick }: Props) {
  const scrollRef = React.useRef<HTMLDivElement>();
  const [activeTab, setActiveTab] = React.useState<string>(emojiData[0].label);
  const refs = React.useRef<Record<string, any>>({});

  const setRefs = (id: string, node: unknown) => {
    refs.current[id] = node;
  };

  const changeTab = React.useCallback((id: string) => {
    const top = refs.current[id]?.offsetTop;

    if (!isNil(top)) {
      scrollRef.current.scrollTop = top;
    }

    setActiveTab(id);
  }, []);

  return (
    <>
      <Box sx={{ height: 250 }}>
        <ScrollContainer
          autoHide
          autoHeight
          autoHeightMax={232}
          ref={scrollRef}
        >
          {emojiData
            // .filter(data => data.label === activeTab)
            .map(data => (
              <EmojiList
                onEmojiClick={onEmojiClick}
                ref={node => setRefs(data.label, node)}
                data={data}
                key={data.label.toString()}
              />
            ))}
        </ScrollContainer>
      </Box>
      <Tabs>
        {emojiData.map(data => (
          <Tab
            role="button"
            onClick={() => changeTab(data.label)}
            key={data.label.toString()}
            active={activeTab === data.label}
          >
            {data.data}
          </Tab>
        ))}
      </Tabs>
    </>
  );
}
