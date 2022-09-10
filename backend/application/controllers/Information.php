<?php
defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;
use Firebase\JWT\JWT;

class Information extends RestController
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('model_information');
        date_default_timezone_set('Asia/Jakarta');
    }

    function info_get()
    {
        $headers = $this->input->request_headers();
        $token = (isset($headers['Token'])) ? $headers['Token'] : false;

        $nik = $this->input->get('nik');

        if ($token) {
            $check_token = $this->model_information->check_token($token);

            if ($check_token['cnt'] > 0) {
                $get = $this->model_information->get_info($nik);

                $data = array();

                foreach ($get as $gt) {
                    if ($gt['gambar'] == NULL) {
                        $foto_info = 'default_info.jpg';
                    } else {
                        $foto_info = $gt['gambar'];
                    }

                    $path = 'https://simpres.baytulikhtiar.com/assets/foto_info/';

                    $path_foto = $path . $foto_info;
                    $type_foto = pathinfo($path_foto, PATHINFO_EXTENSION);
                    $data_foto = @file_get_contents($path_foto);
                    $base64_foto = 'data:image/' . $type_foto . ';base64,' . base64_encode($data_foto);

                    $data[] = array(
                        'id' => $gt['id'],
                        'kategori' => $gt['kategori'],
                        'judul' => $gt['judul'],
                        'gambar' => $base64_foto,
                        'pesan' => $gt['pesan'],
                        'created_date' => $gt['created_date']
                    );
                }

                $res = [
                    'status' => true,
                    'msg' => $data
                ];
            } else {
                $res = [
                    'status' => false,
                    'msg' => 'Token Invalid'
                ];
            }
        } else {
            $res = [
                'status' => false,
                'msg' => 'No Token Provided'
            ];
        }

        $this->response($res, 200);
    }

    function info_detail_get()
    {
        $headers = $this->input->request_headers();
        $token = (isset($headers['Token'])) ? $headers['Token'] : false;

        $id_info = $this->input->get('id_info');

        if ($token) {
            $check_token = $this->model_information->check_token($token);

            if ($check_token['cnt'] > 0) {
                $get = $this->model_information->get_info_detail($id_info);

                if ($get['gambar'] == NULL) {
                    $foto_info = 'default_info.jpg';
                } else {
                    $foto_info = $get['gambar'];
                }

                $path = 'https://simpres.baytulikhtiar.com/assets/foto_info/';

                $path_foto = $path . $foto_info;
                $type_foto = pathinfo($path_foto, PATHINFO_EXTENSION);
                $data_foto = @file_get_contents($path_foto);
                $base64_foto = 'data:image/' . $type_foto . ';base64,' . base64_encode($data_foto);

                $data = array(
                    'id' => $get['id'],
                    'kategori' => $get['kategori'],
                    'judul' => $get['judul'],
                    'gambar' => $base64_foto,
                    'pesan' => $get['pesan'],
                    'created_date' => $get['created_date']
                );

                $res = [
                    'status' => true,
                    'msg' => $data
                ];
            } else {
                $res = [
                    'status' => false,
                    'msg' => 'Token Invalid'
                ];
            }
        } else {
            $res = [
                'status' => false,
                'msg' => 'No Token Provided'
            ];
        }

        $this->response($res, 200);
    }

    function forgot_post()
    {
        $nik = $this->input->post('nik');

        $config = array();

        $check = $this->model_information->check_email($nik);

        if (isset($check['nik'])) {
            if ($check['email'] == null) {
                $new_email = 'saleh.ibrahim91@gmail.com';
                $bcc = 'yudiripayansah@gmail.com';
            } else {
                $new_email = $check['email'];
                $bcc = 'saleh.ibrahim91@gmail.com';
            }

            $salt = 'Semoga Allah melindungi sistem ini dari serangan orang-orang yang tidak bertanggung jawab. Aamiin Allahumma Aamiin';

            $new_password = substr(md5(sha1(md5(sha1(date('Y-m-d H:i:s') . $salt)))), 0, 6);

            $config['mailtype'] = 'html';
            $config['protocol'] = 'smtp';
            $config['smtp_host'] = 'ssl://smtp.gmail.com';
            $config['smtp_user'] = 'infobaytulikhtiar@gmail.com';
            $config['smtp_pass'] = 'admin123baik';
            $config['smtp_port'] = '465';
            $config['newline'] = "\r\n";

            $pesan = "<p>Assalamu'alaikum Wr.Wb,</p>";
            $pesan .= '<p>Password Anda untuk akses App Karyawan telah kami ganti.<br />';
            $pesan .= 'Anda bisa login menggunakan :<br />';
            $pesan .= 'ID Anggota : ' . $check['username'] . '<br />';
            $pesan .= 'Password : ' . $new_password . '</ p>';
            $pesan .= '<p>Kami sangat sarankan agar Anda langsung mengubah Password setelah login berhasil. Terima kasih</p>';
            $pesan .= "<p>Wassalamu'alaikum Wr.Wb</p>";

            $this->email->initialize($config);
            $this->email->from('info@bmbaytulikhtiar.com', 'Info KSPPS Baytul Ikhtiar');
            $this->email->to($new_email);
            $this->email->bcc($bcc);
            $this->email->subject('Informasi Perubahan Password');
            $this->email->message($pesan);

            if ($this->email->send()) {
                $data = array('password_temp' => md5($new_password));
                $param = array('username' => $check['username']);

                $this->model_information->update_password('app_user', $data, $param);

                $res = [
                    'status' => true,
                    'msg' => 'Password berhasil diubah. Silakkan cek email Anda atau hubungi IT Support'
                ];
            } else {
                $res = [
                    'status' => false,
                    'msg' => 'Password Anda gagal dikirim via Email. Coba beberapa saat lagi',
                    'sendEmail' => $this->email->print_debugger()
                ];
            }
        } else {
            $res = [
                'status' => false,
                'msg' => 'Maaf! NIK Anda tidak ditemukan.'
            ];
        }

        $this->response($res, 200);
    }
}
