;(function() {
  var that = $.extend(true, {}, admin_module)
  that.obj.on('change','[name="advanced_search_range"]',function(e) {
    const advanced_search_range_2 = that.obj.find('.advanced_search_range_2')
    const value = e.target.value
      if (value === 'parent') {
        advanced_search_range_2.removeClass('hide')
      } else {
        advanced_search_range_2.addClass('hide')
      }
  })
})()
