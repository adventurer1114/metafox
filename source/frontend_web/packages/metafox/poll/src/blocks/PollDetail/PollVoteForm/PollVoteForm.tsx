import { useGlobal } from '@metafox/framework';
import {
  Button,
  Checkbox,
  FormControlLabel,
  FormGroup,
  FormHelperText,
  Radio,
  RadioGroup
} from '@mui/material';
import produce from 'immer';
import React, { memo } from 'react';
import useStyles from './PollVoteForm.styles';

interface Props {
  voteAgain: any;
  pollId: any;
  identity: any;
  displayAnswers: any;
  isClosed: any;
  hideAnswers: any;
  isMultiple: any;
  answers: any;
  LIMIT_ANSWER_DISPLAY: any;
  isPending: any;
  canVote: any;
  canVoteAgain: any;
  isEmbedInFeed: any;
  setVoteAgain: any;
  setShowPoll: any;
  setIsCanViewVoteAnswer: any;
  setIsCanViewResult: any;
  canViewResultAfter: any;
}

const CheckBoxGroupForm = ({
  displayAnswers,
  classes,
  isClosed,
  handleCheckboxChange,
  viewMore,
  hideAnswers
}: any) => (
  <FormGroup>
    {displayAnswers?.length > 0
      ? displayAnswers.map(i => (
          <FormControlLabel
            key={i.id.toString()}
            className={classes.answerItem}
            control={
              <Checkbox
                disabled={isClosed}
                color="primary"
                size="small"
                name={i.id.toString()}
                onChange={handleCheckboxChange}
                className={classes.radioAnswer}
              />
            }
            label={i.answer}
          />
        ))
      : null}
    {viewMore && hideAnswers?.length > 0
      ? hideAnswers.map(i => (
          <FormControlLabel
            key={i.id.toString()}
            className={classes.answerItem}
            control={
              <Checkbox
                disabled={isClosed}
                color="primary"
                size="small"
                name={i.id.toString()}
                onChange={handleCheckboxChange}
                className={classes.radioAnswer}
              />
            }
            label={i.answer}
          />
        ))
      : null}
  </FormGroup>
);

const RadioGroupForm = ({
  classes,
  value,
  handleRadioChange,
  displayAnswers,
  isClosed,
  viewMore,
  hideAnswers
}: any) => (
  <RadioGroup
    className={classes.answerWrapper}
    value={value}
    onChange={handleRadioChange}
  >
    {displayAnswers?.length > 0 &&
      displayAnswers.map(i => (
        <FormControlLabel
          className={classes.answerItem}
          key={i.id.toString()}
          value={i.id.toString()}
          control={
            <Radio
              className={classes.radioAnswer}
              color="primary"
              size="small"
              disabled={isClosed}
            />
          }
          label={i.answer}
        />
      ))}
    {viewMore && hideAnswers?.length > 0
      ? hideAnswers.map(i => (
          <FormControlLabel
            className={classes.answerItem}
            key={i.id.toString()}
            value={i.id.toString()}
            control={
              <Radio
                className={classes.radioAnswer}
                color="primary"
                size="small"
                disabled={isClosed}
              />
            }
            label={i.answer}
          />
        ))
      : null}
  </RadioGroup>
);

function PollVoteForm({
  voteAgain,
  pollId,
  identity,
  displayAnswers,
  isClosed,
  hideAnswers,
  isMultiple,
  answers,
  LIMIT_ANSWER_DISPLAY,
  isPending,
  canVote,
  canVoteAgain,
  isEmbedInFeed,
  setVoteAgain,
  setShowPoll,
  setIsCanViewVoteAnswer,
  canViewResultAfter,
  setIsCanViewResult
}: Props) {
  const classes = useStyles();
  const { i18n, dispatch, useSession } = useGlobal();
  const { loggedIn } = useSession();

  const [viewMore, setViewMore] = React.useState(false);
  const [helperText, setHelperText] = React.useState<string>('');
  const [value, setValue] = React.useState<string>('');
  const [result, setResult] = React.useState<number[]>([]);

  const handleRadioChange = (event: React.ChangeEvent<HTMLInputElement>) => {
    const { value } = event.target;
    const answerId = parseInt(value);
    setValue(value);
    setResult([answerId]);
    setHelperText('');
  };

  const handleCheckboxChange = (event: React.ChangeEvent<HTMLInputElement>) => {
    const { checked, name } = event.target;
    const answerId = parseInt(name);

    if (checked) {
      setHelperText('');
      setResult(prev =>
        produce(prev, draft => {
          const index = draft.findIndex(item => item === answerId);

          if (index < 0) draft.push(answerId);
        })
      );
    } else {
      setResult(prev =>
        produce(prev, draft => {
          const index = draft.findIndex(item => item === answerId);

          if (index > -1) draft.splice(index, 1);
        })
      );
    }
  };

  const cancelVoteAgain = () => {
    setVoteAgain(false);
    setShowPoll(false);
  };

  const handleSubmit = (event: React.FormEvent<HTMLFormElement>) => {
    event.preventDefault();

    if (!result.length) {
      setHelperText(i18n.formatMessage({ id: 'please_select_an_option' }));

      return null;
    }

    dispatch({
      type: 'submitPoll',
      payload: {
        voteAgain,
        pollId,
        answers: result,
        identity
      },
      meta: {
        onSuccess: data => {
          setShowPoll(false);
          setIsCanViewVoteAnswer(data?.can_view_result_after_vote);
          setIsCanViewResult(data?.can_view_result);
        }
      }
    });
  };

  return (
    <form onSubmit={handleSubmit}>
      {!isMultiple ? (
        <RadioGroupForm
          classes={classes}
          value={value}
          handleRadioChange={handleRadioChange}
          displayAnswers={displayAnswers}
          isClosed={isClosed}
          viewMore={viewMore}
          hideAnswers={hideAnswers}
        />
      ) : (
        <CheckBoxGroupForm
          displayAnswers={displayAnswers}
          classes={classes}
          isClosed={isClosed}
          handleCheckboxChange={handleCheckboxChange}
          viewMore={viewMore}
          hideAnswers={hideAnswers}
        />
      )}
      {answers.length > LIMIT_ANSWER_DISPLAY ? (
        <span
          className={classes.btnToggle}
          onClick={() => setViewMore(!viewMore)}
          role="button"
        >
          {i18n.formatMessage({ id: viewMore ? 'view_less' : 'view_more' })}
        </span>
      ) : null}
      {helperText ? <FormHelperText>{helperText}</FormHelperText> : null}
      {(canVote || canVoteAgain) && (
        <div className={classes.buttonWrapper}>
          {!isPending && loggedIn && (
            <div className={classes.button}>
              <Button
                type="submit"
                variant="outlined"
                disabled={isClosed}
                size={isEmbedInFeed ? 'smaller' : 'medium'}
                color="primary"
                sx={{ fontWeight: 'bold' }}
              >
                {i18n.formatMessage({ id: 'vote' })}
              </Button>
            </div>
          )}
          {voteAgain && (
            <div className={classes.cancelButton}>
              <Button
                variant="outlined"
                disabled={isClosed}
                size={isEmbedInFeed ? 'smaller' : 'medium'}
                color="primary"
                sx={{ fontWeight: 'bold' }}
                onClick={cancelVoteAgain}
              >
                {i18n.formatMessage({ id: 'cancel' })}
              </Button>
            </div>
          )}
        </div>
      )}
    </form>
  );
}

export default memo(PollVoteForm);
