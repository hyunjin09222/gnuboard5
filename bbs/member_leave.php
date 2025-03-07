<?php
include_once('./_common.php');

if (!$member['mb_id'])
    alert('회원만 접근하실 수 있습니다.');

if ($is_admin == 'super')
    alert('최고 관리자는 탈퇴할 수 없습니다');

$post_mb_password = isset($_POST['mb_password']) ? trim($_POST['mb_password']) : '';

if (!($post_mb_password && check_password($post_mb_password, $member['mb_password'])))
    alert('비밀번호가 틀립니다.');

// 회원탈퇴일을 저장
$date = date("Ymd");
$sql = " update {$g5['member_table']} set mb_leave_date = '{$date}', mb_memo = '".date('Ymd', G5_SERVER_TIME)." 탈퇴함\n".sql_real_escape_string($member['mb_memo'])."', mb_certify = '', mb_adult = 0, mb_dupinfo = '' where mb_id = '{$member['mb_id']}' ";
sql_query($sql);

run_event('member_leave', $member);

// 3.09 수정 (로그아웃)
unset($_SESSION['ss_mb_id']);

if (!$url)
    $url = G5_URL;

//소셜로그인 해제
if(function_exists('social_member_link_delete')){
    social_member_link_delete($member['mb_id']);
}

member_delete($member['mb_id']);
member_delete_lineup($member['mb_email']);
session_unset(); // 모든 세션변수를 언레지스터 시켜줌
session_destroy(); // 세션해제함

alert(''.$member['mb_nick'].'님께서는 '. date("Y년 m월 d일") .'에 회원에서 탈퇴 하셨습니다.', $url);