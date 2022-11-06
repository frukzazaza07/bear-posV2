<?php require_once("../class/authUsers.php"); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../css/login.css" rel="stylesheet">
    <!--load all styles -->
    <?php include("../autoload/loadallLib.php") ?>
    <title>bear</title>
</head>

<body>
    <div class="login-main" id="app">
        <div class="login-container">
            <form action="" class="form" autocomplete="off" id="loginForm">
                <div class="login-head mt-3">
                    <h5 class="text-center">เข้าสู่ระบบ</h5>
                </div>
                <div class="login-body mt-4">

                    <div class="form-group">
                        <label for="">ชื่อผู้ใช้</label>
                        <input type="text" class="form-control" placeholder="username" v-model="username">
                    </div>
                    <div class="form-group">
                        <label for="">รหัสผ่าน</label>
                        <input type="password" class="form-control" placeholder="password" v-model="password">
                    </div>

                </div>
                <div class="login-footer mt-3">
                    <div class="button-container d-flex justify-content-center">
                        <button type="button" class="btn btn-primary" @click.prevent="sendLogin()">เข้าสู่ระบบ</button>
                        <button class="button mx-3" @click.prevent="logInWithFacebook">Facebook Login</button>
                        <a class="button-register" href="">สมัครสมาชิก</a>
                    </div>
                    <div v-if="errCheck" class="alert mt-3" v-bind:class="[errCheck == true ? 'alert-danger' : 'alert-success']">{{ errMsg }}</div>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/vue@2"></script>
    <!-- <script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_US/all.js"></script> -->
    <script>
        const app = new Vue({
            el: '#app',
            data: {
                username: "",
                password: "",
                errCheck: false,
                errMsg: "",
            },
            mounted: function() {
                this.loadFacebookSDK(document, "script", "facebook-jssdk");
                this.initFacebook();
            },
            methods: {
                sendLogin: function() {
                    let bodyFormData = new FormData();
                    bodyFormData.append('formLogin', JSON.stringify({
                        userUsername: this.username,
                        userPassword: this.password
                    }));
                    var config = {
                        method: 'post',
                        url: `<?= urlTobackend ?>usersLogin.php`,
                        headers: {
                            'Accept': 'application/json',
                            // 'Authorization': 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiI5IiwianRpIjoiYTJiMzdhNGViZjU1NDRmMDliZDY2ZTk0NWJhMmQxNGE1Nzg3YTk2ZGMyYmMzNGMyNjk2OTgxMzc1OWFkOGYxYjk5MGUwNjJiNTgzYzRhM2YiLCJpYXQiOjE2MjAyNjkwMjIsIm5iZiI6MTYyMDI2OTAyMiwiZXhwIjoxNjUxODA1MDIyLCJzdWIiOiIxIiwic2NvcGVzIjpbXX0.do6kFU1NYBpP1CctpkOe0CK-ZRmQSttF6pK_mnX01Bgv2trq1aHmBKw26YDnWEYR-v3IxqzwTZ32ab5O1PGWRttH20hGUZi4MXQITJ7a1I_JJVnt5WLLR9TCy9yr9FkkvFtpFjyyjEwq2i9crIzWp1VEpIPj563kIUzRlqc7bth0whoXHPKDuxbW0h7K_Vuh9mSc79npKdR_jQdlGLAig9QSVPHGZizZfxsuzy5Cwz6wTpbQMWHm-7JEZIaQt-snW19NJ65fEM7MJU7sgxpN-cUnC13o9xtV7Nj8sq7PYIJo9XnTCg3w541ClAYFlQy1ZGTfurVAoD8_DAUX73fUYN30Jyb9jf-TlTaD4zfjnF784QX0Gpe0HY8aN2ApXVdsaSTk_vq-SPLP9hWgs1OMKxX7YrsBlq6adWtnqUBQ9sTnllskqn9mF3-U06F6VBxsjHJZv3gWD0ot2ivGhZWzs8BvTd5d62YCLaQZQsxgHbJbLFBO2YTWsHnP9TnAD6C23PZa0hmTfvsYXrqePAi2Wa7-UPOtF9wYSNzhhFqbLaQ2-MWjMDAmDfX8LQM4w1ZNBmcxS5pZG3sqdIMmU4KJ8WwImXlYbWv5P2JQXjA6sgFCDciARi0NP1Y_M4pWsK_ipkpESIxz9RRb3zabEDKtWgjgp_8DPi0swZ7VR56bIiM',
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        data: bodyFormData,
                        withCredentials: true
                    };
                    axios.defaults.withCredentials = true;
                    axios(config).then((response) => {
                        console.log(response.data);
                        if (response.data.status) {
                            this.errCheck = false;
                            this.errMsg = response.data.message;
                        } else {
                            this.errCheck = true;
                            this.errMsg = response.data.message;
                        }
                    }).catch((e) => {
                        console.log(e);
                    });


                },
                logInWithFacebook: function() {
                    window.FB.login(function(response) {
                        console.log(response);
                        if (response.status === "connected") {
                            app.getUserFacebook(response.authResponse.userID, response.authResponse.accessToken);
                        } else {
                            console.log(response);
                        }
                    });

                },
                initFacebook: function() {
                    window.fbAsyncInit = function() {
                        window.FB.init({
                            appId: "519543755738035", //You will need to change this
                            cookie: true, // This is important, it's not enabled by default
                            xfbml: true,
                            version: "v10.0",
                        });
                    };
                },
                loadFacebookSDK: function(d, s, id) {
                    var js,
                        fjs = d.getElementsByTagName(s)[0];
                    if (d.getElementById(id)) {
                        return;
                    }
                    js = d.createElement(s);
                    js.id = id;
                    js.src = "https://connect.facebook.net/en_US/sdk.js";
                    fjs.parentNode.insertBefore(js, fjs);
                },
                getUserFacebook: function(profileId, accessToken) { // Testing Graph API after login.  See statusChangeCallback() for when this call is made.
                    let userProfileConfig = ["id", "first_name", "last_name", "birthday", "email", "hometown"];
                    FB.api(`/${profileId}?fields=${userProfileConfig.join()}`, function(response) {
                        let bodyFormData = new FormData();
                        bodyFormData.append('formLogin', JSON.stringify({
                            socialLogin: response.id,
                            socialEmail: response.email,
                            socialFirstname: response.first_name,
                            socialLastname: response.last_name,
                        }));
                        var config = {
                            method: 'post',
                            url: `<?= urlTobackend ?>usersLogin.php`,
                            headers: {
                                'Accept': 'application/json',
                                // 'Authorization': 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiI5IiwianRpIjoiYTJiMzdhNGViZjU1NDRmMDliZDY2ZTk0NWJhMmQxNGE1Nzg3YTk2ZGMyYmMzNGMyNjk2OTgxMzc1OWFkOGYxYjk5MGUwNjJiNTgzYzRhM2YiLCJpYXQiOjE2MjAyNjkwMjIsIm5iZiI6MTYyMDI2OTAyMiwiZXhwIjoxNjUxODA1MDIyLCJzdWIiOiIxIiwic2NvcGVzIjpbXX0.do6kFU1NYBpP1CctpkOe0CK-ZRmQSttF6pK_mnX01Bgv2trq1aHmBKw26YDnWEYR-v3IxqzwTZ32ab5O1PGWRttH20hGUZi4MXQITJ7a1I_JJVnt5WLLR9TCy9yr9FkkvFtpFjyyjEwq2i9crIzWp1VEpIPj563kIUzRlqc7bth0whoXHPKDuxbW0h7K_Vuh9mSc79npKdR_jQdlGLAig9QSVPHGZizZfxsuzy5Cwz6wTpbQMWHm-7JEZIaQt-snW19NJ65fEM7MJU7sgxpN-cUnC13o9xtV7Nj8sq7PYIJo9XnTCg3w541ClAYFlQy1ZGTfurVAoD8_DAUX73fUYN30Jyb9jf-TlTaD4zfjnF784QX0Gpe0HY8aN2ApXVdsaSTk_vq-SPLP9hWgs1OMKxX7YrsBlq6adWtnqUBQ9sTnllskqn9mF3-U06F6VBxsjHJZv3gWD0ot2ivGhZWzs8BvTd5d62YCLaQZQsxgHbJbLFBO2YTWsHnP9TnAD6C23PZa0hmTfvsYXrqePAi2Wa7-UPOtF9wYSNzhhFqbLaQ2-MWjMDAmDfX8LQM4w1ZNBmcxS5pZG3sqdIMmU4KJ8WwImXlYbWv5P2JQXjA6sgFCDciARi0NP1Y_M4pWsK_ipkpESIxz9RRb3zabEDKtWgjgp_8DPi0swZ7VR56bIiM',
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            data: bodyFormData,
                            withCredentials: true
                        };
                        axios.defaults.withCredentials = true;
                        axios(config).then((response) => {
                            console.log(response.data);
                            if (response.data.status) {
                                this.errCheck = false;
                                this.errMsg = response.data.message;
                            } else {
                                this.errCheck = true;
                                this.errMsg = response.data.message;
                            }
                        }).catch((e) => {
                            console.log(e);
                        });
                    });
                }
            }
        });
    </script>
</body>

</html>