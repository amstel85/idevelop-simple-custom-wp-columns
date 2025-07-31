jQuery(document).ready(function($) {
    // Placeholder for admin-specific JavaScript.
    // This will handle dynamic UI interactions for column configuration.

    // Example: Show/hide sections based on post type selection.
    $('#scwc_post_type_select').on('change', function() {
        var selectedPostType = $(this).val();
        $('.scwc-column-config-section').hide();
        $('#scwc-config-' + selectedPostType).show();
    });

    // Trigger change on load to show initial section.
    $('#scwc_post_type_select').trigger('change');
});