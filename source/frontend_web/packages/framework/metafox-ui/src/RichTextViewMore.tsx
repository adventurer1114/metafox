import { useGlobal } from '@metafox/framework';
import { styled, Box } from '@mui/material';
import { alpha } from '@mui/system/colorManipulator';
import React from 'react';

const ToggleViewMoreBtn = styled('span', {
  name: 'ToggleViewMoreBtn',
  slot: 'Root'
})(({ theme }) => ({
  color: theme.palette.text.primary,
  marginTop: theme.spacing(1),
  fontWeight: theme.typography.fontWeightBold,
  display: 'inline-flex',
  fontSize: theme.mixins.pxToRem(13),
  lineHeight: 1,
  ':hover': {
    textDecoration: 'underline'
  }
}));

const Wrapper = styled(Box, {
  name: 'Wrapper',
  slot: 'Root'
})(({ theme }) => ({
  display: 'block',
  overflow: 'hidden',
  position: 'relative'
}));
const ShadowContent = styled('div', {
  name: 'ShadowContent',
  slot: 'Root'
})(({ theme }) => ({
  display: 'flex',
  height: '58px',
  background: `linear-gradient(to top, ${
    theme.palette.background.paper
  } 30%, ${alpha(theme.palette.background.paper, 0.8)} 70%, ${alpha(
    theme.palette.background.paper,
    0.9
  )} 100%)`,
  position: 'absolute',
  left: 0,
  right: 0,
  bottom: 0
}));

type RichTextViewMoreProps = {
  component?: React.ElementType;
  children?: React.ReactNode;
  textViewMore?: string;
  textViewLess?: string;
  defaultShowFull?: boolean;
  maxHeight?: string;
};

const RichTextViewMore = (props: RichTextViewMoreProps) => {
  const {
    component: AsComponent = 'div',
    children,
    textViewMore = 'view_more',
    textViewLess = 'view_less',
    defaultShowFull = false,
    maxHeight = '500px'
  } = props;

  const { i18n } = useGlobal();
  const [isFull, setIsFull] = React.useState(defaultShowFull);
  const [enable, setEnable] = React.useState(false);
  const ref = React.useRef<HTMLDivElement>();
  const checkEnable = React.useCallback(() => {
    if (ref.current && ref.current.scrollHeight > ref.current.clientHeight) {
      setEnable(true);
    }
  }, []);

  React.useEffect(() => {
    if (ref.current.scrollHeight === 0) {
      // delay when some case cannot get height of ele
      setTimeout(() => {
        checkEnable();
      }, 1000);
    } else {
      checkEnable();
    }
  }, []);

  if (!children) return null;

  const heightLimit = isFull ? 'auto' : maxHeight;

  return (
    <AsComponent>
      <Wrapper ref={ref} sx={{ maxHeight: heightLimit }}>
        {children}
        {enable && !isFull ? <ShadowContent /> : null}
      </Wrapper>
      {enable ? (
        <ToggleViewMoreBtn onClick={() => setIsFull(!isFull)} role="button">
          {i18n.formatMessage({ id: isFull ? textViewLess : textViewMore })}
        </ToggleViewMoreBtn>
      ) : null}
    </AsComponent>
  );
};

export default RichTextViewMore;
