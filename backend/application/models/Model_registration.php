<?php

class Model_registration extends CI_Model
{

    function check_token($token)
    {
        $sql = "SELECT COUNT(*) AS cnt FROM tpl_user WHERE token = ?";

        $param = array($token);

        $query = $this->db->query($sql, $param);

        return $query->row_array();
    }

    function check_expired($now, $token)
    {
        $sql = "SELECT (?::DATE - last_login::DATE) AS expired FROM tpl_user WHERE token = ?";

        $param = array($now, $token);

        $query = $this->db->query($sql, $param);

        return $query->row_array();
    }
}
