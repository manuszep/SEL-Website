var $ = require('jquery'),
    Masonry = require('masonry-layout');

var msnry = new Masonry('.grid', {
    itemSelector: 'article',
    columnWidth: 'article'
});