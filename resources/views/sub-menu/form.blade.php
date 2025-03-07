<div class="box box-info padding-1">
    <div class="box-body">



        <input type="hidden" name="_token" value="{{ csrf_token() }}">

        <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                <label>Main Navigation Name</label><br>
                <?php //echo "<pre>";print_r($sub_menu) ?>
                <select class="form-control" required name="main_navigation_name" id="main_navigation_name">
                    <option value="">Select Main Navigation</option>
                    @foreach($main_menu as $key => $y)
                        <option value="<?php echo $y->id.'_'.$y->title_id?>">{{ $y->title}}</option>
                    @endforeach

                </select>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                <label>Sub Navigation Title Name</label>
                <input type="text" name="sub_navigation_title_name" id="sub_navigation_title_name" value="" class="form-control requiredField" />
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                <label>Sub Navigation Url</label>
                <input type="text" name="sub_navigation_url" id="sub_navigation_url" value="" class="form-control requiredField" />
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                <label>Js</label>
                <input type="text" name="js" id="js" value="" class="form-control requiredField" />
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                <label>Page Type</label>
                <select class="form-control" name="page_type" id="page_type">
                    <option value="1">Outer Page</option>
                    <option value="2">Inner Page</option>
                </select>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                <br>
                {{ Form::submit('Submit', ['class' => 'btn btn-sm btn-success']) }}
                <button type="reset" class="btn btn-sm btn-primary">Clear Form</button>
            </div>
        </div>
        <br>


        <br>
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

                                        <th class="text-center">S.No</th>
                                        <th class="text-center">Main Menu</th>
                                        <th class="text-center">Sub Menu</th>
                                        <th class="text-center">Route</th>
                                        <th class="text-center">Type</th>
                                        </thead>
                                        <tbody>
                                        <?php $count = 1;?>
                                        @foreach($sub_menu as $val)
                                            <tr>
                                                <td class="text-center">{{$count++}}</td>
                                                <td>{{$val->m_main_title}}</td>
                                                <td>{{$val->name}}</td>
                                                <td>{{$val->m_controller_name}}</td>
                                                <td>{{($val->page_type == 1)?"Outer Page":"Inner Page"}}</td>

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

    </div>
</div>
