var $ = require('jquery');
import {FormSerializer} from './FormSerializer';

export class Services {
    constructor() {
        let self = this;
        this._cache = {};

        this._cache.form = $('#serviceFiltersForm');
        this._cache.form_fields = this._cache.form.find('input, select, textarea');
        this._cache.listContainer = $('#servicesList');
        this._cache.listWrapper = $('#servicesListWrapper');
        this._xhr = null;
        this._form_data = null;

        this._serializer = new FormSerializer();

        this.handleFormChange = this.handleFormChange.bind(this);
        this.handlePaginationClick = this.handlePaginationClick.bind(this);
    }

    init(app) {
        if (!this._cache.form.length || !this._cache.listWrapper.length) return;
        this._form_data = this.saveFilterFormData();
        this._cache.form.find('button[type=submit]').hide();
        this._cache.form.on('change', 'input, select, textarea', this.handleFormChange);
        $('body').on('click', '.pagination a', this.handlePaginationClick);

        window.addEventListener('popstate', function(e) {
            var s = e.state;
            var form_data = null;

            if (s != null && typeof s.service_url !== 'undefined') {
                form_data = s.form_data;
                this.ajaxCall(s.service_url);
            } else {
                this.ajaxCall("/service");
            }

            this.restoreFilterFormData(form_data || this._form_data);
        }.bind(this));
        this._app = app;
    }

    ajaxCall(request) {
        this._cache.listContainer.addClass('loading');
        this._cache.listWrapper.find('p, .grid').slideUp(function() {
            this._app.$grid.masonry( 'destroy' );
        }.bind(this));

        try {
            this._xhr.abort();
        } catch (e) {}

        this._xhr = $.ajax({
            url: request,
            cache: false
        }).done(function( html ) {
            this._cache.listWrapper.html( html );

            this._app.initMasonry(false);
            this._cache.listWrapper.find('p, .grid').slideDown()
            this._cache.listContainer.removeClass('loading');

        }.bind(this)).fail(function(e) {
            this._cache.listContainer.removeClass('loading');
        });
    }

    handlePaginationClick(e) {
        e.preventDefault();
        this._form_data = this._serializer.serialize(this._cache.form);
        let request = $(e.target).attr('href');
        history.pushState({service_url: request, form_data: this.saveFilterFormData()}, null, request);

        this.ajaxCall(request);
    }

    handleFormChange(e) {
        this._form_data = this._serializer.serialize(this._cache.form);
        let request = "/service?" + this._serializer.convertToRequest(this._form_data);
        history.pushState({service_url: request, form_data: this.saveFilterFormData()}, null, request);

        this.ajaxCall(request);
    }

    saveFilterFormData() {
        let data = {};

        $.each(this._cache.form_fields, function() {
            let id = $(this).attr("id");
            if (id && !this.disabled && (this.checked
                || /select|textarea/i.test(this.nodeName)
                || /text|hidden|password/i.test(this.type))) {
                data[id] = $(this).val();
            }
        });
        return data;
    }

    restoreFilterFormData(data) {
        $.each(this._cache.form_fields, function() {
            let id = $(this).attr("id");
            if (id) {
                if(this.type == 'checkbox' || this.type == 'radio') {
                    $(this).attr("checked", (data[id] == $(this).val()));
                } else {
                    $(this).val(data[id]);
                }
            }
        });
    }
}
