<div class="box box-info padding-1">
    <div class="box-body">

        <div class="row">
            <div class="col-md-12">
                   <div class="form-group row">
                        <label for="name" class="col-lg-3 col-form-label font-weight-semibold">Name</label>
                        <div class="col-lg-9">
                            <input required name="name" class="form-control" type="text" value="">
                        </div>
                    </div>


                    <div class="form-group row">
                        <label for="email" class="col-lg-3 col-form-label font-weight-semibold">Email </label>
                        <div class="col-lg-9">
                            <input required name="email" type="email" class="form-control" >
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="email" class="col-lg-3 col-form-label font-weight-semibold">Password </label>
                        <div class="col-lg-9">
                            <input required id="password" name="password"  type="text" class="form-control" >
                        </div>
                    </div>

                <div class="form-group row">
                    <label for="email" class="col-lg-3 col-form-label font-weight-semibold">Roles </label>
                    <div class="col-lg-9">
                        <select required class="form-control" name="role_id" id="role_id">
                            <option selected>Select</option>
                            @foreach($roles as $val)
                                <option value="{{$val->id}}">{{$val->role_name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>



                    <div class="text-right">
                        <button type="submit" class="btn btn-success">Submit form <i class="icon-paperplane ml-2"></i></button>
                    </div>

            </div>
        </div>

    </div>

</div>
