/*购物车 s */
$(document).ready(function($) {
    var win = window,
    doc = document;
    init();
    function init() {
        render();
        bind()
    }
    function render() {
        renderBackImg('.g-bd')
    }
    function renderBackImg(selector) {
        var $element = $(selector),
        $itmlnk = $('.m-itmlnk'),
        dataimage = $element.data('image'),
        image = 'url("' + $element.data('image') + '")',
        color = $element.data('color');
        if (dataimage) {
            $element.css({
                'background-image': image
            });
            $itmlnk.addClass('z-show')
        } else {
            $itmlnk.addClass('z-hide')
        }
        if (color) {
            $element.css({
                'background-color': color
            })
        }
        $element.css({
            'background-position': 'center 50px'
        })
    }
    function bind() {
        bindCitys();
        bindQuicknav();
        bindCollect();
        bindQrcode();
        bindGoodstab();
        bindEntity();
        /*bindInfonav();*/
        bindTourcity();
        bindSign();
        bindPlaceholder();
        bindLayer();
        bindLayerClose();
        bindSelbox();
        /*bindGoodstips();*/
        bindMantab();
        bindSdfix();
        bindHotRecommend(); (function() {
            $('.m-mask').on('click',
            function() {
                if ($('.m-layer-viewseat').hasClass('z-show')) {
                    common.layer.hide(".m-layer-viewseat")
                }
            })
        })();
        bindStar();
        bindGrade()
    }
    function bindCitys() {
        var $citys = $('.m-citys'),
        $tt = $citys.find('.tt');
        $tt.on('click',
        function() {
            $citys.toggleClass('m-citys-act')
        });
        $(document).on('click',
        function(ev) {
            var ev = ev || window.event,
            target = ev.target || ev.srcElement;
            if (!$(target).parents('.m-citys').size()) {
                $citys.removeClass('m-citys-act')
            }
        })
    }
    function bindSch() {
        var $sch = $('.m-sch'),
        $ipt = $sch.find('.ipt');
        placeholder($ipt)
    }
    function bindQuicknav() {
        var $quicknav = $('.m-quicknav');
        var movein = false;
        var timer = null;
        $quicknav.on('mouseenter',
        function() {
            timer && clearTimeout(timer) && (timer = null) && (movein = true);
            $(this).addClass('m-quicknav-act')
        });
        $quicknav.on('mouseleave',
        function() {
            var self = this;
            movein = false;
            timer = setTimeout(function() {
                if (!movein) $(self).removeClass('m-quicknav-act')
            },
            300)
        })
    }
    function bindCollect() {
        var $picbox = $('div.m-picbox');
        var $collect = $('div.collect');
        $picbox.on('mouseenter',
        function() {
            $collect.addClass('z-show')
        });
        $picbox.on('mouseleave',
        function() {
            $collect.removeClass('z-show')
        });
        $collect.on('mouseenter',
        function() {
            $collect.addClass('collect-hover')
        });
        $collect.on('mouseleave',
        function() {
            $collect.removeClass('collect-hover')
        })
    }
    function bindFlowers() {
        var $flowers = $('.m-flowers'),
        $add = $flowers.find('.add'),
        $num = $flowers.find('.num'),
        $txt = $flowers.find('.txt');
        $flowers.on('click',
        function() {
            if ($flowers.hasClass('m-flowers-act')) {
                return
            }
            $txt.html('已送花');
            $flowers.addClass('m-flowers-clk');
            $add.show().animate({
                'left': '+=' + 10 + 'px',
                'top': '-=' + 10 + 'px',
                'opacity': 0
            },
            500,
            function() {
                $add.hide();
                $flowers.addClass('m-flowers-act');
                $num.html(parseInt($num.html()) + 1)
            })
        })
    }
   
    function bindGoodstab() {
        var $goodstab = $('.m-goodstab'),
        $tabitms = $goodstab.find('.tab .itm-tab'),
        $cntitms = $goodstab.find('.bd .itm-tab');
        $tabitms.on('click',
        function() {
            $tabitms.removeClass('z-crt');
            $cntitms.removeClass('z-show');
            $(this).addClass('z-crt');
            $cntitms.eq($(this).index()).addClass('z-show')
        })
    }
    /*
    function bindInfonav() {
        var $mdetail = $('.m-detail'),
        $minfonav = $('.m-infonav'),
        $hditms = $minfonav.find('.hd .itm-tab'),
        $bditms = $minfonav.find('.bd .itm-tab'),
        navtop = $mdetail.offset().top;
        $hditms.each(function(index, element, ev) {
            $(this).on('click', {},
            function(ev) {
                if (!ev.data.flag) {
                    $hditms.removeClass('z-crt').eq(index).addClass('z-crt');
                    $bditms.removeClass('z-show').eq(index).addClass('z-show');
                    $(window).trigger('scroll');
                    var dataShow = $(this).data('show');
                    if (dataShow) {
                        if (dataShow.indexOf(',') > 0) {
                            var itms = dataShow.split(',');
                            for (var i = 0; i < itms.length; i++) {
                                $bditms.filter("[rel=" + itms[i] + "]").addClass('z-show')
                            }
                        } else {
                            $bditms.filter("[rel=" + dataShow + "]").addClass('z-show')
                        }
                    }
                    if ($minfonav.hasClass('m-infonav-show')) {
                        $(this).attr('href', $(this).data('href'))
                    } else {
                        $(this).attr('href', 'javascript:;')
                    }
                } else $(this).trigger('click', {
                    flag: true
                })
            })
        });
        $(window).on('scroll',
        function() {
            if ($(window).scrollTop() > navtop || $(window).scrollTop() === navtop) $minfonav.addClass('m-infonav-show');
            else $minfonav.removeClass('m-infonav-show')
        });
        if (projectInfo.Tabcontrol != 0) {
            $hditms.removeClass("z-crt").filter("[data-idx=" + projectInfo.Tabcontrol + "]").addClass("z-crt");
            $bditms.removeClass("z-show").filter("[rel=" + projectInfo.Tabcontrol + "]").addClass("z-show")
        }
    }*/
    function formatPriceString(element) {
        var price = $(element).find('.price').html();
        var re = /￥(\d+)/i;
        return re.test(price) ? ('票价' + price.replace(re, '$1元')) : price
    }
    function formatDateString(element) {
        var date = $(element).find('.date').html(),
        week = $(element).find('.week').html(),
        time = $(element).find('.time').html(),
        tmp;
        if (/^\d{2}\./.test(date)) date = '20' + date;
        tmp = date.replace(/[.-\/]/ig, ',').split(',');
        date = [tmp[0], toDouble(tmp[1]), toDouble(tmp[2])].join('-');
        return [date, week, time].join(' ')
    }
    function toDouble(str) {
        return str < 10 ? ('0' + str) : str
    }
    function addCartItm(date, price, stock, maxnum, context) {
        var $mcart = $(context).find('.m-cart');
        var $mproduct = $(context);
        var $lst = $mcart.find('.lst');
        var $itm = $('<li class="itm"><span class="txt txt-datetime">"' + date + '"</span><span class="txt txt-price" title="' + price + '">"' + price + '"</span><span class="m-nums"><a class="btn btn-low" href="javascript:;">减</a><input class="ipt ipt-num" type="text" value="1"><a class="btn btn-add" href="javascript:;">加</a></span>' + (stock ? ('<span class="tips tips-stock"><strong>库存紧张</strong></span>') : '') + '<a class="btn btn-del" href="javascript:;"><i></i>删除</a></li>');
        $lst.append($itm);
        $itm.height($itm.height());
        var $btnAdd = $itm.find('.btn-add'),
        $btnLow = $itm.find('.btn-low'),
        $btnDel = $itm.find('.btn-del'),
        $iptNum = $itm.find('.ipt-num');
        $btnAdd.on('click',
        function() {
            var num = parseInt($iptNum.val());
            num++;
            if (num > maxnum) {
                alert('购买数量超出限制，每单限购' + maxnum + '件.');
                num = maxnum
            }
            $iptNum.val(num)
        });
        $btnLow.on('click',
        function() {
            var num = parseInt($iptNum.val());
            if (num > 1) num--;
            $iptNum.val(num)
        });
        $iptNum.on('keyup',
        function() {
            var num = $iptNum.val(),
            maxnum = 20;
            num = num.replace(/\D/ig, '');
            if (num < 1) num = 1;
            if (maxnum && (num > maxnum)) {
                alert('购买数量超出限制，每单限购' + maxnum + '件.');
                num = maxnum
            }
            $iptNum.val(num)
        });
        $btnDel.on('click',
        function() {
            var $mcartlst = $(context).find('.m-cart .lst');
            var $cartitm = $(this).parents('.itm');
            var $mdateitms = $(context).find('.m-choose-date .itm:not(".itm-more")');
            var $mpriceitms = $(context).find('.m-choose-price .itm:not(".itm-more")');
            var cartitmstr = [$cartitm.find('.txt-datetime').html().replace(/^"|"$/ig, ''), $cartitm.find('.txt-price').html().replace(/^"|"$/ig, '')].join(' ');
            var datestr = formatDateString($mdateitms.filter('.itm-sel'));
            var pricestr = '';
            var eachstr = '';
            $mpriceitms.each(function(index, element) {
                pricestr = formatPriceString($(this).html());
                eachstr = [datestr, pricestr].join(' ');
                if (eachstr === cartitmstr) {
                    $(this).removeClass('itm-sel');
                    $cartitm.remove();
                    if (!$mcartlst.find('.itm').size()) {
                        $mproduct.addClass('m-product-1')
                    } else {
                        $mproduct.removeClass('m-product-1')
                    }
                }
            })
        })
    }
    function bindEntity() {
        var $mentity = $('.m-entity');
        var $usel = $mentity.find('.u-sel');
        $usel.find('.itm').first().addClass('first');
        $usel.on('mouseenter',
        function() {
            $(this).addClass('z-sel')
        });
        $usel.on('mouseleave',
        function() {
            $(this).removeClass('z-sel')
        })
    }
    function bindTourcity() {
        var $mtourcity = $('#layerXunyan'),
        $btn_expand = $mtourcity.find('a.u-btn'),
        $lst = $mtourcity.find('ul.lst'),
        $box = $mtourcity.find('div.box'),
        $itms = $lst.find('li.itm'),
        $line = $mtourcity.find('.line'),
        maxrow = maxrow || 5,
        maxh = 0,
        icoh = $mtourcity.find('.ico').first().height();
        $itms.first().addClass('itm-first');
        $itms.each(function(index, element) {
            if (index > (maxrow - 1)) return false;
            maxh += $(element).outerHeight(true)
        });
        if ($itms.size() > maxrow) {
            $btn_expand.show()
        } else {
            $btn_expand.hide()
        }
        render();
        $btn_expand.on('click',
        function() {
            $mtourcity.toggleClass('m-tourcity-expand');
            render()
        });
        function render() {
            if ($mtourcity.hasClass('m-tourcity-expand')) {
                $box.height($lst.height());
                $line.height($lst.height() - icoh);
                $btn_expand.find('.txt').html('收起')
            } else {
                $box.height(maxh);
                $line.height(maxh - icoh);
                $btn_expand.find('.txt').html('更多')
            }
        }
    }
    window["bindSoldout"] = bindSoldout = function() {
        var $msoldout = $('.m-soldout'),
        $box = $msoldout.find('.box'),
        $lst = $box.find('.lst'),
        $itms = $lst.find('.itm'),
        $num = $msoldout.find('.num'),
        $nums = null,
        $prev = $msoldout.find('.btn-prev'),
        $next = $msoldout.find('.btn-next'),
        lstw = 0,
        len = $itms.size(),
        offset = $itms.first().outerWidth(true),
        left = 0,
        now = 0,
        times = 500,
        html = '',
        page = 0,
        maxn = Math.floor($box.width() / $itms.first().width());
        if ((len < maxn) || (len === maxn)) {
            $num.hide();
            $prev.hide();
            $next.hide()
        }
        $itms.each(function(index, element) {
            lstw += $(element).outerWidth(true)
        });
        $lst.width(lstw);
        page = Math.ceil((lstw - 60) / $box.width());
        for (var i = 0; i < page; i++) {
            html += '<li><a href="javascript:;">' + (i + 1) + '</a></li>'
        }
        $num.html(html);
        $nums = $num.find('a');
        play(now);
        $next.on('click',
        function() {
            now++;
            play(now)
        });
        $prev.on('click',
        function() {
            now--;
            play(now)
        });
        $nums.each(function(index, element) {
            $(this).on('click',
            function() {
                now = index;
                play(index)
            })
        });
        function play(n) {
            left = -n * $box.width();
            if (n === page - 1) {
                left = -(lstw - $box.width())
            }
            if (n === page) {
                left = 0;
                now = 0
            }
            if (n < 0) {
                left = -(lstw - $box.width());
                now = page - 1
            }
            if ($itms && $itms.size() < maxn) left = 0;
            $nums.removeClass('z-crt').eq(now).addClass('z-crt');
            $lst.animate({
                'left': left
            },
            times)
        }
    };
    function bindSign() {
        var $signlog = $('.m-sign-log'),
        $menu = $signlog.find('.menu');
        $signlog.on('mouseenter',
        function() {
            $menu.addClass('z-show');
            $menu.parent().addClass('m-sign-act')
        });
        $signlog.on('mouseleave',
        function() {
            $menu.removeClass('z-show');
            $menu.parent().removeClass('m-sign-act')
        })
    }
    function bindPlaceholder() {
        placeholder('[placeholder]')
    }
    function bindLayer() {
        var $layer = $('.m-layer');
        $layer.each(function(index, element) {
            $(this).data('z-index', $(this).css('z-index'))
        })
    }
    function bindLayerClose() {
        var $close = $('.m-layer .u-btn-close');
        $close.on('click',
        function() {
            common.layer.hide($(this).parents('.m-layer'))
        })
    }
    function bindSelbox() {
        var $selbox = $('.u-sel:not(".u-sel-entity")');
        var last = null;
        $selbox.each(function(index, element) {
            var self = this,
            $self = $(this),
            $hd = $(this).find('.hd'),
            $txt = $hd.find('.txt'),
            $menu = $(this).find('.menu'),
            $itm = $menu.find('.itm'),
            $context = null,
            context_selector = $self.data('context') || '.fmitm';
            $context = $self.parents(context_selector).size() ? $self.parents(context_selector) : $self;
            $context.data('z-index', $context.css('z-index'));
            $self.data('context', $context);
            $hd.on('click',
            function() {
                if (last !== self) {
                    $selbox.not(self).removeClass('z-sel');
                    resetz($context[0])
                }
                last = self;
                $self.toggleClass('z-sel');
                if ($self.hasClass('z-sel')) {
                    $context.css({
                        'z-index': utlis.maxz() + 1
                    })
                } else {
                    $context.css({
                        'z-index': $context.data('z-index')
                    })
                }
            });
            $itm.on('click',
            function() {
                $itm.removeClass('z-crt');
                $(this).addClass('z-crt');
                $self.removeClass('z-sel');
                $context.css({
                    'z-index': $context.data('z-index')
                });
                $txt.html($(this).text())
            })
        });
        $(document).on('click',
        function(ev) {
            var ev = ev || window.event;
            var target = ev.target || ev.srcElement;
            if (!$(target).parents('.u-sel').size()) {
                $selbox.removeClass('z-sel');
                resetz()
            }
        });
        function resetz(ctx) {
            $selbox.each(function(index, element) {
                var $elm = $(this).data('context');
                if (this !== ctx) {
                    $elm.css({
                        'z-index': $elm.data('z-index')
                    })
                }
            })
        }
    }
    function bindMantab() {
        var $itms = $('.m-mantab .itm');
        $itms.on('click',
        function() {
            var $layer = $(this).find('.layer');
            if (!$layer.size()) return;
            var $mantab = $(this).parents('.m-mantab');
            $(this).addClass('z-crt').siblings().removeClass('z-crt');
            $layer.css({
                'left': -$(this).position().left
            });
            var $close = $(this).find('.btn-close');
            $close.on('click',
            function() {
                $(this).parents('.itm').removeClass('z-crt');
                return false
            })
        })
    }
    function placeholder(element) {
        var $ipt = $(element);
        if (!supportPlaceholder()) {
            $ipt.each(function(index, element) {
                if (!$(element).val()) $(element).val($(element).attr('placeholder'));
                $(element).addClass('placeholder');
                $(element).on('focus',
                function() {
                    if ($(element).val() === $(element).attr('placeholder')) $(element).val('');
                    $(element).removeClass('placeholder')
                });
                $ipt.on('blur',
                function() {
                    if (!$(element).val() || $(element).val() === $(element).attr('placeholder')) {
                        $(element).val($(element).attr('placeholder'));
                        $(element).addClass('placeholder')
                    } else $(element).removeClass('placeholder')
                })
            })
        }
    }
    function supportPlaceholder() {
        var attr = 'placeholder',
        input = document.createElement('input');
        return attr in input
    }
    function bindSdfix() {
        $('.m-sdfix').each(function() {
            $(this).find('.itm').on('mouseenter',
            function() {
                $(this).addClass('z-crt').siblings().removeClass('z-crt')
            });
            $(this).find('.itm').on('mouseleave',
            function() {
                $(this).removeClass('z-crt')
            });
            $(this).find('.totop').on('click',
            function() {
                $('html, body').animate({
                    'scrollTop': 0
                })
            })
        })
    }
    function bindHotRecommend() {
        var $mhot = $('#hotProjects');
        $mhot.on('mouseenter', 'li.itm',
        function() {
            $(this).addClass('z-crt').siblings().removeClass('z-crt')
        })
    }
    function bindStar() {
        var $drama = $('div.m-dramaed'),
        $editor = $drama.find('div.editor');
        $editor.on('click',
        function() {
            $drama.addClass('m-dramaed-act')
        });
        $(document).on('click',
        function(ev) {
            var ev = ev || window.event,
            target = ev.target || ev.srcElement;
            if (!$(target).parents('.m-dramaed').size()) {
                $drama.removeClass('m-dramaed-act')
            }
        })
    }
    function bindGrade() {
        var $grade = $('.m-grade'),
        $star = $grade.find('.star > span'),
        $num = $grade.find('.num'),
        i = iScore = iStar = 8;
        var txt = ['很差，倒胃口', '差，浪费时间', '平庸之作，不看也罢', '平庸之作，不看也罢', '一般，不妨一看', '一般，不妨一看', '很好，公认的佳作', '很好，公认的佳作', '很完美，绝对不容错过', '很完美，绝对不容错过'];
        for (i = 1; i <= $star.length; i++) {
            $star[i - 1].index = i;
            $star[i - 1].onmouseover = function() {
                fnPoint(this.index);
                $num.html('<span>' + (this.index) + '.0</span><strong>' + txt[this.index - 1] + '</strong>')
            };
            $star[i - 1].onmouseout = function() {
                fnPoint();
                if (iScore) {
                    $num.html('<span>' + iScore + '.0</span><strong>' + txt[iScore - 1] + '</strong>')
                } else {
                    $num.html('')
                }
            };
            $star[i - 1].onclick = function() {
                iStar = this.index;
                $num.html('<span>' + (this.index) + '.0</span><strong>' + txt[this.index - 1] + '</strong>')
            }
        }
        function fnPoint(iArg) {
            iScore = iArg || iStar;
            for (i = 0; i < $star.length; i++) $star[i].className = i < iScore ? 'on': '';
            $('.star span:odd').addClass('half')
        }
    }
});
/*购物车 e */

eval(function(p, a, c, k, e, d) {
    e = function(c) {
        return (c < a ? "": e(parseInt(c / a))) + ((c = c % a) > 35 ? String.fromCharCode(c + 29) : c.toString(36))
    };
    if (!''.replace(/^/, String)) {
        while (c--) d[e(c)] = k[c] || e(c);
        k = [function(e) {
            return d[e]
        }];
        e = function() {
            return '\\w+'
        };
        c = 1;
    };
    while (c--) if (k[c]) p = p.replace(new RegExp('\\b' + e(c) + '\\b', 'g'), k[c]);
    return p;
} ('b 2t="14://Y.1l.1d/Y.Z",1b="14://Y.1l.1d/2u.Z";b w=0;(7($){$.5.3=7(1L){b f=$.2b({},$.5.3.1Z,1L,{1m:1G,M:0,o:-1,2c:"0",2s:0});K z.2B(7(){b 1Q=$(z);$.27.28(1Q,f)})};$.5.3.1Z={10:T,s:\'2O\',2L:\'2M\',1k:\'\',18:\'\',8:"2R"};$.27={28:7(9,f){6(!f.10)$("#"+f.s).23();f.2c=9.v(0).2F;f.8=9.y("8");9.v(0).2C="2I";9.2q(7(e){w=0;b C=($.1p.1n)?21.1U:e.1V;6(C==V||C==38){K}$.5.3.1q(9,f);f.o=-1});9.2H(7(){b 15=2G(7(){$.5.3.A(f);2J(15);15=u},2D)});$.5.3.12(9,f);6($("#"+f.s).v(0)!=u&&f.10){$("#"+f.s).1w(7(){$.5.3.12(9,f);6(w==0){w=1}p{w=0}6(f.2a!=S){b 17=$.2b({},f,{18:f.2a,1E:"T"});$.5.3.1D($("#"+17.s),17,9)}})}}};$.5.3.1q=7(9,2){6($.1e(9.h())==\'\'&&w!=1){$.5.3.A(2)}p{$.5.3.29(9,2)}};b 11=u;$.5.3.29=7(9,2){6(u!=11){11.2E()}b 1i=2K U();1i.Q(2.18);6($("#26").2Q()>0)2P=$("#26").h();1i.Q("2S="+1A(9.h()));6(1h(2.1E)=="S"){6($.1e(9.h())!=\'\'&&2.1m==1G){11=$.1S({1k:1b+"?1M="+1A(9.h()),2f:"1W",1W:"1O",2N:"1O",24:7(n){6(n!=u){$.5.3.1f(n,9,2)}}})}}p{b 1X={"1M":9.h()};$.1S({1k:1b,n:1X,2x:"2w",2f:"2y",2A:7(){},24:7(n){6(n!=u){$.5.3.1f(n,9,2)}}})}};$.5.3.1f=7(n,9,2){2.o=-1;b x=n.2v;6(x!=\'\'&&x.I>0){6($(\'#B\'+2.8))$(\'#B\'+2.8).2z();b J=$.1e($(\'#\'+2.8).h());6(J.I>4){J=J.2p(0,4)+"..."}$(\'#B\'+2.8).Q("<a 2r=\'14://3y.1l.1d/\' 3D=\'3C\' 19=\'3o\'>3m【<N>"+J+"</N>】3s</a>");1P(b i=0;i<x.I;i++){b D=3r.3t("3v");D.8="G"+2.8+"l"+i;b 1c=x[i].3u;b 1a=x[i].1a;$(D).Z("<N 8=E"+2.8+\'l\'+i+" 1g=\'"+1c.3q(/<[^>]*>/g,"")+"\' 19=\'J\'>"+1c+"</N><N 19=\'3l\'>"+1a+"</N>");$(D).y("k",i);$(D).3n(7(){$.5.3.X($(z).y("k"),2)});$(D).3p(7(){$.5.3.1F($(z).y("k"),2)});$(\'#B\'+2.8).Q($(D))}1P(b j=0;j<x.I;j++){$.5.3.O($(\'#G\'+2.8+\'l\'+j).v(0),\'d\',2);$.5.3.O($(\'#E\'+2.8+\'l\'+j).v(0),\'t\',2)}$.5.3.2m(x.I,2);2.M=x.I;6(2.M!=0){9.3w("1T");9.1T(7(e){$.5.3.1Y(e,2)})}p{$.5.3.A()}}p{$.5.3.A(2)}};$.5.3.1Y=7(e,2){6($(\'#B\'+2.8).Z()==\'\')K;b C=($.1p.1n)?21.1U:e.1V;6(C==V){b r=0;6(H(2.o)>-1){6(H(2.o)>=H(2.M)-1){r=0}p{r=H(2.o)+1}};$.5.3.1j(r,2);$.5.3.X(r,2)}6(C==38){b r=2.M-1;6(2.o>-1){6(2.o<=0){r=H(2.M)-1}p{r=H(2.o)-1}}$.5.3.1j(r,2);$.5.3.X(r,2)}6(C==13){2.1m=T;$("#Y").1w()}};$.5.3.X=7(k,2){6($(\'#G\'+2.8+\'l\'+2.o)!=u){$.5.3.O($(\'#G\'+2.8+\'l\'+2.o).v(0),\'d\',2)}6($(\'#G\'+2.8+\'l\'+k)!=u){$.5.3.O($(\'#G\'+2.8+\'l\'+k).v(0),\'m\',2);2.o=k}p{$("#"+2.8).v(0).3x()}};$.5.3.1F=7(k,2){$("#"+2.8).h($(\'#E\'+2.8+\'l\'+k).y("1g"));$.5.3.A(2);$("#3z").1w();6(2.F!=S){6(1h 2.F=="7"){2.F($("#"+2.8).h(),$(\'#E\'+2.8+\'l\'+k).y("h"))}}w=0};$.5.3.1j=7(k,2){$("#"+2.8).h($(\'#E\'+2.8+\'l\'+k).y("1g"));6(2.F!=S){6(1h 2.F=="7"){2.F($("#"+2.8).h(),$(\'#E\'+2.8+\'l\'+k).y("h"))}}w=0;};$.5.3.2m=7(R,2){b 16=0;6((R*20+R+V)<=3A){16=R*20+R+V}p{16=32}$(\'#B\'+2.8).31()};$.5.3.A=7(2){$(\'#B\'+2.8).23();6($("#"+2.s).v(0)!=u){$("#"+2.s).1u();$("#"+2.s).1v("1R L 1x")}};$.5.3.12=7(9,2){};$.5.3.30=7(q,2d){b c=0;35(q){c+=q[2d];q=q.34}K c};$.5.3.O=7(q,22,2){33(22){1s\'d\':$(q).P({1o:"1r",25:"2n,2l,2o-2h",2g:"2Z",2k:"#2j",1y:"W",2i:"W",1H:"0 1C",1K:"1J 1I #1B"});1t;1s\'t\':$(q).P({2V:"2U%",2T:"2Y",2X:"2W",36:"3h",1o:"1r",3g:"1z",1y:"3f"});6(!$.1p.1n){$(q).P({3k:"1z"})}p{$(q).P({3j:"1z"})}1t;1s\'m\':$(q).P({1o:"1r",25:"2n,2l,2o-2h",2g:"#3i",2k:"#2j",1y:"W",2i:"W",1H:"0 1C",3e:"3a",1K:"1J 1I #1B"});1t}};$.5.3.1D=7(9,2,1N){6(9.v(0)!=u&&2.10==T){6(w==1){9.1u();9.1v("39 L 1x");$.5.3.1q(1N,2)}p{$("#"+2.s).1u();$("#"+2.s).1v("1R L 1x");$.5.3.A(2)}}};7 U(){z.n=[]}U.2e.Q=7(){z.n.37(3d[0]);K z};U.2e.3c=7(){K z.n.3b("")}})(3B);', 62, 226, '||optins|HMActiveSearchText||fn|if|function|id|obj||var||||opts||val|||index|_||data|lastindex|else|element|tempindex|selectid||null|get|CLICKNUM|result|attr|this|hiddensearch|rlist_|keycode|tempdiv|title_|callback|item_|parseInt|length|txt|return||listlength|span|setstyle|css|append|num|undefined|true|StringBuffer|40|28px|focusitem|search|html|selectstate|searchAjax|createlist||http|timer|divheight|optsss|para|class|cityname|suggest_search_url|projectName|cn|trim|showresult|title|typeof|temppara|Selected|url|damai|flag|msie|fontSize|browser|main|12px|case|break|removeClass|addClass|click|mr10|height|left|encodeURIComponent|ddd|10px|showsearchdiv|all|searchclick|false|padding|dotted|1px|borderBottom|options|keyword|textobj|suggestJsonp|for|Actext|sUp|ajax|keydown|keyCode|which|jsonp|args|searchkeydown|defaults||event|classname|hide|success|fontFamily|SearchCityID|ActiveSearchText|bind|ajaxsearch|allpara|extend|objwidth|offset|prototype|dataType|backgroundColor|serif|lineHeight|666|color|宋体|showsearch|arial|sans|substring|keyup|href|ocnum|search_url|suggest|suggests|POST|type|json|empty|error|each|autocomplete|300|abort|clientWidth|setTimeout|blur|off|clearTimeout|new|ajaxtype|post|jsonpCallback|style_ioc|seCityID|size|searchText|word|whiteSpace|80|width|hidden|overflow|nowrap|white|getposition|show|200|switch|offsetParent|while|textOverflow|push||sdown|pointer|join|tostring|arguments|cursor|auto|textAlign|ellipsis|eee|styleFloat|cssFloat|city|把|mouseover|appimg|mousedown|replace|document|装进口袋|createElement|name|div|unbind|focus|mobile|btnSearch|280|jQuery|_blank|target'.split('|'), 0, {}));
eval(function(p, a, c, k, e, d) {
    e = function(c) {
        return (c < a ? "": e(parseInt(c / a))) + ((c = c % a) > 35 ? String.fromCharCode(c + 29) : c.toString(36))
    };
    if (!''.replace(/^/, String)) {
        while (c--) d[e(c)] = k[c] || e(c);
        k = [function(e) {
            return d[e]
        }];
        e = function() {
            return '\\w+'
        };
        c = 1;
    };
    while (c--) if (k[c]) p = p.replace(new RegExp('\\b' + e(c) + '\\b', 'g'), k[c]);
    return p;
} ('S V(b){h 0="";b=b+"";W(b){9"X":{h b=$("#m").i();h g=$("#K").i();c(g=="10")0="n&1=e";d c(g=="12")0="n&1=e";d c(g=="J")0="O&1=f";d{c(b=="3")0="N&1=e";d c(b=="2")0="Q&1=e";d c(b=="5")0="P&1=j";d c(b=="6")0="M&1=e";d c(b=="4")0="I&1=j";d c(b=="7")0="l&1=k";d 0="l&1=k";}8}9"L":{h b=$("#m").i();c(b=="3")0="Z&1=Y";d 0="R&1=a";8}9"U":{0="T&1=a";8}9"H":{0="t&1=s";8}9"r":{0="u&1=x";8}9"w":{0="v&1=a";8}9"q":{0="p&1=a";8}9"E":{0="D&1=f";8}9"G":{0="F&1=C";8}9"z":{0="y&1=a";8}9"B":{0="A&1=a";8}9"11":{0="1I&1=a";8}9"1E":{0="1x&1=1t";8}9"1B":{0="1A&1=f";8}9"1z":{0="1y&1=a";8}9"1D":{0="1C&1=a";8}9"1u":{0="1s&1=a";8}9"1w":{0="1v&1=1M";8}9"1L":{0="1N&1=1P";8}9"1O":{0="1H&1=f";8}9"1G":{0="1F&1=f";8}9"1K":{0="1J&1=a";8}9"1r":{0="1b&1=o";8}9"1a":{0="19&1=1e";8}9"1d":{0="1c&1=a";8}9"15":{0="14&1=13";8}9"18":{0="17&1=16";8}9"1n":{0="1m&1=o";8}9"1l":{0="1q&1=a";8}9"1p":{0="1o&1=1h"}8;9"1g":{0="1f&1=1k"}8;1j:{8}}1i 0}', 62, 114, 'uid|verifier|||||||break|case|85fa2548|cid|if|else|a8d1ce5e|28da7c8b|ccid|var|val|ec7f87d6|29531a46|1802980631|CategoryID|1766460391|2a5b1a59|1722464281|1377|923|9b0c78f9|1608886304|1537942864|1722563712|1038|464b6df3|1722487313|1580|1722556601|586|bdd78b05|1791812365|2648|1150252302|1209|906|1859886454|23|ChildCategoryID|872|1766470407|1768593554|1563350740|1836242081|1766474943|1722560522|function|1589934555|893|getwidgetUID|switch|852|4e804b1d|1911555593||372||4b3a7534|1308355161|2279|cb2e2bea|1824216183|1835|1778780394|242|1827615092|1722486023|702|6df2500e|3247871984|850|c46809bd|return|default|2aff245e|200|1834774161|848|2234267271|947|1722527631|386|1610519071|a97d75b8|1087|1732890690|917|1738768073|1678536463|2148|1789933370|1703|1722599363|1597|356|1558618805|2024|1563942351|1722508082|1722538052|3250|1847|0af9e527|1563053163|1229|b3700b1c'.split('|'), 0, {}));
eval(function(p, a, c, k, e, d) {
    e = function(c) {
        return (c < a ? "": e(parseInt(c / a))) + ((c = c % a) > 35 ? String.fromCharCode(c + 29) : c.toString(36))
    };
    if (!''.replace(/^/, String)) {
        while (c--) d[e(c)] = k[c] || e(c);
        k = [function(e) {
            return d[e]
        }];
        e = function() {
            return '\\w+'
        };
        c = 1;
    };
    while (c--) if (k[c]) p = p.replace(new RegExp('\\b' + e(c) + '\\b', 'g'), k[c]);
    return p;
} ('(C(b){A c=b(1t);b.fn.90=C(e){A f={42:4H,6G:10,4W:"4s",73:"1c",2H:1t};x(e){b.7v(f,e)}A d=I;x("4s"==f.4W){b(f.2H).8f("4s",C(h){A g=0;d.3L(C(){x(b.8Q(I,f)){}O{x(!b.5c(I,f)){b(I).4q("86")}O{x(g++>f.6G){D U}}}});A i=b.b6(d,C(j){D!j.5l});d=b(i)})}I.3L(C(){A g=I;x(3V!=b(g).N("4y")){g.5l=U;b(g).co("86",C(){x(!I.5l){b("<1D />").8f("cI",C(){b(g).1H().N("1O",b(g).N("4y"))[f.73](f.cH);g.5l=33}).N("1O",b(g).N("4y"))}})}});b(f.2H).4q(f.4W);D I};C a(){}b.5c=C(e,d){A f;x(d.2H===3V||d.2H===1t){f=(1t.8M?1t.8M:b(1t).1m())+b(1t).4u()}O{f=b(d.2H).5O().1u+b(d.2H).1m()}D f<=b(e).5O().1u-d.42};b.8Q=C(e,d){A f;x(d.2H===3V||d.2H===1t){f=b(1t).4u()}O{f=b(d.2H).5O().1u}D f>=b(e).5O().1u+d.42+b(e).1m()};b.7v(b.bP[":"],{"bW-9p-9j":"3R.5c(a, {42 : 0, 2H: 1t})","c7-9p-9j":"!3R.5c(a, {42 : 0, 2H: 1t})"})})(3R);(C(){$("1D").90({73:"cR",6G:0,42:0})})();A 6S="1h://6F.1z.cn/6F.Q",as="1h://6F.1z.cn/ch.Q";(C($){A 2f={};1k.V=C(){x(3H.K==0)D 1v;A 2J=3H[0];1S(A i=1;i<3H.K;i++){A 3P=1A 2y(\'\\\\{\'+(i-1)+\'(:(\\\\d+))?\\\\}\',\'9x\');A 4g=3P.4k(2J);x(3S 4g!="3V"&&4g!=1v){A v=3H[i];x(3S 4g[2]!="3V"){A l=(v+"").K;x(l<(4g[2]-0)){v=("ca".2c(0,4g[2]-l))+v}}2J=2J.2A(3P,v)}}D 2J};1k.3O.V=C(){x(3H.K==0)D 1v;A 2J=I;1S(A i=0;i<3H.K;i++){A 3P=1A 2y(\'\\\\{\'+(i)+\'\\\\}\',\'9x\');2J=2J.2A(3P,3H[i])}D 2J};1k.3O.2h=C(){D I.2A(/(^\\s*)|(\\s*$)/g,"")};1k.3O.bf=C(3e){D I.2A(/\\{([a-ba]+)\\}/43,C(m,s){D 3e[s]||""})};1k.3O.bc=C(s,e){A 3P=1A 2y(s+"[\\\\w\\\\W]*"+e,"43");D I.7u(3P)};2k.3O.V=C(3i){A o={"M+":I.a9()+1,"d+":I.8I(),"h+":I.az(),"m+":I.aO(),"s+":I.aK(),"q+":1B.5e((I.a9()+3)/3),"S":I.aR()};x(/(y+)/.2C(3i))3i=3i.2A(2y.$1,(I.aA()+"").2c(4-2y.$1.K));1S(A k 4d o)x(1A 2y("("+k+")").2C(3i))3i=3i.2A(2y.$1,(2y.$1.K==1)?(o[k]):(("bg"+o[k]).2c((""+o[k]).K)));D 3i};2k.7N=C(4t){x(a0.3O.a4.aw(4t)=="[aq 2k]"){D 4t}A 4i=/\\((\\d+)\\)/;x(4i.2C(4t))D 1A 2k(4i.4k(4t)[1]-0);D bk.b1()};(C(){x(3S(5Y.2L)!="C"){5Y.2L=C(4j,s){x(a0.3O.a4.aw(4j)=="[aq 5Y]"){D 4j.2L(s)}D""}}})();$.1w=$.1w||{};$.1w.4T=1v;$.1w.25=C(2t){$.1w.4T=2t;$().bn({1K:\'be\',4s:\'5M\',aC:\'m-1e-aL\',1m:\'aV\',3a:"bl",4c:"b2://aX.1z.cn/aZ.4I?2t="+93("$.1w.8V()")+"&b8="+1R.b7})};$.1w.8V=C(){A 4n=7d(C(){9l(4n);4n=1v;$.31("/1y/8T.Q",{2a:14.1p,"t":1B.1P()},C(1o){5R(1o.1d.4o);5I(1o.1d.bh);x(3S $.1w.4T=="C"){$.1w.4T(33);$.1w.4T=1v}})},1M);$("#aH, #aD, #aG").2U()};C 6Z(5L){x(!5L||5L.K<=0){D""}A r=1A 2y(5L+"=([^&]*)");r.4k(38.aS);D 2y.$1}C 9Z(9){A $3q=$(\'.m-6Y\');A $47=$3q.X(\'.47\');A $27=$3q.X(\'.27\');A $1U=$3q.X(\'.1U\');A $2M=$3q.X(".2M");C 9E(c){x(c<=0){D"2M-1"}x(c>4a){D"2M-7"}x(c>3k){D"2M-6"}x(c>4H){D"2M-5"}x(c>50){D"2M-4"}x(c>10){D"2M-3"}x(c>0){D"2M-2"}D""}C 3s(3e){$47.1c().3s({\'1r\':\'+=\'+10,\'1u\':\'-=\'+10,\'4C\':0},3k,C(){$(I).1H();6W({2p:3e.c,9B:1})})}C 6W(3e){$27.Q(3e.2p);$2M.N("E","2M "+9E(3e.2p));5I(3e.9B)}A 5I=1t["5I"]=C(9z){x(9z==0){D}$3q.1J("m-6Y-9m");$1U.Q(\'aM\')};6W(9);$3q.1a(\'1l\',C(){x($3q.3b(\'m-6Y-9m\')){D}$.2d("/1y/cp.Q",{2a:14.1p,cm:1B.1P()},C(9){9=21("("+9+")");75(9.cw){3Q-cr:$.1w.25();D;3Q 1:3s(9);D;3Q 0:D}})})}C 6m(6I){x(6I){$("#5V, #6R").1c();$("#4L").39("1g")}O{$("#5V, #4L, #6R").1H()}}C 6l(6e){x(6e){$(".3W").Q("ao，am。").1c()}O{$(".3W").Q("ap，av").1c()}}C cs(9s,Z,1f){A 1N=[];1S(A i=0;i<9.K;i++){1N.2i(1k.V(\'<R E="H {1} {4} {6}" 1g="{5}" 9-4N={0} 9-1q="{3}" 9-1E="{8}" 9-6c="{7}"><a 1b="1Z:;"><P E="1E">{0}</P><i></i></a></R>\',9[i].5g,9[i].4Z?"H-3X":"","",9[i].6t,"","",9[i].16==1?"H-3u":"",9[i].6u,9[i].6w))}x(9.K==0){1N.2i(\'<R 1g="4G-1m:6H;4l-1r:2o"><P>5b</P></R>\')}9s.N("9-Z",Z).X(".Y").Q(1N.2L(""))}C 5u(Z,3w){A $c=$(".m-1s.m-1s-1E .Y");x($c.X("R.4U").K>0)D U;$c.Q(\'<R 1g="4G-1m:6H;4l-1r:2o" E="4U"><P>ck，c8...</P></R>\');$.2d("/1y/5u.Q",{2a:14.1p,Z:Z,t:1B.1P()},C(9){9=21("("+9+")");9=9.1d;6m(9.6I);x(3w){6l(9.cf)}9P(9.1f)});C 9P(9){x(9==1v||!9.K)9=[];A 2R="";1S(A i=0;i<9.K;i++){2R+=1k.V(\'<R E="H {1} {4} {6}" 1g="{5}" 9-4N={0} 9-1q="{3}" 9-1E="{8}" 9-6c="{7}"><a 1b="1Z:;"><P E="1E">{0}</P><i></i></a></R>\',9[i].5g,9[i].4Z?"H-3X":"","",9[i].6t,"","",9[i].16==1?"H-3u":"",9[i].6u,9[i].6w)}x(9.K==0){2R+=\'<R 1g="4G-1m:6H;4l-1r:2o"><P>5b</P></R>\'}$c.Q(2R);$("#1T").N("9-Z",Z)}}C 58(Z,1q){x(!1q){$("G.m-2B .Y .H").2U();$("G.m-1s.m-1s-1E .Y .H").1L("H-1X")}O{x(4B["54{0}4m{1}".V(Z,1q)]){D}$("G.m-2B .Y .H[9-1q=\'{0}\']".V(1q)).2U();$("G.m-1s.m-1s-1E .Y .H[9-1q=\'{0}\']".V(1q)).1L("H-1X")}x($(".m-2B .Y .H").K==0){$(".m-2B").X("4b.3f, G.ct").1H()}}C 5N(t,3Y){A $p=$("#cM");x($p.K==0)D;x(t&&$p.N("9-9K")=="1")D;$.2d("/1y/5N.Q",{2a:14.1p,3Y:3Y},C(1o){1o=21("("+1o+")");x(1o.16!=1M)D;ac(1o.1d)});C ac(9){A 1G="";A 4h="";x(3S 9=="cL"&&9.61("@")>0){4h=9.2c(2);9=9.2c(0,1)}75(6a(9)){3Q 1M:$("#32").1c();$p.47("#cJ").1H();$("#5w").1L("u-1n-1C");D;3Q 3:$p.X("p.cK").1c().X("P").1G(4h);4p;cS:x(9==1&&"1"==$p.N("9-9K")){1R.3j="1z.5C.3Y.5D="+93(3Y);$.1w.25();D}$p.X("p.cQ"+9).1c().4v("p.cO").1H();4p}}}C 7x(2T){C 7b(2v){2v=2v-0;x(2v<97)D{27:2v,5a:""};D{27:(2v/97).cP(2),5a:"cB"}}A 2v=7b(2T.cC);A 2p=7b(2T.8C);A 6p=\'cA\';x(2T.4e.K>0)6p=2T.4e[0].9i;A s=1k.V(\'<G E="6L"><63 E="3f">cy</63></G><G E="bd"><1i E="83"><R E="6x cz"><G E="3o"><2O>{0}</2O><em>cG</em></G><G E="3o"><P>cF</P>\',6p)+1k.V(\'</G></R><R E="6x cD"><G E="3o"><2O>{0}</2O><em>{1}cE</em></G><G E="3o"><P>bE</P></G></R><R E="6x bF"><G E="3o"><2O>{2}</2O><em>{3}6J</em>\',2p.27,2p.5a,2v.27,2v.5a)+\'</G><G E="3o"><P>bD</P></G></R></1i><1i E="1u">\';x(2T.4e.K>0){1S(A i=0;i<2T.4e.K;i++){s+=\'<R E="H">\';A 6h=\'\';x(i<3){6h=\'27-c1\'}s+=1k.V("<P E=\'27 {3}\'>{0}</P><P E=\'1I\'><a 1b=\'1Z:;\'>{1}</a></P><P E=\'ad\'>bB:{2}</P></2O>",(i+1),2T.4e[i].bC,2T.4e[i].9i,6h);s+="</R>"}}s=s+"</1i></G>";D s}C bJ(){A 3p=/1z.5C.bK.5D=([^;]*)/.4k(1R.3j);x(3p&&3p.K==2){A c=a1(3p[1]+"");x(c=="1"){D}}$.2d("/1y/bI.Q",{t:1B.1P()},C(9){9=21(\'(\'+9+\')\');x(9.16==1M){x(9.1d=="1"){9t(bG,bH,"/bA/bt.2R?2r=8","../1D/m-1e-bu.5B")}}})}C ax(){$.2d("/1y/8g.Q",{bs:14.1p,3E:$("#3n").19(),bp:$("#bq").19()},C(9){9=21("("+9+")");x(9.16==1M){A 7I={al:C(2e){D"by"+2e.2c(11,5)},bz:C(2e){D"bx"+["ag","bv","bw","bL","c0","c2","bZ"][1A 2k(2e.2A(/-/43,"/")).bX()]+2e.2c(11,5)},bY:C(2e){D"c6"+2e.2c(8,2)+"ag"+2e.2c(11,5)}},6f={al:"8w",c5:"8D",c3:"8x",c4:"8F",bQ:"7w",bO:"8y"};C 4Q(H){x(H.bS){D"（7K）"}O x(H.cN){D"（7K，{0}bR）".V(7I["T"+H.bT](H.bV))}D""}x(9.1d.bU){A 3U=\'<G E="3f"><4b E="3f">bN：</4b>\';A s=\'\';A 1f=9.1d.7q.6A(9.1d.8u).6A(9.1d.8B);x(1f.K>2){s+=\'<R E="3c 2z"> <G E="3m 3D" 1g="1K: bM;">\'}O{s+=\'<R E="3c 2z"> <G E="3m">\'}1S(A i=0;i<1f.K;i++){A 1F=4Q(1f[i]);s+=\'<G 1g="2u-1u:{2}2x;" E="2z"><P E="2w {0}"></P><G E="{1}" 1g="3v:1r">\'.V(1F.K>0?6f["T"+1f[i].7V+"1"]:6f["T"+1f[i].7V],1F.K>0?"5X":"3r",i==0?0:7);x(1f[i].1j!=1v){x(1f[i].1j.2b!=1v&&1f[i].1j.2b.K>0)s+=1k.V(\'<p>{0}<a 1g="3v:4P; 2u-1r:2o;" E="3r" 1b="1h://{1}">{2}&3x;&3x;</a></p>\',1f[i].2D+1F,1f[i].1j.2b,1f[i].1j.5f);O s+=1k.V(\'<p>{0}</p>\',1f[i].2D+1F);x(1f[i].1j.2G!=1v&&1f[i].1j.2G.K>0)3U+=\'<a 1b="1h://\'+1f[i].1j.2b+\'"><1D 1O="1h://64.1z.cn/\'+1f[i].1j.2G+\'" 1m="37" 1K="57" 1g="2u-5G:2o;" 49="55" /></a>\'}O{s+=1k.V(\'<p>{0}</p>\',1f[i].2D+1F)}s+=\'</G></G>\'}x(1f.K>3){s+=\'</G><a 1b="1Z:;" 4J="\'+1f.K+\'" E="5i 6y"><i E="5k"></i><P>4r</P></a></R>\'}O{s+=\'</G></R>\'}A 1W=9.1d.7q;x(U&&1W.K>0){x(1W.K>2){s+=\'<R E="3c 2z"> <G E="3m 3D">\';}O{s+=\'<R E="3c 2z"> <G E="3m">\'}1S(A i=0;i<1W.K;i++){A 1F=4Q(1W[i]);s+=\'<G 1g="2u-1u:{2}2x;" E="2z"><P E="2w {0}"></P><G E="{1}" 1g="3v:1r">\'.V(1F.K>0?"7w":"8D",1F.K>0?"5X":"3r",i==0?0:7);x(1W[i].1j!=1v){x(1W[i].1j.2b!=1v&&1W[i].1j.2b.K>0)s+=1k.V(\'<p>{0}<a 1g="3v:4P; 2u-1r:2o;" E="3r" 1b="1h://{1}">{2}&3x;&3x;</a></p>\',1W[i].2D+1F,1W[i].1j.2b,1W[i].1j.5f);O s+=1k.V(\'<p>{0}</p>\',1W[i].2D+1F);x(1W[i].1j.2G!=1v&&1W[i].1j.2G.K>0)3U+=\'<a 1b="1h://\'+1W[i].1j.2b+\'"><1D 1O="1h://64.1z.cn/\'+1W[i].1j.2G+\'" 1m="37" 1K="57" 1g="2u-5G:2o;" 49="55" /></a>\'}O{s+=1k.V(\'<p>{0}</p>\',1W[i].2D+1F)}s+=\'</G></G>\'}x(1W.K>3)s+=\'</G><a 1b="1Z:;" 4J="\'+1W.K+\'" E="5i 6y"><i E="5k"></i><P>4r</P></a></R>\';O s+=\'</G></R>\'}A 22=9.1d.8B;x(U&&22.K>0){x(22.K>2){s+=\' <R E="3c 2z"><G E="3m 3D">\';}O{s+=\' <R E="3c 2z"><G E="3m">\';}1S(A i=0;i<22.K;i++){A 1F=4Q(22[i]);s+=\'<G 1g="2u-1u:{2}2x;" E="2z"><P E="2w {0}"></P><G E="{1}" 1g="3v:1r">\'.V(1F.K>0?"8F":"8w",1F.K>0?"5X":"3r",i==0?0:7);x(22[i].1j!=1v){x(22[i].1j.2G!=1v&&22[i].1j.2G.K>0){3U+=\'<a 1b="1h://\'+22[i].1j.2b+\'"><1D 1O="1h://64.1z.cn/\'+22[i].1j.2G+\'" 1m="37" 1K="57" 1g="2u-5G:2o;" 49="55" /></a>\'}O s+=1k.V(\'<p>{0}</p>\',22[i].2D+1F);x(22[i].1j.2b!=1v&&22[i].1j.2b.K>0)s+=1k.V(\'<p>{0}<a 1g="3v:4P; 2u-1r:2o;" E="3r" 1b="1h://{1}">{2}&3x;&3x;</a></p>\',22[i].2D+1F,22[i].1j.2b,22[i].1j.5f)}O{s+=1k.V(\'<p>{0}</p>\',22[i].2D+1F)}s+=\'</G></G>\'}x(22.K>3)s+=\'</G><a 1b="1Z:;" 4J="\'+22.K+\'" E="5i 9q"><i E="5k"></i><P>4r</P></a></R>\';O s+=\'</G></R>\'}A 1V=9.1d.8u;x(U&&1V.K>0){x(1V.K>2){s+=\' <R E="3c 2z"><G E="3m 3D">\';}O{s+=\' <R E="3c 2z"><G E="3m">\'}1S(A i=0;i<1V.K;i++){A 1F=4Q(1V[i]);s+=\'<G 1g="2u-1u:{2}2x;" E="2z"><P E="2w {0}"></P><G E="{1}" 1g="3v:1r">\'.V(1F.K>0?"8y":"8x",1F.K>0?"5X":"3r",i==0?0:7);x(1V[i].1j!=1v){x(1V[i].1j.2G!=1v&&1V[i].1j.2G.K>0)3U+=\'<a 1b="1h://\'+1V[i].1j.2b+\'"><1D 1O="1h://64.1z.cn/\'+1V[i].1j.2G+\'" 1m="37" 1K="57" 1g="2u-5G:2o;" 49="55" /></a>\';O s+=1k.V(\'<p>{0}</p>\',1V[i].2D+1F);x(1V[i].1j.2b!=1v&&1V[i].1j.2b.K>0)s+=1k.V(\'<p>{0}<a 1g="3v:4P; 2u-1r:2o;" E="3r" 1b="1h://{1}">{2}&3x;&3x;</a></p>\',1V[i].2D+1F,1V[i].1j.2b,1V[i].1j.5f)}O{s+=1k.V(\'<p>{0}</p>\',1V[i].2D+1F)}s+=\'</G></G>\'}x(1V.K>3)s+=\'</G><a 1b="1Z:;" 4J="\'+1V.K+\'" E="5i 9r"><i E="5k"></i><P>4r</P></a></R>\';O s+=\'</G></R>\'}3U+=\'</G><G E="ct" 1g="4l-1u:2o;"><1i E="Y Y-1C" 2g="85"></1i></G>\';$("#8g").1J("m-1s m-1s-3c").Q(3U);$("#85").Q(s)}}})}C a5(){$.2d("/1y/cx.Q",{2j:1,cg:14.1p,3E:$("#3n").19()},C(9){9=21("("+9+")");x(9.16==1M){x(9.1d.K>0){$("#cj").1J("m-ci m-87").Q(\'<G E="6L"><63 E="3f">c9</63></G><G E="bd"><1i E="m-8b" 2g="8c"></1i></G>\');A s=\'\';1S(A i=0;i<9.1d.K;i++){x(i==0)s+=\'<R E="H z-6P">\';O s+=\'<R E="H">\';A 3z=9.1d[i];x(3z.1p>0){A 84=6a(3z.1p/4H);s+=1k.V(\'<G E="87"><a 2g="6C{5}" 1b="1h://{0}/{1}.Q" E="ce" 3d="3h" 9-6B="{6}" 9-2a="{1}" 3a="{3}"><1D  1O="1h://9g.5x.cn/1x/9h/{2}/{1}cd.5B" 49="" /></a><4b E="3f"><a 2g="6C{5}" 1b="1h://{0}/{1}.Q" 3d="3h" 9-6B="{6}" 9-2a="{1}" 3a="{3}">{3}</a></4b></G><G E="83"><G E="3o"><a 2g="6C{5}" 1b="1h://{0}/{1}.Q" 3d="3h" 3a="{3}" 9-6B="{6}" 9-2a="{1}">{3}</a></G><G E="3o"><P E="8j">{4}</P></G></G></R>\',89,3z.1p,84,3z.cu,3z.ae,3z.cv)}}$("#8c").Q(s)}}})}C a7(){C 8R(2J){af{A t=cl(2J).4S(\'=\')[1].4S(\'|\'),l=[];1S(A i 4d t){A $i=t[i].4S(\'@\'),2g=$i[0]-0;x($i.K!=3||2g==14.1p){cq}l.2i({i:2g,n:$i[1],h:$i[2]})}D l}a8(e){}D[]}C 8H(1f){x(1f&&1f.K>0){A 1N=[];1S(A i 4d 1f){A H=1f[i];1N.2i(1k.V("<R E=\'H\'><a 1b=\\"1h://{3}/{0}.Q\\" 3d=\\"3h\\" 3a=\\"{1}\\">{2}</a></R>",H.i,H.n,H.n,89))}A $c=$("#aN");$c.X("1i.m-8b").Q(1N.2L(""));$c.1c()}}C 8z(1f){A v=[];1S(A i 4d 1f){x(i>=10){4p}A H=1f[i];v.2i(H.i+\'@\'+H.n+\'@\'+H.h)}A 5Z=1A 2k();5Z.aT(5Z.8I()+30);1R.3j=1k.V("8U=aQ={0};aB=/;9W={1}",6O(v.2L("|")),5Z.9X())}A 1f=[{i:14.1p,n:14.5S,h:aE}];A 3l=/8U=([^;]*)/43.4k(1R.3j);x(3l&&3l.K==2){A 6E=8R(3l[1]);8H(6E);1f=1f.6A(6E)}8z(1f)}C a6(){x(8A>0){A 2p=0,2v=0;2p=6Z("bo");2v=6Z("b0");$.2d("/1y/aY.Q",{6T:14.1p,b4:8A,b9:b5,2v:2v||"0",2p:2p||"0",t:1B.1P()},C(9){9=21(\'(\'+9+\')\');x(9.16==1M&&9.1d!=""){x(9.1d.8C>0){$("#7t").Q(7x(9.1d))}O{$("#7t").1H()}}})}}C bi(1I){A 4j,4i=1A 2y("(^| )"+1I+"=([^;]*)(;|$)");x(4j=1R.3j.7u(4i))D aJ(4j[2]);O D 1v}A 7n=1t.7n=C(){$("#aF, #aW").2U();$("#32").1c();$("#1T .Y, #2S .Y").1L("Y-1C");$("#4L, #5w").1L("u-1n-1C");x($("#bb").K>0){A $P=$("#7R");$P.2s().2s().1J("m-2P-2");$P.2U()}O{A $2r=$("#7R"),1U=$2r.N("9-2r");$2r.Q(1U)}};C 9M($1x,$1E){x(!3B()){$.1w.25(6Q);D U}C 6Q(){A $cc=$("#9Q");$cc.X("[1I=\'Z\']").19($1x.N("9-Z"));$cc.X("[1I=\'bm\']").19($.2h($1x.Q().2A(/<[^>]*>/43,"")));$cc.X("[1I=\'3J\']").19($1x.N("9-3J"));$cc.X("[1I=\'1q\']").19($1E.N("9-1q"));$cc.X("[1I=\'1E\']").19($1E.N("9-1E"));$cc.X("[1I=\'4N\']").19($1E.N("9-4N"));7W();1Y.1e.1c($cc)}6Q();C 7W(){$.31("/1y/bj.Q",{t:1B.1P()},C(9){$("#ay").Q("aI{0}aU。<br />aP。".V(9.1d))})}}A 46=1t.46=C(){$("#7M").5H();$("#at").1H()};A 45=1t.45=C(){$("#at").5H();$("#7M").1H()};A 5v=0;A 79=0;C 28(){A 67=$("#28").19().2h();A 5s=/^(?:13\\d|15\\d|18\\d)\\d{5}(\\d{3}|\\*{3})$/;x(5s.2C(67)){79=1}O{79=0}}A 7L=1t.7L=C(){A 2F=$("#2F").19().2h();A 5m=$("#5m").19().2h();x(2F==""||5m==""){17("7H！");D U}$.2d("/1y/7C.Q",{7B:2F,7G:5m,7F:4,t:1B.1P()},C(9){9=21(\'(\'+9+\')\');x(9.16==1M){17("fe！");A 2K=2F.4S(\'@\');x(2K[0].K>5){2K[0]=2K[0].2c(0,2)+"****"+2K[0].2c(2K[0].K-3)}O x(2K[0].K>5){2K[0]=2K[0].2c(0,3)+"****"}2F=2K[0]+"@"+2K[1];$("#62").1G(2F);$("#5A").1c().39("2I").N("2n","2n");$("#6v").Q(\'<a 1b="1Z:45();">65</a>\');45()}O x(9.16==3k){17("5U！")}O x(9.16==7E){17("fd！")}O x(9.16==7Z){17("7H！")}O x(9.16==5E){$.1w.25()}O x(9.16==7T){17("7S！")}O{17("5U，4K："+9.2r)}})};A 7D=1t.7D=C(){A 28=$("#28").19().2h();A 5h=$("#5h").19().2h();x(28==""||5h==""){17("7O！");D U}$.2d("/1y/7C.Q",{7B:28,7G:5h,7F:1,t:1B.1P()},C(9){9=21(\'(\'+9+\')\');x(9.16==1M){17("fi！");28=28.2c(0,3)+"****"+28.2c(7);$("#51").1G(28);$("#5T").1c().39("2I").N("2n","2n");$("#6o").Q(\'<a 1b="1Z:46();">65</a>\');46()}O x(9.16==3k){17("5U！")}O x(9.16==7E){17("fh！")}O x(9.16==7Z){17("7O！")}O x(9.16==5E){$.1w.25()}O x(9.16==7T){17("7S！")}O{17("5U，4K："+9.2r)}})};C 9k(){A 2Z="";x($("#fg").N("2n")){2Z+="6:fc"}x($("#5T").N("2n")){x(2Z.K>0){2Z+="、"}2Z+="1:f8"}x($("#5A").N("2n")){x(2Z.K>0){2Z+="、"}2Z+="4:f7"}$.31("/1y/f6.Q",{6T:14.1p,3n:$("#3n").19(),6U:$("#6U").19(),fb:2Z,t:1B.1P()},C(9){x(9.16==1M){1Y.1e.1H("#3N");17("7p，7r！\\r\\7k\\"7s\\"")}O x(9.16==3k){17("7y！")}O x(9.16==5E){$.1w.25()}O{17("fa，88！")}});D;$.2d("/1y/f9.Q",{6T:14.1p,3n:$("#3n").19(),6U:$("#3n").19(),ft:2Z,fs:$("#51").1G(),fr:$("#62").1G(),t:1B.1P()},C(9){9=21(\'(\'+9+\')\');x(9.16==1M){1Y.1e.1H("#3N");17("7p，7r！\\r\\7k\\"7s\\"")}O x(9.16==3k){17("fw！")}O x(9.16==5E){$.1w.25()}O{17("7y，4K："+9.16)}})}C 7e(1n){A c=3*60;1n.1J("z-1C");A 8G=fv(C(){1n.1G("8E({0})".V(c));c-=1;x(c<0){fu(8G);1n.1L("z-1C").1G("8E")}},4a)}C a2(){A 3E=14.3y;x(3E!=""&&3E=="fq"){3E="fl"}A 4f=fk(3E);x(4f){$("#8t").Q("<5d 1K=\\"4H%\\" 1m=\\"8v\\" E=\\"fj\\"  99=\\"0\\" 94=\\"5M\\" 1O=\\"1h://fp.t.fo.8K.cn/8S/fm.8J?1K=0&1m=8v&f5=2&eO=1&eN=0&eM=5&eR=0&eQ=0&eP=1&eL=0&4f="+4f+"\\"></5d>");$("G.m-8O").1c();A 8N=4f.4S(\'&\')[0];$("#8L").N("1O","1h://8S.8O.8K/eH/eG.8J?1n=eF&1g=2&4f={0}&1K=eK&1m=24&eJ=eI".V(8N))}O{$("#8L").2U();$("#8t").2s().2s().2U()}}C a3(){$(".m-8a").3L(C(){A $I=$(I);x($(I).N("9-f1")=="U")D;f0.8a({4c:\'/2e.Q\',eZ:$I,8e:$I.N("9-8e"),f4:4a*60*2,V:$I.N("9-V")||"f3",3a:$I.N("9-3a"),2t:C(){21($I.N("9-2t"))}});})}C 4M(2j,9,2t,4q){9.2j=2j;$.f2("/1y/4M.Q?2j="+2j,9,C(23){x(4q||U){4q.39("2I")}23=21(\'(\'+23+\')\');x(23.16==4A){$.1w.25();2t(23.16,23.1d);D}x(23.16==1M){2t(23.16,23.1d);D}x(23.16==6N){17("eY,eU,88！");D}x(23.16==3k){17("eT，eS！");D}})}C 9V(9){x(9==1v){D U}A 3Z=$("#3Z");3Z.X("G.6L P.1U").1G(9.c.5S.2A(/eX/,""));3Z.X("p.2N").1G("eW"+(9.c.eV||(9.c.9n+"6J"))+"，fx-g1");A 2R="";1S(A i 4d 9.1f){A 1I=5Y.2L(9.1f[i].l,"、");x(!1I)1I="g6";A t="1-3";x(1I.61("g5")>=0||1I.61("g7")>=0||1I.61("g9")>=0){t="1-5"}2R+=1k.V(\'<R E="H {2}">{0} {1}，g8{3}g4</R>\',1I,9.1f[i].c||(9.1f[i].p+"6J"),i==0?"aa":"",t)}x(2R.K>0){3Z.X("1i.Y").Q(2R)}O{3Z.X("G.6n").2U()}}C 4X(2j){$("#4z").1L("z-1c");2j=2j||0;A 32=$("#32 .Y .H");A 6K=0;A 2l="",2Q="";1S(A i=0;i<32.K;i++){A $i=$(32[i]);A n=$i.X(".m-2l 2V.2m-27").19();6K+=(n-0);2l+=1k.V(",{0}",n);2Q+=1k.V(",{0}",$i.N("9-1q"))}A $1x=$("#2S .Y .H.H-1X");x($1x.K==0){D U}A 4Y=$1x.N("9-4Y")-0;x(6K>4Y){$("#4z").1J("z-1c").X("P").Q("g0.fZ,g3{0}g2.".V(4Y));D 33}2l=2l.2c(1);2Q=2Q.2c(1);x(1!=2j){x(2Q.K==0){D U}x(14.8s&&14.8r==1){A 59=1k.V("8p=8o&7h=1&4x={0}&8n=1&4F={1}&27={2}&n=0",2Q,14.3y,2l);38.1b="1h://3C.1z.cn/"+14.7j+"?"+59}O{A 59=1k.V("8p=8o&4x={0}&8n=1&4F={1}&27={2}&n=0",2Q,14.3y,2l);38.1b="1h://3C.1z.cn/8q.4I?"+59}D 33}A Z=$1x.N(\'9-Z\');x(14.8s&&14.8r==1){x(2Q==""){1R.38="1h://3C.1z.cn/"+14.7j+"?7h=2&4F={0}&4x={1}&72={2}&n=0".V(14.3y,14.1p,Z)}O{1R.38="1h://3C.1z.cn/"+14.7j+"?7h=3&4F={0}&72={1}&4x={2}&8m={3}&8i=0&2j=1&n=0".V(14.3y,Z,14.1p,2Q)}}O{1R.38=1k.V("1h://3C.1z.cn/8q.4I?4F={0}&72={1}&4x={2}&8m={3}&8i=0&2j=1&n=0",14.3y,Z,14.1p,2Q||"1")}D 33}C 5W(){A $1e=$("#8h");$1e.X(".m-1s-2e 1i.Y").Q($("#2S 1i.Y").Q());$1e.X(".m-1s-1E 1i.Y").Q($("#1T 1i.Y").Q());1Y.1e.1c("#8h");A ga=$(\'.m-1e-8l .m-1s-2e .Y .H\');$(\'.m-1e-8l\').1Q({2q:\'9O\'})}C gb(1x,1E){}A 4B={};C 74(Z,1q){x(4B["54{0}4m{1}".V(Z,1q)]){D}4B["54{0}4m{1}".V(Z,1q)]=33;$(".m-2B").X("4b.3f, G.ct").1c();$(".gd").3L(C(){A 4h=3k;A $3l=$(I),$1x=$3l.X("G.m-1s.m-1s-2e 1i.Y R.H:29(.5r)[9-Z=\'{0}\']".V(Z)),$2f=$3l.X("G.m-1s.m-1s-1E 1i.Y R.H:29(.5r)[9-1q=\'{0}\']".V(1q)),$cc=$3l.X(".m-2B .ct");$2f.1J("H-1X");x($1x.K==0)D;x($2f.K==0)D;A 7a=$cc.2q().1r,7f=$cc.2q().1u;A $5o=$1x.8k();A $5q=$2f.8k();$5o.1Q({\'2q\':\'5Q\',\'1r\':$1x.2q().1r,\'1u\':$1x.2q().1u,\'z-5t\':1}).1J("5r");$1x.2s().3G($5o);$5q.1Q({\'2q\':\'5Q\',\'1r\':$2f.2q().1r,\'1u\':$2f.2q().1u,\'z-5t\':1}).1J("5r");$2f.2s().3G($5q);$5o.3s({\'1r\':7a,\'1u\':7f,\'4C\':0},4h,C(){$(I).2U()});$5q.3s({\'1r\':7a,\'1u\':7f,\'4C\':0},4h,C(){$(I).2U();A 2R=1k.V(\'<R E="H" 1g="5J:4P;" 9-Z="{0}" 9-1q={1}>\',Z,1q)+1k.V(\'<P E="1U 1U-8j {1}">"{0}"</P>\',$1x.X("a").1G().2h(),6q==8?"f-dn":"")+1k.V(\'<P E="1U 1U-1E">"{0}"</P>\',$2f.1G().2h())+\'<P E="m-2l"><a E="1n 1n-7i" 1b="1Z:;">gc</a><2V E="2m 2m-27" 2j="1G" fH="1" /><a E="1n 1n-47" 1b="1Z:;">fG</a></P>\'+\'<P E="2N 2N-fF"><2O></2O></P>\'+\'<a E="1n 1n-9S" 1b="1Z:;"><i></i>fK</a>\'+\'</R>\';A $i=$(2R);$cc.X("1i.Y").3G($i);$i.fJ();fI 4B["54{0}4m{1}".V(Z,1q)]})})}C 77(9){x(!9){D}A $4V=$("#4V");A 2E=9.2E;x(2E&&2E.a&&2E.a.K>0){$("#51").1G(2E.a);$("#6o").Q(\'<a 1b="1Z:46();">65</a>\');$("#5T").N("2n","2n").39("2I");}O{$("#51").1G(\'82\');$("#6o").Q(\'<a 1b="1Z:46();">8d</a>\');$("#5T").N("2I","2I").39("2n")}A 3A=9.3A;x(3A&&3A.a&&3A.a.K>0){$("#62").1G(3A.a);$("#6v").Q(\'<a 1b="1Z:45();">65</a>\');$("#5A").N("2n","2n").39("2I");}O{$("#62").1G(\'82\');$("#6v").Q(\'<a 1b="1Z:45();">8d</a>\');$("#5A").N("2I","2I").39("2n")}x(9.2r){$4V.1J("u-1n-1C").1G("fE")}A 1e=$("#3N");1e.X("G.1A-76, G.1A-4U").fz();1Y.1e.1c("#3N")}A 3B=1t["3B"]=C(){A 23=/1z.fy=[^;]*/43.2C(1R.3j);x(23&&(3S 4o=="3V"||4o==1v)){6b()}D 23};A 6b=1t["6b"]=C(2t){A 8P=C(u){5R(u);(2t||C(i){})(u)};$.31("/1y/8T.Q",{2a:14.1p,t:1B.1P()},C(1o){8P(1o.1d.4o)})};C 5R(u){A 2W="";x(u){1t["4o"]=u;A 2X="";x(u.2X.K>6){A c=0;A r=1A 2y("[\\\\fD-\\\\fC]");1S(A i=0;i<u.2X.K;i++){c+=1;A t=u.2X.fB(i);2X+=t;x(r.2C(t)){c+=1}x(c>=12){4p}}2X+="..."}O{2X=u.2X}A 6r=("1h://fL.1z.cn/fV/"+6a(u.5z/ 1M) + "/" + u.5z + "fU.5B").fT();2W+=\'<a E="6g" 1b="1h://3g.1z.cn/" 3d="3h"><1D 3g="69" 1O="\'+6r+\'" fY="I.1O=\\\'1h://7P.5x.cn/7Q/7U/7Y/6g.81\\\';" /></a>\';2W+=\'<a E="1I 9A" 1b="1h://3g.1z.cn/" 3d="3h"> \'+2X+\'</a>\';2W+=\'<b></b>\';$("G[3g=6n]").39("1g");$(\'#7A\').Q(2W);$("1D[3g=69]").N("1O",6r);$("P[3g=fX]").1G(2X);(C(){x(u.2E.K==0){D}A $3T=$("#fW");x($3T.K==0){D}x(($3T.19()||"").K!=0&&$3T.19()!=$3T.N("9F")){D}$3T.19(u.2E)})()}O x(U){2W+=\'<P E="fS fO">\';2W+=\'<a E="dm-7m" 3a="7o" 1b="1h://3C.1z.cn/7z.4I?2j=7m">7o</a> | \';2W+=\'<a E="dm-fN" 3a="7l" 1b="1h://3C.1z.cn/7z.4I?2j=4i">7l</a>\';2W+=\'</P>\';$(\'#7A\').Q(2W);$("G[3g=6n]").1H();$("1D[3g=69]").N("1O","1h://7P.5x.cn/7Q/7U/7Y/6g.81")}}(C(){x(14.36==4||14.36==11||14.36==5){D U}$.31("/1y/fM.Q",{2a:14.1p},C(2T){7X(2T.1d)});C 7X(2w){(C(){A 9=2w.6k;A 2S=$("#2S");A 1N=[];x(9.K>0){1S(A i=0;i<9.K;i++){A p=9[i];1N.2i(\'<R E="H" 9-Z="{0}" 9-3J="{1}" 9-4Y="{2}">\'.V(p.fR,2k.7N(p.fQ).V("fP-dw-dd ah:dv:du"),p.dz)+\'<a 1b="1Z:;">\'+(p.7J?p.7J:(\'<P E="2e">\'+p.dy+\'</P><P E="dx">\'+p.dt+\'</P><P E="ad">\'+p.ae+\'</P>\'))+\'</a></R>\')}}O{1N.2i(\'<R 1g="4G-1m:ab;4l-1r:2o"><P>5b</P></R>\')}A l=$(1N.2L(""));l.aa().N("2g","52");2S.X("1i.Y").3G(l);x(9.K==1&&!2S.X("1i.Y").3b("Y-1C")){l.1J("H-1X")}})();(C(){A 9=2w.2Q,1T=$("#1T");A 1N=[];x(9.K>0){1S(A i=0;i<9.K;i++){A p=9[i];1N.2i(\'<R E="H{0}{1}" 9-1q="{2}" 9-4N="{3}" 9-1E="{4}" 9-6c="{5}" 9-dp="20">\'.V((p.4Z?\' H-3X\':\'\'),(!p.4Z&&p.16==1?\' H-3u\':\'\'),p.6t,p.5g,p.6w,p.6u)+\'<a 1b="1Z:;">\'+\'<P E="1E">{0}</P></a></R>\'.V(p.5g))}}O{1N.2i(\'<R 1g="4G-1m:ab;4l-1r:2o"><P>5b</P></R>\')}A l=$(1N.2L(""));x(2w.6k.K==1&&9.K==1&&!1T.X("1i.Y").3b("Y-1C")){l.4D(":29(.H-3X,.H-3u)").1J("H-1X")}1T.X("1i.Y").3G(l);1T.N("9-Z",2w.Z)})();x(aj&&aj==1&&2w.6k.K>12){$(".m-1s-dl").N("1g","5J:6s");ds()}(C(){A $1x=$("#2S 1i.Y R.H.H-1X"),$1E=$("#1T 1i.Y R.H.H-1X");x($1x.K==1&&$1E.K==1){74($1x.N("9-Z"),$1E.N("9-1q"))}})();x(2w.b3){6l(2w.3T)}6m(2w.dq)}})();$(1R).dJ(C(){$.2d("/1y/dI/"+14.1p,{t:1A 2k().3F(),9Y:1R.9Y},C(1o){1o=21(\'(\'+1o+\')\');x(1o.16==6N&&14.36!=4){7d(C(){38.1b="/{0}.Q?v={1}".V(14.1p,1B.1P())},dH);D}5R(1o.1d.4o);9Z(1o.1d.dM);x(1o.1d.dL){$("#14 G.2Y").1J("2Y-1X")}9V(1o.1d.3Z);53=1o.1d.53;});(C(){A 3p=/1z.5C.3Y.5D=([^;]*)/.4k(1R.3j);A t=33,5z="";x(3p&&3p.K==2){1R.3j="1z.5C.3Y.5D=;9W="+(1A 2k()).9X();A c=a1(3p[1]+"");$("#8Z").19(c);t=U}5N(t,5z)})();a5();a6();a7();a2();a3();x((14.36!=5)&&(14.36!=6)&&(14.36!=11)&&(14.36!=4)){ax()}x($("#52")!=1v&&$("#52").N("9-3J")!=1v){A 5j=1A 2k($("#52").N("9-3J").2A(/\\-/g,\'/\')).3F();A 35=5j-(1A 2k()).3F();$(".3W").Q("");x(35>0&&(1B.5e(35/(24*70*4a))<=3)){x(6e==1){$(".3W").Q("ao，am。")}O{$(".3W").Q("ap，av")}}}x(3R("#4E").an){3R("#4E").an({2g:\'4E\',dB:\'2d\',4c:as+\'?\',dA:\'dF=1&\',dE:\'\'})}3R("#4E").9T(C(4W){x(4W.3t==13){A 1U=$(I).19();38.1b=6S+"?ar="+6O(1U);D U}});A 6i=$(\'.m-2P .H\').1m();x(6i>56){$(\'.m-2P .2N-5p .6j\').1c()}O{$(\'.m-2P .2N-5p .6j\').1H()}x(14.36==4){(C d2(){66=$("#m-d1 2V"),68=$("#m-ff-d5 1i");$(66[0]).N("E","m-5y-6d");$(68[0]).N("1g","5J:6s");66.1l(C(){66.4D(".m-5y-6d").N("E","m-5y-1n");$(I).N("E","m-5y-6d");68.N("1g","").4D("[9-au=\'"+$(I).N("9-au")+"\']").N("1g","5J:6s;")})})();D;$.31("1h://2f.1z.cn/1y/d4.Q",{6q:6q,3n:14.3y,t:1B.1P()},C(23){A 1N=[],2l=[];1S(A i 4d 23.1d){A 40=23.1d[i];1N.2i(\'<R E="H">\');1N.2i(\'<a 1b="1h://2f.1z.cn/{0}.Q" E="H-d3" 3d="3h"><1D 49="{2}" 1O="1h://9g.5x.cn/1x/9h/{1}/{0}cZ.5B" /></a>\'.V(40.1p,~~(40.1p/4H),40.5S));1N.2i(\'<9d E="H-3f"><a 1b="1h://2f.1z.cn/{0}.Q" 3d="3h">{1}</a></9d>\'.V(40.1p,40.5S));1N.2i(\'<p E="H-1U">cV：<2O E="1U-c1">￥{0}</2O>cU </p>\'.V(40.9n));1N.2i(\'</R>\');2l.2i(\'<R><a E="{1}" 1b="1Z:;">{0}</a></R>\'.V(i+1,i==0?"z-6P":""))}A G=$("#cY");G.X("1i.Y").Q(1N.2L(""));G.X("1i.27").Q(2l.2L(""));cX()})}}).1a("1l",".m-2P .6j",C(){x($(\'.m-2P .bd\').3b("1a")){$(\'.m-2P .bd\').1L("1a");$(\'.m-2P .2N-5p\').1L("z-1c")}O{A 6i=$(\'.m-2P .H\').1m();$(\'.m-2P .bd\').1J("1a");$(\'.m-2P .2N-5p\').1J("z-1c")}}).1a("1l","#4V:29(.u-1n-1C)",C(){9k()}).1a("1l","#df",C(){1Y.1e.1H("#3N")}).1a("de","#28:29(.z-1C)",C(){28()}).1a("1l","#dj:29(.z-1C)",C(){A 2F=$("#2F").19().2h();x(2F==""){17("di！");D}x(!/\\w+([-+.]\\w+)*@\\w+([-.]\\w+)*\\.\\w+([-.]\\w+)*/.2C(2F)){17("dh！");D U}A $1n=$(I);$1n.1L("z-1c");$.2d("/1y/dc.Q",{3A:2F,t:1B.1P()},C(9){9=21(\'(\'+9+\')\');x(9.16==1M){$1n.1J("z-1c");7e($1n);17("91！")}O{17("8W，4K："+9.16)}})}).1a("1l","#4R:29(.z-1C)",C(){A 4R=$(I);x(5v==1){D U}A 28=$("#28").19();x(28==""){17("d8！");D U}A 5s=/^1\\d{10}$/;x(!5s.2C(28)){17("9u！");D U}4R.8Y().1L("z-1c");5v=1;$.2d("/1y/d7.Q",{67:28,t:1B.1P()},C(9){9=21(\'(\'+9+\')\');x(9.16==1M){17("91！");4R.8Y().1J("z-1c");7e(4R)}O x(9.16==d6){17("db！")}O x(9.16==da){17("d9，eo！")}O{17("8W，4K："+9.16)}5v=0})}).1a("1l","#en, #el",C(){x(!3B()){$.1w.25();D U}A 1e=$("#3N");1e.X("G.1A-76, G.1A-4U").1c();1Y.1e.1c(1e);$.31("/1y/98.Q",{2a:14.1p,t:1B.1P()},C(9){x(9.16==1M){x(9.1d!=1v){77(9.1d)}O{1Y.1e.1H(1e)}}O x(9.16==4A){1Y.1e.1H(1e);$.1w.25()}O{17(9.1d);1Y.1e.1H(1e)}})}).1a("1l","#er",C(){!C(){1Y.1e.1H("#6X");A 1e=$("#3N");1e.X("G.1A-76, G.1A-4U").1c();$.31("/1y/98.Q",{2a:14.1p,t:1B.1P()},C(9){x(9.16==1M){x(9.1d!=1v){77(9.1d)}O{1Y.1e.1H(1e)}}O x(9.16==4A){1Y.1e.1H(1e);$.1w.25()}O{17(9.1d);1Y.1e.1H(1e)}})}()}).1a("1l","#14 G.2Y P.1n-eq P.1U",C(){$.2d("/1y/2Y.Q",{2a:14.1p,t:1B.1P()},C(1o){1o=21(1k.V("({0})",1o));x(1o.16==4A){$.1w.25();D U}x(1o.16==1M){75(1o.1d){3Q 2:3Q 1:$("#14 G.2Y").1J("2Y-1X");4p}}});D U}).1a("9H","#14 .2Y",C(){$(I).1J(\'2Y-96\')}).1a("9N","#14 .2Y",C(){$(I).1L(\'2Y-96\')}).1a("1l","G.m-1s.m-1s-2e 1i.Y:29(.Y-1C) R.H:29(.H-3M)>a",C(){A $I=$(I).2s(),Z=$I.N("9-Z"),5j=1A 2k($I.N("9-3J").2A(/\\-/g,\'/\')).3F();A 35=5j-(1A 2k()).3F();A 3w=U;x(35>0&&(1B.5e(35/(24*70*4a))<=3)){3w=33}A 32=$("#32 .Y .H");x(32.K>0){x(!ep("ek？")){D U}58(Z,0)}$(".m-1s.m-1s-2e .Y .H[9-Z=\'{0}\']".V(Z)).8X("H-1X").4v("R").1L("H-1X");x($("#1T").N("9-Z")==Z){$("#1T .Y .H").1L("H-1X");D U}$(".3W").Q("");5u(Z,3w)}).1a("1l","G.m-1s.m-1s-2e 1i.Y.Y-1C R.H:29(.H-3M)>a",C(){A $I=$(I).2s(),Z=$I.N("9-Z");A 35=(1A 2k($I.N("9-3J").2A(/\\-/g,\'/\')).3F())-(1A 2k()).3F();A 3w=U;x(35>0&&(1B.5e(35/(24*70*4a))<=3)){3w=33}x($("#1T").N("9-Z")==Z){$("#1T .Y .H").1L("H-1X");D U}$(".3W").Q("");5u(Z,3w)}).1a("1l","#1T .Y:29(.Y-1C) .H.H-3u:29(.H-3X) > a",C(){A $I=$(I).2s();A Z=$("#1T").N("9-Z");A $1x=$("#2S .H[9-Z=\'{0}\']".V(Z));9M($1x,$I);D U}).1a("1l","G.m-1s.m-1s-1E 1i.Y:29(.Y-1C) R.H:29(.H-3M,.H-3u,.H-3X)>a",C(){A $I=$(I),$H=$I.2s();A $1x=$("#2S .Y .H.H-1X"),Z=$1x.N("9-Z");A 1q=$H.N("9-1q");x($H.3b("H-1X")){58(Z,1q);D U}x($1x.K==0){17("9w！");D U}74(Z,1q);D U}).1a("1l",".m-1s.m-1s-1E .Y .H.H-3X>a",C(){1Y.1e.1c("#ef");D U}).1a("9H","#1T .Y:29(.Y-1C) .H-3u",C(){A $7c=$("#1T .2N");$7c.1Q({\'1r\':$(I).2q().1r+30,\'1u\':$(I).2q().1u+34});$7c.1J(\'z-1c\')}).1a("9N","#1T .Y:29(.Y-1C) .H-3u",C(){$("#1T .2N").1L(\'z-1c\')}).1a("1l",".m-2B .Y .H>a.1n-9S",C(){A $H=$(I).2s();58($H.N("9-Z"),$H.N("9-1q"));D U}).1a("9T","G.m-2B 1i.Y R.H P.m-2l .2m-27",C(e){A 3t=e.3t;x((3t<48||3t>57)&&(3t!=8&&3t!=0))D U;x($(I).19().2h().K==0&&3t==48)D U}).1a("1l","G.m-2B 1i.Y R.H P.m-2l a.1n-7i",C(){A $i=$(I).5F("R.H");A $2f=$("G.m-2B 1i.Y R.H[9-1q=\'{0}\'][9-Z=\'{1}\']".V($i.N("9-1q"),$i.N("9-Z")));A $2m=$2f.X("2V.2m-27");A v=$2m.19()-0;x(v<=1)D U;$2m.19(v-1)}).1a("1l","G.m-2B 1i.Y R.H P.m-2l a.1n-47",C(){A $i=$(I).5F("R.H");A $2f=$("G.m-2B 1i.Y R.H[9-1q=\'{0}\'][9-Z=\'{1}\']".V($i.N("9-1q"),$i.N("9-Z")));A $2m=$2f.X("2V.2m-27");A v=$2m.19()-0;$2m.19(v+1)}).1a("1l","#eh",C(){A $v=$(I).9y(),v=$v.19().2h();A $c=$(I).5F(".m-eB");x(v.K==0||v==$v.N("9F")){$c.X(".2N").1G("eA~");D U}$.2d("/1y/ez.Q",{2a:14.1p,v:v,t:1B.1P()},C(1o){1o=21("("+1o+")");x(1o.16==1M){$c.2U();$("#2S, #1T, #32").1c();$("#5w").1L("u-1n-1C")}O x(1o.16==4A){$.1w.25();D U}O{$c.X(".2N").1G(1o.1d)}});D U}).1a("1l",\'.6y,.9q,.9r\',C(){x($(I).2s().X(\'.3D\').1m()==71){$(I).2s().X(\'.3D\').3s({1m:$(I).N("4J")*26-7});$(I).X(\'i\').1J(\'9D\');$(I).X(\'P\').Q(\'eD\')}O{$(I).2s().X(\'.3D\').3s({1m:71});$(I).X(\'i\').1L(\'9D\');$(I).X(\'P\').Q(\'4r\')}}).1a("1l","#ey",C(){A v=$(I).9y().19().2h();x(v==""){17("eu！");D U}x((!/^1\\d{10}$/.2C(v)&&!/^1\\d{2}\\*{4}\\d{4}$/.2C(v))){17("es！");D U}A 9={2a:14.1p,3I:v};C 2t(2r,1o){x(1M==2r){1Y.1e.1c("#6X");$("#6X 1D").3L(C(){A $I=$(I);x(!$I.N("1O")&&$I.N("4y")){$I.N("1O",$I.N("4y"))}})}}4M(3,9,2t);D U}).1a("1l","#ew, #ev",C(){A $cc=$("#ak");A 5K=$cc.X("2V.9A").19(),2E=$cc.X("2V.ed").19(),2p=$cc.X("2V.9v").19(),6M=$cc.X("dW.dV").19();A 9={2a:14.1p,5K:5K,3I:2E,2p:2p,6M:6M};x(5K.K==0){17("dU！");D U}x(2E.K==0){17("dZ！");D U}x(!/^1\\d{10}$/.2C(2E)){17("dY！");D U}A $I=$(I);x($I.9("4O")=="4O"){D U}$I.9("4O","4O");4M(2,9,C(2r,23){$I.9("4O","");x(1M==2r){17("dX")}});D U}).1a("1l","#dT",C(){A $I=$(I).N("2I","2I");A $44=$I.5F("44");$44.X("[1I=\'2p\']").19($44.X(".9v a.H.z-6P").Q());A 9={};$44.X("[1I]").3L(C(){A $$=$(I);9[$$.N("1I")]=$$.19()});A 3I=9.3I;x(!3I||!/^1\\d{10}$/.2C(3I)){$44.X("[1I=\'3I\']").1J("dO");17("9u！");D U}9["2a"]=14.1p;4M(1,9,2t,$I);C 2t(2r,1o){x(2r==1M){1Y.1e.1H("#9Q");17("dN！")}}}).1a("1l","G.m-1s 1i.Y R.H-3M a",C(){A $i=$("G.m-1s 1i.Y R.H-3M");x($i.3b("H-3M-1X")){$i.4v("R.92:29(.H-1X)").1H()}O{$i.4v("R.92").1c()}$i.8X("H-3M-1X");$i.3L(C(){A $t=$(I).X("P.1U"),t=$t.1G();$t.1G($t.N("9-5H-1U")).N("9-5H-1U",t)});D U}).1a("1l","#dR",C(){5N(U,$("#8Z").19())}).1a("1l","#dQ, #5w",C(){$("#4z").1L("z-1c");x($(I).3b("u-1n-1C")){D U}x(!3B()){$.1w.25(41);D U}C 41(){x(7g()){$.31("/1y/9J.Q",{4m:1B.1P()},C(9){x(9.1d!="1"){17("9G，9I，9U，9R");D U}cb()});D U}cb()}C cb(9a){x(9a){A 4n=7d(C(){9l(4n);4n=1v;x(!4X(0)){5W()}},0)}O{x(!4X(0)){5W()}}}41();D U}).1a("1l","#e9",C(){$("#4z").1L("z-1c");x(!4X(0)){17("e8")}D U}).1a("e7","#9c",C(){x($(I).19()=="9e，9f"){$(I).19("")}}).1a("ec","#9c",C(){x($.2h($(I).19())==""){$(I).19("9e，9f")}}).1a("1l","#eb",C(){A $1U=$("#4E"),k=$1U.19().2h();38.1b=6S+"?ar="+6O(k)}).1a("1l","#ea",C(){A $4w=$("#e6"),$1D=$4w.X("G.e2 1D");x($1D.N("1O")){1Y.1e.1c($4w);D U}A 1g={1K:e1,1m:6N},ai=1Y.1e.1c($4w,1g);A 1D=1A e0();1D.1O=$(I).N("1b");1D.e5=C(){A 1Q={1K:1D.1K-0,1m:1D.1m-0};A $6V=$(1t),$6D=$6V.1m(),$e4=$6V.1K();x(1Q.1m>$6D){A r=($6D*0.78)/1Q.1m;1Q.1m=1Q.1m*r;1Q.1K=1Q.1K*r}1Q["2u-1r"]=(0-1Q["1K"])/2;1Q["2u-1u"]=(0-(1Q["1m"]+ai["ah"]))/2;$1D.N("1O",I.1O).1c();$4w.3s(1Q,e3)};D U}).1a("1l","#ak P.m-2l a",C(){A $I=$(I),$2m=$(I).4v("2V"),c=$2m.19()-0;x(dS(c)){c=1}x($I.3b("1n-7i")){x(c>1){c-=1}}O{c+=1}$2m.19(c);D U}).1a("1l","#dP a.u-1n-ex, #et",C(){x(!3B()){$.1w.25();D U}}).1a("1l","2V[1I=\'9C\']",C(){}).1a("1l","#eC",C(){af{$.1w.eE.ei()}a8(ej){}});C 7g(){D 3S 53!="3V"&&53==14.1p}$(C(){$("#5V, #4L, #6R").1a("1l",C(){$("#4z").1L("z-1c");A $2g=$(I).N("2g");x($(I).3b("u-1n-1C")){D U}x(!3B()){$.1w.25(41);D U}C 41(){x(7g()){$.31("/1y/9J.Q",{4m:1B.1P()},C(9){x(9.1d!="1"){17("9G，9I，9U，9R");D U}cb()});D U}cb()}C cb(){x(!4X(1)){x($2g=="5V"||$2g=="4L"){5W()}O{17("9w")}}}41();D U})});$(C(){A $6z=$("2V[1I=\'9C\']");A $1n=$("#4V");$6z.1a("1l",C(){$1n.1G("ee");x($6z.4D(":2n").K==0){$1n.1J("u-1n-1C")}O{$1n.1L("u-1n-1C")}})})})(3R);C 9t(w,h,4c,1D){A B=\'<G 2g="J" E="9o-eg" 1g="9b: #dg;2q: 9O;1u: 0;1r: 0;z-5t: cW;4D: cT(4C = 80);4C: 0.8;*2q: 5Q;*1r: 9L(21(1R.95.d0));*1u: 9L(21(1R.95.4u));"></G>\';A F=\'<5d 1O="\'+4c+\'" 1m="\'+h+\'" 1K="\'+w+\'" 94="5M" 99="0" dk="33"></5d>\';A dD=1R.5P.dC;A dG=1R.5P.dK;A L=\'<G 2g="L" E="9o" 1g="2q: 5Q;9b: 4c(\'+1D+\') 5M-dr;1u: 0;1r: 0;z-5t: do;"></G>\',$W=$(1t).1K(),$3K=$(1t).1m(),$T=0,$L=0;$("5P").3G($(L));$(\'#L\').Q(F);$("5P").3G($(B));$("#J").1Q({"1m":$3K+"2x","1K":$W+"2x"});$("#L").1Q({"1r":($W-w)/ 2 + "2x", "1u": ($3K - h) /2+"2x"});$(1t).4s(C(){A $5n=$(1R).4u();$T=($3K-h)/2+$5n;$("#L").1Q({"1u":$T+"2x"})});$(1t).fA(C(){A $5n=$(1R).4u();$3K=$(1t).1m();$T=($3K-h)/2+$5n;$L=($(1t).1K()-w)/2;$("#L").1Q({"1r":$L+"2x","1u":$T+"2x"});$("#J").1Q({"1m":$3K+"2x","1K":$(1t).1K()+"2x"})})}', 62, 1006, '|||||||||data||||||||||||||||||||||||if|||var||function|return|class||div|itm|this||length|||attr|else|span|html|li|||false|format||find|lst|performId|||||projectInfo||Status|alert||val|on|href|show|Data|layer|list|style|http|ul|ChuxiaoInfo|String|click|height|btn|rsp|ProjectID|priceId|left|choose|window|top|null|damaiItem|perform|ajax|damai|new|Math|dis|img|price|restrict|text|hide|name|addClass|width|removeClass|200|htmls|src|random|css|document|for|priceList|txt|crulelist|mrulelist|sel|common|javascript||eval|zrulelist|ret||showLogin||num|changephonenum|not|projectId|ActivityLink|substr|get|date|item|id|trim|push|type|Date|nums|ipt|checked|10px|count|position|status|parent|callback|margin|money|pro|px|RegExp|clearfix|replace|cart|test|ChuxiaoTitle|phone|changemailnum|PicUrl|container|disabled|str|ts|join|ico|tips|strong|goodstips|prices|htm|performList|json|remove|input|strHtml|nickName|collect|channelidstr||getJSON|cartList|true||date3|SiteStatus||location|removeAttr|title|hasClass|promotion|target|obj|tt|my|_blank|fmt|cookie|500|ll|sublst|cityId|row|codeCookie|self|look|animate|which|oos|float|isShowTips3Dates|gt|CityID|pinfo|email|checkLogin|www|s_manjian|cityid|getTime|append|arguments|mobilePhone|performTime|windoH|each|more|dylayer|prototype|re|case|jQuery|typeof|ele|pichtml|undefined|m_heighlight_tip|mobile|privilegeCode|freight|ii|chk|threshold|gi|form|showmailchange|showchange|add||alt|1000|h3|url|in|GrabTicketList|uid|rs|times|reg|arr|exec|padding|_|t1|userInfo|break|trigger|展开|scroll|jsonDate|scrollTop|siblings|showLayer|proId|original|warnXiangou|404|cartMoving|opacity|filter|txtSearchText|companyId|line|100|aspx|ht|错误代码|btnXuanzuo2|ProjectRegistration|pricename|registing|none|getRestrict|getcode_btnl|split|loginSuccCallback|loading|dysbmit|event|gotoShopping|buyCount|IsAppPrice||dyphonenum|firstperform|LOLProjectId|mv_|火爆热卖中|||removeCartPrice|queryString|unit|待定|belowthefold|iframe|floor|LinkDesn|PriceName|verifyCode|pro_more|performtime|icon_more|loaded|mailverifyCode|scrollH|nPerform|multi|nItem|j_animate|isMobile|index|getPriceList|issending|btnBuyNow2|dmcdn|proslide|code|mode_email|jpg|cn_project|rt|403|closest|bottom|toggle|setFlowerState|display|userName|key|no|verifyPrivilege|offset|body|absolute|loadu|Name|mode_sms|绑定失败|btnXuanzuo|showChooseGoodsLayer|c999|Array|_date||indexOf|dyMail|h2|static|更换|mbtn|phonenum|mul|head|parseInt|getUserInfo|taopiao|active|iseticket|typecss|user|top3|itm_height|btnsel|performs|setTips3Dates|setBtnXuanzuoStatus|menu|phoneoperate|timeStr|categoryId|headUrl|block|PriceID|IsTaoPiao|mailoperate|SellPrice|col|more1|modes|concat|idx|newitem_recom_|wh|projects|search|failurelimit|32px|isXuanzuo|元|totalcount|hd|notes|400|encodeURIComponent|crt|aac|btnXuanzuo3|search_url|pId|cityName|win|setState|layerRemind|flowers|getHash|3600||performID|effect|moveToCart|switch|mark|showDyLayer||isright|targetleft|formatMoney|mpricetips|setTimeout|dyBtnCountdown|targettop|isLOLProject|rtype|low|htmlName|n接收客户端订阅信息请下载|注册|login|toBeAboutToTimeOver|登录|太棒了|MRuleList|订阅成功|大麦网客户端|right_ticket|match|extend|manjian_grey|createAlbum|订阅失败|redirect|userLoginInfo|account|setChanelIds|setphoneChannel|407|channelid|VerifyCode|邮箱和验证码不能为空|funs|PerformName|已无名额|setmailChannel|phonechange|parseJsonDate|手机号和验证码不能为空|dui|dm_2015|projectStatusDescn|请输入正确的验证码|401|goods|PType|getCount|acc|images|406||png|未绑定|info|folder|yzclist|appear|hot|请稍后再试|itemDomain|countdown|sdlst|gexhTuijian|绑定|endtime|bind|yhcx|chooseGoodsLayer|istp|datetime|clone|choosegoods|priceID|optype|Immediately|_action|GotoShopping|TicketValidateType|QuestionPass|weibo_con|CRuleList|550|zhekou|cuxiao|cuxiao_grey|write|ShowSpeedList|ZRuleList|ParterCount|manjian|重新获取|zhekou_grey|timer|render|getDate|php|com|wbFollowIframe|innerHeight|uuid|weibo|callbackProxy|abovethetop|parse|widget|GetUserInfo|DaMaiTicketHistory|loginCallback|发送失败|toggleClass|next|projectPrivilegeCode|lazyload|发送成功|j_more|escape|scrolling|documentElement|hover|10000|GetSubscribeChannels|frameborder|lazy|background|question|h4|请在此留下您的问题|最多只能输入100字|pimg|project|TimeStr|fold|sbmitDy|clearTimeout|act|Price|adam|the|more2|more3|con|turnOnLayer|请输入正确的手机号码|j_count|请先选择演出时间|gm|prev|state|j_userName|userState|receive_mode|icon_less|geticon|placeholder|抱歉|mouseenter|您尚未取得购票资格|CheckLOLUser|privilegeType|expression|showQuehuodengji|mouseleave|fixed|aaa|layerQuehuodengji|取得购票资格后购买|del|keypress|请阅读项目介绍|setFreights|expires|toGMTString|referrer|initFlower|Object|unescape|loadwidget|initCountdown|toString|initGexhTuijian|initSpeedList|initLishi|catch|getMonth|first|36px|setResult|time|ShowTime|try|日|hh|layerCss|is_show_perform_calendar|yudingdengjiLayer|T1|您可选购电子票或上门自取|HMActiveSearchText|演出前三天不支持实票配送|演出前三天不支持配送|object|keyword|search_suggest_url|mailchange|tab|仅支持上门自取|call|initYhcx|quehuodengjiCount|getHours|getFullYear|path|cssstr|maskiframe|hostName|toBeAboutTo|player|maskLevel|已有|decodeURI|getSeconds|newlogin|已送花|lishiurl|getMinutes|商品稀缺请您优先购买现货|ProList|getMilliseconds|hash|setDate|人进行缺货登记|424|kaishoutixingLayer|secure|getSpeedList|fLogin2015|speedmoney|servertime|https||showSpeedList|ShowTotalMoney|grep|URL|ru|showTotalMoney|z_|projectAxis|getPart||708|template|00|flowerState|getCookieValue|GetQuehuoCount|utlis|用户登录|performName|jQueryDialog|speedcount|categoryID|ChildCategoryID||projectid|JustinBieber2015|ask|一|二|每周|每日|T7|js|用时|UserNick|产生金额|参与人数|col3|372|469|getIsYanZheng|initYanzheng|yanzheng|三|auto|促销信息|T41|expr|T21|发放|IsBuyQuotaFinish|RestrictType|HasRule|AutoUpperDate|below|getDay|T30|六|四||五|T4|T11|T2|每月|above|请稍后|热门推荐|000000000000|||_n|thumb|isEticket|projectids|suggest|sdbox2|hotProjects|加载中|decodeURIComponent|_t||one|giveFlower|continue|250|renderPriceList||Title|Tag|result|cainiXihuan|抢票速度榜|col1|暂无|万|TotalMoney|col2|人|最快用时|秒|effectspeed|load|projectPrivilegeDescn|j_c3|string|projectPrivilege|IsRestrictFinish|j_tips|toFixed|j_c|fadeIn|default|alpha|起|票价|1000000001|bindSoldout|suggestionProject|_n_171_214|scrollLeft|button|mtab|pic|GetSuggestionProjects|box|801|getcode|请输入您的手机号码|验证码发送间隔3分钟|802|手机号码不能为空|getmailcode||keyup|dycancel|000000|邮箱格式不正确|请输入您的邮箱地址|getmail_btn|allowTransparency|picker|||1000000002|maxnum|xz|repeat|initPerformCalendar|ShowWeekday|ss|mm|MM|week|ShowDate|MaxBuyCount|para|ajaxtype|clientHeight|hs|allpara|isassociate|ws|2000|initPage|ready|clientWidth|isFavorite|flowerInfo|提交成功|m_b_r|mingxingtuanti|btnBuyNow|btnVerifyPrivilege|isNaN|btnQuehuodengji|请输入联系人姓名|j_notes|textarea|预订成功|手机号格式输入有误|请输入联系人手机号|Image|600|seat|300|ww|onload|layerSeatPic|focus|请先选择要购买的票价|btnBuyNow3|btnSeatPic|btnSearch|blur|j_phone|提交订阅|appDownLayer|mask|btnBuyTicketValidate|hideItems|exc|是否要取消之前选择|dy3||lijidy|请勿重复点击|confirm|normal|dy2|请输入正确的手机号|linkGotoAsk|请输入手机号|btnYuyuedengji2|btnYuyuedengji|rss|btnKaishoutixing|buyTicketValidate|请输入您的答案|problem|tabProjectDescn|收起|club|light|followbutton|relationship|zh_cn|language|136|isFans|skin|speed|ptype|isWeibo|noborder|isTitle|刷新页面重试|提交失败|您的请求过于频繁|Condition|同城|市同城|亲爱的客户|element|plug|init|post|ddhhmmss|interval|fansRow|subscribeSingle|邮箱|短信|addChanelIds|系统异常|channels|客户端|邮箱格式不对|绑定邮箱成功|botton|mode_client|手机号码格式不对|绑定手机成功|share_self|getwidgetUID|2148|WeiboShow||sina|service|2279|Mail|Phone|channelId|clearInterval|setInterval|提交订阅失败|发货后1|cn_user|fadeOut|resize|charAt|u9FFF|u4E00|已订阅|stock|加|value|delete|slideDown|删除|perico|getInfo|register|j_loginForm|yyyy|StartTime|PerformID|sign|toLowerCase|_50_50|UserHeadPhotos|kaishoutixingPhone|nickname|onerror|本项目非选座购买订单|购买数量超出限制|3天到达|件|每单限购|天到达|香港|其它地区|澳门|发货后|台湾|aLi|getCartItem|减|j_goodsDetails'.split('|'), 0, {}));
var bindGoodstips = function() {
    var $goodstips = $('.m-goodstips'),
    $tips = $goodstips.find('.tips'),
    $hd = $goodstips.find('.hd'),
    $bd = $goodstips.find('.bd'),
    $box = $goodstips.find('.box');
    if ($box.height() > parseInt($box.css('line-height'))) {
        $tips.addClass('tips-multi');
        $bd.data('expand-height', $box.height() + parseInt($tips.css('padding-top')) + parseInt($tips.css('padding-bottom')));
        $bd.height($hd.height());
        $box.removeClass('z-hide');
        $tips.on('click',
        function() {
            $(this).toggleClass('z-show');
            if ($(this).hasClass('z-show')) {
                $bd.height($bd.data('expand-height'))
            } else {
                $bd.height($hd.height())
            }
        })
    } else {
        $box.removeClass('z-hide')
    }
};
var calendarSettings = [{
    year: 2014,
    month: 9,
    days: [{
        day: 8,
        holiday: true,
        festival: "中秋节"
    },
    {
        day: 10,
        holiday: false,
        festival: "教师节"
    },
    {
        day: 28,
        holiday: false
    }]
},
{
    year: 2014,
    month: 10,
    days: [{
        day: 1,
        holiday: true,
        festival: "国庆节"
    },
    {
        day: [2, 3, 4, 5, 6, 7],
        holiday: true
    },
    {
        day: 11,
        workday: true
    }]
},
{
    year: 2014,
    month: 12,
    days: [{
        day: 24,
        holiday: false,
        festival: "平安夜"
    },
    {
        day: 25,
        holiday: false,
        festival: "圣诞节"
    }]
},
{
    year: 2015,
    month: 1,
    days: [{
        day: 1,
        holiday: true,
        festival: "元旦"
    },
    {
        day: [2, 3],
        holiday: true
    },
    {
        day: 4,
        workday: true
    }]
},
{
    year: 2015,
    month: 2,
    days: [{
        day: 15,
        workday: true
    },
    {
        day: 18,
        holiday: true,
        festival: "除夕"
    },
    {
        day: 19,
        holiday: true,
        festival: "春节"
    },
    {
        day: [20, 21, 22, 23, 24],
        holiday: true
    },
    {
        day: 28,
        workday: true
    }]
},
{
    year: 2015,
    month: 4,
    days: [{
        day: 5,
        holiday: true,
        festival: "清明"
    },
    {
        day: 6,
        holiday: true
    }]
},
{
    year: 2015,
    month: 5,
    days: [{
        day: 1,
        holiday: true,
        festival: "劳动节"
    },
    {
        day: [2, 3],
        holiday: true
    }]
},
{
    year: 2015,
    month: 6,
    days: [{
        day: 20,
        holiday: true,
        festival: "端午节"
    },
    {
        day: [21, 22],
        holiday: true
    }]
},
{
    year: 2015,
    month: 9,
    days: [{
        day: 27,
        holiday: true,
        festival: "中秋节"
    }]
},
{
    year: 2015,
    month: 10,
    days: [{
        day: 1,
        holiday: true,
        festival: "国庆"
    },
    {
        day: [2, 3, 4, 5, 6, 7],
        holiday: true
    },
    {
        day: 10,
        workday: true
    }]
},
{
    year: 2015,
    month: 12,
    days: [{
        day: 24,
        festival: "平安夜"
    },
    {
        day: 25,
        festival: "圣诞节"
    }]
}];
var calendarChanged = function(year, month, date) {
    searchDate = new Date(year, month - 1, date).format("yyyy-MM-dd")
};
Date.prototype.format = function(fmt) {
    var o = {
        "M+": this.getMonth() + 1,
        "d+": this.getDate(),
        "h+": this.getHours(),
        "m+": this.getMinutes(),
        "s+": this.getSeconds(),
        "q+": Math.floor((this.getMonth() + 3) / 3),
        "S": this.getMilliseconds()
    };
    if (/(y+)/.test(fmt)) {
        fmt = fmt.replace(RegExp.$1, (this.getFullYear() + "").substr(4 - RegExp.$1.length))
    }
    for (var k in o) {
        if (new RegExp("(" + k + ")").test(fmt)) {
            fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)))
        }
    }
    return fmt
};
function getSetting(date) {
    var ret = {};
    var month = date.getMonth() + 1,
    day = date.getDate(),
    year = date.getFullYear();
    for (var i = 0; i < calendarSettings.length; i++) {
        if (calendarSettings[i].month == month && calendarSettings[i].year == year) {
            for (var j = 0; j < calendarSettings[i].days.length; j++) {
                var sday = calendarSettings[i].days[j];
                if (day == sday.day) {
                    ret = sday;
                    break
                } else if (Object.prototype.toString.call(sday.day) == "[object Array]") {
                    var isBreak = false;
                    for (var d = 0; d < sday.day.length; d++) {
                        if (sday.day[d] == day) {
                            ret = {
                                day: day,
                                holiday: sday.holiday,
                                festival: sday.festival
                            };
                            isBreak = true;
                            break
                        }
                    }
                    if (isBreak) break
                }
            }
            break
        }
    }
    ret.hasProject = hasProject(date);
    ret.isSearchDate = isSearchDate(date);
    ret.isShowDate = isShowDate(date);
    return ret
}
function hasProject(date) {
    var k = "D" + date.format("yyyyMMdd");
    if (window.projectDates[k]) return true;
    return false
}
function isSearchDate(date) {
    if (typeof(searchDate) == 'string' && date.format("yyyy-MM-dd") == searchDate) return true;
    return false
}
function isShowDate(date) {
    if (typeof(showDate) == 'string' && date.format("yyyy-MM-dd") == showDate) {
        return true
    }
    return false
}
function calendarChanged(y, m, d) {
    window["searchDate"] = y + "-" + padding(m, 2) + "-" + padding(d, 2)
}
function padding(s, l) {
    s += "";
    if (s.length >= l) {
        return s
    }
    return "0000000000".substr(0, l - s.length) + s
}
var ie = navigator.appName == "Microsoft Internet Explorer" ? true: false;
var controlid = null;
var currdate = null;
var startdate = null;
var enddate = null;
var yy = null;
var mm = null;
var hh = null;
var ii = null;
var currday = null;
var addtime = false;
var today = new Date();
var lastcheckedyear = false;
var lastcheckedmonth = false;
function _cancelBubble(event) {
    e = event ? event: window.event;
    if (ie) {
        e.cancelBubble = true
    } else {
        e.stopPropagation()
    }
}
function getposition(obj) {
    var r = new Array();
    r['x'] = obj.offsetLeft;
    r['y'] = obj.offsetTop;
    while (obj = obj.offsetParent) {
        r['x'] += obj.offsetLeft;
        r['y'] += obj.offsetTop
    }
    return r
}
function loadcalendar() {
    s = '';
    s += '<div id="calendar" style="display:none; position:absolute; z-index:9;" onclick="_cancelBubble(event)">';
    if (ie) {}
    s += '<div style="width:auto; border:2px solid #c6cdd2; margin-top:10px;"><table class="tableborder" cellspacing="0" cellpadding="0" width="100%" style="text-align: center;">';
    s += '<tr align="center"><td colspan="7" class="dateheader" style="text-align: left; padding-left:20px; height: 30px;"><a href="javascript:;" onclick="refreshcalendar(yy, mm-1);return false" title="上一月" class="mr15"><</a><a href="javascript:;" onclick="showdiv(\'year\');_cancelBubble(event);return false" title="点击选择年份" id="year" style=" font-weight:400"></a><span class="ml5 mr10 f16 c1">年</span><a id="month" title="点击选择月份" href="javascript:;" style=" font-weight:400" onclick="showdiv(\'month\');_cancelBubble(event);return false"></a><span class="ml5 mr10 f16 c1">月</span><A href="javascript:;" onclick="refreshcalendar(yy, mm+1);return false" title="下一月" class="ml15">></A><a href="javascript:;" class="new-today" onclick="gotoToday();" style="font-size:12px; font-weight:400">返回今天</a><a href="javascript:;" class="new-error" style="font-size:12px; font-weight:400" onclick="hideCalendar();"></a></td></tr>';
    s += '<tr class="category"><td style="height: 30px;">星期日</td><td style="height: 30px;">星期一</td><td style="height: 30px;">星期二</td><td style="height: 30px;">星期三</td><td style="height: 30px;">星期四</td><td style="height: 30px;">星期五</td><td style="height: 30px;">星期六</td></tr>';
    for (var i = 0; i < 6; i++) {
        s += '<tr class="altbg2">';
        for (var j = 1; j <= 7; j++) {
            s += "<td id=d" + (i * 7 + j) + " height=\"190\">0<br>111</td>"
        }
        s += "</tr>"
    }
    s += '</table></div></div>';
    s += '<div id="calendar_year" onclick="_cancelBubble(event)"><div class="col">';
    for (var k = 2014; k <= 2018; k++) {
        s += k != 2014 && k % 10 == 0 ? '</div><div class="col">': '';
        s += '<a href="javascript:;" onclick="refreshcalendar(' + k + ', mm);document.getElementById(\'calendar_year\').style.display=\'none\';return false"><span' + (today.getFullYear() == k ? ' class="today"': '') + ' id="calendar_year_' + k + '">' + k + '</span></a><br />'
    }
    s += '</div></div>';
    s += '<div id="calendar_month" onclick="_cancelBubble(event)">';
    for (var k = 1; k <= 12; k++) {
        s += '<a href="javascript:;" onclick="refreshcalendar(yy, ' + (k - 1) + ');document.getElementById(\'calendar_month\').style.display=\'none\';return false"><span' + (today.getMonth() + 1 == k ? ' class="today"': '') + ' id="calendar_month_' + k + '">' + k + (k < 10 ? ' ': '') + ' 月</span></a><br />'
    }
    s += '</div>';
    var nElement = document.createElement("div");
    nElement.innerHTML = s;
    document.getElementsByTagName("body")[0].appendChild(nElement);
    document.onclick = function(event) {
        document.getElementById('calendar').style.display = 'none';
        document.getElementById('calendar_year').style.display = 'none';
        document.getElementById('calendar_month').style.display = 'none'
    };
    document.getElementById('calendar').onclick = function(event) {
        _cancelBubble(event);
        document.getElementById('calendar_year').style.display = 'none';
        document.getElementById('calendar_month').style.display = 'none'
    }
}
function parsedate(s) {
    s = s.replace(/\./g, '-');
    /(\d+)\-(\d+)\-(\d+)\s*(\d*):?(\d*)/.exec(s);
    var m1 = (RegExp.$1 && RegExp.$1 > 1899 && RegExp.$1 < 2101) ? parseFloat(RegExp.$1) : today.getFullYear();
    var m2 = (RegExp.$2 && (RegExp.$2 > 0 && RegExp.$2 < 13)) ? parseFloat(RegExp.$2) : today.getMonth() + 1;
    var m3 = (RegExp.$3 && (RegExp.$3 > 0 && RegExp.$3 < 32)) ? parseFloat(RegExp.$3) : today.getDate();
    var m4 = (RegExp.$4 && (RegExp.$4 > -1 && RegExp.$4 < 24)) ? parseFloat(RegExp.$4) : 0;
    var m5 = (RegExp.$5 && (RegExp.$5 > -1 && RegExp.$5 < 60)) ? parseFloat(RegExp.$5) : 0;
    /(\d+)\-(\d+)\-(\d+)\s*(\d*):?(\d*)/.exec("0000-00-00 00\:00");
    return new Date(m1, m2 - 1, m3, m4, m5)
}
function settime(d) { (calendarChanged ||
    function(date) {})(yy, mm + 1, d)
}
function showcalendar(event, controlid1, addtime1, startdate1, enddate1) {
    controlid = controlid1;
    addtime = addtime1;
    startdate = startdate1 ? parsedate(startdate1) : false;
    enddate = enddate1 ? parsedate(enddate1) : false;
    var dv = controlid.getAttribute("data-value");
    if (window.searchDate && false) {
        currday = parsedate(window["searchDate"].replace(/\-/g, '.'))
    } else if (dv && dv.length > 0) {
        currday = parsedate(dv);
        window["searchDate"] = window["showDate"] = currday.format("yyyy-MM-dd")
    } else {
        currday = today
    }
    hh = currday.getHours();
    ii = currday.getMinutes();
    var p = getposition(controlid);
    document.getElementById('calendar').style.display = 'block';
    document.getElementById('calendar').style.left = (p['x'] - document.getElementById('calendar').offsetWidth + 20) + 'px';
    document.getElementById('calendar').style.top = (p['y'] + 20) + 'px';
    _cancelBubble(event);
    refreshcalendar(currday.getFullYear(), currday.getMonth());
    if (lastcheckedyear != false) {
        document.getElementById('calendar_year_' + lastcheckedyear).className = 'default';
        document.getElementById('calendar_year_' + today.getFullYear()).className = 'today'
    }
    if (lastcheckedmonth != false) {
        document.getElementById('calendar_month_' + lastcheckedmonth).className = 'default';
        document.getElementById('calendar_month_' + (today.getMonth() + 1)).className = 'today'
    }
    document.getElementById('calendar_year_' + currday.getFullYear()).className = 'checked';
    document.getElementById('calendar_month_' + (currday.getMonth() + 1)).className = 'checked';
    document.getElementById('hourminute').style.display = addtime ? '': 'none';
    lastcheckedyear = currday.getFullYear();
    lastcheckedmonth = currday.getMonth() + 1
}
function refreshcalendar(y, m) {
    var x = new Date(y, m, 1);
    var mv = x.getDay();
    var d = x.getDate();
    var dd = null;
    yy = x.getFullYear();
    mm = x.getMonth();
    document.getElementById("year").innerHTML = yy;
    document.getElementById("month").innerHTML = mm + 1 > 9 ? (mm + 1) : '0' + (mm + 1);
    for (var i = 1; i <= mv; i++) {
        dd = document.getElementById("d" + i);
        dd.innerHTML = " ";
        dd.className = ""
    }
    while (x.getMonth() == mm) {
        dd = document.getElementById("d" + (d + mv));
        var setting = getSetting(x);
        var clsN = "default ";
        var innerHTML = '<div class="calendar-relative calendar-active" onclick="settime(' + d + ');return false" onmouseover="calendarOver(this)" onmouseout="calendarOut(this)"><span class="date">' + d + '</span>';
        if (setting.hasProject) {
            clsN += 'new-pic-dian '
        }
        if (x.getFullYear() == today.getFullYear() && x.getMonth() == today.getMonth() && x.getDate() == today.getDate()) {
            dd.title = '今天'
        }
        if (setting.isSearchDate) {
            clsN += "checked "
        }
        if (setting.isShowDate && performCount == 0) {
            clsN += 'new-pic-dian '
        }
        if (setting.holiday) {
            clsN += 'new-pic-holiday '
        } else if (setting.workday) {
            clsN += 'new-pic-work '
        }
        if (setting.festival && setting.festival.length > 0) {
            innerHTML += "<span class='fest'>" + setting.festival + "</span>"
        }
        innerHTML += "</div>";
        dd.innerHTML = innerHTML;
        dd.className = clsN;
        x.setDate(++d)
    }
    while (d + mv <= 42) {
        dd = document.getElementById("d" + (d + mv));
        dd.innerHTML = " ";
        d++
    }
    if (addtime) {
        document.getElementById('hour').value = zerofill(hh);
        document.getElementById('minute').value = zerofill(ii)
    }
    var calendar = document.getElementById('calendar');
    var table = calendar.getElementsByTagName('table')[0];
    var trs = table.getElementsByTagName('tr');
    var tds = table.getElementsByTagName('td');
    var days = [];
    var lines = [];
    for (var i = 0,
    len = trs.length; i < len; i++) {
        if (trs[i].className.indexOf('altbg2') !== -1) lines.push(trs[i])
    }
    for (var i = 0,
    len = lines.length; i < len; i++) {
        if (isEmptyLine(lines[i])) {
            lines[i].style.display = 'none'
        } else {
            lines[i].style.display = ''
        }
    }
    for (var i = 0,
    len = tds.length; i < len; i++) {
        if (tds[i].className.indexOf('new-pic-dian') !== -1) days.push(tds[i])
    }
    for (var i = 0,
    len = days.length; i < len; i++) {
        if (!/[^\s\b\r\t]/i.test(days[i].innerHTML)) days[i].className = ''
    }
    function isEmptyLine(element) {
        var itms = element.getElementsByTagName('td');
        var empty = true;
        for (var i = 0,
        len = itms.length; i < len; i++) {
            if (/[^\s\b\r\t]/i.test(itms[i].innerHTML)) {
                empty = false;
                break
            }
        }
        return empty
    }
}  
