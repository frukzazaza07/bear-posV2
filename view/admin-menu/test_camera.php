<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <div>
        <button id="start-camera" onclick="re()">Start Camera</button>
        <video id="video" width="320" height="240" autoplay></video>
        <button id="click-photo" onclick="take()">Click Photo</button>
        <canvas id="canvas" width="320" height="240"></canvas>
    </div>
    <script>
        let camera_button = document.querySelector("#start-camera");
        let video = document.querySelector("#video");
        let click_button = document.querySelector("#click-photo");
        let canvas = document.querySelector("#canvas");

        async function re() {
            let video = document.querySelector("#video");
            let stream = await navigator.mediaDevices.getUserMedia({
                video: true,
                audio: false
            });
            video.srcObject = stream;
        }

        function take() {
            let canvas = document.querySelector("#canvas");
            canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
            let image_data_url = canvas.toDataURL('image/jpeg');

            // data url of the image
            console.log(image_data_url);
        }
        // camera_button.addEventListener('click', async function() {
        //     let stream = await navigator.mediaDevices.getUserMedia({
        //         video: true,
        //         audio: false
        //     });
        //     video.srcObject = stream;
        // });

        // click_button.addEventListener('click', function() {
        //     canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
        //     let image_data_url = canvas.toDataURL('image/jpeg');

        //     // data url of the image
        //     console.log(image_data_url);
        // });
    </script>
</body>

</html>