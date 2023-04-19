import { RefOf } from '@metafox/framework';
import { Box } from '@mui/material';
import React from 'react';

type Props = {
  children: React.ReactNode;
  onContentSize?: (width: number) => void;
  minHeight?: number;
};

function GridMain(
  { children, onContentSize, minHeight }: Props,
  ref: RefOf<HTMLDivElement>
) {
  const handleResize = React.useCallback(() => {
    // eslint-disable-next-line no-console

    if (!ref.current) return;

    if (onContentSize) onContentSize(ref.current.scrollWidth);
  }, [onContentSize, ref]);

  React.useEffect(() => {
    // detect re-calculator when toggle menu side bar make change width
    const observer = new ResizeObserver(entries => {
      if (!ref?.current) return;

      if (onContentSize) onContentSize(undefined);

      setImmediate(() => {
        handleResize();
      });
    });
    observer.observe(ref?.current);

    return () => ref?.current && observer.unobserve(ref?.current);
  }, []);

  React.useEffect(() => {
    window.addEventListener('resize', handleResize);

    handleResize();

    return () => window.removeEventListener('resize', handleResize);
  }, [handleResize]);

  return (
    <Box
      sx={{ position: 'relative', overflowX: 'auto', width: '100%', minHeight }}
      component="div"
      ref={ref}
    >
      {children}
    </Box>
  );
}

export default React.forwardRef(GridMain);
