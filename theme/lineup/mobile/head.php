<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

if (G5_COMMUNITY_USE === false) {
    define('G5_IS_COMMUNITY_PAGE', true);
    include_once(G5_THEME_SHOP_PATH . '/shop.head.php');
    return;
}

include_once(G5_THEME_PATH . '/head.sub.php');
include_once(G5_LIB_PATH . '/latest.lib.php');
include_once(G5_LIB_PATH . '/outlogin.lib.php');
include_once(G5_LIB_PATH . '/poll.lib.php');
include_once(G5_LIB_PATH . '/visit.lib.php');
include_once(G5_LIB_PATH . '/connect.lib.php');
include_once(G5_LIB_PATH . '/popular.lib.php');
?>

<header id="hd">
    <?php include_once(LINEUP_PATH . '/menu_top.php'); ?>

    
</header>
<ins class="kakao_ad_area" style="display:none;"
data-ad-unit = "DAN-mDrxvKx1MWRqXxit"
data-ad-width = "320"
data-ad-height = "50"></ins>
<script type="text/javascript" src="//t1.daumcdn.net/kas/static/ba.min.js" async></script>
<div id="wrapper">

    <div id="container">
        <?php if (!defined("_INDEX_")) { ?>
            <h2 id="container_title" class="top" title="<?php echo get_text($g5['title']); ?>">
                <a href="javascript:history.back();"><i class="fa fa-chevron-left" aria-hidden="true"></i><span class="sound_only">뒤로가기</span></a> <?php echo get_head_title($g5['title']); ?>
            </h2>
        <?php }
