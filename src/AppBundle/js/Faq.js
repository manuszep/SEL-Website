var $ = require('jquery');

export class Faq {
    constructor() {
        this._cache = {};

        this._cache.list = $('.faq-list');
        this._cache.questions = this._cache.list.find('.question');
        this._cache.answers = this._cache.list.find('.answer');

        this._cache.answers.hide();

        this.setupEvents();
    }

    setupEvents() {
        let self = this;
        this._cache.questions.on('click.selFaq', function() {
            self.handleQuestionClick($(this));
        });
    }

    handleQuestionClick($question) {
        let $answer = $question.siblings('.answer');

        this._cache.answers.not($answer).slideUp();
        $answer.slideDown();
    }
}
