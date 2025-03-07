@extends('layouts.master')
@section('page_title', 'Customer Data')
@section('content')

    <div class="card card-default">

        <div class="card-body">
            <div class="box box-info padding-1">
                <div class="box-body">
                    <div class="page-wrapper">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="text-center bg-grey">
                                            <h2>Customer Detail</h2>
                                        </div>

                                         <div class="row">
                                             <br>
                                             @if($customer)
                                                 @foreach($customer[0] as $key => $custVal)
                                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                                        <p style="font-size: 14px;"><strong>{{$key}}</strong> : {{$custVal}}</p>
                                                    </div>
                                                 @endforeach
                                              @endif

                                        </div>

                                        <div class="text-center bg-grey">
                                            <h2>Application Detail</h2>
                                        </div>

                                        <div class="row">
                                            <br>
                                            @if($applications)
                                                @foreach($applications[0] as $key1 => $appVal)
                                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                                        <p style="font-size: 14px;"><strong>{{$key1}} </strong>: {{$appVal}}</p>
                                                    </div>
                                                @endforeach
                                            @endif

                                        </div>

                                        <div class="text-center bg-grey">
                                            <h2>Attachments Detail</h2>
                                        </div>

                                        <div class="row">
                                            <br>
                                            @if($user_attachments)
                                                @foreach($user_attachments[0] as $key2 => $attVal)
                                                    @if(!in_array($key2,['id','createdAt','updatedAt','userId']))

                                                     <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                                         <p style="font-size: 14px;"><strong>{{$key2}}</strong> :
                                                            @if($attVal)
                                                                <br>
                                                                <?php $count=1; $arr = explode(',',$attVal) ?>
                                                                @foreach($arr as $arrVal)
                                                                    <a target="_blank" href="https://api.asaanghar.com/attachments/{{$arrVal}}">{{$count++}}. View</a><br>
                                                                 @endforeach

                                                            @endif
                                                        </p>
                                                    </div>

                                                    @endif
                                                @endforeach
                                            @endif

                                        </div>
                                    </div>

                                </div>
                            </div>
                          </div>
                         </div>
                    </div>
                 </div>

            </div>
        </div>

@endsection
