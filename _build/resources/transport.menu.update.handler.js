var topic = '/tvcollector/';
var register = 'mgr';
var console = MODx.load({
    xtype: 'modx-console',
    register: register,
    topic: topic,
    show_filename: 0,
    listeners: {
        'shutdown': {
            fn: function () {
            },
            scope: this
        }
    }
});

MODx.Ajax.request({
    url: MODx.config['connector_url'],
    params: {
        action: 'tvcollector/processor',
        register: register,
        topic: topic,
        process: 'update'
    },
    'success': {
        fn: function () {
            console.fireEvent('complete');
        },
        scope: this
    }
});

console.show(Ext.getBody());
