import wx from 'weixin-js-sdk'
export default {
  //检测是否是安卓 排除小程序
  checkAndroid : function (){
    var u = navigator.userAgent;
    var isAndroid = u.indexOf('Android') > -1 || u.indexOf('Adr') > -1; //android终端
    if (!isAndroid) {
      return false;
    }

    var ua = window.navigator.userAgent.toLowerCase();
    var check_min = true;
    if (ua.match(/MicroMessenger/i) == 'micromessenger') {    //判断是否是微信环境
      wx.miniProgram.getEnv(function (resMini) {
        if (!resMini.miniprogram) {
          check_min = false;
        }
      })
    }
    if (!check_min) {
      return false;
    } else {
      return true;
    }
  },
}
