<?php
 defined('BASEPATH') OR exit('No direct script access allowed');


class BranchModel extends CI_Model {
        private $table = 'branches';
        public $id;
        public $name;
        public $address;
        public $phoneNumber;
        public $created_at;
        public $rule = [
            [
                'field' => 'name',
                'label' => 'name',
                'rules' => 'required'
            ],
            [
                'field' => 'address',
                'label' => 'address',
                'rules' => 'required'
            ],
            [
                'field' => 'phoneNumber',
                'label' => 'phoneNumber',
                'rules' => 'required'
            ],
        ];


        public function Rules() {
            return $this->rule;
        }


        public function getAll() {
            $this->db->get('data_mahasiswa')->result();
        }


        public function store($request) {
            $this->name = $request->name;
            $this->address = $request->address;
            $this->phoneNumber = $request->phoneNumber;
            $this->created_at = date('Y-m-d H:i:s');
          
            //$this->password = password_hash($request->password, PASSWORD_BCRYPT);

            if ($this->db->insert($this->table, $this)) {
                return [
                    'msg' => 'Berhasil',
                    'error' => FALSE,
                ];
            }

            return [
                'msg' => 'Gagal',
                'error' => TRUE,
            ];
        }


        public function update($request, $id) {
            $updateData = [
                'address' => $request->address,
                'phoneNumber' => $request->phoneNumber,
                'name' => $request->name
            ];

            if ($this->db->where('id', $id)->update($this->table, $updateData)) {
                return [
                    'msg' => 'Berhasil',
                    'error' => FALSE,
                ];
            }

            return [
                'msg' => 'Gagal',
                'error' => TRUE,
            ];
        }


        public function destroy($id) {
            if (empty($this->db->select('*')->where(array('id' => $id))->get($this->table)->row())) {
                return [
                    'msg' => 'ID Tidak Ditemukan!',
                    'error' => TRUE,
                ];
            }
            
            if ($this->db->delete($this->table, array('id' => $id))) {
                return [
                    'msg' => 'Berhasil',
                    'error' => FALSE,
                ];
            }

            return [
                'msg' => 'Gagal',
                'error' => TRUE,
            ];
        }
    }
?>