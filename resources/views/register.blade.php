@extends('layouts.app')

@section('content')
    <div class="row" id="register-form">
        <div class="col-md-6 offset-md-3">
            <div class="form-group">
                <label for="name">name</label>
                <input type="text" name="name" id="name" class="form-control">
            </div>
            <div class="form-group">
                <label for="name">email</label>
                <input type="email" name="email" id="email" disabled class="form-control">
            </div>
            <button class="btn btn-primary" id="register-submit">Submit</button>
        </div>
    </div>
@endsection

@section('js')
    <script>
    $(document).ready(function(){
        console.log(localStorage.getItem("email"));
        $('#register-form #email').val(localStorage.getItem("email"));
    });
    $('#register-submit').click(function(){
        $.ajax({
            url:'/api/finalRegister',
            type:"POST",
            data:{
                name:$('#register-form #name').val();
            },
            success:function(data){
                
            },
            error:function(error){

            }
        })
    })
</script>
@endsection