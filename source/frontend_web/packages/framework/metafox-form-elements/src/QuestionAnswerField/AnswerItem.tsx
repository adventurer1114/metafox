import { useGlobal } from '@metafox/framework';
import { LineIcon } from '@metafox/ui';
import { CheckCircle, RadioButtonUnchecked } from '@mui/icons-material';
import { Radio, styled, TextField, Tooltip, Typography } from '@mui/material';
import React, { memo } from 'react';
import ErrorMessage from '../ErrorMessage';
import useStyles from './styles';

const name = 'AnswerItem';
const StyledIconClose = styled('div', {
  name: 'AnswerItem',
  slot: 'IconClose'
})(({ theme }) => ({
  cursor: 'pointer',
  display: 'flex',
  justifyContent: 'center',
  alignItems: 'center',
  width: theme.spacing(5),
  height: theme.spacing(5),
  margin: theme.spacing(0, 0.5),
  '& .ico': {
    fontSize: theme.mixins.pxToRem(18),
    color: theme.palette.text.hint
  }
}));

const StyledRadioGroup = styled('div', { name, slot: 'Radiogroup' })(
  ({ theme }) => ({
    display: 'flex',
    alignItems: 'center'
  })
);

const AnswerItem = ({
  lastElement,
  index,
  item,
  disabled,
  questionIndex,
  addMoreAnswer,
  handleDeleteAnswer,
  handleChangeAnswer,
  handleChangeCorrectAnswer,
  submitCount,
  error
}: any) => {
  const classes = useStyles();
  const { i18n } = useGlobal();
  const [touched, setTouched] = React.useState<boolean>(false);

  // don't show error until user leave text field
  const haveError = Boolean(error && (touched || submitCount));

  return (
    <>
      <div key={index} className={classes.answerItem}>
        <TextField
          className={classes.answerInput}
          onBlur={() => setTouched(true)}
          variant="outlined"
          margin="dense"
          disabled={disabled}
          required
          placeholder={i18n.formatMessage(
            { id: 'answer_index' },
            { index: index + 1 }
          )}
          defaultValue={item.answer}
          error={haveError}
          onChange={e => handleChangeAnswer(e, questionIndex, index)}
        />
        <div className={classes.endAdornmentButton}>
          <div className={classes.buttonWrapper}>
            <StyledIconClose onClick={handleDeleteAnswer}>
              <Tooltip
                title={i18n.formatMessage({ id: 'remove_answer' })}
                placement="top"
              >
                <LineIcon icon="ico-close" />
              </Tooltip>
            </StyledIconClose>
            <StyledRadioGroup>
              <Radio
                checked={Boolean(item.is_correct)}
                icon={<RadioButtonUnchecked />}
                checkedIcon={<CheckCircle />}
                disabled={disabled}
                name="checkedH"
                color="primary"
                onChange={e =>
                  handleChangeCorrectAnswer(e, questionIndex, index)
                }
                className={classes.radioButton}
              />
              <Typography
                variant="body1"
                color="text.hint"
                sx={{ display: { sm: 'block', xs: 'none' } }}
              >
                {i18n.formatMessage({ id: 'correct_answer' })}
              </Typography>
            </StyledRadioGroup>
          </div>
        </div>
      </div>
      {haveError ? <ErrorMessage error={error} /> : null}
    </>
  );
};

export default memo(AnswerItem);
