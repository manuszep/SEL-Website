var $ = require('jquery');

export class AddTool {
    constructor() {
        this._cache = {};

        this._cache.tool = $('.add-tool');
        this._cache.toggle = $('.add-tool .toggle');

        this.setupEvents();
    }

    setupEvents() {
        let self = this;

        this._cache.toggle.on('click', function(e) {
            e.preventDefault();
            self._cache.tool.toggleClass("active");
        })
    }
}