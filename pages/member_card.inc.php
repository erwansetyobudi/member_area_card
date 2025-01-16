<?php
// check if member already logged in
$is_member_login = utility::isMemberLogin();

if ($is_member_login == false) {
    header('Location: ?p=member');
    exit;
}

$member_q = $dbs->query('SELECT m.member_name, m.member_id, m.member_image, m.member_address, m.member_email, m.inst_name, m.postal_code, m.pin, m.member_phone, m.expire_date, m.register_date, mt.member_type_name FROM member AS m
        LEFT JOIN mst_member_type AS mt ON m.member_type_id=mt.member_type_id
        WHERE m.member_id = \'' . $dbs->escape_string($_SESSION['mid']) . '\'');

$member_datas[] = $member_q->fetch_assoc();

// include printed settings configuration file
include SB.'admin'.DS.'admin_template'.DS.'printed_settings.inc.php';

// load print settings from database to override value from printed_settings file
loadPrintSettings($dbs, 'membercard');

// chunk cards array
$chunked_card_arrays = array_chunk($member_datas, $card_ = $sysconf['print']['membercard']['items_per_row']);

// create html ouput
ob_start();
$card_conf = $sysconf['print']['membercard'];
$card_template = $card_conf['template'];
$card_path = SWB.FLS.DS.'membercard'.DS.$card_template.DS;
$card_logo = $card_path.IMG.DS.$card_conf['logo'];
$card_stamp = $card_path.IMG.DS.$card_conf['stamp_file'];
$card_signature = $card_path.IMG.DS.$card_conf['signature_file'];

require_once SB.FLS.DS.'membercard'.DS.$card_template.DS.'membercard.php';
exit(ob_get_clean());
