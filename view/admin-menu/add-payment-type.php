<?php require_once("template.php"); ?>

<?= templateHeader; ?>
<link rel="stylesheet" href="../../css/add-payment-type.css">
<?= templateBody; ?>
<div class="content-wrapper p-4" id="app">
    <div class="content-header pb-1">
        <h4>เพิ่มประเภทการชำระเงินใหม่</h4>
        <hr>
    </div>
    <div class="content-body">
        <div class="container-fluid content-custom" style="position: relative;">
            <form class="add-new-payment-type-form pb-4" id="addNewPaymentTypeForm" method="POST" enctype="multipart/form-data">
                <div class="row form-step px-2 pb-3" id="step1">
                    <h5 class="mb-2">ข้อมูลประเภทการชำระเงิน</h5>
                    <div class="col-6">
                        <div class="form-group">
                            <label for="">รหัสประเภทการชำระเงิน</label>
                            <input type="text" class="form-control" id="paymentTypeSubId" name="paymentTypeSubId" placeholder="p-000001">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label for="">ชื่อประเภทการชำระเงิน</label>
                            <input type="text" class="form-control" id="paymentTypeName" name="paymentTypeName" placeholder="ซาลาเปา">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label for="">รายละเอียดประเภทการชำระเงิน</label>
                            <textarea type="text" class="form-control" id="paymentTypeDetail" name="paymentTypeDetail" placeholder="ประเภทการชำระเงินสำหรับสินค้าใหม่"></textarea>
                        </div>
                    </div>
                    <!-- เผื่อมี -->
                    <!-- <div class="col-6 col-lg-3">
                        <div class="form-group">
                            <label for="">ประเภทประเภทการชำระเงิน</label>
                            <select type="text" class="form-control">
                                <option></option>
                            </select>
                            <small class="text-danger"><b>ประเภทการชำระเงินมีผลกับหน้ารายการขาย <br>สินค้าจะถูกจัดไปอยู่หมวดหมู่ที่เลือก</b></small>
                        </div>
                    </div> -->
                    <div class="col-6 col-lg-3">
                        <div class="form-group">
                            <label for="">วันหมดอายุ</label>
                            <div class="col-12">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" id="inlineCheckbox1" name="dateExpiredSelect" value="1" checked>
                                    <label class="form-check-label" for="inlineCheckbox1">ไม่ระบุ</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" id="inlineCheckbox2" name="dateExpiredSelect" value="2">
                                    <label class="form-check-label" for="inlineCheckbox2">ระบุ</label>
                                </div>
                            </div>

                            <input type="text" class="form-control d-none" id="paymentTypeDateExpired" name="paymentTypeDateExpired" placeholder="2021-12-31">
                            <small class="text-danger"><b>ถ้ากำหนดวันหมดอายุ<br>ประเภทการชำระเงินจะหายไปจากรายการประเภทการชำระเงินอัตโนมัติเมื่อครบกำหนด</b></small>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="form-group">
                            <label for="">ระบุสาขา</label>
                            <select type="text" class="form-control" id="paymentTypeBranch" name="paymentTypeBranch" placeholder="">
                                <option value="0">ทุกสาขา</option>
                            </select>
                            <small class="text-danger"><b>ประเภทการชำระเงินจะแสดงเฉพาะสาขาที่เลือก</b></small>
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
                    <div class="col-12 text-center">
                        <input type="submit" class="btn btn-primary" value="เพิ่มประเภทการชำระเงิน">
                    </div>
                </div>
                <!-- step1 end -->
            </form>
        </div><!-- /.container-fluid -->
    </div>
</div>
<?= templateFootter ?>
<script src="/bear/js/customValidation.js"></script>
<script>
    const companyId = "<?= $_SESSION["usersLogin"]["companyId"] ?>";
    const branchBy = "<?= $_SESSION["usersLogin"]["branchId"] ?>"; //ต้องทำ login แยก ระหว่างคนที่มีสิทธิ์ดูได้ทุกสาขากับคนที่ดูได้เฉพาะสาขา
    const loginBy = "<?= $_SESSION["usersLogin"]["companyUserId"] ?>";
    $(document).ready(() => {
        getAllBranch();
        $("#paymentTypeBranch").val("0");
    });
    $("input[name=dateExpiredSelect]").change((e) => {
        if ($('input[name=dateExpiredSelect]:checked').val() == "2") {
            $("#paymentTypeDateExpired").removeClass("d-none");
        } else {
            $("#paymentTypeDateExpired").addClass("d-none");
        }
    });
    $("#addNewPaymentTypeForm").submit((e) => {
        e.preventDefault();
        sendDataToBackend();
    });
    async function getAllBranch() {
        const response = await $.ajax({
            url: `<?= urlTobackend ?>loadBranch.php?companyId=${btoa(companyId)}`,
            type: "GET",
            dataType: 'json',
            error: (err) => {
                console.log(err)
            }

        });
        const setData = await setBranchData(response.results);
        const showData = await addOptionDataToSelectElem("#paymentTypeBranch", setData);
    }

    function setBranchData(data) {
        let returnData = [];
        data.forEach((value, index) => {
            returnData.push(
                [value.branch_id, value.branch_name]
            )
        })
        return returnData;
    }

    function addOptionDataToSelectElem(elemId, data) {
        // ส่ง array เข้ามาโดย key 0 =value ของ option; key 1 = text show
        let optionForAppend = "";
        data.forEach((value, index) => {
            optionForAppend += `<option value="${value[0]}">${value[1]}</option>`
        })
        $(elemId).append(optionForAppend);
    }

    function sendDataToBackend() {
        let JSONData = {
            companyId: companyId,
            createBy: loginBy,
            paymentTypeSubId: $("#paymentTypeSubId").val(),
            paymentTypeName: $("#paymentTypeName").val(),
            paymentTypeDetail: $("#paymentTypeDetail").val(),
            paymentTypeDateExpired: $("#paymentTypeDateExpired").val(),
            paymentTypeBranch: $("#paymentTypeBranch").val(),
            paymentTypeJson: "",
        }
        JSONData.paymentTypeJson = JSON.stringify(JSONData);
        const checkValidate = validate(JSONData);
        showError("#errorList", checkValidate, "#errorContainer");
        if (checkValidate.length != 0) {
            return;
        }
        $("#errorContainer").addClass("d-none");
        let dataForSend = JSON.stringify(JSONData);

        Swal.fire({
            title: `ยืนยันการเพิ่มประเภทการชำระเงิน ${JSONData.paymentTypeName}(${JSONData.paymentTypeSubId})?`,
            showDenyButton: true,
            // showCancelButton: true,
            confirmButtonText: `ยืนยัน`,
            denyButtonText: `ยกเลิก`,
        }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            if (result.isConfirmed) {
                $.ajax({
                    url: `<?= urlTobackend ?>addPaymentTypes`,
                    type: "post",
                    dataType: 'json',
                    data: {
                        jsonData: dataForSend
                    },
                    success: function(res) {
                        if (res.status) {
                            Swal.fire({
                                text: res.message,
                                confirmButtonText: `ปิด`,
                                showLoaderOnConfirm: true,
                                icon: "success",
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    $("#errorContainer").addClass("d-none");
                                    $("#paymentTypeSubId").val("");
                                    $("#paymentTypeName").val("");
                                    $("#paymentTypeDetail").val("");
                                    $("#paymentTypeDateExpired").val("");
                                    $("#paymentTypeBranch").val("");
                                    $("#paymentTypeStockAmount").val("");
                                }
                            })
                        } else {
                            console.log(res);
                            showError("#errorList", res.results, "#errorContainer");
                        }

                    },
                    error: (err) => {
                        console.log(err)
                    }
                });
            }
        }).catch((e) => {
            console.log(e);
        });


    }

    function validate(JSONData) {
        const classCustomValidation = new CustomValidation();
        const optionCheckDateExpiredOnly = ["paymentTypeDateExpired"];
        const optionCheckSpecialCharacterOnly = ["paymentTypeJson"];
        const optionCheckNumericOnly = [
            "paymentTypeSubId",
            "paymentTypeName",
            "paymentTypeDetail",
            "paymentTypeDateExpired",
            "paymentTypeStockAmount",
            "paymentTypeJson",
        ];
        const optionCheckStringOnly = [
            "companyId",
            "createBy",
            "paymentTypeJson",
        ];
        const setData = setDataForValidation(JSONData);
        const checkEmpty = classCustomValidation.checkEmpty(setData, optionCheckDateExpiredOnly);
        const checkSpecialCharacter = classCustomValidation.checkSpecialCharacter(setData, optionCheckSpecialCharacterOnly, /[ `!@#$%^&*()+\=\[\]{};':"\\|,.<>\/?~]/);
        const checkNumeric = classCustomValidation.checkNumeric(setData, optionCheckNumericOnly);
        const checkString = classCustomValidation.checkString(setData, optionCheckStringOnly);
        const setValidationErrorData = classCustomValidation.setValidationErrorData(checkEmpty, checkSpecialCharacter, checkNumeric, checkString);
        return setValidationErrorData;
    }

    function setDataForValidation(data) {
        // [name:11,detail:"11@"] รูปแบบที่ต้องการ array key : value
        let returnData = [];
        for (let index in data) {
            returnData[index] = data[index];
        }
        return returnData;
    }

    function showError(id, dataError, idDisplayNone) {
        if (dataError.length == 0) {
            $(idDisplayNone).addClass("d-none");
            return;
        }
        $(idDisplayNone).removeClass("d-none");
        $(id + ">li").remove();
        dataError.forEach((value, index) => {
            $(id).append(`<li>${value}</li>`);
        });
    }
</script>