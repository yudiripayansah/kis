<?php

class Model_information extends CI_Model
{

	function check_token($token)
	{
		$sql = "SELECT COUNT(*) AS cnt FROM kis_user WHERE token = ?";

		$param = array($token);

		$query = $this->db->query($sql, $param);

		return $query->row_array();
	}

	function check_expired($now, $token)
	{
		$sql = "SELECT (?::DATE - last_login::DATE) AS expired FROM kis_user WHERE token = ?";

		$param = array($now, $token);

		$query = $this->db->query($sql, $param);

		return $query->row_array();
	}

	function get_cif($noanggota)
	{
		$sql = "SELECT * FROM kis_anggota WHERE noanggota = ?";

		$param = array($noanggota);

		$query = $this->db->query($sql, $param);

		return $query->row_array();
	}

	function get_deposito($noanggota)
	{
		$sql = "SELECT COALESCE(SUM(saldo),0) AS saldo_deposito FROM kis_deposito WHERE status = '1' AND noanggota = ?";

		$param = array($noanggota);

		$query = $this->db->query($sql, $param);

		return $query->row_array();
	}

	function get_financing($noanggota)
	{
		$sql = "SELECT COALESCE(SUM(saldo_pkk+saldo_mgn),0) AS saldo_outstanding FROM kis_pembiayaan WHERE status = '1' AND noanggota = ?";

		$param = array($noanggota);

		$query = $this->db->query($sql, $param);

		return $query->row_array();
	}

	function get_sum_cif()
	{
		$sql = "SELECT
		COUNT(*) AS jumlah,
		COALESCE(SUM(simpok),0) AS simpok,
		COALESCE(SUM(simwa),0) AS simwa,
		COALESCE(SUM(sukarela),0) AS sukarela
		FROM kis_anggota";

		$query = $this->db->query($sql);

		return $query->row_array();
	}

	function get_sum_deposito()
	{
		$sql = "SELECT COALESCE(SUM(saldo),0) AS saldo_deposito FROM kis_deposito WHERE status = '1'";

		$query = $this->db->query($sql);

		return $query->row_array();
	}

	function get_sum_financing()
	{
		$sql = "SELECT COALESCE(SUM(saldo_pkk+saldo_mgn),0) AS saldo_outstanding FROM kis_pembiayaan WHERE status = '1'";

		$query = $this->db->query($sql);

		return $query->row_array();
	}

	function get_detail_saving($noanggota, $jenis_trx)
	{
		$sql = "SELECT * FROM kis_trx_simpanan WHERE noanggota = ? AND jenis_trx = ? ORDER BY trx_date ASC";

		$param = array($noanggota, $jenis_trx);

		$query = $this->db->query($sql, $param);

		return $query->result_array();
	}

	function get_detail_deposito($noanggota)
	{
		$sql = "SELECT
		ktd.notran,
		ktd.nomrek,
		ktd.nourut,
		ktd.trx_date,
		ktd.saldo_awal,
		ktd.amount_trx,
		ktd.saldo
		FROM kis_trx_deposito AS ktd
		JOIN kis_deposito AS kd ON kd.nomrek = ktd.nomrek
		WHERE kd.noanggota = ?
		ORDER BY ktd.trx_date ASC";

		$param = array($noanggota);

		$query = $this->db->query($sql, $param);

		return $query->result_array();
	}

	function get_detail_financing($noanggota)
	{
		$sql = "SELECT
		ktp.notran,
		ktp.tgl_jtempo,
		ktp.tgl_bayar,
		ktp.angs_ke,
		ktp.angs_pokok,
		ktp.saldo_pokok,
		ktp.saldo_margin
		FROM kis_trx_pembiayaan AS ktp
		JOIN kis_pembiayaan AS kp ON kp.nomrek = ktp.nomrek
		WHERE kp.noanggota = ?
		ORDER BY ktp.tgl_jtempo ASC";

		$param = array($noanggota);

		$query = $this->db->query($sql, $param);

		return $query->result_array();
	}
}
