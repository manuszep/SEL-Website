var $ = require('jquery');
import {FormSerializer} from './FormSerializer';

export class Services {
    constructor() {
        let self = this;
        this._cache = {};

        this._cache.form = $('#serviceFiltersForm');
        this._cache.listContainer = $('#servicesList');
        this._cache.listWrapper = $('#servicesListWrapper');
        this._xhr = null;

        this.handleFormChange = this.handleFormChange.bind(this);
        this.handlePaginationClick = this.handlePaginationClick.bind(this);
    }

    init(app) {
        if (!this._cache.form.length || !this._cache.listWrapper.length) return;
        this._cache.form.find('button[type=submit]').hide();
        this._cache.form.on('change', 'input, select, textarea', this.handleFormChange);
        $('body').on('click', '.pagination a', this.handlePaginationClick);
        this._app = app;
    }

    ajaxCall(request) {
        this._cache.listContainer.addClass('loading');
        this._app.$grid.masonry( 'destroy' );
        this._app.$grid.find('article').slideUp();

        try {
            this._xhr.abort();
        } catch (e) {}

        this._xhr = $.ajax({
            url: request,
            cache: false
        }).done(function( html ) {
            this._cache.listWrapper.html( html );

            this._app.$grid.find('article').hide().slideDown(function() {
                this._app.initMasonry(false);
            }.bind(this));
            this._cache.listContainer.removeClass('loading');
        }.bind(this)).fail(function(e) {
            this._cache.listContainer.removeClass('loading');
        });
    }

    handlePaginationClick(e) {
        e.preventDefault();
        let request = $(e.target).attr('href');

        this.ajaxCall(request);
    }

    handleFormChange(e) {
        let s = new(FormSerializer);
        let request = "/service?" + s.getAsRequest(this._cache.form);

        this.ajaxCall(request);
    }
}
