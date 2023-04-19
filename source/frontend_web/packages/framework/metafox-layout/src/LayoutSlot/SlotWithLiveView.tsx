/**
 * @type: ui
 * name: layout.slot.LiveEdit
 * chunkName: boot
 */
import { useGlobal } from '@metafox/framework';
import { camelCase } from 'lodash';
import React from 'react';
import { LayoutSlotProps } from '../types';
import { Slot, SlotContent, SlotStage } from './StyledSlot';

export default function SlotWithLiveView(props: LayoutSlotProps) {
  const { jsxBackend, useLayout, ErrorBoundary } = useGlobal();
  const { blocks } = useLayout();
  const {
    slotName,
    showEmpty = true,
    flexWeight,
    elements,
    xs,
    rootStyle,
    stageStyle,
    contentStyle
  } = props;

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
      zeroMinWidth
      xs={useFlex ? undefined : xs}
      flexWeight={flexWeight}
      useFlex={useFlex}
      data-testid={camelCase(`LayoutSlot_${slotName}`)}
      {...rootStyle}
    >
      <SlotStage {...stageStyle}>
        <SlotContent {...contentStyle}>
          {items?.map((item, index) => (
            <ErrorBoundary
              key={index.toString()}
              errorPage={item.props.errorPage}
            >
              {jsxBackend.render(item)}
            </ErrorBoundary>
          ))}
        </SlotContent>
      </SlotStage>
    </Slot>
  );
}
