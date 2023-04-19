import { useGlobal } from '@metafox/framework';
import { Block, BlockContent, BlockHeader } from '@metafox/layout';
import { Card, ListItem, Switch, Typography } from '@mui/material';
import React, { useState } from 'react';
import { useDispatch } from 'react-redux';

const RuleMode = ({ title, identity, item, ...rest }: any) => {
  const { i18n } = useGlobal();
  const dispatch = useDispatch();

  const [value, setValue] = useState(!!item?.is_rule_confirmation);
  const [loadingSubmit, setLoadingSubmit] = useState(false);

  const handleChange = (checked: boolean) => {
    setLoadingSubmit(true);
    setValue(checked);

    dispatch({
      type: 'group/updateRuleConfirmation',
      payload: { identity, is_rule_confirmation: checked },
      meta: {
        onSuccess: () => setLoadingSubmit(false),
        onFailure: () => setLoadingSubmit(false)
      }
    });
  };

  return (
    <Block {...rest}>
      <BlockHeader title={title} />
      <Typography variant="body2" paragraph>
        {i18n.formatMessage({ id: 'group_rules_description' })}
      </Typography>
      <BlockContent>
        <Card sx={{ boxShadow: 'none' }}>
          <ListItem
            sx={{ py: 4 }}
            secondaryAction={
              <Switch
                onChange={(_, checked) => handleChange(checked)}
                checked={value}
                size="medium"
                color="primary"
                disabled={loadingSubmit}
              />
            }
          >
            <Typography component="div" variant="body1">
              {i18n.formatMessage({ id: 'enable_rule_mode' })}
            </Typography>
          </ListItem>
        </Card>
      </BlockContent>
    </Block>
  );
};

export default RuleMode;
