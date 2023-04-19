/**
 * @type: ui
 * name: layout.section.list_info
 */

import HtmlViewer from '@metafox/html-viewer';
import { LineIcon } from '@metafox/ui';
import { Box, styled } from '@mui/material';
import React from 'react';

// eslint-disable-next-line @typescript-eslint/no-unused-vars
const StyledTextInfo = styled('div')(({ theme }) => ({
  fontSize: theme.mixins.pxToRem(15),
  color: theme.palette.text.primary
}));

const BoxWrapper = styled(Box)(({ theme }) => ({
  '&:not(:last-child)': {
    marginBottom: theme.spacing(3)
  }
}));

const Label = styled(Box)(({ theme }) => ({
  fontSize: theme.mixins.pxToRem(13),
  color: theme.palette.grey[500],
  marginBottom: theme.spacing(2)
}));

const Value = styled(Box)(({ theme }) => ({
  fontSize: theme.mixins.pxToRem(15),
  color:
    theme.palette.mode === 'dark'
      ? theme.palette.grey[100]
      : theme.palette.grey['A700']
}));

const Field = ({ field }) => {
  if (field.value === null) return null;

  return (
    <BoxWrapper>
      {field.icon || field.label ? (
        <Label>
          {field.icon ? <LineIcon icon={field.icon} /> : field.label}
        </Label>
      ) : null}
      {field.value ? (
        <Value>
          <HtmlViewer html={field.value} />
        </Value>
      ) : null}
    </BoxWrapper>
  );
};

const Section = ({ section }) => {
  return Object.values(section.fields).map((field, index) => (
    <Field field={field} key={index.toString()} />
  ));
};

export default Section;
