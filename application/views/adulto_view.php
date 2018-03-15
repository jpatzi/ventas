<!DOCTYPE html>
<html>
    <head> 
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ajax CRUD with Bootstrap modals and Datatables</title>
    <link href="<?php echo base_url('assets/bootstrap/css/bootstrap.min.css')?>" rel="stylesheet">
    <link href="<?php echo base_url('assets/datatables/css/dataTables.bootstrap.css')?>" rel="stylesheet">
    <link href="<?php echo base_url('assets/bootstrap-datepicker/css/bootstrap-datepicker3.min.css')?>" rel="stylesheet">
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    </head> 
<body>
    <div class="container">
        <h1 style="font-size:20pt">REGISTOR DEL ADULTO MAYOR</h1>

        <h3>---</h3>
        <br />
        <button class="btn btn-success" onclick="add_person()"><i class="glyphicon glyphicon-plus"></i> Adicionar</button>
        <button class="btn btn-default" onclick="reload_table()"><i class="glyphicon glyphicon-refresh"></i> Actualizar</button>
        <br />
        <br />
        <table id="table" class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>Codigo</th>
                    <th>Carnet</th>
                    <th>Nombres</th>
                    <th>Ap. Paterno</th>
                    <th>Ap. Materno</th>
                    <th>Estado</th>
                    <th style="width:125px;">Action</th>
                </tr>
            </thead>
            <tbody>
            </tbody>

            <tfoot>
            <tr>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Gender</th>
                <th>Address</th>
                <th>Date of Birth</th>
                <th>Action</th>
            </tr>
            </tfoot>
        </table>
    </div>

<script src="<?php echo base_url('assets/jquery/jquery-2.1.4.min.js')?>"></script>
<script src="<?php echo base_url('assets/bootstrap/js/bootstrap.min.js')?>"></script>
<script src="<?php echo base_url('assets/datatables/js/jquery.dataTables.min.js')?>"></script>
<script src="<?php echo base_url('assets/datatables/js/dataTables.bootstrap.js')?>"></script>
<script src="<?php echo base_url('assets/bootstrap-datepicker/js/bootstrap-datepicker.min.js')?>"></script>


<script type="text/javascript">

var save_method; //for save method string
var table;

$(document).ready(function() {

    //datatables
    table = $('#table').DataTable({ 

        "processing": true, //Feature control the processing indicator.
        "serverSide": true, //Feature control DataTables' server-side processing mode.
        "order": [], //Initial no order.

        // Load data for the table's content from an Ajax source
        "ajax": {
            "url": "<?php echo site_url('adulto/ajax_list')?>",
            "type": "POST"
        },

        //Set column definition initialisation properties.
        "columnDefs": [
        { 
            "targets": [ -1 ], //last column
            "orderable": false, //set not orderable
        },
        ],

    });

    //datepicker
    $('.datepicker').datepicker({
        autoclose: true,
        format: "yyyy-mm-dd",
        todayHighlight: true,
        orientation: "top auto",
        todayBtn: true,
        todayHighlight: true,  
    });

    //set input/textarea/select event when change value, remove class error and remove text help block 
    $("input").change(function(){
        $(this).parent().parent().removeClass('has-error');
        $(this).next().empty();
    });
    $("textarea").change(function(){
        $(this).parent().parent().removeClass('has-error');
        $(this).next().empty();
    });
    $("select").change(function(){
        $(this).parent().parent().removeClass('has-error');
        $(this).next().empty();
    });

});



function add_person()
{
    save_method = 'add';
    $('#form')[0].reset(); // reset form on modals
    $('.form-group').removeClass('has-error'); // clear error class
    $('.help-block').empty(); // clear error string
    $('#modal_form').modal('show'); // show bootstrap modal
    $('.modal-title').text('Adicionar'); // Set Title to Bootstrap modal title
}

function edit_person(id)
{
    save_method = 'update';
    $('#form')[0].reset(); // reset form on modals
    $('.form-group').removeClass('has-error'); // clear error class
    $('.help-block').empty(); // clear error string

    //Ajax Load data from ajax
    $.ajax({
        url : "<?php echo site_url('adulto/ajax_edit/')?>/" + id,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {

            $('[name="id"]').val(data.id);
            $('[name="firstname"]').val(data.firstname);
            $('[name="lastname"]').val(data.lastname);
            $('[name="gender"]').val(data.gender);
            $('[name="address"]').val(data.address);
            $('[name="dob"]').datepicker('update',data.dob);
            $('#modal_form').modal('show'); // show bootstrap modal when complete loaded
            $('.modal-title').text('Edit Person'); // Set title to Bootstrap modal title

        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error get data from ajax');
        }
    });
}

function reload_table()
{
    table.ajax.reload(null,false); //reload datatable ajax 
}

function save()
{
    $('#btnSave').text('saving...'); //change button text
    $('#btnSave').attr('disabled',true); //set button disable 
    var url;

    if(save_method == 'add') {
        url = "<?php echo site_url('adulto/ajax_add')?>";
    } else {
        url = "<?php echo site_url('adulto/ajax_update')?>";
    }

    // ajax adding data to database
    $.ajax({
        url : url,
        type: "POST",
        data: $('#form').serialize(),
        dataType: "JSON",
        success: function(data)
        {

            if(data.status) //if success close modal and reload ajax table
            {
                $('#modal_form').modal('hide');
                reload_table();
            }
            else
            {
                for (var i = 0; i < data.inputerror.length; i++) 
                {
                    $('[name="'+data.inputerror[i]+'"]').parent().parent().addClass('has-error'); //select parent twice to select div form-group class and add has-error class
                    $('[name="'+data.inputerror[i]+'"]').next().text(data.error_string[i]); //select span help-block class set text error string
                }
            }
            $('#btnSave').text('save'); //change button text
            $('#btnSave').attr('disabled',false); //set button enable 


        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error adding / update data');
            $('#btnSave').text('save'); //change button text
            $('#btnSave').attr('disabled',false); //set button enable 

        }
    });
}

function delete_person(id)
{
    if(confirm('Are you sure delete this data?'))
    {
        // ajax delete data to database
        $.ajax({
            url : "<?php echo site_url('person/ajax_delete')?>/"+id,
            type: "POST",
            dataType: "JSON",
            success: function(data)
            {
                //if success reload ajax table
                $('#modal_form').modal('hide');
                reload_table();
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error deleting data');
            }
        });

    }
}

</script>

<!-- Bootstrap modal -->
<div class="modal fade" id="modal_form" role="dialog">
    <div class="modal-dialog" style="width:1000px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Formulario Adulto Mayor</h3>
            </div>
            <div class="modal-body form">
                <form action="#" id="form" class="form-horizontal">
                    <input type="hidden" value="" name="id"/> 
                    <div class="form-body">
    <div class="panel panel-default">
     <div class="panel-body">
         <legend> <h5><strong>DATOS DEL ADULTO MAYOR</strong></h5> </legend>  
          <div class="row">                       
              <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label col-md-4">Carnet</label>
                            <div class="col-md-5">
                                <input name="carnet" placeholder="Carnet" class="form-control" type="text">
                            </div>
                            <div class="col-md-3">    
                                <select name="departamento" class="form-control">
                                    <option value="">Selec.</option>
                                    <?php foreach ($departamento as $dep): ?>
                    					<option value="<?php echo $dep->dep_codigo; ?>"><?php echo $dep->dep_descripcion; ?></option>
                  					<?php endforeach; ?>
                                </select>
                                
                                <span class="help-block"></span>
                            </div>
                        </div>
               </div>
            </div>
                        
          <div class="row">
          		<div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label col-md-4">Nombres</label>
                            <div class="col-md-8">
                                <input name="nombres" placeholder="Ingrese Nombres" class="form-control" type="text">
                                <span class="help-block"></span>
                            </div>
                        </div>
                  </div>
                 
                 <div class="col-md-6">       
                        <div class="form-group">
                            <label class="control-label col-md-4">Apellido Paterno</label>
                            <div class="col-md-8">
                                <input name="paterno" placeholder="Apellido Paterno" class="form-control" type="text">
                                <span class="help-block"></span>
                            </div>
                        </div>
                 </div>
           </div>
           <div class="row">
                 <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label col-md-4">Apellido Materno</label>
                            <div class="col-md-8">
                                <input name="materno" placeholder="Apellido Materno" class="form-control" type="text">
                                <span class="help-block"></span>
                            </div>
                        </div>
                 </div>
                 
                 <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label col-md-4">Apellido Casado</label>
                            <div class="col-md-8">
                                <input name="casado" placeholder="Apellido de Casado" class="form-control" type="text">
                                <span class="help-block"></span>
                            </div>
                        </div>
                  </div>
            </div>
          <div class="row">
                     <div class="col-md-6">   
                        <div class="form-group">
                            <label class="control-label col-md-4">Estado Civil</label>
                            <div class="col-md-8">
                                <select name="estadocivil" class="form-control">
                                    <option value="">--Seleccionar--</option>
                                    <?php foreach ($estadocivil as $estado): ?>
                    					<option value="<?php echo $estado->est_codigo; ?>"><?php echo $estado->est_descripcion; ?></option>
                  					<?php endforeach; ?>
                                </select>
                                <span class="help-block"></span>
                            </div>
                        </div>
                     </div>
                    
                    <div class="col-md-6">    
                        <div class="form-group">
                            <label class="control-label col-md-4">Edad(Años)</label>
                            <div class="col-md-8">
                                <input name="edad" placeholder="Edad" class="form-control" type="text">
                                <span class="help-block"></span>
                            </div>
                        </div>
                     </div>
          </div>
          
          <div class="row">
                 <div class="col-md-4">  
                       <div class="form-group">
                                  <label class="control-label col-md-4">Distrito</label>
                                      <div class="col-md-8">
                                         <select name="distrito" class="form-control">
                                    <option value="">--Seleccionar--</option>
                                    <?php foreach ($distrito as $dis): ?>
                    					<option value="<?php echo $dis->dis_codigo; ?>"><?php echo $dis->dis_descripcion; ?></option>
                  					<?php endforeach; ?>
                                </select>
                                          <span class="help-block"></span>
                                      </div>
                         </div>
                 </div>
                 
                 <div class="col-md-4">  
                 <div class="form-group">
                            <label class="control-label col-md-4">Zona</label>
                            <div class="col-md-8">
                                <select name="zona" class="form-control">
                                    <option value="">--Seleccionar--</option>
                                    <?php foreach ($estadocivil as $estado): ?>
                    					<option value="<?php echo $estado->est_codigo; ?>"><?php echo $estado->est_descripcion; ?></option>
                  					<?php endforeach; ?>
                                </select>
                                <span class="help-block"></span>
                            </div>
                        </div>
                 </div>
                 
                 <div class="col-md-4">  
                 <div class="form-group">
                            <label class="control-label col-md-4">Direccion</label>
                            <div class="col-md-8">
                                <input name="direccion" placeholder="Calle/Avenida Nro" class="form-control" type="text">
                                <span class="help-block"></span>
                            </div>
                        </div>
                 </div>
                    
         </div>
          
          <div class="row">
                 <div class="col-md-6">  
                       <div class="form-group">
                                  <label class="control-label col-md-4">Sexo</label>
                                      <div class="col-md-8">
                                          <input type="radio" name="sexo" value="MASCULINO">Masculino&nbsp;&nbsp;
                                          <input type="radio" name="sexo" value="FEMENINO">Femenino&nbsp;&nbsp;&nbsp;
                                          <span class="help-block"></span>
                                      </div>
                         </div>
                 </div>
                 
                 <div class="col-md-6">  
                 <div class="form-group">
                            <label class="control-label col-md-4">Tipo de Sangre</label>
                            <div class="col-md-8">
                                <input name="tiposangre" placeholder="Tipo de Sangre" class="form-control" type="text">
                                <span class="help-block"></span>
                            </div>
                        </div>
                 </div>
                    
         </div>  
         
        <div class="row">
              <div class="col-md-6">  
                 <div class="form-group">
                            <label class="control-label col-md-4">Carga Foto</label>
                            <div class="col-md-8">
                                <input type="file" name="foto">
                                <span class="help-block"></span>
                            </div>
                        </div>
                 </div>
        </div>    
         
     </div> <!--panel default-->
      </div>  <!--panel body 		-->	 
 <div class="panel panel-default">
  <div class="panel-body">
         <legend> <h5><strong>DATOS DEL RESPONZABLE</strong></h5> </legend>  
         <div class="row">
          		<div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label col-md-4">Nombres</label>
                            <div class="col-md-8">
                                <input name="resnombres" placeholder="Ingrese Nombres" class="form-control" type="text">
                                <span class="help-block"></span>
                            </div>
                        </div>
                  </div>
                 
                 <div class="col-md-6">       
                        <div class="form-group">
                            <label class="control-label col-md-4">Apellido Paterno</label>
                            <div class="col-md-8">
                                <input name="respaterno" placeholder="Apellido Paterno" class="form-control" type="text">
                                <span class="help-block"></span>
                            </div>
                        </div>
                 </div>
           </div>
           <div class="row">
                 <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label col-md-4">Apellido Materno</label>
                            <div class="col-md-8">
                                <input name="resmaterno" placeholder="Apellido Materno" class="form-control" type="text">
                                <span class="help-block"></span>
                            </div>
                        </div>
                 </div>
                 
                 <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label col-md-4">Parentesco</label>
                                      <div class="col-md-8">
                                          <select name="resparentesco" class="form-control">
                                    <option value="">--Seleccionar--</option>
                                    <?php foreach ($parentesco as $paren): ?>
                    					<option value="<?php echo $paren->par_codigo; ?>"><?php echo $paren->par_descripcion; ?></option>
                  					<?php endforeach; ?>
                                </select>
                                          <span class="help-block"></span>
                                      </div>
                        </div>
                  </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                 <div class="form-group">
                            <label class="control-label col-md-2">Telefonos</label>
                            <div class="col-md-2">
                                <input name="telefono1" placeholder="telefono 1" class="form-control" type="text">
                                <span class="help-block"></span>
                            </div>
                            <div class="col-md-2">
                                <input name="telefono2" placeholder="telefono 2" class="form-control" type="text">
                                <span class="help-block"></span>
                            </div>
                            <div class="col-md-2">
                                <input name="telefono3" placeholder="telefono 3" class="form-control" type="text">
                                <span class="help-block"></span>
                            </div>
                            <div class="col-md-2">
                                <input name="telefono4" placeholder="telefono 4" class="form-control" type="text">
                                <span class="help-block"></span>
                            </div>
                            <div class="col-md-2">
                                <input name="telefono5" placeholder="telefono 5" class="form-control" type="text">
                                <span class="help-block"></span>
                            </div>
                  </div>
              </div>
            </div>
  </div> <!--panel default-->
    </div>  <!--panel body 		-->	                            
                             
                         
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnSave" onclick="save()" class="btn btn-primary">Guardar</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- End Bootstrap modal -->
</body>
</html>