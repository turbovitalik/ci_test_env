<?php

class Index extends MY_Controller
{
    public function main()
    {
        $this->load->view('base/header');
        $this->load->view('base/footer');
    }
}