<div class="box box-info padding-1">
    <div class="box-body">
        <div class="page-wrapper">


            <!-- ============================================================== -->
            <!-- Start Page Content -->
            <!-- ============================================================== -->

            <input type="hidden" name="_token" value="{{ csrf_token() }}"><div class="row">

                <div class="col-12">
                    <div class="card">
                        <div class="card-body">


                            <?php
                            echo Form::open(array('url' => 'uad/addMainMenuTitleDetail','id'=>'addMainMenuTitleForm'));
                            ?>
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <div class="row">
                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                    <label>Main Navigation Name</label>
                                    <input type="text" name="main_menu_name" id="main_menu_name" value="" required class="form-control" />
                                </div>
                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                    <label>Sub Navigation Title Name</label>
                                    <input type="text" name="title_name" id="title_name" value="" required class="form-control" />
                                </div>
                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                    <label>Icon</label>
                                    <input type="text" name="icon" id="icon" value="" required class="form-control" />
                                </div>
                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                    <label>Menu Type</label>
                                    <select type="text" name="menu_type" id="menu_type" required class="form-control" >
                                    <option value="1">Company</option>
                                    <option value="2">Master</option>
                                    </select>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right">
                                    {{ Form::submit('Submit', ['class' => 'btn btn-sm btn-success']) }}
                                    <button type="reset" id="reset" class="btn btn-sm btn-primary">Clear Form</button>

                                    <?php
                                    //echo Form::submit('Click Me!');
                                    ?>
                                </div>
                            </div>



                            <?php
                            echo Form::close();
                            ?>

                            <div class="text-center ajax-loader"></div>
                        </div>

                    </div>
                </div>

            </div>

            <span id="employeeAttendenceReportSection">


        <div class="row">

            <div class="col-12">
                <div class="card">
                    <div class="card-body" id="PrintregionWisePayrollReport">
                        <div class="row">
							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
								<span class="subHeadingLabelClass">View Main Menu Title</span>
							</div>
						</div>
                        <div class="row">
									<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
										<div class="table-responsive">
											<table class="table table-sm mb-0 table-bordered table-striped table-hover">
												<thead>

													<th class="">S.No</th>
													<th class="">Main Menu</th>
													<th class="">Icon</th>
													<th class="text-center">Action</th>
												</thead>
												<tbody>
                                                <?php $count = 1;?>

                                                @foreach($main_menu as $val2)
                                                  <tr>
                                                      <td>{{$count++}}</td>
                                                      <td>{{$val2['title']}}</td>
                                                      <td>{{$val2['icon']}}</td>
                                                      <td>-</td>
                                                  </tr>
                                                @endforeach
												</tbody>
											</table>
										</div>
									</div>
								</div>
                    </div>

                </div>
            </div>

        </div>
        </span>
            <!-- ============================================================== -->
            <!-- End PAge Content -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            <!-- Right sidebar -->
            <!-- ============================================================== -->
            <!-- .right-sidebar -->
            <!-- ============================================================== -->
            <!-- End Right sidebart -->
            <!-- ============================================================== -->
        </div>

    </div>    </div>
