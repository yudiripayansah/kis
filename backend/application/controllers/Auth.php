<?php
defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;
use Firebase\JWT\JWT;

class Auth extends RestController{
    function __construct(){
        parent::__construct();
        $this->load->model('model_auth');
        date_default_timezone_set('Asia/Jakarta');
    }

    function generate_token($user_id,$username,$fullname,$role_id,$branch_code,$nik,$status_karyawan,$email,$no_hp){
        $key = getenv('JWT_SECRET');

        $payload = array(
            'user_id' => $user_id,
            'username' => $username,
            'fullname' => $fullname,
            'role_id' => $role_id,
            'branch_code' => $branch_code,
            'nik' => $nik,
            'status_karyawan' => $status_karyawan,
            'email' => $email,
            'no_hp' => $no_hp
        );

        $jwt = JWT::encode($payload,$key);

        $decoded = JWT::decode($jwt,$key,array('HS256'));

        return [$jwt,$decoded];
    }

    function login_post(){
        $username = $this->input->post('username');
        $password = md5($this->input->post('password'));
        $last_login = date('Y-m-d H:i:s');

        $check_user = $this->model_auth->check_user($username,$password);

        $count = count($check_user);

        if($count > 0){
            $user_id = $check_user['user_id'];
            $username = $check_user['username'];
            $cif_no = $check_user['cif_no'];
            $fullname = $check_user['fullname'];
            $role_id = $check_user['role_id'];
            $branch_code = $check_user['branch_code'];
            $nik = $check_user['nik'];

            $check_staff = $this->model_auth->check_staff($nik);

            $status_karyawan = $check_staff['status_karyawan'];
            $foto_karyawan = $check_staff['foto_karyawan'];
            $email = $check_staff['email'];
            $no_hp = $check_staff['no_hp'];

            if($foto_karyawan == NULL){
                $foto_karyawan = 'default_user.jpg';
            }

            $path = 'https://simpres.baytulikhtiar.com/assets/foto_karyawan/';

            $path_foto = $path.$foto_karyawan;
            $type_foto = pathinfo($path_foto,PATHINFO_EXTENSION);
            $data_foto = @file_get_contents($path_foto);
            $base64_foto = 'data:image/'.$type_foto.';base64,'.base64_encode($data_foto);

            $token = $this->generate_token($user_id,$username,$fullname,$role_id,$branch_code,$nik,$status_karyawan,$email,$no_hp);

            $userData = array(
                'user_id' => $user_id,
                'username' => $username,
                'cif_no' => $cif_no,
                'fullname' => $fullname,
                'role_id' => $role_id,
                'branch_code' => $branch_code,
                'nik' => $nik,
                'status_karyawan' => $status_karyawan,
                'email' => $email,
                'no_hp' => $no_hp,
                'foto' => $base64_foto
            );

            $data = array(
                'last_login' => $last_login,
                'token' => $token[0]
            );

            $param = array('user_id' => $user_id);

            $this->db->trans_begin();
            $this->model_auth->updates('app_user',$data,$param);

            if($this->db->trans_status() === TRUE){
                $this->db->trans_commit();
                $res = [
                    'status' => TRUE,
                    'msg' => 'Login Berhasil! Anda akan dialihkan ke halaman Dashboard',
                    'data' => $userData,
                    'token' => $token[0]
                ];
            } else {
                $this->db->trans_rollback();
                $res = [
                    'status' => FALSE,
                    'msg' => 'Maaf! Jaringan Anda tidak stabil. Silakkan dicoba lagi.',
                    'data' => [
                        'username' => $username,
                        'password' => $password,
                        'input' => $this->input->post()
                    ]
                ];
            }
        } else {
            $res = [
                'status' => FALSE,
                'msg' => 'Maaf! Akun Anda tidak ditemukan.',
                'data' => [
                    'username' => $username,
                    'password' => $password,
                    'input' => $this->input->post()
                ]
            ];
        }

        $this->response($res, 200);
    }

    function example_get(){
        $res = [
            'status' => true,
            'msg' => 'contoh api get'
        ];

        $this->response($res, 200);
    }

    function example_put(){
        $res = [
            'status' => true,
            'msg' => 'contoh api put'
        ];

        $this->response($res, 200);
    }

    function example_delete(){
        $res = [
            'status' => true,
            'msg' => 'contoh api delete'
        ];

        $this->response($res, 200);
    }

    function example_patch(){
        $res = [
            'status' => true,
            'msg' => 'contoh api patch'
        ];

        $this->response($res, 200);
    }
}
