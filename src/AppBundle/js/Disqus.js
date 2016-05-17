var $ = require('jquery');

export class Disqus {
    constructor(opts) {
        let self = this;
        this._cache = {};

        this._cache.wrapper = $('#disqus_thread');

        if (opts) {
            this.forEachParam(function(param) {
                self.setParam(param, opts[param]);
            });
        }

        this.embed(opts);
        this.count();
    }

    setParam(key, value) {
        if (value) {
            if (key === 'developer') {
                value = parseInt(value, 10);
            }
            window['disqus_' + key] = value;
        }
    }

    forEachParam(fn) {
        var params = [
            'shortname',
            'identifier',
            'title',
            'url',
            'developer'
        ];

        params.forEach(fn);
    }


    loadDisqus(script) {
        if (!window.disqus_shortname) {
            // at the minimum shortname needs to be defined
            console.log('"shortname" parameter missing');
            return;
        }

        let s = $('<script></script>');

        s.attr('src', '//' + window.disqus_shortname + '.disqus.com/' + script);

        $('body').append(s);
    }

    embed(opts) {
        var self = this,
            el = this._cache.wrapper,
            ds;

        if (!el.length) {
            return;
        }

        self.forEachParam(function(param) {
            if (!opts || !opts[param]) {
                self.setParam(param, el.data(param));
            }
        });
        
        self.loadDisqus('embed.js');
    }

    count() {
        var el = $('a[href$="#disqus_thread"]');

        if (!el.length) {
            return;
        }

        this.loadDisqus('count.js');
    }
}
