@extends('layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="row content">
            <div class="col-sm-2 sidenav">
                @include('includes.sidebar')
            </div>
            <div class="col-md-10">
                @if(isset($status['message']))
                    <script>
                        alert("Propagation succeeded for {{ $status['weeks'] }} weeks!")
                        window.location.replace('/propfinish');
                    </script>

                @else
                    <script>
                        alert("Propagation failed on {{ $status['date'] }} ")
                        window.location.replace("/propfinish/" + "<?php echo $status['date'] ?>" );
                    </script>

                @endif
            </div>
        </div>
    </div>
@endsection
