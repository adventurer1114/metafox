/**
 * @type: ui
 * name: layout.section.icon_list
 */
import { styled } from '@mui/material';
import { isEmpty } from 'lodash';
import * as React from 'react';
import { LineIcon } from '@metafox/ui';

interface InfoItem {
  icon: string;
  label: string;
  value: any;
  description: any;
  status: any;
}

interface Section {
  label: string;
  description?: string;
  fields?: Record<string, InfoItem>;
}

type Props = {
  section: Section;
};

const AboutIcon = styled(LineIcon, {
  name: 'AboutIcon'
})(({ theme }) => ({
  fontSize: theme.typography.fontSize,
  display: 'flex',
  paddingTop: theme.spacing(0.25),
  marginRight: theme.spacing(1),
  lineHeight: '18px'
}));

const StyledItem = styled('div')(({ theme }) => ({
  ...theme.typography.body1,
  color: theme.palette.text.secondary,
  display: 'flex',
  overflow: 'hidden',
  ':not(:last-child)': {
    marginBottom: theme.spacing(1.5)
  }
}));

const StyledLabel = styled('span')(({ theme }) => ({
  display: 'block',
  overflow: 'hidden',
  wordBreak: 'break-all',
  '& span': {
    display: 'initial',
    textTransform: 'capitalize',
    '&.success': {
      color: theme.palette.success.main
    },
    '&.warning': {
      color: theme.palette.warning.main
    },
    '&.error': {
      color: theme.palette.error.main
    }
  }
}));

const InformationList = ({ section }: Props) => {
  const { fields } = section;

  return (
    <div>
      {Object.values(fields).map((field, index) => {
        return !isEmpty(field.value) ? (
          <StyledItem key={`${index}`}>
            <AboutIcon icon={field.icon} />
            <StyledLabel>
              {field.value}
              {field.status && <StyledLabel>{field.status}</StyledLabel>}
              {field.description && <div>{field.description}</div>}
            </StyledLabel>
          </StyledItem>
        ) : null;
      })}
    </div>
  );
};

export default InformationList;
