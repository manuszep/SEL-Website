var $ = require('jquery');

export class Tab {
    constructor() {
        let current_index = window.location.hash.replace("#section", "");

        this._cache = {};

        this._cache.tabbedSections = $('.tabbed-sections');

        if (!this._cache.tabbedSections.length) return;

        this._cache.sections = this._cache.tabbedSections.find('[data-tab-title]');
        this._cache.navigationLinks = $();

        this.buildNavigation();

        if(typeof this._cache.navigationLinks[current_index] !== 'undefined') {
            this.enableTab(this._cache.navigationLinks.eq(current_index), true);
        } else {
            this.enableTab(this._cache.navigationLinks.eq(0), true);
        }

        this.setupEvents();
    }
    
    buildNavigation() {
        let self = this;
        let $nav = $('<nav class="wrapper tab-wrapper"></nav>');
        let $navigation_list = $('<ul class="tabbed-navigation"></ul>');
        let index = 0;
        
        this._cache.sections.each(function() {
            let $this = $(this);
            let $li = $('<li></li>');
            let $a = $('<a href="#section' + index + '">' + $this.data('tab-title') + '</a>');
            $a.data('target', $this);
            $li.append($a);
            $navigation_list.append($li);
            self._cache.navigationLinks = self._cache.navigationLinks.add($a);
            index++;
        });
        
        $nav.append($navigation_list);
        this._cache.tabbedSections.prepend($nav);
    }

    setupEvents() {
        let self = this;

        this._cache.navigationLinks.on('click.Tab', function() {
            self.enableTab($(this));
        });

        $(window).on('hashchange', function() {
            let current_hash = window.location.hash;

            if (current_hash.substr(0, 8) === '#section') {
                let current_index = current_hash.replace("#section", "");

                this.enableTab(self._cache.navigationLink.eq(current_index));
            }
        });
    }

    enableTab($link, force = false) {
        let $target = $link.data('target');

        if ($target.is(':visible') && !force) {
            return;
        }

        this._cache.navigationLinks.removeClass('active');
        $link.addClass("active");

        this._cache.sections.filter(":visible").slideUp(200);
        $target.slideDown(300);
    }
}
