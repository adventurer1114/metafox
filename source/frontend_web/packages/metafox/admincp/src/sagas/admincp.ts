/**
 * @type: saga
 * name: admincp
 * bundle: admincp
 */

import {
  AppResourceAction,
  getGlobalContext,
  GridMassAction,
  GridOrderAction,
  GridRowAction,
  handleActionConfirm,
  handleActionError,
  handleActionFeedback
} from '@metafox/framework';
import { get, isArray, isObject, isBoolean } from 'lodash';
import { takeEvery } from 'redux-saga/effects';
import qs from 'query-string';

/**
 * General active a single row in data grid
 */
function* activeRow({ payload, meta: { apiRef } }: GridRowAction) {
  const { id, row, action = 'active', reload, field = 'is_active' } = payload;
  const active = row[field] ? 0 : 1;

  try {
    const { apiClient, compactUrl } = yield* getGlobalContext();

    const dataSource =
      payload.dataSource ?? apiRef.current.config.actions[action];

    const ok = yield handleActionConfirm(dataSource as AppResourceAction);

    if (!ok) return;

    // check if row is dirty

    if (row._dirty) {
      if (process.env.NODE_ENV === 'development') {
        // eslint-disable-next-line no-console
        console.warn('The selected record is dirty');
      }
    }

    let newData: any = active;

    if (isBoolean(row[field])) {
      newData = active ? true : false;
    }

    // optimist update
    apiRef.current.patchRow(id, { [field]: newData, _dirty: true });

    const response = yield apiClient.request({
      url: compactUrl(dataSource.apiUrl, { id }),
      data: { active, id },
      method: dataSource.apiMethod || 'patch'
    });

    if (reload) {
      apiRef.current.refresh();
    } else {
      apiRef.current.patchRow(id, { _dirty: false });
    }

    yield* handleActionFeedback(response);
  } catch (error) {
    // revert data to previous state
    apiRef.current.patchRow(id, { [field]: active ? 0 : 1, _dirty: false });

    // alert error state
    yield* handleActionError(error);
  }
}

/**
 * General active a single row in data grid
 */
function* requestRow({ payload, meta: { apiRef } }: GridRowAction) {
  const { id, action = 'show' } = payload;

  try {
    const dataSource =
      payload.dataSource ?? apiRef.current.config.actions[action];

    const ok = yield handleActionConfirm(dataSource as AppResourceAction);

    if (!ok) return;

    const { apiClient, compactUrl } = yield* getGlobalContext();

    const response = yield apiClient.request({
      url: compactUrl(dataSource.apiUrl, { id }),
      data: { id },
      method: dataSource.apiMethod || 'get'
    });

    yield* handleActionFeedback(response);
  } catch (error) {
    // alert error state
    yield* handleActionError(error);
  }
}

/**
 * General active a single row in data grid
 */
function* removeRow({
  payload: { id, row, action, reload, dataSource, confirm },
  meta: { apiRef }
}: GridRowAction) {
  try {
    const { apiClient, compactUrl } = yield* getGlobalContext();

    dataSource = dataSource ?? apiRef.current.config.actions[action];

    const ok = yield handleActionConfirm({ confirm } as AppResourceAction);

    if (!ok) return;

    // optimist update
    apiRef.current.patchRow(id, { _dirty: true });

    const response = yield apiClient.request({
      url: compactUrl(dataSource.apiUrl, { id }),
      data: { id },
      method: dataSource.apiMethod || 'delete'
    });

    if (reload) {
      apiRef.current.refresh();
    } else {
      // solve _dirty state
      apiRef.current.removeRow(id);
    }

    yield* handleActionFeedback(response);
  } catch (error) {
    // revert data to previous state

    // alert error state
    yield* handleActionError(error);
  }
}

// batch active multiple row in data grid
function* batchActiveRow({
  type,
  payload: { id, dataSource, reload, action, field = 'is_active' },
  meta: { apiRef }
}: GridMassAction) {
  try {
    const active = type === 'row/batchActive' ? 1 : 0;

    const { apiClient, compactUrl } = yield* getGlobalContext();

    dataSource = dataSource ?? apiRef.current.config.actions[action];

    isArray(id)
      ? apiRef.current.patchRows(id, { [field]: active, _dirty: true })
      : apiRef.current.patchRow(id, { [field]: active, _dirty: true });

    const response = yield apiClient.request({
      url: compactUrl(dataSource.apiUrl, { id }),
      data: { active, id },
      method: dataSource.apiMethod || 'post'
    });

    // solve _dirty state

    if (reload) {
      apiRef.current.refresh();
    } else if (isArray(id)) {
      apiRef.current.patchRows(id, { [field]: active, _dirty: true });
    } else {
      apiRef.current.patchRow(id, { [field]: active, _dirty: true });
    }

    yield* handleActionFeedback(response);
  } catch (error) {
    yield* handleActionError(error);

    // revert previous state or reload
    apiRef.current.refresh();
  }
}

// batch active multiple row in data grid
function* batchEditRows({
  payload: { id, action, reload, field },
  meta: { apiRef }
}: GridMassAction) {
  try {
    const { apiClient, dialogBackend, compactUrl } = yield* getGlobalContext();

    const dataSource = apiRef.current.config.actions[action];

    const asFormDialog = !!(dataSource?.asFormDialog ?? true);

    const ok = yield handleActionConfirm(dataSource);

    if (!ok) return;

    if (asFormDialog) {
      const data = yield dialogBackend.present({
        component: 'core.dialog.RemoteForm',
        props: {
          dataSource,
          pageParams: { id }
        }
      });

      if (data) {
        // update or refresh
        if (reload) {
          apiRef.current.refresh();
        } else if (isArray(data)) {
          apiRef.current.patchMultiRows(data);
        } else {
          apiRef.current.patchRows(id, data);
        }

        // refresh current data
        // apiRef.current.refresh();
      }
    } else {
      const response = yield apiClient.request({
        url: compactUrl(dataSource.apiUrl, { id }),
        data: { ...dataSource.apiParams, id },
        method: dataSource.apiMethod || 'post'
      });

      const data = response?.data.data;

      // update or refresh
      if (reload) {
        apiRef.current.refresh();
      } else if (isArray(data)) {
        apiRef.current.refresh();
        apiRef.current.patchMultiRows(data);
      } else if (isObject(data)) {
        apiRef.current.patchRows(id, data);
      }

      yield* handleActionFeedback(response);
    }
  } catch (error) {
    yield* handleActionError(error);

    // revert previous state or reload
    apiRef.current.refresh();
  }
}

// batch active multiple row in data grid
function* batchRemoveRows({
  payload: { id, action, reload, field },
  meta: { apiRef }
}: GridMassAction) {
  try {
    const { apiClient, dialogBackend, compactUrl } = yield* getGlobalContext();

    const dataSource = apiRef.current.config.actions[action];

    const asFormDialog = dataSource?.asFormDialog ?? false;

    const ok = yield handleActionConfirm(dataSource);

    if (!ok) return;

    if (asFormDialog) {
      const data = yield dialogBackend.present({
        component: 'core.dialog.RemoteForm',
        props: {
          dataSource,
          pageParams: { id }
        }
      });

      if (reload) {
        apiRef.current.refresh();
      } else if (data) {
        apiRef.current.removeRows(id);
      }
    } else {
      const response = yield apiClient.request({
        url: compactUrl(dataSource.apiUrl, { id }),
        data: { ...dataSource.apiParams, id },
        method: dataSource.apiMethod || 'delete'
      });

      if (reload) {
        apiRef.current.refresh();
      } else {
        apiRef.current.removeRows(id);
      }

      yield* handleActionFeedback(response);
    }
  } catch (error) {
    yield* handleActionError(error);

    // revert previous state or reload
    apiRef.current.refresh();
  }
}

// edit a single row
function* editRow({
  payload: { id, action = 'editItem', row: item, reload, dialogProps },
  meta: { apiRef }
}: GridRowAction) {
  const { dialogBackend, compactData, compactUrl, apiClient } =
    yield* getGlobalContext();

  const dataSource = apiRef.current.config.actions[action];

  try {
    const asFormDialog = dataSource?.asFormDialog ?? true;

    const ok = yield handleActionConfirm(dataSource);

    if (!ok) return;

    if (asFormDialog) {
      const data = yield dialogBackend.present({
        component: 'core.dialog.RemoteForm',
        props: {
          ...dialogProps,
          dataSource,
          pageParams: { id }
        }
      });

      if (data) {
        if (reload) {
          apiRef.current.refresh();
        } else {
          apiRef.current.setRow(id, data);
        }
      }
    } else if (dataSource?.download) {
      const url = compactUrl(dataSource.pageUrl, { id });
      yield window.open(url);
    } else if (dataSource.pageUrl) {
      const url = compactUrl(dataSource.pageUrl, { id });
      const { navigate } = yield* getGlobalContext();
      navigate(url);
    } else if (dataSource.link) {
      const url = get(item, dataSource.link);

      if (!url) return;

      const { navigate } = yield* getGlobalContext();
      navigate(url);
    } else {
      const response = yield apiClient.request({
        url: compactUrl(dataSource.apiUrl, { id }),
        data: { id },
        params: compactData(dataSource.apiParams, { id }),
        method: dataSource.apiMethod || 'post'
      });

      yield* handleActionFeedback(response);

      if (reload) {
        apiRef.current.refresh();
      } else {
        apiRef.current.patchRow(id, response.data.data);
      }
    }
  } catch (error) {
    yield* handleActionError(error);
  }
}

// edit a single row
function* rowRedirect({
  payload: { id, to },
  meta: { apiRef }
}: GridRowAction) {
  const { navigate, compactUrl } = yield* getGlobalContext();

  const url = compactUrl(to, { id });

  navigate(url);
}

// edit a single row
function* addRow({
  payload: { id, dataSource, reload, action = 'addItem', dialogProps },
  meta: { apiRef }
}: GridRowAction) {
  try {
    const { dialogBackend, i18n } = yield* getGlobalContext();

    dataSource = dataSource ?? apiRef.current.config.actions[action];

    const data = yield dialogBackend.present({
      component: 'core.dialog.RemoteForm',
      props: {
        ...dialogProps,
        dataSource,
        pageParams: { id },
        confirmLeaveDialogWhenDirty: {
          message: i18n.formatMessage({
            id: 'the_change_you_made_will_not_be_saved'
          }),
          title: i18n.formatMessage({
            id: 'unsaved_changes'
          })
        }
      }
    });

    if (data) {
      // put data to current row
      apiRef.current.refresh();
    }
  } catch (error) {
    yield* handleActionError(error);
  }
}

function download(url, token) {
  let filename = '';
  const headers = {
    Authorization: `Bearer ${token}`
  };

  fetch(url, { headers })
    .then(response => {
      filename = response.headers
        .get('Content-Disposition')
        .split('filename=')[1];

      return response.blob();
    })
    .then(blob => {
      const link = document.createElement('a');
      link.href = URL.createObjectURL(blob);
      link.download = filename;
      link.click();
    })
    .catch(() => {
      // keep empty
    });
}

function* downloadRow({
  payload: { id, action },
  meta: { apiRef }
}: GridRowAction) {
  const { compactUrl, cookieBackend } = yield* getGlobalContext();

  const { pageUrl: downloadURl } = apiRef.current.config.actions[action];

  const url = compactUrl(downloadURl, { id });

  const token = cookieBackend.get('token');

  download(url, token);
}

function* showCacheDialog() {
  const { dialogBackend } = yield* getGlobalContext();

  dialogBackend.present({
    component: 'core.dialog.RemoteForm',
    props: {
      dataSource: {
        apiUrl: 'admincp/core/form/core.admin.destroy_cache'
      }
    }
  });
}

function* batchItem({
  type,
  payload: { id, dataSource, action, reload },
  meta: { apiRef }
}: GridMassAction) {
  // const { dialogBackend } = yield* getGlobalContext();
  try {
    const { apiClient, compactUrl } = yield* getGlobalContext();

    dataSource = dataSource ?? apiRef.current.config.actions[action];

    const response = yield apiClient.request({
      url: compactUrl(dataSource.apiUrl, { id }),
      data: dataSource.apiParams,
      method: dataSource.apiMethod || 'post'
    });

    yield* handleActionFeedback(response);

    if (reload) {
      apiRef.current.refresh();
    }
  } catch (error) {
    yield* handleActionError(error);
  }
}

function* sortableRows({
  type,
  payload: { order_ids },
  meta: { apiRef, onSuccess }
}: GridOrderAction & { meta: { onSuccess?: void } }) {
  // const { dialogBackend } = yield* getGlobalContext();
  try {
    const { apiClient } = yield* getGlobalContext();

    const dataSource = apiRef.current.config.actions['orderItem'];

    if (!dataSource) return;

    const response = yield apiClient.request({
      url: dataSource.apiUrl,
      data: { order_ids },
      method: dataSource.apiMethod || 'post'
    });

    typeof onSuccess === 'function' && onSuccess();

    if (response?.data?.status === 'success') {
      apiRef.current.orderRows(order_ids);
    }

    yield* handleActionFeedback(response);
  } catch (error) {
    yield* handleActionError(error);
  }
}

function* sortableColumns({
  payload: { sortableField, type },
  meta: { apiRef }
}) {
  try {
    const { navigate, location } = yield* getGlobalContext();

    // todo update this source
    // const location = '/';

    const parsedSearch = qs.parse(location.search);
    parsedSearch.order = sortableField;
    parsedSearch.order_by = type;

    const stringified = qs.stringify(parsedSearch);

    location.search = stringified;

    navigate(location);
  } catch (error) {
    yield* handleActionError(error);
  }
}

const sagas = [
  takeEvery('row/redirect', rowRedirect),
  takeEvery('@admin/showCacheDialog', showCacheDialog),
  takeEvery('@admin/batchItem', batchItem),
  takeEvery('row/active', activeRow),
  takeEvery('row/request', requestRow),
  takeEvery('row/remove', removeRow),
  takeEvery('row/edit', editRow),
  takeEvery('row/download', downloadRow),
  takeEvery('row/add', addRow),
  takeEvery('row/batchEdit', batchEditRows),
  takeEvery('row/batchRemove', batchRemoveRows),
  takeEvery(['row/batchActive', 'row/batchInactive'], batchActiveRow),
  takeEvery('row/sortable', sortableRows),
  takeEvery('column/sortable', sortableColumns)
];

export default sagas;
