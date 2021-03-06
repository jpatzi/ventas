<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Adulto extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('adulto_model','adulto');
	}

	public function index()
	{
		$this->load->helper('url');
		$datos['estadocivil']=$this->adulto->estadocivil_get();
		$datos['parentesco']=$this->adulto->parentesco_get();
		$datos['departamento']=$this->adulto->departamento_get();
		$datos['distrito']=$this->adulto->distrito_get();
		$this->load->view('adulto_view',$datos);
	}

	public function ajax_list()
	{
		$list = $this->adulto->get_datatables();
		$data = array();
		$no = $_POST['start'];
		foreach ($list as $adulto) {
			$no++;
			$row = array();
			$row[] = $adulto->adu_asignacion;
			$row[] = $adulto->adu_carnet;
			$row[] = $adulto->adu_nombres;
			$row[] = $adulto->adu_paterno;
			$row[] = $adulto->adu_materno;
			$row[] = $adulto->adu_estado;

			//add html for action
			$row[] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit" onclick="edit_person('."'".$adulto->adu_codigo."'".')"><i class="glyphicon glyphicon-pencil"></i> Edit</a>
				  <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Hapus" onclick="delete_person('."'".$adulto->adu_codigo."'".')"><i class="glyphicon glyphicon-trash"></i> Delete</a>';
		
			$data[] = $row;
		}

		$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $this->adulto->count_all(),
						"recordsFiltered" => $this->adulto->count_filtered(),
						"data" => $data,
				);
		//output to json format
		echo json_encode($output);
	}

	public function ajax_edit($id)
	{
		$data = $this->person->get_by_id($id);
		$data->dob = ($data->dob == '0000-00-00') ? '' : $data->dob; // if 0000-00-00 set tu empty for datepicker compatibility
		echo json_encode($data);
	}

	public function ajax_add()
	{
		//$this->_validate();
		$data = array(
				'adu_carnet' => $this->input->post('carnet'),
				'' => $this->input->post('departamento'),
				'' => $this->input->post('nombres'),
				'' => $this->input->post('paterno'),
				'' => $this->input->post('materno'),
				'' => $this->input->post('casado'),
				'' => $this->input->post('estadocivil'),
				'' => $this->input->post('edad'),
				'' => $this->input->post('sexo'),
				'' => $this->input->post('resnombres'),
				'' => $this->input->post('respaterno'),
				'' => $this->input->post('resmaterno'),
				'' => $this->input->post('resparentesco'),
				'' => $this->input->post('telefono1'),
				'' => $this->input->post('telefono2'),
				'' => $this->input->post('telefono3'),
				'' => $this->input->post('telefono4'),
				'' => $this->input->post('telefono5'),
		
			);
		$insert = $this->adulto->save($data);
		echo json_encode(array("status" => TRUE));
	}

	public function ajax_update()
	{
		$this->_validate();
		$data = array(
				'firstname' => $this->input->post('firstname'),
				'lastname' => $this->input->post('lastname'),
				'gender' => $this->input->post('gender'),
				'address' => $this->input->post('address'),
				'dob' => $this->input->post('dob'),
			);
		$this->person->update(array('id' => $this->input->post('id')), $data);
		echo json_encode(array("status" => TRUE));
	}

	public function ajax_delete($id)
	{
		$this->person->delete_by_id($id);
		echo json_encode(array("status" => TRUE));
	}


	private function _validate()
	{
		$data = array();
		$data['error_string'] = array();
		$data['inputerror'] = array();
		$data['status'] = TRUE;

		if($this->input->post('firstname') == '')
		{
			$data['inputerror'][] = 'firstname';
			$data['error_string'][] = 'Primer nombre es requerido';
			$data['status'] = FALSE;
		}

		if($this->input->post('lastname') == '')
		{
			$data['inputerror'][] = 'lastname';
			$data['error_string'][] = 'Paterno es requerido';
			$data['status'] = FALSE;
		}

		if($this->input->post('dob') == '')
		{
			$data['inputerror'][] = 'dob';
			$data['error_string'][] = 'Date of Birth is required';
			$data['status'] = FALSE;
		}

		if($this->input->post('gender') == '')
		{
			$data['inputerror'][] = 'gender';
			$data['error_string'][] = 'Please select gender';
			$data['status'] = FALSE;
		}

		if($this->input->post('address') == '')
		{
			$data['inputerror'][] = 'address';
			$data['error_string'][] = 'Addess is required';
			$data['status'] = FALSE;
		}

		if($data['status'] === FALSE)
		{
			echo json_encode($data);
			exit();
		}
	}

}
