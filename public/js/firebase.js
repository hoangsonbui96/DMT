function firebaseCloundMessaging() {
    var firebaseConfig = {
        apiKey: "AIzaSyDLGl0opJtSnTVpXH1xlOM3l_m69jB10UM",
        authDomain: "office-laravel.firebaseapp.com",
        databaseURL: "https://office-laravel.firebaseio.com",
        projectId: "office-laravel",
        storageBucket: "office-laravel.appspot.com",
        messagingSenderId: "909687168099",
        appId: "1:909687168099:web:9decad51a0493213686c56",
        measurementId: "G-WCD62LE4E8"
    };

    firebase.initializeApp(firebaseConfig);

    if ('Notification' in window) {
        const messaging = firebase.messaging();

        if (Notification.permission === "granted" || Notification.permission === 'prompt' || Notification.permission === 'default') {
            messaging.requestPermission()
                .then(function () {
                    return messaging.getToken();
                })
                .then(function(token) {
                    console.log(token);
                    $("#token").html(token);
                    return token;
                }).catch(function (err) {
                    console.log('User Chat Token Error'+ err);
                });
        } else {
            console.log(Notification.permission);
        }

        messaging.onMessage(function(payload) {
            var dataBody = JSON.parse(payload.notification.body);
            if (dataBody.type === 'audio') {
                var audio = new Audio(dataBody.msg);
                document.getElementById('clock').autofocus;
                audio.play();
            } else {
                const noteTitle = payload.notification.title;
                const noteOptions = {
                    body: dataBody.msg,
                    icon: payload.notification.icon,
                };
                new Notification(noteTitle, noteOptions);
            }
        });
    }
}