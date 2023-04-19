/**
 * @type: formElement
 * name: form.element.Slider
 */
import Slider from '@mui/material/Slider';
import { useField } from 'formik';
import React from 'react';
import { FormFieldProps } from '@metafox/form';

const SliderField = ({ name }: FormFieldProps) => {
  const [field] = useField(name ?? 'SliderField');
  const getAriaValueText = (value: number): string => `${value} years`;

  return (
    <Slider
      defaultValue={30}
      getAriaValueText={getAriaValueText}
      aria-labelledby="discrete-slider"
      valueLabelDisplay="auto"
      step={10}
      marks
      min={10}
      max={110}
      onChange={field.onChange}
      onBlur={field.onBlur}
    />
  );
};

export default SliderField;
