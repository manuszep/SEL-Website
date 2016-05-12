var $ = require('jquery'),
    Masonry = require('masonry-layout');

import {Filter} from './Filter.js';

var msnry = new Masonry('.grid', {
    itemSelector: 'article',
    columnWidth: 'article'
});

var filter = new Filter();
