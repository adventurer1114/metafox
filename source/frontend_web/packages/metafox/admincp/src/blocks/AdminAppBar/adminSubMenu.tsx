const adminSubMenu = {
  items: [
    {
      icon: 'ico-language',
      value: '@core/codeGenerate/addTranslation',
      label: 'Code Generator',
      as: 'link',
      name: 'code',
      showWhen: ['eq', 'setting.app.env', 'local']
    },
    {
      name: 'rebuild',
      icon: 'ico-refresh-o',
      to: '/admincp/layout/build/wizard',
      label: 'Rebuild Site',
      title: 'Rebuild Site',
      as: 'link'
    },
    {
      name: 'cache',
      icon: 'ico-noun-broom',
      value: '@admin/showCacheDialog',
      label: 'Clear Cache',
      title: 'Clear Cache',
      as: 'link'
    },
    {
      icon: 'ico-code',
      value: '@core/codeGenerate/show',
      label: 'Code Generator',
      as: 'link',
      name: 'code',
      showWhen: ['eq', 'setting.app.env', 'local']
    },
    {
      icon: 'ico-bell-o',
      to: '/notification',
      label: 'Notifications',
      as: 'popover',
      name: 'new_notification',
      content: {
        component: 'notification.ui.notificationPopper'
      }
    },
    { as: 'divider' },
    {
      icon: '',
      as: 'adminUser',
      name: 'adminUser',
      target: '_blank'
    },
    {
      icon: 'ico-shutdown',
      as: 'link',
      name: 'logout',
      to: '/admincp/logout',
      title: 'Logout',
      label: 'Logout'
    },
    {
      as: 'divider',
      name: 'divider'
    },
    {
      icon: 'ico-external-link',
      value: 'viewSite',
      as: 'viewSite',
      label: 'View Site',
      name: 'viewSite'
    }
  ]
};

export default adminSubMenu;
