import {Filter} from './Filter.js';
import {Form} from './Form.js';
import {Tab} from './Tab.js';
import {Flash} from './Flash.js';
import {Disqus} from './Disqus.js';
import {Faq} from './Faq.js';
import {AddTool} from './AddTool';
import {Services} from './Services';

let $ = require('jquery'),
    Masonry = require('masonry-layout'),
    imagesLoaded = require('imagesloaded'),
    jQueryBridget = require('jquery-bridget'),
    svg4everybody = require('svg4everybody'),
    inputMask = require('jquery.inputmask'),
    SEL = {};

window.jQuery = window.$ = $;
imagesLoaded.makeJQueryPlugin( $ );
jQueryBridget( 'masonry', Masonry, $ );
svg4everybody();

$('body').removeClass('nojs').addClass('js');

let filter = new Filter();
let form = new Form();
let tab = new Tab();
let flash = new Flash();
let disqus = new Disqus();
let faq = new Faq();
let add_tool = new AddTool();

window.Services = new Services();

SEL.initMasonry = function(fade) {
    fade = typeof fade === 'undefined' ? true : fade;

    SEL.$grid = $('.masonry');
    let $articles = SEL.$grid.find('article');

    if (SEL.$grid.length) {
        if (fade) $articles.hide();

        /* Relayout when images are loaded to avoid overlapping items */
        SEL.$grid.imagesLoaded( function() {
            if (fade) $articles.fadeIn(500);

            var g = SEL.$grid.masonry({
                itemSelector: 'article',
                columnWidth: 'article'
            });
        });
    }
};

SEL.initMasonry();

require('trumbowyg');
$.trumbowyg.svgPath = '/img/icons-wysiwyg.svg';


$('.wysiwyg').trumbowyg({
    fullscreenable: false,
    closable: false,
    lang: 'fr',
    removeformatPasted: true,
    btns: ['viewHTML', '|', 'undo', 'redo', '|', 'btnGrp-lists', '|', 'bold', 'italic', '|', 'link', '|', 'removeformat']
});


require ('air-datepicker');
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