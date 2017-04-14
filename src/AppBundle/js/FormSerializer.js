var $ = require('jquery');

export class FormSerializer {
    constructor() {
        this.json = {};
        this.push_counters = {};
        this.patterns = {
            "validate": /^[a-zA-Z][a-zA-Z0-9_]*(?:\[(?:\d*|[a-zA-Z0-9_]+)\])*$/,
            "key":      /[a-zA-Z0-9_]+|(?=\[\])/g,
            "push":     /^$/,
            "fixed":    /^\d+$/,
            "named":    /^[a-zA-Z0-9_]+$/
        };
    }

    build(base, key, value) {
        base[key] = value;
        return base;
    }

    pushCounter(key) {
        if(this.push_counters[key] === undefined){
            this.push_counters[key] = 0;
        }
        return this.push_counters[key]++;
    }

    serialize($elem) {
        let self = this;
        $.each($elem.serializeArray(), function(){

            // skip invalid keys
            if(!self.patterns.validate.test(this.name)){
                return;
            }

            var k,
                keys = this.name.match(self.patterns.key),
                merge = this.value,
                reverse_key = this.name;

            while((k = keys.pop()) !== undefined){

                // adjust reverse_key
                reverse_key = reverse_key.replace(new RegExp("\\[" + k + "\\]$"), '');

                // push
                if(k.match(self.patterns.push)){
                    merge = self.build([], self.pushCounter(reverse_key), merge);
                }

                // fixed
                else if(k.match(self.patterns.fixed)){
                    merge = self.build([], k, merge);
                }

                // named
                else if(k.match(self.patterns.named)){
                    merge = self.build({}, k, merge);
                }
            }

            self.json = $.extend(true, self.json, merge);
        });

        return self.json;
    }

    getAsRequest($elem) {
        return $.param(this.serialize($elem));
    }
}
