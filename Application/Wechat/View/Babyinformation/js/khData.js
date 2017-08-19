 //公用静态变量  非全局
var i=0;
$(function () {
    //获取本条服务的id值,查询所有评论
    var needSid=sid;
    $.ajax({
        url:'../../detail/sid/'+needSid,
        dataType:'json',
        type:'get',
        success:function(re){

            var itemIndex = 0;

            var tabLoadEndArray = [false, false, false];
            //第一个参数，所有条数
            var tabLenghtArray = [re.length, 15, 47];
            var tabScroolTopArray = [0, 0, 0];

            // dropload
            var dropload = $('.khfxWarp').dropload({
                scrollArea: window,
                domDown: {
                    domClass: 'dropload-down',
                    domRefresh: '<div class="dropload-refresh">上拉加载更多</div>',
                    domLoad: '<div class="dropload-load"><span class="loading"></span>加载中...</div>',
                    domNoData: '<div class="dropload-noData">已无数据</div>'
                },
                loadDownFn: function (me) {
                    setTimeout(function () {
                        if (tabLoadEndArray[itemIndex]) {
                            me.resetload();
                            me.lock();
                            me.noData();
                            me.resetload();
                            return;
                        }
                        var result = '';

                        for (var index = 0; index < 5; index++) {
                            if (tabLenghtArray[itemIndex] > 0) {
                                tabLenghtArray[itemIndex]--;
                            } else {
                                tabLoadEndArray[itemIndex] = true;
                                break;
                            }
                            if (itemIndex == 0) {
                                result
                                    += ''
                                    + '    <div class="weui-cell evaluation">'
                                    + '    <div class="weui-cell__hd">'
                                    + '      <img src="'+re[i].head_portrait+'">'
                                    + '    </div>'
                                    + '    <div class="weui-cell__bd">'
                                    + '        <div class="peopleName"> '
                                    + '            <span class="name1">'+re[i].nickname+'</span> '
                                    + '            <div class="starBox"> '
                                    + '                <span>'+re[i].grade+'</span> '
                                    + '            </div>'
                                    + '        </div>'
                                    + '        <p class="peopleCon">'+re[i].estimate+'</p>'
                                    + '        <p class="peopleTime">'+re[i].ctime+'</p>'
                                    + '    </div>'
                                    + '    </div>'
                                    + '    <script type="text/javascript" src="/Application/Wechat/View/Babyinformation/js/index.js"></script>';
                                i++;
                            } else if (itemIndex == 1) {
                                result
                                    += ''
                                    + '    <div class="weui-cell evaluation">'
                                    + '    <div class="weui-cell__hd">'
                                    + '      <img src="/Application/Wechat/View/Mydata/img/未标题-1.png">'
                                    + '    </div>'
                                    + '    <div class="weui-cell__bd">'
                                    + '        <div class="peopleName"> '
                                    + '            <span class="name1">某某2某某某</span> '
                                    + '            <div class="starBox"> '
                                    + '                <span>好评</span> '
                                    + '            </div>'
                                    + '        </div>'
                                    + '        <p class="peopleCon">好好好</p>'
                                    + '        <p class="peopleTime">2017-06-20 10:57</p>'
                                    + '    </div>'
                                    + '    </div>'
                                    + '    <script type="text/javascript" src="/Application/Wechat/View/Babyinformation/js/index.js"></script>';
                            } else if (itemIndex == 2) {
                                result
                                    += ''
                                    + '    <div class="weui-cell evaluation">'
                                    + '    <div class="weui-cell__hd">'
                                    + '      <img src="/Application/Wechat/View/Mydata/img/未标题-1.png">'
                                    + '    </div>'
                                    + '    <div class="weui-cell__bd">'
                                    + '        <div class="peopleName"> '
                                    + '            <span class="name1">某某某3某某</span> '
                                    + '            <div class="starBox"> '
                                    + '                <span>好评</span> '
                                    + '            </div>'
                                    + '        </div>'
                                    + '        <p class="peopleCon">好好好</p>'
                                    + '        <p class="peopleTime">2017-06-20 10:57</p>'
                                    + '    </div>'
                                    + '    </div>'
                                    + '    <script type="text/javascript" src="/Application/Wechat/View/Babyinformation/js/index.js"></script>';
                            }
                        }
                        $('.khfxPane').eq(itemIndex).append(result);
                        me.resetload();
                    }, 500);
                }
            });
        }
    })

});