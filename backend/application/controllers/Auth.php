<?php
defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;
use Firebase\JWT\JWT;

class Auth extends RestController
{
    function __construct()
    {
        parent::__construct();

        $this->load->model('model_auth');

        date_default_timezone_set('Asia/Jakarta');
    }

    function generate_token($id_user, $password, $tipe_user, $last_login)
    {
        $key = getenv('JWT_SECRET');

        $payload = array(
            'id_user' => $id_user,
            'tipe_user' => $tipe_user,
            'password' => $password,
            'last_login' => $last_login
        );

        $jwt = JWT::encode($payload, $key);

        $decoded = JWT::decode($jwt, $key, array('HS256'));

        return [$jwt, $decoded];
    }

    function index()
    {
        echo 'Test API Sukses';
    }

    function forgot_password_post()
    {
        $username = $this->input->post('username');

        $berkah = 'Semoga Allah melindungi sistem ini dari serangan orang-orang yang tidak bertanggung jawab. Aamiin Allahumma Aamiin';

        $new_password = substr(sha1(date('Y-m-d H:i:s')), 0, 6);

        // Cek Username ke tabel kis_user
        $check = $this->model_auth->check_username($username);

        $count = count($check);

        if ($count > 0) {
            $data = array(
                'password' => sha1($berkah . $new_password),
                'password_temp' => $new_password
            );

            $param = array('id_user' => $username);

            $update = $this->model_auth->update('kis_user', $data, $param);

            if ($update === TRUE) {
                $res = [
                    'status' => TRUE,
                    'msg' => 'Reset Password Berhasil! Silakkan hubungi Petugas Anda',
                    'data' => [$this->input->post()]
                ];
            } else {
                $res = [
                    'status' => FALSE,
                    'msg' => 'Maaf! Reset Password belum berhasil. Silakkan ulangi beberapa saat lagi',
                    'data' => $this->input->post()
                ];
            }
        } else {
            $res = [
                'status' => FALSE,
                'msg' => 'Maaf! Akun Anda tidak ditemukan',
                'data' => $this->input->post()
            ];
        }

        $this->response($res, 200);
    }

    function check_username_post()
    {
        $id_user = $this->input->post('id_user');

        $check = $this->model_auth->check_username($id_user);

        $count = count($check);

        if ($count > 0) {
            $userAccount = array(
                'id_user' => $check['id_user'],
                'tipe_user' => $check['tipe_user']
            );

            $res = [
                'status' => TRUE,
                'msg' => 'Silakkan masukkan Password Anda',
                'data' => $userAccount,
                'token' => NULL
            ];
        } else {
            $check = $this->model_auth->check_member($id_user);

            $count = count($check);

            if ($count > 0) {
                if ($check['status'] <> 3) {
                    $data = array(
                        'id_user' => $check['noanggota'],
                        'tipe_user' => 1
                    );

                    $insert = $this->model_auth->insert('kis_user', $data);

                    if ($insert === TRUE) {
                        $userAccount = array(
                            'id_user' => $check['noanggota'],
                            'tipe_user' => 1
                        );

                        $res = [
                            'status' => TRUE,
                            'msg' => 'Silakkan masukkan Password Anda',
                            'data' => $userAccount,
                            'token' => NULL
                        ];
                    } else {
                        $res = [
                            'status' => FALSE,
                            'msg' => 'Maaf! Login tidak berhasil',
                            'data' => $this->input->post(),
                            'token' => NULL
                        ];
                    }
                } else {
                    $res = [
                        'status' => FALSE,
                        'msg' => 'Maaf! Login tidak berhasil. Anda sudah dikeluarkan dari Keanggotaan',
                        'data' => $this->input->post(),
                        'token' => NULL
                    ];
                }
            } else {
                $res = [
                    'status' => FALSE,
                    'msg' => 'Maaf! Akun Anda tidak ditemukan',
                    'data' => $this->input->post(),
                    'token' => NULL
                ];
            }
        }

        $this->response($res, 200);
    }

    function check_password_post()
    {
        $id_user = $this->input->post('id_user');
        $tipe_user = $this->input->post('tipe_user');
        $word = $this->input->post('password');

        $last_login = date('Y-m-d H:i:s');

        $berkah = 'Semoga Allah melindungi sistem ini dari serangan orang-orang yang tidak bertanggung jawab. Aamiin Allahumma Aamiin';

        $password = sha1($berkah . $word);

        $check = $this->model_auth->check_username($id_user);

        $token = $this->generate_token($id_user, $password, $tipe_user, $last_login);

        if ($check['password'] == null) { // KHUSUS ANGGOTA PERTAMA KALI LOGIN
            $data = array(
                'password' => $password,
                'last_login' => $last_login,
                'token' => $token[0]
            );

            $param = array('id_user' => $id_user);

            $update = $this->model_auth->update('kis_user', $data, $param);

            if ($update === TRUE) {
                $userAccount = array(
                    'id_user' => $id_user,
                    'tipe_user' => $tipe_user,
                    'last_login' => $last_login
                );

                $res = [
                    'status' => TRUE,
                    'msg' => 'Login Berhasil! Anda akan dialihkan ke halaman Dashboard',
                    'data' => $userAccount,
                    'token' => $token[0]
                ];
            } else {
                $res = [
                    'status' => FALSE,
                    'msg' => 'Maaf! Login tidak berhasil',
                    'data' => ['input' => $this->input->post()],
                    'token' => NULL
                ];
            }
        } else {
            if ($check['password_temp'] == NULL) { // BUKAN ANGGOTA YANG LUPA PASSWORD
                if ($check['password'] == $password) {
                    $data = array(
                        'last_login' => $last_login,
                        'token' => $token[0]
                    );

                    $param = array('id_user' => $id_user);

                    $update = $this->model_auth->update('kis_user', $data, $param);

                    if ($update === TRUE) {
                        $userAccount = array(
                            'id_user' => $id_user,
                            'tipe_user' => $tipe_user,
                            'last_login' => $last_login
                        );

                        $res = [
                            'status' => TRUE,
                            'msg' => 'Login Berhasil! Anda akan dialihkan ke halaman Dashboard',
                            'data' => $userAccount,
                            'token' => $token[0]
                        ];
                    } else {
                        $res = [
                            'status' => FALSE,
                            'msg' => 'Maaf! Login tidak berhasil',
                            'data' => ['input' => $this->input->post()],
                            'token' => NULL
                        ];
                    }
                } else {
                    $res = [
                        'status' => FALSE,
                        'msg' => 'Maaf! Password Anda salah',
                        'data' => $this->input->post(),
                        'token' => NULL
                    ];
                }
            } else { // KHUSUS ANGGOTA YANG LUPA PASSWORD
                if ($check['password_temp'] == $word) {
                    $data = array(
                        'password' => $password,
                        'password_temp' => NULL
                    );

                    $param = array('id_user' => $id_user);

                    $update = $this->model_auth->update('kis_user', $data, $param);

                    if ($update === TRUE) {
                        $userAccount = array(
                            'id_user' => $id_user,
                            'tipe_user' => $tipe_user,
                            'last_login' => $last_login
                        );

                        $res = [
                            'status' => TRUE,
                            'msg' => 'Login Berhasil! Anda akan dialihkan ke halaman Dashboard',
                            'data' => $userAccount,
                            'token' => $token[0]
                        ];
                    } else {
                        $res = [
                            'status' => FALSE,
                            'msg' => 'Maaf! Login tidak berhasil',
                            'data' => ['input' => $this->input->post()],
                            'token' => NULL
                        ];
                    }
                } else {
                    $res = [
                        'status' => FALSE,
                        'msg' => 'Maaf! Anda tidak menggunakan Password baru',
                        'data' => $this->input->post(),
                        'token' => NULL
                    ];
                }
            }
        }

        $this->response($res, 200);
    }

    function change_password_post()
    {
        $headers = $this->input->request_headers();
        $token = (isset($headers['token'])) ? $headers['token'] : FALSE;

        $username = $this->input->post('username');
        $word = $this->input->post('password');
        $confirm_password = $this->input->post('confirm_password');

        $berkah = 'Semoga Allah melindungi sistem ini dari serangan orang-orang yang tidak bertanggung jawab. Aamiin Allahumma Aamiin';

        $password = sha1($berkah . $word);

        $check = $this->model_auth->check_username($username);

        $count = count($check);

        $now = date('Y-m-d');

        if ($token) {
            $check_token = $this->model_auth->check_token($token);

            if ($check_token['cnt'] > 0) {
                $check_expired = $this->model_auth->check_expired($now, $token);

                if ($check_expired['expired'] > 7) {
                    $res = [
                        'status' => FALSE,
                        'msg' => 'Token Expired',
                        'data' => NULL
                    ];
                } else {
                    if ($count > 0) {
                        if ($word == $confirm_password) {
                            $data = array('password' => $password);
                            $param = array('id_user' => $username);

                            $update = $this->model_auth->update('kis_user', $data, $param);

                            if ($update === TRUE) {
                                $res = [
                                    'status' => TRUE,
                                    'msg' => 'Berhasil! Password berhasil diubah',
                                    'data' => $data
                                ];
                            } else {
                                $res = [
                                    'status' => FALSE,
                                    'msg' => 'Maaf! Password tidak berhasil diubah',
                                    'data' => ['input' => $this->input->post()]
                                ];
                            }
                        } else {
                            $res = [
                                'status' => FALSE,
                                'msg' => 'Maaf! Password dan Konfirmasi Password belum sama',
                                'data' => $this->input->post()
                            ];
                        }
                    } else {
                        $res = [
                            'status' => FALSE,
                            'msg' => 'Maaf! Username tidak ditemukan',
                            'data' => $this->input->post()
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
