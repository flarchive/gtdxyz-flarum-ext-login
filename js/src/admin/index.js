import app from 'flarum/admin/app';

app.initializers.add('gtdxyz-login', () => {

  app.extensionData
    .for('gtdxyz-login')
    .registerSetting({
      setting: 'gtdxyz-login.type',
      label: app.translator.trans('gtdxyz-login.admin.login_identification'),
      type: 'select',
      options: {
        default: app.translator.trans('gtdxyz-login.admin.login_default'),
        email: app.translator.trans('gtdxyz-login.admin.login_email'),
        username: app.translator.trans('gtdxyz-login.admin.login_username'),
      },
    })
    
});
