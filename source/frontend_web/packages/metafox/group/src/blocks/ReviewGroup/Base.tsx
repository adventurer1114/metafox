import { BlockViewProps, useGlobal } from '@metafox/framework';
import { Block, BlockContent } from '@metafox/layout';
import { groupPreviewChanged } from '@metafox/group';
import { Container, LineIcon } from '@metafox/ui';
import { Box, Button, styled } from '@mui/material';
import { get, isEmpty } from 'lodash';
import React, { useState } from 'react';
import StatusComposer from '@metafox/feed/blocks/StatusComposer/Base';

const Wrapper = styled('div')({
  display: 'block',
  '& .overViewBottom': {
    marginTop: 16,
    display: 'flex'
  }
});

const ProfileHeaderContainer = styled(Container)({
  padding: 0
});

const UserInfoContainer = styled('div')(({ theme }) => ({
  backgroundColor: theme.mixins.backgroundColor('paper'),
  display: 'flex',
  justifyContent: 'space-between',
  alignItems: 'flex-start',
  padding: theme.spacing(2, 2, 3),
  [theme.breakpoints.down('sm')]: {
    flexFlow: 'column',
    width: '100%',
    alignItems: 'center'
  }
}));

const UserInfo = styled(Box)(({ theme }) => ({
  [theme.breakpoints.down('sm')]: {
    flexFlow: 'column',
    width: '100%',
    alignItems: 'center',
    marginBottom: theme.spacing(2)
  }
}));

const Title = styled('h1')(({ theme }) => ({
  fontWeight: 'bold',
  fontSize: theme.mixins.pxToRem(32),
  color: theme.palette.text.primary,
  margin: 0,
  padding: 0,
  wordWrap: 'break-word',
  wordBreak: 'break-word',
  [theme.breakpoints.down('sm')]: {
    textAlign: 'center'
  }
}));

const Summary = styled('div')(({ theme }) => ({
  color: theme.palette.text.secondary,
  fontSize: theme.mixins.pxToRem(18),
  paddingTop: theme.spacing(1),
  [theme.breakpoints.down('sm')]: {
    textAlign: 'center'
  }
}));

const ProfileHeaderBottom = styled('div')(({ theme }) => ({
  backgroundColor: theme.mixins.backgroundColor('paper'),
  borderTop: 'solid 1px',
  borderTopColor: theme.palette.border?.secondary,
  display: 'flex',
  justifyContent: 'space-between',
  alignItems: 'center',
  borderBottomLeftRadius: '8px',
  borderBottomRightRadius: '8px',
  overflow: 'hidden'
}));

const WrapperMenu = styled('div')(({ theme }) => ({
  display: 'flex',
  maxWidth: '55%',
  flexGrow: 1,
  [theme.breakpoints.down('sm')]: {
    width: 'auto',
    maxWidth: '100%'
  }
}));

const ProfileMenu = styled('div')({
  flex: 1,
  minWidth: 0
});

const TabItem = styled('div')(({ theme }) => ({
  minHeight: 40,
  borderBottom: '2px solid transparent',
  marginRight: theme.spacing(2),
  padding: theme.spacing(1, 2),
  height: '100%',
  display: 'inline-block',
  alignItems: 'center',
  justifyContent: 'center',
  float: 'left',
  textDecoration: 'none',
  textTransform: 'uppercase',
  fontSize: theme.mixins.pxToRem(15),
  fontWeight: 'bold',
  color: theme.palette.text.secondary,
  '&:hover': {
    textDecoration: 'none'
  }
}));

const ActionButtons = styled('div')(({ theme }) => ({
  display: 'flex',
  paddingRight: theme.spacing(2),
  '& button': {
    marginLeft: theme.spacing(1),
    textTransform: 'capitalize',
    fontWeight: 'bold',
    whiteSpace: 'nowrap',
    borderRadius: theme.spacing(0.5),
    fontSize: theme.mixins.pxToRem(13),
    padding: theme.spacing(0.5, 1.25),
    minWidth: theme.spacing(4),
    height: theme.spacing(4),
    '& .ico': {
      fontSize: theme.mixins.pxToRem(13)
    }
  }
}));

const StatuscomposerContainer = styled('div')(({ theme }) => ({
  flex: 2,
  backgroundColor: theme.mixins.backgroundColor('paper'),
  marginRight: theme.spacing(2),
  padding: theme.spacing(2),
  borderRadius: theme.spacing(1),
  pointerEvents: 'none'
}));

const AboutGroup = styled('div')(({ theme }) => ({
  flex: 1,
  alignItems: 'start',
  backgroundColor: theme.mixins.backgroundColor('paper'),
  borderRadius: theme.spacing(1),
  padding: theme.spacing(2)
}));

const TitleAbout = styled('div')(({ theme }) => ({
  fontWeight: 'bold',
  fontSize: theme.mixins.pxToRem(18),
  color: theme.palette.text.primary,
  margin: 0,
  padding: 0,
  wordWrap: 'break-word',
  wordBreak: 'break-word',
  [theme.breakpoints.down('sm')]: {
    textAlign: 'center'
  }
}));

const SummaryAbout = styled('div')(({ theme }) => ({
  color: theme.palette.text.secondary,
  fontSize: theme.mixins.pxToRem(14),
  paddingTop: theme.spacing(1),
  [theme.breakpoints.down('sm')]: {
    textAlign: 'center'
  }
}));

export default function GroupProfileHeaderView({ blockProps }: BlockViewProps) {
  const { ItemActionMenu, i18n, assetUrl, eventCenter, ProfileHeaderCover } =
    useGlobal();
  const [groupPrivacy, setGroupPrivacy] = useState(
    i18n.formatMessage({ id: 'group_privacy' })
  );
  const [title, setTitle] = useState(i18n.formatMessage({ id: 'group_name' }));

  React.useEffect(() => {
    const token = eventCenter.on(groupPreviewChanged, data => {
      const values = get(data, 'values');
      const req_method_options = get(
        data,
        'schema.elements.content.elements.basic.elements.reg_method.options'
      );

      if (isEmpty(values) || !req_method_options) return;

      const privacy = req_method_options[values?.reg_method]?.label;
      const name = values.name;

      privacy && setGroupPrivacy(privacy);
      setTitle(name || i18n.formatMessage({ id: 'group_name' }));
    });

    return () => eventCenter.off(groupPreviewChanged, token);
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  const menuItems = [
    { label: i18n.formatMessage({ id: 'overview' }) },
    { label: i18n.formatMessage({ id: 'about' }) },
    { label: i18n.formatMessage({ id: 'member' }) },
    { label: i18n.formatMessage({ id: 'event' }) }
  ];

  const cover = assetUrl('group.cover_no_image');

  return (
    <Block>
      <BlockContent>
        <div>
          <Wrapper>
            <Box>
              <ProfileHeaderCover
                image={cover}
                alt={'photo'}
                left={0}
                top={0}
                canEdit={false}
              />
              <div>
                <ProfileHeaderContainer maxWidth="md" disableGutters>
                  <UserInfoContainer>
                    <UserInfo
                      display="flex"
                      justifyContent="space-between"
                      alignItems="flex-start"
                    >
                      <div>
                        <Title>{title}</Title>
                        <Summary>
                          {groupPrivacy} ·{' '}
                          {i18n.formatMessage(
                            { id: 'value_member' },
                            { value: 1 }
                          )}
                        </Summary>
                      </div>
                    </UserInfo>
                  </UserInfoContainer>
                </ProfileHeaderContainer>

                <ProfileHeaderContainer maxWidth="md" disableGutters>
                  <ProfileHeaderBottom>
                    <WrapperMenu>
                      <ProfileMenu>
                        {menuItems.map((item, index) => (
                          <TabItem key={index.toString()}>{item.label}</TabItem>
                        ))}
                      </ProfileMenu>
                    </WrapperMenu>
                    <ActionButtons>
                      <ItemActionMenu
                        id="actionMenu"
                        label="ActionMenu"
                        handleAction={() => {}}
                        items={[]}
                        control={
                          <Button variant="outlined" size="large">
                            <LineIcon icon={'ico-dottedmore-o'} />
                          </Button>
                        }
                      />
                    </ActionButtons>
                  </ProfileHeaderBottom>
                </ProfileHeaderContainer>
                <Container
                  maxWidth="md"
                  disableGutters
                  className="overViewBottom"
                >
                  <StatuscomposerContainer>
                    <StatusComposer />
                  </StatuscomposerContainer>
                  <AboutGroup>
                    <TitleAbout>
                      {i18n.formatMessage({ id: 'about' })}
                    </TitleAbout>
                    <SummaryAbout>
                      {groupPrivacy} ·{' '}
                      {i18n.formatMessage({ id: 'value_member' }, { value: 1 })}
                    </SummaryAbout>
                  </AboutGroup>
                </Container>
              </div>
            </Box>
          </Wrapper>
        </div>
      </BlockContent>
    </Block>
  );
}
