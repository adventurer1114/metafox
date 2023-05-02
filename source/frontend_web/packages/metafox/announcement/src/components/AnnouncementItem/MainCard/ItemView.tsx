import { Link } from '@metafox/framework';
import { ItemSummary, ItemTitle, ItemView } from '@metafox/ui';
import { styled } from '@mui/material';
import React from 'react';
import { AnnouncementItemProps } from '../../../types';

type Props = AnnouncementItemProps;

const Description = styled('span')(({ theme }) => ({
  flex: 'unset',
  marginTop: 'unset',
  '[dir="rtl"] &': {
    '& span': {
      display: 'inline-block'
    }
  }
}));
const Total = styled('div')(({ theme }) => ({
  display: 'flex',
  marginTop: theme.spacing(1),
  '& div:first-of-type p': {
    fontWeight: 'bold'
  }
}));

const Dot = styled('span')(({ theme }) => ({
  padding: theme.spacing(0, 0.5)
}));

const capitalizeFirstLetter = (string: String) => {
  const lowerCase = string.toLowerCase();

  return lowerCase.charAt(0).toUpperCase() + lowerCase.slice(1);
};

const StyleNotification = styled('span', {
  name: 'StyleNotification',
  shouldForwardProp: prop => prop !== 'type'
})<{ type?: string }>(({ theme, type }) => ({
  ...(type === 'danger' && {
    color: theme.palette.error.main
  }),
  ...(type === 'success' && {
    color: theme.palette.success.main
  }),
  ...(type === 'warning' && {
    color: theme.palette.warning.main
  }),
  ...(type === 'info' && {
    color: theme.palette.primary.main
  })
}));

export default function AnnouncementItemView({
  item,
  wrapAs,
  wrapProps
}: Props) {
  if (!item) return null;

  const { link: to } = item;

  return (
    <ItemView wrapAs={wrapAs} wrapProps={wrapProps} testid={'itemAnnouncement'}>
      <ItemTitle role="button">
        <Link color="inherit" to={to} asModal resetModal>
          {item?.title}
        </Link>
      </ItemTitle>
      <Total>
        <Description role="button">
          <ItemSummary>
            <StyleNotification type={item?.style}>
              {capitalizeFirstLetter(item?.style)}
            </StyleNotification>
            <Dot>&bull;</Dot>
            <span
              style={{ fontWeight: 'normal' }}
            >{`${item?.description}`}</span>
          </ItemSummary>
        </Description>
      </Total>
    </ItemView>
  );
}
