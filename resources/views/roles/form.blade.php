
    <div class="box box-info padding-1">
        <div class="box-body">

            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <label class="sf-label pointer">Role Name</label>
                    <input type="text" name="role_name" required id="role_name" class="form-control requiredField">
                </div>

            </div>
            <br>
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    @foreach($menu as $menuVal)
                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                <h3 class="panel-title">
                                    <label>
                                        {{$menuVal->title_id}} &nbsp;
                                        <input class="radio1c" onclick="checkPages('<?=$menuVal->id?>')" id="main_{{$menuVal->id}}" type="checkbox" name="main_modules[]" value=" {{$menuVal->id}}">
                                    </label>
                                </h3>
                            </div>
                            <div class="panel-body">
                                <div class="row">

                                    @foreach($menuVal->submenu as $sbVal)
                                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                            <ul>
                                                <li>
                                                    <label>
                                                        <input onclick="checkinnerpages('<?=$menuVal->id?>')" name="submenu_id[]" class="radio1c ck_<?=$menuVal->id?>" type="checkbox" value="{{$sbVal->id}}">
                                                        &nbsp;
                                                        <strong> {{$sbVal->name}}</strong>&nbsp;&nbsp;
                                                    </label>

                                                </li>
                                            </ul>
                                        </div>

                                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                            <ul class="privilegesList nav">
                                                <li class="pagesList">
                                                    <input name="crud[]" class="radio1c ck_<?=$menuVal->id?>" type="checkbox" value="{{$sbVal->m_controller_name}}_view" />
                                                    <strong>View</strong>&nbsp;&nbsp;
                                                </li>
                                                <li class="pagesList">
                                                    <input name="crud[]" class="radio1c ck_<?=$menuVal->id?>" type="checkbox" value="{{$sbVal->m_controller_name}}_edit" />
                                                    <strong>Edit</strong>&nbsp;&nbsp;
                                                </li>
                                                <li class="pagesList">
                                                    <input name="crud[]" class="radio1c ck_<?=$menuVal->id?>" type="checkbox" value="{{$sbVal->m_controller_name}}_approve" />
                                                    <strong>Approve</strong>&nbsp;&nbsp;
                                                </li>
                                                <li class="pagesList">
                                                    <input name="crud[]" class="radio1c ck_<?=$menuVal->id?>" type="checkbox" value="{{$sbVal->m_controller_name}}_reject" />
                                                    <strong>Reject</strong>&nbsp;&nbsp;
                                                </li>
                                                <li class="pagesList">
                                                    <input name="crud[]" class="radio1c ck_<?=$menuVal->id?>" type="checkbox" value="{{$sbVal->m_controller_name}}_repost" />
                                                    <strong>Repost</strong>&nbsp;&nbsp;
                                                </li>
                                                <li class="pagesList">
                                                    <input name="crud[]" class="radio1c ck_<?=$menuVal->id?>" type="checkbox" value="{{$sbVal->m_controller_name}}_delete" />
                                                    <strong>Delete</strong>&nbsp;&nbsp;
                                                </li>
                                                <li class="pagesList">
                                                    <input name="crud[]" class="radio1c ck_<?=$menuVal->id?>" type="checkbox" value="{{$sbVal->m_controller_name}}_print" />
                                                    <strong>Print</strong>&nbsp;&nbsp;
                                                </li>
                                                <li class="pagesList">
                                                    <input name="crud[]" class="radio1c ck_<?=$menuVal->id?>" type="checkbox" value="{{$sbVal->m_controller_name}}_export" />
                                                    <strong>Export</strong>&nbsp;&nbsp;
                                                </li>
                                            </ul>
                                        </div>
                                    @endforeach
                                </div>
                                <br>
                            </div>
                        </div>
                    @endforeach
                </div>
                <br>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right">
                    <button type="submit" class="btn btn-sm btn-success">Submit</button>
                </div>
            </div>
        </div>
    </div>


