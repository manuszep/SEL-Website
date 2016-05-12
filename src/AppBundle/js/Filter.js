var $ = require('jquery');

export class Filter {
    constructor(selector = "[data-fast-scroll]") {
        this._cache = {};

        this._cache.filterBlocks = $('.filters');
        this._cache.filterBlockToggles = this._cache.filterBlocks.find('.filter-toggle');
        this._cache.filterBlockForms = this._cache.filterBlocks.find('form');

        this._cache.filterBlockToggles.show();
        this._cache.filterBlockForms.hide();

        this.setupEvents();
    }

    setupEvents() {
        this._cache.filterBlockToggles.on('click.selFilter', this.handleToggleClick);
    }

    handleToggleClick(e) {
        let $this = $(this);
        let form = $this.siblings('form');

        form.slideToggle();
        $this.toggleClass('toggled');
    }
}
