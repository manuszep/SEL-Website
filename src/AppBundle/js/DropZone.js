var $ = require('jquery');
var DZ = require("./Lib/DropZone");

export class DropZone {
    constructor($source) {
        var $proto = $($source.find('[data-prototype]').data('prototype'));
        var $input = $proto.find('input[type=file]').detach();
        $proto.find('input[type=hidden]').remove();

        this.input_index = 0;

        this.setupMarkup($source, $proto);

        var dzw = $source.find(".drop-zone-wrapper");

        this.setupGlobalEvents();
        if (!dzw.length) return;

        DZ.autoDiscover = false;

        this._cache = {
            Source: $source,
            DropZoneWrapper: dzw,
            FileInput: $input,
            DropZoneElement: $('<div class="dropzone"></div>').appendTo(dzw),
            Submit: $('[type=submit]')
        };

        this.uploadUrl = this._cache.DropZoneWrapper.data('upload-url');
        this.removeUrl = this._cache.DropZoneWrapper.data('remove-url');

        this.paramName = this._cache.FileInput.attr('name');
        //this._cache.FileInput.remove();

        this.setupDropZone();
        this.setupEvents();
    }

    setupMarkup($source, $proto) {
        var $inputs = $source.find('input[type=file]');
        $inputs.each(function() {
            var path = $(this).data('path');
            $(this).attr('type', 'hidden');
            $(this).val(path);
        });

        this.input_index = $inputs.length;

        $source.append($proto);
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
            console.log(data);
            var new_input = this.createInput(data.path);

            file.path = data.path;
            file.field_id = new_input.index;

            this._cache.DropZoneWrapper.append(new_input.markup);
        }.bind(this));

        this.DropZone.on('removedfile', function(file) {
            this._cache.Submit.addClass('loading');
            this._cache.Submit.attr('disabled', true);

            var field_name = this.paramName.replace(/__name__/g, file.field_id);
            this._cache.Source.find('[name="' + field_name + '"]').remove();

            /*var _xhr = $.ajax({
                url: this.removeUrl + '&path=' + file.path + '&index=' + file.field_id,
                cache: false
            }).complete(function(data) {


                this._cache.Submit.removeClass('loading');
                this._cache.Submit.attr('disabled', false);
            }.bind(this));*/
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

    setupGlobalEvents() {
        $('.file-delete').on('click', function(e) {
            e.preventDefault();
            var $link = $(e.target);
            var $row = $link.parents('.uploaded-file');

            if ($row.hasClass('single-file')) {
                var subfolder_input_name = $row.find('input').attr('name').replace('[file]', '[subfolder]');
                var $folder_input = $('input[name="' + subfolder_input_name + '"]');
                // For a single file, we need to pass the folder input with 'delete' value
                var value = $folder_input.data('value') || $folder_input.val();
                $folder_input.data('value', value);
                $folder_input.val('delete');

                $row.find('.file-row').remove();
            } else {
                // For a multi file, the folder input can be removed altogether
                //$folder_input.remove();
                $row.parents().eq(2).remove();
            }
        }.bind(this));

        $('input[type=file]').on('change', function(e) {
            var $input = $(e.target);
            var val = $input.val();
            var subfolder_input_name = $input.attr('name').replace('[file]', '[subfolder]');
            var $folder_input = $('input[name="' + subfolder_input_name + '"]');
            var folder_value = $folder_input.data('value');

            console.log(val, $folder_input.val(), folder_value);
            if (val && $folder_input.val() == 'delete' && folder_value) {
                $folder_input.val(folder_value);
            }
        });
    }

    createInput(path) {
        var name = this.paramName.replace(/__name__/g, this.input_index);
        return {
            markup: '<input type="hidden" name="' + name + '" value="' + path + '"/>',
            index: this.input_index++
        };
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