/**
 * @type: ui
 * name: layout.liveStickySlot
 * chunkName: boot
 */
import { useGlobal, useScrollDirection } from '@metafox/framework';
import { camelCase } from 'lodash';
import React from 'react';
import { LayoutSlotProps } from '../types';
import { Slot, SlotContent, SlotStage } from './StyledSlot';

// Support Drag & Drop
export default function LayoutStickySlot(props: LayoutSlotProps) {
  const { jsxBackend, useLayout } = useGlobal();
  const { blocks } = useLayout();
  const {
    slotName,
    showEmpty = true,
    elements,
    flexWeight,
    xs,
    rootStyle,
    stageStyle,
    contentStyle
  } = props;

  const sideRef = React.useRef(null);
  const firstTop = React.useRef(0);
  const [styleEle, setStyleEle] = React.useState({
    styleSticky: {},
    stylePseudoSticky: {}
  });
  const scroll = useScrollDirection();
  const [isStickyStatic, setIsStickyStatic] = React.useState(false);
  const offsetSticky = 88;

  React.useEffect(() => {
    const winScroll = window.scrollY;
    const ele = sideRef.current;
    const bound = ele && ele.getBoundingClientRect();

    if (!bound) return;

    firstTop.current = bound.top + winScroll;
  }, []);

  React.useEffect(() => {
    const ele = sideRef.current;
    const winScroll = window.scrollY;

    if (!ele) return;

    const bound = ele && ele.getBoundingClientRect();
    const winHeight = window.innerHeight;
    const heightSide = ele.clientHeight;
    const top = winHeight - heightSide - offsetSticky; // spacing with bottom
    const bottom = winHeight - heightSide - offsetSticky; // spacing with top(header place)
    const heightPseudoDown = Math.max(
      winScroll > firstTop.current
        ? winScroll - firstTop.current + offsetSticky
        : 0,
      0
    );
    const heightPseudoUp = Math.max(
      winScroll + (winHeight - heightSide - firstTop.current - offsetSticky),
      0
    );

    if (bound.height <= winHeight) {
      setIsStickyStatic(true);

      return;
    }

    if ('down' === scroll) {
      if (offsetSticky - 10 < bound.top) {
        setStyleEle({
          ...styleEle,
          stylePseudoSticky: {
            height: heightPseudoDown
          },
          styleSticky: {
            top,
            bottom: ''
          }
        });
      } else {
        setStyleEle({
          ...styleEle,
          styleSticky: {
            top,
            bottom: ''
          }
        });
      }
    } else {
      if (bound.top + heightSide + offsetSticky <= winHeight) {
        setStyleEle({
          ...styleEle,
          stylePseudoSticky: {
            height: heightPseudoUp
          },
          styleSticky: {
            top: '',
            bottom
          }
        });
      } else {
        setStyleEle({
          ...styleEle,
          styleSticky: {
            top: '',
            bottom
          }
        });
      }
    }
  }, [scroll, styleEle]);

  const items =
    elements ??
    blocks
      .filter(item => item.slotName === slotName)
      .sort((a, b) => a.order - b.order);

  if (!items.length && !showEmpty) {
    return null;
  }

  const useFlex = '0' < flexWeight || !xs;

  return (
    <Slot
      item
      xs={useFlex ? undefined : xs}
      useFlex={useFlex}
      flexWeight={flexWeight}
      data-testid={camelCase(`LayoutSlot_${slotName}`)}
      {...rootStyle}
    >
      <div style={!isStickyStatic ? styleEle.stylePseudoSticky : {}}></div>
      <SlotStage
        ref={sideRef}
        style={!isStickyStatic ? styleEle.styleSticky : { top: offsetSticky }}
        sticky={isStickyStatic ? 'sideStickyStatic' : 'sideSticky'}
        {...stageStyle}
      >
        <SlotContent {...contentStyle}>{jsxBackend.render(items)}</SlotContent>
      </SlotStage>
    </Slot>
  );
}
