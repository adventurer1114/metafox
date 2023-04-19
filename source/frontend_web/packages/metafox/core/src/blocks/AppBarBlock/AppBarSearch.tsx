import { SuggestionListHandle, useGlobal } from '@metafox/framework';
import { ClickOutsideListener, LineIcon } from '@metafox/ui';
import { InputBase } from '@mui/material';
import clsx from 'clsx';
import { trim } from 'lodash';
import * as React from 'react';
import useStyles from './GlobalSearchForm.style';
import RecentSearchList from './RecentSearchList';
import SuggestionList from './SuggestionList';

interface Props {
  openSearch?: any;
  minimize?: boolean;
  closeSearch?: () => void;
}

interface State {
  open: boolean;
  focus: boolean;
  text: string;
  menuOpened?: boolean;
  minimize: boolean;
}

export default function AppBarSearch({
  openSearch,
  minimize: isMinimized,
  closeSearch
}: Props) {
  const classes = useStyles();
  const {
    dispatch,
    usePageParams,
    i18n,
    eventCenter,
    navigate,
    location,
    getSetting
  } = useGlobal();
  const { q, pathname } = usePageParams();
  const [state, setState] = React.useState<State>({
    open: false,
    text: /^\/search/.test(pathname) ? q : '',
    focus: false,
    minimize: !!isMinimized
  });
  const appSearchSetting = getSetting('search');

  const placeholder = i18n.formatMessage({ id: 'search' });
  const listRef = React.useRef<SuggestionListHandle>();
  const inputRef = React.useRef<HTMLInputElement>();
  const containerRef = React.useRef<HTMLDivElement>();

  React.useEffect(() => {
    const token = eventCenter.on('minimizeGlobalSearchForm', () => {
      setState(prev => ({ ...prev, minimize: true }));
    });

    return () => {
      eventCenter.off('minimizeGlobalSearchForm', token);
    };
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  React.useEffect(() => {
    if (openSearch === true) {
      onFocus();
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [openSearch]);

  const handleSearch = React.useCallback(
    (query: string) => {
      if (query.startsWith('#') && query.substring(1)) {
        navigate(`/hashtag/search?q=${query.substring(1)}`);
      } else {
        navigate(`/search/?q=${query}`);
      }

      if (inputRef?.current) {
        inputRef.current.blur();
      }

      onBlur();
    },
    // eslint-disable-next-line react-hooks/exhaustive-deps
    []
  );

  const onKeyboardEscape = React.useCallback(() => {
    setState(prev => ({ ...prev, focus: false, open: false }));
  }, []);

  const onKeyboardArrowUp = React.useCallback(() => {
    if (listRef.current?.movePrev) {
      listRef.current.movePrev();
    }
  }, []);

  const onKeyboardArrowDown = React.useCallback(() => {
    if (listRef.current?.moveNext) {
      listRef.current.moveNext();
    }
  }, []);

  const onBlur = React.useCallback(() => {
    setState(prev => ({ ...prev, focus: false, open: false }));
  }, []);

  const onSubmit = React.useCallback((evt?: React.FormEvent) => {
    const x: string = inputRef.current?.value;

    if (x === '#') return;

    if (evt) {
      evt.preventDefault();
      evt.stopPropagation();
    }

    const trimmedQuery = trim(x);

    if (!trimmedQuery) {
      onBlur();
    }

    if (trimmedQuery) {
      dispatch({
        type: 'recentSearch/ADD',
        payload: { text: trimmedQuery }
      });
      handleSearch(trimmedQuery);
    }

    if (closeSearch) closeSearch();
    // eslint-disable-next-line
  }, []);

  const onQueryChanged = React.useCallback(
    (evt: React.ChangeEvent<{ value: string }>) => {
      const text = evt.currentTarget.value;
      setState(prev => ({ ...prev, text, selectedIndex: 0 }));
      dispatch({ type: 'suggestions/QUERY', payload: { text } });
    },
    // eslint-disable-next-line react-hooks/exhaustive-deps
    []
  );

  const onFocus = React.useCallback(() => {
    if (!state.open) setState(prev => ({ ...prev, focus: true, open: true }));

    dispatch({ type: 'suggestions/QUERY', payload: { text: state.text } });
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [state.open, state.text]);

  const handleClickAway = React.useCallback(() => {
    if (closeSearch) closeSearch();

    setState(prev => ({ ...prev, open: false, focus: false }));
  }, []);

  const onKeyboardEnter = React.useCallback(() => {
    onSubmit();
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  const cancelEvent = React.useCallback((evt: React.KeyboardEvent) => {
    evt.preventDefault();
    evt.stopPropagation();
  }, []);

  const onKeyDown = React.useCallback(
    (evt: React.KeyboardEvent) => {
      const code = evt?.keyCode;

      if (evt.metaKey || evt.ctrlKey) return;

      switch (code) {
        case 9:
          onKeyboardEscape();
          break;
        case 27: // escape
          onKeyboardEscape();
          cancelEvent(evt);
          break;
        case 13: // enter
          onKeyboardEnter();
          cancelEvent(evt);
          break;
        case 38: // arrow up
          onKeyboardArrowUp();
          cancelEvent(evt);
          break;
        case 40: // arrow down
          onKeyboardArrowDown();
          cancelEvent(evt);
          break;
        default:
          if (!state.open) {
            onFocus();
          }
      }
    },
    // eslint-disable-next-line react-hooks/exhaustive-deps
    [state.open]
  );

  React.useEffect(() => {
    if (!location.pathname.startsWith('/search')) {
      setState(prev => ({ ...prev, text: '', open: false }));
    }
  }, [location]);

  if (!appSearchSetting) return null;

  return (
    <ClickOutsideListener onClickAway={handleClickAway}>
      <div
        ref={containerRef}
        data-testid="formSearch"
        className={clsx(classes.root, state.open && classes.rootOpen)}
        role="search"
        id="globalSearchBox"
      >
        <form
          className={clsx(classes.form, state.open && classes.formFocused)}
          method="get"
          aria-expanded={!state.minimize}
          onSubmit={onSubmit}
        >
          <InputBase
            startAdornment={
              <LineIcon className={classes.searchIcon} icon={'ico-search-o'} />
            }
            placeholder={placeholder}
            classes={{
              root: classes.inputRoot,
              input: classes.inputInput
            }}
            autoComplete="off"
            value={state.text}
            name="search"
            inputProps={{
              'aria-label': 'search',
              'data-testid': 'searchBox',
              autoComplete: 'off',
              autoCapitalize: 'off'
            }}
            inputRef={inputRef}
            onFocus={onFocus}
            onChange={onQueryChanged}
            onKeyDown={onKeyDown}
          />
        </form>
        {state.open ? (
          <div className={clsx(classes.resultWrapper)}>
            {state.text ? (
              <SuggestionList
                ref={listRef}
                onSearch={handleSearch}
                classes={classes}
                text={state.text}
                isActionSearch={inputRef.current?.value !== '#'}
              />
            ) : (
              <RecentSearchList
                onSearch={handleSearch}
                ref={listRef}
                classes={classes}
              />
            )}
          </div>
        ) : null}
      </div>
    </ClickOutsideListener>
  );
}
