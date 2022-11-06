<?php
require_once("template.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/bear/vendor/autoload.php");

// use thiagoalessio\TesseractOCR\TesseractOCR;

// echo (new TesseractOCR('text2.png'))
//     ->lang('eng')
//     ->run();
// cd ไปยัง path ที่รูปอยู่
// exec("tesseract test2.png out.txt -l eng", $output, $status);
// echo $status;
// print_r($output);
// exit;
?>

<?= templateHeader; ?>
<link rel="stylesheet" href="../../css/add-employee.css">
<?= templateBody; ?>
<div class="content-wrapper p-4" id="app">
    <div class="content-header pb-1">
        <h4>เพิ่มพนักงานใหม่</h4>
        <hr>
    </div>
    <div class="content-body">
        <div class="container-fluid content-custom" style="position: relative;">
            <form class="add-new-employee-form pb-4 overflow-y-custom" id="addNewEmployeeForm" method="POST" enctype="multipart/form-data">
                <div class="row form-step px-2 pb-3" id="step1" v-bind:class="[form.templateStep == 1 ? 'step-show' : 'step-hide d-none',]">
                    <h5 class="mb-2">ข้อมูลพนักงาน</h5>
                    <div class="col-6 col-lg-5">
                        <div class="form-group">
                            <label for="">ชื่อพนักงาน</label>
                            <input type="text" class="form-control" id="employeeFirstname" name="employeeFirstname" maxlength="255" placeholder="ชื่อจริง" v-model="employee.employeeFirstname">
                        </div>
                    </div>
                    <div class="col-6 col-lg-5">
                        <div class="form-group">
                            <label for="">นามสกุลพนักงาน</label>
                            <input type="text" class="form-control" id="employeeLastname" name="employeeLastname" maxlength="255" placeholder="นามสกุล" v-model="employee.employeeLastname">
                        </div>
                    </div>
                    <div class="col-6 col-lg-2">
                        <div class="form-group">
                            <label for="">ชื่อเล่นพนักงาน</label>
                            <input type="text" class="form-control" id="employeeNickname" name="employeeNickname" maxlength="255" placeholder="ชื่อเล่น" v-model="employee.employeeNickname">
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="form-group">
                            <label for="">วัน/เดือน/ปี เกิด</label>
                            <input type="text" class="form-control" id="employeeBirthday" name="employeeBirthday" placeholder="2021-12-31" v-model="employee.employeeBirthday">
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="form-group">
                            <label for="">เบอร์โทรศัพท์</label>
                            <input type="text" class="form-control" id="employeeMobile" name="employeeMobile" placeholder="081-234-5678" v-model="employee.employeeMobile" v-mask="'###-###-####'">
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="form-group">
                            <label for="">อีเมล</label>
                            <input type="text" class="form-control" id="employeeEmail" name="employeeEmail" placeholder="email@gmail.com" v-model="employee.employeeEmail">
                        </div>
                    </div>
                    <div class="col-6 col-lg-6">
                        <div class="form-group">
                            <label for="">ที่อยู่</label>
                            <input type="text" class="form-control" id="employeeAddress" name="employeeAddress" placeholder="73/5 หมู่ 5" v-model="employee.employeeAddress">
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="form-group">
                            <label for="">จังหวัด</label>
                            <select type="text" class="form-control" id="employeeProvinces" name="employeeProvinces" v-model="employee.employeeProvinces" @change="[amphurData=[],employee.employeeAmphur = '', employee.employeePostcode='', tambonData=[],employee.employeeTambon = '', loadAmphur(employee.employeeProvinces),provincesSelected = employee.employeeProvinces]">
                                <option value="">เลือก</option>
                                <option v-for="(item, index) in provincesData" :value="item.id.trim()">{{ item.name_th.replace("*","") }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="form-group">
                            <label for="">อำเภอ/เขต</label>
                            <select type="text" class="form-control" id="employeeAmphur" name="employeeAmphur" :disabled="provincesSelected == '' || employee.employeeProvinces == ''" v-model="employee.employeeAmphur" @change="[employee.employeePostcode='', tambonData=[],employee.employeeTambon = '', loadTambon(employee.employeeAmphur),amphurSelected = employee.employeeAmphur]">
                                <option value="">เลือก</option>
                                <option v-for="(item, index) in amphurData" :value="item.id.trim()">เขต{{ item.name_th.replace("*","").replace("เขต","") }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="form-group">
                            <label for="">ตำบล/แขวง</label>
                            <select type="text" class="form-control" id="employeeTambon" name="employeeTambon" :disabled="amphurSelected == '' || employee.employeeAmphur == ''" v-model="employee.employeeTambon" @change="[loadPostcode(employee.employeeTambon)]">
                                <option value="">เลือก</option>
                                <option v-for="(item, index) in tambonData" :value="item.id.trim()">แขวง{{ item.name_th.replace("*","").replace("แขวง","") }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-6 col-lg-2">
                        <div class="form-group">
                            <label for="">รหัสไปรษณีย์</label>
                            <input type="text" class="form-control" id="employeePostcode" name="employeePostcode" placeholder="10230" v-model="employee.employeePostcode">
                        </div>
                    </div>
                    <div id="errorContainer" class="col-12 row justify-content-center d-none">
                        <div class="col-12 col-md-6">
                            <div class="alert alert-danger">
                                <ul id="errorList" style="margin-bottom: 0px;">

                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 row justify-content-center mt-3" v-if="formErrorStep1.length > 0">
                        <div class="col-12 col-md-6">
                            <div class="alert alert-danger">
                                <ul style="margin-bottom: 0px;">
                                    <li v-for="(item, index) in formErrorStep1">{{ item }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 text-center">
                        <input type="button" class="btn btn-primary" value="ถัดไป" @click.prevent="form.templateStep++">
                    </div>
                </div>
                <!-- step1 end -->
                <div class="row px-2 pb-3" v-bind:class="[form.templateStep == 2 ? 'step-show' : 'step-hide d-none',]" id="step2">
                    <h5 class="mb-2">ข้อมูลพนักงาน</h5>
                    <div class="col-6">
                        <div class="form-group">
                            <label for="">รหัสพนักงาน</label>
                            <input type="text" class="form-control" id="employeeSubId" name="employeeSubId" placeholder="e-000001" v-model="employee.employeeSubId">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label for="">ชื่อผู้ใช้</label>
                            <input type="text" class="form-control" id="employeeUsername" name="employeeUsername" placeholder="username" v-model="employee.employeeUsername">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label for="">รายละเอียดพนักงาน</label>
                            <textarea type="text" class="form-control" id="employeeDetail" name="employeeDetail" placeholder="รายละเอียดพนักงาน" v-model="employee.employeeDetail"></textarea>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="form-group">
                            <label for="">ระบุสาขา</label>
                            <select type="text" class="form-control" id="employeeBranch" name="employeeBranch" placeholder="" v-model="employee.employeeBranch">
                                <!-- <option value="0">ทุกสาขา</option> -->
                                <option v-for="(item, index) in branchData" :value="item.branch_id">{{ item.branch_name }}</option>
                            </select>
                            <small class="text-danger"><b>พนักงานจะแสดงเฉพาะสาขาที่เลือก</b></small>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="form-group">
                            <label for="">ตำแหน่งงาน</label>
                            <select type="text" class="form-control" id="employeePosition" name="employeePosition" placeholder="" v-model="employee.employeePosition">
                                <option v-for="(item, index) in positionData" :value="item.position_id">{{ item.position_name }}</option>
                            </select>
                            <small class="text-danger"><b>โปรดเลือกตำแหน่งให้ตรง</b></small>
                        </div>
                    </div>
                    <div class="col-12 row justify-content-center mt-3" v-if="formErrorStep2.length > 0">
                        <div class="col-12 col-md-6">
                            <div class="alert alert-danger">
                                <ul style="margin-bottom: 0px;">
                                    <li v-for="(item, index) in formErrorStep2">{{ item }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 text-center">
                        <input type="button" class="btn btn-warning" value="ย้อนกลับ" @click.prevent="form.templateStep--">
                        <input type="button" class="btn btn-primary" value="ถัดไป" @click.prevent="form.templateStep++">
                    </div>
                </div>
                <!-- step2 end -->
                <div class="row px-2 pb-3" id="step3" v-bind:class="[form.templateStep == 3 ? 'step-show' : 'step-hide d-none',]">
                    <h5 class="mb-2">รูปพนักงาน</h5>
                    <!-- กล้อง -->
                    <div class="col-12 mt-3 text-center">
                        <video class="d-none" :id="'cameraContainer'" width="400" height="300" autoplay></video>
                        <div class="mt-2">
                            <button class="btn btn-primary btn-sm" id="requestCamera" @click.prevent="requestCamera('cameraContainer', 'requestCamera')">เปิดกล้อง</button>
                            <div>
                                <div class="text-danger"><strong><span>ถ่ายรูป/เลือกรูปภาพ*</span></strong></div>
                            </div>
                        </div>
                    </div>
                    <!-- end กล้อง -->
                    <div class="col-12 col-lg-6 mt-3 chooseImage" v-for="(item,index) in imageUploadList">
                        <div class="product-preview-container" id="preview">
                            <label for="formFile" class="form-label">{{ item.labelName }}</label>
                            <div v-if="!item.imageData">
                                <input class="form-control" type="file" @change="createImage(index,$event)" v-model="item.imageData">
                            </div>
                            <div class="text-center" v-else>
                                <img class="product-preview-img mb-2" :src="item.imagePreview" />
                                <div><strong>{{ item.imageName }}</strong></div>
                                <button class="btn btn-danger mt-1" @click.prevent="removeImage(index)">ลบรูป</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6 mt-3 d-none take-photo-container" v-for="(item,index) in imageUploadList">
                        <div class="product-preview-container" id="preview">
                            <label for="formFile" class="form-label">{{ item.labelName }}</label>
                            <!-- กล้อง -->
                            <div>
                                <div class="mb-2">
                                    <button class="btn btn-secondary btn-sm mt-2 btn-take-photo" :id="'takePhoto' + index" @click.prevent="takePhoto('cameraContainer', 'photo' + index, 'takePhoto' + index, index)">ถ่ายรูป</button>
                                </div>
                                <div class="text-center">
                                    <canvas class="d-none canvas-take-photo" :id="'photo' + index" width="300px" height="240px"></canvas>
                                </div>
                            </div>
                            <!-- end กล้อง -->
                        </div>
                    </div>
                    <div class="col-12 row justify-content-center mt-3" v-if="formErrorStep3.length > 0">
                        <div class="col-12 col-md-6">
                            <div class="alert alert-danger">
                                <ul style="margin-bottom: 0px;">
                                    <li v-for="(item, index) in formErrorStep3">{{ item }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <input type="hidden">
                    </div>
                    <div class="col-12 text-center" v-bind:class="[]">
                        <input type="button" class="btn btn-warning" value="ย้อนกลับ" @click.prevent="form.templateStep--">
                        <button type="submit" class="btn btn-success" @click.prevent="setDataForSendToBackend()">เพิ่มพนักงาน</button>
                    </div>
                </div>
                <!-- step3 end -->
            </form>
        </div><!-- /.container-fluid -->
    </div>
</div>
<?= templateFootter ?>
<script src="/bear/js/customValidation.js"></script>
<script src="https://cdn.jsdelivr.net/npm/vue@2"></script>
<script src="https://cdn.jsdelivr.net/npm/v-mask/dist/v-mask.min.js"></script>
<script>
    // As a plugin
    Vue.use(VueMask.VueMaskPlugin);

    // Or as a directive
    Vue.directive('mask', VueMask.VueMaskDirective);
</script>
<script>
    $(document).ready(() => {
        // $("#employeeMobile").mask("999-999-9999");
        // $("#employeePostcode").mask("99999");
    });
    const app = new Vue({
        el: '#app',
        mounted: function() {
            this.loadBranch();
            this.loadPosition();
            this.loadProvince();
        },
        data: {
            createBy: {
                companyId: "<?= $_SESSION["usersLogin"]["companyId"] ?>",
                branchBy: "<?= $_SESSION["usersLogin"]["branchId"] ?>", //ต้องทำ login แยก ระหว่างคนที่มีสิทธิ์ดูได้ทุกสาขากับคนที่ดูได้เฉพาะสาขา
                loginBy: "<?= $_SESSION["usersLogin"]["companyUserId"] ?>",
                positionId: "<?= $_SESSION["usersLogin"]["positionId"] ?>",
            },
            form: {
                templateStep: 1,
            },
            employee: {
                employeeFirstname: "",
                employeeLastname: "",
                employeeNickname: "",
                employeeBirthday: "",
                employeeMobile: "",
                employeeEmail: "",
                employeeAddress: "",
                employeeProvinces: "",
                employeeAmphur: "",
                employeeTambon: "",
                employeePostcode: "",
                employeeSubId: "",
                employeeUsername: "",
                employeeDetail: "",
                employeeBranch: "",
                employeePosition: "",
                employeeImage: "",
            },
            provincesSelected: "",
            amphurSelected: "",
            branchData: [],
            positionData: [],
            provincesData: [],
            amphurData: [],
            tambonData: [],
            axiosConfig: {
                method: 'get',
                url: "",
                headers: {
                    'Accept': 'application/json',
                    // 'Authorization': 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiI5IiwianRpIjoiYTJiMzdhNGViZjU1NDRmMDliZDY2ZTk0NWJhMmQxNGE1Nzg3YTk2ZGMyYmMzNGMyNjk2OTgxMzc1OWFkOGYxYjk5MGUwNjJiNTgzYzRhM2YiLCJpYXQiOjE2MjAyNjkwMjIsIm5iZiI6MTYyMDI2OTAyMiwiZXhwIjoxNjUxODA1MDIyLCJzdWIiOiIxIiwic2NvcGVzIjpbXX0.do6kFU1NYBpP1CctpkOe0CK-ZRmQSttF6pK_mnX01Bgv2trq1aHmBKw26YDnWEYR-v3IxqzwTZ32ab5O1PGWRttH20hGUZi4MXQITJ7a1I_JJVnt5WLLR9TCy9yr9FkkvFtpFjyyjEwq2i9crIzWp1VEpIPj563kIUzRlqc7bth0whoXHPKDuxbW0h7K_Vuh9mSc79npKdR_jQdlGLAig9QSVPHGZizZfxsuzy5Cwz6wTpbQMWHm-7JEZIaQt-snW19NJ65fEM7MJU7sgxpN-cUnC13o9xtV7Nj8sq7PYIJo9XnTCg3w541ClAYFlQy1ZGTfurVAoD8_DAUX73fUYN30Jyb9jf-TlTaD4zfjnF784QX0Gpe0HY8aN2ApXVdsaSTk_vq-SPLP9hWgs1OMKxX7YrsBlq6adWtnqUBQ9sTnllskqn9mF3-U06F6VBxsjHJZv3gWD0ot2ivGhZWzs8BvTd5d62YCLaQZQsxgHbJbLFBO2YTWsHnP9TnAD6C23PZa0hmTfvsYXrqePAi2Wa7-UPOtF9wYSNzhhFqbLaQ2-MWjMDAmDfX8LQM4w1ZNBmcxS5pZG3sqdIMmU4KJ8WwImXlYbWv5P2JQXjA6sgFCDciARi0NP1Y_M4pWsK_ipkpESIxz9RRb3zabEDKtWgjgp_8DPi0swZ7VR56bIiM',
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                withCredentials: true
            },
            imageUrlPreview: "",
            imageSelectedIdCard: false,
            imageForSendIdCard: "",
            imageSelectedEmployee: false,
            imageForSendEmployee: "",
            checkFileUpload: "",
            imageUploadList: [{
                labelName: "รูปบัตรประชาชน",
                imageData: "",
                uploadType: "",
                imagePreview: "",
                imageSelected: false,
                imageName: "",

            }, {
                labelName: "รูปพนักงาน",
                imageData: "",
                uploadType: "",
                imagePreview: "",
                imageSelected: false,
                imageName: "",
            }],
            formErrorStep1: [],
            formErrorStep2: [],
            formErrorStep3: [],
        },
        computed: {
            videoSelector() {
                return document.querySelector('#video')
            }
        },
        methods: {
            loadBranch: function() {
                this.axiosConfig.url = `<?= urlTobackend ?>loadBranch.php?companyId=${btoa(this.createBy.companyId)}`;
                axios.defaults.withCredentials = true;
                axios(this.axiosConfig).then((response) => {
                    if (response.data.status) {
                        for (let i = 0; i < response.data.results.length; i++) {
                            this.branchData.push(response.data.results[i]);
                        }
                    } else {
                        console.log(response.data);
                    }
                }).catch((e) => {
                    console.log(e);
                });
            },
            loadPosition: function() {
                this.axiosConfig.url = `<?= urlTobackend ?>loadPosition.php?companyId=${btoa(this.createBy.companyId)}&branchId=${btoa(this.createBy.branchBy)}`;
                axios.defaults.withCredentials = true;
                axios(this.axiosConfig).then((response) => {
                    if (response.data.status) {
                        for (let i = 0; i < response.data.results.length; i++) {
                            this.positionData.push(response.data.results[i]);
                        }
                    } else {
                        console.log(response.data);
                    }
                }).catch((e) => {
                    console.log(e);
                });
            },
            loadProvince: function() {
                this.axiosConfig.url = `<?= urlTobackend ?>loadProvince.php?omega=${btoa("secret")}`;
                axios.defaults.withCredentials = true;
                axios(this.axiosConfig).then((response) => {
                    if (response.data.status) {
                        for (let i = 0; i < response.data.results.length; i++) {
                            this.provincesData.push(response.data.results[i]);
                        }
                    } else {
                        console.log(response.data);
                    }
                }).catch((e) => {
                    console.log(e);
                });
            },
            loadAmphur: function(provinceId) {
                this.axiosConfig.url = `<?= urlTobackend ?>loadAmphur.php?provinceId=${provinceId}&omega=${btoa("secret")}`;
                axios.defaults.withCredentials = true;
                axios(this.axiosConfig).then((response) => {
                    if (response.data.status) {
                        for (let i = 0; i < response.data.results.length; i++) {
                            this.amphurData.push(response.data.results[i]);
                        }
                    } else {
                        console.log(response.data);
                    }
                }).catch((e) => {
                    console.log(e);
                });
            },
            loadTambon: function(amphurId) {
                this.axiosConfig.url = `<?= urlTobackend ?>loadTambon.php?amphurId=${amphurId}&omega=${btoa("secret")}`;
                axios.defaults.withCredentials = true;
                axios(this.axiosConfig).then((response) => {
                    if (response.data.status) {
                        for (let i = 0; i < response.data.results.length; i++) {
                            this.tambonData.push(response.data.results[i]);
                        }
                    } else {
                        console.log(response.data);
                    }
                }).catch((e) => {
                    console.log(e);
                });
            },
            loadPostcode: function(tambonId) {
                this.axiosConfig.url = `<?= urlTobackend ?>loadPostcode.php?tambonId=${tambonId}&omega=${btoa("secret")}`;
                axios.defaults.withCredentials = true;
                axios(this.axiosConfig).then((response) => {
                    if (response.data.status) {
                        this.employee.employeePostcode = response.data.results[0].zip_code;
                    } else {
                        console.log(response.data);
                    }
                }).catch((e) => {
                    console.log(e);
                });
            },
            createImage(index, e) {
                const files = e.target.files || e.dataTransfer.files;
                if (!files.length) {
                    this.imageUploadList[index].imageData = "";
                    this.checkFile(index);
                    return;
                }
                const file = files[0];
                const image = new Image();
                const reader = new FileReader();
                this.imageUploadList[index].imageData = file; //ใช้ส่งไป backend
                if (!this.checkFile(index)) {
                    this.imageUploadList[index].imageData = "";
                    return;
                }
                reader.onload = (e) => {
                    this.imageUploadList[index].imagePreview = e.target.result;
                    this.imageUploadList[index].uploadType = "inputFile";
                };
                reader.readAsDataURL(file);
            },
            removeImage: function(index = "") {
                if (index == "") {
                    for (let i = 0; i < this.imageUploadList.length; i++) {
                        this.imageUploadList[i].imagePreview = "";
                        this.imageUploadList[i].imageData = "";
                        this.imageUploadList[i].uploadType = "";
                    }
                } else {
                    this.imageUploadList[index].imagePreview = "";
                    this.imageUploadList[index].imageData = "";
                    this.imageUploadList[index].uploadType = "";
                }

            },
            checkFile(index) {
                this.formErrorStep3 = [];
                if (this.imageUploadList[index].imageData == "") {
                    this.formErrorStep3.push("Picture can't empty.");
                    return false;
                }
                const allowFileType = [
                    "jpeg",
                    "png",
                    "jpg",
                ];
                const checkFileTypeArray = this.imageUploadList[index].imageData.name.split(".");
                let returnStatus = true;
                if (allowFileType.indexOf(checkFileTypeArray[checkFileTypeArray.length - 1]) <= 0) {
                    this.formErrorStep3.push("Picture type fail. Please upload type 'jpeg', 'png'!");
                    returnStatus = false;
                }
                if (this.imageUploadList[index].imageData.size > 3000000) {
                    this.formErrorStep3.push("Picture type fail. Please upload size less 4mb.");
                    returnStatus = false;
                }
                return returnStatus;
            },
            validate(JSONData) {
                const classCustomValidation = new CustomValidation();
                const optionCheckNumericOnly = [
                    "employeeFirstname",
                    "employeeLastname",
                    "employeeBirthday",
                    "employeeEmail",
                    "employeeSubId",
                    "employeeUsername",
                    "employeeDetail",
                    "employeeAddress",
                    "employeeIdCardImage",
                    "employeeProfileImage",
                ];
                const optionCheckStringOnly = [
                    "employeeMobile",
                    "employeeBranch",
                    "employeeProvinces",
                    "employeeAmphur",
                    "employeeTambon",
                    "employeePostcode",
                    "employeeBranch",
                    "employeePosition",
                    "employeeIdCardImage",
                    "employeeProfileImage",
                ];
                const setData = this.setDataForValidation(JSONData);
                const checkEmpty = classCustomValidation.checkEmpty(setData);
                const checkSpecialCharacter = classCustomValidation.checkSpecialCharacter(setData, [], /[ `!#$%^&*()+\=\[\]{};':"\\|,<>\/?~]/);
                const checkNumeric = classCustomValidation.checkNumeric(setData, optionCheckNumericOnly);
                const checkString = classCustomValidation.checkString(setData, optionCheckStringOnly);
                const setValidationErrorData = classCustomValidation.setValidationErrorData(checkEmpty, checkSpecialCharacter, checkNumeric, checkString);
                return setValidationErrorData;
            },
            setStepDataValidate(objectData, stepData) {
                let dataReturn = {};
                for (const [key, value] of Object.entries(objectData)) { //แปลง object เป็น array แล้ว ลูป get key + value
                    if (stepData.indexOf(key) >= 0) {
                        dataReturn[key] = value;
                    }
                }
                return dataReturn;
            },
            setDataForValidation(data) {
                // [name:11,detail:"11@"] รูปแบบที่ต้องการ array key : value
                let returnData = [];
                for (let index in data) {
                    returnData[index] = data[index];
                }
                return returnData;
            },
            validateLogic() {
                this.clearFormErrorStep();
                let step1Data = [
                    "employeeFirstname",
                    "employeeLastname",
                    "employeeBirthday",
                    "employeeMobile",
                    "employeeEmail",
                    "employeeAddress",
                    "employeeProvinces",
                    "employeeAmphur",
                    "employeeTambon",
                    "employeePostcode",
                ];
                let step2Data = [
                    "employeeSubId",
                    "employeeUsername",
                    "employeeDetail",
                    "employeeBranch",
                    "employeePosition",
                ];
                let step3Data = [
                    "employeeIdCardImage",
                    "employeeProfileImage",
                ];
                this.formErrorStep1 = this.validate(this.setStepDataValidate(this.employee, step1Data));
                this.formErrorStep2 = this.validate(this.setStepDataValidate(this.employee, step2Data));
                this.formErrorStep3 = this.validateImage();
                // this.employee.employeeImage = 

                if (this.formErrorStep1.length > 0) {
                    this.form.templateStep -= 2;
                } else if (this.formErrorStep2.length > 0) {
                    this.form.templateStep -= 1;
                }
                if (this.formErrorStep1.length > 0 || this.formErrorStep2.length > 0 || this.formErrorStep3.length > 0) return false;

                return true;
            },
            setImageForSendToBackend() {
                this.employee.employeeImage = this.imageUploadList; //เอาค่า image ใส่เตรียมส่ง
            },
            validateImage() {
                let returnData = [];
                for (let i = 0; i < this.imageUploadList.length; i++) {
                    if (this.imageUploadList[i].imageData == "") {
                        returnData.push("แนบรูป" + this.imageUploadList[i].labelName);
                    }
                }
                return returnData;
            },
            setDataForSendToBackend() {
                // set image for sent
                this.setImageForSendToBackend();
                // validate
                this.employee.employeeMobile = this.employee.employeeMobile.replace("-", ""); // ลบ - ของ Input mask plugin
                this.employee.employeeMobile = this.employee.employeeMobile.replace("-", ""); // ลบ - ของ Input mask plugin
                if (!this.validateLogic()) return;
                this.employee["createBy"] = { // เอา this.createBy รวมกับข้อมูลที่ต้องส่ง
                    companyId: this.createBy.companyId,
                    branchBy: this.createBy.branchBy,
                    loginBy: this.createBy.loginBy,
                    positionId: this.createBy.positionId,
                };
                Swal.fire({
                    title: `ยืนยันการเพิ่มพนักงาน ${this.employee.employeeFirstname} ${this.employee.employeeLastname}?`,
                    showDenyButton: true,
                    // showCancelButton: true,
                    confirmButtonText: `ยืนยัน`,
                    denyButtonText: `ยกเลิก`,
                }).then((result) => {
                    /* Read more about isConfirmed, isDenied below */
                    if (result.isConfirmed) {
                        let formData = new FormData();
                        const dataSendToBackend = JSON.stringify(this.employee);
                        // formData.append('pictureFile', this.imageForSend);
                        formData.append('jsonData', dataSendToBackend);
                        this.axiosConfig.url = `<?= urlTobackend ?>addEmployeeCompany.php`;
                        this.axiosConfig.method = `post`;
                        this.axiosConfig.headers = {
                            'Accept': 'application/json',
                            'Content-Type': 'multipart/form-data; application/x-www-form-urlencoded',
                        };
                        this.axiosConfig.data = formData;
                        axios(this.axiosConfig).then((response) => {
                            if (response.data.status) {
                                Swal.fire({
                                    title: response.data.message,
                                    text: response.data.message,
                                    confirmButtonText: `ปิด`,
                                    showLoaderOnConfirm: true,
                                    icon: "success",
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        this.requestCamera('cameraContainer', 'requestCamera', "close");
                                        this.clearFormErrorStep();
                                        this.clearPhotoImage();
                                        this.clearDataAfterSendToBackEnd();
                                        this.form.templateStep -= 2;
                                    }
                                })
                            } else {
                                // โชว์ step ที่ error
                                console.log(response.data);
                                this.formErrorStep1 = response.data.results.step1;
                                this.formErrorStep2 = response.data.results.step2;
                                this.formErrorStep3 = response.data.results.step3;
                                if (this.formErrorStep1.length > 0) {
                                    this.form.templateStep -= 2;
                                } else if (this.formErrorStep2.length > 0) {
                                    this.form.templateStep -= 1;
                                }

                            }
                        }).catch((e) => {
                            console.log(e);
                        });
                    } else if (result.isDenied) {
                        Swal.fire('ตรวจสอบรายการให้เรียบร้อย', '', 'info')
                    }
                })
            },
            async requestCamera(elemCameraId, button, status = "") {
                if (status == "close" && buttonId.innerHTML == "เปิดกล้อง") return;
                let stream = await this.startCamera();
                let video = document.querySelector("#" + elemCameraId); //เรียกใช้ใน compute
                let buttonId = document.querySelector("#" + button);
                if (buttonId.innerHTML == "เปิดกล้อง") {
                    buttonId.classList.add("btn-danger");
                    video.classList.remove("d-none");
                    buttonId.innerHTML = "ปิดกล้อง/เลือกรูป";
                    video.srcObject = stream;
                    this.showAndHideTakePhotoOrChoosePhoto(1);
                } else {
                    buttonId.classList.remove("btn-danger");
                    video.classList.add("d-none");
                    buttonId.innerHTML = "เปิดกล้อง";
                    this.stopBothVideoAndAudio(stream);
                    this.clearPhotoImage();
                    this.showAndHideTakePhotoOrChoosePhoto(0);
                }

            },
            takePhoto(elemCameraId, photoId, button, imageUploadListIndex) {
                let requestCamera = document.querySelector("#requestCamera");
                if (requestCamera.innerHTML == "เปิดกล้อง") return;
                let buttonId = document.querySelector("#" + button);

                if (buttonId.innerHTML == "ถ่ายรูป") {
                    buttonId.classList.add("btn-danger");
                    buttonId.innerHTML = "ลบรูป";
                    this.showTakePhotoImage(elemCameraId, photoId, button, 1, imageUploadListIndex); // 1 = takePhoto success
                } else {
                    buttonId.classList.remove("btn-danger");
                    buttonId.innerHTML = "ถ่ายรูป";
                    this.showTakePhotoImage(elemCameraId, photoId, button, 0, imageUploadListIndex); // 0 = takePhoto failed
                }

            },
            showTakePhotoImage(elemCameraId, photoId, button, status, imageUploadListIndex) {
                let canvas = document.querySelector("#" + photoId);
                if (status == 0) {
                    canvas.classList.add("d-none");
                    this.clearImageDataIfDeleteTakePhotoClick(imageUploadListIndex);
                } else {
                    canvas.classList.remove("d-none");
                    let video = document.querySelector("#" + elemCameraId);
                    canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
                    let imageDataUrl = canvas.toDataURL('image/jpeg');
                    this.imageUploadList[imageUploadListIndex].imageData = imageDataUrl;
                    this.imageUploadList[imageUploadListIndex].uploadType = "canvas";
                }

            },
            async startCamera() {
                let stream = await navigator.mediaDevices.getUserMedia({
                    video: true,
                    audio: false
                });
                return stream;
            },
            stopBothVideoAndAudio(stream) {
                stream.getTracks().forEach(function(track) {
                    if (track.readyState == 'live') {
                        track.stop();
                    }
                });
            },
            showAndHideTakePhotoOrChoosePhoto(status) {
                let chooseImage = document.querySelectorAll(".chooseImage");
                let takePhoto = document.querySelectorAll(".take-photo-container");
                chooseImage.forEach((element, index) => {
                    if (status == 1) {
                        element.classList.add("d-none");

                    } else {
                        element.classList.remove("d-none");
                    }

                });
                takePhoto.forEach((element, index) => {
                    if (status == 1) {
                        element.classList.remove("d-none");
                    } else {
                        element.classList.add("d-none");
                    }
                });
            },
            clearImageDataIfDeleteTakePhotoClick(index) {
                this.imageUploadList[index].imageData = "";
                this.employee.employeeImage[index].imageData = "";
            },
            clearPhotoImage() {
                // clear image data sent to backend
                // มี 2 รูปที่ต้อง upload แต่ถ้า ลบรูปใดรูปหนึ่งออกก็ให้เอาออกหมดเลย เพราะยังไงก็ส่งไปไม่ได้ต้องให้ส่ง 2 รูปเท่านั้น ง่ายดี
                for (let i = 0; i < this.imageUploadList.length; i++) {
                    this.imageUploadList[i].imageData = "";
                }
                this.employee.employeeImage = [];
                let canvas = document.querySelectorAll("canvas");
                let btnTakePhoto = document.querySelectorAll(".btn-take-photo");
                let canvasTakePhoto = document.querySelectorAll(".canvas-take-photo");
                canvas.forEach((element, index) => {
                    element.getContext('2d').clearRect(0, 0, element.width, element.height);
                });
                btnTakePhoto.forEach((element, index) => {
                    element.classList.remove("btn-danger");
                    element.innerHTML = "ถ่ายรูป";
                });
                canvasTakePhoto.forEach((element, index) => {
                    element.classList.add("d-none");
                });
            },
            clearFormErrorStep() {
                this.formErrorStep1 = [];
                this.formErrorStep2 = [];
                this.formErrorStep3 = [];
            },
            clearDataAfterSendToBackEnd() {
                this.employee.employeeFirstname = "";
                this.employee.employeeLastname = "";
                this.employee.employeeNickname = "";
                this.employee.employeeBirthday = "";
                this.employee.employeeMobile = "";
                this.employee.employeeEmail = "";
                this.employee.employeeAddress = "";
                this.employee.employeeProvinces = "";
                this.employee.employeeAmphur = "";
                this.employee.employeeTambon = "";
                this.employee.employeePostcode = "";
                this.employee.employeeSubId = "";
                this.employee.employeeUsername = "";
                this.employee.employeeDetail = "";
                this.employee.employeeBranch = "";
                this.employee.employeePosition = "";
                this.employee.employeeImage = "";
                for (let i = 0; i < this.imageUploadList.length; i++) {
                    this.imageUploadList[i].imageData = "";
                    this.imageUploadList[i].imagePreview = "";
                    this.imageUploadList[i].imageSelected = false;
                    this.imageUploadList[i].imageName = "";
                }
            },
        }
    });
</script>