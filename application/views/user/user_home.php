    <div class="modal fade" id="modal_form" role="dialog" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title">Person Form</h3>
                </div>
                <div class="modal-body form">
                    <form action="#" id="form" class="form-horizontal">
                        <div class="form-body">
                          <div class="form-group">
                              <label class="control-label col-md-3">Username</label>
                              <div class="col-md-9">
                                  <input disabled name="username" placeholder="Username" class="form-control" type="text"/>
                                  <span class="help-block"></span>
                              </div>
                          </div>
                            <div class="form-group">
                                <label class="control-label col-md-3">First Name</label>
                                <div class="col-md-9">
                                    <input name="first_name" placeholder="First Name" class="form-control" type="text"/>
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3">Last Name</label>
                                <div class="col-md-9">
                                    <input name="last_name" placeholder="Last Name" class="form-control" type="text"/>
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3">Password</label>
                                <div class="col-md-9">
                                    <input name="password" placeholder="Password" class="form-control" type="password"/>
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

                            <div class="form-group">
                                <label class="control-label col-md-3" id="label-photo">Upload Photo </label>
                                <div class="col-md-9">
                                    <input name="photo" type="file" />
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
<script type="text/javascript">

function save()
{
    $('#btnSave').text('saving...'); //change button text
    $('#btnSave').attr('disabled',true); //set button disable
    var url = "<?php echo site_url('user/edit_current_user')?>";
	var formData = new FormData($('#form')[0]);
    $.ajax({
        url : url,
        type: "POST",
        data: formData,
        dataType: "JSON",
        processData: false,
        contentType: false,
        success: function(data)
        {
            if(data.status) //if success close modal and reload ajax table
            {
                $('#modal_form').modal('hide');
                console.log(data);
                reload_profile(data);
            } else {
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

function edit_user()
{
    $('#form')[0].reset(); // reset form on modals
    $('.form-group').removeClass('has-error'); // clear error class
    $('.help-block').empty(); // clear error string

    //Ajax Load data from ajax
    $.ajax({
        url : "<?php echo site_url('user/get_current_user')?>/",
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {
            $('[name="username"]').val(data.username);
            $('[name="first_name"]').val(data.first_name);
            $('[name="last_name"]').val(data.last_name);
            $('[name="password"]').val(data.password);
            $('[name="address"]').val(data.address);

            $('#modal_form').modal('show'); // show bootstrap modal when complete loaded
            $('.modal-title').text('Edit User'); // Set title to Bootstrap modal title
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error get data from ajax');
        }
    });
}

function reload_profile(user) {
    console.log(user);
    var photo_url = "<?php echo base_url('upload'); ?>" + "/" + user.photo;
    $("#emri").text(user.first_name);
    $("#first_name").text(user.first_name);
    $("#last_name").text(user.last_name);
    $("#address").text(user.address);
    $("#profile_photo").attr('src', photo_url);
}

</script>
<br><br>
<div class="container well span6">
	<div class="row-fluid">
        <div class="span2" >
		    <img id = "profile_photo" src="<?php echo base_url('upload') .'/' . $user->photo; ?>" class="img-circle">
        </div>

        <div class="span8">
            <h3><?php echo $user->username;?></span></h3>
            <h5>First Name: <span id = "first_name"><?php echo $user->first_name; ?></span></h5>
            <h5>Last Name: <span id = "last_name"> <?php echo $user->last_name; ?></span></h5>
            <h5>Address: <span id = "address"><?php echo $user->address; ?></span></h5>
            <h5>Department: <span id = "address"><?php echo $dept; ?></span></h5>
        </div>

        <div class="span2">
            <div class="row">
                <div class="col-md-12">
                    <a  class="btn btn-success " role="button"  href="<?php echo site_url('chat/') ?>"><i class="glyphicon glyphicon-envelope"></i> Chat</a>
                    <button class="btn btn-primary" href="javascript:void(0)" title="Edit" onclick="edit_user()"><i class="glyphicon glyphicon-pencil"></i> Edit</button>
                    <form action="<?php echo site_url('auth/logout'); ?>" method="GET" style="display: inline" class="pull-right">
                        <button class="btn btn-danger" type="submit"><i class="glyphicon glyphicon-log-out"></i> Logout</button>
                    </form>
                </div>
            </div>
        </div>
</div>
</div>
