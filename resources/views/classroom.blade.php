<html>

<head>
    <title> OpenTok Getting Started </title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{asset("/css/all.min.css")}}">
    <style>
        body,
        html {
            background-color: gray;
            height: 100%;
        }

        #videos {
            position: relative;
            width: 100%;
            height: 100%;
            margin-left: auto;
            margin-right: auto;
        }

        #subscriber {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            z-index: 10;
        }

        #publisher {
            position: absolute;
            width: 360px;
            height: 240px;
            bottom: 10px;
            left: 10px;
            z-index: 100;
            border: 3px solid white;
            border-radius: 3px;
        }

        #publisher.large
    { width: 640px; height: 480px; }
    #publisher.small
    { width:100px; height: 100px; }

@media screen and (max-width: 650px) {
    #publisher
    { width: 89px; height: 50px; }
}
        #screen-preview {
            position: absolute;
            width: 100%;
            height: 100%;
            z-index: 10;
            bottom: 300px
        }

        .video-menu {
            position: absolute;
            width: 550px;
            height: 50px;
            bottom: 10px;
            left: 400px;
            z-index: 100;
            border: 3px solid white;
            border-radius: 3px;
            display: flex;
            flex-direction: row;
            justify-content: center;

        }

        .btn {
            color: white;
            height: 30px;
            margin-top: 10px;
            padding: 3px;
            margin-left: 2%;
            text-decoration-line: none;
            background-color: red;
            font-weight: bold;

        }

                .btn-2 {
            color: white;
            height: 30px;
            margin-top: 10px;
            padding: 3px;
            text-align: center;
            margin-left: 2%;
            text-decoration-line: none;
            background-color: rgb(7, 7, 68);

        }
                       .btn-3 {
            color: white;
            height: 30px;
            margin-top: 10px;
            padding: 3px;
            text-align: center;
            margin-left: 2%;
            text-decoration-line: none;
            background-color: black;

        }

    </style>

    <script src="https://static.opentok.com/v2/js/opentok.min.js"></script>

</head>

<body>
    <div id="videos">
        <div id="subscriber"></div>
        <div class="video-menu">
            <form action="{{route('home')}}">
            <button class="btn">Leave</button>
            </form>
           <button class="btn-2" id="close" onclick="toggleCamera()">on/off camera</button>
           <button class="btn-2" id="share_screen" onclick="shareScreen()">share screen</button>
           
        </div>
        <div id="publisher"></div>
        <div id="screen-preview" class="screen-preview"></div>
    </div>

<script type="text/javascript">
 var session;
 var connectionCount = 0;
 var apiKey = "47231534";
 var sessionId = "{{$sessionId}}";
 var token = "{{$token}}";
 var publisher;
 var camera = false;
 var publisherName = "{{$user->name}}"

 var enableVideo = true;

 // Replace apiKey and sessionId with your own values:

 session = OT.initSession(apiKey, sessionId);
 session.on("streamCreated", function (event) {

    var parentElementId = event.stream.videoType === 'screen' ?
    'sub-screen-sharing-container' :
    'sub-camera-container';

     console.log("New stream in the session: " + event.stream.streamId);
     session.subscribe(event.stream , 'subscriber', {
         insertMode: 'append',
         width: '100%',
         height: '100%',
         fitMode: "cover",
         style: {
         buttonDisplayMode: 'on',
         nameDisplayMode: "on"
     }
     });
 });

//  subscriber.on('videoDimensionsChanged', function(event) {
//   subscriber.element.style.width = event.newValue.width + 'px';
//   subscriber.element.style.height = event.newValue.height + 'px';
//   // You may want to adjust other UI.
// });

 session.on({
     connectionCreated: function (event) {
         connectionCount++;
         alert(connectionCount + ' connections.');
     },
     connectionDestroyed: function (event) {
         connectionCount--;
         alert(connectionCount + ' connections.');
     },
     sessionDisconnected: function sessionDisconnectHandler(event) {
         // The event is defined by the SessionDisconnectEvent class
         alert('Disconnected from the session.');
         document.getElementById('disconnectBtn').style.display = 'none';
         if (event.reason == 'networkDisconnected') {
             alert('Your network connection terminated.')
         }
     }
 });

 var publisher = OT.initPublisher('publisher', {
     insertMode: 'append',
     width: '100%',
     height: '100%',
     publishAudio: true,
     publishVideo: enableVideo,
     name: publisherName,
     fitMode: "contain",
     style: {
         buttonDisplayMode: 'on',
         nameDisplayMode: "on"
     }
 }, error => {
     if (error) {
         alert(error.message);
     }
 });

 function toggleCamera() {
     if (enableVideo) {
         publisher.publishVideo(false);
         enableVideo = false;
     } else {
         publisher.publishVideo(true);
         enableVideo = true;
     }
 }

 // Replace token with your own value:
 session.connect(token, function (error) {
     if (error) {
         alert('Unable to connect ya hadeer: ', error.message);
     } else {
         // document.getElementById('disconnectBtn').style.display = 'block';
         alert('Connected to the session.');
         connectionCount = 1;

         if (session.capabilities.publish == 1) {
             session.publish(publisher);
         } else {

             alert("You cannot publish an audio-video stream.");
         }
     }
 });

 // share screen 
 function shareScreen() {

  OT.checkScreenSharingCapability(function(response) {
  if(!response.supported || response.extensionRegistered === false) {
    // This browser does not support screen sharing.
        alert("This browser does not support screen sharing.");

  } else if (response.extensionInstalled === false) {
    // Prompt to install the extension.
        alert("Prompt to install the extension.");

  } else {
    // Screen sharing is available. Publish the screen.
    var publisher = OT.initPublisher('screen-preview',
      {videoSource: 'screen'},
      function(error) {
        if (error) {
          // Look at error.message to see what went wrong.
            alert("error " , error.message );

        } else {
          session.publish(publisher, function(error) {
            if (error) {
              // Look error.message to see what went wrong.
                alert("error " , error.message );
            }
          });
        }
      }
    );
  }
});
 }

 publisher.on('mediaStopped', function(event) {
  // The user clicked stop.

    alert("The user clicked stop " );
});

publisher.on('streamDestroyed', function(event) {
  if (event.reason === 'mediaStopped') {
    // User clicked stop sharing
    alert("User clicked stop sharing " );

  } else if (event.reason === 'forceUnpublished') {
    // A moderator forced the user to stop sharing.
    alert("A moderator forced the user to stop sharing." );
  }
});
</script>

</body>

</html>
