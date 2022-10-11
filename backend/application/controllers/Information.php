<?php
defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

class Information extends RestController
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('model_information');
        date_default_timezone_set('Asia/Jakarta');
    }

    function dashboard_post()
    {
        $headers = $this->input->request_headers();
        $token = (isset($headers['Token'])) ? $headers['Token'] : FALSE;

        $id_user = $this->input->post('id_user');
        $tipe_user = $this->input->post('tipe_user');

        $now = date('Y-m-d');

        if ($token) {
            $check_token = $this->model_information->check_token($token);

            if ($check_token['cnt'] > 0) {
                $check_expired = $this->model_information->check_expired($now, $token);

                if ($check_expired['expired'] > 7) {
                    $res = [
                        'status' => FALSE,
                        'msg' => 'Token Expired',
                        'data' => NULL
                    ];
                } else {
                    if ($tipe_user == 1) {
                        $get_cif = $this->model_information->get_cif($id_user);
                        $get_deposito = $this->model_information->get_deposito($id_user);
                        $get_financing = $this->model_information->get_financing($id_user);

                        $data = array(
                            'noanggota' => $get_cif['noanggota'],
                            'jumlah' => NULL,
                            'nama' => $get_cif['nama'],
                            'majelis' => $get_cif['majelis'],
                            'desa' => $get_cif['desa'],
                            'simpok' => currency($get_cif['simpok']),
                            'simwa' => currency($get_cif['simwa']),
                            'sukarela' => currency($get_cif['sukarela']),
                            'saldo_deposito' => currency($get_deposito['saldo_deposito']),
                            'saldo_outstanding' => currency($get_financing['saldo_outstanding'])
                        );
                    } else {
                        $get_sum_cif = $this->model_information->get_sum_cif();
                        $get_sum_deposito = $this->model_information->get_sum_deposito();
                        $get_sum_financing = $this->model_information->get_sum_financing();

                        $data = array(
                            'noanggota' => NULL,
                            'jumlah' => $get_sum_cif['jumlah'],
                            'simpok' => $get_sum_cif['simpok'],
                            'simwa' => $get_sum_cif['simwa'],
                            'sukarela' => $get_sum_cif['sukarela'],
                            'saldo_deposito' => currency($get_sum_deposito['saldo_deposito']),
                            'saldo_outstanding' => currency($get_sum_financing['saldo_outstanding'])
                        );
                    }

                    $res = [
                        'status' => TRUE,
                        'msg' => NULL,
                        'data' => $data
                    ];
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

    function history_member_saving_post()
    {
        $headers = $this->input->request_headers();
        $token = (isset($headers['Token'])) ? $headers['Token'] : FALSE;

        $id_user = $this->input->post('id_user');
        $jenis_trx = $this->input->post('jenis_trx');

        $now = date('Y-m-d');

        if ($token) {
            $check_token = $this->model_information->check_token($token);

            if ($check_token['cnt'] > 0) {
                $check_expired = $this->model_information->check_expired($now, $token);

                if ($check_expired['expired'] > 7) {
                    $res = [
                        'status' => FALSE,
                        'msg' => 'Token Expired',
                        'data' => NULL
                    ];
                } else {
                    $get = $this->model_information->get_detail_saving($id_user, $jenis_trx);

                    $data = array();

                    foreach ($get as $gt) {
                        if ($gt['dc_trx'] == 'C') {
                            $setor = $gt['amount_trx'];
                            $tarik = 0;
                        } else {
                            $setor = 0;
                            $tarik = $gt['amount_trx'];
                        }

                        $data[] = array(
                            'trx_date' => $gt['trx_date'],
                            'saldo_awal' => currency($gt['saldo_awal']),
                            'setor' => currency($setor),
                            'tarik' => currency($tarik),
                            'saldo' => currency($gt['saldo'])
                        );
                    }

                    $res = [
                        'status' => TRUE,
                        'msg' => NULL,
                        'data' => $data
                    ];
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

    function history_member_deposito_post()
    {
        $headers = $this->input->request_headers();
        $token = (isset($headers['Token'])) ? $headers['Token'] : FALSE;

        $id_user = $this->input->post('id_user');

        $now = date('Y-m-d');

        if ($token) {
            $check_token = $this->model_information->check_token($token);

            if ($check_token['cnt'] > 0) {
                $check_expired = $this->model_information->check_expired($now, $token);

                if ($check_expired['expired'] > 7) {
                    $res = [
                        'status' => FALSE,
                        'msg' => 'Token Expired',
                        'data' => NULL
                    ];
                } else {
                    $get = $this->model_information->get_detail_deposito($id_user);

                    $data = array();

                    foreach ($get as $gt) {
                        $data[] = array(
                            'notran' => $gt['notran'],
                            'nomrek' => $gt['nomrek'],
                            'nourut' => $gt['nourut'],
                            'trx_date' => $gt['trx_date'],
                            'saldo_awal' => currency($gt['saldo_awal']),
                            'amount_trx' => currency($gt['amount_trx']),
                            'saldo' => currency($gt['saldo'])
                        );
                    }

                    $res = [
                        'status' => TRUE,
                        'msg' => NULL,
                        'data' => $data
                    ];
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

    function history_member_financing_post()
    {
        $headers = $this->input->request_headers();
        $token = (isset($headers['Token'])) ? $headers['Token'] : FALSE;

        $id_user = $this->input->post('id_user');

        $now = date('Y-m-d');

        if ($token) {
            $check_token = $this->model_information->check_token($token);

            if ($check_token['cnt'] > 0) {
                $check_expired = $this->model_information->check_expired($now, $token);

                if ($check_expired['expired'] > 7) {
                    $res = [
                        'status' => FALSE,
                        'msg' => 'Token Expired',
                        'data' => NULL
                    ];
                } else {
                    $get = $this->model_information->get_detail_financing($id_user);

                    $data = array();

                    foreach ($get as $gt) {
                        $data[] = array(
                            'notran' => $gt['notran'],
                            'tgl_jtempo' => $gt['tgl_jtempo'],
                            'tgl_bayar' => $gt['tgl_bayar'],
                            'angs_ke' => $gt['angs_ke'],
                            'angs_pokok' => currency($gt['angs_pokok']),
                            'saldo_pokok' => currency($gt['saldo_pokok']),
                            'saldo_margin' => currency($gt['saldo_margin'])
                        );
                    }

                    $res = [
                        'status' => TRUE,
                        'msg' => NULL,
                        'data' => $data
                    ];
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
