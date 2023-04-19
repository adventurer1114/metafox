/**
 * @type: block
 * name: core.block.footer
 * title: Footer Menu
 * keywords: general
 * description: Display footer menu include privacy, terms of use, ....
 * chunkName: block.home
 */

import { createBlock, Link, useGlobal } from '@metafox/framework';
import { styled } from '@mui/material';
import React from 'react';

type FooterMenuProps = {
  color?:
    | 'inherit'
    | 'initial'
    | 'primary'
    | 'secondary'
    | 'textPrimary'
    | 'textSecondary'
    | 'error';
  [key: string]: any;
};

const name = 'Footer';

const Root = styled('div', { name, slot: 'root' })(({ theme }) => ({
  padding: theme.spacing(2.5, 0)
}));

const Content = styled('div', {
  name,
  slot: 'content',
  shouldForwardProp: prop => prop !== 'isSideSlot'
})<{ isSideSlot: boolean }>(({ theme, isSideSlot }) => ({
  display: isSideSlot ? 'block' : 'flex',
  justifyContent: 'space-between',
  alignItems: 'center',
  lineHeight: isSideSlot ? '1.5' : 'unset',
  color: isSideSlot
    ? theme.palette.text.hint
    : theme.palette.default.contrastText
}));

const LeftMenu = styled('ul', {
  name,
  slot: 'leftMenu',
  shouldForwardProp: prop => prop !== 'isSideSlot'
})<{
  isSideSlot: boolean;
}>(({ theme, isSideSlot }) => ({
  listStyle: 'none',
  padding: 0,
  margin: 0,
  display: isSideSlot ? 'inline' : 'block',
  '& > li': {
    display: 'inline-block',
    paddingRight: isSideSlot ? theme.spacing(2) : theme.spacing(3)
  }
}));

const RightMenu = styled('div', { name, slot: 'rightMenu' })<{
  isSideSlot: boolean;
}>(({ theme, isSideSlot }) => ({
  display: isSideSlot ? 'inline' : 'block',
  '& > span': {
    display: 'inline-block',
    '&:not(:last-child)': {
      paddingRight: isSideSlot ? theme.spacing(2) : theme.spacing(3)
    }
  }
}));

function FooterMenu({ color = 'inherit', slotName }: FooterMenuProps) {
  const { useAppMenu, getSetting } = useGlobal();

  const leftFooterMenu = useAppMenu('core', 'leftFooterMenu');
  const rightFooterMenu = useAppMenu('core', 'rightFooterMenu');
  const setting = getSetting();

  const copyright = setting?.core?.general?.site_copyright;

  const isSideSlot = slotName === 'subside';

  if (!leftFooterMenu && !rightFooterMenu) return null;

  return (
    <Root>
      <Content isSideSlot={isSideSlot}>
        <LeftMenu isSideSlot={isSideSlot}>
          {leftFooterMenu
            ? leftFooterMenu.items.map((item, index) => (
                <li key={index.toString()}>
                  <Link color={color} to={item.to} key={item.to}>
                    {item.label}
                  </Link>
                </li>
              ))
            : null}
        </LeftMenu>
        <RightMenu isSideSlot={isSideSlot}>
          {rightFooterMenu
            ? // eslint-disable-next-line no-confusing-arrow
              rightFooterMenu.items.map((item, index) => (
                <span key={`k${index}`}>
                  {item.name === 'copyright' ? (
                    `${copyright} ${new Date().getFullYear()}`
                  ) : (
                    <Link color={color} to={item.to} key={item.to}>
                      {item.label}
                    </Link>
                  )}
                </span>
              ))
            : null}
        </RightMenu>
      </Content>
    </Root>
  );
}

export default createBlock({
  extendBlock: FooterMenu,
  defaults: {
    title: 'Footer Menu'
  },
  overrides: {
    noHeader: true
  }
});
