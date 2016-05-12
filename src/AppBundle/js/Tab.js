var $ = require('jquery');

export class Tab {
    constructor() {
        this._cache = {};

        this._cache.tabbedSections = $('.tabbed-sections');
        this._cache.sections = this._cache.tabbedSections.find('[data-tab-title]');
        this._cache.navigationLinks = $();

        this.buildNavigation();
        this._cache.sections.not(":eq(0)").hide();
        this.setupEvents();
    }
    
    buildNavigation() {
        let self = this;
        let $nav = $('<nav class="wrapper"></nav>');
        let $navigation_list = $('<ul class="tabbed-navigation"></ul>');
        
        this._cache.sections.each(function() {
            let $this = $(this);
            let $li = $('<li></li>');
            let $a = $('<a href="#">' + $this.data('tab-title') + '</a>');
            $a.data('target', $this);
            $li.append($a);
            $navigation_list.append($li);
            self._cache.navigationLinks = self._cache.navigationLinks.add($a);
        });
        
        $nav.append($navigation_list);
        this._cache.tabbedSections.prepend($nav);
    }

    setupEvents() {
        this._cache.navigationLinks.on('click.Tab', {sections: this._cache.sections}, this.handleLinkClick);
    }

    handleLinkClick(e) {
        let $target = $(this).data('target');

        if ($target.is(':visible')) {
            return;
        }

        e.data.sections.filter(":visible").slideUp(200);
        $target.slideDown(300);
    }
}
