import { useIsMobile } from '@metafox/framework';
import clsx from 'clsx';
import React, { useEffect, useRef, useState } from 'react';
import useStyles from './styles';

interface State {
  isSticky: boolean;
  styledPseudoSticky?: React.CSSProperties;
  styledIsSticky?: React.CSSProperties;
}

export default function StickyBar(props: any) {
  const { children, className } = props;
  const classes = useStyles();
  const isMobile = useIsMobile();

  const [sticky, setSticky] = useState<State>({
    isSticky: false
  });
  const outerRef = useRef<HTMLDivElement>();
  const innerRef = useRef<HTMLDivElement>();
  const pseudoRef = useRef<HTMLDivElement>();

  useEffect(() => {
    if (!outerRef.current || !pseudoRef.current) return;

    const elementPosition: number = pseudoRef.current.offsetTop;
    const elementHeight: number = innerRef.current.offsetHeight;

    const elementWrapperRect = outerRef.current.getBoundingClientRect();
    const elementWrapperPositionLeft = elementWrapperRect.left;
    const elementWrapperPositionRight =
      document.body.clientWidth - elementWrapperRect.right;

    let lastSticky: boolean = false;

    const onScroll = () => {
      if (!elementPosition || !elementHeight) return;

      const winScroll = window.pageYOffset;

      if (elementPosition < winScroll) {
        if (!lastSticky) {
          lastSticky = true;
          // sticky
          setSticky({
            isSticky: true,
            styledPseudoSticky: { height: elementHeight },
            styledIsSticky: {
              left: isMobile ? 0 : elementWrapperPositionLeft,
              right: isMobile ? 0 : elementWrapperPositionRight
            }
          });
        }
      } else if (lastSticky) {
        lastSticky = false;
        setSticky({
          isSticky: false,
          styledPseudoSticky: { height: 0 },
          styledIsSticky: undefined
        });
      }
    };

    window.addEventListener('scroll', onScroll);

    return () => {
      window.removeEventListener('scroll', onScroll);
    };
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  return (
    <div ref={outerRef}>
      <div
        data-pseudo="pseudo-sticky"
        ref={pseudoRef}
        style={sticky.styledPseudoSticky}
      />
      <div
        ref={innerRef}
        className={clsx(sticky.isSticky ? classes.sticky : '', className)}
        style={sticky.styledIsSticky}
      >
        {children({ sticky: sticky.isSticky })}
      </div>
    </div>
  );
}
