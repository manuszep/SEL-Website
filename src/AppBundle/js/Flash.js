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

    addFlash(type, message) {
        var $message = $('<div class="flash-messages"><div class="wrapper"><div class="message ' + type + '">' + message + '</div></div></div>');
        $('.header').prepend($message);
        console.log(message)

        $message.slideDown(200, function() {
            setTimeout(function() {
                $message.slideUp(200);
            }, 5000);
        });
    }
}
