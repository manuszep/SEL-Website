import {Filter} from './Filter.js';
import {Form} from './Form.js';

let $ = require('jquery'),
    Masonry = require('masonry-layout');

let $grid = $('.grid');

if ($grid.length) {
    $grid.each(function() {
        new Masonry(this, {
            itemSelector: 'article',
            columnWidth: 'article'
        });
    });
}

let filter = new Filter();
let form = new Form();
