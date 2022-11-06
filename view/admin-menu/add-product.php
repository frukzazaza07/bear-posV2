<?php require_once("template.php"); ?>

<?= templateHeader; ?>
<link rel="stylesheet" href="../../css/add-prodcut.css">
<?= templateBody; ?>
<div class="content-wrapper p-4" id="app" v-bind:class="[templateStep == 2 || templateStep == 3 || formErrorStep1.length > 0 ? 'overflow-y-custom' : '',]">
    <div class="content-header pb-1">
        <h4>เพิ่มสินค้าใหม่</h4>
        <hr>
    </div>
    <div class="content-body">
        <div class="container-fluid content-custom" style="position: relative;">
            <form class="add-new-product-form pb-4" id="addNewProductForm" method="POST" enctype="multipart/form-data">
                <div class="row form-step px-2 pb-3" id="step1" v-bind:class="[templateStep == 1 ? 'step-show' : 'step-hide',]">
                    <h5 class="mb-2">ข้อมูลสินค้า</h5>
                    <div class="col-6">
                        <div class="form-group">
                            <label for="">รหัสสินค้า</label>
                            <input type="text" class="form-control" placeholder="p-000001" v-model="dataSend.productCode">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label for="">ชื่อสินค้า</label>
                            <input type="text" class="form-control" placeholder="ซาลาเปาหมู" v-model="dataSend.productName">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label for="">รายละเอียดสินค้า</label>
                            <textarea type="text" class="form-control" placeholder="รับมาขายชั่วคราว" v-model="dataSend.productDetail"></textarea>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="form-group">
                            <label for="">ประเภทสินค้า</label>
                            <select type="text" class="form-control" v-model="dataSend.productType">
                                <option v-for="(item,index) in dataAllProductType" :value="item.type_id">{{ item.type_name }}</option>
                            </select>
                            <small class="text-danger"><b>ประเภทสินค้ามีผลกับหน้ารายการขาย <br>สินค้าจะถูกจัดไปอยู่หมวดหมู่ที่เลือก</b></small>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="form-group">
                            <label for="">วันหมดอายุ</label>
                            <div class="col-12">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" id="inlineCheckbox1" value="1" v-model="expiredDatePicked" @change.prevent="[expiredChecked = false,]">
                                    <label class="form-check-label" for="inlineCheckbox1">ไม่ระบุ</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" id="inlineCheckbox2" value="2" v-model="expiredDatePicked" @change.prevent="[expiredChecked = true,]">
                                    <label class="form-check-label" for="inlineCheckbox2">ระบุ</label>
                                </div>
                            </div>

                            <input type="text" class="form-control" placeholder="2021-12-31" v-if="expiredChecked" v-model="dataSend.productExpired">
                            <small class="text-danger"><b>ถ้ากำหนดวันหมดอายุ<br>สินค้าจะหายไปจากหน้าการขายอัตโนมัติเมื่อครบกำหนด</b></small>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="form-group">
                            <label for="">ระบุสาขา</label>
                            <select type="text" class="form-control" placeholder="" v-model="dataSend.productBranch">
                                <option value="0">ทุกสาขา</option>
                                <option v-for="(item,index) in dataAllBranch" :value="item.branch_id">{{ item.branch_name }}</option>
                            </select>
                            <small class="text-danger"><b>สินค้าจะแสดงตามสาขาที่เลือก</b></small>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="form-group">
                            <label for="">ราคาขาย</label>
                            <input type="text" class="form-control" placeholder="100" v-model="dataSend.productPrice">
                            <small class="text-danger"><b>สินค้าจะแสดงราคาตามที่ระบุ</b></small>
                        </div>
                    </div>
                    <div class="col-12 row justify-content-center" v-if="formErrorStep1.length > 0">
                        <div class="col-12 col-md-6">
                            <div class="alert alert-danger">
                                <ul style="margin-bottom: 0px;">
                                    <li v-for="(item, index) in formErrorStep1">{{ item }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 text-center">
                        <input type="button" class="btn btn-primary" value="ถัดไป" @click.prevent="[nextStep(),]">
                    </div>
                </div>
                <!-- step2 -->
                <div class="form-step px-2" id="step2" v-bind:class="[templateStep == 2 ? 'step-show' : 'step-hide d-none',]">
                    <h5 class="mb-2">ตั้งค่าตัดสต็อก</h5>
                    <div class="row materialList-container" v-for="(item,index) in materialList">
                        <div class="col-12 d-flex">
                            <ul class="d-flex justify-content-between p-0 m-0" style="list-style: none; width: 100%">
                                <li class="step2-li-action">{{index+1}}</li>
                                <li class="step2-li-action text-danger" style="cursor: pointer;" @click="deleteStep2Template(index)"><b>X</b></li>
                            </ul>
                        </div>
                        <div class="col-6 col-lg-4">
                            <div class="form-group">
                                <label for="">เลือกวัตถุดิบ</label>
                                <select class="form-control" placeholder="" v-model="item.materialSelect">
                                    <option v-for="(item,index) in dataAllMaterial" :value="item.material_id">{{ item.material_name }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3">
                            <div class="form-group">
                                <label for="">จำนวนที่ตัดสต็อก</label>
                                <input type="text" class="form-control" placeholder="เช่นถ้าตัดที่ละชิ้นให้ใส่เลข 1" v-model="item.stockCutAmount">
                            </div>
                        </div>
                        <div class="col-6 col-lg-2">
                            <div class="form-group">
                                <label for="">หน่วยในการตัดสต็อก</label>
                                <select class="form-control" placeholder="ชิ้น, cc, ลิตร, กรัม" v-model="item.stockCutUnitSelect">
                                    <option value="piece">ชิ้น</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3">
                            <div class="form-group">
                                <label for="">ระบุสาขา</label>
                                <select type="text" class="form-control" placeholder="" v-model="item.stockCutBranch">
                                    <option value="0">ทุกสาขา</option>
                                    <option v-for="(item,index) in dataAllBranch" :value="item.branch_id">{{ item.branch_name }}</option>
                                </select>
                                <small class="text-danger"><b>สินค้าจะตัดสต็อกเฉพาะตามสาขาที่เลือก</b></small>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="">รายละการตัดสต็อก</label>
                                <textarea class="form-control" placeholder="ตัดสต็อกเป็นชิ้น" v-model="item.stockCutDetail"></textarea>
                            </div>
                        </div>
                        <!-- <div class="col-6 col-lg-3">
                            <div class="form-group">
                                <label for="">วันหมดอายุ</label>
                                <div class="col-12">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" id="inlineCheckbox1" v-model="picked" value="1">
                                        <label class="form-check-label" for="inlineCheckbox1">ไม่ระบุ</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" id="inlineCheckbox2" v-model="picked" value="2">
                                        <label class="form-check-label" for="inlineCheckbox2">ระบุ</label>
                                    </div>
                                </div>
                                <input type="text" class="form-control" placeholder="2021-12-31" v-if="expiredChecked" v-for="item.materialDateExpired">
                                <small class="text-danger"><b>ถ้ากำหนดวันหมดอายุ<br>สินค้าจะหายไปจากหน้าการขายอัตโนมัติเมื่อครบกำหนด</b></small>
                            </div>
                        </div> -->
                        <input type="hidden" v-model="item.cutStockId = (index + 1)">
                        <!-- end loop -->
                    </div>
                    <div class="col-12 row justify-content-center" v-if="formErrorStep2.length > 0">
                        <div class="col-12 col-md-6">
                            <div class="alert alert-danger">
                                <ul style="margin-bottom: 0px;">
                                    <li v-for="(item, index) in formErrorStep2">{{ item }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 text-center mb-3">
                        <div class="col-12 text-center mb-2">
                            <input type="button" class="btn btn-success" value="เพิ่มวัตถุดิบ" @click.prevent="[addStep2Template()]">
                        </div>
                        <input type="button" class="btn btn-secondary" value="ก่อนหน้า" @click.prevent="[prevStep()]">
                        <input type="button" class="btn btn-primary" value="ถัดไป" @click.prevent="[nextStep()]">
                    </div>
                </div>
                <!-- end step 2 -->
                <!-- step 3 -->
                <div class="form-step px-2" id="step3" v-bind:class="[templateStep == 3 ? 'step-show' : 'step-hide d-none',]">
                    <h5 class="mb-2">รูปสินค้า</h5>
                    <div class="mt-3">
                        <div class="product-preview-container" id="preview">
                            <div v-if="!imageSelected">
                                <label for="formFile" class="form-label">เลือกรูปสินค้า</label>
                                <input class="form-control" type="file" @change="previewImage(imageSelected, $event)">
                            </div>
                            <div class="text-center" v-else>
                                <img class="product-preview-img mb-2" :src="imageSelected" />
                                <div><strong>{{ imageForSend.name }}</strong></div>
                                <button class="btn btn-danger mt-1" @click.prevent="removeImage(imageSelected)">ลบรูป</button>
                            </div>
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
                    <div class="col-12 text-center" v-bind:class="[formErrorStep3.length == 0 ? 'mt-3 mb-3' : '']">
                        <input type="button" class="btn btn-secondary" value="ก่อนหน้า" @click.prevent="[prevStep()]">
                        <button type="submit" class="btn btn-success" @click.prevent="setDataForSendToBackend()">เพิ่มสินค้า</button>
                    </div>
                </div>
            </form>
        </div><!-- /.container-fluid -->
    </div>
</div>
<?= templateFootter ?>
<script src="https://cdn.jsdelivr.net/npm/vue@2"></script>
<script src="/bear/js/customValidation.js"></script>
<script>
    const app = new Vue({
        el: '#app',
        data: {
            companyBy: "<?= $_SESSION["usersLogin"]["companyId"] ?>",
            branchBy: "<?= $_SESSION["usersLogin"]["branchId"] ?>", //ต้องทำ login แยก ระหว่างคนที่มีสิทธิ์ดูได้ทุกสาขากับคนที่ดูได้เฉพาะสาขา
            loginBy: "<?= $_SESSION["usersLogin"]["companyUserId"] ?>",
            expiredChecked: false,
            templateStep: 1,
            backupTemplateStep: this.templateStep,
            hideElemClass: "",
            materialList: [{}],
            materialCount: 1,
            expiredDatePicked: "1",
            imageUrlPreview: "",
            imageSelected: false,
            imageForSend: "",
            materialList: [{
                cutStockId: "",
                materialSelect: "",
                stockCutAmount: "",
                stockCutUnitSelect: "",
                stockCutDetail: "",
                stockCutBranch: "",

            }],
            axiosConfig: {
                method: 'get',
                url: "",
                headers: {
                    'Accept': 'application/json',
                    // 'Authorization': 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiI5IiwianRpIjoiYTJiMzdhNGViZjU1NDRmMDliZDY2ZTk0NWJhMmQxNGE1Nzg3YTk2ZGMyYmMzNGMyNjk2OTgxMzc1OWFkOGYxYjk5MGUwNjJiNTgzYzRhM2YiLCJpYXQiOjE2MjAyNjkwMjIsIm5iZiI6MTYyMDI2OTAyMiwiZXhwIjoxNjUxODA1MDIyLCJzdWIiOiIxIiwic2NvcGVzIjpbXX0.do6kFU1NYBpP1CctpkOe0CK-ZRmQSttF6pK_mnX01Bgv2trq1aHmBKw26YDnWEYR-v3IxqzwTZ32ab5O1PGWRttH20hGUZi4MXQITJ7a1I_JJVnt5WLLR9TCy9yr9FkkvFtpFjyyjEwq2i9crIzWp1VEpIPj563kIUzRlqc7bth0whoXHPKDuxbW0h7K_Vuh9mSc79npKdR_jQdlGLAig9QSVPHGZizZfxsuzy5Cwz6wTpbQMWHm-7JEZIaQt-snW19NJ65fEM7MJU7sgxpN-cUnC13o9xtV7Nj8sq7PYIJo9XnTCg3w541ClAYFlQy1ZGTfurVAoD8_DAUX73fUYN30Jyb9jf-TlTaD4zfjnF784QX0Gpe0HY8aN2ApXVdsaSTk_vq-SPLP9hWgs1OMKxX7YrsBlq6adWtnqUBQ9sTnllskqn9mF3-U06F6VBxsjHJZv3gWD0ot2ivGhZWzs8BvTd5d62YCLaQZQsxgHbJbLFBO2YTWsHnP9TnAD6C23PZa0hmTfvsYXrqePAi2Wa7-UPOtF9wYSNzhhFqbLaQ2-MWjMDAmDfX8LQM4w1ZNBmcxS5pZG3sqdIMmU4KJ8WwImXlYbWv5P2JQXjA6sgFCDciARi0NP1Y_M4pWsK_ipkpESIxz9RRb3zabEDKtWgjgp_8DPi0swZ7VR56bIiM',
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                withCredentials: true,
                data: undefined,
            },
            dataAllMaterial: [],
            dataAllProductType: [],
            dataAllBranch: [],
            dataSend: {
                userCreate: "<?= $_SESSION["usersLogin"]["companyUserId"] ?>",
                productCode: "",
                productName: "",
                productDetail: "",
                productType: "",
                productExpired: "",
                productBranch: "",
                productCompany: "<?= $_SESSION["usersLogin"]["companyId"] ?>",
                productPrice: "",
                productMaterialList: [],
            },
            checkFileUpload: "",
            formError: true,
            formErrorStep1: [],
            formErrorStep2: [],
            formErrorStep3: [],
        },
        mounted: function() {
            this.setFormHeight();
            this.getAllBranch();
            this.getAllProductType();
            this.getAllMaterial();
        },
        methods: {
            async getAllBranch() {
                this.axiosConfig.url = `<?= urlTobackend ?>loadBranch.php?companyId=${btoa(this.companyBy)}`;
                const response = await this.fetchDataDynamicUrl();
                this.dataAllBranch = response.data.results;
            },
            async getAllProductType() {
                this.axiosConfig.url = `<?= urlTobackend ?>loadTypeProduct.php?companyId=${this.companyBy}&branchId=${this.branchBy}`;
                const response = await this.fetchDataDynamicUrl();
                this.dataAllProductType = response.data.results;
            },
            async getAllMaterial() {
                this.axiosConfig.url = `<?= urlTobackend ?>loadMaterial.php?companyId=${this.companyBy}&branchId=${this.branchBy}`;
                const response = await this.fetchDataDynamicUrl();
                this.dataAllMaterial = response.data.results;
            },
            fetchDataDynamicUrl() {
                axios.defaults.withCredentials = true;
                return axios(this.axiosConfig);
            },
            hideElem() {
                this.hideElemClass = "";
                // this.expiredChecked = false; ก่อนหน้านี้ซ่อน
                setTimeout(() => document.getElementById("step" + app.backupTemplateStep).classList.add("d-none"), 400);
                setTimeout(() => document.getElementById("step" + app.templateStep).classList.add("position-relative"), 700);
            },
            nextStep() {
                this.backupTemplateStep = this.templateStep;
                this.hideElem();
                this.templateStep += 1;

            },
            prevStep() {
                this.backupTemplateStep = this.templateStep;
                this.hideElem();
                this.templateStep -= 1;
            },
            addStep2Template() {
                this.materialList.push({
                    cutStockId: "",
                    materialSelect: "",
                    stockCutAmount: "",
                    stockCutUnitSelect: "",
                    stockCutDetail: "",
                    stockCutBranch: "",

                });
            },
            deleteStep2Template(index) {
                if (this.materialList.length == 1) return
                this.materialList.splice(index, 1);
            },
            showList() {
                console.log(this.materialList);
            },
            setFormHeight() {
                // document.getElementById("addNewProductForm").style.height = (document.getElementById("app").offsetHeight / 2) + "px";
            },
            previewImage(item, e) {
                const files = e.target.files || e.dataTransfer.files;
                if (!files.length) {
                    this.imageForSend = "";
                    this.checkFile();
                    return;
                }

                this.createImage(item, files[0]);
            },
            createImage(item, file) {
                const image = new Image();
                const reader = new FileReader();
                this.imageForSend = file;
                if (!this.checkFile()) return;
                reader.onload = (e) => {
                    this.imageSelected = e.target.result;
                };
                reader.readAsDataURL(file);
            },
            removeImage: function(item) {
                this.imageForSend = "";
                this.imageSelected = false;
            },
            checkForm() {
                let formErrorCheck = true;
                this.resetFormError(); //set empty
                // checkstep1
                this.checkValidate(this.dataSend, "1");
                //checkstep2
                this.checkValidate(this.materialList, "2");
                //  checkstep3
                this.checkFile();

                if (this.formErrorStep1.length > 0) {
                    this.prevStep();
                    this.prevStep();
                    formErrorCheck = false;
                } else if (this.formErrorStep2.length > 0) {
                    this.prevStep();
                    formErrorCheck = false;
                } else if (this.formErrorStep3.length > 0) {
                    formErrorCheck = false;
                }
                this.formError = formErrorCheck;

                return formErrorCheck;
            },
            checkDataSend() {
                if (this.dataSend.productCode == "") this.formErrorStep1.push("productCode is require");
                if (this.dataSend.productName == "") this.formErrorStep1.push("productName is require");
                if (this.dataSend.productDetail == "") this.formErrorStep1.push("productDetail is require");
                if (this.dataSend.productType == "") this.formErrorStep1.push("productType is require");
                if (this.dataSend.productExpired == "" && this.expiredDatePicked == "2") this.formErrorStep1.push("productExpired is require");
                if (this.dataSend.productBranch == "") this.formErrorStep1.push("productBranch is require");
                if (this.dataSend.productCompany != "<?= $_SESSION["usersLogin"]["companyId"] ?>") this.formErrorStep1.push("productCompany can't edit");
                if (this.dataSend.productPrice == "") this.formErrorStep1.push("productPrice is require");
            },
            checkproductMaterialList() {
                let returnValue;
                let productMaterialListError = [];
                if (this.materialList.length == 0) {
                    this.formErrorStep2.push("Material can't empty.");
                }
                for (let i = 0; i < this.materialList.length; i++) {
                    let checkError = true;
                    if (this.materialList[i].cutStockId == "") checkError = false;
                    if (this.materialList[i].materialSelect == "") checkError = false;
                    if (this.materialList[i].stockCutAmount == "") checkError = false;
                    if (this.materialList[i].stockCutUnitSelect == "") checkError = false;
                    if (this.materialList[i].stockCutDetail == "") checkError = false;
                    if (this.materialList[i].stockCutBranch == "") checkError = false;
                    if (!checkError) {
                        this.formErrorStep2.push("Material #" + (i + 1) + " have empty input.");
                    }
                }

                return productMaterialListError;
            },

            checkFile() {
                this.formErrorStep3 = [];
                if (this.imageForSend == "") {
                    this.formErrorStep3.push("Picture can't empty.");
                    return false;
                }
                const allowFileType = [
                    "jpeg",
                    "png",
                    "jpg",
                ];
                const checkFileTypeArray = this.imageForSend.name.split(".");
                let returnStatus = true;
                if (allowFileType.indexOf(checkFileTypeArray[checkFileTypeArray.length - 1]) > 0) {
                    console.log(this.imageForSend.name);
                } else {
                    this.formErrorStep3.push("Picture type fail. Please upload type 'jpeg', 'png'!");
                    returnStatus = false;
                }
                return returnStatus;
            },
            checkValidate(validateData, stepCheck) {
                let returnStatus = true;
                const classCustomValidation = new CustomValidation();
                const optionCheckDateExpiredOnly = ["productExpired", "productMaterialList"];
                const optionCheckSpecialCharacterOnly = ["productMaterialList"];
                const optionCheckNumericOnly = [
                    "productCode",
                    "productName",
                    "productDetail",
                    "productExpired",
                    "productMaterialList",
                    "stockCutUnitSelect",
                    "stockCutDetail",
                ];
                const optionCheckStringOnly = [
                    "productCode",
                    "productBranch",
                    "productCompany",
                    "productPrice",
                    "productMaterialList",
                    "stockCutAmount ",
                    "cutStockId",
                ];
                const setData = this.setDataForValidation(validateData);
                const checkEmpty = classCustomValidation.checkEmpty(setData, optionCheckDateExpiredOnly);
                const checkSpecialCharacter = classCustomValidation.checkSpecialCharacter(setData, optionCheckSpecialCharacterOnly, /[ `!@#$%^&*()+\=\[\]{};':"\\|,<>\/?~]/);
                const checkNumeric = classCustomValidation.checkNumeric(setData, optionCheckNumericOnly);
                const checkString = classCustomValidation.checkString(setData, optionCheckStringOnly);
                const setValidationErrorData = classCustomValidation.setValidationErrorData(checkEmpty, checkSpecialCharacter, checkNumeric, checkString);
                // const setValidationErrorData = classCustomValidation.setValidationErrorData(checkNumeric);
                if (setValidationErrorData.length > 0) {
                    returnStatus = false;
                    if (stepCheck == "1") {
                        this.pushValidateError1(setValidationErrorData);
                    } else if (stepCheck == "2") {
                        this.pushValidateError2(setValidationErrorData);
                    }

                }

                return returnStatus;
            },
            pushValidateError1(errorData) {
                if (errorData.length > 0) {
                    errorData.forEach((value, index) => {
                        this.formErrorStep1.push(value);
                    });
                }

            },
            pushValidateError2(errorData) {
                if (errorData.length > 0) {
                    errorData.forEach((value, index) => {
                        this.formErrorStep2.push(value);
                    });
                }

            },
            setDataForValidation(data) {
                // [name:11,detail:"11@"] รูปแบบที่ต้องการ array key : value
                let returnData = [];
                for (let index in data) {
                    returnData[index] = data[index];
                }
                return returnData;
            },
            resetFormError() {
                this.formErrorStep1 = [];
                this.formErrorStep2 = [];
                this.formErrorStep3 = [];
            },
            setDataForSendToBackend() {
                if (!this.checkForm()) return;
                Swal.fire({
                    title: `ยืนยันการเพิ่มสินค้า ${this.dataSend.productName}(${this.dataSend.productCode})?`,
                    showDenyButton: true,
                    // showCancelButton: true,
                    confirmButtonText: `ยืนยัน`,
                    denyButtonText: `ยกเลิก`,
                }).then((result) => {
                    /* Read more about isConfirmed, isDenied below */
                    if (result.isConfirmed) {
                        this.dataSend.productMaterialList = this.materialList;
                        let formData = new FormData();
                        const dataSendToBackend = JSON.stringify(this.dataSend);
                        formData.append('pictureFile', this.imageForSend);
                        formData.append('dataSendJson', dataSendToBackend);
                        this.axiosConfig.url = `<?= urlTobackend ?>addProducts.php`;
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
                                        this.clearDataAfterSendToBackEnd();
                                        this.removeImage();
                                        this.prevStep();
                                        this.prevStep();
                                    }
                                })
                            } else {
                                console.log(response.data);
                            }
                        }).catch((e) => {
                            console.log(e);
                        });
                    } else if (result.isDenied) {
                        Swal.fire('ตรวจสอบรายการให้เรียบร้อย', '', 'info')
                    }
                })
            },
            clearDataAfterSendToBackEnd() {
                this.materialList = [{
                    cutStockId: "",
                    materialSelect: "",
                    stockCutAmount: "",
                    stockCutUnitSelect: "",
                    stockCutDetail: "",
                    stockCutBranch: "",

                }];
                this.imageForSend = "";
                this.dataSend = {
                    userCreate: "<?= $_SESSION["usersLogin"]["companyUserId"] ?>",
                    productCode: "",
                    productName: "",
                    productDetail: "",
                    productType: "",
                    productExpired: "",
                    productBranch: "",
                    productCompany: "<?= $_SESSION["usersLogin"]["companyId"] ?>",
                    productPrice: "",
                    productMaterialList: [],
                };
            }
        }
    });
</script>
<?= templateFootter; ?>