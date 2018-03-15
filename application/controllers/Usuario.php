<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Usuario extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('usuario_model','usuario');
	}

	public function index()
	{
		$this->load->helper('url');
		$this->load->view('usuario_view');
	}

	public function ajax_list()
	{
		$list = $this->usuario->get_datatables();
		$data = array();
		$no = $_POST['start'];
		foreach ($list as $usuario) {
			$no++;
			$row = array();
			$row[] = $usuario->usu_ci;
			$row[] = $usuario->usu_nombre;
			$row[] = $usuario->usu_paterno;
			$row[] = $usuario->usu_materno;
			$row[] = $usuario->usu_celular;
			$row[] = $usuario->niv_codigo;
			$row[] = $usuario->usu_estado;
			

			//add html for action
			$row[] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Editar" onclick="edit_usuario('."'".$usuario->usu_codigo."'".')"><i class="glyphicon glyphicon-pencil"></i> Editar</a>';
			//      <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Hapus" onclick="delete_usuario('."'".$usuario->usu_codigo."'".')"><i class="glyphicon glyphicon-trash"></i> Eliminar</a>
		
			$data[] = $row;
		}

		$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $this->usuario->count_all(),
						"recordsFiltered" => $this->usuario->count_filtered(),
						"data" => $data,
				);
		//output to json format
		echo json_encode($output);
	}

	public function ajax_edit($id)
	{
		$data = $this->usuario->get_by_id($id);
		//$data->dob = ($data->dob == '0000-00-00') ? '' : $data->dob; // if 0000-00-00 set tu empty for datepicker compatibility
		echo json_encode($data);
	}

	public function ajax_add()
	{
		$this->_validate();
		$data = array(
				'usu_ci' => $this->input->post('carnet'),
				'usu_nombre' => $this->input->post('nombres'),
				'usu_paterno' => $this->input->post('paterno'),
				'usu_materno' => $this->input->post('materno'),
				'usu_celular' => $this->input->post('celular'),
				'niv_codigo' => $this->input->post('nivel'),
				'usu_estado' => $this->input->post('estado'),
				'usu_usuario' => $this->input->post('nombres')
			);
		$insert = $this->usuario->save($data);
		echo json_encode(array("status" => TRUE));
	}

	public function ajax_update()
	{
		$this->_validate();
		$data = array(
				'usu_ci' => $this->input->post('carnet'),
				'usu_nombre' => $this->input->post('nombres'),
				'usu_paterno' => $this->input->post('paterno'),
				'usu_materno' => $this->input->post('materno'),
				'usu_celular' => $this->input->post('celular'),
				'niv_codigo' => $this->input->post('nivel'),
				'usu_estado' => $this->input->post('estado'),
				'usu_usuario' => $this->input->post('nombres')
			);
		$this->usuario->update(array('usu_codigo' => $this->input->post('id')), $data);
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

		if($this->input->post('carnet') == '')
		{
			$data['inputerror'][] = 'carnet';
			$data['error_string'][] = 'Carnet es requerido';
			$data['status'] = FALSE;
		}

		if($this->input->post('nombres') == '')
		{
			$data['inputerror'][] = 'nombres';
			$data['error_string'][] = 'Nombres es requerido';
			$data['status'] = FALSE;
		}

		if($this->input->post('paterno') == '')
		{
			$data['inputerror'][] = 'paterno';
			$data['error_string'][] = 'Apellido Paterno es requerido';
			$data['status'] = FALSE;
		}

		/*if($this->input->post('celular') == '')
		{
			$data['inputerror'][] = 'celular';
			$data['error_string'][] = 'Celular es ';
			$data['status'] = FALSE;
		}*/

		if($this->input->post('estado') == '')
		{
			$data['inputerror'][] = 'estado';
			$data['error_string'][] = 'Estado es requerido';
			$data['status'] = FALSE;
		}
		
		if($this->input->post('nivel') == '')
		{
			$data['inputerror'][] = 'nivel';
			$data['error_string'][] = 'Nivel es requerido';
			$data['status'] = FALSE;
		}

		if($data['status'] === FALSE)
		{
			echo json_encode($data);
			exit();
		}
	}

}
