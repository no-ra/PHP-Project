<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Admin Page</title>
        <link href="<?php echo base_url('public/bootstrap/css/bootstrap.min.css')?>" rel="stylesheet">
        <link href="<?php echo base_url('public/datatables/css/dataTables.bootstrap.css')?>" rel="stylesheet">
        <link rel="stylesheet" href="<?php echo base_url('public/css/style.min.css');?>" />
        <script src="<?php echo base_url('public/jquery/jquery-3.1.0.js')?>"></script>
        <script src="<?php echo base_url('public/bootstrap/js/bootstrap.min.js')?>"></script>
        <script src="<?php echo base_url('public/datatables/js/jquery.dataTables.min.js')?>"></script>
        <script src="<?php echo base_url('public/datatables/js/dataTables.bootstrap.js')?>"></script>
        <script src="<?php echo base_url('public/js/jstree.min.js');?>"></script>
    </head>

<body>

    <div class="container">
        <h1 style="font-size:20pt"></h1>

        <h3>Admin Panel</h3>
        <br />
        <div class="row">
            <div class="col-md-12">
                <button class="btn btn-success" onclick="add_person()"><i class="glyphicon glyphicon-plus"></i> Add User</button>
                <button class="btn btn-success" onclick="add_department()"><i class="glyphicon glyphicon-plus"></i> Add Department</button>
                <button class="btn btn-default" onclick="reload_table()"><i class="glyphicon glyphicon-refresh"></i> Reload</button>
                <form class="pull-right" style="display:inline" action="<?php echo site_url('auth/logout'); ?>" method="GET">
                    <a  class="btn btn-success " role="button"  href="<?php echo site_url('chat/') ?>"><i class="glyphicon glyphicon-envelope"></i> Chat</a>
                    <button class="btn btn-danger" type="submit"><i class="glyphicon glyphicon-log-out"></i> Logout</button>
                </form>
            </div>
        </div>
        <br />
        <br />

        <div class="row">
            <div id="tree-container"></div>
            <br/>
            <!-- <div class="col-md-10"> -->
                <table id="table"  class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Address</th>
                            <th>Role</th>
                            <th>Department</th>
                          <th style="width:125px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            <!-- </div> -->
        </div>
    </div>

<script type="text/javascript">

var save_method; //for save method string
var table;
var dept_id = ''; 
var table = undefined;

$(document).ready(function() {

    //datatables
    create_table();

    $('#tree-container').jstree({
        'plugins': ["wholerow"],
        'core' : {

        'data' : {
            "url" : "<?php echo site_url('admin/getChildren');?>", 
            "plugins" : ["wholerow","dnd","contextmenu"],
            "dataType" : "json" // needed only if you do not supply JSON headers
            }
        }
    });

    $('#tree-container').on("select_node.jstree", function (e, data) {
        console.log(e);
        dept_id = data.node.id;
        create_table();
    });

});

function create_table() {
    if(table != undefined) {
        table.destroy();
    }
    table = $('#table').DataTable({

        "processing": true, //Feature control the processing indicator.
        "serverSide": true, //Feature control DataTables' server-side processing mode.
        "order": [], //Initial no order.
        "ajax": {
            "url": "<?php echo site_url('admin/ajax_list')?>/" + dept_id,
            "type": "POST"
        },
        "columnDefs": [{
            "targets": [ -1 ], //last column
            "orderable": false, //set not orderable
        }],
    });
}

function add_person(){
    save_method = 'add';
    $('#form')[0].reset(); // reset form on modals
    $('.form-group').removeClass('has-error'); // clear error class
    $('.help-block').empty(); // clear error string
    $('#modal_form').modal('show'); // show bootstrap modal
    $('.modal-title').text('Add Person'); // Set Title to Bootstrap modal title
}

function edit_person(id)
{
    save_method = 'update';
    $('#form')[0].reset(); // reset form on modals
    $('.form-group').removeClass('has-error'); // clear error class
    $('.help-block').empty(); // clear error string

    //Ajax Load data from ajax
    $.ajax({
        url : "<?php echo site_url('admin/ajax_edit/')?>/" + id,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {

            $('[name="id"]').val(data.id);
            $('[name="username"]').val(data.username);
            $('[name="first_name"]').val(data.first_name);
            $('[name="last_name"]').val(data.last_name);
            $('[name="password"]').val(data.password);
            $('[name="address"]').val(data.address);
            $('[name="admin"]').val(data.admin);
            $('[name="dept"]').val(data.dept_id);

            $('#modal_form').modal('show'); // show bootstrap modal when complete loaded
            $('.modal-title').text('Edit Person'); // Set title to Bootstrap modal title

        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error get data from ajax');
        }
    });
}

function reload_table(){
    table.ajax.reload(null,false); //reload datatable ajax
}
function reload_tree(){
    $('#tree-container').jstree(true).refresh();
}

function save()
{
    $('#btnSave').text('saving...'); //change button text
    $('#btnSave').attr('disabled',true); //set button disable
    var url;

    if(save_method == 'add') {
        url = "<?php echo site_url('admin/ajax_add')?>";
    } else {
        url = "<?php echo site_url('admin/ajax_update')?>";
    }
	var formData = $('#form').serialize();
    // ajax adding data to database
    $.ajax({
        url : url,
        type: "POST",
        data: formData,
        dataType: "JSON",
        success: function(data)
        {

            if(data.status) //if success close modal and reload ajax table
            {
                $('#modal_form').modal('hide');
                reload_table();
            } else {
                console.log(data);
                for (var i = 0; i < data.inputerror.length; i++){
                    $('[name="'+data.inputerror[i]+'"]').parent().parent().addClass('has-error');
                    $('[name="'+data.inputerror[i]+'"]').next().text(data.error_string[i]);
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

function delete_person(id){
    if(confirm('Are you sure delete this data?'))  {
        $.ajax({
            url : "<?php echo site_url('admin/ajax_delete')?>/"+id,
            type: "POST",
            dataType: "JSON",
            success: function(data)  {
                $('#modal_form').modal('hide');
                reload_table();
            },
            error: function (jqXHR, textStatus, errorThrown)  {
                alert('Error deleting data');
            }
        });
    }
}

function add_department()
{
    save_method = 'add';
    $('#form1')[0].reset(); // reset form on modals
    $('.form-group').removeClass('has-error'); // clear error class
    $('.help-block').empty(); // clear error string
    $('#modal_formDept').modal('show'); // show bootstrap modal
    $('.modal-title').text('Add Department'); // Set Title to Bootstrap modal title
}



function save_dept()
{
    $('#btnSaveDept').text('saving...'); //change button text
    $('#btnSaveDept').attr('disabled',true); //set button disable
    var url;

    if(save_method == 'add') {
        url = "<?php echo site_url('admin/add_dept')?>";
	}
	var formData = $('#form1').serialize();
    // ajax adding data to database
    $.ajax({
        url : url,
        type: "POST",
        data: formData,
        dataType: "JSON",
        success: function(data)
        {

            if(data.status) //if success close modal and reload ajax table
            {
                $('#modal_formDept').modal('hide');
                reload_tree();
            }
            $('#btnSaveDept').text('save'); //change button text
            $('#btnSaveDept').attr('disabled',false); //set button enable

        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error adding / update data');
            $('#btnSaveDept').text('save'); //change button text
            $('#btnSaveDept').attr('disabled',false); //set button enable

        }
    });
}


</script>
<div class="modal fade" id="modal_formDept" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Person Form</h3>
            </div>
            <div class="modal-body form">
                <form action="#" id="form1" class="form-horizontal">
                    <input type="hidden" value="" name="id"/>
                    <div class="form-body">

					 <div class="form-group">
                         <label class="control-label col-md-3">Name of department</label>
                         <div class="col-md-9">
                             <textarea name="name_dept" placeholder="Departments name" class="form-control"></textarea>
                             <span class="help-block"></span>
                         </div>
                     </div>

					 <div id='dept' class="form-group">
                        <label class="control-label col-md-3">Departments</label>
                        <div class="col-md-9">
                            <select name="dept" class="form-control">
                             <?php foreach($departments as $dept){
                                    echo '<option value="' . $dept->id . '">' . $dept->name . '</option>';
                             }?>
                             </select
                             <span class="help-block"></span>
                        </div>
                    </div>
					<div class="modal-footer">
                <button type="button" id="btnSaveDept" onclick="save_dept()" class="btn btn-primary">Save</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>

			  </div>
                </form>
            </div>
         </div>
    </div>
</div>


<!-- Bootstrap modal  -->

<div class="modal fade" id="modal_form" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Person Form</h3>
            </div>
            <div class="modal-body form">
                <form action="#" id="form" class="form-horizontal">
                    <input type="hidden" value="" name="id"/>
                    <div class="form-body">
                      <div class="form-group">
                        <label class="control-label col-md-3">Username</label>
                          <div class="col-md-9">
                              <input  name="username" placeholder="Username" class="form-control" type="text" echo set_value('username');>
                              <span class="help-block"></span>
                          </div>
                      </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">First Name</label>
                            <div class="col-md-9">
                                <input name="first_name" placeholder="First Name" class="form-control" type="text">
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Last Name</label>
                            <div class="col-md-9">
                                <input name="last_name" placeholder="Last Name" class="form-control" type="text">
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Password</label>
                            <div class="col-md-9">
                                <input name="password" placeholder="Password" class="form-control" type="password">
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                           <label class="control-label col-md-3">Role</label>
                           <div class="col-md-9">
                               <select name="admin" class="form-control">
                                   <option value="">--Select Role--</option>
                                   <option value="0">User</option>
                                   <option value="1">Admin</option>
                               </select>
                               <span class="help-block"></span>
                           </div>
                       </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Address</label>
                            <div class="col-md-9">
                                <textarea name="address" placeholder="Address" class="form-control"></textarea>
                                <span class="help-block"></span>
                            </div>
                        </div>

                        <div id='dept' class="form-group">
                           <label class="control-label col-md-3">Departments</label>
                           <div class="col-md-9">
                               <select name="dept" class="form-control">
                                <?php foreach($departments as $row){
                                        echo '<option value="' . $row->id . '">' . $row->name . '</option>';
                                }?>
                                </select
                                <span class="help-block"></span>
                           </div>
                       </div>



                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnSave" onclick="save()" class="btn btn-primary">Save</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- End Bootstrap modal -->
</body>
</html>
