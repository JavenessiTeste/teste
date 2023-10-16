<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1, maximum-scale=1">
</head>
<body>
<div>
    <video id="localVideo"  autoplay playsinline muted style="max-width: 100%;height: auto;"></video>
    <video id="remoteVideo" autoplay playsinline       style="max-width: 100%;height: auto;"></video>
</div>
<div id="log"></div>
<script type="text/javascript">

    var answer = 0;
    
	var pc=null
	
	var localStream=null;
	
	var ws=null;
    
    // Not necessary with websockets, but here I need it to distinguish calls
    var unique = <?php echo $_GET['CHAVE']?>;//Math.floor(100000 + Math.random() * 900000);

    var localVideo = document.getElementById('localVideo');
    var remoteVideo = document.getElementById('remoteVideo');
    var configuration  = {
        'iceServers': [
			{ 'urls': 'stun:stun.l.google.com:19302' },
			{ 'urls': 'stun:stun.nextcloud.com:443' },
			//{'urls': 'stun:stun1.l.google.com:19302' },
			//{'urls': 'stun:stun2.l.google.com:19302' }
        ]
    };

	// Start
    navigator.mediaDevices.getUserMedia({
            audio: true, // audio is off here, enable this line to get audio too
            video: true
        }).then(function (stream) {
            localVideo.srcObject = stream;
            localStream = stream;

            try {
                ws = new EventSource('serverGet.php?unique='+unique);
            } catch(e) {
               addLog("Could not create eventSource "+e);
            }

            // Websocket-hack: EventSource does not have a 'send()'
            // so I use an ajax-xmlHttpRequest for posting data.
            // Now the eventsource-functions are equal to websocket.
			ws.send = function send(message) {
				
				 var xhttp = new XMLHttpRequest();
				 xhttp.onreadystatechange = function() {
					 addLog('ABCD');
					 if (this.readyState!=4) {
					   return;
					 }
					 if (this.status != 200) {
					   addLog("Error sending to server with message: " +message);
					 }
				 };
				 
				 var jsonMessage = JSON.parse(message);
				 addLog(jsonMessage); 
				 //if(jsonMessage.data!== null){
					addLog("serverPost");
					
					addLog("Data: "+JSON.stringify(jsonMessage)); 
					addLog("Data: "+jsonMessage.data); 
					addLog("Data: "+jsonMessage.event); 
					xhttp.open('POST', 'serverPost.php?unique='+unique, true);
					xhttp.setRequestHeader("Content-Type","Application/X-Www-Form-Urlencoded");
					xhttp.send(message);
				// }
			}

            // Websocket-hack: onmessage is extended for receiving 
            // multiple events at once for speed, because the polling 
            // frequency of EventSource is low.
			ws.onmessage = function(e) {
				
				if (e.data.includes("_MULTIPLEVENTS_")) {
					multiple = e.data.split("_MULTIPLEVENTS_");
					for (x=0; x<multiple.length; x++) {
						onsinglemessage(multiple[x]);
					}
				} else {
					onsinglemessage(e.data);
				}
			}

            // Go show myself
            localVideo.addEventListener('loadedmetadata', 
                function () {
					
                    publish('client-call', null)
                }
            );
			
        }).catch(function (e) {
            addLog("Problem while getting audio/video stuff "+e);
			alert("Verifique se não existe um antivirus bloqueando a camera, ou se seu navegador tem permissão de camera e microfone.");
			document.location.reload(true);
        });
		
    
    function onsinglemessage(data) {
		console.log(data);
		if(data=='' ||data== undefined || data==null)
			return false;
        var package = JSON.parse(data);
        var data = package.data;
       
		addLog("received single message: " + package.event);
		 addLog("Data: " + JSON.stringify(data));
        switch (package.event) {
            case 'client-call':
                icecandidate(localStream);
                pc.createOffer({
                    offerToReceiveAudio: 1,
                    offerToReceiveVideo: 1
                }).then(function (desc) {
                    pc.setLocalDescription(desc).then(
                        function () {
                            publish('client-offer', pc.localDescription);
                        }
                    ).catch(function (e) {
                        addLog("Problem with publishing client offer"+e);
						//document.location.reload(true);
                    });
                }).catch(function (e) {
                    addLog("Problem while doing client-call: "+e);
                });
                break;
            case 'client-answer':
                if (pc==null) {
                    addLog('Before processing the client-answer, I need a client-offer');
                    break;
                }
                pc.setRemoteDescription(new RTCSessionDescription(data),function(){}, 
                    function(e) { addLog("Problem while doing client-answer: "+e);
                });
                break;
            case 'client-offer':
                icecandidate(localStream);
                pc.setRemoteDescription(new RTCSessionDescription(data), function(){
                    if (!answer) {
                        pc.createAnswer(function (desc) {
                                pc.setLocalDescription(desc, function () {
                                    publish('client-answer', pc.localDescription);
                                }, function(e){
                                   addLog("Problem getting client answer: "+e);
                                });
                            }
                        ,function(e){
                            addLog("Problem while doing client-offer: "+e);
                        });
                        answer = 1;
                    }
                }, function(e){
                    addLog("Problem while doing client-offer2: "+e);
                });
                break;
            case 'client-candidate':
               if (pc==null) {
                    addLog('Before processing the client-answer, I need a client-offer');
                    break;
                }
                pc.addIceCandidate(new RTCIceCandidate(data), function(){}, 
                    function(e) { addLog("Problem adding ice candidate: "+e);});
                break;
        }
    };

    function icecandidate(localStream) {
		
        pc = new RTCPeerConnection(configuration);
        pc.onicecandidate = function (event) {
            if (event.candidate) {
                publish('client-candidate', event.candidate);
            }
        };
        try {
            pc.addStream(localStream);
        }catch(e){
            var tracks = localStream.getTracks();
            for(var i=0;i<tracks.length;i++){
                pc.addTrack(tracks[i], localStream);
            }
        }
        pc.ontrack = function (e) {
            //document.getElementById('remoteVideo').style.display="block";
            //document.getElementById('localVideo').style.display="none";
			document.getElementById('localVideo').style.zIndex = "999";
			document.getElementById('localVideo').style.width = '100px';
			document.getElementById('localVideo').style.position = 'absolute';
			document.getElementById('localVideo').style.left = '20px';
			document.getElementById('localVideo').style.top = '20px';
			addLog("-----------***-----------");
			addLog(pc.connectionState);
            remoteVideo.srcObject = e.streams[0];
        };
    }

    function publish(event, data) {
       addLog("sending ws.send: " + event);
	   addLog("Data: " + JSON.stringify(data));
        ws.send(JSON.stringify({
            event:event,
            data:data
        }));
    }
	function addLog(log){
		
		var p = document.getElementById('log');
		var newElement = document.createElement('p');
		//newElement.setAttribute('id', elementId);
		newElement.innerHTML = log;
		p.appendChild(newElement);
		
	}

</script>
</body>
</html>