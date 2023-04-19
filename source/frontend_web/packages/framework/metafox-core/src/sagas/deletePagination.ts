import { put } from 'redux-saga/effects';
import { deletePaginationAction } from '../actions';

export default function* deletePagination(identity: string) {
  yield put(deletePaginationAction(identity));
}
