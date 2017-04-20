var $ = require('jquery');

export class Services {
    constructor() {
        this.handleFormChange = this.handleFormChange.bind(this);
        this.handlePaginationClick = this.handlePaginationClick.bind(this);
        this.handlePopState = this.handlePopState.bind(this);
        this.handleAjaxSuccess = this.handleAjaxSuccess.bind(this);
        this.handleAjaxFail = this.handleAjaxFail.bind(this);
    }

    init() {
        this._cache =  {
            form: $('#serviceFiltersForm'),
            formFields: $('#serviceFiltersForm').find('input, select, textarea'),
            notFound: $('#ServicesNotFound'),
            container: $('#servicesList'),
            loader: $('#ServicesLoader'),
            wrapper: $('#servicesListWrapper')
        };

        this.search_url = window.location.href;

        if (!this._cache.form.length, !this._cache.notFound.length, !this._cache.container.length, !this._cache.loader.length, !this._cache.wrapper.length) return;

        this.setupEvents();

        this._cache.form.find('button[type=submit]').hide();

        this.saveFilterFormData();
    }

    setupEvents() {
        this._cache.form.on('change', 'input, select, textarea', this.handleFormChange);
        $('body').on('click', '.pagination a', this.handlePaginationClick);
        window.addEventListener('popstate', this.handlePopState);
        window.addEventListener('hashchange', this.handleUrlChange);
    }

    handleFormChange(e) {
        this.saveFilterFormData();
        let request = "/service" + this.form_request;

        history.pushState(this.form_data, null, request);
        this.makeRequest(request);
    }

    handlePaginationClick(e) {
        e.preventDefault();
        this.saveFilterFormData();

        let request = $(e.target).attr('href');

        history.pushState(this.form_data, null, request);
        this.makeRequest(request);
    }

    handlePopState(e) {
        e.preventDefault();
        this.search_url = window.location.href;

        this.makeRequest(this.search_url);
        this.form_data = e.state;
        this.restoreFilterFormData();
    }

    handleAjaxSuccess(html) {
        this.unsetLoadingState();
        // Get list of articles from html
        let $html = $(html);
        let items = $html.find('article');
        let pagination = $html[2];

        // Append articles to dom (and append to masonry object)
        this._cache.wrapper.append(items);

        this._cache.wrapper.slideDown(function() {
            if (!items.length) {
                this._cache.notFound.slideDown();
            }
            // wait for images loading
            this._cache.wrapper.imagesLoaded( function() {
                // Relayout masonry
                this._cache.wrapper.masonry('appended', items).masonry('layout');
                $('.pagination').remove();
                this._cache.wrapper.after(pagination);
            }.bind(this));
        }.bind(this));
    }

    handleAjaxFail() {
        this.unsetLoadingState();
    }

    convertFormDataToRequest(data) {
        let request = "?";

        for (var key in data) {
            request += data[key].name + "=" + data[key].value + "&";
        }

        return encodeURI(request.replace(/&\s*$/, ""));
    }

    saveFilterFormData() {
        let data = {};

        $.each(this._cache.formFields, function() {
            let id = $(this).attr("id");
            let name = $(this).attr("name");

            if (id && (this.checked || /select|textarea/i.test(this.nodeName) || /text|hidden|password/i.test(this.type))) {
                data[id] = {
                    name: name,
                    value: $(this).val()
                }
            } else if (id) {
                data[id] = {
                    name: name,
                    value: ""
                }
            }
        });

        this.form_data = data;
        this.form_request = this.convertFormDataToRequest(data);
    }

    restoreFilterFormData() {
        // Loop all form elements
        this._cache.formFields.each(function(key, field) {
            let id = field.id; // Get element id
            let val = "";

            // The form_data object may be null or the value may not exist.
            // Try to use the stored data but keep null if nothing is found.
            try {val = this.form_data[id].value;} catch(e) {}

            // We don't want to alter disabled fields
            if (field.disabled) return;

            if(field.type == 'checkbox' || field.type == 'radio') {
                $(field).prop("checked", (val == $(field).val()));
            } else {
                $(field).val(val);
            }
        }.bind(this));
    }

    makeRequest(request) {
        // If an ajax call is running, cancel it
        try {this._xhr.abort();} catch (e) {}

        this.setLoadingState();
        this._cache.notFound.slideUp();
        this._cache.wrapper.slideUp(function() {
            let articles = this._cache.wrapper.find('article');
            this._cache.wrapper.masonry('remove',this._cache.wrapper.find('article')).masonry('layout');
            articles.remove();

            this._xhr = $.ajax({
                url: request,
                cache: false
            }).done(this.handleAjaxSuccess).fail(this.handleAjaxFail);
        }.bind(this));
    }

    setLoadingState() {
        this._cache.container.addClass('loading');
    }

    unsetLoadingState() {
        this._cache.container.removeClass('loading');
    }
}
