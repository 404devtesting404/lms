<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Name</th>
                <th>Function</th>
                <th>time / date</th>
                <th>IP</th>
                <th>Location</th>
                <th>Issue</th>
            </tr>
        </thead>
        <tbody>
        @foreach($activity_logs as $log)
            <tr>
                <td> <span style="color:blue;">{{$log->user->name}}</span></td>
                <td><a style="color:brown;" target="_blank" href="{{$log->url}}">  {{$log->url}}</a></td>
                <td>
                    <span style="color:green;">
                    {{\App\Helpers\Qs::dateFormat2($log->timestamp)}}
                    </span> -
                    <span style="color:green;">
                    {{\App\Helpers\Qs::dateFormat($log->timestamp)}}
                    </span>
                </td>
                <td>{{$log->ip_address}}</td>
                <td>
                    @if($log->latitude !='')
                        <a target="_blank" href="http://maps.google.com/maps?q={{$log->latitude.','.$log->longitude}}">Open</a>
                    @endif
                </td>
                <td>
                    @if($log->latitude =='')
                        <span style="color:red">location blocked by user</span>
                    @endif
                </td>

            </tr>
        @endforeach
        </tbody>
    </table>
</div>
