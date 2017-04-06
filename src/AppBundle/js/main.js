import {Filter} from './Filter.js';
import {Form} from './Form.js';
import {Tab} from './Tab.js';
import {Flash} from './Flash.js';
import {Disqus} from './Disqus.js';
import {Faq} from './Faq.js';
import {AddTool} from './AddTool';

let $ = require('jquery'),
    Masonry = require('masonry-layout'),
    imagesLoaded = require('imagesloaded'),
    jQueryBridget = require('jquery-bridget'),
    inputMask = require('jquery.inputmask'),
    svg4eferybody = require('svg4everybody');

window.jQuery = window.$ = $;
svg4eferybody();

require('trumbowyg');
require ('air-datepicker');


imagesLoaded.makeJQueryPlugin( $ );
jQueryBridget( 'masonry', Masonry, $ );

$('body').removeClass('nojs').addClass('js');

let $grid = $('.masonry');
let $articles = $grid.find('article');

if ($grid.length) {
    $articles.hide();

    /* Relayout when images are loaded to avoid overlapping items */
    $grid.imagesLoaded( function() {
        $articles.fadeIn(500);

        $grid.masonry({
            itemSelector: 'article',
            columnWidth: 'article'
        });

        //$grid.masonry('layout');
    });
}

let filter = new Filter();
let form = new Form();
let tab = new Tab();
let flash = new Flash();
let disqus = new Disqus();
let faq = new Faq();
let add_tool = new AddTool();

$.trumbowyg.svgPath = '/img/icons-wysiwyg.svg';


$('.wysiwyg').trumbowyg({
    fullscreenable: false,
    closable: false,
    lang: 'fr',
    removeformatPasted: true,
    btns: ['viewHTML', '|', 'undo', 'redo', '|', 'btnGrp-lists', '|', 'bold', 'italic', '|', 'link', '|', 'removeformat']
});

$('[data-inputmask-regex]').inputmask("Regex");

$.fn.datepicker.language['fr'] = {
    days: ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'],
    daysShort: ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'],
    daysMin: ['Di', 'Lu', 'Ma', 'Me', 'Je', 'Ve', 'Sa'],
    months: ['Janvier','Février','Mars','Avril','Mai','Juin', 'Juillet','Août','Septembre','Octobre','Novembre','Décembre'],
    monthsShort: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jui', 'Jui', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'],
    today: 'Aujourd\'hui',
    clear: 'Effacer',
    dateFormat: 'dd/mm/yyyy',
    timeFormat: 'hh:ii aa',
    firstDay: 1
};

$('[data-datepicker]').datepicker({
    language: 'fr'
});

$('[data-datepicker-future]').datepicker({
    language: 'fr',
    minDate: new Date()
});