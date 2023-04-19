import { BlockViewProps, RouteLink, useGlobal } from '@metafox/framework';
import { Block, BlockContent, BlockHeader } from '@metafox/layout';
import { LineIcon } from '@metafox/ui';
import { getImageSrc } from '@metafox/utils';
import { Avatar } from '@mui/material';
import * as React from 'react';
import useStyles from './styles';

export type Props = BlockViewProps;

export default function StoryBlock({
  title,
  blockProps = { variant: 'contained' }
}: Props) {
  const classes = useStyles();
  const { useSession, i18n } = useGlobal();
  const { user: authUser } = useSession();

  if (!authUser) {
    return null;
  }

  return (
    <Block>
      <BlockHeader title={title} />
      <BlockContent>
        <div className={classes.storyWrapper}>
          <RouteLink
            role="button"
            to={`/${authUser.user_name}`}
            className={`${classes.storyItem} ${classes.addStory}`}
          >
            <Avatar
              alt=""
              src={getImageSrc(authUser.avatar, '240')}
              sx={{ width: 76, height: 76 }}
              className={classes.avatar}
            />
            <LineIcon icon="ico-plus" className={classes.iconPlus} />
            <span className={classes.userName}>
              {i18n.formatMessage({ id: 'add' })}
            </span>
          </RouteLink>
          {[1, 2, 3, 4, 5, 6, 7].map((item, index) => (
            <RouteLink
              key={index.toString()}
              role="button"
              to={`/${authUser.user_name}`}
              className={`${classes.storyItem} ${
                3 === item ? classes.active : ''
              }`}
            >
              <Avatar
                alt=""
                sx={{ width: 76, height: 76 }}
                src={getImageSrc(authUser.avatar, '240')}
                className={classes.avatar}
              />
              <span className={classes.userName}>{authUser?.first_name}</span>
            </RouteLink>
          ))}
        </div>
      </BlockContent>
    </Block>
  );
}
