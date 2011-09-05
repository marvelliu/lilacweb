function sharetoAll(url,title)
{
  var f='http://www.lilacbbs.com/'+url;
  var a=[];
  a.push(sharetosohu(f,title));
  a.push(sharetotencent(f,title));
  a.push(sharetosina(f,title));
  return '[·ÖÏíµ½ ' + a.join(' ') + ']';
}

function sharetosohu(url,title)
{
  return '<a title="·ÖÏíµ½ËÑºüÎ¢²©" href="javascript:void((function(s,d,e,r,l,p,t,z,c){var f=\'http://t.sohu.com/third/post.jsp?\',u=z||d.location,p=[\'&url='+escape(escape(url))+'&title='+escape(title)+'&content=\',c||\'utf-8\',\'&pic=\',e(p||\'\')].join(\'\');function%20a(){if(!window.open([f,p].join(\'\'),\'mb\',[\'toolbar=0,status=0,resizable=1,width=660,height=470,left=\',(s.width-660)/2,\',top=\',(s.height-470)/2].join(\'\')))u.href=[f,p].join(\'\');};if(/Firefox/.test(navigator.userAgent))setTimeout(a,0);else%20a();})(screen,document,encodeURIComponent,\'\',\'\',\'\',\'\',\'\',\'GBK\'));" ><span style="display:inline-block;width:16px;height:16px;margin:0 3px 0.2em ;vertical-align:middle;background:url(http://s2.cr.itc.cn/img/t/152.png) no-repeat"></span>ËÑºüÎ¢²©</a>';
}

function sharetotencent(url,title)
{
  return '<a onclick="(function(){var _t = \''+encodeURI(title)+'\';var _url = \''+encodeURIComponent(url)+'\';var _appkey = \''+encodeURI('185c1394b1bc4fdd905cdf3ca861b366')+'\';var _pic = \''+encodeURI('')+'\';var _site = \'http://www.newsmth.net\';var _u = \'http://v.t.qq.com/share/share.php?url=\'+_url+\'&appkey=\'+_appkey+\'&site=\'+_site+\'&pic=\'+_pic+\'&title=\'+_t;window.open( _u,\'\', \'width=700, height=680, top=0, left=0, toolbar=no, menubar=no, scrollbars=no, location=yes, resizable=no, status=no\');})();" href="javascript:void(0)" style="height: 16px; font-size: 12px; line-height: 16px;" title="·ÖÏíµ½ÌÚÑ¶Î¢²©"><img align="absmiddle" src="http://v.t.qq.com/share/images/s/weiboicon16.png">ÌÚÑ¶Î¢²©</a>';
}

function sharetosina(url,title)
{
  var param = {
    url:url,
    appkey:'57541378',
    title:title, 
    pic:'', 
    ralateUid:''
  };
  var temp = [];
  for( var p in param ){
    temp.push(p + '=' + encodeURIComponent( param[p] || '' ) );
  }
  var open_url = 'http://service.t.sina.com.cn/share/share.php?'+temp.join('&');
  return '<a onclick="(function(){window.open(\''+open_url+'\',\'_blank\',\'width=615,height=505\');})();" href="javascript:void(0)" style="height: 16px; font-size: 12px; line-height: 16px;" title="·ÖÏíµ½ĞÂÀËÎ¢²©"><span style="display: inline-block; width: 16px; height: 16px; vertical-align: middle; background: url(http://img.t.sinajs.cn/t3/appstyle/opent/images/app/btn_trans.gif) no-repeat scroll 0pt -56px transparent;"></span>ĞÂÀËÎ¢²©</a>';
}
