<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH.'libraries/JWT.php';
use \Firebase\JWT\JWT;

Class Api extends MST_Controller {
    function __construct(){
        parent::__construct();
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, DELETE, PUT, OPTIONS");
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
        $this->load->model('model_api');
        date_default_timezone_set('Asia/Jakarta');
    }

    function index(){
        echo 'Halo<br />';
    }

    function m_check_username(){
        $cif_no = $this->input->post('cif_no');

        // Cek ID Anggota ke tabel bm_anggota
        $check_cif_no = $this->model_api->check_cif_no($cif_no);

        $count = count($check_cif_no);

        $payload = array('cif_no' => $cif_no);

        $DBCore = $this->coreDB();

        if($count > 0){
            // Berhasil
            $token = JWT::encode($payload,$this->key());

            $this->session->set_userdata('token',$token);

            $result = array(
                'status' => '1',
                'cif_no' => $cif_no,
                'message' => 'Silakkan masukkan password',
                'token' => $token
            );
        } else {
            // ID Anggota tidak ditemukan, lanjutkan pencarian ke Database SIRKAH
            $url = 'https://app.baytulikhtiar.com/index.php/api/m_check_username';

            $data_username = array('cif_no' => $cif_no);

            $ch = curl_init($url);

            curl_setopt($ch,CURLOPT_POST,TRUE);
            curl_setopt($ch,CURLOPT_POSTFIELDS,$data_username);
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);

            $response_json = curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            curl_close($ch);

            $ret = json_decode($response_json);

            $datax = array(
                'data' => $ret,
                'message' => $httpcode
            );

            $status = $datax['data']->status;
            $username = $datax['data']->cif_no;
            $message = $datax['data']->message;

            if($status == '1'){
                // INSERT ke Tabel bm_anggota dan dialihkan ke halaman Buat Password Baru
                $insert = array('cif_no' => $username);

                // DATABASE TRANSACTION
                $DBCore->trans_begin();
                $this->insert('bm_anggota',$insert);

                if($DBCore->trans_status() === TRUE){
                    $DBCore->trans_commit();

                    $token = JWT::encode($payload,$this->key());

                    $this->session->set_userdata('token',$token);

                    $result = array(
                        'status' => '2',
                        'cif_no' => $username,
                        'message' => 'Silakkan buat password baru',
                        'token' => $token
                    );
                } else {
                    $DBCore->trans_rollback();

                    $result = array(
                        'status' => '4',
                        'cif_no' => $username,
                        'message' => 'Maaf! Buat password gagal. Silakkan coba lagi',
                        'token' => NULL
                    );
                }
            } else {
                $result = array(
                    'status' => '3',
                    'cif_no' => $username,
                    'message' => $message,
                    'token' => NULL
                );
            }
        }

        echo json_encode($result);
    }

    function m_check_password(){
        $cif_no = $this->input->post('cif_no');
        $password = $this->input->post('password');
        $status = $this->input->post('status');
        $token = $this->input->post('token');

        $password = sha1(md5(sha1('2021'.strtolower($password).'apps')));

        // Ambil informasi nama dan cif_type dari Database SIRKAH
        $url = 'https://app.baytulikhtiar.com/index.php/api/m_get_cif';

        $DBCore = $this->coreDB();

        if($status == '1'){
            // Cek Password ke tabel bm_anggota
            $check_membership = $this->model_api->check_membership($cif_no,$password);

            $data_cif = array('cif_no' => $cif_no);

            $ch = curl_init($url);

            curl_setopt($ch,CURLOPT_POST,TRUE);
            curl_setopt($ch,CURLOPT_POSTFIELDS,$data_cif);
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);

            $response_json = curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            curl_close($ch);

            $ret = json_decode($response_json);

            $datax = array(
                'data' => $ret,
                'message' => $httpcode
            );

            $nama = $datax['data']->nama;
            $cif_type = $datax['data']->cif_type;

            if($password == $check_membership['password']){
                //if($token == $this->session->userdata('token')){
                    $result = array(
                        'status' => '1',
                        'cif_no' => $check_membership['cif_no'],
                        'nama' => $nama,
                        'cif_type' => $cif_type,
                        'saldo' => 'Rp. '.number_format($check_membership['saldo'],0,',','.'),
                        'message' => 'Berhasil! Selamat Datang di Aplikasi Anggota KSSPS BAIK'
                    );
                /*
                } else {
                    $result = array(
                        'status' => '4',
                        'cif_no' => '',
                        'nama' => '',
                        'cif_type' => '',
                        'saldo' => 'Rp. 0',
                        'message' => 'Maaf! Silakkan login kembali'
                    );
                }
                */
            } else {
                $result = array(
                    'status' => '4',
                    'cif_no' => NULL,
                    'nama' => NULL,
                    'cif_type' => NULL,
                    'saldo' => 'Rp. 0',
                    'message' => 'Maaf! Password Anda salah'
                );
            }
        } else if($status == '2'){
            $update = array('password' => $password);
            $param = array('cif_no' => $cif_no);

            $data_cif = array('cif_no' => $cif_no);

            $ch = curl_init($url);

            curl_setopt($ch,CURLOPT_POST,TRUE);
            curl_setopt($ch,CURLOPT_POSTFIELDS,$data_cif);
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);

            $response_json = curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            curl_close($ch);

            $ret = json_decode($response_json);

            $datax = array(
                'data' => $ret,
                'message' => $httpcode
            );

            $nama = $datax['data']->nama;
            $cif_type = $datax['data']->cif_type;

            // DATABASE TRANSACTION
            $DBCore->trans_begin();
            $this->update2('bm_anggota',$update,$param);

            if($DBCore->trans_status() === TRUE){
                $DBCore->trans_commit();

                //if($token == $this->session->userdata('token')){
                    $result = array(
                        'status' => '2',
                        'cif_no' => $cif_no,
                        'nama' => $nama,
                        'cif_type' => $cif_type,
                        'saldo' => 'Rp. 0',
                        'message' => 'Berhasil! Selamat Datang di Aplikasi Anggota KSSPS BAIK'
                    );
                /*
                } else {
                    $result = array(
                        'status' => '4',
                        'cif_no' => '',
                        'nama' => '',
                        'cif_type' => '',
                        'saldo' => 'Rp. 0',
                        'message' => 'Maaf! Silakkan login kembali'
                    );
                }
                */
            } else {
                $DBCore->trans_rollback();

                $result = array(
                    'status' => '3',
                    'cif_no' => $cif_no,
                    'nama' => $nama,
                    'cif_type' => $cif_type,
                    'saldo' => 'Rp. 0',
                    'message' => 'Maaf! Buat password gagal. Silakkan coba lagi'
                );
            }
        }

        echo json_encode($result);
    }

    function m_saldo_membership(){
        $cif_no = $this->input->post('cif_no');
        $token = $this->input->post('token');

        // Ambil informasi saldo keanggotaan dari Database SIRKAH
        $url = 'https://app.baytulikhtiar.com/index.php/api/m_get_saldo';

        $data_cif = array('cif_no' => $cif_no);

        $ch = curl_init($url);

        curl_setopt($ch,CURLOPT_POST,TRUE);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$data_cif);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);

        $response_json = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        $ret = json_decode($response_json);

        $datax = array(
            'data' => $ret,
            'message' => $httpcode
        );

        $simpok = $datax['data']->simpok;
        $simwa = $datax['data']->simwa;
        $sukarela = $datax['data']->sukarela;

        //if($token == $this->session->userdata('token')){
            $result = array(
                'simpok' => 'Rp. '.number_format($simpok,0,',','.'),
                'simwa' => 'Rp. '.number_format($simwa,0,',','.'),
                'sukarela' => 'Rp. '.number_format($sukarela,0,',','.'),
                'status' => '1',
                'message' => NULL
            );
        /*
        } else {
            $result = array(
                'lwk' => 'Rp. 0',
                'simpok' => 'Rp. 0',
                'sukarela' => 'Rp. 0',
                'status' => '2',
                'message' => 'Maaf! Silakkan login kembali'
            );
        }
        */

        echo json_encode($result);
    }

    function m_saldo_saving(){
        $cif_no = $this->input->post('cif_no');
        $token = $this->input->post('token');

        // Ambil informasi saldo tabungan dari Database SIRKAH
        $url = 'https://app.baytulikhtiar.com/index.php/api/m_get_saving';

        $data_cif = array('cif_no' => $cif_no);

        $ch = curl_init($url);

        curl_setopt($ch,CURLOPT_POST,TRUE);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$data_cif);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);

        $response_json = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        $ret = json_decode($response_json);

        $datax = array(
            'data' => $ret,
            'message' => $httpcode
        );

        $count = count($datax['data']);

        $result = array();

        //if($token == $this->session->userdata('token')){
            if($count > 0){
                for($i = 0; $i < $count; $i++){
                    $product_name = $datax['data'][$i]->product_name;
                    $account_saving_no = $datax['data'][$i]->account_saving_no;
                    $saldo = $datax['data'][$i]->saldo;
                    $cif_type = $datax['data'][$i]->cif_type;

                    $result[] = array(
                        'product_name' => $product_name,
                        'account_saving_no' => $account_saving_no,
                        'saldo' => 'Rp. '.number_format($saldo,0,',','.'),
                        'cif_type' => $cif_type,
                        'status' => '1',
                        'message' => NULL
                    );
                }
            } else {
                $result[] = array(
                    'product_name' => 'TIDAK ADA DATA',
                    'account_saving_no' => 'TIDAK ADA DATA',
                    'saldo' => 'TIDAK ADA DATA',
                    'cif_type' => 'TIDAK ADA DATA',
                    'status' => 'TIDAK ADA DATA',
                    'message' => 'TIDAK ADA DATA'
                );
            }
        /*
        } else {
            $result[] = array(
                'product_name' => '',
                'account_saving_no' => '',
                'saldo' => 'Rp. 0',
                'cif_type' => '',
                'status' => '2',
                'message' => 'Maaf! Silakkan login kembali'
            );
        }
        */

        echo json_encode($result);
    }

    function m_statement_tabungan(){
        $product_name = $this->input->post('product_name');
        $account_saving_no = $this->input->post('account_saving_no');
        $cif_type = $this->input->post('cif_type');
        $from_date = $this->input->post('from_date');
        $thru_date = $this->input->post('thru_date');
        $token = $this->input->post('token');

        $from_date = str_replace('/','-',$from_date);
        $thru_date = str_replace('/','-',$thru_date);

        $from_date = date('Y-m-d',strtotime($from_date));
        $thru_date = date('Y-m-d',strtotime($thru_date));

        // Ambil informasi statement tabungan dari Database SIRKAH
        $url = 'https://app.baytulikhtiar.com/index.php/api/m_statement_saving';

        $data_statement = array(
            'product_name' => $product_name,
            'account_saving_no' => $account_saving_no,
            'cif_type' => $cif_type,
            'from_date' => $from_date,
            'thru_date' => $thru_date
        );

        $ch = curl_init($url);

        curl_setopt($ch,CURLOPT_POST,TRUE);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$data_statement);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);

        $response_json = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        $ret = json_decode($response_json);

        $datax = array(
            'data' => $ret,
            'message' => $httpcode
        );

        $count = count($datax['data']);

        $result = array();

        //if($token == $this->session->userdata('token')){
            if($count > 0){
                for($i = 0; $i < $count; $i++){
                    $transaction_date = $datax['data'][$i]->transaction_date;
                    $amount = $datax['data'][$i]->amount;
                    $description = $datax['data'][$i]->description;
                    $saldo = $datax['data'][$i]->saldo;

                    $result[] = array(
                        'transaction_date' => $transaction_date,
                        'amount' => 'Rp. '.number_format($amount,0,',','.'),
                        'description' => $description,
                        'saldo' => 'Rp. '.number_format($saldo,0,',','.'),
                        'status' => '1',
                        'message' => NULL
                    );
                }
            } else {
                $result[] = array(
                    'transaction_date' => 'TIDAK ADA DATA',
                    'amount' => 'TIDAK ADA DATA',
                    'description' => 'TIDAK ADA DATA',
                    'saldo' => 'TIDAK ADA DATA',
                    'status' => 'TIDAK ADA DATA',
                    'message' => 'TIDAK ADA DATA'
                );
            }
        /*
        } else {
            $result[] = array(
                'product_name' => '',
                'account_saving_no' => '',
                'saldo' => 'Rp. 0',
                'cif_type' => '',
                'status' => '2',
                'message' => 'Maaf! Silakkan login kembali'
            );
        }
        */

        echo json_encode($result);
    }

    function m_saldo_financing(){
        $cif_no = $this->input->post('cif_no');
        $token = $this->input->post('token');

        // Ambil informasi statement tabungan dari Database SIRKAH
        $url = 'https://app.baytulikhtiar.com/index.php/api/m_saldo_financing';

        $data_financing = array('cif_no' => $cif_no);

        $ch = curl_init($url);

        curl_setopt($ch,CURLOPT_POST,TRUE);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$data_financing);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);

        $response_json = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        $ret = json_decode($response_json);

        $datax = array(
            'data' => $ret,
            'message' => $httpcode
        );

        $count = count($datax['data']);

        $result = array();

        if($count > 0){
            for($i = 0; $i < $count; $i++){
                $pembiayaan_ke = $datax['data'][$i]->pembiayaan_ke;
                $pokok = $datax['data'][$i]->pokok;
                $margin = $datax['data'][$i]->margin;
                $saldo_pokok = $datax['data'][$i]->saldo_pokok;
                $saldo_margin = $datax['data'][$i]->saldo_margin;
                $saldo_catab = $datax['data'][$i]->saldo_catab;
                $jangka_waktu = $datax['data'][$i]->jangka_waktu;
                $status_rekening = $datax['data'][$i]->status;
                $account_financing_no = $datax['data'][$i]->account_financing_no;

                $result[] = array(
                    'pembiayaan_ke' => $pembiayaan_ke,
                    'pokok' => 'Rp. '.number_format($pokok,0,',','.'),
                    'margin' => 'Rp. '.number_format($margin,0,',','.'),
                    'saldo_pokok' => 'Rp. '.number_format($saldo_pokok,0,',','.'),
                    'saldo_margin' => 'Rp. '.number_format($saldo_margin,0,',','.'),
                    'saldo_catab' => 'Rp. '.number_format($saldo_catab,0,',','.'),
                    'jangka_waktu' => $jangka_waktu,
                    'status_rekening' => $status_rekening,
                    'account_financing_no' => $account_financing_no,
                    'status' => '1',
                    'message' => NULL
                );
            }
        } else {
            $result[] = array(
                'pembiayaan_ke' => 'TIDAK ADA DATA',
                'pokok' => 'TIDAK ADA DATA',
                'margin' => 'TIDAK ADA DATA',
                'saldo_pokok' => 'TIDAK ADA DATA',
                'saldo_margin' => 'TIDAK ADA DATA',
                'saldo_catab' => 'TIDAK ADA DATA',
                'jangka_waktu' => 'TIDAK ADA DATA',
                'status_rekening' => 'TIDAK ADA DATA',
                'account_financing_no' => 'TIDAK ADA DATA',
                'status' => 'TIDAK ADA DATA',
                'message' => 'TIDAK ADA DATA'
            );
        }

        echo json_encode($result);
    }

    function m_get_inquiry(){
        $cif_no = $this->input->post('cif_no');
        $amount = $this->input->post('amount');
        $token = $this->input->post('token');

        // Ambil informasi keanggotaan dari Database SIRKAH
        $url = 'https://app.baytulikhtiar.com/index.php/api/m_get_inquiry';

        $data_financing = array('cif_no' => $cif_no);

        $ch = curl_init($url);

        curl_setopt($ch,CURLOPT_POST,TRUE);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$data_financing);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);

        $response_json = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        $ret = json_decode($response_json);

        $datax = array(
            'data' => $ret,
            'message' => $httpcode
        );

        $branch_name = $datax['data']->branch_name;
        $cm_name = $datax['data']->cm_name;
        $nama = $datax['data']->nama;
        $status = $datax['data']->status;

        if($status == '1'){
            $result = array(
                'branch_name' => $branch_name,
                'cm_name' => $cm_name,
                'cif_no' => $cif_no,
                'name' => $nama,
                'amount' => 'Rp. '.number_format($amount,0,',','.'),
                'message' => NULL
            );
        } else if($status == '2') {
            $result = array(
                'branch_name' => NULL,
                'cm_name' => NULL,
                'cif_no' => NULL,
                'name' => NULL,
                'amount' => NULL,
                'message' => 'Maaf! Rekening Tujuan Anda sudah tidak aktif'
            );
        } else {
            $result = array(
                'branch_name' => NULL,
                'cm_name' => NULL,
                'cif_no' => NULL,
                'name' => NULL,
                'amount' => NULL,
                'message' => 'Maaf! Rekening Tujuan Anda tidak tersedia'
            );
        }

        echo json_encode($result);
    }

    function m_transfer(){
        $sender = $this->input->post('sender');
        $sender_name = $this->input->post('sender_name');
        $receiver = $this->input->post('receiver');
        $receiver_name = $this->input->post('receiver_name');
        $amount = $this->input->post('amount');
        $password = $this->input->post('password');
        $token = $this->input->post('token');

        $DBCore = $this->coreDB();

        $trx_date = date('Y-m-d');
        $created_date = date('Y-m-d H:i:s');

        $password = sha1(md5(sha1('2021'.strtolower($password).'apps')));

        // Cek Password ke tabel bm_anggota
        $check_membership = $this->model_api->check_membership($sender,$password);

        if($password == $check_membership['password']){
            if($check_membership['saldo'] > $amount){
                // Get Saldo Awal Pengirim dan Penerima
                $get_saldo_sender = $this->model_api->check_cif_no($sender);
                $get_saldo_receiver = $this->model_api->check_cif_no($receiver);

                $count_receiver = count($get_saldo_receiver);

                if($count_receiver > 0){
                    // Statement Pengirim
                    $send = array(
                        'trx_date' => $trx_date,
                        'trx_type' => '11',
                        'cif_no' => $sender,
                        'amount' => $amount,
                        'flag_dc' => 'D',
                        'description' => 'TRANSFER KE '.$receiver.' ('.$receiver_name.')',
                        'saldo_awal' => $get_saldo_sender['saldo'],
                        'created_date' => $created_date
                    );

                    // UPDATE saldo pengirim
                    $update_sender = array('saldo' => $get_saldo_sender['saldo'] - $amount);
                    $param_sender = array('cif_no' => $sender);

                    // Stetement Penerima
                    $receive = array(
                        'trx_date' => $trx_date,
                        'trx_type' => '11',
                        'cif_no' => $receiver,
                        'amount' => $amount,
                        'flag_dc' => 'C',
                        'description' => 'TERIMA TRANSFER DARI '.$sender.' ('.$sender_name.')',
                        'saldo_awal' => $get_saldo_receiver['saldo'],
                        'created_date' => $created_date
                    );

                    // UPDATE saldo penerima
                    $update_receiver = array('saldo' => $get_saldo_receiver['saldo'] + $amount);
                    $param_receiver = array('cif_no' => $receiver);

                    // DATABASE TRANSACTION
                    $DBCore->trans_begin();
                    $this->insert('bm_transaksi',$send);
                    $this->insert('bm_transaksi',$receive);
                    $this->update2('bm_anggota',$update_sender,$param_sender);
                    $this->update2('bm_anggota',$update_receiver,$param_receiver);

                    if($DBCore->trans_status() === TRUE){
                        $DBCore->trans_commit();
                        $result = array(
                            'status' => '1',
                            'message' => 'Transfer Sukses!'
                        );
                    } else {
                        $DBCore->trans_rollback();
                        $result = array(
                            'status' => '2',
                            'message' => 'Transfer Gagal! Silakkan cek Saldo Dompet BAIK Anda.'
                        );
                    }
                } else {
                    $result = array(
                        'status' => '2',
                        'message' => 'Transfer Gagal! No. Rekening Tujuan tidak ditemukan.'
                    );
                }
            } else {
                $result = array(
                    'status' => '2',
                    'message' => 'Transfer Gagal! Saldo Dompet BAIK Anda tidak mencukupi.'
                );
            }
        } else {
            $result = array(
                'status' => '2',
                'message' => 'Maaf! Password Anda salah.'
            );
        }

        echo json_encode($result);
    }

    function m_view_profile(){
        $cif_no = $this->input->post('cif_no');
        $token = $this->input->post('token');

        // Ambil informasi keanggotaan dari Database SIRKAH
        $url = 'https://app.baytulikhtiar.com/index.php/api/m_get_inquiry';

        $data_financing = array('cif_no' => $cif_no);

        $ch = curl_init($url);

        curl_setopt($ch,CURLOPT_POST,TRUE);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$data_financing);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);

        $response_json = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        $ret = json_decode($response_json);

        $datax = array(
            'data' => $ret,
            'message' => $httpcode
        );

        $branch_name = $datax['data']->branch_name;
        $cm_name = $datax['data']->cm_name;
        $nama = $datax['data']->nama;
        $status = $datax['data']->status;

        // GET SALDO DOMPET BAIK
        $saldo_dompet = $this->model_api->check_cif_no($cif_no);

        if($status == '1'){
            $result = array(
                'branch_name' => $branch_name,
                'cm_name' => $cm_name,
                'cif_no' => $cif_no,
                'name' => $nama,
                'saldo' => 'Rp. '.number_format($saldo_dompet['saldo']),
                'message' => NULL
            );
        } else if($status == '2') {
            $result = array(
                'branch_name' => NULL,
                'cm_name' => NULL,
                'cif_no' => NULL,
                'name' => NULL,
                'saldo' => NULL,
                'message' => 'Maaf! Rekening Tujuan Anda sudah tidak aktif'
            );
        } else {
            $result = array(
                'branch_name' => NULL,
                'cm_name' => NULL,
                'cif_no' => NULL,
                'name' => NULL,
                'saldo' => NULL,
                'message' => 'Maaf! Rekening Tujuan Anda tidak tersedia'
            );
        }

        echo json_encode($result);
    }

    function m_update_password(){
        $cif_no = $this->input->post('cif_no');
        $pass = $this->input->post('password');
        $token = $this->input->post('token');

        $password = sha1(md5(sha1('2021'.strtolower($pass).'apps')));

        $data = array('password' => $password);

        $param = array('cif_no' => $cif_no);

        // BEGIN TRANSACTION
        $this->db->trans_begin();
        $this->model_api->update_password('bm_anggota',$data,$param);

        if($this->db->trans_status() === TRUE){
            $this->db->trans_commit();
            $result = array(
                'status' => '1',
                'status_message' => 'Selamat! Password berhasil diubah'
            );
        } else {
            $this->db->trans_rollback();
            $result = array(
                'status' => '2',
                'status_message' => 'Maaf! Password gagal diubah. Silakkan dicoba kembali'
            );
        }

        echo json_encode($result);
    }

    function m_statement_wallet(){
        $cif_no = $this->input->post('cif_no');
        $from = $this->input->post('from');
        $thru = $this->input->post('thru');
        $token = $this->input->post('token');

        $from = date('Y-m-d',strtotime(str_replace('/','-',$from)));
        $thru = date('Y-m-d',strtotime(str_replace('/','-',$thru)));

        $statement = $this->model_api->get_statement_wallet($cif_no,$from,$thru);

        $count = count($statement);

        $result = array();

        if($count > 0){
            foreach($statement as $stt){
                if($stt['amount_credit'] == 0){
                    $jumlah = $stt['amount_debet'];
                    $saldo_akhir = $stt['saldo_awal'] - $jumlah;
                }

                if($stt['amount_debet'] == 0){
                    $jumlah = $stt['amount_credit'];
                    $saldo_akhir = $stt['saldo_awal'] + $jumlah;
                }

                $result[] = array(
                    'saldo_awal' => 'Rp. '.number_format($stt['saldo_awal'],0,',','.'),
                    'trx_date' => date('d/m/Y',strtotime(str_replace('-','/',$stt['trx_date']))),
                    'amount' => 'Rp. '.number_format($jumlah,0,',','.'),
                    'description' => $stt['description'],
                    'saldo_akhir' => 'Rp. '.number_format($saldo_akhir,0,',','.')
                );
            }
        } else {
            $result[] = array(
                'saldo_awal' => 'TIDAK ADA DATA',
                'trx_date' => 'TIDAK ADA DATA',
                'amount' => 'TIDAK ADA DATA',
                'description' => 'TIDAK ADA DATA',
                'saldo_akhir' => 'TIDAK ADA DATA'
            );
        }

        echo json_encode($result);
    }

    function m_statement_installment(){
        $account_financing_no = $this->input->post('account_financing_no');
        $token = $this->input->post('token');

        // Ambil informasi kartu angsuran dari Database SIRKAH
        $url = 'https://app.baytulikhtiar.com/index.php/api/m_statement_installment';

        $data_installment = array('account_financing_no' => $account_financing_no);

        $ch = curl_init($url);

        curl_setopt($ch,CURLOPT_POST,TRUE);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$data_installment);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);

        $response_json = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        $ret = json_decode($response_json);

        $datax = array(
            'data' => $ret,
            'message' => $httpcode
        );

        $count = count($datax['data']);

        $result = array();

        if($count > 0){
            for($i = 0; $i < $count; $i++){
                $angsuran_ke = $datax['data'][$i]->angsuran_ke;
                $account_financing_no = $datax['data'][$i]->account_financing_no;
                $saldo_pokok = $datax['data'][$i]->saldo_pokok;
                $saldo_margin = $datax['data'][$i]->saldo_margin;
                $saldo_catab = $datax['data'][$i]->saldo_catab;
                $angsuran = $datax['data'][$i]->angsuran;
                $tanggal_bayar = $datax['data'][$i]->tanggal_bayar;

                if($tanggal_bayar != ''){
                    $tgl_bayar = str_replace('-','/',$tanggal_bayar);
                } else {
                    $tgl_bayar = '';
                }

                $result[] = array(
                    'angsuran_ke' => $angsuran_ke,
                    'account_financing_no' => $account_financing_no,
                    'saldo_pokok' => 'Rp. '.number_format($saldo_pokok,0,',','.'),
                    'saldo_margin' => 'Rp. '.number_format($saldo_margin,0,',','.'),
                    'saldo_catab' => 'Rp. '.number_format($saldo_catab,0,',','.'),
                    'angsuran' => 'Rp. '.number_format($angsuran,0,',','.'),
                    'tanggal_bayar' => $tgl_bayar
                );
            }
        } else {
            $result[] = array(
                'angsuran_ke' => 'TIDAK ADA DATA',
                'account_financing_no' => 'TIDAK ADA DATA',
                'saldo_pokok' => 'TIDAK ADA DATA',
                'saldo_margin' => 'TIDAK ADA DATA',
                'saldo_catab' => 'TIDAK ADA DATA',
                'angsuran' => 'TIDAK ADA DATA',
                'tanggal_bayar' => 'TIDAK ADA DATA'
            );
        }

        echo json_encode($result);
    }
}