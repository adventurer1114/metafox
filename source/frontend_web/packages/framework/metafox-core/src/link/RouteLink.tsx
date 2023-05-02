import { useGlobal } from '@metafox/framework';
import { LinkProps as MuiLinkProps } from '@mui/material';
import { isString } from 'lodash';
import * as React from 'react';
import { Link, LinkProps as RouteLinkProps } from 'react-router-dom';
import { isExternalLink } from '@metafox/utils';
import ExternalLink from './ExternalLink';

const IsUrlReg = /^(http|https)?:?\/\//s;

export type LinkProps = MuiLinkProps &
  RouteLinkProps & {
    asModal?: boolean;
    asChildPage?: boolean;
    keepScroll?: boolean;
    hoverCard?: boolean | string;
    onClick?: (evt: React.SyntheticEvent<HTMLElement>) => void;
    aliasPath?: string;
    href?: string;
  };

export default React.forwardRef<HTMLAnchorElement, LinkProps>(
  (
    {
      to,
      asModal,
      hoverCard,
      keepScroll,
      asChildPage,
      aliasPath,
      resetModal,
      href,
      ...rest
    }: LinkProps,
    ref
  ) => {
    const mn = useGlobal();
    const [isClicking, setIsClicking] = React.useState(false);
    let state;

    if (asModal) {
      state = { asModal, keepScroll, aliasPath, resetModal };
    } else if (asChildPage) {
      state = { asChildPage, keepScroll, aliasPath };
    } else if (aliasPath) {
      state = { keepScroll, aliasPath };
    }

    if (isString(hoverCard) || (hoverCard && to)) {
      rest['data-popover'] = isString(hoverCard) ? hoverCard : to;
      rest.onMouseLeave = mn.popoverBackend?.onLeaveAnchor;
      rest.onMouseEnter = !isClicking ? mn.popoverBackend?.onEnterAnchor : null;
      rest.onClick = e => {
        setIsClicking(true);
        mn.popoverBackend?.onLeaveAnchor(e);
      };
    }

    href = href ?? (isString(to) ? to : to?.pathname);

    if (href && isExternalLink(href)) {
      return <ExternalLink {...rest} to={to} href={href} />;
    }

    // some case url same domain but startWith http
    if (!to || IsUrlReg.test(href)) {
      // eslint-disable-next-line jsx-a11y/anchor-has-content
      return <a href={href} {...rest} />;
    }

    return <Link to={to} ref={ref} state={state} {...rest} />;
  }
);
