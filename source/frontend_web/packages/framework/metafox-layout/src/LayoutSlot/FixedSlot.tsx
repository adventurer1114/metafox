/**
 * @type: ui
 * name: layout.liveFixedSlot
 * chunkName: boot
 */
import { useFixedRect, useGlobal } from '@metafox/framework';
import { Scrollbars } from '@metafox/scrollbars';
import { Divider } from '@mui/material';
import { camelCase } from 'lodash';
import React from 'react';
import ScrollProvider from '../ScrollProvider';
import { LayoutSlotProps } from '../types';
import {
  Slot,
  SlotContent,
  SlotStage,
  StickyBlock,
  StickyContent,
  StickyHeader
} from './StyledSlot';

export default function FixedSlotContainer(props: LayoutSlotProps) {
  const wrapper = React.useRef<HTMLDivElement>();
  const { jsxBackend, useLayout } = useGlobal();
  const { blocks } = useLayout();
  const fixedRect = useFixedRect(wrapper);
  const scrollRef = React.useRef();
  const [hasDivider, setHasDivider] = React.useState<boolean>(false);

  const {
    slotName,
    showEmpty = true,
    flexWeight,
    xs,
    layoutEditMode,
    elements,
    disabledScroll,
    contentStyle,
    rootStyle,
    stageStyle
  } = props;

  const items =
    elements ??
    blocks
      .filter(item => item.slotName === slotName)
      .sort((a, b) => a.order - b.order);

  if (!items.length && !showEmpty) {
    return null;
  }

  const isLiveEdit = layoutEditMode & 1;
  const enableFix = 767 < window.outerWidth;
  const freeze = items.filter(c => c.props.freeze).length;

  const useFlex = '0' < flexWeight || !xs;

  // eslint-disable-next-line react-hooks/rules-of-hooks
  React.useEffect(() => {
    if (freeze <= 0 || !scrollRef.current) return;

    const ele: HTMLDivElement = scrollRef.current;
    const initialScrollRefPosition: number = ele.getBoundingClientRect().top;

    let isSetBorder: boolean = false;

    const onScroll = () => {
      const currentScrollRefPosition: number =
        ele.scrollTop + initialScrollRefPosition;

      if (currentScrollRefPosition > initialScrollRefPosition) {
        if (!isSetBorder) {
          isSetBorder = true;
          setHasDivider(true);
        }
      } else if (isSetBorder) {
        isSetBorder = false;
        setHasDivider(false);
      }
    };

    ele.addEventListener('scroll', onScroll);

    return () => {
      ele.removeEventListener('scroll', onScroll);
    };
  }, [freeze]);

  return (
    <Slot
      item
      {...rootStyle}
      flexWeight={flexWeight}
      useFlex={useFlex}
      xs={useFlex ? undefined : xs}
      data-testid={camelCase(`LayoutSlot_${slotName}`)}
    >
      <SlotStage liveEdit={Boolean(isLiveEdit)} {...stageStyle}>
        <div ref={wrapper}>
          <SlotContent
            style={fixedRect.style}
            fixed={Boolean(enableFix && fixedRect.width)}
            {...contentStyle}
          >
            {0 < freeze ? (
              <StickyBlock>
                <StickyHeader>
                  {jsxBackend.render(items, c => c.props.freeze)}
                </StickyHeader>
                {hasDivider ? <Divider variant={'middle'} /> : null}
                <StickyContent>
                  {disabledScroll ? (
                    jsxBackend.render(items)
                  ) : (
                    <ScrollProvider scrollRef={scrollRef}>
                      <Scrollbars autoHide scrollRef={scrollRef}>
                        {jsxBackend.render(items, c => !c.props.freeze)}
                      </Scrollbars>
                    </ScrollProvider>
                  )}
                </StickyContent>
              </StickyBlock>
            ) : disabledScroll ? (
              jsxBackend.render(items)
            ) : (
              <ScrollProvider scrollRef={scrollRef}>
                <Scrollbars autoHide scrollRef={scrollRef}>
                  {jsxBackend.render(items)}
                </Scrollbars>
              </ScrollProvider>
            )}
          </SlotContent>
        </div>
      </SlotStage>
    </Slot>
  );
}
