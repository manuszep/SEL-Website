var $ = require('jquery');

export class Faq {
    constructor() {
        this._cache = {};

        this._cache.list = $('.faq-list');
        this._cache.questions = $('.faq-list .question');
        this._cache.questions_actions = $('.faq-list .question a');
        this._cache.answers = $('.faq-list .answer');

        this._cache.answers.hide();

        this.setupEvents();
    }

    setupEvents() {
        let self = this;
        this._cache.questions_actions.on('click.selFaq', function(e) {
            e.stopPropagation();
        });

        this._cache.questions.on('click.selFaq', function(e) {
            self.handleQuestionClick($(this));
        });
    }

    handleQuestionClick($question) {
        let $answer = $question.siblings('.answer');

        this._cache.answers.not($answer).slideUp();
        $answer.slideDown();
    }
}
