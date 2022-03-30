/* 米拓企业建站系统 Copyright (C) 长沙米拓信息技术有限公司 (https://www.metinfo.cn). All rights reserved. */
;(function() {
  var that = $.extend(true, {}, admin_module);
  TEMPLOADFUNS[that.hash] = function() {
    init(1)
  }
  function init() {
    $.ajax({
      url: that.own_name + '&c=pseudo_static&a=doGetPseudoStatic',
      type: 'GET',
      dataType: 'json',
      success: function(result) {
        let data = result.data
        Object.keys(data).map(item => {
          if (item === 'met_pseudo' || item === 'met_defult_lang') {
            if (data[item] !== that.obj.find(`[name="${item}"]`).val()) $(`[name="${item}"]`).click()
            return
          }
          that.obj.find(`[name=${item}]`).val(data[item])
        })
      }
    })
  }
  M.component.modal_options['.pseudostatic-modal']={
    modalRefresh:'one',
    modalFullheight:1,
    callback:(key)=>{
      if(!$(key+' .modal-body pre').length) $.ajax({
        url: that.own_name + '&c=pseudo_static&a=doSavePseudoStatic',
        type: 'POST',
        dataType: 'json',
        data: {
          pseudo_download: 1
        },
        success: function(result) {
          let data = result.data
          $(key+' .modal-body').html(`<pre class='mb-0'>${data}</pre>`);
        }
      });
    }
  }
})()
