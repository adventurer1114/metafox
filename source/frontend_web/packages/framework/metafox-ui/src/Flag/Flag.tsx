import { useGlobal } from '@metafox/framework';
import clsx from 'clsx';
import React from 'react';
import LineIcon from '../LineIcon';
import useStyles from './Flag.styles';

export interface FlagProps {
  type?: 'is_featured' | 'is_sponsor' | 'is_pending' | 'is_expires';
  color?: 'primary' | 'white' | 'yellow';
  variant?: 'feedView' | 'itemView' | 'detailView' | 'text';
  className?: string;
  'data-testid': string;
  value?: boolean;
  text?: string;
}

function Flag({
  type,
  color = 'primary',
  variant = 'feedView',
  'data-testid': testid,
  value,
  className,
  text
}: FlagProps) {
  const classes = useStyles();
  const { i18n } = useGlobal();

  if (!value) return null;

  let label = {
    icon: '',
    title: ''
  };
  switch (type) {
    case 'is_featured':
      label = {
        icon: 'ico-diamond',
        title: 'featured'
      };
      break;
    case 'is_sponsor':
      label = {
        icon: 'ico-sponsor',
        title: 'sponsored'
      };
      break;
    case 'is_pending':
      label = {
        icon: 'ico-clock-o',
        title: 'pending'
      };
      break;
    case 'is_expires':
      label = {
        icon: '',
        title: text
      };
      break;
    default:
  }

  return (
    <span className={clsx(classes.root, className, classes[variant])}>
      <span className={clsx(classes.item, classes[color])} data-testid={testid}>
        <span className={`${classes.icon}`}>
          <LineIcon icon={label.icon} />
        </span>
        <span className={clsx(classes.text, classes[type])}>
          {i18n.formatMessage({ id: label.title })}
        </span>
      </span>
    </span>
  );
}

export default Flag;
