/**
 * @type: formElement
 * name: form.element.Container
 * chunkName: formBasic
 */
import { Element, FormFieldProps } from '@metafox/form';
import { Box, Typography } from '@mui/material';
import { styled } from '@mui/material/styles';
import clsx from 'clsx';
import { camelCase, map } from 'lodash';
import React from 'react';

const FormContainerRoot = styled(Box, {
  name: 'MuiFormContainer',
  slot: 'Root',
  shouldForwardProp: (prop: string) => !/isSidePlacement|horizontal/i.test(prop)
})<{ isSidePlacement?: boolean; horizontal?: boolean }>(
  ({ theme, isSidePlacement, horizontal }) => ({
    position: 'relative',
    ...(isSidePlacement && {
      paddingRight: theme.spacing(2)
    })
  })
);

const BoxWrapper = styled(Box, {
  name: 'BoxWrapper'
})(({ theme }) => ({
  '&.multiStep': {
    marginRight: 'auto',
    order: 1
  }
}));

const Header = ({ label, description, isMultiStep }) => {
  if (!label && !description) return null;

  return (
    <BoxWrapper sx={{ pt: 1 }} className={isMultiStep ? 'multiStep' : ''}>
      {label ? (
        <Typography component="h3" color="text.primary" variant="h5">
          {label}
        </Typography>
      ) : null}
      {description ? (
        <Typography component="p" color="text.secondary" variant="body2">
          {description}
        </Typography>
      ) : null}
    </BoxWrapper>
  );
};

const ContainerBody = styled(Box, {
  name: 'MuiFormContainer',
  slot: 'Body',
  shouldForwardProp: (prop: string) => !/horizontal/.test(prop)
})<{ horizontal: boolean }>(({ theme, horizontal }) => ({
  position: 'relative',
  ...(horizontal
    ? {
        display: 'flex',
        alignItems: 'center',
        flexWrap: 'wrap',
        '&>div': {
          paddingRight: theme.spacing(1)
        }
      }
    : {
        display: 'flex',
        flexDirection: 'column'
      })
}));

export default function Container({ formik, config }: FormFieldProps) {
  const {
    testid,
    description,
    isMultiStep,
    wrapAs: Wrapper,
    className,
    variant = 'vertical',
    label,
    elements,
    wrapperProps,
    sx,
    separator
  } = config;

  if (Wrapper) {
    const noSeparator = wrapperProps?.separator ? '' : 'noSeparator';

    return (
      <Wrapper className={noSeparator} sx={sx}>
        <Header
          isMultiStep={isMultiStep}
          label={label}
          description={description}
        />
        {map(elements, (config, key) => (
          <Element formik={formik} key={key.toString()} config={config} />
        ))}
      </Wrapper>
    );
  }

  return (
    <FormContainerRoot
      {...wrapperProps}
      data-testid={camelCase(testid)}
      className={clsx(className, separator)}
      sx={sx}
    >
      <Header
        isMultiStep={isMultiStep}
        label={label}
        description={description}
      />
      <ContainerBody
        horizontal={variant === 'horizontal'}
        className={clsx(className, separator)}
      >
        {map(elements, (config, key) => (
          <Element formik={formik} key={key.toString()} config={config} />
        ))}
      </ContainerBody>
    </FormContainerRoot>
  );
}
