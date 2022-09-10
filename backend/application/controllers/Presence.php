<?php
defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;
use Firebase\JWT\JWT;

class Presence extends RestController
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('model_presence');
        date_default_timezone_set('Asia/Jakarta');
    }

    function hak_cuti_ijin_get()
    {
        $headers = $this->input->request_headers();
        $token = (isset($headers['Token'])) ? $headers['Token'] : false;

        $nik = $this->input->get('nik');

        $get = $this->model_presence->get_cuti_remain($nik);

        $data = array(
            'hak_cuti' => $get->row('hak_cuti'),
            'hak_ijin' => $get->row('hak_ijin')
        );

        if ($token) {
            $check_token = $this->model_presence->check_token($token);

            if ($check_token['cnt'] > 0) {
                $res = [
                    'status' => true,
                    'msg' => 'Hak Cuti berhasil didapatkan',
                    'data' => $data
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

    function cuti_post()
    {
        $headers = $this->input->request_headers();
        $token = (isset($headers['Token'])) ? $headers['Token'] : false;
        $branch_code = $this->input->post('branch_code');
        $nik = $this->input->post('nik');
        $sisa_cuti = $this->input->post('sisa_cuti');
        $sisa_ijin = $this->input->post('sisa_ijin');
        $tipe = $this->input->post('tipe');
        $khusus = $this->input->post('khusus');
        $tgl_cuti = date('Y-m-d', strtotime($this->input->post('tgl_cuti')));
        $tgl_cuti2 = date('Y-m-d', strtotime($this->input->post('tgl_cuti2')));
        $keterangan = nl2br($this->input->post('keterangan'));
        $user_id = $nik;

        $hari_allday = hitung_hari_allday($tgl_cuti, $tgl_cuti2);
        $hari_workday = hitung_hari_weekend($tgl_cuti, $tgl_cuti2);
        $hari_libur = $this->model_presence->jumlah_hari_libur($tgl_cuti, $tgl_cuti2)->row('total');

        $total_hari = $hari_workday - $hari_libur;

        $created_by = $user_id;
        $approve_by = NULL;

        if ($tipe == 'cuti_khusus') {
            $kategori_cuti = $khusus;
        } else {
            $kategori_cuti = 1;
        }

        if ($token) {
            $check_token = $this->model_presence->check_token($token);

            if ($check_token['cnt'] > 0) {
                $now = date('Y-m-d');

                $check_expired = $this->model_presence->check_expired($now, $token);

                if ($check_expired['expired'] > 7) {
                    $res = [
                        'status' => false,
                        'msg' => 'Token Expired'
                    ];
                } else {
                    $arr_ci = $this->model_presence->get_cuti_remain($nik);

                    $hak_cuti = $arr_ci->row('hak_cuti');
                    $hak_ijin = $arr_ci->row('hak_ijin');

                    $total_xxx = $hak_cuti + $hak_ijin;

                    if ($tipe == 'ijin' or $tipe == 'cuti') {
                        if ($hak_cuti == 0 and $hak_ijin == 0) {
                            $res = [
                                'status' => false,
                                'msg' => 'Hak Ijin dan Cuti telah habis'
                            ];
                        }

                        if ((int)$total_hari > (int)$total_xxx) {
                            $res = [
                                'status' => false,
                                'msg' => 'Hak ' . $tipe . ' tidak mencukupi ' . $total_hari . " >= " . $total_xxx
                            ];
                        }
                    }

                    if ($tipe == 'ijin') {
                        if ($total_hari <= $hak_ijin) {
                            // PENGURANGAN HAK IJIN
                            // $exec = $this->model_presence->pengurangan_ijin($nik, $hari_workday);

                            // INSERT KE TABEL APP ALFA
                            $data = array(
                                'nik' => $nik,
                                'tgl_cuti' => $tgl_cuti,
                                'kategori_cuti' => $kategori_cuti,
                                'keterangan' => $keterangan,
                                'aprove_by' => $approve_by,
                                'created_by' => $created_by,
                                'created_date' => date('Y-m-d H:i:s'),
                                'group' => $tipe,
                                'hari' => $total_hari,
                                'tgl_cuti2' => $tgl_cuti2
                            );

                            $exec = $this->model_presence->store('app_alfa', $data);
                        } else {
                            $nilai_x = $total_hari - $hak_ijin; // nilai yang digunakan untuk mengurangi hak cuti

                            // $exec = $this->model_presence->pengurangan_ijin($nik, $hak_ijin); // set to zero

                            // PERHITUNGAN TANGGAL AKHIR TIDAK BENTROK DENGAN TGL LIBUR
                            $no = $hak_ijin - 1;
                            $no_libur = 0;

                            while ($no_libur <= 1) {
                                $tgl_akhir = date('Y-m-d', strtotime($tgl_cuti . '+ ' . $no . ' DAY'));

                                $cek_tgl_libur = $this->model_presence->cek_tgl_libur($tgl_akhir);

                                if ($cek_tgl_libur == 0) {
                                    $no_libur = 2;
                                } else {
                                    $no_libur = 0;
                                }

                                $no++;
                            }

                            $data = array(
                                'nik' => $nik,
                                'tgl_cuti' => $tgl_cuti,
                                'tgl_cuti2' => $tgl_akhir,
                                'kategori_cuti' => $kategori_cuti,
                                'keterangan' => $keterangan,
                                'aprove_by' => $approve_by,
                                'created_by' => $created_by,
                                'created_date' => date('Y-m-d H:i:s'),
                                'group' => 'ijin',
                                'hari' => $hak_ijin
                            );

                            $exec = $this->model_presence->store('app_alfa', $data);

                            // PERHITUNGAN TANGGAL AWAL TIDAK BENTROK DENGAN TGL LIBUR
                            $no = $nilai_x;
                            $no_libur = 0;

                            while ($no_libur <= 1) {
                                $tgl_awal = date('Y-m-d', strtotime($tgl_akhir . '+ ' . $no . ' DAY'));

                                $cek_tgl_libur = $this->model_presence->cek_tgl_libur($tgl_awal);

                                if ($cek_tgl_libur == 0) {
                                    $no_libur = 2;
                                } else {
                                    $no_libur = 0;
                                }

                                $no++;
                            }

                            $data2 = array(
                                'nik' => $nik,
                                'tgl_cuti' => $tgl_awal,
                                'tgl_cuti2' => $tgl_cuti2,
                                'kategori_cuti' => $kategori_cuti,
                                'keterangan' => $keterangan,
                                'aprove_by' => $approve_by,
                                'created_by' => $created_by,
                                'created_date' => date('Y-m-d H:i:s'),
                                'group' => 'cuti',
                                'hari' => $nilai_x
                            );

                            $exec2 = $this->model_presence->store('app_alfa', $data2);

                            $exec = $this->model_presence->pengurangan_cuti($nik, $nilai_x);
                        }
                    } elseif ($tipe == 'cuti') {
                        $exec = $this->model_presence->pengurangan_cuti($nik, $total_hari);

                        $data = array(
                            'nik' => $nik,
                            'tgl_cuti' => $tgl_cuti,
                            'tgl_cuti2' => $tgl_cuti2,
                            'kategori_cuti' => $kategori_cuti,
                            'keterangan' => $keterangan,
                            'aprove_by' => $approve_by,
                            'created_by' => $created_by,
                            'created_date' => date('Y-m-d H:i:s'),
                            'group' => $tipe,
                            'hari' => $total_hari
                        );

                        $exec = $this->model_presence->store('app_alfa', $data);
                    } else {
                        $data = array(
                            'nik' => $nik,
                            'tgl_cuti' => $tgl_cuti,
                            'kategori_cuti' => $kategori_cuti,
                            'keterangan' => $keterangan,
                            'aprove_by' => $approve_by,
                            'created_by' => $created_by,
                            'created_date' => date('Y-m-d H:i:s'),
                            'group' => $tipe,
                            'hari' => $total_hari,
                            'tgl_cuti2' => $tgl_cuti2
                        );

                        $exec = $this->model_presence->store('app_alfa', $data);
                    }

                    for ($i = 0; $i < $hari_allday; $i++) {
                        $tgl_cuti_temp = date('Y-m-d', strtotime($tgl_cuti . '+ ' . $i . ' DAY'));
                        $cek_tgl_libur = $this->model_presence->cek_tgl_libur($tgl_cuti_temp);

                        $table = 'app_absensi_manual';

                        if ($cek_tgl_libur == 0 && date('l', strtotime($tgl_cuti_temp)) != 'Saturday' && date('l', strtotime($tgl_cuti_temp)) != 'Sunday') {
                            $cek_tipe = $this->model_presence->get_where('app_alfa', [
                                'nik' => $nik,
                                'tgl_cuti' => $tgl_cuti_temp,
                            ]);

                            $where = array(
                                'nik' => $nik,
                                'tanggal' => $tgl_cuti_temp
                            );

                            $data = array(
                                'masuk' => '',
                                'keluar' => '',
                                'keterangan' => $keterangan
                            );

                            $exec = $this->model_presence->update($table, $where, $data);
                        }
                    }

                    if ($exec === true) {
                        $res = [
                            'status' => true,
                            'msg' => 'Pengajuan Proses Cuti Berhasil'
                        ];
                    } else {
                        $res = [
                            'status' => false,
                            'msg' => 'Pengajuan Proses Cuti Gagal'
                        ];
                    }
                }
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

    function presence_detail_post()
    {
        $headers = $this->input->request_headers();
        $token = (isset($headers['Token'])) ? $headers['Token'] : false;
        $periode = $this->input->post('periode');
        $nik = $this->input->post('nik');
        $explode = explode('-', $periode);
        $month = $explode[0];
        $year = $explode[1];

        $thru_date = $year . '-' . $month . '-20';
        $from_date = date('Y-m-d', strtotime($thru_date . ' - 1 MONTH + 1 DAY'));

        if ($token) {
            $check_token = $this->model_presence->check_token($token);

            if ($check_token['cnt'] > 0) {
                $get = $this->model_presence->get_presence_detail($nik, $from_date, $thru_date);

                $data = array();

                foreach ($get as $gt) {
                    $data[] = array(
                        'masuk' => $gt['masuk'],
                        'keluar' => $gt['keluar'],
                        'tanggal' => $gt['tanggal'],
                        'keterangan' => $gt['keterangan']
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

    function kasbon_post()
    {
        $headers = $this->input->request_headers();
        $token = (isset($headers['Token'])) ? $headers['Token'] : false;
        $nik = $this->input->post('nik');
        $jml_kasbon = $this->input->post('jml_kasbon');
        $tgl_pengajuan = date('Y-m-d H:i:s');
        $keterangan = $this->input->post('keterangan');

        $jml_kasbon = numeric($jml_kasbon);

        if ($token) {
            $check_token = $this->model_presence->check_token($token);

            if ($check_token['cnt'] > 0) {
                $data = array(
                    'nik' => $nik,
                    'jml_kasbon' => $jml_kasbon,
                    'tgl_pengajuan' => $tgl_pengajuan,
                    'keterangan' => $keterangan
                );

                $exec = $this->model_presence->store('app_kasbon', $data);

                if ($exec === true) {
                    $res = [
                        'status' => true,
                        'msg' => 'Pengajuan Kasbon Berhasil'
                    ];
                } else {
                    $res = [
                        'status' => false,
                        'msg' => 'Pengajuan Kasbon Gagal'
                    ];
                }
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

    function profile_staff_post()
    {
        $headers = $this->input->request_headers();
        $token = (isset($headers['Token'])) ? $headers['Token'] : false;

        $nik = $this->input->post('nik');

        if ($token) {
            $check_token = $this->model_presence->check_token($token);

            if ($check_token['cnt'] > 0) {
                $get = $this->model_presence->get_profile_staff($nik);

                $data = array(
                    'nik' => $get['nik'],
                    'status' => $get['status_staff'],
                    'from_position' => $get['from_position'],
                    'from_branch' => $get['from_branch'],
                    'username' => $get['username'],
                    'fullname' => $get['fullname'],
                    'no_ktp' => $get['no_ktp'],
                    'alamat' => $get['alamat'],
                    'jk' => $get['jk'],
                    'golongan_darah' => $get['golongan_darah'],
                    'tmp_lahir' => $get['tmp_lahir'],
                    'tgl_lahir' => $get['tgl_lahir'],
                    'from_pernikahan' => $get['from_pernikahan'],
                    'jumlah_anak' => $get['jumlah_anak'],
                    'npwp' => $get['npwp'],
                    'no_hp' => $get['no_hp'],
                    'email' => $get['email'],
                    'sd' => $get['sd'],
                    'smp' => $get['smp'],
                    'sma' => $get['sma'],
                    'diploma' => $get['diploma'],
                    'sarjana' => $get['sarjana'],
                    'sertifikat' => $get['sertifikat'],
                    'lainnya' => $get['lainnya']
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

    function list_presence_post()
    {
        $headers = $this->input->request_headers();
        $token = (isset($headers['Token'])) ? $headers['Token'] : false;

        $periode = $this->input->post('periode');
        $nik = $this->input->post('nik');

        $explode = explode('-', $periode);
        $month = $explode[0];
        $year = $explode[1];

        $thru_date = $year . '-' . $month . '-20';
        $from_date = date('Y-m-d', strtotime($thru_date . ' - 1 MONTH + 1 DAY'));

        if ($token) {
            $check_token = $this->model_presence->check_token($token);

            if ($check_token['cnt'] > 0) {
                $get = $this->model_presence->get_list_presence($nik, $from_date, $thru_date);

                $data = array();

                foreach ($get as $gt) {
                    $data[] = array(
                        'fullname' => $gt['fullname'],
                        'nik' => $gt['nik'],
                        'kategori_cuti' => $gt['kategori_cuti'],
                        'keterangan' => $gt['keterangan'],
                        'hari' => $gt['hari'],
                        'tipe' => $gt['tipe']
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

    function list_kasbon_post()
    {
        $headers = $this->input->request_headers();
        $token = (isset($headers['Token'])) ? $headers['Token'] : false;

        $tahun = $this->input->post('tahun');
        $nik = $this->input->post('nik');

        $from_date = $tahun . '-01-01';
        $thru_date = $tahun . '-12-31';

        if ($token) {
            $check_token = $this->model_presence->check_token($token);

            if ($check_token['cnt'] > 0) {
                $get = $this->model_presence->get_list_kasbon($nik, $from_date, $thru_date);

                $data = array();

                foreach ($get as $gt) {
                    $tgl_pengajuan = date('Y-m-d', strtotime($gt['tgl_pengajuan']));
                    $tgl_pengajuan = date('d/m/Y', strtotime(str_replace('-', '/', $tgl_pengajuan)));

                    $data[] = array(
                        'jml_kasbon' => $gt['jml_kasbon'],
                        'keterangan' => $gt['keterangan'],
                        'tgl_pengajuan' => $tgl_pengajuan,
                        'status_kasbon' => $gt['status_kasbon']
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

    function edit_profile_post()
    {
        $headers = $this->input->request_headers();
        $token = (isset($headers['Token'])) ? $headers['Token'] : false;

        $nik = $this->input->post('nik');
        $password = $this->input->post('password');
        $nama = $this->input->post('fullname');
        $foto_karyawan = $this->input->post('foto_karyawan');
        $ktp = $this->input->post('no_ktp');
        $alamat = $this->input->post('alamat');
        $jenis_kelamin = $this->input->post('jk');
        $golongan_darah = $this->input->post('golongan_darah');
        $tempat_lahir = $this->input->post('tmp_lahir');
        $tanggal_lahir = $this->input->post('tgl_lahir');
        $status_pernikahan = $this->input->post('from_pernikahan');
        $jumlah_anak = $this->input->post('jumlah_anak');
        $npwp = $this->input->post('npwp');
        $handphone = $this->input->post('no_hp');
        $email = $this->input->post('email');
        $sd = $this->input->post('sd');
        $smp = $this->input->post('smp');
        $sma = $this->input->post('sma');
        $diploma = $this->input->post('diploma');
        $sarjana = $this->input->post('sarjana');
        $sertifikat = $this->input->post('sertifikat');

        $created_by = $nik;
        $created_date = date('Y-m-d H:i:s');

        if ($token) {
            $check_token = $this->model_presence->check_token($token);

            if ($check_token['cnt'] > 0) {
                /*
                if($password != ''){
                    $data = array('password' => md5($password));
                    $param = array('nik' => $nik);

                    $this->model_presence->update2('app_user',$data,$param);
                }
                */

                $data2 = array(
                    'nik' => $nik,
                    'fullname' => $nama,
                    'no_ktp' => $ktp,
                    'alamat' => $alamat,
                    'jk' => $jenis_kelamin,
                    'gol_darah' => $golongan_darah,
                    'tmp_lahir' => $tempat_lahir,
                    'tgl_lahir' => date('Y-m-d', strtotime($tanggal_lahir)),
                    'from_pernikahan' => $status_pernikahan,
                    'jml_anak' => $jumlah_anak,
                    'npwp' => $npwp,
                    'no_hp' => $handphone,
                    'email' => $email,
                    'sd' => $sd,
                    'smp' => $smp,
                    'sma' => $sma,
                    'diploma' => $diploma,
                    'sarjana' => $sarjana,
                    'sertifikat' => $sertifikat,
                    'created_by' => $created_by,
                    'created_date' => $created_date
                );

                if ($foto_karyawan != '') {
                    $data2['foto_karyawan'] = $foto_karyawan;
                }

                $exec = $this->model_presence->store('app_pending_profile', $data2);

                if ($exec === true) {
                    $res = [
                        'status' => true,
                        'msg' => 'Pengajuan Edit Profil Berhasil. Silakkan menunggu Approval oleh Admin'
                    ];
                } else {
                    $res = [
                        'status' => false,
                        'msg' => 'Pengajuan Edit Profil Gagal'
                    ];
                }
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

    function keterlambatan_post()
    {
        $headers = $this->input->request_headers();
        $token = (isset($headers['Token'])) ? $headers['Token'] : false;
        $branch_code = $this->input->post('branch_code');
        $nik = $this->input->post('nik');
        $kategori_terlambat = $this->input->post('kategori_terlambat');
        $kategori_absen = $this->input->post('kategori_absen');
        $tgl_terlambat = $this->input->post('tgl_terlambat');
        $keterangan = $this->input->post('keterangan');

        $created_date = date('Y-m-d H:i:s');

        $input = [
            'branch_code' => $branch_code,
            'nik'         => $nik,
            'kategori_terlambat' => $kategori_terlambat,
            'kategori_absen' => $kategori_absen,
            'tgl_terlambat'  => $tgl_terlambat,
            'keterangan'     => $keterangan,
            'created_by'     => $nik,
            'created_date' => $created_date

        ];

        $exec = $this->model_presence->store('app_keterlambatan', $input);

        if ($exec === true) {
            $res = [
                'status' => true,
                'msg' => 'Pengajuan Keterlambatan Berhasil. Silakkan menunggu Approval oleh Admin'
            ];
        } else {
            $res = [
                'status' => false,
                'msg' => 'Pengajuan Keterlambatan Gagal'
            ];
        }

        $this->response($res, 200);
    }

    function update_fbtoken_post()
    {
        $headers = $this->input->request_headers();
        $token = (isset($headers['Token'])) ? $headers['Token'] : false;
        $user_id = $this->input->post('user_id');
        $fbtoken = $this->input->post('fbtoken');
        if ($token) {
            $check_token = $this->model_presence->check_token($token);
            if ($check_token['cnt'] > 0) {
                $doUpdate = $this->model_presence->update('app_user', array('user_id' => $user_id), array('firebase_token' => $fbtoken));
                if ($doUpdate) {
                    $res = [
                        'status' => true,
                        'msg' => 'Update Firebase Token Berhasil'
                    ];
                } else {
                    $res = [
                        'status' => true,
                        'msg' => 'Update Firebase Token Gagal'
                    ];
                }
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

    function notifikasi_get($title = false, $message = false, $action = '', $ids = [])
    {
        if (!$title) {
            $title = $this->input->get('title');
        }
        if (!$message) {
            $message = $this->input->get('message');
        }
        if (!$action) {
            $action = $this->input->get('action');
        }
        if (count($ids) < 1) {
            $ids = $this->input->get('ids');
        }
        $postfield = array(
            "notification" => array(
                "title" => $title,
                "body" => $message,
                "click_action" => $action
            ),
            "registration_ids" => $ids
        );
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://fcm.googleapis.com/fcm/send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($postfield),
            CURLOPT_HTTPHEADER => array(
                'Authorization: key=AAAADSwDD9Q:APA91bFEPUN68Y3oUCmaOOOeDwKikCiyID3X9m4_LcrbhaGhENdTEu6pCyl8NkK3aBcvyG_EfOjgLwP-H68I5CY14rJchLFFvFMFAV5pLAAfa_CRfLUKXceSOPJNkZejFAxEavhbTn74',
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $this->response($response, 200);
    }
}
