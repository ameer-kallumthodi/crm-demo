<?php
namespace App\Controllers\App;
use App\Models\Teams_model;
class Team_report extends AppBaseController
{
    private $teams_model;
    public function __construct()
    {
        parent::__construct();
        $this->teams_model = new Teams_model();
      

    }

    public function index(){
        $this->data['team_list'] = $this->teams_model->get()->getResultArray();
        $this->data['page_title'] = 'Team Report';
        $this->data['page_name'] = 'Team_report/index';
        return view('App/index', $this->data);
    }


}
