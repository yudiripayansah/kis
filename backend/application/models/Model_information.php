<?php

class Model_information extends CI_Model
{

	function check_token($token)
	{
		$sql = "SELECT COUNT(*) AS cnt FROM app_user WHERE token = ?";

		$param = array($token);

		$query = $this->db->query($sql, $param);

		return $query->row_array();
	}

	function check_expired($now, $token)
	{
		$sql = "SELECT (?::DATE - last_login::DATE) AS expired FROM app_user WHERE token = ?";

		$param = array($now, $token);

		$query = $this->db->query($sql, $param);

		return $query->row_array();
	}

	function get_info($nik)
	{
		$sql = "SELECT
		id,
		(CASE WHEN kategori = '1' THEN
			'Hari Kerja'
		WHEN kategori = '2' THEN
			'Kepegawaian'
		WHEN kategori = '3' THEN
			'Penggajian'
		ELSE
			'Lain-lain'
		END) AS kategori,
		judul,
		gambar,
		pesan,
		created_date
		FROM app_info WHERE penerima = '0'

		UNION ALL

		SELECT
		ai.id,
		(CASE WHEN ai.kategori = '1' THEN
			'Hari Kerja'
		WHEN ai.kategori = '2' THEN
			'Kepegawaian'
		WHEN ai.kategori = '3' THEN
			'Penggajian'
		ELSE
			'Lain-lain'
		END) AS kategori,
		ai.judul,
		ai.gambar,
		ai.pesan,
		ai.created_date
		FROM app_info AS ai
		JOIN app_info_receipt AS air ON air.id_info = ai.id
		WHERE ai.penerima = '1' AND air.nik = ? ";

		$param = array($nik);

		$sql .= "ORDER BY 4 DESC";

		$query = $this->db->query($sql, $param);

		return $query->result_array();
	}

	function get_info_detail($id)
	{
		$sql = "SELECT
		id,
		(CASE WHEN kategori = '1' THEN
			'Hari Kerja'
		WHEN kategori = '2' THEN
			'Kepegawaian'
		WHEN kategori = '3' THEN
			'Penggajian'
		ELSE
			'Lain-lain'
		END) AS kategori,
		judul,
		gambar,
		pesan,
		created_date
		FROM app_info WHERE id = ?";

		$param = array($id);

		$query = $this->db->query($sql, $param);

		return $query->row_array();
	}

	function check_email($nik)
	{
		$sql = "SELECT
		ak.nik,
		ak.email,
		au.username
		FROM app_karyawan AS ak
		JOIN app_user AS au ON au.nik = ak.nik
		WHERE ak.nik = ?";

		$param = array($nik);

		$query = $this->db->query($sql, $param);

		return $query->row_array();
	}

	function update_password($table, $data, $param)
	{
		$this->db->update($table, $data, $param);
	}
}
