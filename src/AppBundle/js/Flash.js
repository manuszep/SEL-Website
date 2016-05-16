var $ = require('jquery');

export class Flash {
    constructor() {
        let self = this;
        this._cache = {};

        this._cache.messagesWrapper = $('.flash-messages');
        this._cache.messages = this._cache.messagesWrapper.find('.message');

        setTimeout(function() {
            self.setupAnimation();
        }, 1000);

    }

    setupAnimation() {
        let self = this;
        this._cache.messagesWrapper.slideDown(200, function() {
            setTimeout(function() {
                self._cache.messages.slideUp(200);
            }, 5000);
        });
    }
}
