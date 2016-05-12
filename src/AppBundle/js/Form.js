var $ = require('jquery');

export class Form {
    constructor() {
        this._cache = {};

        this._cache.deleteButtons = $('.btn-delete');

        this.setupEvents();
    }

    setupEvents() {
        this._cache.deleteButtons.on('click.selForm', this.handleDeleteClick);
    }

    handleDeleteClick(e) {
        let message = $(this).data('confirm-message');
        if (! confirm(message)) {
            e.preventDefault();
            return false;
        }
        
        return true;
    }
}
