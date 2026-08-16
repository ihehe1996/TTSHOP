<?php defined('TT_ROOT') || exit('access denied!'); ?>

test

<script>
    $('#menu-order').addClass('open');
    $('#menu-order > ul').css('display', 'block');
    $('#menu-order > a > i.nav_right').attr('class', 'fa fa-angle-down nav_right');
    $('#menu-order-test').addClass('menu-current');
</script>