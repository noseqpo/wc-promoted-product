jQuery(document).ready(function() {
    jQuery("#ppd_promote").change(function() {
        jQuery("#ppd_promote_toggle").toggle(this.checked);
    }).change();

    jQuery("#ppd_set_expiry").change(function() {
        jQuery("#ppd_set_expiry_toggle").toggle(this.checked);
    }).change();
});

jQuery(document).ready(function($) {
    function toggleExpiryDateRequired() {
        var expiryDateField = $('#ppd_expiry_date');
        var setExpiryCheckbox = $('#ppd_set_expiry');

        if (setExpiryCheckbox.is(':checked')) {
            expiryDateField.prop('required', true);
        } else {
            expiryDateField.prop('required', false);
        }
    }
    toggleExpiryDateRequired();
    $('#ppd_set_expiry').on('change', toggleExpiryDateRequired);
});