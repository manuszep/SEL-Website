var $ = require('jquery');

export class FilterForm {
    constructor() {
        this.handleFormChange = this.handleFormChange.bind(this);
    }

    init() {
        this._cache =  {
            items: $('.service-filter-item'),
            checks: $('.service-filter-item__item input')
        };

        this.setupEvents();

        this.updateServiceFilterItems();
    }

    setupEvents() {
        this._cache.checks.on('click', this.handleFormChange);
        this._cache.items.on('click', this.handleToggleClick);
        $('body').on('click', this.handleBodyClick);
    }

    handleFormChange(e) {
        console.log(e);
        this.updateServiceFilterItems();
    }

    handleToggleClick(e) {
        e.stopPropagation();
        var $this = $(this);

        $('.service-filter-item').not(this).removeClass('active');
        if ($(e.target).hasClass('service-filter-item__toggle') || $(e.target).hasClass('service-filter-item__toggle__label')) {
            $this.toggleClass("active");
        } else if (!$this.hasClass("active")) {
            $this.addClass("active");
        }
    }

    handleBodyClick() {
        $('.service-filter-item').removeClass('active');
    }

    updateServiceFilterItems() {
        $('.service-filter-item').each(function() {
            var value = [],
                $label = $(this).find('.service-filter-item__toggle__label'),
                $checkboxes = $(this).find('input');

            $(this).find('input:checked + label').each(function() {
                value.push($(this).text());
            });

            if (!value.length || $checkboxes.length == value.length) {$label.text('Tous'); return;};
            $label.text(value.join(', '));
        });
    }
}