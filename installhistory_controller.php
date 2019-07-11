<?php
class Installhistory_controller extends Module_controller
{
    // Require authentication
    public function __construct()
    {
        // Store module path
        $this->module_path = dirname(__FILE__) .'/';
        $this->view_path = $this->module_path . 'views/';

        if (! $this->authorized()) {
            redirect('auth/login');
        }
    }

    public function get_apple_data($serial_number = '')
    {
        $obj = new View();
        $obj->view('json', array('msg' => $this->_get_data($serial_number, 'apple')));
    }

    public function get_third_party_data($serial_number = '')
    {
        $obj = new View();
        $obj->view('json', array('msg' => $this->_get_data($serial_number, 'third_party')));
    }

    private function _get_data($serial_number = '', $what = 'apple')
    {
        $where = [['installhistory.serial_number', $serial_number]];
        if($what == 'apple'){
            $where[] = ['installhistory.packageIdentifiers', 'LIKE', 'com.apple.%'];
        }else{
            $where[] = ['installhistory.packageIdentifiers', 'NOT LIKE', 'com.apple.%'];
        }
        return Installhistory_model::select('displayName', 'displayVersion', 'date', 'processName')
            ->where($where)
            ->filter()
            ->get()
            ->toArray();
    }
}
