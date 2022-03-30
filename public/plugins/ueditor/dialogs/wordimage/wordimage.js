/**
 * Created by JetBrains PhpStorm.
 * User: taoqili
 * @Date：2022-03-14 17:03:28
 * Time: 下午12:50
 * To change this template use File | Settings | File Templates.
 */



var wordImage = {};
//(function(){
wordImage.init = function() {
	showLocalPath("localPath");
	document.getElementById('clipboard').onclick=function(){
		copyToClipboard(document.getElementById('localPath').value);
	};
};
function showLocalPath(id) {
    //单张编辑
    var img = editor.selection.getRange().getClosedNode();
    var images = editor.execCommand('wordimage');
    if(images.length==1 || (img && img.tagName == 'IMG')){
		document.getElementById(id).value =img?img.getAttribute("word_img"):images[0];
        return;
    }
	var path = images[0];
    var leftSlashIndex  = path.lastIndexOf("/")||0,  //不同版本的doc和浏览器都可能影响到这个符号，故直接判断两种
        rightSlashIndex = path.lastIndexOf("\\")||0,
        separater = leftSlashIndex > rightSlashIndex ? "/":"\\" ;

	path = path.substring(0, path.lastIndexOf(separater)+1);
	document.getElementById(id).value = path;
}
//})();
// 设置剪贴板文字
function copyToClipboard(text){
    if(window.clipboardData){
       window.clipboardData.setData('text',text);
    }else{
       (function(text){
          document.oncopy=function(e){
             e.clipboardData.setData('text',text);
             e.preventDefault();
             document.oncopy=null;
          }
       })(text);
       document.execCommand('Copy');
    }
	if(top.M && top.M.load){
		top.M.load('alertify',()=>{
			top.alertify.success('复制成功');
		})
	}else{
		alert('复制成功');
	}
}