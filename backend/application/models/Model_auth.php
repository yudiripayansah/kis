<?php

class Model_auth extends CI_Model
{

	function check_user($username, $password)
	{
		$sql = "SELECT
		aus.*,
		apk.cif_no
		FROM app_user AS aus
		LEFT JOIN app_karyawan AS apk ON apk.nik = aus.nik
		WHERE aus.username = ? AND aus.password = ?";

		$param = array($username, $password);

		$query = $this->db->query($sql, $param);

		return $query->row_array();
	}

	function check_staff($nik)
	{
		$sql = "SELECT
		(CASE WHEN akd.status = '10' THEN
			'Karyawan Training'
		 WHEN akd.status = '11' THEN
		 	'Perpanjang Training'
		 WHEN akd.status = '20' THEN
		 	'Karyawan Kontrak Satu'
		 WHEN akd.status = '21' THEN
		 	'Karyawan Kontrak Dua'
		 WHEN akd.status = '22' THEN
		 	'Perpanjang Kontrak Dua'
		 WHEN akd.status = '30' THEN
		 	'Karyawan Tetap'
		 WHEN akd.status = '40' THEN
		 	'Karyawan Magang'
		 ELSE
		 	'Resign'
		END) AS status_karyawan,
		ak.foto_karyawan,
		ak.email,
		ak.no_hp
		FROM app_karyawan AS ak
		JOIN app_karyawan_detail AS akd ON akd.nik = ak.nik
		WHERE ak.nik = ?";

		$param = array($nik);

		$query = $this->db->query($sql, $param);

		return $query->row_array();
	}

	function updates($table, $data, $param)
	{
		$this->db->update($table, $data, $param);
	}
}
