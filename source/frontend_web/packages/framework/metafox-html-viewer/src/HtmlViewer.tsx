import React from 'react';
import ReactHtmlParser from 'react-html-parser';
import default_transform from './transform';
import { HtmlComponentProps } from './types';

function nl2br(text: string): string {
  return `${text}`.replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1<br/>$2');
}

const HtmlComponent = ({
  html,
  decodeEntities = true,
  transform = default_transform,
  component: Wrap = React.Fragment,
  disableNl2br,
  preprocessNodes
}: HtmlComponentProps) => (
  <Wrap>
    {html
      ? ReactHtmlParser(disableNl2br ? html : nl2br(html), {
          decodeEntities,
          transform,
          preprocessNodes
        })
      : null}
  </Wrap>
);

export default HtmlComponent;
