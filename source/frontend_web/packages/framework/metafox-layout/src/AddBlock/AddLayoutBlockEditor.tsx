/**
 * @type: dialog
 * name: layout.addLayoutBlockDialog
 */

import { useGlobal } from '@metafox/framework';
import { Dialog, DialogContent, DialogTitle } from '@metafox/dialog';
import { escapeRegExp, randomId, sleep } from '@metafox/utils';
import { Autocomplete, Box, TextField } from '@mui/material';
import { styled } from '@mui/material/styles';
import React from 'react';
import ItemView, { BlockItemShape } from './ItemView';
import { uniq } from 'lodash';

const Tag = styled(Box, {
  name: 'Editor',
  slot: 'Tag'
})(({ theme }) => ({
  display: 'inline-block',
  color: theme.palette.primary.main,
  cursor: 'pointer',
  fontSize: theme.typography.body1.fontSize,
  background: '#2682d511',
  margin: '2px',
  padding: '0 4px',
  borderRadius: '4px'
}));

export default function AddLayoutBlockEditor(props) {
  const { parentBlockId, slotName, pageName, pageSize } = props;
  const { useDialog, layoutBackend, dispatch, i18n } = useGlobal();
  const { dialogProps, closeDialog } = useDialog();
  const [keyword, setKeyword] = React.useState<string>('');
  const allBlocks = layoutBackend.getAllBlockView();
  // getSetting('app.env') == 'local' for configuration.
  const experiment = process.env.NODE_ENV === 'development';
  const admincp = process.env.MFOX_BUILD_TYPE === 'admincp';

  const keywords: string[] = React.useMemo(() => {
    const arr = [];

    Object.values(allBlocks)
      .filter(x => experiment || !x.experiment) // only development mode allow experiment
      .filter(x => (!admincp && !x.admincp) || (admincp && x.admincp)) // only admincp area allow admincp content
      .forEach(({ keywords }) => {
        if (!keywords) return;

        keywords
          .split(',')
          .map(x => x.trim().toLowerCase())
          .forEach(x => arr.push(x));
      });

    return uniq(arr).filter(Boolean).sort();
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  const onItemSelected = (blockName: string) => {
    sleep(150).then(() => {
      const blockId = randomId();
      const block = {
        blockId,
        parentBlockId,
        pageName,
        slotName,
        blockName,
        pageSize
      };
      layoutBackend.addBlock(block);

      dispatch({ type: '@layout/editBlock', payload: block });
    });
    closeDialog();
  };

  const items: BlockItemShape[] = React.useMemo(
    () =>
      Object.values(allBlocks)
        .map((x: any) => ({ ...x }))
        .filter(x => experiment || !x.experiment) // only development mode allow experiment
        .filter(x => (!admincp && !x.admincp) || (admincp && x.admincp)) // only admincp area allow admincp content
        .filter(Boolean),
    // eslint-disable-next-line react-hooks/exhaustive-deps
    []
  );

  const filteredItems = React.useMemo(() => {
    if (!keyword) return items;

    const reg = new RegExp(escapeRegExp(keyword), 'i');

    return items.filter(x => {
      return (
        reg.test(x.keywords) || reg.test(x.title) || reg.test(x.description)
      );
    });
  }, [items, keyword]);

  return (
    <Dialog {...dialogProps} maxWidth="md" fullWidth>
      <DialogTitle id="alert-dialog-title">
        {i18n.formatMessage({ id: 'create_layout_block' })}
      </DialogTitle>
      <DialogContent sx={{ height: '50vh' }}>
        <Box sx={{ display: 'flex', flexDirection: 'row' }}>
          <Box sx={{ width: 220, mr: 2 }}>
            <Autocomplete<string, false, false, true>
              sx={{ mb: 2 }}
              freeSolo
              options={keywords}
              getOptionLabel={option => option}
              onChange={(evt: any, newValue: string | null) => {
                setKeyword(newValue);
              }}
              value={keyword}
              renderInput={params => (
                <TextField
                  {...params}
                  hiddenLabel
                  autoFocus
                  fullWidth
                  size="small"
                  variant="outlined"
                  placeholder={i18n.formatMessage({ id: 'search' })}
                />
              )}
            />
            {keywords.map(x => {
              return (
                <Tag key={x} onClick={() => setKeyword(x)}>
                  {x}
                </Tag>
              );
            })}
          </Box>
          <Box sx={{ flex: 1 }}>
            {filteredItems.map((item, index) => (
              <ItemView
                key={index.toString()}
                item={item}
                onItemSelected={onItemSelected}
                onKeyWorkClicked={setKeyword}
              />
            ))}
          </Box>
        </Box>
      </DialogContent>
    </Dialog>
  );
}
