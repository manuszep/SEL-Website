var $ = require('jquery');
var DZ = require("./Lib/DropZone");

export class DropZone {
    constructor() {
        var dzw = $("#DropZoneWrapper");

        if (!dzw.length) return;

        DZ.autoDiscover = false;

        this._cache = {
            DropZoneWrapper: dzw,
            FileInput: dzw.find('input[type=file]'),
            DropZoneElement: $('<div class="dropzone"></div>').appendTo(dzw),
            Submit: $('[type=submit]')
        };

        this.uploadUrl = this._cache.DropZoneWrapper.data('upload-url');
        this.removeUrl = this._cache.DropZoneWrapper.data('remove-url');

        this.paramName = this._cache.FileInput.attr('name');
        this._cache.FileInput.remove();

        this.setupDropZone();
        this.setupEvents();
    }

    setupDropZone() {
        this.DropZone = new DZ(this._cache.DropZoneElement[0], {
            url: this.uploadUrl,
            paramName: this.paramName,
            dictDefaultMessage: 'Déposez des fichiers ici',
            addRemoveLinks: true,
            dictRemoveFile: 'Supprimer'
        });
    }

    setupEvents() {
        this.DropZone.on("success", function(file, data) {
            file.path = data.path;
            this._cache.DropZoneWrapper.append('<input type="hidden" name="' + this.paramName + '" value="' + data.path + '"/>');
        }.bind(this));

        this.DropZone.on('removedfile', function(file) {
            this._cache.Submit.addClass('loading');
            this._cache.Submit.attr('disabled', true);
            var _xhr = $.ajax({
                url: this.removeUrl + '&path=' + file.path,
                cache: false
            }).complete(function() {
                this._cache.Submit.removeClass('loading');
                this._cache.Submit.attr('disabled', false);
            }.bind(this));
        }.bind(this));

        this.DropZone.on('processing', function(file) {
            this._cache.Submit.addClass('loading');
            this._cache.Submit.attr('disabled', true);
        }.bind(this));

        this.DropZone.on('queuecomplete', function(file) {
            this._cache.Submit.removeClass('loading');
            this._cache.Submit.attr('disabled', false);
        }.bind(this));
    }
}

/*myDropzone.on("success", function(data) {
    window.dropZoneSuccess(data);
});

myDropzone.on("error", function(data) {
    window.Flash.addFlash("error", 'Une erreur est survenue');
});

myDropzone.on('removedfile', function(data) {
    window.dropZoneRemove(data);
});*/