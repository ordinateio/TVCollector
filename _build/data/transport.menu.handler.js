var topic = '/tvcollector/';
var register = 'mgr';
var console = MODx.load({
  xtype:         'modx-console',
  register:      register,
  topic:         topic,
  show_filename: 0,
  listeners: {
    'shutdown': {
      fn:function() {
        MODx.Ajax.request({
          url: MODx.config.connector_url,
          params: {
            action:   'tvcollector/index',
            register: register,
            topic:    topic
          },
          listeners: {
            'success':{
              fn:function() {
                console.fireEvent('complete');
              },
              scope:this
            }
          }
        });
      },
      scope:this
    }
  }
});
console.show(Ext.getBody());
