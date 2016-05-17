import {Filter} from './Filter.js';
import {Form} from './Form.js';
import {Tab} from './Tab.js';
import {Flash} from './Flash.js';
import {Disqus} from './Disqus.js';

let $ = require('jquery'),
    Masonry = require('masonry-layout');

$('body').addClass('js');

let $grid = $('.grid');

if ($grid.length) {
    $grid.each(function() {
        new Masonry(this, {
            itemSelector: 'article',
            columnWidth: 'article'
        });
    });

    /* Relayout when images are loaded to avoid overlapping items */
    $grid.imagesLoaded( function() {
        $grid.masonry('layout');
    });
}

let filter = new Filter();
let form = new Form();
let tab = new Tab();
let flash = new Flash();
let disqus = new Disqus();