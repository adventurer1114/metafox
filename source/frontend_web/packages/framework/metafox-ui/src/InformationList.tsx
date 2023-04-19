import { styled } from '@mui/material';
import { isEmpty } from 'lodash';
import * as React from 'react';
import LineIcon from './LineIcon';

type Props = {
  values?: InfoItem[];
};
type InfoItem = {
  icon: string;
  info: any;
  description: any;
  status: any;
  class_style: any;
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

const InformationList = (props: Props) => {
  const { values } = props;

  if (!values || !(values?.length > 0)) return null;

  return (
    <div>
      {values.map((item, index) => {
        return !isEmpty(item.info) ? (
          <StyledItem key={`${index}`}>
            <AboutIcon icon={item.icon} />
            <StyledLabel>
              {item.info}
              {item.status && (
                <StyledLabel className={item?.class_style}>
                  {item.status}
                </StyledLabel>
              )}
              {item.description && <div>{item.description}</div>}
            </StyledLabel>
          </StyledItem>
        ) : null;
      })}
    </div>
  );
};

export default InformationList;
