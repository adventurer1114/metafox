/**
 * @type: formElement
 * name: form.element.Answer
 */

import { LineIcon } from '@metafox/ui';
import {
  Button,
  Checkbox,
  Radio,
  styled,
  TextField,
  Typography
} from '@mui/material';
import React, { useEffect, useMemo, useState } from 'react';

const StyledAnswer = styled('div', { name: 'StyledAnswer' })(({ theme }) => ({
  display: 'flex',
  alignItems: 'center',
  padding: theme.spacing(1, 0),
  borderBottom: '1px solid',
  borderBottomColor: theme.palette.divider
}));

const ContentButton = styled('div', {
  name: 'StyledAnswer',
  slot: 'ContentButton'
})(() => ({
  width: '100%',
  cursor: 'pointer'
}));

enum TypeQuestion {
  FreeAnswer,
  Select,
  CheckBox
}

interface AnswerProps {
  type: number;
  value?: string;
  onRemove: () => void;
  onChange: React.ChangeEventHandler<HTMLTextAreaElement | HTMLInputElement>;
  onBlur: React.FocusEventHandler<HTMLTextAreaElement | HTMLInputElement>;
}

const Answer = ({
  type,
  value: valueProp,
  onRemove,
  onChange,
  onBlur
}: AnswerProps) => {
  const [isEdit, setIsEdit] = useState(!valueProp);
  const [value, setValue] = useState('');
  const [hide, setHide] = useState(type === TypeQuestion.FreeAnswer);

  useEffect(() => {
    setValue(valueProp);
  }, [valueProp]);

  const handleBlur: React.FocusEventHandler<
    HTMLTextAreaElement | HTMLInputElement
  > = e => {
    setIsEdit(false);
    onBlur(e);
  };

  const handleChange: React.ChangeEventHandler<
    HTMLInputElement | HTMLTextAreaElement
  > = e => {
    const { value } = e.target;

    setValue(value);
    onChange && onChange(e);
  };

  const handleRemove = () => {
    setHide(true);
    onRemove && onRemove();
  };

  const contentInput = (
    <TextField
      size="small"
      fullWidth
      autoFocus
      id="outlined-basic"
      value={value}
      variant="outlined"
      onChange={handleChange}
      onBlur={handleBlur}
      inputProps={{
        maxLength: 255
      }}
    />
  );

  const contentButton = (
    <ContentButton onClick={() => setIsEdit(true)}>
      <Typography variant="h6">{value}</Typography>
    </ContentButton>
  );

  const contentRender = isEdit ? contentInput : contentButton;

  const iconRender = useMemo(() => {
    if (type === TypeQuestion.CheckBox) return <Checkbox disabled checked />;

    if (type === TypeQuestion.Select) return <Radio disabled />;
  }, [type]);

  if (hide) return null;

  return (
    <StyledAnswer>
      {iconRender}
      {contentRender}
      <Button onClick={handleRemove}>
        <LineIcon icon=" ico-close" />
      </Button>
    </StyledAnswer>
  );
};

export default Answer;
