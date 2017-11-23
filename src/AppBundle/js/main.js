import {Form} from './Form.js';
import {Tab} from './Tab.js';
import {Flash} from './Flash.js';
import {Disqus} from './Disqus.js';
import {Faq} from './Faq.js';
import {AddTool} from './AddTool';
import {Services} from './Services';
import {Stats} from './Stats';
import {DropZone} from './DropZone';

let $ = require('jquery'),
    Masonry = require('masonry-layout'),
    imagesLoaded = require('imagesloaded'),
    jQueryBridget = require('jquery-bridget'),
    svg4everybody = require('svg4everybody'),
    inputMask = require('jquery.inputmask'),
    SEL = {},
    Chart = require('chart.js');

window.jQuery = window.$ = $;
window.Chart = Chart;
imagesLoaded.makeJQueryPlugin( $ );
jQueryBridget( 'masonry', Masonry, $ );
jQueryBridget( 'dxChart', Chart, $ );
svg4everybody();

$('body').removeClass('nojs').addClass('js');

let form = new Form();
let tab = new Tab();
window.Flash = new Flash();
let disqus = new Disqus();
let faq = new Faq();
let add_tool = new AddTool();

window.Services = new Services();
window.Stats = new Stats();

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
$.trumbowyg.btnsGrps = {
    design:   ['bold', 'italic', 'underline', 'strikethrough'],
    semantic: ['strong', 'em'],
    justify:  ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
    lists:    ['unorderedList', 'orderedList'],
    headers: {
        dropdown: ['h2', 'h3', 'h4']
    }
};
$.trumbowyg.svgPath = '/img/icons-wysiwyg.svg';


$('.wysiwyg').trumbowyg({
    fullscreenable: false,
    closable: false,
    lang: 'fr',
    removeformatPasted: true,
    btnsDef: {
        // Customizables dropdowns
        formattingLight: {
            dropdown: ['p', 'h2', 'h3', 'h4', 'h5'],
            ico: 'p'
        }
    },
    btns: [
        ['viewHTML'],
        ['undo', 'redo'],
        'formattingLight',
        'btnGrp-lists',
        ['bold', 'italic'],
        ['link'],
        ['removeformat']]
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

$('.drop-zone-collection').each(function() {
    $(this).data('dropzone', new DropZone($(this)));
})

// Recaptcha


window.SelonReCaptchaSuccess = function() {
    var errorDivs = document.getElementsByClassName('recaptcha-error');
    if (errorDivs.length) {
        errorDivs[0].className = '';
    }

    var errorMsgs = document.getElementsByClassName('recaptcha-error-message');
    if (errorMsgs.length) {
        errorMsgs[0].parentNode.removeChild(errorMsgs[0]);
    }

    var forms = document.getElementsByClassName('recaptcha-form');
    if (forms.length) {
        var recaptchaSubmitEvent = document.createEvent('Event');
        recaptchaSubmitEvent.initEvent('submit', true, true);
        forms[0].addEventListener('submit', function (e) {
            e.target.submit();
        }, false);
        forms[0].dispatchEvent(recaptchaSubmitEvent);
    }
};