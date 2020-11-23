;(function() {
    var that = $.extend(true, {}, admin_module);
    that.obj.on('change', 'select[name="met_online_skin"]', function(event) {
        var view = $('option[value="' + $(this).val() + '"]', this).data('view')
        $(this).parent().find('a').attr('href', view).find('img').attr('src', view);
    });
})();