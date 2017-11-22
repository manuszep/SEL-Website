var $ = require('jquery');

export class FormSerializer {
    constructor(form_id) {
        let form = (form_id instanceof jQuery) ? form_id : $('#' + form_id);
        this._cache = {
            form: form,
            formFields: form.find('input, select, textarea')
        };
    }

    saveFormData() {
        let data = {};

        $.each(this._cache.formFields, function() {
            let id = $(this).attr("id");
            let name = $(this).attr("name");
            let type = $(this).attr("type");

            if (id && (this.checked || /select|textarea/i.test(this.nodeName) || /text|hidden|password/i.test(this.type))) {
                data[id] = {
                    name: name,
                    value: $(this).val()
                }
            } else if (id && type != 'radio') {
                data[id] = {
                    name: name,
                    value: ""
                }
            }
        });

        this.form_data = data;
        this.form_request = this.convertFormDataToRequest(data);
    }

    convertFormDataToRequest(data) {
        let request = "?";

        for (var key in data) {
            request += data[key].name + "=" + data[key].value + "&";
        }

        return encodeURI(request.replace(/&\s*$/, ""));
    }

    restoreFormData() {
        // Loop all form elements
        this._cache.formFields.each(function(key, field) {
            let id = field.id; // Get element id
            let val = "";

            // The form_data object may be null or the value may not exist.
            // Try to use the stored data but keep null if nothing is found.
            try {val = this.form_data[id].value;} catch(e) {}

            // We don't want to alter disabled fields
            if (field.disabled) return;

            if(field.type == 'checkbox' || field.type == 'radio') {
                $(field).prop("checked", (val == $(field).val()));
            } else {
                $(field).val(val);
            }
        }.bind(this));
    }

    setFormData(data) {
        this.form_data = data;
    }

    getFormData() {
        return this.form_data;
    }

    getRequest() {
        return this.form_request;
    }
}
