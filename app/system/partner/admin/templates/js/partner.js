;(function() {
    var that = $.extend(true, {}, admin_module);
    // 分类列表初始化
    function renderNavlist() {
        metui.ajax({
            url: that.own_name + 'c=index&a=doCategory'
        }, function(result) {
            if (parseInt(result.status)) {
                var nav_html = '',
                    content_html='';
                $.each(result.data, function(index, val) {
                    nav_html+=`<a class="nav-link ${index?'':'active'}" href="#met-partner-tab-pane-${val.pid}" data-toggle="tab" data-pid="${val.pid}">${val.name}</a>`
                    content_html+=`<div class="tab-pane fade ${index?'':'show active'}" id="met-partner-tab-pane-${val.pid}"></div>`
                });
                that.obj.find('.nav').html(nav_html);
                that.obj.find('.tab-content').html(content_html);
                that.obj.find('.nav a:first-child').click();
            }
        })
    }
    // 切换分类
    that.obj.on('click', '.nav a', function(event) {
        var $tab_pane=that.obj.find(`.tab-content ${$(this).attr('href')}`);
        if(!$tab_pane.html()){
            metui.ajax({
                url: that.own_name + 'c=index&a=doindex',
                data:{pid:$(this).data('pid')}
            }, function(result) {
                if (parseInt(result.status)) {
                    let html = '';
                    if(result.msg) html+=`<div class="msg">${result.msg}</div>${html}`;
                    html+='<div class="row mt-3 list px-2">';
                    result.data.data.map(item => {
                        const card = `<div class="col-12 col-md-6 col-lg-4 col-xl-3 px-2 mb-3">
                        <div class="media" >
                          <div class="body">
                            <a href="${item.homepage ? item.homepage : ''}" class="link" target="_blank">
                              <img class="mr-2" src="${item.logo}">
                            </a>
                            <div class="media-body">
                              <h5 class="mt-0 mb-1 h6">
                                <a href="${item.homepage ? item.homepage : ''}" class="link d-block" target="_blank">${item.user_name}</a>
                              </h5>
                              <div class="card-text">${item.service}</div>
                            </div>
                          </div>
                        </div>
                      </div>`
                        html += card
                    });
                    html+='</div>';
                    $tab_pane.html(html);
                }
            })
        }
    })
    renderNavlist();
    TEMPLOADFUNS[that.hash] = function() {
        that.obj.find('.nav a:first-child').click();
    }
})();