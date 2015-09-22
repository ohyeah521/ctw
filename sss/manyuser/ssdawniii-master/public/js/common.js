$(function(){
  //搜索
  $('#search').click(function() {
    var email = $(this).siblings('div').find('input').val();
    var url = location.href;
    if (url.indexOf('&email') > 0) {
      var postion = url.indexOf('&email');
      url = url.substring(0,postion);
    }
    url += '&email='+email;
    location.href = url;
  });

  //锁定用户
  $('.lock').click(function() {
    if(confirm('确定锁定吗？')) {
      $(this).siblings('.lockform').submit();
    }
  });

  //解锁用户
  $('.unlock').click(function() {
    if(confirm('确定解锁吗？')) {
      $(this).siblings('.unlockform').submit();
    }
  });

  //修改运行密码
  $('.changeRunpwd').click(function() {
    if(confirm('确定重置密码吗？重置密码后要稍等几分钟，才能生效')) {
      $(this).siblings('form').submit();
    }
  });
});
