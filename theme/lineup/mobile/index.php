<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

include_once(LINEUP_PATH . "/functions.php");

if (G5_COMMUNITY_USE === false) {
    include_once(G5_THEME_MSHOP_PATH . '/index.php');
    return;
}

include_once(G5_THEME_MOBILE_PATH . '/head.php');

?>

<!-- 메인화면 최신글 시작 -->
<?php
    $req = isset($_GET['req']) ? $_GET['req'] : null;
    switch ($req) {
        case null:
            if ($is_member)
            include_once(LINEUP_PATH . "/myteam.php");

            // 이 함수가 바로 최신글을 추출하는 역할을 합니다.
            // 사용방법 : latest(스킨, 게시판아이디, 출력라인, 글자수);
            // 테마의 스킨을 사용하려면 theme/basic 과 같이 지정    
            echo latest('theme/basic', 'notice', 4, 23);		// 최소설치시 자동생성되는 공지사항게시판
            echo googleAd('banner');
            echo latest('theme/basic', 'free', 4, 23);		// 최소설치시 자동생성되는 자유게시판
            echo latest('theme/basic', 'qa', 4, 23);			// 최소설치시 자동생성되는 질문답변게시판            
            break;
        default:
            include_once(LINEUP_PATH . "/" . $req . ".php");
  }
?>
<!-- 메인화면 최신글 끝 -->



<?php
include_once(G5_THEME_MOBILE_PATH . '/tail.php');
