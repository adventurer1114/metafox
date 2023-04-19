/**
 * @type: layoutBlockFeature
 * name: gridLayout
 */
import { BlockFeatureCreatorConfig } from '@metafox/layout/types';

export default function blockProps({
  manager,
  disabled
}: BlockFeatureCreatorConfig) {
  const gridLayoutOptions = [{ label: 'none', value: 'No layout' }].concat(
    Object.keys(manager.layoutBackend.getGridPresets()).map(value => ({
      label: value,
      value
    }))
  );

  const itemLayoutOptions = [{ label: 'none', value: 'No layout' }].concat(
    Object.keys(manager.layoutBackend.getItemPresets()).map(value => ({
      label: value,
      value
    }))
  );

  return {
    component: 'Container',
    elements: {
      gridLayout: {
        component: 'Select',
        name: 'gridLayout',
        fullWidth: true,
        margin: 'normal',
        label: 'Grid Container Style',
        defaultValue: 'none',
        placeholder: 'No layout',
        options: gridLayoutOptions
      },
      itemLayout: {
        component: 'Select',
        name: 'itemLayout',
        fullWidth: true,
        margin: 'normal',
        label: 'Grid Item Style',
        defaultValue: 'none',
        placeholder: 'No layout',
        options: itemLayoutOptions
      }
    }
  };
}
