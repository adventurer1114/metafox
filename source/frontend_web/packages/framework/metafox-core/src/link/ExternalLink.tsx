import React from 'react';
import { useGlobal } from '@metafox/framework';
import { LinkProps as RouteLinkProps } from 'react-router-dom';

export type ExternalLinkProps = RouteLinkProps & {
  children?: React.ReactNode;
  href?: string;
};

const ExternalLink = (props: ExternalLinkProps) => {
  const { href, children, ...rest } = props;
  const { dialogBackend, i18n, getSetting } = useGlobal();
  const warnOnExternal = getSetting('core.spam.warning_on_external_links');

  const handleClick = (
    e: React.SyntheticEvent<HTMLSpanElement, MouseEvent>
  ) => {
    if (!warnOnExternal) return;

    e.preventDefault();
    dialogBackend
      .confirm({
        message: i18n.formatMessage(
          {
            id: 'this_link_leads_to_an_untrusted_site_are_you_sure_you_want_to_proceed'
          },
          { link: href }
        )
      })
      .then(ok => {
        if (ok) {
          const newTab = window.open();
          newTab.location.href = href;
        }
      });
  };

  return (
    <a {...rest} href={href} onClick={handleClick}>
      {children}
    </a>
  );
};

export default ExternalLink;
