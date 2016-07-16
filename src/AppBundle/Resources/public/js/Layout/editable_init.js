
// x-editable
$.fn.editable.defaults.mode = 'inline';
$.fn.editable.defaults.onblur = 'submit';
$.fn.editable.defaults.emptytext = '-';

// x-editable templates
$.fn.editableform.template = '<form class="form-inline editableform"> <div class="control-group"> <div><div class="editable-input ui mini input"></div><div class="editable-buttons"></div></div> <div class="ui red message editable-error-block"></div> </div> </form>';
$.fn.editableform.buttons  = '<button type="submit" class="editable-submit ui tiny circular green icon button"><i class="checkmark icon"></i></button> <button type="button" class="editable-cancel ui tiny circular red icon button"><i class="remove icon"></i></button>';

//todo ca devrais pas etre la... d
$(document).ready(function() {
    $('.xeditable').editable({
        success: function(response, newValue) {
            if(!response.success) return response.msg;
        }
    });
});