var loginHTML = '<div class="login_list">' +
    '<input type="text" class="fz14 c3" data-reg="/^1[3-9]\\d{9}$/" id="tel" data-validation="true" data-error="请输入手机号"  placeholder="请输入手机号">' +
    '</div>' +
    '<div class="login_list">' +
    '<input type="password" class="fz14 c3" placeholder="请输入密码" data-reg="/\\S/" data-validation="true" data-error="请输入密码" placeholder="请输入密码">' +
    '</div>' +
    '<div class="submit_btn">' +
    '<button type="button" class="" onclick="submit(\'asdasdasdd\');">登录</button>' +
    '</div>' +
    '<div class="w314 fz14 lh30 c9">' +
    '<a href="javascript:void(0);" class="yellow" onclick="showMarks(rePassWordHTML);">忘记密码？</a>' +
    '<a href="javascript:void(0);" class="yellow tar" onclick="showMarks(registerHTML);">去注册</a>' +
    '</div>';
var registerHTML = '<div class="login_list">' +
    '<input type="text" class="fz14 c3" data-reg="/^1[3-9]\\d{9}$/" id="tel" data-validation="true" data-error="请输入手机号" placeholder="请输入手机号">' +
    '</div>' +
    '<div class="login_list">' +
    '<div class="oh">' +
    '<div class="code_inp fl">' +
    '<input type="text" data-reg="/\\S/" data-validation="true" data-error="请输入验证码" class="fz14 c3" placeholder="请输入验证码">' +
    '</div>' +
    '<button type="button" class="code_btn" onclick="getCode(\'register\',\'url\');">获取验证码</button>' +
    '</div>' +
    '</div>' +
    '<div class="login_list">' +
    '<input type="password" class="fz14 c3" data-reg="/\\S/" data-validation="true" data-error="请输入密码" placeholder="请输入密码">' +
    '</div>' +
    '<div class="login_list">' +
    '<input type="password" class="fz14 c3" data-reg="/\\S/" data-validation="true" data-error="请确认密码" placeholder="请确认密码">' +
    '</div>' +
    '<div class="submit_btn">' +
    '<button type="button" class="" onclick="submit(\'url\');">注册</button>' +
    '</div>' +
    '<div class="w314 fz14 lh30 c9">已有账号？<a href="javascript:void(0);" class="yellow" onclick="showMarks(loginHTML);">登录</a></div>';

var rePassWordHTML = '<div class="login_list">' +
    '<input type="text" class="fz14 c3" data-reg="/^1[3-9]\\d{9}$/" id="tel" data-validation="true" data-error="请输入手机号"  placeholder="请输入手机号">' +
    '</div>' +
    '<div class="login_list">' +
    '<div class="oh">' +
    '<div class="code_inp fl">' +
    '<input type="text" class="fz14 c3" placeholder="请输入验证码" data-reg="/\\S/" data-validation="true" data-error="请输入验证码">' +
    '</div>' +
    '<button type="button" class="code_btn" onclick="getCode(\'rePassWord\',\'url\');">获取验证码</button>' +
    '</div>' +
    '</div>' +
    '<div class="login_list">' +
    '<input type="password" class="fz14 c3" data-reg="/\\S/" data-validation="true" data-error="请输入密码" placeholder="请输入密码">' +
    '</div>' +
    '<div class="login_list">' +
    '<input type="password" class="fz14 c3" data-reg="/\\S/" data-validation="true" data-error="请确认密码" placeholder="请确认密码">' +
    '</div>' +
    '<div class="submit_btn">' +
    '<button type="button" class="" onclick="submit(\'url\');">找回密码</button>' +
    '</div>' +
    '<div class="w314 fz14 lh30 c9">已有账号？<a href="javascript:void(0);" class="yellow" onclick="showMarks(loginHTML);">登录</a></div>';

function appendHtml(name) {
    var boxHTML = '<div class="login_warpper">' +
        '<div class="login_box height_auto">' +
        '<div class="login_close"><img src="images/close.png" alt="" onclick="close_box()"></div>' +
        '<div class="tac">' +
        '<img src="images/i_03.jpg" alt="" class="mb20">' +
        '</div>' +
        '<div class="hook_tab">' +
        name +
        '</div>' +
        '</div>';
    $("body").append(boxHTML);
}



function close_box() { //关闭层
    $(".login_warpper").remove();
}

function showMarks(name) { //打开登录层
    var dom = $(".login_warpper");
    if (dom.length > 0) { //元素已存在
        $(".hook_tab").html(name);
    } else { //元素不存在
        appendHtml(name);
    }
}

function submit(url) {
    var isSubmit = true;
    $('.login_warpper input').each(function(key, el) {
        if ($(el).attr('data-validation') == 'true') {
            var reg = eval($(el).attr('data-reg'));
            var msg = $(el).attr('data-error');
            var value = $(el).val();
            if (!reg.test(value)) {
                $.alert(msg);
                isSubmit = false;
                return false;
            }
        }
    });
    if (isSubmit) {
        console.log('开始提交');
    }

};

var isGetCode = false; //验证码标注
function getCode(op, url) { //获取验证码
    if (!isGetCode) {
        var userreg = /^1[3-9]\d{9}$/;
        if (!userreg.test($("#tel").val())) {
            $.alert('电话号码不正确')
            return;
        }
        isGetCode = true;
        var time = 60;
        var countDown = setInterval(function() {
            if (time == 1) {
                time = 60;
                $(".code_btn").html('获取验证码');
                clearInterval(countDown);
                isGetCode = false;
            } else {
                time--;
                $(".code_btn").html(time + 's');
            }
        }, 1000);
    }
}