; (function ($) {
    $(document).ready(function () {
        $(".action-button").on('click', function () {
            let task = $(this).data('task');
            let params = { "action": "roles_display_result", "nonce": plugindata.nonce, "task": task };
            $.post(plugindata.ajax_url, params, function (data) {
                $("#roles_capabilities_result").html("<pre>" + data + "</pre>").show();
            });
        });
    });
})(jQuery);