/**
 * @type: saga
 * name: layoutBackendSaga
 */

import {
  FormSubmitAction,
  getGlobalContext,
  handleActionError,
  handleActionFeedback,
  LAYOUT_EDITOR_TOGGLE,
  LocalAction
} from '@metafox/framework';
import {
  EditBlockParams,
  EditContainerParams,
  EditMode,
  EditSlotParams
} from '@metafox/layout';
import { omit, pick } from 'lodash';
import {
  call,
  delay,
  put,
  all,
  takeEvery,
  takeLatest
} from 'redux-saga/effects';

function* saveBlockLayout({
  payload: { values, form, dialogItem }
}: FormSubmitAction) {
  try {
    const { styleName } = values;

    const { layoutBackend } = yield* getGlobalContext();

    /// which data to overwrite to preset
    const data = omit(values, ['styleName']);

    yield layoutBackend.setBlockPreset(styleName, data);

    if (dialogItem) {
      dialogItem.closeDialog();
    }

    yield layoutBackend.reload();
  } catch (err) {
    yield* handleActionError(err);
  }
}

function* testConnection() {
  const { apiClient, dialogBackend } = yield* getGlobalContext();

  try {
    yield apiClient.get('/local/layout/snippet/ping');

    return true;
  } catch (error) {
    // warn: following message does not need to be translation.
    yield dialogBackend.alert({
      message:
        'Whoops!, Failed to connect development server. ' +
        'You are in development mode, run: yarn start:dev0 to start server'
    });
    // do nothing
  }

  return false;
}

function* publishLayouts() {
  const { dialogBackend, apiClient, layoutBackend, i18n, preferenceBackend } =
    yield* getGlobalContext();

  try {
    const ok = yield dialogBackend.confirm({
      message: i18n.formatMessage({ id: 'layout_publish_confirm' })
    });

    if (!ok) return;

    if (layoutBackend.isThemeDirty()) {
      yield* sendThemeData(true, false);
    }

    if (layoutBackend.isVariantDirty()) {
      yield* sendStyleData(true, false);
    }

    const themeId = preferenceBackend.getTheme();

    const response = yield apiClient.post('/layout/snippet/publish', {
      theme: themeId
    });

    yield* handleActionFeedback(response);

    layoutBackend.canPublish(false);
  } catch (err) {
    yield* handleActionError(err);
  }
}

function* saveTheme() {
  const { layoutBackend } = yield* getGlobalContext();

  const local = process.env.NODE_ENV === 'development';

  const ok = local ? yield* testConnection() : true;

  if (!ok) return;

  if (layoutBackend.isThemeDirty()) {
    yield* sendThemeData(true, local);
  }

  if (layoutBackend.isVariantDirty()) {
    yield* sendStyleData(true, local);
  }

  layoutBackend.canSave(false);
}

function* revertSnippet({ payload }: LocalAction<string>) {
  const { apiClient, layoutBackend } = yield* getGlobalContext();

  try {
    const response = yield apiClient.post(`/layout/snippet/revert/${payload}`);

    yield* handleActionFeedback(response);

    layoutBackend.revert(response.data.data.data);

    layoutBackend.canPublish(true);
  } catch (err) {
    yield* handleActionError(err);
  }
}

function* sendStyleData(active: boolean = true, local: boolean = false) {
  try {
    const { apiClient, layoutBackend } = yield* getGlobalContext();

    const themeInfo = layoutBackend.getThemeInfo();
    const variant = layoutBackend.getVariantInfo();
    const variantDir = variant.dir;

    const url = local
      ? '/local/layout/snippet/variant'
      : '/layout/snippet/variant';

    const response = yield apiClient.post(url, {
      theme: themeInfo.id,
      variant: variant.id,
      active,
      files: [
        {
          filename: `${variantDir}/styles.json`,
          name: 'variant',
          content: layoutBackend.getThemeConfig()
        }
      ]
    });

    layoutBackend.setVariantDirty(false);

    yield layoutBackend.reload();

    yield* handleActionFeedback(response);
  } catch (err) {
    yield* handleActionError(err);
  }
}

function* sendThemeData(active: boolean = true, local: boolean = false) {
  try {
    const { apiClient, layoutBackend } = yield* getGlobalContext();

    const themeInfo = layoutBackend.getThemeInfo();
    const variantInfo = layoutBackend.getVariantInfo();
    const themeDir = themeInfo.dir;

    const url = local ? '/local/layout/snippet/theme' : '/layout/snippet/theme';

    const response = yield apiClient.post(url, {
      theme: themeInfo.id,
      variant: variantInfo.id,
      active,
      files: [
        {
          filename: `${themeDir}/layout.templates.json`,
          content: layoutBackend.getTemplates(),
          name: 'templates'
        },
        {
          filename: `${themeDir}/layout.pages.json`,
          name: 'pageLayouts',
          content: layoutBackend.getPageLayouts()
        },
        {
          filename: `${themeDir}/layout.noContents.json`,
          name: 'noContentLayouts',
          content: layoutBackend.getNoContentPresets()
        },
        {
          filename: `${themeDir}/layout.grids.json`,
          name: 'gridLayouts',
          content: layoutBackend.getGridPresets()
        },
        {
          filename: `${themeDir}/layout.items.json`,
          name: 'itemLayouts',
          content: layoutBackend.getItemPresets()
        },
        {
          filename: `${themeDir}/layout.siteBlocks.json`,
          name: 'siteBlocks',
          content: layoutBackend.getSiteBlocks()
        },
        {
          filename: `${themeDir}/layout.blocks.json`,
          name: 'blockLayouts',
          content: layoutBackend.getBlockPresets()
        }
      ]
    });

    yield layoutBackend.reload();

    layoutBackend.setThemeDirty(false);

    yield* handleActionFeedback(response);
  } catch (err) {
    yield* handleActionError(err);
  }
}

function* saveNoContentLayout({
  payload: { values, form, dialogItem }
}: FormSubmitAction) {
  try {
    const { styleName } = values;

    const { layoutBackend } = yield* getGlobalContext();

    /// which data to overwrite to preset
    const config = omit(values, ['styleName']);

    yield layoutBackend.setNoContentPreset(styleName, config);

    if (dialogItem) {
      dialogItem.closeDialog();
    }

    yield layoutBackend.reload();
  } catch (err) {
    yield* handleActionError(err);
  }
}

function* saveGridLayout({
  payload: { values, dialogItem, form }
}: FormSubmitAction) {
  try {
    const { styleName } = values;

    const { layoutBackend } = yield* getGlobalContext();

    /// which data to overwrite to preset
    const config = pick(values, [
      'gridContainerProps',
      'gridItemProps',
      'itemProps',
      'gridVariant'
    ]);

    yield layoutBackend.setGridPreset(styleName, config);

    if (dialogItem) {
      dialogItem.closeDialog();
    }

    if (form) {
      form.setSubmitting(false);
    }
  } catch (err) {
    yield* handleActionError(err);
  }
}

function* editBlockLayout({
  payload: { styleName },
  meta
}: LocalAction<{
  styleName: string;
}>) {
  try {
    if (!styleName) throw new Error('Style name is required');

    const { dialogBackend } = yield* getGlobalContext();

    yield dialogBackend.present({
      component: 'layout.dialog.EditBlockLayout',
      props: {
        styleName
      }
    });
  } catch (err) {
    yield* handleActionError(err);
  }
}

function* editNoContentLayout({
  payload: { styleName },
  meta
}: LocalAction<
  {
    styleName: string;
  },
  { onSuccess: any }
>) {
  try {
    if (!styleName) throw new Error('Style name is required');

    const { dialogBackend } = yield* getGlobalContext();

    yield dialogBackend.present({
      component: 'layout.dialog.EditNoContentLayout',
      props: {
        styleName
      }
    });
  } catch (err) {
    yield* handleActionError(err);
  }
}

function* editGridLayout({
  payload: { styleName }
}: LocalAction<{ styleName: string }>) {
  try {
    const { dialogBackend } = yield* getGlobalContext();

    yield dialogBackend.present({
      component: 'layout.dialog.EditGridLayout',
      props: {
        styleName
      }
    });
  } catch (err) {
    yield* handleActionError(err);
  }
}

function* editItemLayout({
  payload: { styleName }
}: LocalAction<{ styleName: string }>) {
  try {
    const { dialogBackend } = yield* getGlobalContext();

    yield dialogBackend.present({
      component: 'layout.dialog.EditItemLayout',
      props: {
        styleName
      }
    });
  } catch (err) {
    yield* handleActionError(err);
  }
}

function* manageGridLayout() {
  try {
    const { dialogBackend } = yield* getGlobalContext();

    yield dialogBackend.present({
      component: 'layout.dialog.ManageGridLayout',
      props: {}
    });
  } catch (err) {
    yield* handleActionError(err);
  }
}

function* manageItemLayout() {
  try {
    const { dialogBackend } = yield* getGlobalContext();

    yield dialogBackend.present({
      component: 'layout.dialog.ManageItemLayout',
      props: {}
    });
  } catch (err) {
    yield* handleActionError(err);
  }
}

function* manageTemplates() {
  try {
    const { dialogBackend } = yield* getGlobalContext();

    yield dialogBackend.present({
      component: 'layout.dialog.ManageLayouts',
      props: {}
    });
  } catch (err) {
    yield* handleActionError(err);
  }
}

function* manageBlockLayout() {
  try {
    const { dialogBackend } = yield* getGlobalContext();

    dialogBackend.present({
      component: 'layout.dialog.ManageBlockLayout',
      props: {}
    });
  } catch (err) {
    yield* handleActionError(err);
  }
}

function* manageNoContentLayout() {
  try {
    const { dialogBackend } = yield* getGlobalContext();

    dialogBackend.present({
      component: 'layout.dialog.ManageNoContentLayout',
      props: {}
    });
  } catch (err) {
    yield* handleActionError(err);
  }
}

function* editBlock({ payload }: LocalAction<EditBlockParams>) {
  try {
    const { dialogBackend } = yield* getGlobalContext();

    yield dialogBackend.present({
      component: 'layout.dialog.EditBlock',
      props: payload
    });
  } catch (err) {
    yield* handleActionError(err);
  }
}

function* toggleBlock({ payload }: LocalAction<EditBlockParams>) {
  try {
    const { layoutBackend } = yield* getGlobalContext();

    layoutBackend.toggleBlock(payload);
  } catch (err) {
    yield* handleActionError(err);
  }
}

function* deleteBlock({ payload }: LocalAction<EditBlockParams>) {
  try {
    const { layoutBackend, dialogBackend, i18n } = yield* getGlobalContext();

    const ok = yield dialogBackend.confirm({
      title: i18n.formatMessage({ id: 'are_you_sure' }),
      message: i18n.formatMessage({ id: 'do_you_want_to_delete_this_block' })
    });

    if (!ok) return;

    yield layoutBackend.deleteBlock(payload);
  } catch (err) {
    yield* handleActionError(err);
  }
}

function* createBlock({ payload }: LocalAction<EditBlockParams>) {
  try {
    const { dialogBackend } = yield* getGlobalContext();

    yield dialogBackend.present({
      component: 'layout.addLayoutBlockDialog',
      props: {
        ...payload,
        parentBlockId: payload.blockId,
        blockId: undefined
      }
    });
  } catch (err) {
    yield* handleActionError(err);
  }
}

function* createSlot({ payload }: LocalAction<EditContainerParams>) {
  try {
    const { dialogBackend } = yield* getGlobalContext();

    dialogBackend.present({
      component: 'layout.addNewSlotDialog',
      props: payload
    });
  } catch (err) {
    yield* handleActionError(err);
  }
}

function* createContainer({ payload }: LocalAction<EditContainerParams>) {
  try {
    const { dialogBackend } = yield* getGlobalContext();

    dialogBackend.present({
      component: 'layout.dialog.AddNewContainer',
      props: payload
    });
  } catch (err) {
    yield* handleActionError(err);
  }
}

function* createTemplate({
  payload
}: LocalAction<{ baseTemplate: string; pageName: string; pageSize: string }>) {
  try {
    const { layoutBackend } = yield* getGlobalContext();

    const { baseTemplate } = payload;

    const templateName = `${baseTemplate}-1`;

    yield layoutBackend.addNewTemplate(baseTemplate, templateName);

    yield delay(1000);

    yield layoutBackend.editTemplate(templateName);
  } catch (err) {
    yield* handleActionError(err);
  }
}

export function* saveTemplate({ payload }: LocalAction<{}>) {
  try {
    const { dialogBackend } = yield* getGlobalContext();

    yield dialogBackend.present({
      component: 'layout.dialog.saveTemplate',
      props: payload
    });
  } catch (err) {
    yield* handleActionError(err);
  }
}

function* changeTemplate({ payload }: LocalAction<{}>) {
  try {
    const { dialogBackend, layoutBackend } = yield* getGlobalContext();

    const templateName = yield dialogBackend.present({
      component: 'layout.dialog.ChooseLayout',
      props: payload
    });

    if (!templateName) return;

    yield layoutBackend.setEditMode(EditMode.launch);

    yield layoutBackend.changeTemplate(templateName, payload);
  } catch (err) {
    yield* handleActionError(err);
  }
}

function* browseTemplate({ payload }: LocalAction<{}>) {
  try {
    const { dialogBackend } = yield* getGlobalContext();

    yield dialogBackend.present({
      component: 'layout.dialog.browseTemplate'
    });
  } catch (err) {
    yield* handleActionError(err);
  }
}

function* inspectPage({ payload }: LocalAction<{}>) {
  try {
    const { dialogBackend } = yield* getGlobalContext();

    yield dialogBackend.present({
      component: 'layout.dialog.InspectPageDialog',
      props: payload
    });
  } catch (err) {
    yield* handleActionError(err);
  }
}

function* deleteContainer({ payload }: LocalAction<EditContainerParams>) {
  try {
    const { dialogBackend, layoutBackend } = yield* getGlobalContext();

    const ok = yield dialogBackend.confirm({
      title: 'Confirm',
      message: 'Do you want to remove this container?'
    });

    if (!ok) return;

    yield layoutBackend.deleteContainer(payload);
    yield layoutBackend.reload();
  } catch (err) {
    yield* handleActionError(err);
  }
}

function* editContainer({ payload }: LocalAction<EditContainerParams>) {
  try {
    const { dialogBackend } = yield* getGlobalContext();

    yield dialogBackend.present({
      component: 'layout.dialog.editContainer',
      props: payload
    });
  } catch (err) {
    yield* handleActionError(err);
  }
}

function* editSlot({ payload }: LocalAction<EditSlotParams>) {
  try {
    const { dialogBackend } = yield* getGlobalContext();

    dialogBackend.present({
      component: 'layout.editSlotDialog',
      props: payload
    });
  } catch (err) {
    yield* handleActionError(err);
  }
}

function* deleteSlot({ payload }: LocalAction<EditSlotParams>) {
  try {
    const { dialogBackend, layoutBackend, i18n } = yield* getGlobalContext();

    const ok = yield dialogBackend.confirm({
      title: i18n.formatMessage({ id: 'are_you_sure' }),
      message: i18n.formatMessage({ id: 'delete_this_slot' })
    });

    if (!ok) return;

    yield layoutBackend.deleteSlot(payload);
  } catch (err) {
    yield* handleActionError(err);
  }
}

// layout editor

function* toggleEditor() {
  const { localStore, layoutBackend } = yield* getGlobalContext();

  const key = LAYOUT_EDITOR_TOGGLE;

  const enabled = localStore.get(key);

  localStore.set(key, enabled ? '' : '1');

  layoutBackend.reload();

  if (!enabled) {
    layoutBackend.setEditMode(EditMode.editLive);
  }
}

function* closeEditor() {
  yield put({ type: '@layout/toggleEditor' });
  yield put({ type: '@layout/leavePreview' });
  yield put({ type: '@layout/liveView' });
}

function* toggleDarkMode() {
  const { preferenceBackend } = yield* getGlobalContext();

  preferenceBackend.toggleDarkMode();
}

function* toggleRtl() {
  const { preferenceBackend } = yield* getGlobalContext();

  preferenceBackend.setAndRemember('userDirection', prev => {
    return 'rtl' === prev ? 'ltr' : 'rtl';
  });
}

function* leavePreview() {
  const { eventCenter, preferenceBackend } = yield* getGlobalContext();

  eventCenter.removePreviewCenter();
  preferenceBackend.setAndRemember('previewDevice', '');
}

function* previewOnDevice({ payload }: LocalAction<string>) {
  const { preferenceBackend } = yield* getGlobalContext();

  preferenceBackend.setAndRemember('previewDevice', payload);
}

function* liveView() {
  const { layoutBackend } = yield* getGlobalContext();
  layoutBackend.setEditMode(EditMode.launch);
}

function* editPageContent() {
  const { layoutBackend } = yield* getGlobalContext();
  layoutBackend.setEditMode(EditMode.editPageContent);
}

function* editSiteContent() {
  const { layoutBackend } = yield* getGlobalContext();
  layoutBackend.setEditMode(EditMode.editSiteContent);
}

function* liveEdit() {
  const { layoutBackend } = yield* getGlobalContext();
  layoutBackend.setEditMode(EditMode.editLive);
}

function* inspectTheme() {
  const { dialogBackend } = yield* getGlobalContext();

  yield dialogBackend.present({
    component: 'layout.dialog.InspectThemeDialog'
  });
}

function* editLayout() {
  const { layoutBackend } = yield* getGlobalContext();
  layoutBackend.setEditMode(EditMode.editLayout);
}

export function* discardChanges({ payload }: LocalAction<string>) {
  try {
    const { apiClient, layoutBackend, dialogBackend, i18n } =
      yield* getGlobalContext();

    layoutBackend.setThemeDirty(false);

    const { id: variant } = layoutBackend.getVariantInfo();
    const { id: theme } = layoutBackend.getThemeInfo();

    const ok = yield dialogBackend.confirm({
      message: i18n.formatMessage({
        id: 'layout_discard_changes_confirm_message'
      })
    });

    if (!ok) return;

    layoutBackend.canPublish(true);

    yield all([
      apiClient.post(`/layout/snippet/history/purge/${variant}`),
      apiClient.post(`/layout/snippet/history/purge/${theme}`)
    ]);

    layoutBackend.discardChanges();
  } catch (err) {
    yield* handleActionError(err);
  }
}

export function* clearHistory({ payload }: LocalAction<string>) {
  try {
    const { apiClient, layoutBackend, dialogBackend, i18n } =
      yield* getGlobalContext();

    layoutBackend.setThemeDirty(false);

    const ok = yield dialogBackend.confirm({
      message: i18n.formatMessage({ id: 'are_you_sure' })
    });

    if (!ok) return;

    const response = yield apiClient.post(
      `/layout/snippet/history/purge/${payload}`
    );

    yield* handleActionFeedback(response);

    layoutBackend.discardChanges();
  } catch (err) {
    yield* handleActionError(err);
  }
}

function* viewHistories() {
  try {
    const { dialogBackend, layoutBackend } = yield* getGlobalContext();

    const { id: variant } = layoutBackend.getVariantInfo();
    const { id: theme } = layoutBackend.getThemeInfo();

    yield call(dialogBackend.present, {
      component: 'layout.dialog.ManageHistories',
      props: {
        variant,
        theme
      }
    });
  } catch (err) {
    yield* handleActionError(err);
  }
}

const sagas = [
  takeEvery('@layout/saveBlockLayout', saveBlockLayout),
  takeEvery('@layout/saveNoContentLayout', saveNoContentLayout),
  takeEvery('@layout/saveGridLayout', saveGridLayout),
  takeLatest('@layout/manageBlockLayout', manageBlockLayout),
  takeLatest('@layout/manageNoContentLayout', manageNoContentLayout),
  takeLatest('@layout/editBlockLayout', editBlockLayout),
  takeLatest('@layout/editNoContentLayout', editNoContentLayout),
  takeLatest('@layout/manageGridLayout', manageGridLayout),
  takeLatest('@layout/manageItemLayout', manageItemLayout),
  takeLatest('@layout/manageLayouts', manageTemplates),
  takeLatest('@layout/editGridLayout', editGridLayout),
  takeLatest('@layout/editItemLayout', editItemLayout),
  takeLatest('@layout/createBlock', createBlock),
  takeLatest('@layout/editBlock', editBlock),
  takeLatest('@layout/toggleBlock', toggleBlock),
  takeLatest('@layout/deleteBlock', deleteBlock),
  takeLatest('@layout/editSlot', editSlot),
  takeLatest('@layout/revert', revertSnippet),
  takeLatest('@layout/discardChanges', discardChanges),
  takeLatest('@layout/clearHistory', clearHistory),
  takeLatest('@layout/viewHistory', viewHistories),
  takeLatest('@layout/deleteSlot', deleteSlot),
  takeLatest('@layout/createSlot', createSlot),
  takeLatest('@layout/createContainer', createContainer),
  takeLatest('@layout/deleteContainer', deleteContainer),
  takeLatest('@layout/editContainer', editContainer),
  // template action
  takeLatest('@layout/createTemplate', createTemplate),
  takeLatest('@layout/saveTheme', saveTheme),
  takeLatest('@layout/publishLayouts', publishLayouts),
  takeLatest('@layout/chooseLayout', changeTemplate),
  takeLatest('@layout/browseTemplate', browseTemplate),
  // page action
  takeLatest('@layout/inspectPage', inspectPage),
  takeLatest(['@layout/toggleEditor', 'layout/control/toggle'], toggleEditor),
  takeLatest('@layout/closeEditor', closeEditor),
  takeLatest(['toggleDarkMode', '@layout/toggleDarkMode'], toggleDarkMode),
  takeLatest('@layout/toggleRtl', toggleRtl),
  takeLatest('@layout/leavePreview', leavePreview),
  takeLatest('@layout/previewOnDevice', previewOnDevice),
  takeLatest('@layout/editPageContent', editPageContent),
  takeLatest('@layout/editSiteContent', editSiteContent),
  takeLatest('@layout/inspectTheme', inspectTheme),
  takeLatest('@layout/editLayout', editLayout),
  takeLatest('@layout/liveEdit', liveEdit),
  takeLatest('@layout/liveView', liveView)
];

export default sagas;
