@extends('layouts.app')

@section('content')

    <button class="btn btn-info">Show Login Form</button>
    <button class="btn btn-primary btn-rounded mb-4"  data-toggle="modal" data-target="#modalLoginForm">Show Register Form</button>

    {{-- //register modal --}}
    <div class="modal fade" id="modalLoginForm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
  aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header text-center">
        <h4 class="modal-title w-100 font-weight-bold">Sign in</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body mx-3">
        <div class="md-form mb-5 d-flex">
            <label data-error="wrong" data-success="right" for="defaultForm-email">Your email</label>
          <input type="email" id="email" class="form-control validate">
        </div>

      </div>
      <div class="modal-footer d-flex justify-content-center">
        <button class="btn btn-default" id="login">Login</button>
      </div>
    </div>
  </div>
</div>

{{-- //enter otp modal-body --}}

<div id="otpModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">ENTER OTP</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <input type="number" id="otp" name="otp" class="form-control">
        
      </div>
      <div class="modal-footer">
        <button id="confirm-otp" name="confirm-otp" class="btn btn-primary">Verify</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>
@endsection

@section('js')
    <script>
        $('#login').click(function(){
            const email=$(' #email').val();
            // console.log(email);
            $.ajax({
                url:'/api/auth/register',
                type:'POST',
                data:{
                    "_token":"{{csrf_token()}}",
                    "email":email,
                },
                success:function(data){
                  // console.log(data);
                    if(data.status=='OK')
                    {
                      $('#modalLoginForm').modal('hide');
                      $('#otpModal').modal('show'); 
                      
                      const token=data.data.token;
                      localStorage.setItem('token',token);
                      localStorage.setItem('email',email);
                      $('#confirm-otp').click(function(){
                        const otp=$('#otpModal #otp').val();
                        console.log(otp);
                            $.ajax({
                            url:'/api/verifyOtp',
                            type:'POST',
                            headers: {
                                Authorization: 'Bearer '+token
                            },
                            data:{
                              "_token":"{{csrf_token()}}",
                              "otp":otp
                            },
                            success:function(data){
                              if(data.status=='OK')
                              {
                                $.ajax({
                                  url:'/api/reg',
                                  type:'GET',
                                  success:function(data){
                                    document.write(data);
                                  }
                                })
                              }
                            },
                            error:function(error){

                            }
                          })
                      });
                      
                    }
                    // alert(JSON.stringify(data));
                },
                error:function(error){
                    console.log(error);
                }
            })
        })
    </script>
@endsection