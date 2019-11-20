<?php

    use Restserver\Libraries\REST_Controller;

    class Branch extends REST_Controller {
        public function __construct() {
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: GET, OPTIONS, POST, DELETE');
            header("Access-Control-Allow-Headers: Authorization, Content-Type, Content-Length, Accept-Encoding");
            parent::__construct();
            $this->load->model('BranchModel');
            $this->load->library('form_validation');
            $this->load->helper(['jwt', 'Authorization']);
 
        }

        
        public function index_get() {
            
            $data = $this->verify_request();
            // Send the return data as reponse
            $status = parent::HTTP_OK;
            $response = ['status' => $status, 'data' => $data];
            $this->response($response, $status);
            return $this->returnData($this->db->get('branches')->result(), false);
        }


        public function index_post($id = null) {
            $validation = $this->form_validation;
            $rule = $this->BranchModel->rules();

            if ($id == null) {
                array_push($rule, [
                    'field' => 'name',
                    'label' => 'name',
                    'rules' => 'required'
                ],
                [
                    'field' => 'phoneNumber',
                    'label' => 'phoneNumber',
                    'rules' => 'required|is_unique[branches.phoneNumber]|numeric'    
                ]);
            } else {
                array_push($rule, [
                    'field' => 'phoneNumber',
                    'label' => 'phoneNumber',
                    'rules' => 'required|is_unique[branches.phoneNumber]|numeric'
                ]);
            }

            $validation->set_rules($rule);

            if (!$validation->run()) 
                return $this->returnData($this->form_validation->error_array(), true);
            
            $branch = new branchData();
            $branch->name = $this->post('name');
            $branch->address = $this->post('address');
            $branch->phoneNumber = $this->post('phoneNumber');
            date_default_timezone_set('Asia/Jakarta');
            $now = date('Y-m-d H:i:s');
            $branch->created_at = $now;

            if ($id == null) 
                $response = $this->BranchModel->store($branch);
            else 
                $response = $this->BranchModel->update($branch, $id);

            return $this->returnData($response['msg'], $response['error']);
        }


        public function index_delete($id = null) {
            if ($id == null)
                return $this->returnData('Parameter ID Tidak Ditemukan', true);

            $response = $this->BranchModel->destroy($id);
            return $this->returnData($response['msg'], $response['error']);
        }

        public function returnData($msg, $error) {
            $response['error'] = $error;
            $response['message'] = $msg;

            return $this->response($response);
        }
        private function verify_request()
        {
            // Get all the headers
            $headers = $this->input->request_headers();
            // Extract the token
            $token = $headers['Authorization'];
            // Use try-catch
            // JWT library throws exception if the token is not valid
            try {
                // Validate the token
                // Successfull validation will return the decoded user data else returns false
                $data = AUTHORIZATION::validateToken($token);
                if ($data === false) {
                    $status = parent::HTTP_UNAUTHORIZED;
                    $response = ['status' => $status, 'msg' => 'Unauthorized Access!'];
                    $this->response($response, $status);
                    exit();
                } else {
                    return $data;
                }
            } catch (Exception $e) {
                // Token is invalid
                // Send the unathorized access message
                $status = parent::HTTP_UNAUTHORIZED;
                $response = ['status' => $status, 'msg' => 'Unauthorized Access! '];
                $this->response($response, $status);
            }
        }
     }


    class branchData {
        public $name;
        public $address;
        public $phoneNumber;
        public $created_at;
    }

?>