@extends('layouts.app')

@section('content')
    FCM NOTIFICATION.
    <br>
    <a href="/fcm/send" class="btn btn-danger"> Send</a>
@endsection
@section('js')
    <script>
                // Your web app's Firebase configuration
        var firebaseConfig = {
            apiKey: "AIzaSyDqDC0dDT8CAww8eyS_c6A4H8vs8FmSg0o",
            authDomain: "como-35100.firebaseapp.com",
            projectId: "como-35100",
            storageBucket: "como-35100.appspot.com",
            messagingSenderId: "228270858431",
            appId: "1:228270858431:web:e4e03f55d2f1363afba60a"
        };
        // Initialize Firebase
        firebase.initializeApp(firebaseConfig);
        //firebase.analytics();
        const messaging = firebase.messaging();
            messaging
        .requestPermission()
        .then(function () {
        //MsgElem.innerHTML = "Notification permission granted." 
            console.log("Notification permission granted.");

            // get the token in the form of promise
            return messaging.getToken()
        })
        .then(function(token) {
        // print the token on the HTML page     
        console.log(token);
        
        
        
        })
        .catch(function (err) {
            console.log("Unable to get permission to notify.", err);
        });

        messaging.onMessage(function(payload) {
            console.log(payload);
            var notify;
            notify = new Notification(payload.notification.title,{
                body: payload.notification.body,
                icon: payload.notification.icon,
                tag: "Dummy"
            });
            console.log(payload.notification);
        });

            //firebase.initializeApp(config);
        var database = firebase.database().ref().child("/users/");
        
        database.on('value', function(snapshot) {
            renderUI(snapshot.val());
        });

        // On child added to db
        database.on('child_added', function(data) {
            console.log("Comming");
            if(Notification.permission!=='default'){
                var notify;
                
                notify= new Notification('CodeWife - '+data.val().username,{
                    'body': data.val().message,
                    'icon': 'bell.png',
                    'tag': data.getKey()
                });
                notify.onclick = function(){
                    alert(this.tag);
                }
            }else{
                alert('Please allow the notification first');
            }
        });

        self.addEventListener('notificationclick', function(event) {       
            event.notification.close();
        });


    </script>
@endsection