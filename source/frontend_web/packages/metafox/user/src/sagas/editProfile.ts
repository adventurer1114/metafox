/**
 * @type: saga
 * name: user/editProfile
 */

import { getGlobalContext, getItem, ItemLocalAction } from '@metafox/framework';
import { takeEvery } from 'redux-saga/effects';

function* editProfile(action: ItemLocalAction) {
  const {
    payload: { identity }
  } = action;

  const item = yield* getItem(identity);

  const { navigate } = yield* getGlobalContext();

  navigate(`/user/${item.id}/profile`);
}

const sagas = [takeEvery('user/editProfile', editProfile)];

export default sagas;

// 5-2019
// 2 nam kinh nghiem
// react-native
// react-navigation
// redux + thunk, redux-saga,
// react + state
// animation
// native: ObjectC, Swift, Java.. [reviewed]

// es6.
// let, const, var ...
// arrow:
// promise.all
// generator *
// typescript:
// eslint

// react-native
// useLayoutEffect
// useRef()

// useContext()

// redux-saga

// image:
// 400pxXx400px => iphone12,
// 400px400px

// Styling React.

// Image

// WebSocket
// offline first

// SVG (SVG)
