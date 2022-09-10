<?php

class Model_presence extends CI_Model
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

	function jumlah_hari_libur($from_date, $thru_date)
	{
		$today = date('Y-m-d');
		$this->db->select('count(id) total');
		$this->db->where('tanggal >=', $from_date);
		$this->db->where('tanggal <', $thru_date);

		return $this->db->get('app_hari_libur');
	}

	function get_cuti_remain($nik)
	{
		$this->db->select('hak_cuti,hak_ijin');
		$this->db->where('nik', $nik);

		return $this->db->get('app_karyawan_detail', 1);
	}

	function pengurangan_cuti($nik, $hari)
	{
		$this->db->set('hak_cuti ', 'hak_cuti::int - ' . $hari . '::int', false);
		$this->db->where('nik', $nik);
		return $this->db->update('app_karyawan_detail');
	}

	function pengurangan_ijin($nik, $hari)
	{
		$this->db->set('hak_ijin ', 'hak_ijin::INT - ' . $hari . '::INT', FALSE);
		$this->db->where('nik', $nik);
		return $this->db->update('app_karyawan_detail');
	}

	function store($table, $data)
	{
		return $this->db->insert($table, $data);
	}

	function cek_tgl_libur($tgl)
	{
		$this->db->where('tanggal = ', $tgl);
		return $this->db->count_all_results('app_hari_libur');
	}

	function get_where($table, $where)
	{
		$this->db->where($where);
		return $this->db->get($table);
	}

	function update($table, $where, $data)
	{
		if (!empty($where)) {
			$this->db->where($where);
		}

		return $this->db->update($table, $data);
	}

	function update2($table, $data, $param)
	{
		$this->db->update($table, $data, $param);
	}

	function get_presence_detail($nik, $from_date, $thru_date)
	{
		$sql = "SELECT
		a.nik,
		b.fullname,
		a.masuk,
		a.keluar,
		a.tanggal,
		a.keterangan
		FROM app_absensi_manual AS a
		JOIN app_karyawan AS b ON a.nik = b.nik
		WHERE a.nik = ? AND a.tanggal BETWEEN ? AND ?
		ORDER BY a.tanggal ASC";

		$param = array($nik, $from_date, $thru_date);

		$query = $this->db->query($sql, $param);

		return $query->result_array();
	}

	function get_profile_staff($nik)
	{
		$sql = "SELECT
		apk.nik,
		apr.description AS status_staff,
		ap.description AS from_position,
		apa.description AS from_branch,
		au.username,
		apk.fullname,
		apk.no_ktp,
		apk.alamat,
		apk.jk,
		apk.gol_darah AS golongan_darah,
		apk.tmp_lahir,
		apk.tgl_lahir,
		apk.from_pernikahan,
		apk.jml_anak AS jumlah_anak,
		apk.npwp,
		apk.no_hp,
		apk.email,
		apd.sd,
		apd.smp,
		apd.sma,
		apd.diploma,
		apd.sarjana,
		apd.sertifikat,
		apd.lainnya
		FROM app_karyawan AS apk
		JOIN app_karyawan_detail AS akd ON akd.nik = apk.nik
		LEFT JOIN app_user AS au ON au.nik = apk.nik
		JOIN app_pendidikan AS apd ON apd.nik = apk.nik
		JOIN app_parameter AS ap ON ap.parameter_id = akd.from_position AND ap.parameter_group = 'jabatan'
		JOIN app_parameter AS apa ON apa.parameter_id = akd.from_branch AND apa.parameter_group = 'cabang'
		JOIN app_parameter AS apr ON apr.parameter_id = akd.status AND apr.parameter_group = 'status'
		WHERE apk.nik = ?";

		$param = array($nik);

		$query = $this->db->query($sql, $param);

		return $query->row_array();
	}

	function get_list_presence($nik, $from_date, $thru_date)
	{
		$sql = "SELECT
		ap.fullname,
		aa.nik,
		(CASE WHEN aa.kategori_cuti = '1' THEN
			'Non Khusus'
		 WHEN aa.kategori_cuti = '2' THEN
		 	'Istri Melahirkan'
		 WHEN aa.kategori_cuti = '3' THEN
		 	'Suami/Istri/Orang Tua/Mertua/Saudara Kandung meninggal dunia'
		 WHEN aa.kategori_cuti = '4' THEN
		 	'Saudara Tiri/Nenek/Kakek meninggal dunia'
		 WHEN aa.kategori_cuti = '5' THEN
		 	'Pernikahan Anak'
		 WHEN aa.kategori_cuti = '6' THEN
		 	'Khitanan Anak'
		 WHEN aa.kategori_cuti = '7' THEN
		 	'Umroh'
		 ELSE
		 	'Bersalin'
		END) AS kategori_cuti,
		aa.keterangan,
		aa.hari,
		aa.group AS tipe
		FROM app_alfa AS aa
		JOIN app_karyawan AS ap ON ap.nik = aa.nik
		WHERE aa.nik = ? AND aa.tgl_cuti BETWEEN ? AND ?
		ORDER BY aa.tgl_cuti";

		$param = array($nik, $from_date, $thru_date);

		$query = $this->db->query($sql, $param);

		return $query->result_array();
	}

	function get_list_kasbon($nik, $from_date, $thru_date)
	{
		$sql = "SELECT
		ak.jml_approve,
		(CASE WHEN ak.jml_approve IS NULL THEN ak.jml_kasbon ELSE ak.jml_approve END) AS jml_kasbon,
		ak.keterangan,
		ak.tgl_pengajuan,
		(CASE WHEN ak.status_kasbon = '0' THEN
			'Regis'
		WHEN ak.status_kasbon = '1' THEN
			'Disetujui'
		ELSE
			'Ditolak'
		END) AS status_kasbon
		FROM app_kasbon AS ak
		JOIN app_karyawan AS akr ON akr.nik = ak.nik
		WHERE akr.nik = ? AND ak.tgl_pengajuan BETWEEN ? AND ?
		ORDER BY ak.tgl_pengajuan";

		$param = array($nik, $from_date, $thru_date);

		$query = $this->db->query($sql, $param);

		return $query->result_array();
	}
}
