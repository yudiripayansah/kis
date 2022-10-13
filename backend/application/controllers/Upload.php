<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Upload extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('model_upload');
        date_default_timezone_set('Asia/Jakarta');
    }

    function member()
    {
        $member = file_get_contents('php://input');

        $decode = json_decode($member);

        $count = count($decode);

        $data = array();

        for ($i = 0; $i < $count; $i++) {
            $data[] = array(
                'noanggota' => $decode[$i]->noanggota,
                'nama' => $decode[$i]->nama,
                'majelis' => $decode[$i]->majelis,
                'desa' => $decode[$i]->desa,
                'status' => $decode[$i]->status,
                'simpok' => $decode[$i]->simpok,
                'simwa' => $decode[$i]->simwa,
                'sukarela' => $decode[$i]->sukarela
            );
        }

        $this->db->trans_begin();
        $this->model_upload->delete('kis_anggota');
        $this->model_upload->insert_batch('kis_anggota', $data);

        if ($this->db->trans_status() === TRUE) {
            $this->db->trans_commit();

            $result = array(
                'status' => TRUE,
                'message' => 'Data berhasil diupload!'
            );
        } else {
            $this->db->trans_rollback();

            $result = array(
                'status' => FALSE,
                'message' => 'Data gagal diupload!'
            );
        }

        echo json_encode($result);
    }
}
