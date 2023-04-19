import { useAnnouncements } from '@metafox/announcement/hooks';
import { AnnouncementItemShape } from '@metafox/announcement/types';
import { ListViewBlockProps, useGlobal } from '@metafox/framework';
import { Block, BlockContent, BlockHeader, BlockTitle } from '@metafox/layout';
import { LineIcon } from '@metafox/ui';
import { IconButton, styled } from '@mui/material';
import React from 'react';
import Slider from 'react-slick';
import 'slick-carousel/slick/slick-theme.css';
import 'slick-carousel/slick/slick.css';

export type Props = ListViewBlockProps;

const NavButtonWrapper = styled('div', {
  name: 'NavButtonWrapper',
  shouldForwardProp: prop => prop !== 'themeId'
})<{ themeId: string }>(({ theme, themeId }) => ({
  color: theme.palette.primary.main,
  display: 'flex',
  alignItems: 'center',

  '& > span': {
    fontSize: theme.mixins.pxToRem(18),
    cursor: 'pointer',
    '&:hover': {
      color: theme.palette.primary.dark
    }
  }
}));
const SlideCount = styled('div', {
  name: 'SlideCount'
})(({ theme }) => ({
  fontSize: theme.mixins.pxToRem(15),
  color: theme.palette.text.secondary,
  marginLeft: theme.spacing(1.5),
  marginRight: theme.spacing(1.5)
}));
const UnreadButton = styled('span', {
  shouldForwardProp: props => props !== 'isRead'
})<{ isRead?: boolean }>(({ theme, isRead }) => ({
  right: theme.spacing(2),
  bottom: theme.spacing(2),
  position: 'absolute',
  color: theme.palette.primary.main,
  borderBottom: 'solid 1px transparent',
  userSelect: 'none',
  '&:hover': {
    borderBottom: `solid 1px ${theme.palette.primary.main}`,
    cursor: 'pointer'
  },
  ...(isRead && {
    color: theme.palette.grey['400'],
    '&:hover': {}
  })
}));

const BlockContentWrapper = styled(BlockContent)(({ theme }) => ({
  '& .slick-list': {
    marginBottom: theme.spacing(2.5)
  }
}));

export default function AnnouncementListing({ title }: Props) {
  const PrevButtonRef = React.useRef<HTMLInputElement>();
  const { jsxBackend, useTheme } = useGlobal();
  const [open, setOpen] = React.useState(true);
  const { i18n, dispatch } = useGlobal();
  const [numberRemoveRef, setNumberRemoveRef] = React.useState(0);
  const [currentSlideState, setCurrentSlideState] = React.useState(0);
  const [loading, setLoading] = React.useState(false);
  const [meta, setMeta] = React.useState<any>();
  const { direction } = useTheme() || {};

  React.useEffect(() => {
    setLoading(true);
    dispatch({
      type: 'announcement/getAnnouncementList',
      meta: {
        onSuccess: ({ data, meta }) => {
          setLoading(!data);
          setMeta(meta);
        }
      }
    });
  }, []);

  const announcements = useAnnouncements();
  const data = Object.values(announcements).filter(
    announcement => !(announcement.can_be_closed && announcement.is_read)
  );

  const handlePrevClick = currentSlide => {
    if (currentSlide === 0) return;

    PrevButtonRef.current.click();
  };

  const ItemView = jsxBackend.get('announcement.itemView.mainCard');
  const ItemViewLoading = jsxBackend.get(
    'announcement.itemView.mainCard.skeleton'
  );

  const item: AnnouncementItemShape = data[currentSlideState];

  const onMarkAsRead = () => {
    if (!item) return;

    if (item.is_read) return;

    dispatch({
      type: 'announcement/markAsRead',
      payload: item.id,
      meta: {
        onSuccess: () => {
          setNumberRemoveRef(prev => prev + 1);
        }
      }
    });
  };

  const NextArrow = props => {
    const { onClick, currentSlide } = props;
    const { usePreference } = useGlobal();
    const { themeId } = usePreference();

    return (
      <NavButtonWrapper themeId={themeId} dir={direction}>
        <LineIcon
          icon={direction === 'rtl' ? 'ico-angle-right' : 'ico-angle-left'}
          onClick={() => handlePrevClick(currentSlide)}
        />
        <SlideCount>
          {currentSlide + 1}/{meta?.total - numberRemoveRef}
        </SlideCount>
        <LineIcon
          icon={direction === 'rtl' ? 'ico-angle-left' : 'ico-angle-right'}
          onClick={onClick}
        />
      </NavButtonWrapper>
    );
  };

  const PrevArrow = props => {
    const { onClick } = props;

    return (
      <div
        style={{ display: 'none' }}
        onClick={onClick}
        ref={PrevButtonRef}
      ></div>
    );
  };

  const beforeChange = (oldIndex, newIndex) => {
    setCurrentSlideState(newIndex);
    const urlNextPage = meta?.links[meta?.links?.length - 1]?.url;

    if (
      urlNextPage &&
      meta?.total > meta?.per_page &&
      meta?.total > data.length &&
      newIndex + 3 === data.length - 1
    ) {
      dispatch({
        type: 'announcement/getAnnouncementPage',
        payload: urlNextPage
      });
    }
  };

  const sliderSetting = {
    dots: false,
    infinite: true,
    speed: 500,
    slidesToShow: 1,
    slidesToScroll: 1,
    useTransform: false,
    adaptiveHeight: true,
    nextArrow: <NextArrow />,
    prevArrow: <PrevArrow />,
    beforeChange
  };

  const toggleOpen = React.useCallback(() => setOpen(open => !open), []);

  if (loading) {
    return (
      <Block>
        <BlockHeader>
          <BlockTitle>{i18n.formatMessage({ id: title })}</BlockTitle>
        </BlockHeader>
        <BlockContent>
          <ItemViewLoading />
        </BlockContent>
      </Block>
    );
  }

  if (!data.length) return null;

  return (
    <Block>
      <BlockHeader>
        <BlockTitle>{i18n.formatMessage({ id: title })}</BlockTitle>
        <IconButton size="small" color="default" onClick={toggleOpen}>
          <LineIcon icon={open ? 'ico-angle-up' : 'ico-angle-down'} />
        </IconButton>
      </BlockHeader>
      {open ? (
        <BlockContentWrapper>
          <Slider {...sliderSetting}>
            {data.length &&
              data.map(item => (
                <div dir={direction} key={item.id.toString()}>
                  {React.createElement(ItemView, {
                    identity: `announcement.entities.announcement.${item.id}`
                  })}
                </div>
              ))}
          </Slider>
          <UnreadButton isRead={item?.is_read} onClick={onMarkAsRead}>
            {item?.is_read
              ? i18n.formatMessage({ id: 'i_have_read_this' })
              : i18n.formatMessage({ id: 'mark_as_read' })}
          </UnreadButton>
        </BlockContentWrapper>
      ) : null}
    </Block>
  );
}
