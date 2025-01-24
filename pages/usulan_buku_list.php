<?php
/**
 * @Created by          : Erwan Setyo Budi
 * @Date                : 2025-01-24
 * @File name           : usulan_buku.php
 */
use SLiMS\DB;
use SLiMS\Plugins;
use SLiMS\Table\Schema;
use SLiMS\Url;

defined('INDEX_AUTH') OR die('Direct access not allowed!');

// IP based access limitation
require LIB . 'ip_based_access.inc.php';
do_checkIP('smc');
do_checkIP('smc-bibliography');

// start the session
require SB . 'admin/default/session.inc.php';
require SB.'admin/default/session_check.inc.php';
require SIMBIO . 'simbio_GUI/table/simbio_table.inc.php';
require SIMBIO . 'simbio_GUI/form_maker/simbio_form_table_AJAX.inc.php';
require SIMBIO . 'simbio_GUI/paging/simbio_paging.inc.php';
require SIMBIO . 'simbio_DB/datagrid/simbio_dbgrid.inc.php';
require SIMBIO.'simbio_DB/simbio_dbop.inc.php';


// privileges checking
$can_read = utility::havePrivilege('bibliography', 'r');
$can_write = utility::havePrivilege('bibliography', 'w'); // Tambahkan definisi $can_write

if (!$can_read) {
    die('<div class="errorBox">' . __('You are not authorized to view this section') . '</div>');
}

/* RECORD OPERATION */
if (isset($_POST['saveData']) AND $can_read AND $can_write) {
    $data = [
        'nama_lengkap' => trim(strip_tags($_POST['nama_lengkap'])),
        'nomor_anggota' => trim(strip_tags($_POST['nomor_anggota'])),
        'institution' => trim(strip_tags($_POST['institution'] ?? null)),
        'kontak' => trim(strip_tags($_POST['kontak'] ?? null)),
        'judul_buku' => trim(strip_tags($_POST['judul_buku'])),
        'pengarang' => trim(strip_tags($_POST['pengarang'] ?? null)),
        'tahun_terbit' => trim(strip_tags($_POST['tahun_terbit'] ?? null)),
        'penerbit' => trim(strip_tags($_POST['penerbit'] ?? null)),
        'isbn' => trim(strip_tags($_POST['isbn'] ?? null)),
        'kategori' => trim(strip_tags($_POST['kategori'] ?? null)),
        'format' => trim(strip_tags($_POST['format'] ?? null)),
        'bahasa' => trim(strip_tags($_POST['bahasa'] ?? null)),
        'harga' => trim(strip_tags($_POST['harga'] ?? null)),
        'tautan' => trim(strip_tags($_POST['tautan'] ?? null)),
        'alasan' => trim(strip_tags($_POST['alasan'] ?? null)),
        'tanggapan' => trim(strip_tags($_POST['tanggapan']))
    ];

    if (empty($data['nama_lengkap']) || empty($data['nomor_anggota']) || empty($data['judul_buku']) || empty($data['alasan'])) {
        toastr(__('Fields marked with * are required'))->error();
        exit();
    }

    $sql_op = new simbio_dbop($dbs);

    if (isset($_POST['updateRecordID'])) {
        $updateRecordID = $dbs->escape_string(trim($_POST['updateRecordID']));
        if ($sql_op->update('usulan_buku', $data, 'id='.$updateRecordID)) {
            toastr(__('Data Successfully Updated'))->success();
            echo '<script type="text/javascript">parent.jQuery(\'#mainContent\').simbioAJAX(parent.jQuery.ajaxHistory[0].url);</script>';
        } else {
            toastr(__('Data FAILED to Update. Please Contact System Administrator')."\nDEBUG : " . $sql_op->error)->error();
        }
        exit();
    } else {
        if ($sql_op->insert('usulan_buku', $data)) {
            toastr(__('New Data Successfully Saved'))->success();
            echo '<script type="text/javascript">parent.jQuery(\'#mainContent\').simbioAJAX(\''.$_SERVER['PHP_SELF'].'\');</script>';
        } else {
            toastr(__('Data FAILED to Save. Please Contact System Administrator')."\nDEBUG : " . $sql_op->error)->error();
        }
        exit();
    }
} else if (isset($_POST['itemID']) AND !empty($_POST['itemID']) AND isset($_POST['itemAction'])) {
    $sql_op = new simbio_dbop($dbs);
    $error_num = 0;
    if (!is_array($_POST['itemID'])) {
        $_POST['itemID'] = array((integer)$_POST['itemID']);
    }

    foreach ($_POST['itemID'] as $itemID) {
        $itemID = (integer)$itemID;
        if (!$sql_op->delete('usulan_buku', 'id='.$itemID)) {
            $error_num++;
        }
    }

    if ($error_num == 0) {
        toastr(__('All Data Successfully Deleted'))->success();
    } else {
        toastr(__('Some or All Data NOT deleted successfully!'))->warning();
    }
    exit();
}
/* RECORD OPERATION END */

/* search form */
?>
<div class="menuBox">
<div class="menuBoxInner masterFileIcon">
    <div class="per_title">
        <h2><?php echo __('Usul Buku'); ?></h2>
  </div>
    <div class="sub_section">
      <div class="btn-group">
      <a href="<?php echo SWB; ?>plugins/member_area_card/pages/usulan_buku_list.php" class="btn btn-default"><?php echo __('Daftar Usul Buku'); ?></a>
      <a href="<?php echo SWB; ?>plugins/member_area_card/pagesusulan_buku_list.php?action=detail" class="btn btn-default"><?php echo __('Usul Buku Baru'); ?></a>
      </div>
    <form name="search" action="<?php echo SWB; ?>plugins/member_area_card/pages/usulan_buku_list.php" id="search" method="get" class="form-inline"><?php echo __('Search'); ?> 
    <input type="text" name="keywords" class="form-control col-md-3" />
    <input type="submit" id="doSearch" value="<?php echo __('Search'); ?>" class="s-btn btn btn-default" />
    </form>
  </div>
</div>
</div>
<?php
/* search form end */
/* main content */
if (isset($_POST['detail']) OR (isset($_GET['action']) AND $_GET['action'] == 'detail')) {
    if (!($can_read AND $can_write)) {
        die('<div class="errorBox">'.__('You don\'t have enough privileges to view this section').'</div>');
    }
    /* RECORD FORM */
    $itemID = (integer)isset($_POST['itemID'])?$_POST['itemID']:0;
    $rec_q = $dbs->query('SELECT * FROM usulan_buku WHERE id='.$itemID);
    $rec_d = $rec_q->fetch_assoc();

    // create new instance
    $form = new simbio_form_table_AJAX('mainForm', $_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'], 'post');
    $form->submit_button_attr = 'name="saveData" value="'.__('Save').'" class="s-btn btn btn-default"';

    // form table attributes
    $form->table_attr = 'id="dataList" class="s-table table"';
    $form->table_header_attr = 'class="alterCell font-weight-bold"';
    $form->table_content_attr = 'class="alterCell2"';

    if ($rec_q->num_rows > 0) {
        $form->edit_mode = true;
        $form->record_id = $itemID;
        $form->record_title = $rec_d['judul_buku'];
        $form->submit_button_attr = 'name="saveData" value="'.__('Update').'" class="s-btn btn btn-primary"';
    }

    $form->addTextField('text', 'nama_lengkap', __('Nama Lengkap').'*', $rec_d['nama_lengkap'] ?? '', 'style="width: 60%;" class="form-control"');
    $form->addTextField('text', 'nomor_anggota', __('Nomor Anggota').'*', $rec_d['nomor_anggota'] ?? '', 'style="width: 60%;" class="form-control"');
    $form->addTextField('text', 'institution', __('Institusi'), $rec_d['institution'] ?? '', 'style="width: 60%;" class="form-control"');
    $form->addTextField('text', 'kontak', __('Kontak'), $rec_d['kontak'] ?? '', 'style="width: 60%;" class="form-control"');
    $form->addTextField('text', 'judul_buku', __('Judul Buku').'*', $rec_d['judul_buku'] ?? '', 'style="width: 60%;" class="form-control"');
    $form->addTextField('text', 'pengarang', __('Pengarang'), $rec_d['pengarang'] ?? '', 'style="width: 60%;" class="form-control"');
    $form->addTextField('text', 'tahun_terbit', __('Tahun Terbit'), $rec_d['tahun_terbit'] ?? '', 'style="width: 60%;" class="form-control"');
    $form->addTextField('text', 'penerbit', __('Penerbit'), $rec_d['penerbit'] ?? '', 'style="width: 60%;" class="form-control"');
    $form->addTextField('text', 'isbn', __('ISBN'), $rec_d['isbn'] ?? '', 'style="width: 60%;" class="form-control"');
    $form->addTextField('text', 'kategori', __('Kategori'), $rec_d['kategori'] ?? '', 'style="width: 60%;" class="form-control"');
    $form->addTextField('text', 'format', __('Format'), $rec_d['format'] ?? '', 'style="width: 60%;" class="form-control"');
    $form->addTextField('text', 'bahasa', __('Bahasa'), $rec_d['bahasa'] ?? '', 'style="width: 60%;" class="form-control"');
    $form->addTextField('text', 'harga', __('Harga'), $rec_d['harga'] ?? '', 'style="width: 60%;" class="form-control"');
    $form->addTextField('text', 'tautan', __('Tautan'), $rec_d['tautan'] ?? '', 'style="width: 60%;" class="form-control"');
    $form->addTextField('text', 'alasan', __('Alasan').'*', $rec_d['alasan'] ?? '', 'style="width: 60%;" class="form-control"');
    $form->addTextField('text', 'tanggapan', __('tanggapan').'*', $rec_d['tanggapan'] ?? '', 'style="width: 60%;" class="form-control"');


    // edit mode messagge
    if ($form->edit_mode) {
        echo '<div class="infoBox">'.__('You are going to edit visitor room data').' : <b>'.$rec_d['nama_lengkap'].'</b>  <br />'.__('Last Update').' '.$rec_d['updated_at'].'</div>'; //mfc
    }
    // print out the form object
    echo $form->printOut();
} else {
    /* GMD LIST */
    // table spec
    $table_spec = 'usulan_buku AS g';

    // create datagrid
    $datagrid = new simbio_datagrid();
    if ($can_read AND $can_write) {
      $datagrid->setSQLColumn('g.id',
        'g.nama_lengkap AS \''.__('Nama').'\'',
        'g.judul_buku AS \''.__('Judul Buku').'\'',
        'g.tanggapan AS \''.__('Tanggapan').'\'',
        'g.tanggal_usulan AS \''.__('Tanggal Usulan').'\'');
    } else {
      $datagrid->setSQLColumn('g.nama_lengkap AS \''.__('Nama Lengkap').'\'',
        'g.judul_buku AS \''.__('Judul Buku').'\'',
        'g.tanggapan AS \''.__('tanggapan').'\'',
        'g.tanggal_usulan AS \''.__('Tanggal Usulan').'\'');

    }


    $datagrid->setSQLorder('nama_lengkap ASC');
   

    // is there any search
    if (isset($_GET['keywords']) AND $_GET['keywords']) {
       $keywords = $dbs->escape_string($_GET['keywords']);
       $datagrid->setSQLCriteria("g.nama_lengkap LIKE '%$keywords%' OR g.judul_buku LIKE '%$keywords%'");
    }

    // set table and table header attributes
    $datagrid->table_attr = 'id="dataList" class="s-table table"';
    $datagrid->table_header_attr = 'class="dataListHeader" style="font-weight: bold;"';
    // set delete proccess URL
    #$datagrid->chbox_form_URL = $_SERVER['PHP_SELF'].'?type='.$type;
    $datagrid->chbox_form_URL = $_SERVER['PHP_SELF'].'?type=';

    // put the result into variables
    $datagrid_result = $datagrid->createDataGrid($dbs, $table_spec, 20, ($can_read AND $can_write));
    if (isset($_GET['keywords']) AND $_GET['keywords']) {
        $msg = str_replace('{result->num_rows}', $datagrid->num_rows, __('Found <strong>{result->num_rows}</strong> from your keywords')); //mfc
        echo '<div class="infoBox">'.$msg.' : "'.htmlentities($_GET['keywords']).'"</div>';
    }

    echo $datagrid_result;
}
/* main content end */
