<?php require_once("template.php"); ?>

<?= templateHeader; ?>
<link rel="stylesheet" href="../../css/add-material.css">
<?= templateBody; ?>
<div class="content-wrapper p-4" id="app">
    <div class="content-header pb-1">
        <h4>เพิ่มสินค้าใหม่</h4>
        <hr>
    </div>
    <div class="content-body">
        <div class="container-fluid content-custom" style="position: relative;">
            <form class="add-new-material-form pb-4" id="addNewMaterialForm" method="POST" enctype="multipart/form-data">
                <div class="row form-step px-2 pb-3" id="step1">
                    <h5 class="mb-2">ข้อมูลวัตถุดิบ</h5>
                    <div class="col-6">
                        <div class="form-group">
                            <label for="">รหัสวัตถุดิบ</label>
                            <input type="text" class="form-control" id="materialSubId" name="materialSubId" placeholder="p-000001">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label for="">ชื่อวัตถุดิบ</label>
                            <input type="text" class="form-control" id="materialName" name="materialName" placeholder="ซาลาเปาหมู">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label for="">รายละเอียดวัตถุดิบ</label>
                            <textarea type="text" class="form-control" id="materialDetail" name="materialDetail" placeholder="วัตถุดิบสำหรับสินค้าใหม่"></textarea>
                        </div>
                    </div>
                    <!-- เผื่อมี -->
                    <!-- <div class="col-6 col-lg-3">
                        <div class="form-group">
                            <label for="">ประเภทวัตถุดิบ</label>
                            <select type="text" class="form-control">
                                <option></option>
                            </select>
                            <small class="text-danger"><b>ประเภทสินค้ามีผลกับหน้ารายการขาย <br>สินค้าจะถูกจัดไปอยู่หมวดหมู่ที่เลือก</b></small>
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

                            <input type="text" class="form-control d-none" id="materialDateExpired" name="materialDateExpired" placeholder="2021-12-31">
                            <small class="text-danger"><b>ถ้ากำหนดวันหมดอายุ<br>วัตถุดิบจะหายไปจากรายการวัตถุดิบอัตโนมัติเมื่อครบกำหนด</b></small>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="form-group">
                            <label for="">ระบุสาขา</label>
                            <select type="text" class="form-control" id="materialBranch" name="materialBranch" placeholder="">
                                <option value="0">ทุกสาขา</option>
                            </select>
                            <small class="text-danger"><b>วัตถุดิบจะแสดงเฉพาะสาขาที่เลือก</b></small>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="form-group">
                            <label for="">จำนวนสต็อค</label>
                            <input type="text" class="form-control" id="materialStockAmount" name="materialStockAmount" placeholder="100">
                            <small class="text-danger"><b>วัตถุดิบจะแสดงสต็อคตามที่ระบุ</b></small>
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
                        <input type="submit" class="btn btn-primary" value="เพิ่มวัตถุดิบ">
                    </div>
                </div>
                <!-- step1 end -->
            </form>
        </div><!-- /.container-fluid -->
    </div>
</div>
<?= templateFootter ?>
<script>
    const companyId = "<?= $_SESSION["usersLogin"]["companyId"] ?>";
    const branchBy = "<?= $_SESSION["usersLogin"]["branchId"] ?>"; //ต้องทำ login แยก ระหว่างคนที่มีสิทธิ์ดูได้ทุกสาขากับคนที่ดูได้เฉพาะสาขา
    const loginBy = "<?= $_SESSION["usersLogin"]["companyUserId"] ?>";
    $(document).ready(() => {
        getAllBranch();
    });
    $("input[name=dateExpiredSelect]").change((e) => {
        if ($('input[name=dateExpiredSelect]:checked').val() == "2") {
            $("#materialDateExpired").removeClass("d-none");
        } else {
            $("#materialDateExpired").addClass("d-none");
        }
    });
    $("#addNewMaterialForm").submit((e) => {
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
        const showData = await addOptionDataToSelectElem("#materialBranch", setData);
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
            materialSubId: $("#materialSubId").val(),
            materialName: $("#materialName").val(),
            materialDetail: $("#materialDetail").val(),
            materialDateExpired: $("#materialDateExpired").val(),
            materialBranch: $("#materialBranch").val(),
            materialStockAmount: $("#materialStockAmount").val(),
        }
        var dataValidate = Object.entries(JSONData);;
        const checkValidate = validate(dataValidate);
        if (checkValidate.length != 0) {
            showError("#errorList", checkValidate, "#errorContainer");
            return;
        }
        $("#errorContainer").addClass("d-none");
        let dataForSend = JSON.stringify(JSONData);
        let formData = new FormData(document.getElementById("addNewMaterialForm"));

        Swal.fire({
            title: `ยืนยันการเพิ่มวัตถุดิบ ${JSONData.materialName}(${JSONData.materialSubId})?`,
            showDenyButton: true,
            // showCancelButton: true,
            confirmButtonText: `ยืนยัน`,
            denyButtonText: `ยกเลิก`,
        }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            if (result.isConfirmed) {
                $.ajax({
                    url: `<?= urlTobackend ?>addMaterials.php`,
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
                                    $("#materialSubId").val("");
                                    $("#materialName").val("");
                                    $("#materialDetail").val("");
                                    $("#materialDateExpired").val("");
                                    $("#materialBranch").val("");
                                    $("#materialStockAmount").val("");
                                }
                            })
                        } else {
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

    function validate(data) {
        // index 0 = key index 1 = value
        let returnData = [];
        data.forEach((value, index) => {
            if (!checkEmpty(value[1]) && value[0] != "materialDateExpired") {
                returnData.push(value[0] + " is not Empty");
            } else {
                if (value[0] != "materialStockAmount" && value[0] != "companyId" && value[0] != "createBy" && !checkString(value[1])) {
                    returnData.push(value[0] + " use string type only.");
                }
                if (!checkNumeric(value[1]) && (value[0] == "companyId" || (value[0] == "materialStockAmount" || value[0] == "createBy"))) {
                    returnData.push(value[0] + ": " + value[1] + " use numeric type only.");
                }
                if (value[0] != "materialStockAmount" && !checkSpecialCharacter(value[1])) {
                    returnData.push(value[0] + " can't use special character.");
                }
            }

        });
        return returnData;
    }

    function checkEmpty(string) {
        let check = true;
        if (string === "") {
            check = false;
        }
        return check;
    }

    function checkNumeric(number) {
        let check = true;
        const format = /^[0-9.]+$/;
        const checkNumber = format.test(number);
        // check ก่อนว่าเป็นตัวเลขไหมถ้าจริงไปดัก ตัวอย่าง 10. 
        if (!checkNumber) {
            check = false;
        } else {
            const myArr = number.split(".");
            const firstIndex = myArr[0];
            const lastIndex = myArr[myArr.length - 1];
            if (firstIndex == "" || lastIndex == "") {
                check = false;
            }
        }
        return check;
    }

    function checkSpecialCharacter(text) {
        let check = true;
        // /[ `!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?~]/;
        const format = /[ `!@#$%^&*()+\=\[\]{};':"\\|,.<>\/?~]/;
        checkFormat = format.test(text);
        if (checkFormat) {
            check = false;
        }
        return check;
    }


    function checkString(string) {
        let check = true;
        if (typeof string !== "string") {
            check = false;
        }
        return check;
    }

    function showError(id, dataError, idDisplayNone) {
        $(idDisplayNone).removeClass("d-none");
        $(id + ">li").remove();
        dataError.forEach((value, index) => {
            $(id).append(`<li>${value}</li>`);
        });
    }
</script>