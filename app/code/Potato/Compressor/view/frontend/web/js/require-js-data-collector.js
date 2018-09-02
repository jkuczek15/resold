define([
    'jquery'
], function($){
    return {
        init: function(key, url) {
            this.key = key;
            this.url = url;
        },

        run: function() {
            var me = this;
            var modules = me._modules();
            if (modules.length > 0) {
                setTimeout(function(){
                    me.run();
                }, 1000);
                return;
            }
            this.doRequest();
        },

        doRequest: function() {
            $.ajax(this.url, {
                method: 'post',
                data: {
                    key: this.key,
                    list: this._scripts()
                }
            });
        },

        _modules: function() {
            return $.map(require.s.contexts._.registry, function(o, key){
                if (o.enabled) {
                    return key;
                }
            });
        },

        _scripts: function() {
            var jsList = Object.keys(require.s.contexts._.urlFetched);
            var textList = [];
            $.each(Object.keys(require.s.contexts._.defined), function(i, module){
                if (module.indexOf('text!') !== 0) {
                    return;
                }
                var name = module.replace(/^text!/, '');
                textList.push(require.toUrl(name));
            });
            return $.merge(jsList, textList);
        }
    };
});