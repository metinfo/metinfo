;(function() {
  var that = $.extend(true, {}, admin_module)
  that.obj.on('change','[name="global_search_range"]',function(e) {
    const checked = that.obj.find('#global_search_range_1').data('checked')
    const module_collapse = that.obj.find('#module-collapse')
    const column_collapse = that.obj.find('#column-collapse')
    module_collapse.removeClass('show')
    column_collapse.removeClass('show')
    const value = e.target.value
    if (value === 'module') module_collapse.addClass('show');
    if (value === 'column') column_collapse.addClass('show');
  })
})()
