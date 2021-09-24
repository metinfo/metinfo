/* 米拓企业建站系统 Copyright (C) 长沙米拓信息技术有限公司 (https://www.metinfo.cn). All rights reserved. */
;(function() {
    var that = $.extend(true, {}, admin_module)
    renderTable()
    unzipData()
    importData()
    deleteData()

    function renderTable() {
        M.component.commonList(function(thats, table_order) {
            return {
                ajax: {
                    dataSrc: function(result) {
                        if (result.status === 403) {
                            return []
                        }
                        that.data = result
                        let newData = []
                        that.data &&
                        $.each(that.data, function(index, val) {
                            let list = [
                                index,
                                `<a style="width:350px;overflow:hidden;white-space: nowrap;text-overflow: ellipsis;display:block;" title="${val.filename}">${val.filename}</a>`,
                                val.typename,
                                val.db_type,
                                val.ver,
                                `${val.filesize}MB`,
                                val.maketime,
                                val.number,
                                `
                ${val.unzip_url ? `<button class="btn btn-primary ml-2 btn-unzip" data-api="${val.unzip_url}">${METLANG.webupate7}</a>` : ''}
                ${
                                    val.import_url
                                        ? `<button class="btn btn-primary ml-2 btn-import"
                data-index="${index}"
                >${METLANG.setdbImportData}</button>`
                                        : val.error_info||''
                                    }
                <button class="btn ml-2 btn-recovery-delete" data-index="${index}">${METLANG.delete}</button>
                <a class="btn btn-default ml-2" href="${val.download_url}">${METLANG.databackup3}</a>
                `
                            ]

                            newData.push(list)
                        })

                        return newData
                    }
                }
            }
        })
        that.obj.find('#recovery-table').on('init.dt', function(event) {
            that.table = datatable['#recovery-table']
        })
    }

    function unzipData() {
        that.obj.find('.btn-unzip').metClickConfirmAjax({
            confirm_text: METLANG.unzip_tips,
            true_fun: function() {
                const api = $(this)[0].el.data('api')
                M.ajax(
                    {
                        url: api
                    },
                    function(result) {
                        metAjaxFun({
                            result: result,
                            true_fun: function() {
                                that.table.ajax.reload()
                            }
                        })
                    }
                )
            }
        })
    }

    function addPercent(modal,one) {
        var precent=one||parseInt(modal.find('.progress-bar').attr('data-precent')||0);
        if (precent < 90 || one) {
            precent++;
            modal.find('.progress-bar').text(precent + '%').css('width', `${precent}%`).attr('data-precent',precent);
            if(that.item_data.number>1){
                var progress_num=`<span class="text-primary">${that.item_data.fileid}/${that.item_data.number}</span>${that.item_data.number==that.item_data.fileid?`<span class="font-size-14 ml-2">${METLANG.js1}</span>`:''}`;
                modal.find('.progress-num').html(progress_num);
            }
            !one && setTimeout(() => {
                addPercent(modal)
            }, 800)
        }
    }

    function importData() {
        $(document).on('click', '#recovery-table .btn-import', function(e) {
            var item=that.data[$(this).data('index')];
            M.ajax(
                {
                    url: item.import_url
                },
                function(result) {
                    metAjaxFun({
                        result: result,
                        true_fun: function() {
                            that.item_data={
                                import1 : result.import_1,
                                import2 : result.import_2,
                                number:parseInt(item.number),
                                fileid:0
                            }
                            let modal = $('.import-modal')
                            if (modal.length === 0) {
                                $('body').append(
                                    M.component.modalFun({
                                        modalTitle: METLANG.setdbImportData,
                                        modal_class: '.import-modal',
                                        modalUrl: 'databack/import',
                                        modalOktext: METLANG.confirm,
                                        modalFooterok: 0
                                    })
                                )
                                modal = $('.import-modal')
                                modal.modal()
                            } else {
                                modal.modal()
                            }
                        },
                        false_fun: function() {}
                    })
                }
            )
        })
        M.component.modal_options['.import-modal'] = {
            callback: function() {
                const modal = $('.import-modal')
                setTimeout(() => {
                    renderImportModal(modal)
                }, 230)
            }
        }
        function renderImportModal(modal) {
            let html =
                `
      <div class="p-2">
      <h4 class="h5">${METLANG.being_imported}<span class="progress-num ml-2"></span></h4>
      <div class="progress">
      <div class="progress-bar progress-bar-striped" role="progressbar" style="width: 0%"></div>
      </div>
      </div>
    `
            modal.find('.import1,.import2').click(function() {
                M.ajax(
                    {
                        url: that.item_data['import'+($(this).hasClass('import1')?1:2)]
                    },
                    function(result) {
                        continueBack(result,modal)
                    }
                );
                modal.find('.met-import').html(html)
                addPercent(modal);
            })
        }
    }

    function continueBack(result,modal) {
        if (result.status === 2) {
            that.item_data.fileid=result.fileid-1;
            var percent=parseInt(parseInt(that.item_data.fileid)/parseInt(that.item_data.number)*100);
            addPercent(modal,percent-1);
            M.ajax(
                {
                    url: `${result.call_url}`
                },
                function(result) {
                    continueBack(result,modal)
                }
            )
        }
        if (result.status === 1) {
            metAjaxFun({
                result: result,
                true_reload:1
            });
        }
        if (result.status === 0) {
            metAjaxFun({
                result: result,
                true_fun: function() {
                    that.table.ajax.reload()
                }
            })
        }
    }

    function deleteData() {
        that.obj.find('.btn-recovery-delete').metClickConfirmAjax({
            true_fun: function() {
                const index = $(this)[0].el.data('index')
                M.ajax(
                    {
                        url: that.data[index].del_url
                    },
                    function(result) {
                        metAjaxFun({
                            result: result,
                            true_fun: function() {
                                that.table.ajax.reload()
                            }
                        })
                    }
                )
            }
        })
    }

    window.recoveryFileFun = function(obj) {
        TEMPLOADFUNS[that.hash]()
    }
})()
