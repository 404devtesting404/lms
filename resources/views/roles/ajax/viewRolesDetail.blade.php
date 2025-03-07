<div class="well">
<div class="panel" style="height: auto;">
    <div class="panel-body">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            @foreach($menu as $menuVal)
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            <label>
                                {{$menuVal->title_id}} &nbsp;
                                @if(in_array($menuVal->id, $main_modules))
                                    <i class="icon-checkmark" style="color:green"></i>

                                @else
                                    <i class="icon-checkmark" style="color:grey"></i>

                                @endif

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

                                                @if(in_array($sbVal->id, $submenu_id))
                                                    <i class="icon-checkmark" style="color:green"></i>
                                                @else
                                                    <i class="icon-checkmark" style="color:grey"></i>
                                                @endif
                                                &nbsp;
                                                <strong> {{$sbVal->name}}</strong>&nbsp;&nbsp;
                                            </label>

                                        </li>
                                    </ul>
                                </div>

                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                    <ul class="privilegesList nav">
                                        <li class="pagesList">
                                            @if(in_array($sbVal->m_controller_name.'_view', $crud))
                                                <i class="icon-checkmark" style="color:green"></i>
                                            @else
                                                <i class="icon-checkmark" style="color:grey"></i>
                                            @endif
                                            <strong>View</strong>&nbsp;&nbsp;
                                        </li>
                                        <li class="pagesList">
                                            @if(in_array($sbVal->m_controller_name.'_edit', $crud))
                                                <i class="icon-checkmark" style="color:green"></i>
                                            @else
                                                <i class="icon-checkmark" style="color:grey"></i>
                                            @endif
                                            <strong>Edit</strong>&nbsp;&nbsp;
                                        </li>
                                        <li class="pagesList">

                                            @if(in_array($sbVal->m_controller_name.'_approve', $crud))
                                                <i class="icon-checkmark" style="color:green"></i>
                                            @else
                                                <i class="icon-checkmark" style="color:grey"></i>
                                            @endif  <strong>Approve</strong>&nbsp;&nbsp;
                                        </li>
                                        <li class="pagesList">

                                            @if(in_array($sbVal->m_controller_name.'_reject', $crud))
                                                <i class="icon-checkmark" style="color:green"></i>
                                            @else
                                                <i class="icon-checkmark" style="color:grey"></i>
                                            @endif <strong>Reject</strong>&nbsp;&nbsp;
                                        </li>
                                        <li class="pagesList">

                                            @if(in_array($sbVal->m_controller_name.'_repost', $crud))
                                                <i class="icon-checkmark" style="color:green"></i>
                                            @else
                                                <i class="icon-checkmark" style="color:grey"></i>
                                            @endif<strong>Repost</strong>&nbsp;&nbsp;
                                        </li>
                                        <li class="pagesList">

                                            @if(in_array($sbVal->m_controller_name.'_delete', $crud))
                                                <i class="icon-checkmark" style="color:green"></i>
                                            @else
                                                <i class="icon-checkmark" style="color:grey"></i>
                                            @endif <strong>Delete</strong>&nbsp;&nbsp;
                                        </li>
                                        <li class="pagesList">

                                            @if(in_array($sbVal->m_controller_name.'_print', $crud))
                                                <i class="icon-checkmark" style="color:green"></i>
                                            @else
                                                <i class="icon-checkmark" style="color:grey"></i>
                                            @endif <strong>Print</strong>&nbsp;&nbsp;
                                        </li>
                                        <li class="pagesList">

                                            @if(in_array($sbVal->m_controller_name.'_export', $crud))
                                                <i class="icon-checkmark" style="color:green"></i>
                                            @else
                                                <i class="icon-checkmark" style="color:grey"></i>
                                            @endif <strong>Export</strong>&nbsp;&nbsp;
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
    </div>

</div>
</div>