import { PhotoItemProps } from '@metafox/photo/types';
import { Image, ItemView } from '@metafox/ui';
import { getImageSrc } from '@metafox/utils';
import { CircularProgress, styled } from '@mui/material';
import React from 'react';
import useStyles from './styles';

const LoadingStyled = styled('div', { name: 'LoadingStyled' })(({ theme }) => ({
  position: 'absolute',
  height: '100%',
  width: '100%',
  display: 'flex',
  justifyContent: 'center',
  alignItems: 'center',
  color: theme.palette.background.paper,
  zIndex: 1,
  '&:before': {
    content: '""',
    position: 'absolute',
    top: 0,
    bottom: 0,
    left: 0,
    right: 0,
    backgroundColor: theme.palette.action.active
  }
}));

const PhotoChooseCoverItem = ({
  item,
  identity,
  itemProps,
  wrapAs,
  wrapProps
}: PhotoItemProps) => {
  const classes = useStyles();
  const [loading, setLoading] = React.useState<boolean>(false);

  if (!item) return null;

  const cover = getImageSrc(item.image);
  const { onSuccess, close } = itemProps;

  const handleChoose = () => {
    setLoading(true);
    close && close();
    onSuccess && onSuccess(item);
  };

  return (
    <ItemView
      wrapAs={wrapAs}
      wrapProps={wrapProps}
      testid={`${item.resource_name}`}
      data-eid={identity}
    >
      <div className={classes.root}>
        {loading && (
          <LoadingStyled data-testid="loadingIndicator">
            <CircularProgress color="inherit" />
          </LoadingStyled>
        )}
        <div onClick={handleChoose}>
          <Image src={cover} aspectRatio={'43'} alt={item.title} />
        </div>
      </div>
    </ItemView>
  );
};

export default PhotoChooseCoverItem;
