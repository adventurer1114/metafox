import { useGlobal } from '@metafox/framework';
import { TruncateText, TruncateTextProps } from '@metafox/ui';
import { styled } from '@mui/material/styles';
import React from 'react';

const ToggleViewMoreBtn = styled('span', {
  name: 'ToggleViewMoreBtn',
  slot: 'Root'
})(({ theme }) => ({
  color: theme.palette.text.primary,
  marginTop: theme.spacing(1.5),
  fontWeight: theme.typography.fontWeightBold,
  display: 'inline-flex',
  fontSize: theme.mixins.pxToRem(13),
  lineHeight: 1,
  ':hover': {
    textDecoration: 'underline'
  }
}));

type TruncateViewMoreProps = {
  component?: React.ElementType;
  truncateProps: TruncateTextProps;
  children?: React.ReactNode;
  textViewMore?: string;
  textViewLess?: string;
  defaultShowFull?: boolean;
};

const TruncateViewMore = (props: TruncateViewMoreProps) => {
  const {
    component: AsComponent = 'div',
    truncateProps,
    children,
    textViewMore = 'view_more',
    textViewLess = 'view_less',
    defaultShowFull = false
  } = props;

  const { i18n } = useGlobal();
  const [isFull, setIsFull] = React.useState(defaultShowFull);
  const [enable, setEnable] = React.useState(false);
  const [initScroll, setInitScroll] = React.useState<Boolean>(true);
  const ref = React.useRef<HTMLDivElement>();

  const checkEnable = React.useCallback(() => {
    // 2px is minor threshold some browser
    if (
      ref.current &&
      ref.current.scrollHeight - ref.current.clientHeight > 2
    ) {
      setEnable(true);
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [ref?.current?.scrollHeight, ref?.current?.clientHeight]);

  React.useEffect(() => {
    checkEnable();
    setInitScroll(false);
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [ref?.current?.scrollHeight]);

  if (!children) return null;

  const { style } = truncateProps;

  return (
    <AsComponent>
      <TruncateText
        {...truncateProps}
        showFull={isFull}
        ref={ref}
        style={style}
        sx={{ overflow: initScroll ? 'scroll' : 'hidden' }}
      >
        {children}
      </TruncateText>
      {enable ? (
        <ToggleViewMoreBtn onClick={() => setIsFull(!isFull)} role="button">
          {i18n.formatMessage({ id: isFull ? textViewLess : textViewMore })}
        </ToggleViewMoreBtn>
      ) : null}
    </AsComponent>
  );
};

export default TruncateViewMore;
