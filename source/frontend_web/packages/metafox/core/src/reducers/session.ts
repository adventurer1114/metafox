/**
 * @type: reducer
 * name: session
 */
import { createSlice } from '@reduxjs/toolkit';

const slice = createSlice({
  name: 'session',
  initialState: {
    user: {},
    loggedIn: false,
    accounts: []
  },
  reducers: {
    login(state, action) {
      state.user = action.payload;
      state.loggedIn = true;
    },
    update(state, action) {
      state.user = action.payload;
      state.loggedIn = true;
    },
    logout(state) {
      state.user = null;
      state.loggedIn = false;
    },
    setAccounts(state, action) {
      state.accounts = action.payload;
    }
  }
});

export const { login, logout } = slice.actions;

export default slice.reducer;
