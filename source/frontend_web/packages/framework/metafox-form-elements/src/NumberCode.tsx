/**
 * @type: formElement
 * name: form.element.NumberCode
 * chunkName: formBasic
 */

import React from 'react';
import { FormFieldProps } from '@metafox/form';
import { Box, FormControl, styled } from '@mui/material';
import { useField } from 'formik';
import ErrorMessage from './ErrorMessage';
import { camelCase } from 'lodash';

const RootStyled = styled(Box)(({ theme }) => ({
  display: 'flex',
  justifyContent: 'center',
  alignItems: 'center',
  margin: theme.spacing(1.5, 0, 2.5, 0)
}));

const Wrapper = styled(Box)(({ theme }) => ({
  display: 'flex',
  flexDirection: 'column'
}));

const WrapperError = styled(Box)(({ theme }) => ({
  minHeight: theme.spacing(3.25)
}));

const ItemInput = styled(Box)(({ theme }) => ({
  border: theme.mixins.border('secondary'),
  ...(theme.palette.mode === 'light' && {
    borderColor: 'rgba(0, 0, 0, 0.2)'
  }),
  width: '48px',
  height: '54px',
  display: 'flex',
  alignItems: 'center',
  justifyContent: 'center',
  fontSize: '32px',
  position: 'relative',
  marginRight: theme.spacing(1),
  borderRadius: theme.spacing(0.5),
  '&.last-item': {
    marginRight: 0
  }
}));

const InputStyled = styled('input')(({ theme }) => ({
  position: 'absolute',
  border: 'none',
  fontSize: '32px',
  textAlign: 'center',
  outline: 'none',
  backgroundColor: 'transparent'
}));

const Shadows = styled('input')(({ theme }) => ({
  position: 'absolute',
  left: '0',
  top: '0',
  bottom: '0',
  right: '0',
  border: theme.mixins.border('primary'),
  borderRadius: theme.spacing(0.5),
  backgroundColor: 'transparent',
  width: '48px',
  height: '54px'
}));

const NumberCode = ({ config, name, formik }: FormFieldProps) => {
  const [field, meta, { setValue, setTouched }] = useField(name ?? 'TextField');
  const [text, setText] = React.useState('');
  const [focused, setFocused] = React.useState(false);
  const values = text.split('');
  const { number = 6, type = 'text' } = config;
  const refInput = React.useRef<any>();

  const CODE_LENGTH = new Array(number).fill(0);

  const handleClick = () => {
    if (refInput.current) {
      refInput.current.focus();
    }
  };

  const handleFocus = () => {
    setFocused(true);
  };

  const handleBlur = e => {
    setFocused(false);
    field.onBlur(e);
    setTouched(true);
  };

  const handleChange = e => {
    const _value = e.target.value;

    if (text.length >= CODE_LENGTH.length) return null;

    const data = (text + _value).slice(0, CODE_LENGTH.length);

    setText(data);

    let result: any = data ? data : undefined;

    if (type === 'number' && result) {
      result = parseInt(result);
    }

    setValue(result);
  };

  const handleKeyUp = e => {
    if (e.key === 'Backspace') {
      const result = text.slice(0, text.length - 1);

      setText(result);
      setValue(result);
    }
  };

  const selectedIndex =
    values.length < CODE_LENGTH.length ? values.length : CODE_LENGTH.length - 1;

  const hideInput = !(values.length < CODE_LENGTH.length);

  const firstAndLast = values.length === 0 ? 0 : 8;

  React.useEffect(() => {
    setText('');
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  const haveError = Boolean(meta.error && (focused || meta.touched));

  return (
    <FormControl data-testid={camelCase(`field ${name}`)}>
      <RootStyled>
        <Wrapper onClick={handleClick}>
          <Box sx={{ display: 'flex', position: 'relative' }}>
            {CODE_LENGTH.map((v, index) => {
              const selected = values.length === index;
              const filled =
                values.length === CODE_LENGTH.length &&
                index === CODE_LENGTH.length - 1;

              return (
                <ItemInput
                  key={index}
                  className={CODE_LENGTH.length - 1 === index && 'last-item'}
                >
                  {values[index]}
                  {(selected || filled) && focused && <Shadows />}
                </ItemInput>
              );
            })}
            <InputStyled
              value=""
              ref={refInput}
              onFocus={handleFocus}
              onBlur={handleBlur}
              onChange={handleChange}
              onKeyUp={handleKeyUp}
              className="input"
              style={{
                width: '48px',
                height: '54px',
                top: '0px',
                bottom: '0px',
                left: `${selectedIndex * (48 + firstAndLast)}px`,
                opacity: hideInput ? 0 : 1
              }}
            />
          </Box>
          <WrapperError>
            {haveError ? <ErrorMessage error={meta.error} /> : null}
          </WrapperError>
        </Wrapper>
      </RootStyled>
    </FormControl>
  );
};

export default NumberCode;
