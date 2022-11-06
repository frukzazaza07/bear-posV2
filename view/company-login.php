<?php //require_once("../engine/auth/checkLogin.php"); 
?>
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
                    <div class="mt-2">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" value="0" v-model="adminPage">
                            <label class="form-check-label" for="inlineRadio1">Pos Page</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" value="1" v-model="adminPage">
                            <label class="form-check-label" for="inlineRadio2">Admin Page</label>
                        </div>
                    </div>


                </div>
                <div class="login-footer mt-3">
                    <div class="button-container d-flex justify-content-center row">
                        <div class="col-12 text-center"><button type="button" class="btn btn-primary" @click.prevent="sendLogin()" :disabled="disabled == 1">{{ btnLoginMsg }}</button></div>
                        <!-- <div class="col-12 col-lg-6"><a class="button-register" href="">เพิ่มพนักงาน</a></div> -->
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
                disabled: 0,
                btnLoginMsg: "เข้าสู่ระบบ",
                errMsg: "",
                adminPage: "0",
            },
            mounted: function() {},
            methods: {
                sendLogin: function() {
                    this.disabled = 1;
                    this.btnLoginMsg = "กำลังโหลด...";
                    let bodyFormData = new FormData();
                    bodyFormData.append('formLogin', JSON.stringify({
                        userUsername: this.username,
                        userPassword: this.password,
                        pageOption: this.adminPage,
                    }));
                    var config = {
                        method: 'post',
                        url: `<?= urlTobackend ?>companyLogin.php`,
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
                        // console.log(response.data);
                        if (response.data.status) {
                            this.errCheck = false;
                            let urlGo = "<?= domain ?>view/pos";
                            // ให้หน้า admin check session positionId อีกที
                            if (this.adminPage == "1" && parseInt(response.data.results["positionId"]) < 4) urlGo = "<?= domain ?>view/admin-menu/add-product";
                            setTimeout(() => {
                                window.location.href = urlGo;
                            }, 500);

                        } else {
                            setTimeout(() => {
                                this.disabled = 0;
                                this.btnLoginMsg = "เข้าสู่ระบบ";
                            }, 500);
                            this.errCheck = true;
                            this.errMsg = response.data.message;
                            console.log(response.data);
                        }
                    }).catch((e) => {
                        setTimeout(() => {
                            this.disabled = 0;
                            this.btnLoginMsg = "เข้าสู่ระบบ";
                        }, 500);
                        this.errCheck = true;
                        this.errMsg = e;
                        console.log(e);
                    });


                },

            }
        });
    </script>
</body>

</html>