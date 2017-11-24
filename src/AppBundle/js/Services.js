var $ = require('jquery');
import {FormSerializer} from './FormSerializer';

export class Services {
    constructor() {
        this.handlePaginationClick = this.handlePaginationClick.bind(this);
        this.handlePopState = this.handlePopState.bind(this);
        this.handleAjaxSuccess = this.handleAjaxSuccess.bind(this);
        this.handleAjaxFail = this.handleAjaxFail.bind(this);
        this.handleSubmitClick = this.handleSubmitClick.bind(this);
    }

    init() {
        this._cache =  {
            form: $('#serviceFiltersForm'),
            notFound: $('#ServicesNotFound'),
            container: $('#servicesList'),
            loader: $('#ServicesLoader'),
            wrapper: $('#servicesListWrapper'),
            items: $('.service-filter-item'),
            checks: $('.service-filter-item__item input[type="checkbox"]')
        };

        this.serializer = new FormSerializer(this._cache.form);

        this.search_url = window.location.href;

        if (!this._cache.notFound.length, !this._cache.container.length, !this._cache.loader.length, !this._cache.wrapper.length) return;

        this.setupEvents();

        this.serializer.saveFormData();
    }

    setupEvents() {
        $('body').on('click', '.pagination a', this.handlePaginationClick);
        window.addEventListener('popstate', this.handlePopState);
        this._cache.form.find('button[type=submit]').on('click', this.handleSubmitClick);
    }

    handleSubmitClick(e) {
        e.preventDefault();
        this.serializer.saveFormData();
        let request = "/service" + this.serializer.getRequest();

        history.pushState(this.serializer.getFormData(), null, request);
        this.makeRequest(request);
    }

    handlePaginationClick(e) {
        e.preventDefault();
        this.serializer.saveFormData();

        let request = $(e.target).attr('href');

        history.pushState(this.serializer.getFormData(), null, request);
        this.makeRequest(request);
    }

    handlePopState(e) {
        e.preventDefault();
        this.search_url = window.location.href;

        this.makeRequest(this.search_url);
        this.serializer.setFormData(e.state);
        this.serializer.restoreFormData();
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
