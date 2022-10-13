<?php
defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

class Registration extends RestController
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('model_registration');
        date_default_timezone_set('Asia/Jakarta');
    }
}
