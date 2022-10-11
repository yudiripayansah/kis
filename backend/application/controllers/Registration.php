<?php
defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

class Registration extends RestController
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('model_registration');
        date_default_timezone_set('Asia/Jakarta');
    }

    function membership_post()
    {
        $headers = $this->input->request_headers();
        $token = (isset($headers['Token'])) ? $headers['Token'] : FALSE;

        $cif_id  = md5(date('Y-m-d H:i:s'));

        $cm_code = $this->input->post('cm_code');
        $nama = $this->input->post('nama');
        $panggilan = $this->input->post('panggilan');
        $kelompok = $this->input->post('kelompok');
        $setoran_lwk = $this->input->post('setoran_lwk');
        $setoran_mingguan = $this->input->post('setoran_mingguan');
        $tgl_gabung = date('Y-m-d', strtotime(str_replace('/', '-', $this->input->post('tgl_gabung'))));
        $referensi = $this->input->post('referensi');
        $pribadi_jenis_kelamin = $this->input->post('pribadi_jenis_kelamin');
        $pribadi_ibu_kandung = $this->input->post('pribadi_ibu_kandung');
        $pribadi_tmp_lahir = $this->input->post('pribadi_tmp_lahir');
        $pribadi_tgl_lahir = date('Y-m-d', strtotime(str_replace('/', '-', $this->input->post('pribadi_tgl_lahir'))));
        $pribadi_usia = $this->input->post('pribadi_usia');
        $pribadi_no_ktp = $this->input->post('pribadi_no_ktp');
        $pribadi_no_hp = $this->input->post('pribadi_no_hp');
        $pribadi_pendidikan = $this->input->post('pribadi_pendidikan');
        $pribadi_pekerjaan = $this->input->post('pribadi_pekerjaan');
        $pribadi_ket_pekerjaan = $this->input->post('pribadi_ket_pekerjaan');
        $pribadi_literasi_latin = $this->input->post('pribadi_literasi_latin');
        $pribadi_literasi_arab = $this->input->post('pribadi_literasi_arab');
        $pasangan_jmlkeluarga = $this->input->post('pasangan_jmlkeluarga');
        $pasangan_jmltanggungan = $this->input->post('pasangan_jmltanggungan');
        $status_perkawinan = $this->input->post('status_perkawinan');
        $pasangan_nama = $this->input->post('pasangan_nama');
        $pasangan_tmplahir = $this->input->post('pasangan_tmplahir');
        $pasangan_tglahir = date('Y-m-d', strtotime(str_replace('/', '-', $this->input->post('pasangan_tglahir'))));
        $pasangan_usia = $this->input->post('pasangan_usia');
        $pasangan_no_ktp = $this->input->post('pasangan_no_ktp');
        $pasangan_no_hp = $this->input->post('pasangan_no_hp');
        $pasangan_pendidikan = $this->input->post('pasangan_pendidikan');
        $pasangan_pekerjaan = $this->input->post('pasangan_pekerjaan');
        $pasangan_ketpekerjaan = $this->input->post('pasangan_ketpekerjaan');
        $pasangan_literasi_latin = $this->input->post('pasangan_literasi_latin');
        $pasangan_literasi_arab = $this->input->post('pasangan_literasi_arab');
        $pribadi_alamat = $this->input->post('pribadi_alamat');
        $rt = $this->input->post('rt');
        $rw = $this->input->post('rw');
        $pribadi_desa = $this->input->post('pribadi_desa');
        $pribadi_kecamatan = $this->input->post('pribadi_kecamatan');
        $pribadi_kabupaten = $this->input->post('pribadi_kabupaten');
        $pribadi_kodepos = $this->input->post('pribadi_kodepos');
        $pribadi_koresponden_alamat = $this->input->post('pribadi_koresponden_alamat');
        $koresponden_rt = $this->input->post('koresponden_rt');
        $koresponden_rw = $this->input->post('koresponden_rw');
        $pribadi_koresponden_desa = $this->input->post('pribadi_koresponden_desa');
        $pribadi_koresponden_kecamatan = $this->input->post('pribadi_koresponden_kecamatan');
        $pribadi_koresponden_kabupaten = $this->input->post('pribadi_koresponden_kabupaten');
        $pribadi_koresponden_kodepos = $this->input->post('pribadi_koresponden_kodepos');
        $rmhstatus = $this->input->post('rmhstatus');
        $rmhjamban = $this->input->post('rmhjamban');
        $rmhair = $this->input->post('rmhair');
        $rmhbahanbakar = $this->input->post('rmhbahanbakar');
        $lahansawah = $this->input->post('lahansawah');
        $lahankebun = $this->input->post('lahankebun');
        $ternakunggas = $this->input->post('ternakunggas');
        $ternakdomba = $this->input->post('ternakdomba');
        $ternakkerbau = $this->input->post('ternakkerbau');
        $kendsepeda = $this->input->post('kendsepeda');
        $kendmotor = $this->input->post('kendmotor');
        $kendmobil = $this->input->post('kendmobil');
        $elekkulkas = $this->input->post('elekkulkas');
        $elekmesincuci = $this->input->post('elekmesincuci');
        $elektv = $this->input->post('elektv');
        $elekkomputer = $this->input->post('elekkomputer');
        $elekhape = $this->input->post('elekhape');
        $elekvcd = $this->input->post('elekvcd');

        $now = date('Y-m-d');

        if ($token) {
            $check_token = $this->model_registration->check_token($token);

            if ($check_token['cnt'] > 0) {
                $check_expired = $this->model_registration->check_expired($now, $token);

                if ($check_expired['expired'] > 7) {
                    $res = [
                        'status' => FALSE,
                        'msg' => 'Token Expired',
                        'data' => NULL
                    ];
                } else {
                    $url = 'https://baik.sirkah.id/index.php/api_tpl/m_process_membership';
                    //$url = 'http://localhost/sirkah-baik/index.php/api_tpl/m_process_membership';

                    $data_member = array(
                        'cif_id' => $cif_id,
                        'cm_code' => $cm_code,
                        'nama' => $nama,
                        'panggilan' => $panggilan,
                        'kelompok' => $kelompok,
                        'pribadi_jenis_kelamin' => $pribadi_jenis_kelamin,
                        'pribadi_ibu_kandung' => $pribadi_ibu_kandung,
                        'pribadi_tmp_lahir' => $pribadi_tmp_lahir,
                        'pribadi_tgl_lahir' => $pribadi_tgl_lahir,
                        'pribadi_usia' => $pribadi_usia,
                        'pribadi_alamat' => $pribadi_alamat,
                        'rt' => $rt,
                        'rw' => $rw,
                        'pribadi_desa' => $pribadi_desa,
                        'pribadi_kecamatan' => $pribadi_kecamatan,
                        'pribadi_kabupaten' => $pribadi_kabupaten,
                        'pribadi_kodepos' => $pribadi_kodepos,
                        'pribadi_no_ktp' => $pribadi_no_ktp,
                        'pribadi_pendidikan' => $pribadi_pendidikan,
                        'pribadi_pekerjaan' => $pribadi_pekerjaan,
                        'pribadi_ket_pekerjaan' => $pribadi_ket_pekerjaan,
                        'tgl_gabung' => $tgl_gabung,
                        'pribadi_koresponden_alamat' => $pribadi_koresponden_alamat,
                        'koresponden_rt' => $koresponden_rt,
                        'koresponden_rw' => $koresponden_rw,
                        'pribadi_koresponden_desa' => $pribadi_koresponden_desa,
                        'pribadi_koresponden_kecamatan' => $pribadi_koresponden_kecamatan,
                        'pribadi_koresponden_kabupaten' => $pribadi_koresponden_kabupaten,
                        'pribadi_koresponden_kodepos' => $pribadi_koresponden_kodepos,
                        'pribadi_no_hp' => $pribadi_no_hp,
                        'status_perkawinan' => $status_perkawinan,
                        'referensi' => $referensi,
                        'setoran_lwk' => $setoran_lwk,
                        'setoran_mingguan' => $setoran_mingguan,
                        'pribadi_literasi_latin' => $pribadi_literasi_latin,
                        'pribadi_literasi_arab' => $pribadi_literasi_arab,
                        'pasangan_nama' => $pasangan_nama,
                        'pasangan_tmplahir' => $pasangan_tmplahir,
                        'pasangan_tglahir' => $pasangan_tglahir,
                        'pasangan_usia' => $pasangan_usia,
                        'pasangan_pendidikan' => $pasangan_pendidikan,
                        'pasangan_pekerjaan' => $pasangan_pekerjaan,
                        'pasangan_ketpekerjaan' => $pasangan_ketpekerjaan,
                        'pasangan_literasi_latin' => $pasangan_literasi_latin,
                        'pasangan_literasi_arab' => $pasangan_literasi_arab,
                        'pasangan_jmltanggungan' => $pasangan_jmltanggungan,
                        'pasangan_jmlkeluarga' => $pasangan_jmlkeluarga,
                        'rmhstatus' => $rmhstatus,
                        'rmhjamban' => $rmhjamban,
                        'rmhair' => $rmhair,
                        'rmhbahanbakar' => $rmhbahanbakar,
                        'lahansawah' => $lahansawah,
                        'lahankebun' => $lahankebun,
                        'ternakkerbau' => $ternakkerbau,
                        'ternakdomba' => $ternakdomba,
                        'ternakunggas' => $ternakunggas,
                        'elekhape' => $elekhape,
                        'elektv' => $elektv,
                        'elekvcd' => $elekvcd,
                        'elekkulkas' => $elekkulkas,
                        'kendsepeda' => $kendsepeda,
                        'kendmotor' => $kendmotor,
                        'kendmobil' => $kendmobil,
                        'pasangan_no_ktp' => $pasangan_no_ktp,
                        'pasangan_no_hp' => $pasangan_no_hp,
                        'elekkomputer' => $elekkomputer,
                        'elekmesincuci' => $elekmesincuci
                    );

                    $ch = curl_init($url);

                    curl_setopt($ch, CURLOPT_POST, TRUE);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_member);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

                    $response_json = curl_exec($ch);

                    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

                    curl_close($ch);

                    $ret = json_decode($response_json);

                    $datax = array(
                        'data' => $ret,
                        'message' => $httpcode
                    );

                    $status = $datax['data']->status;
                    $message = $datax['data']->message;

                    if ($status == 1) {
                        $res = [
                            'status' => TRUE,
                            'msg' => $message,
                            'data' => [$this->input->post()]
                        ];
                    } else {
                        $res = [
                            'status' => FALSE,
                            'msg' => $message,
                            'data' => NULL
                        ];
                    }
                }
            } else {
                $res = [
                    'status' => FALSE,
                    'msg' => 'Token Invalid',
                    'data' => NULL
                ];
            }
        } else {
            $res = [
                'status' => FALSE,
                'msg' => 'No Token Provided',
                'data' => NULL
            ];
        }

        $this->response($res, 200);
    }

    function resign_get()
    {
        $headers = $this->input->request_headers();
        $token = (isset($headers['Token'])) ? $headers['Token'] : FALSE;

        $cif_no = $this->input->get('cif_no');

        $now = date('Y-m-d');

        if ($token) {
            $check_token = $this->model_registration->check_token($token);

            if ($check_token['cnt'] > 0) {
                $check_expired = $this->model_registration->check_expired($now, $token);

                if ($check_expired['expired'] > 7) {
                    $res = [
                        'status' => FALSE,
                        'msg' => 'Token Expired',
                        'data' => NULL
                    ];
                } else {
                    $url = 'https://baik.sirkah.id/index.php/api_tpl/m_resign';
                    //$url = 'http://localhost/sirkah-baik/index.php/api_tpl/m_resign';

                    $data_resign = array('cif_no' => $cif_no);

                    $ch = curl_init($url);

                    curl_setopt($ch, CURLOPT_POST, TRUE);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_resign);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

                    $response_json = curl_exec($ch);

                    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

                    curl_close($ch);

                    $ret = json_decode($response_json);

                    $datax = array(
                        'data' => $ret,
                        'message' => $httpcode
                    );

                    $status = $datax['data']->status;
                    $message = $datax['data']->message;
                    $data = $datax['data']->data;

                    if ($status == 1) {
                        $res = [
                            'status' => TRUE,
                            'msg' => NULL,
                            'data' => $data
                        ];
                    } else {
                        $res = [
                            'status' => TRUE,
                            'msg' => $message,
                            'data' => NULL
                        ];
                    }
                }
            } else {
                $res = [
                    'status' => FALSE,
                    'msg' => 'Token Invalid',
                    'data' => NULL
                ];
            }
        } else {
            $res = [
                'status' => FALSE,
                'msg' => 'No Token Provided',
                'data' => NULL
            ];
        }

        $this->response($res, 200);
    }

    function process_resign_post()
    {
        $headers = $this->input->request_headers();
        $token = (isset($headers['Token'])) ? $headers['Token'] : FALSE;

        $cif_no = $this->input->post('cif_no');
        $cm_code = $this->input->post('cm_code');
        $fa_code = $this->input->post('fa_code');
        $tipe_mutasi = 1;
        $tanggal_mutasi = date('Y-m-d', strtotime(str_replace('/', '-', $this->input->post('tanggal_mutasi'))));
        $alasan = $this->input->post('alasan');
        $keterangan = $this->input->post('keterangan');
        $saldo_pembiayaan = $this->input->post('saldo_pembiayaan');
        $saldo_margin = $this->input->post('saldo_margin');
        $flag_saldo_margin = $this->input->post('flag_saldo_margin');
        $saldo_catab = $this->input->post('saldo_catab');
        $saldo_tabungan_wajib = $this->input->post('saldo_tabungan_wajib');
        $saldo_tabungan_kelompok = $this->input->post('saldo_tabungan_kelompok');
        $saldo_sukarela = $this->input->post('saldo_sukarela');
        $saldo_simpanan_wajib = $this->input->post('saldo_simpanan_wajib');
        $saldo_tabungan_berencana = $this->input->post('saldo_tabungan_berencana');
        $saldo_individu = $this->input->post('saldo_individu');
        $saldo_deposito = $this->input->post('saldo_deposito');
        $simpanan_pokok = $this->input->post('simpanan_pokok');
        $saldo_lwk = $this->input->post('saldo_lwk');
        $saldo_tabungan_majelis = $this->input->post('saldo_tabungan_majelis');
        $saldo_smk = $this->input->post('saldo_smk');
        $saldo_cadangan_resiko = $this->input->post('saldo_cadangan_resiko');
        $bonus_bagihasil = $this->input->post('bonus_bagihasil');
        $potongan_pembiayaan = $this->input->post('potongan_pembiayaan');
        $setoran_tambahan = $this->input->post('setoran_tambahan');
        $penarikan_tabungan_sukarela = $this->input->post('penarikan_tabungan_sukarela');

        $now = date('Y-m-d');

        if ($token) {
            $check_token = $this->model_registration->check_token($token);

            if ($check_token['cnt'] > 0) {
                $check_expired = $this->model_registration->check_expired($now, $token);

                if ($check_expired['expired'] > 7) {
                    $res = [
                        'status' => FALSE,
                        'msg' => 'Token Expired',
                        'data' => NULL
                    ];
                } else {
                    $url = 'https://baik.sirkah.id/index.php/api_tpl/m_process_resign';
                    //$url = 'http://localhost/sirkah-baik/index.php/api_tpl/m_process_resign';

                    $data_resign = array(
                        'cif_no' => $cif_no,
                        'cm_code' => $cm_code,
                        'fa_code' => $fa_code,
                        'tipe_mutasi' => $tipe_mutasi,
                        'tanggal_mutasi' => $tanggal_mutasi,
                        'alasan' => $alasan,
                        'keterangan' => $keterangan,
                        'saldo_pembiayaan' => $saldo_pembiayaan,
                        'saldo_margin' => $saldo_margin,
                        'flag_saldo_margin' => $flag_saldo_margin,
                        'saldo_catab' => $saldo_catab,
                        'saldo_tabungan_wajib' => $saldo_tabungan_wajib,
                        'saldo_tabungan_kelompok' => $saldo_tabungan_kelompok,
                        'saldo_sukarela' => $saldo_sukarela,
                        'saldo_simpanan_wajib' => $saldo_simpanan_wajib,
                        'saldo_tabungan_berencana' => $saldo_tabungan_berencana,
                        'saldo_individu' => $saldo_individu,
                        'saldo_deposito' => $saldo_deposito,
                        'simpanan_pokok' => $simpanan_pokok,
                        'saldo_lwk' => $saldo_lwk,
                        'saldo_tabungan_majelis' => $saldo_tabungan_majelis,
                        'saldo_smk' => $saldo_smk,
                        'saldo_cadangan_resiko' => $saldo_cadangan_resiko,
                        'bonus_bagihasil' => $bonus_bagihasil,
                        'potongan_pembiayaan' => $potongan_pembiayaan,
                        'setoran_tambahan' => $setoran_tambahan,
                        'penarikan_tabungan_sukarela' => $penarikan_tabungan_sukarela
                    );

                    $ch = curl_init($url);

                    curl_setopt($ch, CURLOPT_POST, TRUE);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_resign);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

                    $response_json = curl_exec($ch);

                    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

                    curl_close($ch);

                    $ret = json_decode($response_json);

                    $datax = array(
                        'data' => $ret,
                        'message' => $httpcode
                    );

                    $status = $datax['data']->status;
                    $message = $datax['data']->message;

                    if ($status == 1) {
                        $res = [
                            'status' => TRUE,
                            'msg' => $message,
                            'data' => [$this->input->post()]
                        ];
                    } else {
                        $res = [
                            'status' => FALSE,
                            'msg' => $message,
                            'data' => NULL
                        ];
                    }
                }
            } else {
                $res = [
                    'status' => FALSE,
                    'msg' => 'Token Invalid',
                    'data' => NULL
                ];
            }
        } else {
            $res = [
                'status' => FALSE,
                'msg' => 'No Token Provided',
                'data' => NULL
            ];
        }

        $this->response($res, 200);
    }
}
