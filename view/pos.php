<?php require_once($_SERVER['DOCUMENT_ROOT'] . "/bear/class/authUsers.php"); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../css/pos.css" rel="stylesheet">
    <!--load all styles -->
    <?php include("../autoload/loadallLib.php") ?>
    <title>bear</title>
</head>

<body>
    <div id="app" class="container-fluid" style="position: relative; min-height: 100%;" v-if="posReadyStatus">
        <nav class="header-main">
            <div class="row">
                <div class="col-12 d-flex justify-content-between pt-2">
                    <div class="logo">
                        logo
                    </div>
                    <div class="col-md-9 app-menu-container">
                        <ul class="app-menu m-0">
                            <li class="app-menu-item" @click.prevent="[actionAppMenu = 'cancelBill',loadAllBill(),loadBillMessage()]" v-bind:class="[actionAppMenu == 'cancelBill' ? 'app-menu-item-active' : '']">ยกเลิกบิล</li>
                            <li class="app-menu-item" @click.prevent="[actionAppMenu = 'viewSales', loadDailySaleDetails()]" v-bind:class="[actionAppMenu == 'viewSales' ? 'app-menu-item-active' : '']">ดูยอดขาย</li>
                            <li class="app-menu-item btn btn-sm btn-warning" @click.prevent="[actionAppMenu = 'closeShift', closeShiftOrDay.title = 'ปิดกะ']" v-bind:class="[actionAppMenu == 'closeShift' ? 'app-menu-item-active' : '']" @click="closeShiftOrDayModal = true">ปิดกะ</li>
                            <li class="app-menu-item btn btn-sm btn-danger" @click.prevent="[actionAppMenu = 'closeDay', closeShiftOrDay.title = 'ปิดวัน']" v-bind:class="[actionAppMenu == 'closeDay' ? 'app-menu-item-active' : '']" @click="closeShiftOrDayModal = true">ปิดวัน</li>
                        </ul>
                    </div>
                    <div class="user">
                        {{ shift.employeeName }}
                    </div>
                </div>

            </div>
        </nav>
        <section class="row">
            <div class="col-5 ps-0">
                <article class="main-order">
                    <div class="add-order">
                        <ul class="px-2">
                            <li>+</li>
                            <!-- <li>ขนมจีบ</li>
                            <li>ซาลาเปา</li>
                            <li>น้ำ</li> -->
                        </ul>
                    </div>
                    <div class="my-line"></div>
                    <div class="order-detail overflow-y-custom">
                        <div class="">
                            <table class="table">
                                <thead class="text-center">
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col" style="width: 50%;">ชื่อสินค้า</th>
                                        <th scope="col">ราคา</th>
                                        <th scope="col">จำนวน</th>
                                        <th scope="col">รวม</th>
                                        <th scope="col"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, index) in ordersAll" :key="item.id">
                                        <th scope="row">{{index+1}}</th>
                                        <td>{{ item.name }}</td>
                                        <td class="text-end">{{ item.price }}</td>
                                        <td class="text-end">{{ item.amount }}</td>
                                        <td class="text-end">{{ item.total }}</td>
                                        <td class="text-end m-0 px-0">
                                            <button class="btn btn-link btn-sm" type="button" @click.prevent="deleteProductOrders(item)">
                                                <i class="far fa-trash-alt text-danger"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="order-sum">
                        <div class="row justify-content-center">
                            <div class="col-8 row">
                                <table class="table sum-detail">
                                    <thead class="text-center">
                                        <tr>
                                            <th scope="col">ยอดรวม</th>
                                            <th scope="col">
                                                <div style="text-align: left; display: inline-block; width:40%;">{{ orderCount }} รายการ</div>
                                                <div style="text-align: right; display: inline-block; width:50%;">{{ sum }}</div>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th scope="col">Vat(%)</th>
                                            <th scope="col">{{ vat }}</th>
                                        </tr>
                                        <tr>
                                            <th scope="col">ส่วนลด</th>
                                            <th scope="col">{{ discount }}</th>
                                        </tr>
                                        <tr>
                                            <th scope="col">ยอดสุทธิ</th>
                                            <th scope="col" style="background-color: #26E015;">{{ totalSum }}</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="order-payment">
                        <div class="row">
                            <div class="d-grid gap-2 col-6 pe-0" class="payment">
                                <button class="btn btn-danger" type="button" @click.prevent="cancelOrder">ยกเลิก</button>
                            </div>
                            <div class="d-grid gap-2 col-6 ps-0" class="payment">
                                <button class="btn btn-primary" @click.prevent="firstPayment = true">ชำระเงิน</button>
                            </div>
                        </div>
                    </div>
                </article>
            </div>
            <div class="col-7 m-0 main-menu" style="position: relative;">
                <div class="menu-type">
                    <ul class="px-2">
                        <li @click.prevent="sortMenu('bestSales')">รวม</li>
                        <li v-for="(item, index) in menuType" @click.prevent="sortMenu(item.type_id)">{{ item.type_name }}</li>
                    </ul>
                </div>
                <div class="my-line"></div>
                <div class="menu-container overflow-y-custom">
                    <ul class="menu-all">
                        <li class="menu-item" v-for="(item, index) in menuSort" @click.prevent="addProductOrders(item)">
                            <div><span>{{ item.name }}</span></div>
                            <div class="menu-img-container">
                                <img src="../ex.jpg" class="menu-img">
                            </div>
                            <div class="onclick-add-product"><span><b>เพิ่ม</b></span></div>
                        </li>
                    </ul>
                </div>
                <nav aria-label="Page navigation" class="my-navigation pt-3">
                    <div class="form-group mb-2">
                        <label>จำนวน: </label>
                        <input type="text" class="form-control d-inline" style="width: 70px;" v-model="valueAmount">
                    </div>
                    <!-- <ul class="pagination">
                        <li class="page-item">
                            <a class="page-link" href="#" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                        <li class="page-item"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                        <li class="page-item">
                            <a class="page-link" href="#" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    </ul> -->
                </nav>
            </div>
        </section>

        <!-- ชำระเงิน -->
        <div class="modal-payment-main" v-if="firstPayment" v-bind:class="[firstPayment == true? firstPaymentModalActive : '']">
            <div class="modal-payment-container">
                <div class="modal-payment-head">
                    <h5>เลือกช่องทางชำระเงิน</h5>
                </div>
                <hr>
                <div class="modal-payment-body">
                    <div class="payment-type">
                        <ul>
                            <li v-for="(item, index) in paymentsType" @click.prevent="[paymentConfirmType = item.payment_id, selectedPaymentType = item.payment_id]" :class="[{lineBottomGreen:item.payment_id == paymentConfirmType}]" @click="[(paymentConfirmType == 1 ? clearChangeTypePeyment() : '')]">{{ item.payment_name }}</li>
                        </ul>
                    </div>
                    <div class=" row payment-detail money-pay mt-5">
                        <div class="col-6 px-5 payment-detail-left">
                            <div class="calculator-container" v-if="paymentConfirmType == 1">
                                <div class="calculator-number">
                                    <div class="calculator-button" id="num1" v-bind:class="[isButtonActive == 49 || isButtonActive ==97 ? activeClass : '']">1</div>
                                    <div class="calculator-button" id="num2" v-bind:class="[isButtonActive == 50 || isButtonActive ==98 ? activeClass : '']">2</div>
                                    <div class="calculator-button" id="num3" v-bind:class="[isButtonActive == 51 || isButtonActive ==99 ? activeClass : '']">3</div>
                                    <div class="calculator-button" id="num4" v-bind:class="[isButtonActive == 52 || isButtonActive ==100 ? activeClass : '']">4</div>
                                    <div class="calculator-button" id="num5" v-bind:class="[isButtonActive == 53 || isButtonActive ==101 ? activeClass : '']">5</div>
                                    <div class="calculator-button" id="num6" v-bind:class="[isButtonActive == 54 || isButtonActive ==102 ? activeClass : '']">6</div>
                                    <div class="calculator-button" id="num7" v-bind:class="[isButtonActive == 55 || isButtonActive ==103 ? activeClass : '']">7</div>
                                    <div class="calculator-button" id="num8" v-bind:class="[isButtonActive == 56 || isButtonActive ==104 ? activeClass : '']">8</div>
                                    <div class="calculator-button" id="num9" v-bind:class="[isButtonActive == 57 || isButtonActive ==105 ? activeClass : '']">9</div>
                                    <div class="calculator-button" id="num10" v-bind:class="[isButtonActive == 48 || isButtonActive ==96 ? activeClass : '']">0</div>
                                    <div class="calculator-button" id="num11" v-bind:class="[isButtonActive == 13 ? activeClass : '', isButtonNotActive]">Enter</div>
                                </div>
                            </div>
                            <div class="calculator-container" v-if="paymentConfirmType != 1">
                                <h6><mark>{{ imageUploadList.labelName }}</mark></h6>
                                <div class="col-12 mt-3" v-if="requestCameraStatus == false">
                                    <div class="product-preview-container" id="preview">
                                        <!-- <label for="formFile" class="form-label">{{ item.labelName }}</label> -->
                                        <div v-if="imageUploadList.imagePreview == ''">
                                            <input class="form-control" type="file" @change="createImage($event)" v-model="imageUploadList.imageData">
                                        </div>
                                        <div v-else>
                                            <div class="img-container text-center">
                                                <img class="product-preview-img mb-2" :src="imageUploadList.imagePreview" />
                                            </div>
                                            <div class="text-center">
                                                <strong>{{ imageUploadList.imageName }}</strong>
                                                <button v-if="imageUploadList.imagePreview != ''" class="btn btn-danger btn-sm mt-1" @click.prevent="removeImage()">ลบรูป</button>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="col-12 mt-3" v-if="fileValidation.length > 0">
                                        <div class="col-12">
                                            <div class="alert alert-danger mb-0">
                                                <ul style="margin-bottom: 0px;">
                                                    <li v-for="(item, index) in fileValidation">{{ item }}</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 mt-3 text-center">
                                    <div class="video-container" :id="'videoContainer'">
                                        <div class="mb-2">
                                            <button class="btn btn-primary btn-sm" id="requestCamera" @click.prevent="requestCamera('cameraContainer', 'requestCamera')">เปิดกล้อง</button>
                                        </div>
                                        <video class="d-none" :id="'cameraContainer'" width="100%" autoplay></video>
                                    </div>
                                    <div class="text-center">
                                        <canvas class="d-none canvas-take-photo" :id="'photo'"></canvas>
                                        <div class="mb-2" v-if="requestCameraStatus === true">
                                            <button class="btn btn-success mt-2 btn-take-photo" :id="'btnTakePhoto'" @click.prevent="takePhoto('cameraContainer', 'photo', 'btnTakePhoto', 'cameraContainer')">ถ่ายรูป</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-6 mt-3 take-photo-container">
                                    <!-- <div class="product-preview-container" id="preview">
                                        <label for="formFile" class="form-label">{{ imageUploadList.labelName }}</label>
                                    </div> -->
                                </div>
                            </div>
                        </div>
                        <div class="col-6 px-5 payment-detail-right">
                            <table class="table sum-detail">
                                <thead class="text-center">
                                    <tr>
                                        <th scope="col">รายการทั้งหมด</th>
                                        <th scope="col">{{ orderCount }}</th>
                                    </tr>
                                    <tr>
                                        <th scope="col">ยอดรวม</th>
                                        <th scope="col">{{ sum }}</th>
                                    </tr>
                                    <tr>
                                        <th scope="col">Vat(%)</th>
                                        <th scope="col">{{ vat }}</th>
                                    </tr>
                                    <tr>
                                        <th scope="col">ส่วนลด</th>
                                        <th scope="col">{{ discount }}</th>
                                    </tr>
                                    <tr>
                                        <th scope="col">ยอดสุทธิ</th>
                                        <th scope="col" style="background-color: #F0EF12;">{{ totalSum }}</th>
                                    </tr>
                                    <tr>
                                        <th scope="col">จำนวนที่ชำระ</th>
                                        <th scope="col">
                                            <input type="text" class="form-control" v-model="moneyPay" @keyup="payments(event)" @focus="paymentConfirm == false ? moneyPay = '' : moneyPay = moneyPay" @blur="paymentConfirm == false ? moneyPay = 0 : moneyPay = moneyPay">
                                        </th>
                                    </tr>
                                    <tr>
                                        <th scope="col">เงินทอน</th>
                                        <th scope="col" style="background-color: #26E015;">
                                            {{ cashChange }}
                                        </th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-payment-footer">
                    <div class="row justify-content-end">
                        <div class="d-grid gap-2 col-1 pe-0 mx-1" class="payment">
                            <button class="btn btn-danger" type="button" @click.prevent="[firstPayment = false, selectedPaymentType = undefined, paymentConfirmType = 1, requestCameraStatus = false]">ปิด</button>
                        </div>
                        <div class="d-grid gap-2 col-2 pe-0 ps-0 mx-1" class="payment">
                            <button class="btn btn-warning" type="button" @click.prevent="[actionAppMenu = 'discounts']">ส่วนลด</button>
                        </div>
                        <div class="d-grid gap-2 col-2 ps-0 mx-1" class="payment">
                            <button class="btn btn-primary" type="button" @click="sendPaymentToBackend()">ยืนยัน</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End ชำระเงิน -->


        <!-- calcel bill , view sales, discount -->
        <div class="modal-payment-main" v-if="actionAppMenu == 'cancelBill' || actionAppMenu == 'viewSales' || actionAppMenu == 'discounts'" v-bind:class="[actionAppMenu != ''? firstPaymentModalActive : '']">
            <div class="modal-app-menu-container">
                <div class="modal-app-menu-left" v-bind:class="[billSelectedDetail == true ? 'left-to-minus-50' : '',actionAppMenu == 'viewSales' ? 'view-daily-sales' : '',discountSelectedDetail == true && actionAppMenu == 'discounts' && discountList.length ? 'left-to-minus-50' : '' ]">
                    <div class="modal-app-menu-head">
                        <h5 v-if="actionAppMenu == 'cancelBill'">ยกเลิกบิล</h5>
                        <h6 class="text-center" v-if="actionAppMenu == 'viewSales'">ดูยอดขาย</h6>
                        <h6 class="text-center" v-if="actionAppMenu == 'discounts'">ส่วนลด</h6>
                        <hr v-bind:class="[actionAppMenu == 'viewSales' ? 'mb-0' : '' ]">
                        <div class="view-sales-container" v-if="actionAppMenu == 'viewSales'">
                            <div class="view-sales-head">
                                <h4>{{ dailySaleTotalDetails.dailySaleTotalSum }} บาท</h4>
                                <!-- <span class="dailysale-sum-detail">{{ dailySaleTotalDetails.dailySaleSumAll }} - {{ dailySaleTotalDetails.dailySaleDiscountSumAll }}(ส่วนลด)</span> -->
                                <span class="dailysale-sum-detail">ส่วนลด: {{ dailySaleTotalDetails.dailySaleDiscountSumAll }}</span>
                            </div>
                            <div class="view-sales-head">
                                <div class="view-sales-head-type pt-2" v-for="(item, index) in dailySaleTotalDetails.dailySaleDetails">
                                    <span class="">{{ item.totalSum }} บาท</span>
                                    <small class="text-muted"><b>{{ item.payment_name }}</b></small>
                                </div>
                            </div>
                        </div>
                        <div class="" v-if="actionAppMenu == 'discounts'">
                            <ul class="discount-choice-type">
                                <li @click.prevent="[discountTypeSelected = 'customDiscount', addDiscountList = '']" :class="[{lineBottomGreen:'customDiscount' == discountTypeSelected}]">{{ customDiscountSet.discount_type_name }}</li>
                                <li v-for="(item, index) in discountChoice" @click.prevent="[discountTypeSelected = index, discountChoiceSelected = item]" :class="[{lineBottomGreen:index == discountTypeSelected}]">{{ index }}</li>
                            </ul>
                        </div>
                    </div>
                    <div class="modal-app-menu-body overflow-y-custom" v-if="actionAppMenu == 'cancelBill'">
                        <div v-if="billAll.length == 0" class="text-center text-muted">ไม่พบข้อมูล</div>
                        <ul class="bill-list m-0 p-0">
                            <li class="bill-list-item" v-for="(item, index) in billAll" @click="[selectedBill = item.id, billSelectedDetail = true, showBillDetail(item), pushCancelBill(item)]" :class="[{highlightBill:item.id == selectedBill}, item.billActive == 0 ? 'text-danger' : '']">
                                <span>{{ item.id }}</span>
                                <span>{{ item.totalSum }}</span>
                            </li>
                        </ul>
                    </div>
                    <div class="modal-app-menu-body overflow-y-custom p-3" v-if="actionAppMenu == 'discounts'">
                        <div v-if="discountChoice.length == 0" class="text-center text-muted">ไม่พบข้อมูล</div>

                        <div class="form-group" v-if="discountTypeSelected == 'customDiscount'">
                            <div class="text-center"><span class=""><b>หมายเหตุ*</b></span><small class="text-danger px-2"><b>ถ้าเลือกเป็น % โปรดตรวจสอบรายการให้ครบถ้วนก่อนกำหนดส่วนลด</b></small></div>
                            <label for="">เลือกประเภท <span class="text-danger"><b>*</b></span></label>
                            <select class="form-control" v-model="customDiscountSet.discount_choice_name">
                                <option value="ระบุเป็น %">ระบุเป็น %</option>
                                <option value="ระบุเป็น บาท">ระบุเป็น บาท</option>
                            </select>
                        </div>
                        <div class="form-group" v-if="discountTypeSelected == 'customDiscount'">
                            <label for="">จำนวนส่วนลด <span class="text-danger"><b>*</b></span><span></label>
                            <input type="text" class="form-control" v-model="customDiscountSet.discount_choice_value" @focus="customDiscountSet.discount_choice_value == 0 ? customDiscountSet.discount_choice_value = '' : customDiscountSet.discount_choice_value = customDiscountSet.discount_choice_value" @blur="customDiscountSet.discount_choice_value == 0 ? customDiscountSet.discount_choice_value = 0 : customDiscountSet.discount_choice_value = customDiscountSet.discount_choice_value">
                            </input>
                        </div>
                        <div class="form-group" v-if="discountTypeSelected == 'customDiscount'">
                            <label for="">รายละเอียดส่วนลด <span class="text-danger"><b>*</b></span><span></label>
                            <textarea class="form-control" v-model="customDiscountSet.discountDetail"></textarea>
                        </div>
                        <table style="width: 100%;" v-if="discountTypeSelected != 'customDiscount'">
                            <thead>
                                <tr>
                                    <th class="text-center">ชื่อส่วนลด</th>
                                    <th class="text-center">มูลค่า</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="daily-sale-item" v-for="(item, index) in discountChoiceSelected" @click="[selectedDailySaleProductType = index,addDiscountList = item]" :class="[{highlightBill:index == selectedDailySaleProductType}]">
                                    <td style="text-align: left;">{{ item.discount_choice_name }}</td>
                                    <td style="text-align: right;">{{ item.discount_choice_value }}</td>
                                </tr>
                            </tbody>
                        </table>

                    </div>
                    <div class="modal-app-menu-body pe-2 ps-2 overflow-y-custom" v-if="actionAppMenu == 'viewSales'">
                        <table style="width: 100%;">
                            <thead>
                                <tr>
                                    <th class="text-center">ประเภทสินค้า</th>
                                    <th class="text-center">จำนวน/ยอดขาย</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class=" daily-sale-item" v-for="(item, index) in dailySaleTotalDetails.dailySaleTotalDetailByProductType" @click="[selectedDailySaleProductType = index, billSelectedDetail = true, addDailySaleProductSubType(item),addDiscountListHead = index]" :class="[{highlightBill:index == selectedDailySaleProductType}]">
                                    <td style="text-align: left;">{{ index }}</td>
                                    <td style="text-align: right;">({{ item.countSubType }}) {{ item.sumType }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-app-menu-footer">
                        <hr>
                        <div class="col-12 text-center alert alert-success alert-sm" v-if="cancelStatus && actionAppMenu == 'cancelBill'">
                            <div class="">{{ dynamicMessage }}</div>
                        </div>
                        <div class="d-flex justify-content-end px-2" style="width: 100%;">
                            <div class="me-3">
                                <button class="btn btn-danger" type="button" @click.prevent="[actionAppMenu = '',cancelStatus ='', billSelectedDetail = false, selectedDailySaleProductType = undefined]">ปิด</button>
                            </div>
                            <div class="" v-if="actionAppMenu == 'cancelBill'">
                                <button class="btn btn-primary" type="button" @click="[showUserConfirm(),confirmCancelCheck = true]">ยกเลิก</button>
                            </div>
                            <div class="" v-if="actionAppMenu == 'discounts'">
                                <button class="btn btn-primary" type="button" @click="[setDiscountList(addDiscountList), discountSelectedDetail = true, (customDiscountSet.discount_choice_name != '' && parseFloat(customDiscountSet.discount_choice_value) != 0 && customDiscountSet.discountDetail != '' ? setDiscountList(customDiscountSet) : ''),calOrders()]">ตกลง</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-app-menu-right" v-bind:class="[billSelectedDetail == true ? 'left-to-plus-50' : '', actionAppMenu == 'viewSales' ? 'daily-sale-selected-container' : '', discountSelectedDetail == true && actionAppMenu == 'discounts' && discountList.length > 0 ? 'left-to-plus-50' : '']">
                    <div class="on-bill-select-main">
                        <div class="on-bill-select-container overflow-y-custom">
                            <div class="bill-container-action" v-if="actionAppMenu == 'cancelBill'">
                                <div class="bill-head text-center">
                                    <div>{{ shopDetail.shopName }}</div>
                                    <div>TAX#{{ shopDetail.shopTax }} (VAT Included)</div>
                                    <div>เลขที่ใบเสร็จ: {{ billDetail.id }}</div>
                                    <div class="mt-2">ใบเสร็จรับเงิน/ใบกำกับภาษีแบบย่อ</div>
                                </div>
                                <div class="bill-body">
                                    <div class="d-flex justify-content-center">
                                        <table style="width: 100%;">
                                            <tr v-for="(item, index) in billDetail.orders">
                                                <td style="text-align: left;">{{ item.name }} {{ item.price }} * {{ item.amount }}</td>
                                                <td style="text-align: right;">{{ item.total }}</td>
                                            </tr>
                                            <hr>
                                        </table>
                                    </div>
                                    <hr style="margin: .5rem 0px;">
                                    <div class="d-flex justify-content-center">
                                        <table style="width: 80%;">
                                            <tr>
                                                <td style="text-align: left;">ราคารวม ({{ billDetail.orderCountAll }})</td>
                                                <td style="text-align: right;">{{ billDetail.sum }}</td>
                                            </tr>
                                            <tr>
                                                <td style="text-align: left;">VAT(%)</td>
                                                <td style="text-align: right;">{{ billDetail.vat }}</td>
                                            </tr>
                                            <tr>
                                                <td style="text-align: left;">ส่วนลด</td>
                                                <td style="text-align: right;">{{ billDetail.discount }}</td>
                                            </tr>
                                            <tr>
                                                <td style="text-align: left;"><b>ราคาสุทธิ</b></td>
                                                <td style="text-align: right;"><b>{{ billDetail.totalSum }}</b></td>
                                            </tr>
                                            <tr>
                                                <td style="text-align: left;">เงินที่ชำระ</td>
                                                <td style="text-align: right;">{{ billDetail.moneyPay }}</td>
                                            </tr>
                                            <tr>
                                                <td style="text-align: left;">เงินทอน</td>
                                                <td style="text-align: right;">{{ billDetail.cashChange }}</td>
                                            </tr>
                                        </table>
                                    </div>

                                </div>
                                <div class="bill-footer text-center mt-2">
                                    <div v-for="item in thankMessageForBillLastLine">{{ item.message_detail }}</div>
                                </div>
                            </div>
                            <div class="bill-container-action" v-if="actionAppMenu == 'viewSales'">
                                <div class="view-sales-detail-head text-center">
                                    <h5>รายละเอียด: <b>{{ addDiscountListHead }}</b></h5>

                                </div>
                                <hr>
                                <div class="view-sales-detail-body">
                                    <div class="d-flex justify-content-center">
                                        <table class="table" style="width: 80%;">
                                            <thead style="text-align: center;">
                                                <tr>
                                                    <th>รายการสินค้า</th>
                                                    <th>จำนวน/รวม</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(item, index) in dailySaleProductSubType">
                                                    <td>{{ item.product_name }} {{ item.order_product_price }}*{{ item.order_product_amount }} </td>
                                                    <td style="text-align: right;">{{ item.order_product_total_sum }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="view-sales-detail-footer"></div>
                            </div>
                            <div class="bill-container-action" v-if="actionAppMenu == 'discounts'">
                                <div class="view-sales-detail-head text-center">
                                    <h5>รายการส่วนลด</h5>
                                    <h6><b>รวม</b> <i>{{ discount }}</i></h5>
                                </div>
                                <hr>
                                <div class="view-sales-detail-body">
                                    <div class="d-flex justify-content-center">
                                        <table class="table" style="width: 80%;">
                                            <thead style="text-align: center;">
                                                <tr>
                                                    <th>ชื่อส่วนลด</th>
                                                    <th>มูลค่า</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr class="discount-list-item" v-for="(item, index) in discountList" @click="[discountList.splice(index,1),calOrders()]">
                                                    <td>{{ item.discount_choice_name }}</td>
                                                    <td style="text-align: right;">{{ item.discount_choice_value }} ({{ item.discount_choice_count }})</td>
                                                    <td class="text-light discount-list-delete">ลบ</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="view-sales-detail-footer"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-confirm-main" v-if="modalUserConfirm != '' && !returnFormBackend.status  && billCancel.length > 0 && confirmCancelCheck == true " v-bind:class="[modalUserConfirm != ''? confirmModalActive : '']">
            <div class="modal-confirm-container p-3">
                <div class="modal-confirm-head">
                    <h5>ระบุรหัสผ่าน</h5>
                </div>
                <hr>
                <div class="modal-confirm-body d-flex justify-content-center">
                    <div class="col-7">
                        <input type="password" class="form-control" v-model="confirmPassword">
                    </div>
                </div>
                <div class="modal-confirm-footer mt-3">
                    <div class="row justify-content-center px-2">
                        <div class="d-grid gap-2 col-2 pe-0 mx-2" class="payment">
                            <button class="btn btn-danger" type="button" @click.prevent="[modalUserConfirm = '',cancelStatus = false]">ปิด</button>
                        </div>
                        <div class="d-grid gap-2 col-2 ps-0 mx-2" v-if="billCancel.length > 0" class="payment" @click="[userConfirmSend()]">
                            <button class="btn btn-primary" type="button">ตกลง</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--END calcel bill -->
        <!-- open shift -->
        <!-- <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModalToggle" @click="[randomTextFuntion()]">
            Launch static backdrop modal
            {{ openShiftModal }}
        </button> -->
        <!-- Modal -->
        <div v-if="openShiftModalStep1 && loadCheckCloseDayData.length == 0 || checkDayIsClose == true" class="modal fade show" id="exampleModalToggle" style="display: block; background-color: rgba(0,0,0,.8);" aria-hidden="true" aria-labelledby="exampleModalToggleLabel" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="staticBackdropLabel">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalToggleLabel">BearPOS</h5>
                    </div>
                    <div class="modal-body">
                        {{ haiGumLangJai }}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary">ออกจากการขาย</button>
                        <button v-if="checkDayIsClose == false" class="btn btn-primary" @click.prevent="[openShiftModalStep1 = false,openShiftModalStep2 = true]">ทำการเปิดกะ</button>
                    </div>
                </div>
            </div>
        </div>
        <div v-if="openShiftModalStep2" class="modal fade show" id="exampleModalToggle2" style="display: block; background-color: rgba(0,0,0,.8);" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <form action="#" class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header pb-1">
                        <div class="modal-title" id="staticBackdropLabel" style="width: 100%;">
                            <div v-if="loadShiftLoading.status">
                                <div class="d-flex justify-content-between">
                                    <h6 class="text-muted">{{ (shiftData.length == 0 ? "เปิดกะที่:1" : "เปิดกะที่:" + (shiftData.subShift.length + 1)) }}</h6>
                                    <div class="text-muted"> {{ currentTime }}</div>
                                </div>
                                <h6>{{ shift.title }}</h6>
                            </div>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div v-if="loadShiftLoading.status" class="form-group">
                            <label for="">ระบุเงินทอน</label>
                            <input type="text" class="form-control" v-model="openShiftAmount">
                        </div>
                        <div v-else>
                            <h6>{{ loadShiftLoading.text }}</h6>
                        </div>
                        <div class="col-12 mt-2" v-if="errorInfo.length > 0">
                            <div class="alert alert-sm alert-danger mb-0 p-2">
                                <ul id="errorList" style="margin-bottom: 0px;">
                                    <li v-for="(value, index) in errorInfo">{{ value }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ออกจากการขาย</button>
                        <button type="button" class="btn btn-primary" @click.prevent="[sendOpenShiftToBackend()]">เปิดกะ</button>
                    </div>
                </div>
            </form>
        </div>
        <!-- end open shift -->
        <!-- check close day modal  -->
        <div v-if="loadCheckCloseDayData.mainShift != undefined && loadCheckCloseDayData.length != 0" style="display: block; background-color: rgba(0,0,0,.8);" id="closeLastDayModal" class="modal fade show" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <form action="#" class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header pb-1">
                        <div class="modal-title" id="staticBackdropLabel" style="width: 100%;">
                            <div v-if="loadShiftLoading.status">
                                <div class="d-flex justify-content-between">
                                    <h6 class="text-muted">{{ closeDay.title }}</h6>
                                    <div class="text-muted"> {{ currentTime }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="">{{ closeDay.title2 }}</label>
                            <input type="text" class="form-control" v-model="openShiftAmount">
                        </div>
                        <div class="col-12 mt-2" v-if="errorCheckCloseDay.length > 0">
                            <div class="alert alert-sm alert-danger mb-0 p-2">
                                <ul id="errorList" style="margin-bottom: 0px;">
                                    <li v-for="(value, index) in errorCheckCloseDay">{{ value }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ออกจากการขาย</button>
                        <button type="button" class="btn btn-primary" @click.prevent="[sendCloseDayToBackend()]">ปิดวัน</button>
                    </div>
                </div>
            </form>
        </div>
        <!-- end check close day or shift modal -->
        <!-- ทำต่อ ส่งค่าปิดกะ / ปิดวันไป backend แล้วให้ sum table bill where bill_of_shift ใส่ table main_shift // main_sub_shift -->
        <!-- close shift/day Modal -->
        <div class="modal fade" v-if="closeShiftOrDayModal == true" style="display: block; background-color: rgba(0,0,0,.8); opacity: 1;" id="closeShiftOrDayModal">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="pt-3 px-3">
                        <div class="d-flex justify-content-between">
                            <h6 class="" id="exampleModalLabel">{{ closeShiftOrDay.title }}{{ (actionAppMenu == 'closeShift' ? "ที่: " + shiftData.subShift.length : "") }}</h6>
                            <div class="text-muted"> {{ currentTime }}</div>
                        </div>
                        <h6>{{ shift.title }}</h6>
                        <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> -->
                    </div>
                    <div class="modal-body pt-1">
                        <div class="form-group">
                            <label for="" class="text-muted">{{ closeShiftOrDay.label }} <b class="text-dark">{{ closeShiftOrDay.labelCashType }}</b></label>
                            <input type="text" class="form-control" v-model="closeShiftOrDay.value">
                        </div>
                        <div class="col-12 mt-2" v-if="errorCloseShiftOrDay.length > 0">
                            <div class="alert alert-sm alert-danger mb-0 p-2">
                                <ul id="errorList" style="margin-bottom: 0px;">
                                    <li v-for="(value, index) in errorCloseShiftOrDay">{{ value }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" @click="closeShiftOrDayModal = false">ปิด</button>
                        <button v-if="actionAppMenu == 'closeShift'" type="button" class="btn btn-primary" @click.prevent="[sendCloseShiftOrDayToBackend('closeShift')]">ปิดกะ</button>
                        <button v-else-if="actionAppMenu == 'closeDay'" type="button" class="btn btn-primary" @click.prevent="[sendCloseShiftOrDayToBackend('closeDay')]">ปิดวัน</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- end close shift/day Modal -->
    </div>

    <script src="https://cdn.jsdelivr.net/npm/vue@2"></script>
    <script src="/bear/js/customValidation.js"></script>
    <script src="/bear/js/myLibary.js"></script>
    <script>
        const app = new Vue({
            el: '#app',
            data: {
                menuAll: [{ //จำลอง query จาก backend
                        id: 1,
                        name: "ซาลาเปาหมู",
                        price: 15,
                        type: 3
                    },
                    {
                        id: 2,
                        name: "ซาลาเปาหมูแดง",
                        price: 15,
                        type: 3
                    },
                    {
                        id: 3,
                        name: "ซาลาเปาหมูสับไข่เค็ม",
                        price: 15,
                        type: 3
                    },
                    {
                        id: 4,
                        name: "ขนมจีบกุ้ง",
                        price: 15,
                        type: 2
                    },
                    {
                        id: 5,
                        name: "ขนมจีบหมู",
                        price: 3,
                        type: 2
                    },
                    {
                        id: 6,
                        name: "ขนมจีบไก่",
                        price: 3,
                        type: 2
                    },
                    {
                        id: 7,
                        name: "ชานมเย็น",
                        price: 20,
                        type: 4
                    },
                    {
                        id: 8,
                        name: "น้ำเปล่า",
                        price: 10,
                        type: 4
                    },
                ],
                menuType: [{ //จำลอง query จาก backend
                        id: 1,
                        name: "ขายดี",
                    },
                    {
                        id: 2,
                        name: "ขนมจีบ",
                    },
                    {
                        id: 3,
                        name: "ซาลาเปา",
                    },
                    {
                        id: 4,
                        name: "น้ำ",
                    },
                ],
                loginBy: <?= $_SESSION["usersLogin"]["companyUserId"] ?>,
                companyBy: <?= $_SESSION["usersLogin"]["companyId"] ?>,
                branchBy: <?= $_SESSION["usersLogin"]["branchId"] ?>,
                companyData: [],
                branchData: [],
                menuSort: [],
                ordersAll: [],
                paymentsType: [],
                dailySaleTotalDetails: [],
                discountList: [],
                addDiscountList: [],
                addDiscountListHead: "",
                discountSelectedDetail: false,
                paymentConfirmType: 1,
                valueAmount: 1,
                sum: 0,
                vat: 0, //เดี๋ยวดึงจาก database
                discount: 0, //ยังไม่ทำ
                totalSum: 0,
                orderCount: 0, //นับเฉพาะรายการสินค้าที่ไม่ซ้ำ /ใบเสร็จ
                orderCountAll: 0, // นับทั้งหมด /ใบเสร็จ
                moneyPay: 0,
                cashChange: 0,
                calculatorButton: 0,
                isButtonActive: 0,
                isButtonNotActive: 0,
                confirmCancelCheck: false,
                activeClass: "button-keydown",
                paymentConfirm: false,
                firstPayment: false,
                firstPaymentModalActive: "modal-payment-main-active",
                confirmModalActive: "",
                actionAppMenu: "",
                billAll: [],
                billDetail: [],
                discountChoice: [],
                discountChoiceSelected: [],
                customDiscountSet: {
                    discount_choice_id: 0,
                    discount_choice_name: "",
                    discount_choice_value: 0,
                    discountDetail: "",
                    discount_type_id: 0,
                    discount_type_name: "กำหนดส่วนลด",
                },
                discountTypeSelected: "ส่วนลดทั้งหมด",
                customdiscountType: "",
                selectedBill: undefined,
                selectedDailySaleProductType: undefined,
                selectedPaymentType: "เงินสด",
                dailySaleProductSubType: [],
                billSelectedDetail: false,
                shopDetail: { //ดึงข้อมูลร้านจาก backend
                    shopName: "คุณเบียร์ซาลาเปา",
                    shopTax: "12345678900",
                },
                billToBackEnd: [],
                paymentToBackend: [],
                thankMessageForBillLastLine: [
                    /*row 1*/
                    {
                        message: "ขอบคุณที่อุดหนุนะคะ"
                    }, //ใน backend ให้ insert เข้า table thankMessageForBillLastline เป็น row ทีละ row เลย จะได้ยืดหยุ่น
                    /*row 2*/
                    {
                        message: "แล้วกลับมาอุดหนุนอีกนะคะ"
                    },
                    /*row 3*/
                    {
                        message: "Thank you very much :)"
                    },
                ],
                modalUserConfirm: "",
                confirmPassword: "",
                returnFormBackend: {
                    status: false,
                    results: [],
                    message: "test return"
                }, //จำลอง return
                billCancel: [],
                dynamicMessage: "", //ใช้รับข้อความจาก backend
                cancelStatus: false,
                requestCameraStatus: false,
                imageUploadList: {
                    labelName: "แนบ Slip.",
                    imageData: "",
                    uploadType: "",
                    imagePreview: "",
                    imageSelected: false,
                    imageName: "",
                },
                fileValidation: [],
                shiftData: [],
                shift: {
                    employeeName: "<?= $_SESSION["usersLogin"]["companyUserFirstname"] . " (" . $_SESSION["usersLogin"]["companyUserNickname"] . ")"; ?>",
                    title: "โดย " + "<?= $_SESSION["usersLogin"]["companyUserFirstname"] . " (" . $_SESSION["usersLogin"]["companyUserNickname"] . ")"; ?>",
                },
                randomText: [
                    "สู้ๆ น้า",
                    "ไม่ต้องทำเพื่อร้านทำเพื่อเงินเดือนน้า",
                    "มาต้อนรับลูกค้าที่เรารักกันเถอะ :)",
                    "วันนี้อย่าลืมให้กำลังใจตัวเองด้วยน้า :)",
                ],
                haiGumLangJai: "",
                openShiftAmount: "",
                errorInfo: [],
                currentTime: "",
                openShiftModalStep1: false,
                openShiftModalStep2: false,
                loadShiftLoading: {
                    status: false,
                    text: "โหลดข้อมูล...",
                },
                cookieName: "openShiftStatus",
                expireDays: 1,
                posReadyStatus: false,
                errorCheckCloseDay: [],
                loadCheckCloseDayData: [],
                closeDay: {
                    title: "ยังไม่ปิดวัน วันที่: ",
                    title2: "ระบุเงินสดกะสุดท้าย",
                },
                currentShiftId: "",
                currentDayId: "",
                closeShiftOrDay: {
                    title: "",
                    value: "",
                    label: "ระบุจำนวนเงินของกะ",
                    labelCashType: "(เงินสด)",
                },
                errorCloseShiftOrDay: [],
                closeShiftOrDayModal: false,
                checkDayIsClose: false,
            },
            async mounted() {
                this.loadMenu();
                this.loadMenutype();
                this.loadPaymentsType();
                this.loadDiscountChoice();
                this.randomTextFuntion();
                this.loadCompany();
                this.loadBranch();
                await this.loadFinishDayAndShift();
                this.posReadyStatus = true;

                setInterval(function() {
                    app.runOpenShiftTime();
                }, 1000);

            },
            methods: {
                async loadFinishDayAndShift() {
                    const loadCheckCloseDayData = await this.loadCloseDay();
                    // check ว่าเปิดวันไปแล้วหรือยัง modal จะได้ไม่ต้องเด้งบ่อยๆ
                    // เดี๋ยวกลับมาทำต่อหลังจาก check cookie แล้วกรณี false ให้ check database อีกทีเผื่อเขาลบ cookie จะได้ไม่เปิดวันซ้ำ
                    const checkOpenShiftCookie = await this.checkCookie(this.cookieName);
                    const loadShiftData = await this.loadShift();
                    this.shiftData = loadShiftData.data.results;
                    if (this.shiftData.length != 0) {
                        this.currentShiftId = this.shiftData.subShift[this.shiftData.subShift.length - 1].sub_shift_id;
                        this.currentDayId = this.shiftData.mainShift[this.shiftData.mainShift.length - 1].main_shift_id;
                    }
                    await this.checkOpenShiftOrNot(checkOpenShiftCookie); //show modal เปิดวันหรือไม่ต้องโชว์
                },
                loadCompany: function() {
                    var config = {
                        method: 'get',
                        url: `<?= urlTobackend ?>loadCompany.php?companyId=${btoa(this.companyBy)}`,
                        headers: {
                            'Accept': 'application/json',
                            // 'Authorization': 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiI5IiwianRpIjoiYTJiMzdhNGViZjU1NDRmMDliZDY2ZTk0NWJhMmQxNGE1Nzg3YTk2ZGMyYmMzNGMyNjk2OTgxMzc1OWFkOGYxYjk5MGUwNjJiNTgzYzRhM2YiLCJpYXQiOjE2MjAyNjkwMjIsIm5iZiI6MTYyMDI2OTAyMiwiZXhwIjoxNjUxODA1MDIyLCJzdWIiOiIxIiwic2NvcGVzIjpbXX0.do6kFU1NYBpP1CctpkOe0CK-ZRmQSttF6pK_mnX01Bgv2trq1aHmBKw26YDnWEYR-v3IxqzwTZ32ab5O1PGWRttH20hGUZi4MXQITJ7a1I_JJVnt5WLLR9TCy9yr9FkkvFtpFjyyjEwq2i9crIzWp1VEpIPj563kIUzRlqc7bth0whoXHPKDuxbW0h7K_Vuh9mSc79npKdR_jQdlGLAig9QSVPHGZizZfxsuzy5Cwz6wTpbQMWHm-7JEZIaQt-snW19NJ65fEM7MJU7sgxpN-cUnC13o9xtV7Nj8sq7PYIJo9XnTCg3w541ClAYFlQy1ZGTfurVAoD8_DAUX73fUYN30Jyb9jf-TlTaD4zfjnF784QX0Gpe0HY8aN2ApXVdsaSTk_vq-SPLP9hWgs1OMKxX7YrsBlq6adWtnqUBQ9sTnllskqn9mF3-U06F6VBxsjHJZv3gWD0ot2ivGhZWzs8BvTd5d62YCLaQZQsxgHbJbLFBO2YTWsHnP9TnAD6C23PZa0hmTfvsYXrqePAi2Wa7-UPOtF9wYSNzhhFqbLaQ2-MWjMDAmDfX8LQM4w1ZNBmcxS5pZG3sqdIMmU4KJ8WwImXlYbWv5P2JQXjA6sgFCDciARi0NP1Y_M4pWsK_ipkpESIxz9RRb3zabEDKtWgjgp_8DPi0swZ7VR56bIiM',
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        withCredentials: true
                    };
                    axios.defaults.withCredentials = true;
                    axios(config).then((response) => {
                        if (response.data.status) {
                            this.companyData = response.data.results;
                        } else {
                            console.log(response.data);
                        }
                    }).catch((e) => {
                        console.log(e);
                    });
                },
                loadBranch: function() {
                    var config = {
                        method: 'get',
                        url: `<?= urlTobackend ?>loadBranch.php?companyId=${btoa(this.companyBy)}`,
                        headers: {
                            'Accept': 'application/json',
                            // 'Authorization': 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiI5IiwianRpIjoiYTJiMzdhNGViZjU1NDRmMDliZDY2ZTk0NWJhMmQxNGE1Nzg3YTk2ZGMyYmMzNGMyNjk2OTgxMzc1OWFkOGYxYjk5MGUwNjJiNTgzYzRhM2YiLCJpYXQiOjE2MjAyNjkwMjIsIm5iZiI6MTYyMDI2OTAyMiwiZXhwIjoxNjUxODA1MDIyLCJzdWIiOiIxIiwic2NvcGVzIjpbXX0.do6kFU1NYBpP1CctpkOe0CK-ZRmQSttF6pK_mnX01Bgv2trq1aHmBKw26YDnWEYR-v3IxqzwTZ32ab5O1PGWRttH20hGUZi4MXQITJ7a1I_JJVnt5WLLR9TCy9yr9FkkvFtpFjyyjEwq2i9crIzWp1VEpIPj563kIUzRlqc7bth0whoXHPKDuxbW0h7K_Vuh9mSc79npKdR_jQdlGLAig9QSVPHGZizZfxsuzy5Cwz6wTpbQMWHm-7JEZIaQt-snW19NJ65fEM7MJU7sgxpN-cUnC13o9xtV7Nj8sq7PYIJo9XnTCg3w541ClAYFlQy1ZGTfurVAoD8_DAUX73fUYN30Jyb9jf-TlTaD4zfjnF784QX0Gpe0HY8aN2ApXVdsaSTk_vq-SPLP9hWgs1OMKxX7YrsBlq6adWtnqUBQ9sTnllskqn9mF3-U06F6VBxsjHJZv3gWD0ot2ivGhZWzs8BvTd5d62YCLaQZQsxgHbJbLFBO2YTWsHnP9TnAD6C23PZa0hmTfvsYXrqePAi2Wa7-UPOtF9wYSNzhhFqbLaQ2-MWjMDAmDfX8LQM4w1ZNBmcxS5pZG3sqdIMmU4KJ8WwImXlYbWv5P2JQXjA6sgFCDciARi0NP1Y_M4pWsK_ipkpESIxz9RRb3zabEDKtWgjgp_8DPi0swZ7VR56bIiM',
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        withCredentials: true
                    };
                    axios.defaults.withCredentials = true;
                    axios(config).then((response) => {
                        if (response.data.status) {
                            this.branchData = response.data.results;
                        } else {
                            console.log(response.data);
                        }
                    }).catch((e) => {
                        console.log(e);
                    });
                },
                addProductOrders: function(orders) {
                    let tempData = {
                        companyId: this.companyBy,
                        branchId: this.branchBy,
                        id: orders.id,
                        name: orders.name,
                        price: orders.price,
                        amount: parseInt(this.valueAmount),
                        total: orders.price * parseInt(this.valueAmount),
                    };
                    let checkSame = 0;
                    this.orderCount = 0;
                    this.orderCountAll += 1; //ถ้ากดชำระเงินค่อย set 0
                    if (this.ordersAll.length > 0) {
                        for (let i = 0; i < this.ordersAll.length; i++) {
                            if (this.ordersAll[i].id == orders.id) {
                                tempData.amount = this.ordersAll[i].amount += parseInt(this.valueAmount);
                                tempData.total = orders.price * tempData.amount;
                                checkSame += 1;
                                this.ordersAll[i].amount = tempData.amount;
                                this.ordersAll[i].total = tempData.total;
                            }
                            this.orderCount += 1;
                        }
                    }
                    if (checkSame == 0) {
                        this.orderCount += 1;
                        this.ordersAll.push(tempData);
                    }
                    if (parseInt(this.valueAmount) != 1) {
                        this.valueAmount = 1;
                    }
                    this.calOrders();
                },
                loadPaymentsType: function() {
                    var config = {
                        method: 'get',
                        url: `<?= urlTobackend ?>loadPaymentsType.php?companyId=${this.companyBy}&branchId=${this.branchBy}`,
                        headers: {
                            'Accept': 'application/json',
                            // 'Authorization': 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiI5IiwianRpIjoiYTJiMzdhNGViZjU1NDRmMDliZDY2ZTk0NWJhMmQxNGE1Nzg3YTk2ZGMyYmMzNGMyNjk2OTgxMzc1OWFkOGYxYjk5MGUwNjJiNTgzYzRhM2YiLCJpYXQiOjE2MjAyNjkwMjIsIm5iZiI6MTYyMDI2OTAyMiwiZXhwIjoxNjUxODA1MDIyLCJzdWIiOiIxIiwic2NvcGVzIjpbXX0.do6kFU1NYBpP1CctpkOe0CK-ZRmQSttF6pK_mnX01Bgv2trq1aHmBKw26YDnWEYR-v3IxqzwTZ32ab5O1PGWRttH20hGUZi4MXQITJ7a1I_JJVnt5WLLR9TCy9yr9FkkvFtpFjyyjEwq2i9crIzWp1VEpIPj563kIUzRlqc7bth0whoXHPKDuxbW0h7K_Vuh9mSc79npKdR_jQdlGLAig9QSVPHGZizZfxsuzy5Cwz6wTpbQMWHm-7JEZIaQt-snW19NJ65fEM7MJU7sgxpN-cUnC13o9xtV7Nj8sq7PYIJo9XnTCg3w541ClAYFlQy1ZGTfurVAoD8_DAUX73fUYN30Jyb9jf-TlTaD4zfjnF784QX0Gpe0HY8aN2ApXVdsaSTk_vq-SPLP9hWgs1OMKxX7YrsBlq6adWtnqUBQ9sTnllskqn9mF3-U06F6VBxsjHJZv3gWD0ot2ivGhZWzs8BvTd5d62YCLaQZQsxgHbJbLFBO2YTWsHnP9TnAD6C23PZa0hmTfvsYXrqePAi2Wa7-UPOtF9wYSNzhhFqbLaQ2-MWjMDAmDfX8LQM4w1ZNBmcxS5pZG3sqdIMmU4KJ8WwImXlYbWv5P2JQXjA6sgFCDciARi0NP1Y_M4pWsK_ipkpESIxz9RRb3zabEDKtWgjgp_8DPi0swZ7VR56bIiM',
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        withCredentials: true
                    };
                    axios.defaults.withCredentials = true;
                    axios(config).then((response) => {
                        if (response.data.status) {
                            this.paymentsType = response.data.results;
                        } else {
                            console.log(response.data);
                        }
                    }).catch((e) => {
                        console.log(e);
                    });

                },
                loadDiscountChoice: function() {
                    var config = {
                        method: 'get',
                        url: `<?= urlTobackend ?>loadDiscounts.php?companyId=${this.companyBy}&branchId=${this.branchBy}`,
                        headers: {
                            'Accept': 'application/json',
                            // 'Authorization': 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiI5IiwianRpIjoiYTJiMzdhNGViZjU1NDRmMDliZDY2ZTk0NWJhMmQxNGE1Nzg3YTk2ZGMyYmMzNGMyNjk2OTgxMzc1OWFkOGYxYjk5MGUwNjJiNTgzYzRhM2YiLCJpYXQiOjE2MjAyNjkwMjIsIm5iZiI6MTYyMDI2OTAyMiwiZXhwIjoxNjUxODA1MDIyLCJzdWIiOiIxIiwic2NvcGVzIjpbXX0.do6kFU1NYBpP1CctpkOe0CK-ZRmQSttF6pK_mnX01Bgv2trq1aHmBKw26YDnWEYR-v3IxqzwTZ32ab5O1PGWRttH20hGUZi4MXQITJ7a1I_JJVnt5WLLR9TCy9yr9FkkvFtpFjyyjEwq2i9crIzWp1VEpIPj563kIUzRlqc7bth0whoXHPKDuxbW0h7K_Vuh9mSc79npKdR_jQdlGLAig9QSVPHGZizZfxsuzy5Cwz6wTpbQMWHm-7JEZIaQt-snW19NJ65fEM7MJU7sgxpN-cUnC13o9xtV7Nj8sq7PYIJo9XnTCg3w541ClAYFlQy1ZGTfurVAoD8_DAUX73fUYN30Jyb9jf-TlTaD4zfjnF784QX0Gpe0HY8aN2ApXVdsaSTk_vq-SPLP9hWgs1OMKxX7YrsBlq6adWtnqUBQ9sTnllskqn9mF3-U06F6VBxsjHJZv3gWD0ot2ivGhZWzs8BvTd5d62YCLaQZQsxgHbJbLFBO2YTWsHnP9TnAD6C23PZa0hmTfvsYXrqePAi2Wa7-UPOtF9wYSNzhhFqbLaQ2-MWjMDAmDfX8LQM4w1ZNBmcxS5pZG3sqdIMmU4KJ8WwImXlYbWv5P2JQXjA6sgFCDciARi0NP1Y_M4pWsK_ipkpESIxz9RRb3zabEDKtWgjgp_8DPi0swZ7VR56bIiM',
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        withCredentials: true
                    };
                    axios.defaults.withCredentials = true;
                    axios(config).then((response) => {
                        if (response.data.status && response.data.results.length > 0) {
                            this.discountChoice = this.groupArrayDiscountByObjectKey(response.data.results);
                            this.discountChoiceSelected = this.discountChoice["ส่วนลดทั้งหมด"];
                        } else {
                            console.log(response.data);
                        }
                    }).catch((e) => {
                        console.log(e);
                    });
                },
                loadDailySaleDetails: function() {
                    var config = {
                        method: 'get',
                        url: `<?= urlTobackend ?>loadDailySales?companyId=${this.companyBy}&branchId=${this.branchBy}&dateSelect=<?= date("Y-m-d") ?>`,
                        headers: {
                            'Accept': 'application/json',
                            // 'Authorization': 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiI5IiwianRpIjoiYTJiMzdhNGViZjU1NDRmMDliZDY2ZTk0NWJhMmQxNGE1Nzg3YTk2ZGMyYmMzNGMyNjk2OTgxMzc1OWFkOGYxYjk5MGUwNjJiNTgzYzRhM2YiLCJpYXQiOjE2MjAyNjkwMjIsIm5iZiI6MTYyMDI2OTAyMiwiZXhwIjoxNjUxODA1MDIyLCJzdWIiOiIxIiwic2NvcGVzIjpbXX0.do6kFU1NYBpP1CctpkOe0CK-ZRmQSttF6pK_mnX01Bgv2trq1aHmBKw26YDnWEYR-v3IxqzwTZ32ab5O1PGWRttH20hGUZi4MXQITJ7a1I_JJVnt5WLLR9TCy9yr9FkkvFtpFjyyjEwq2i9crIzWp1VEpIPj563kIUzRlqc7bth0whoXHPKDuxbW0h7K_Vuh9mSc79npKdR_jQdlGLAig9QSVPHGZizZfxsuzy5Cwz6wTpbQMWHm-7JEZIaQt-snW19NJ65fEM7MJU7sgxpN-cUnC13o9xtV7Nj8sq7PYIJo9XnTCg3w541ClAYFlQy1ZGTfurVAoD8_DAUX73fUYN30Jyb9jf-TlTaD4zfjnF784QX0Gpe0HY8aN2ApXVdsaSTk_vq-SPLP9hWgs1OMKxX7YrsBlq6adWtnqUBQ9sTnllskqn9mF3-U06F6VBxsjHJZv3gWD0ot2ivGhZWzs8BvTd5d62YCLaQZQsxgHbJbLFBO2YTWsHnP9TnAD6C23PZa0hmTfvsYXrqePAi2Wa7-UPOtF9wYSNzhhFqbLaQ2-MWjMDAmDfX8LQM4w1ZNBmcxS5pZG3sqdIMmU4KJ8WwImXlYbWv5P2JQXjA6sgFCDciARi0NP1Y_M4pWsK_ipkpESIxz9RRb3zabEDKtWgjgp_8DPi0swZ7VR56bIiM',
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        withCredentials: true
                    };
                    axios.defaults.withCredentials = true;
                    axios(config).then((response) => {
                        if (response.data.status) {
                            this.dailySaleTotalDetails = {
                                dailySaleTotalSum: response.data.results.dailySaleTotalSumAll,
                                dailySaleTotalDetailByProductType: response.data.results.dailySaleDetail,
                                dailySaleDetails: response.data.results.dailySaleTotalSumByType,
                                dailySaleSumAll: response.data.results.dailySaleSumAll,
                                dailySaleDiscountSumAll: response.data.results.dailySaleDiscountSumAll,
                            };

                        } else {
                            console.log(response.data);
                        }
                    }).catch((e) => {
                        console.log(e);
                    });

                },
                addDailySaleProductSubType: function(mainType) {
                    this.dailySaleProductSubType.slice(0, 0);
                    let subType = [{
                        product_id: "",
                        product_name: "",
                        order_product_price: 0,
                        order_product_amount: 0,
                        order_product_total_sum: 0,
                    }];
                    let checkProductId;
                    let setIndex = 0;
                    for (let i = 0; i < mainType.subTypeDetails.length; i++) {

                        if (i > 0) {
                            if (mainType.subTypeDetails[i].product_id == mainType.subTypeDetails[i - 1].product_id) {
                                subType[setIndex].order_product_amount += parseInt(mainType.subTypeDetails[i].order_product_amount);
                                subType[setIndex].order_product_total_sum = parseFloat(subType[setIndex].order_product_price) * parseFloat(subType[setIndex].order_product_amount);
                            } else {
                                setIndex++;
                                subType[setIndex] = {
                                    product_id: mainType.subTypeDetails[i].product_id,
                                    product_name: mainType.subTypeDetails[i].product_name,
                                    order_product_price: parseFloat(mainType.subTypeDetails[i].order_product_price),
                                    order_product_amount: parseInt(mainType.subTypeDetails[i].order_product_amount),
                                    order_product_total_sum: parseFloat(mainType.subTypeDetails[i].order_product_total_sum),
                                };
                            }
                        } else {
                            subType[setIndex] = {
                                product_id: mainType.subTypeDetails[i].product_id,
                                product_name: mainType.subTypeDetails[i].product_name,
                                order_product_price: parseFloat(mainType.subTypeDetails[i].order_product_price),
                                order_product_amount: parseInt(mainType.subTypeDetails[i].order_product_amount),
                                order_product_total_sum: parseFloat(mainType.subTypeDetails[i].order_product_total_sum),
                            };
                        }

                    }
                    this.dailySaleProductSubType = subType;
                },
                deleteProductOrders: function(orders) {
                    this.orderCount -= 1;
                    for (let i = 0; i < this.ordersAll.length; i++) {
                        if (this.ordersAll[i].id == orders.id) {
                            this.orderCountAll -= orders.amount;
                            this.ordersAll.splice(i, 1);
                        }
                    }
                    this.calOrders();
                },
                calOrders: function() {
                    this.calDiscount();
                    this.sum = 0;
                    for (let i = 0; i < this.ordersAll.length; i++) {
                        this.sum += this.ordersAll[i].total;
                    }
                    this.totalSum = ((this.sum * this.vat / 100) + this.sum) - parseFloat(this.discount);
                },
                sortMenu: function(menuTypeId) {
                    this.menuSort = this.menuSort.slice(0, 0);
                    if (menuTypeId == "bestSales") {
                        this.loadMenu();
                    } else {
                        for (let i = 0; i < this.menuAll.length; i++) {
                            if (this.menuAll[i].type == menuTypeId) {
                                this.menuSort.push(this.menuAll[i]);
                            }
                        }
                    }
                },
                loadMenutype: function() {
                    var config = {
                        method: 'get',
                        url: `<?= urlTobackend ?>loadTypeProduct.php?companyId=${this.companyBy}&branchId=${this.branchBy}`,
                        headers: {
                            'Accept': 'application/json',
                            // 'Authorization': 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiI5IiwianRpIjoiYTJiMzdhNGViZjU1NDRmMDliZDY2ZTk0NWJhMmQxNGE1Nzg3YTk2ZGMyYmMzNGMyNjk2OTgxMzc1OWFkOGYxYjk5MGUwNjJiNTgzYzRhM2YiLCJpYXQiOjE2MjAyNjkwMjIsIm5iZiI6MTYyMDI2OTAyMiwiZXhwIjoxNjUxODA1MDIyLCJzdWIiOiIxIiwic2NvcGVzIjpbXX0.do6kFU1NYBpP1CctpkOe0CK-ZRmQSttF6pK_mnX01Bgv2trq1aHmBKw26YDnWEYR-v3IxqzwTZ32ab5O1PGWRttH20hGUZi4MXQITJ7a1I_JJVnt5WLLR9TCy9yr9FkkvFtpFjyyjEwq2i9crIzWp1VEpIPj563kIUzRlqc7bth0whoXHPKDuxbW0h7K_Vuh9mSc79npKdR_jQdlGLAig9QSVPHGZizZfxsuzy5Cwz6wTpbQMWHm-7JEZIaQt-snW19NJ65fEM7MJU7sgxpN-cUnC13o9xtV7Nj8sq7PYIJo9XnTCg3w541ClAYFlQy1ZGTfurVAoD8_DAUX73fUYN30Jyb9jf-TlTaD4zfjnF784QX0Gpe0HY8aN2ApXVdsaSTk_vq-SPLP9hWgs1OMKxX7YrsBlq6adWtnqUBQ9sTnllskqn9mF3-U06F6VBxsjHJZv3gWD0ot2ivGhZWzs8BvTd5d62YCLaQZQsxgHbJbLFBO2YTWsHnP9TnAD6C23PZa0hmTfvsYXrqePAi2Wa7-UPOtF9wYSNzhhFqbLaQ2-MWjMDAmDfX8LQM4w1ZNBmcxS5pZG3sqdIMmU4KJ8WwImXlYbWv5P2JQXjA6sgFCDciARi0NP1Y_M4pWsK_ipkpESIxz9RRb3zabEDKtWgjgp_8DPi0swZ7VR56bIiM',
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        withCredentials: true
                    };
                    axios.defaults.withCredentials = true;
                    axios(config).then((response) => {
                        if (response.data.status) {
                            this.menuType = response.data.results;
                        } else {
                            console.log(response.data);
                        }
                    }).catch((e) => {
                        console.log(e);
                    });
                    // return axios(config);
                },
                loadMenu: function() {
                    var config = {
                        method: 'get',
                        url: `<?= urlTobackend ?>loadProducts.php?companyId=${this.companyBy}&branchId=${this.branchBy}`,
                        headers: {
                            'Accept': 'application/json',
                            // 'Authorization': 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiI5IiwianRpIjoiYTJiMzdhNGViZjU1NDRmMDliZDY2ZTk0NWJhMmQxNGE1Nzg3YTk2ZGMyYmMzNGMyNjk2OTgxMzc1OWFkOGYxYjk5MGUwNjJiNTgzYzRhM2YiLCJpYXQiOjE2MjAyNjkwMjIsIm5iZiI6MTYyMDI2OTAyMiwiZXhwIjoxNjUxODA1MDIyLCJzdWIiOiIxIiwic2NvcGVzIjpbXX0.do6kFU1NYBpP1CctpkOe0CK-ZRmQSttF6pK_mnX01Bgv2trq1aHmBKw26YDnWEYR-v3IxqzwTZ32ab5O1PGWRttH20hGUZi4MXQITJ7a1I_JJVnt5WLLR9TCy9yr9FkkvFtpFjyyjEwq2i9crIzWp1VEpIPj563kIUzRlqc7bth0whoXHPKDuxbW0h7K_Vuh9mSc79npKdR_jQdlGLAig9QSVPHGZizZfxsuzy5Cwz6wTpbQMWHm-7JEZIaQt-snW19NJ65fEM7MJU7sgxpN-cUnC13o9xtV7Nj8sq7PYIJo9XnTCg3w541ClAYFlQy1ZGTfurVAoD8_DAUX73fUYN30Jyb9jf-TlTaD4zfjnF784QX0Gpe0HY8aN2ApXVdsaSTk_vq-SPLP9hWgs1OMKxX7YrsBlq6adWtnqUBQ9sTnllskqn9mF3-U06F6VBxsjHJZv3gWD0ot2ivGhZWzs8BvTd5d62YCLaQZQsxgHbJbLFBO2YTWsHnP9TnAD6C23PZa0hmTfvsYXrqePAi2Wa7-UPOtF9wYSNzhhFqbLaQ2-MWjMDAmDfX8LQM4w1ZNBmcxS5pZG3sqdIMmU4KJ8WwImXlYbWv5P2JQXjA6sgFCDciARi0NP1Y_M4pWsK_ipkpESIxz9RRb3zabEDKtWgjgp_8DPi0swZ7VR56bIiM',
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        withCredentials: true
                    };
                    axios.defaults.withCredentials = true;
                    axios(config).then((response) => {
                        if (response.data.status) {
                            this.menuAll = response.data.results;
                            for (let i = 0; i < this.menuAll.length; i++) {
                                this.menuSort.push(this.menuAll[i]);
                            }
                        } else {
                            console.log(response.data);
                        }
                    }).catch((e) => {
                        console.log(e);
                    });
                },
                payments: function(e) {
                    this.isButtonActive = e.keyCode;
                    let checkMoneyPay = this.checkMoneyPay();
                    if ((e.keyCode >= 48 && e.keyCode <= 57) || (e.keyCode >= 96 && e.keyCode <= 105)) {

                    } else {

                    }
                    if (e.keyCode == 13) {
                        if (checkMoneyPay) {
                            this.sendPaymentToBackend();
                        }

                    }
                },
                checkMoneyPay: function() {
                    let checkValid = true;
                    if (this.moneyPay == "") {
                        this.cashChange = "ระบุจำนวนเงินที่ชำระ";
                        checkValid = false;
                    } else if (isNaN(this.moneyPay)) {
                        this.cashChange = "กรอกตัวเลขเท่านั้น!";
                        checkValid = false;
                    } else if (this.cashChange == "กรอกตัวเลขเท่านั้น!") {
                        checkValid = false;
                    } else if (this.moneyPay < this.totalSum) {
                        this.cashChange = "ชำระเงินไม่ถูกต้อง!";
                        checkValid = false;
                    } else {
                        this.calCashChange();
                    }
                    if (checkValid === true && this.moneyPay != "") {
                        this.paymentConfirm = true;
                    } else {
                        this.paymentConfirm = false;
                    }
                    return checkValid;
                },
                sendPaymentToBackend: function() {
                    let checkMoneyPay = this.checkMoneyPay();
                    if (checkMoneyPay) {
                        let bodyFormData = new FormData();
                        bodyFormData.append('ordersAll', JSON.stringify(this.ordersAll));
                        bodyFormData.append('discountAll', JSON.stringify(this.discountList));
                        bodyFormData.append('paymentType', this.paymentConfirmType);
                        bodyFormData.append('currentShiftId', this.currentShiftId);
                        bodyFormData.append('sum', this.sum);
                        bodyFormData.append('vat', this.vat);
                        bodyFormData.append('discount', this.discount);
                        bodyFormData.append('totalSum', this.totalSum);
                        bodyFormData.append('orderCount', this.orderCount);
                        bodyFormData.append('orderCountAll', this.orderCountAll);
                        bodyFormData.append('moneyPay', this.moneyPay);
                        bodyFormData.append('cashChange', this.cashChange);
                        bodyFormData.append('billBy', this.loginBy);
                        bodyFormData.append('companyBy', this.companyBy);
                        bodyFormData.append('branchBy', this.branchBy);
                        if (this.imageUploadList.uploadType != "") {
                            // this.imageUploadList.imageData = string url File
                            bodyFormData.append('slipFile', JSON.stringify(this.imageUploadList));
                        }
                        var config = {
                            method: 'post',
                            url: '<?= urlTobackend ?>insertBills',
                            headers: {
                                'Accept': 'application/json',
                                // 'Authorization': 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiI5IiwianRpIjoiYTJiMzdhNGViZjU1NDRmMDliZDY2ZTk0NWJhMmQxNGE1Nzg3YTk2ZGMyYmMzNGMyNjk2OTgxMzc1OWFkOGYxYjk5MGUwNjJiNTgzYzRhM2YiLCJpYXQiOjE2MjAyNjkwMjIsIm5iZiI6MTYyMDI2OTAyMiwiZXhwIjoxNjUxODA1MDIyLCJzdWIiOiIxIiwic2NvcGVzIjpbXX0.do6kFU1NYBpP1CctpkOe0CK-ZRmQSttF6pK_mnX01Bgv2trq1aHmBKw26YDnWEYR-v3IxqzwTZ32ab5O1PGWRttH20hGUZi4MXQITJ7a1I_JJVnt5WLLR9TCy9yr9FkkvFtpFjyyjEwq2i9crIzWp1VEpIPj563kIUzRlqc7bth0whoXHPKDuxbW0h7K_Vuh9mSc79npKdR_jQdlGLAig9QSVPHGZizZfxsuzy5Cwz6wTpbQMWHm-7JEZIaQt-snW19NJ65fEM7MJU7sgxpN-cUnC13o9xtV7Nj8sq7PYIJo9XnTCg3w541ClAYFlQy1ZGTfurVAoD8_DAUX73fUYN30Jyb9jf-TlTaD4zfjnF784QX0Gpe0HY8aN2ApXVdsaSTk_vq-SPLP9hWgs1OMKxX7YrsBlq6adWtnqUBQ9sTnllskqn9mF3-U06F6VBxsjHJZv3gWD0ot2ivGhZWzs8BvTd5d62YCLaQZQsxgHbJbLFBO2YTWsHnP9TnAD6C23PZa0hmTfvsYXrqePAi2Wa7-UPOtF9wYSNzhhFqbLaQ2-MWjMDAmDfX8LQM4w1ZNBmcxS5pZG3sqdIMmU4KJ8WwImXlYbWv5P2JQXjA6sgFCDciARi0NP1Y_M4pWsK_ipkpESIxz9RRb3zabEDKtWgjgp_8DPi0swZ7VR56bIiM',
                                'Content-Type': 'application/x-www-form-urlencoded',

                            },
                            data: bodyFormData,
                            withCredentials: true
                        };
                        axios(config).then((response) => {
                            if (response.data.status) {
                                console.log(response.data);
                                let billId = response.data.results.billId;
                                window.open(`<?= domain ?>view/bill-detail?billId=${btoa(billId)}&companyId=${btoa(this.companyBy)}&branchId=${btoa(this.branchBy)}`, '_blank').focus();
                                Swal.fire({
                                    title: 'ทำรายการสำเร็จ :)',
                                    text: response.data.message,
                                    confirmButtonText: `ปิด`,
                                    showLoaderOnConfirm: true,
                                    icon: "success",
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        app.firstPayment = false;
                                        app.cancelOrder();
                                    }
                                })
                            } else {
                                console.log(response.data);
                            }
                        }).catch((e) => {
                            console.log(e);
                        });
                    }


                },
                calCashChange: function() {
                    this.cashChange = this.moneyPay - this.totalSum;
                },
                cancelOrder: function() {
                    this.ordersAll = this.ordersAll.slice(0, 0); //วิธี set array empty vuejs
                    this.discountList = this.discountList.slice(0, 0);
                    this.sum = 0;
                    this.vat = 0;
                    this.discount = 0;
                    this.totalSum = 0;
                    this.orderCount = 0;
                    this.moneyPay = 0;
                    this.cashChange = 0;
                    this.calculatorButton = 0;
                    this.isButtonActive = 0;
                    this.isButtonNotActive = 0;
                    this.paymentConfirm = false;
                },
                myFilter: function() {
                    this.isActive1 = !this.isActive1;
                    // some code to filter users
                },
                showBillDetail: function(bill) {
                    this.billDetail = bill;
                    console.log(this.billDetail);
                },
                sendToBackend: function() {
                    if (returnFormBackend.status) {

                    }
                },
                pushCancelBill: function(bill) {
                    this.billCancel.push(bill);

                },
                showUserConfirm: function() {
                    if (this.billCancel.length > 0) {
                        this.modalUserConfirm = 'confirmCancel';
                    }
                },
                loadAllBill: function() {
                    var config = {
                        method: 'get',
                        url: `<?= urlTobackend ?>loadBills?companyId=${this.companyBy}&branchId=${this.branchBy}&dateSelect=<?= date("Y-m-d") ?>`,
                        headers: {
                            'Accept': 'application/json',
                            // 'Authorization': 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiI5IiwianRpIjoiYTJiMzdhNGViZjU1NDRmMDliZDY2ZTk0NWJhMmQxNGE1Nzg3YTk2ZGMyYmMzNGMyNjk2OTgxMzc1OWFkOGYxYjk5MGUwNjJiNTgzYzRhM2YiLCJpYXQiOjE2MjAyNjkwMjIsIm5iZiI6MTYyMDI2OTAyMiwiZXhwIjoxNjUxODA1MDIyLCJzdWIiOiIxIiwic2NvcGVzIjpbXX0.do6kFU1NYBpP1CctpkOe0CK-ZRmQSttF6pK_mnX01Bgv2trq1aHmBKw26YDnWEYR-v3IxqzwTZ32ab5O1PGWRttH20hGUZi4MXQITJ7a1I_JJVnt5WLLR9TCy9yr9FkkvFtpFjyyjEwq2i9crIzWp1VEpIPj563kIUzRlqc7bth0whoXHPKDuxbW0h7K_Vuh9mSc79npKdR_jQdlGLAig9QSVPHGZizZfxsuzy5Cwz6wTpbQMWHm-7JEZIaQt-snW19NJ65fEM7MJU7sgxpN-cUnC13o9xtV7Nj8sq7PYIJo9XnTCg3w541ClAYFlQy1ZGTfurVAoD8_DAUX73fUYN30Jyb9jf-TlTaD4zfjnF784QX0Gpe0HY8aN2ApXVdsaSTk_vq-SPLP9hWgs1OMKxX7YrsBlq6adWtnqUBQ9sTnllskqn9mF3-U06F6VBxsjHJZv3gWD0ot2ivGhZWzs8BvTd5d62YCLaQZQsxgHbJbLFBO2YTWsHnP9TnAD6C23PZa0hmTfvsYXrqePAi2Wa7-UPOtF9wYSNzhhFqbLaQ2-MWjMDAmDfX8LQM4w1ZNBmcxS5pZG3sqdIMmU4KJ8WwImXlYbWv5P2JQXjA6sgFCDciARi0NP1Y_M4pWsK_ipkpESIxz9RRb3zabEDKtWgjgp_8DPi0swZ7VR56bIiM',
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        withCredentials: true
                    };
                    axios.defaults.withCredentials = true;
                    axios(config).then((response) => {
                        if (response.data.status) {
                            this.billAll = this.billAll.slice(0, 0); //ลบของเดิมก่อน
                            let newSetData = this.groupArrayByObjectKey(response.data.results);
                            for (let i = 0; i < newSetData.length; i++) {
                                this.billAll.push({
                                    id: newSetData[i][0].bill_sub_id,
                                    mainId: newSetData[i][0].bill_id,
                                    created_at: newSetData[i][0].created_at,
                                    sum: newSetData[i][0].bill_sum,
                                    vat: newSetData[i][0].bill_bat,
                                    discount: newSetData[i][0].bill_discount,
                                    totalSum: newSetData[i][0].bill_total_sum,
                                    orderCount: newSetData[i][0].bill_order_count,
                                    orderCountAll: newSetData[i][0].bill_order_count_all,
                                    moneyPay: newSetData[i][0].bill_money_pay,
                                    cashChange: newSetData[i][0].bill_money_change,
                                    billActive: newSetData[i][0].bill_active,
                                    orders: []
                                });
                                for (let x = 0; x < newSetData[i].length; x++) {
                                    // if (response.data.results[i].bill_id == response.data.results[x].bill_id) {
                                    this.billAll[i].orders.push({
                                        name: newSetData[i][x].product_name,
                                        price: newSetData[i][x].order_product_price,
                                        amount: newSetData[i][x].order_product_amount,
                                        total: newSetData[i][x].order_product_total_sum
                                    });
                                    // }
                                }
                            }
                        } else {
                            console.log(response.data);
                        }
                    }).catch((e) => {
                        console.log(e);
                    });
                },
                groupArrayByObjectKey: function(arrayData) {
                    let group = arrayData.reduce((r, a) => {
                        r[a.bill_id] = [...r[a.bill_id] || [], a];
                        return r;
                    }, {});
                    var results = Object.keys(group).map((key) => group[key]);
                    return results;
                },
                groupArrayDiscountByObjectKey: function(arrayData) {
                    let group = arrayData.reduce((r, a) => {
                        r[a.discount_type_name] = [...r[a.discount_type_name] || [], a];
                        return r;
                    }, {});
                    // var results = Object.keys(group).map((key) => group[key]); //อันนี้จะแปลงจาก key ส่วนลดทั้งหมด: เป็น 0, 1,2
                    return group;
                },
                userConfirmSend: function() {
                    let setJson = {
                        companyBy: this.companyBy,
                        branchBy: this.branchBy,
                        confirmPassword: this.confirmPassword,
                    }
                    let bodyFormData = new FormData();
                    bodyFormData.append('formLogin', JSON.stringify(setJson));

                    var config = {
                        method: 'post',
                        url: `<?= urlTobackend ?>checkCompanyUserPermission.php`,
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
                            this.afterUserConfirmCancelBill();
                        } else {
                            this.errCheck = true;
                            this.errMsg = response.data.message;
                        }
                    }).catch((e) => {
                        console.log(e);
                    });



                },
                afterUserConfirmCancelBill: function() {
                    var config = {
                        method: 'get',
                        url: `<?= urlTobackend ?>cancelBills?companyId=${this.companyBy}&branchId=${this.branchBy}&billId=${this.billCancel[0].mainId}&billSubId=${this.billCancel[0].id}`,
                        headers: {
                            'Accept': 'application/json',
                            // 'Authorization': 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiI5IiwianRpIjoiYTJiMzdhNGViZjU1NDRmMDliZDY2ZTk0NWJhMmQxNGE1Nzg3YTk2ZGMyYmMzNGMyNjk2OTgxMzc1OWFkOGYxYjk5MGUwNjJiNTgzYzRhM2YiLCJpYXQiOjE2MjAyNjkwMjIsIm5iZiI6MTYyMDI2OTAyMiwiZXhwIjoxNjUxODA1MDIyLCJzdWIiOiIxIiwic2NvcGVzIjpbXX0.do6kFU1NYBpP1CctpkOe0CK-ZRmQSttF6pK_mnX01Bgv2trq1aHmBKw26YDnWEYR-v3IxqzwTZ32ab5O1PGWRttH20hGUZi4MXQITJ7a1I_JJVnt5WLLR9TCy9yr9FkkvFtpFjyyjEwq2i9crIzWp1VEpIPj563kIUzRlqc7bth0whoXHPKDuxbW0h7K_Vuh9mSc79npKdR_jQdlGLAig9QSVPHGZizZfxsuzy5Cwz6wTpbQMWHm-7JEZIaQt-snW19NJ65fEM7MJU7sgxpN-cUnC13o9xtV7Nj8sq7PYIJo9XnTCg3w541ClAYFlQy1ZGTfurVAoD8_DAUX73fUYN30Jyb9jf-TlTaD4zfjnF784QX0Gpe0HY8aN2ApXVdsaSTk_vq-SPLP9hWgs1OMKxX7YrsBlq6adWtnqUBQ9sTnllskqn9mF3-U06F6VBxsjHJZv3gWD0ot2ivGhZWzs8BvTd5d62YCLaQZQsxgHbJbLFBO2YTWsHnP9TnAD6C23PZa0hmTfvsYXrqePAi2Wa7-UPOtF9wYSNzhhFqbLaQ2-MWjMDAmDfX8LQM4w1ZNBmcxS5pZG3sqdIMmU4KJ8WwImXlYbWv5P2JQXjA6sgFCDciARi0NP1Y_M4pWsK_ipkpESIxz9RRb3zabEDKtWgjgp_8DPi0swZ7VR56bIiM',
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        withCredentials: true
                    };
                    axios.defaults.withCredentials = true;
                    axios(config)
                        .then((response) => {
                            if (response.data.status) {
                                this.returnFormBackend.status = true;
                                if (this.returnFormBackend.status) {
                                    for (let i = 0; i < this.billAll.length; i++) {
                                        if (this.billAll[i].id == this.billCancel[0].id) {
                                            this.billAll[i].billActive = 0;
                                            break;
                                        }
                                    }
                                    this.dynamicMessage = `ยกเลิกบิล ${this.billCancel[0].id} เรียบร้อย`;
                                    this.billCancel.splice(0, 1);
                                    this.cancelStatus = true;
                                    this.confirmCancelCheck = false;
                                    this.returnFormBackend.status = false;
                                }
                            }
                        })
                        .catch((e) => {
                            console.log(e);
                        });
                },

                calDiscount: function() {
                    this.discount = 0;
                    for (let i = 0; i < this.discountList.length; i++) {
                        this.discount += parseFloat(this.discountList[i].discount_choice_value) * parseFloat(this.discountList[i].discount_choice_count);
                    }
                },
                setDiscountList: function(discountItem) {
                    if (typeof discountItem !== 'object') {
                        return;
                    }

                    let discountListFormat = {
                        discount_choice_id: discountItem.discount_choice_id + "",
                        discount_choice_name: discountItem.discount_choice_name,
                        discount_choice_value: discountItem.discount_choice_value,
                        discount_choice_count: 1,
                        discount_type_id: discountItem.discount_type_id,
                        discount_type_name: discountItem.discount_type_name,
                    };
                    if (discountItem.discount_type_name == "กำหนดส่วนลด", discountItem.discount_choice_name == "ระบุเป็น %") {
                        discountListFormat.discount_choice_name = `${discountItem.discount_choice_name} (${discountItem.discount_choice_value}%)`;
                        discountListFormat.discount_choice_value = (this.totalSum * discountItem.discount_choice_value) / 100;
                    }
                    let totalCountDiscount = 0;
                    let checkDiscountSame = null;

                    if (this.discountList.length > 0 && discountItem.discount_type_name != "กำหนดส่วนลด") {
                        for (let i = 0; i < this.discountList.length; i++) {
                            if (discountItem.discount_choice_id == this.discountList[i].discount_choice_id) { //เผื่อเผลอตั้งชื่อเหมือนกันเลยใช้ id
                                checkDiscountSame = i;
                                break;
                            }
                        }
                    }
                    if (checkDiscountSame !== null) {
                        totalCountDiscount = parseFloat(this.discountList[checkDiscountSame].discount_choice_count) + parseFloat(discountListFormat.discount_choice_count);
                        this.discountList[checkDiscountSame].discount_choice_count = totalCountDiscount;
                    }
                    if (this.discountList.length == 0 || checkDiscountSame === null) {
                        this.discountList.push(discountListFormat);
                    }
                    if (discountItem.discount_type_name == "กำหนดส่วนลด") {
                        this.setCustomDiscountEmpty();
                    }

                },
                setCustomDiscountEmpty: function() {
                    this.customDiscountSet.discount_choice_name = "";
                    this.customDiscountSet.discount_choice_value = 0;
                    this.customDiscountSet.discountDetail = "";
                },
                loadBillMessage: function() {
                    var config = {
                        method: 'get',
                        url: `<?= urlTobackend ?>loadBillMessage?companyId=${btoa(this.companyBy)}&branchId=${btoa(this.branchBy)}`,
                        headers: {
                            'Accept': 'application/json',
                            // 'Authorization': 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiI5IiwianRpIjoiYTJiMzdhNGViZjU1NDRmMDliZDY2ZTk0NWJhMmQxNGE1Nzg3YTk2ZGMyYmMzNGMyNjk2OTgxMzc1OWFkOGYxYjk5MGUwNjJiNTgzYzRhM2YiLCJpYXQiOjE2MjAyNjkwMjIsIm5iZiI6MTYyMDI2OTAyMiwiZXhwIjoxNjUxODA1MDIyLCJzdWIiOiIxIiwic2NvcGVzIjpbXX0.do6kFU1NYBpP1CctpkOe0CK-ZRmQSttF6pK_mnX01Bgv2trq1aHmBKw26YDnWEYR-v3IxqzwTZ32ab5O1PGWRttH20hGUZi4MXQITJ7a1I_JJVnt5WLLR9TCy9yr9FkkvFtpFjyyjEwq2i9crIzWp1VEpIPj563kIUzRlqc7bth0whoXHPKDuxbW0h7K_Vuh9mSc79npKdR_jQdlGLAig9QSVPHGZizZfxsuzy5Cwz6wTpbQMWHm-7JEZIaQt-snW19NJ65fEM7MJU7sgxpN-cUnC13o9xtV7Nj8sq7PYIJo9XnTCg3w541ClAYFlQy1ZGTfurVAoD8_DAUX73fUYN30Jyb9jf-TlTaD4zfjnF784QX0Gpe0HY8aN2ApXVdsaSTk_vq-SPLP9hWgs1OMKxX7YrsBlq6adWtnqUBQ9sTnllskqn9mF3-U06F6VBxsjHJZv3gWD0ot2ivGhZWzs8BvTd5d62YCLaQZQsxgHbJbLFBO2YTWsHnP9TnAD6C23PZa0hmTfvsYXrqePAi2Wa7-UPOtF9wYSNzhhFqbLaQ2-MWjMDAmDfX8LQM4w1ZNBmcxS5pZG3sqdIMmU4KJ8WwImXlYbWv5P2JQXjA6sgFCDciARi0NP1Y_M4pWsK_ipkpESIxz9RRb3zabEDKtWgjgp_8DPi0swZ7VR56bIiM',
                            'Content-Type': 'application/x-www-form-urlencoded',

                        },
                        withCredentials: true
                    };
                    axios(config).then((response) => {
                        if (response.data.status) {
                            this.thankMessageForBillLastLine = response.data.results;
                        } else {
                            console.log(response.data);
                        }
                    }).catch((e) => {
                        console.log(e);
                    });

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
                        this.requestCameraStatus = true;
                        this.removeImage(); // ถ้าถ่ายรูปให้ลบรูปใน upload ออก
                        this.showAndHideTakePhotoOrChoosePhoto(1);
                    } else {
                        buttonId.classList.remove("btn-danger");
                        video.classList.add("d-none");
                        buttonId.innerHTML = "เปิดกล้อง";
                        this.requestCameraStatus = false;
                        this.stopBothVideoAndAudio(stream);
                        this.clearPhotoImage();
                        this.clearImageDataIfDeleteTakePhotoClick();
                        this.showAndHideTakePhotoOrChoosePhoto(0);
                    }
                },
                async takePhoto(elemCameraId, photoId, button, cameraContainer = "") {
                    if (this.requestCameraStatus === false) return;
                    let stream = await this.startCamera();
                    let buttonId = document.querySelector("#" + button);
                    let videoContainer = document.querySelector("#" + cameraContainer);
                    let video = document.querySelector("#" + elemCameraId); //เรียกใช้ใน compute
                    let canvasPhoto = document.querySelector("#" + photoId); //เรียกใช้ใน compute
                    let videoHeight = video.offsetHeight;
                    let videoWidth = video.offsetWidth;
                    if (buttonId.innerHTML == "ถ่ายรูป") {
                        buttonId.classList.add("btn-danger");
                        buttonId.innerHTML = "ลบรูป";
                        canvasPhoto.style.width = videoWidth + "px";
                        canvasPhoto.style.height = videoHeight + "px";
                        this.stopBothVideoAndAudio(stream);
                        videoContainer.classList.add("d-none");
                        this.requestCameraStatus = true;
                        this.showTakePhotoImage(elemCameraId, photoId, button, 1); // 1 = takePhoto success
                    } else {
                        videoContainer.classList.remove("d-none");
                        buttonId.classList.remove("btn-danger");
                        buttonId.innerHTML = "ถ่ายรูป";
                        video.srcObject = stream;
                        this.clearPhotoImage();
                        this.clearImageDataIfDeleteTakePhotoClick();
                        this.showAndHideTakePhotoOrChoosePhoto(1);
                        this.showTakePhotoImage(elemCameraId, photoId, button, 0); // 0 = takePhoto failed
                    }
                },
                showTakePhotoImage(elemCameraId, photoId, button, status) {
                    let canvas = document.querySelector("#" + photoId);
                    if (status == 0) {
                        canvas.classList.add("d-none");
                        this.clearImageDataIfDeleteTakePhotoClick();
                    } else {
                        canvas.classList.remove("d-none");
                        let video = document.querySelector("#" + elemCameraId);
                        canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
                        let imageDataUrl = canvas.toDataURL('image/png');
                        this.imageUploadList.imageData = imageDataUrl;
                        this.imageUploadList.uploadType = "fileTakePhoto";
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
                clearImageDataIfDeleteTakePhotoClick() {
                    this.imageUploadList.imageData = "";
                    this.imageUploadList.uploadType = "";
                },
                clearPhotoImage() {
                    // clear image data sent to backend
                    let canvas = document.querySelector("canvas");
                    let canvasTakePhoto = document.querySelector(".canvas-take-photo");
                    canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height);
                    canvasTakePhoto.classList.add("d-none");
                },
                async clearChangeTypePeyment() {
                    let stream = await this.startCamera();
                    this.stopBothVideoAndAudio(stream);
                    this.imageUploadList.imageData = "";
                    this.imageUploadList.uploadType = "";
                    this.imageUploadList.imagePreview = "";
                    this.imageUploadList.imageSelected = false;
                    this.imageUploadList.imageName = "";
                    this.requestCameraStatus = false;
                },
                createImage(e) {
                    const files = e.target.files || e.dataTransfer.files;
                    if (!files.length) {
                        this.imageUploadList.imageData = "";
                        this.checkFile();
                        return;
                    }
                    const file = files[0];
                    const image = new Image();
                    const reader = new FileReader();
                    if (!this.checkFile(file)) {
                        this.imageUploadList.imageData = "";
                        return;
                    }
                    this.fileValidation = [];
                    // this.imageUploadList.imageData = file; //ใช้ส่งไป backend ส่งปกติ ใช้ $_FILE[] ใน php รับ
                    this.imageUploadList.imageName = file.name;
                    this.requestCameraStatus = false;
                    reader.onload = (e) => {
                        this.imageUploadList.imageData = e.target.result;; //ใช้ส่งไป backend ให้ส่งแบบ url file แบบ canvas
                        this.imageUploadList.imagePreview = e.target.result;
                        this.imageUploadList.uploadType = "fileUpload";
                    };
                    reader.readAsDataURL(file);
                },
                removeImage: function() {
                    this.imageUploadList.imagePreview = "";
                    this.imageUploadList.imageData = "";
                    this.imageUploadList.uploadType = "";
                },
                checkFile(file) {
                    const classCustomValidation = new CustomValidation();
                    let returnData = [];
                    let check = classCustomValidation.checkFile(file, 3000000, ["jpeg", "png", "jpg"])
                    let statusCheck = true;
                    if (check.length > 0) {
                        this.fileValidation = check;
                        statusCheck = false;
                    }
                    return statusCheck;
                },
                loadShift() {
                    const classMyLibary = new myLibary();
                    var config = {
                        method: 'get',
                        url: `<?= urlTobackend ?>loadShift.php?companyId=${this.companyBy}&branchId=${this.branchBy}&currentDate=${classMyLibary.getCurrentDate()}`,
                        headers: {
                            'Accept': 'application/json',
                            // 'Authorization': 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiI5IiwianRpIjoiYTJiMzdhNGViZjU1NDRmMDliZDY2ZTk0NWJhMmQxNGE1Nzg3YTk2ZGMyYmMzNGMyNjk2OTgxMzc1OWFkOGYxYjk5MGUwNjJiNTgzYzRhM2YiLCJpYXQiOjE2MjAyNjkwMjIsIm5iZiI6MTYyMDI2OTAyMiwiZXhwIjoxNjUxODA1MDIyLCJzdWIiOiIxIiwic2NvcGVzIjpbXX0.do6kFU1NYBpP1CctpkOe0CK-ZRmQSttF6pK_mnX01Bgv2trq1aHmBKw26YDnWEYR-v3IxqzwTZ32ab5O1PGWRttH20hGUZi4MXQITJ7a1I_JJVnt5WLLR9TCy9yr9FkkvFtpFjyyjEwq2i9crIzWp1VEpIPj563kIUzRlqc7bth0whoXHPKDuxbW0h7K_Vuh9mSc79npKdR_jQdlGLAig9QSVPHGZizZfxsuzy5Cwz6wTpbQMWHm-7JEZIaQt-snW19NJ65fEM7MJU7sgxpN-cUnC13o9xtV7Nj8sq7PYIJo9XnTCg3w541ClAYFlQy1ZGTfurVAoD8_DAUX73fUYN30Jyb9jf-TlTaD4zfjnF784QX0Gpe0HY8aN2ApXVdsaSTk_vq-SPLP9hWgs1OMKxX7YrsBlq6adWtnqUBQ9sTnllskqn9mF3-U06F6VBxsjHJZv3gWD0ot2ivGhZWzs8BvTd5d62YCLaQZQsxgHbJbLFBO2YTWsHnP9TnAD6C23PZa0hmTfvsYXrqePAi2Wa7-UPOtF9wYSNzhhFqbLaQ2-MWjMDAmDfX8LQM4w1ZNBmcxS5pZG3sqdIMmU4KJ8WwImXlYbWv5P2JQXjA6sgFCDciARi0NP1Y_M4pWsK_ipkpESIxz9RRb3zabEDKtWgjgp_8DPi0swZ7VR56bIiM',
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        withCredentials: true
                    };
                    axios.defaults.withCredentials = true;
                    return axios(config)
                },
                validateOpenShift(numberOnly) {
                    const classCustomValidation = new CustomValidation();
                    const returnData = classCustomValidation.checkNumeric([{
                        openShiftAmount: numberOnly
                    }]);
                    return returnData;
                },
                sendOpenShiftToBackend() {
                    const checkOpenShift = this.validateOpenShift(this.openShiftAmount);
                    if (checkOpenShift.length > 0) {
                        this.errorInfo = checkOpenShift;
                        return;
                    }
                    this.errorInfo = [];
                    Swal.fire({
                        title: `ยืนยันเปิดกะด้วยจำนวนเงิน ${ new Intl.NumberFormat().format(this.openShiftAmount) } บาท?`,
                        showDenyButton: true,
                        // showCancelButton: true,
                        confirmButtonText: `ยืนยัน`,
                        denyButtonText: `ยกเลิก`,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            let bodyFormData = new FormData();
                            bodyFormData.append('jsonData', JSON.stringify({
                                companyId: this.companyBy,
                                branchId: this.branchBy,
                                openBy: this.loginBy,
                                openShiftAmount: this.openShiftAmount
                            }));

                            var config = {
                                method: 'post',
                                url: '<?= urlTobackend ?>openShift',
                                headers: {
                                    'Accept': 'application/json',
                                    // 'Authorization': 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiI5IiwianRpIjoiYTJiMzdhNGViZjU1NDRmMDliZDY2ZTk0NWJhMmQxNGE1Nzg3YTk2ZGMyYmMzNGMyNjk2OTgxMzc1OWFkOGYxYjk5MGUwNjJiNTgzYzRhM2YiLCJpYXQiOjE2MjAyNjkwMjIsIm5iZiI6MTYyMDI2OTAyMiwiZXhwIjoxNjUxODA1MDIyLCJzdWIiOiIxIiwic2NvcGVzIjpbXX0.do6kFU1NYBpP1CctpkOe0CK-ZRmQSttF6pK_mnX01Bgv2trq1aHmBKw26YDnWEYR-v3IxqzwTZ32ab5O1PGWRttH20hGUZi4MXQITJ7a1I_JJVnt5WLLR9TCy9yr9FkkvFtpFjyyjEwq2i9crIzWp1VEpIPj563kIUzRlqc7bth0whoXHPKDuxbW0h7K_Vuh9mSc79npKdR_jQdlGLAig9QSVPHGZizZfxsuzy5Cwz6wTpbQMWHm-7JEZIaQt-snW19NJ65fEM7MJU7sgxpN-cUnC13o9xtV7Nj8sq7PYIJo9XnTCg3w541ClAYFlQy1ZGTfurVAoD8_DAUX73fUYN30Jyb9jf-TlTaD4zfjnF784QX0Gpe0HY8aN2ApXVdsaSTk_vq-SPLP9hWgs1OMKxX7YrsBlq6adWtnqUBQ9sTnllskqn9mF3-U06F6VBxsjHJZv3gWD0ot2ivGhZWzs8BvTd5d62YCLaQZQsxgHbJbLFBO2YTWsHnP9TnAD6C23PZa0hmTfvsYXrqePAi2Wa7-UPOtF9wYSNzhhFqbLaQ2-MWjMDAmDfX8LQM4w1ZNBmcxS5pZG3sqdIMmU4KJ8WwImXlYbWv5P2JQXjA6sgFCDciARi0NP1Y_M4pWsK_ipkpESIxz9RRb3zabEDKtWgjgp_8DPi0swZ7VR56bIiM',
                                    'Content-Type': 'application/x-www-form-urlencoded',

                                },
                                data: bodyFormData,
                                withCredentials: true
                            };
                            axios(config).then((response) => {
                                if (response.data.status) {
                                    app.lineNotify(new Intl.NumberFormat().format(this.openShiftAmount), `เปิดกะที่: ${(this.shiftData.length == 0 ? "1" : "" + (this.shiftData.subShift.length + 1))}`);
                                    this.openShiftAmount = "";
                                    this.currentShiftId = response.data.results.currentSubShiftId;
                                    Swal.fire({
                                        title: 'เปิดกะสำเร็จ :)',
                                        text: response.data.message,
                                        confirmButtonText: `ลุย`,
                                        showLoaderOnConfirm: true,
                                        icon: "success",
                                    }).then((result) => {
                                        // app.cookieValue = "true" เปิดวัน/เปิดกะแล้ว และยังไม่ปิดวัน/ปิดกะนั้นๆ
                                        // app.cookieValue = "false" เปิดวัน/เปิดกะแล้ว และปิดวัน/ปิดกะนั้นๆแล้ว
                                        app.setCookie(app.cookieName, "true", app.expireDays);
                                        app.openShiftModalStep2 = false;
                                    })
                                } else {
                                    this.errorInfo = response.data.results;
                                    console.log(response.data);
                                }
                            }).catch((e) => {
                                console.log(e);
                            });
                        }
                    });
                },
                runOpenShiftTime() {
                    // Update the count down every 1 second
                    let today = new Date();
                    let time = (today.getHours().toString().length == 1 ? "0" + today.getHours() : today.getHours()) + ':' + ((today.getMinutes() + 1).toString().length == 1 ? "0" + (today.getMinutes() + 1) : (today.getMinutes() + 1)) + ':' + ((today.getSeconds() + 1).toString().length == 1 ? "0" + (today.getSeconds() + 1) : (today.getSeconds() + 1));
                    let date = (today.getDate().toString().length == 1 ? "0" + today.getDate() : today.getDate()) + '-' + ((today.getMonth() + 1).toString().length == 1 ? "0" + (today.getMonth() + 1) : (today.getMonth() + 1)) + '-' + (today.getFullYear() + 543);
                    this.currentTime = date + " " + time;
                },
                randomTextFuntion() {
                    this.haiGumLangJai = this.randomText[Math.floor(Math.random() * (this.randomText.length - 1))];
                },
                showOpenShiftModalStep1() {
                    this.openShiftModalStep1 = true;
                },
                hideOpenShiftModalStep1() {
                    this.openShiftModalStep1 = false;
                },
                setCookie(cookieName, cookieValue, expireDays) {
                    const d = new Date();
                    d.setTime(d.getTime() + (expireDays * 24 * 60 * 60 * 1000));
                    let expires = "expires=" + d.toUTCString();
                    document.cookie = cookieName + "=" + cookieValue + ";" + expires + ";path=/";
                },
                getCookie(cookieName) {
                    let name = cookieName + "=";
                    let ca = document.cookie.split(';');
                    for (let i = 0; i < ca.length; i++) {
                        let c = ca[i];
                        while (c.charAt(0) == ' ') {
                            c = c.substring(1);
                        }
                        if (c.indexOf(name) == 0) {
                            return c.substring(name.length, c.length);
                        }
                    }
                    return "";
                },
                checkCookie(cookieName) {
                    let cookieValue = this.getCookie(cookieName);
                    return cookieValue;
                },
                checkOpenShiftOrNot(checkOpenShiftCookie) {
                    let returnData = false
                    if (this.shiftData.length == 0) {
                        this.loadShiftLoading.status = true;
                        this.showOpenShiftModalStep1();
                        return returnData;
                    }
                    if (parseInt(this.shiftData.mainShift[0].main_shift_close_status) == 1) { //ถ้ามีการปิดวันไปแล้ว ไม่ให้เปิดวันอีก
                        this.checkDayIsClose = true;
                        this.haiGumLangJai = "ไม่สามารถเปิดวันได้อีก เนื่องจากได้ทำการปิดวันไปแล้ว";
                    }
                    let lastSubShiftIndex = this.shiftData.subShift.length - 1;
                    const subShiftCloseStatus = this.shiftData.subShift[lastSubShiftIndex].sub_shift_close_status;
                    // console.log(checkOpenShiftCookie);
                    if (parseInt(subShiftCloseStatus) == 1) {
                        // if (parseInt(subShiftCloseStatus) == 1 && (checkOpenShiftCookie == "" || checkOpenShiftCookie == "true")) {
                        this.loadShiftLoading.status = true;
                        this.showOpenShiftModalStep1();
                    }
                },
                loadCheckCloseDay() {
                    const classMyLibary = new myLibary();
                    var config = {
                        method: 'get',
                        url: `<?= urlTobackend ?>checkCloseShift.php?companyId=${this.companyBy}&branchId=${this.branchBy}&currentDate=${classMyLibary.getCurrentDate()}`,
                        headers: {
                            'Accept': 'application/json',
                            // 'Authorization': 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiI5IiwianRpIjoiYTJiMzdhNGViZjU1NDRmMDliZDY2ZTk0NWJhMmQxNGE1Nzg3YTk2ZGMyYmMzNGMyNjk2OTgxMzc1OWFkOGYxYjk5MGUwNjJiNTgzYzRhM2YiLCJpYXQiOjE2MjAyNjkwMjIsIm5iZiI6MTYyMDI2OTAyMiwiZXhwIjoxNjUxODA1MDIyLCJzdWIiOiIxIiwic2NvcGVzIjpbXX0.do6kFU1NYBpP1CctpkOe0CK-ZRmQSttF6pK_mnX01Bgv2trq1aHmBKw26YDnWEYR-v3IxqzwTZ32ab5O1PGWRttH20hGUZi4MXQITJ7a1I_JJVnt5WLLR9TCy9yr9FkkvFtpFjyyjEwq2i9crIzWp1VEpIPj563kIUzRlqc7bth0whoXHPKDuxbW0h7K_Vuh9mSc79npKdR_jQdlGLAig9QSVPHGZizZfxsuzy5Cwz6wTpbQMWHm-7JEZIaQt-snW19NJ65fEM7MJU7sgxpN-cUnC13o9xtV7Nj8sq7PYIJo9XnTCg3w541ClAYFlQy1ZGTfurVAoD8_DAUX73fUYN30Jyb9jf-TlTaD4zfjnF784QX0Gpe0HY8aN2ApXVdsaSTk_vq-SPLP9hWgs1OMKxX7YrsBlq6adWtnqUBQ9sTnllskqn9mF3-U06F6VBxsjHJZv3gWD0ot2ivGhZWzs8BvTd5d62YCLaQZQsxgHbJbLFBO2YTWsHnP9TnAD6C23PZa0hmTfvsYXrqePAi2Wa7-UPOtF9wYSNzhhFqbLaQ2-MWjMDAmDfX8LQM4w1ZNBmcxS5pZG3sqdIMmU4KJ8WwImXlYbWv5P2JQXjA6sgFCDciARi0NP1Y_M4pWsK_ipkpESIxz9RRb3zabEDKtWgjgp_8DPi0swZ7VR56bIiM',
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        withCredentials: true
                    };
                    axios.defaults.withCredentials = true;
                    return axios(config)
                },
                sendCloseDayToBackend: function() { // function คล้ายกับอันล่าง ตอนสร้างไม่ได้คิดเผื่อไปใช้ต่อ อุแง้
                    const checkOpenShift = this.validateOpenShift(this.openShiftAmount);
                    if (checkOpenShift.length > 0) {
                        this.errorCheckCloseDay = checkOpenShift;
                        return;
                    }
                    this.errorCheckCloseDay = [];
                    Swal.fire({
                        title: `ยืนยันปิดวัน/ปิดกะด้วยจำนวนเงิน ${ new Intl.NumberFormat().format(this.openShiftAmount) } บาท?`,
                        showDenyButton: true,
                        // showCancelButton: true,
                        confirmButtonText: `ยืนยัน`,
                        denyButtonText: `ยกเลิก`,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            let bodyFormData = new FormData();
                            bodyFormData.append('jsonData', JSON.stringify({
                                companyId: this.companyBy,
                                branchId: this.branchBy,
                                closeBy: this.loginBy,
                                cashPayment: this.openShiftAmount,
                                mainShiftId: this.loadCheckCloseDayData.mainShift[0].main_shift_id,
                            }));

                            var config = {
                                method: 'post',
                                url: '<?= urlTobackend ?>closeDay',
                                headers: {
                                    'Accept': 'application/json',
                                    // 'Authorization': 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiI5IiwianRpIjoiYTJiMzdhNGViZjU1NDRmMDliZDY2ZTk0NWJhMmQxNGE1Nzg3YTk2ZGMyYmMzNGMyNjk2OTgxMzc1OWFkOGYxYjk5MGUwNjJiNTgzYzRhM2YiLCJpYXQiOjE2MjAyNjkwMjIsIm5iZiI6MTYyMDI2OTAyMiwiZXhwIjoxNjUxODA1MDIyLCJzdWIiOiIxIiwic2NvcGVzIjpbXX0.do6kFU1NYBpP1CctpkOe0CK-ZRmQSttF6pK_mnX01Bgv2trq1aHmBKw26YDnWEYR-v3IxqzwTZ32ab5O1PGWRttH20hGUZi4MXQITJ7a1I_JJVnt5WLLR9TCy9yr9FkkvFtpFjyyjEwq2i9crIzWp1VEpIPj563kIUzRlqc7bth0whoXHPKDuxbW0h7K_Vuh9mSc79npKdR_jQdlGLAig9QSVPHGZizZfxsuzy5Cwz6wTpbQMWHm-7JEZIaQt-snW19NJ65fEM7MJU7sgxpN-cUnC13o9xtV7Nj8sq7PYIJo9XnTCg3w541ClAYFlQy1ZGTfurVAoD8_DAUX73fUYN30Jyb9jf-TlTaD4zfjnF784QX0Gpe0HY8aN2ApXVdsaSTk_vq-SPLP9hWgs1OMKxX7YrsBlq6adWtnqUBQ9sTnllskqn9mF3-U06F6VBxsjHJZv3gWD0ot2ivGhZWzs8BvTd5d62YCLaQZQsxgHbJbLFBO2YTWsHnP9TnAD6C23PZa0hmTfvsYXrqePAi2Wa7-UPOtF9wYSNzhhFqbLaQ2-MWjMDAmDfX8LQM4w1ZNBmcxS5pZG3sqdIMmU4KJ8WwImXlYbWv5P2JQXjA6sgFCDciARi0NP1Y_M4pWsK_ipkpESIxz9RRb3zabEDKtWgjgp_8DPi0swZ7VR56bIiM',
                                    'Content-Type': 'application/x-www-form-urlencoded',

                                },
                                data: bodyFormData,
                                withCredentials: true
                            };
                            axios(config).then((response) => {
                                if (response.data.status) {

                                    const mainShiftCreateAtExplode = this.loadCheckCloseDayData.mainShift[0].main_shift_created_at.split(" ");
                                    const mainShiftCreateAt = mainShiftCreateAtExplode[0];
                                    const classMyLibary = new myLibary();
                                    const lastDay = -1;
                                    const yesterday = classMyLibary.getCurrentDate(lastDay)
                                    const today = classMyLibary.getCurrentDate()
                                    const dateDiff = classMyLibary.dateDiff(today, mainShiftCreateAt, "day");
                                    // ส่ง line notify
                                    app.lineNotify(new Intl.NumberFormat().format(this.openShiftAmount), `ปิดวันที่ ${mainShiftCreateAt}(${(dateDiff == 0 ? "วันนี้": dateDiff +" วันก่อน")})`);
                                    // app.cookieValue = "true" เปิดวัน/เปิดกะแล้ว และยังไม่ปิดวัน/ปิดกะนั้นๆ
                                    // app.cookieValue = "false" เปิดวัน/เปิดกะแล้ว และปิดวัน/ปิดกะนั้นๆแล้ว
                                    this.openShiftAmount = "";
                                    app.loadCheckCloseDayData = [];
                                    app.setCookie(app.cookieName, "false", app.expireDays);
                                    // ให้ดึงข้อมูลปิดวันมาอีกรอบ เผื่อมีวันหลุด
                                    app.loadCloseDay();
                                    Swal.fire({
                                        title: `ปิดวันที่ ${mainShiftCreateAt}(${(dateDiff == 0 ? "วันนี้": dateDiff +" วันก่อน")}) สำเร็จ :)`,
                                        text: response.data.message,
                                        confirmButtonText: `ลุย`,
                                        showLoaderOnConfirm: true,
                                        icon: "success",
                                    }).then((result) => {

                                    })
                                } else {
                                    this.errorInfo = response.data.results;
                                    console.log(response.data);
                                }
                            }).catch((e) => {
                                console.log(e);
                            });
                        }
                    });
                },
                async loadCloseDay() {
                    const loadCheckCloseDayData = await this.loadCheckCloseDay();
                    if (loadCheckCloseDayData.data.results.length == 0) return
                    this.loadCheckCloseDayData = loadCheckCloseDayData.data.results;
                    let lastDayExplode = this.loadCheckCloseDayData.mainShift[0].main_shift_created_at.split(" ");
                    this.closeDay.title += lastDayExplode[0]; // [0] = เฉพาะวันที่ ไม่เอาเวลา
                    // console.log(loadCheckCloseDayData);
                },
                lineNotify(cash, openOrClose) {
                    const classMyLibary = new myLibary();
                    const today = classMyLibary.getCurrentDate(1, "t")
                    const message = `\nร้าน: ${this.companyData[0].company_name}\nสาขา: ${this.branchData[0].branch_name}\nชื่อพนักงาน: ${this.shift.employeeName}\n${openOrClose}: \nจำนวนเงิน: ${cash} \nวันที่: ${today}`;
                    const secertF = 'O6DDWXKokuuzKObESpXSBbpckVdvUIzmJoEcVVIvWHu';
                    let bodyFormData = new FormData();
                    bodyFormData.append('jsonData', JSON.stringify({
                        companyId: this.companyBy,
                        branchId: this.branchBy,
                        loginBy: this.loginBy,
                        lineMessage: message,
                        lineToken: secertF,
                    }));

                    var config = {
                        method: 'post',
                        url: '<?= urlTobackend ?>lineNotify',
                        headers: {
                            'Accept': 'application/json',
                            // 'Authorization': 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiI5IiwianRpIjoiYTJiMzdhNGViZjU1NDRmMDliZDY2ZTk0NWJhMmQxNGE1Nzg3YTk2ZGMyYmMzNGMyNjk2OTgxMzc1OWFkOGYxYjk5MGUwNjJiNTgzYzRhM2YiLCJpYXQiOjE2MjAyNjkwMjIsIm5iZiI6MTYyMDI2OTAyMiwiZXhwIjoxNjUxODA1MDIyLCJzdWIiOiIxIiwic2NvcGVzIjpbXX0.do6kFU1NYBpP1CctpkOe0CK-ZRmQSttF6pK_mnX01Bgv2trq1aHmBKw26YDnWEYR-v3IxqzwTZ32ab5O1PGWRttH20hGUZi4MXQITJ7a1I_JJVnt5WLLR9TCy9yr9FkkvFtpFjyyjEwq2i9crIzWp1VEpIPj563kIUzRlqc7bth0whoXHPKDuxbW0h7K_Vuh9mSc79npKdR_jQdlGLAig9QSVPHGZizZfxsuzy5Cwz6wTpbQMWHm-7JEZIaQt-snW19NJ65fEM7MJU7sgxpN-cUnC13o9xtV7Nj8sq7PYIJo9XnTCg3w541ClAYFlQy1ZGTfurVAoD8_DAUX73fUYN30Jyb9jf-TlTaD4zfjnF784QX0Gpe0HY8aN2ApXVdsaSTk_vq-SPLP9hWgs1OMKxX7YrsBlq6adWtnqUBQ9sTnllskqn9mF3-U06F6VBxsjHJZv3gWD0ot2ivGhZWzs8BvTd5d62YCLaQZQsxgHbJbLFBO2YTWsHnP9TnAD6C23PZa0hmTfvsYXrqePAi2Wa7-UPOtF9wYSNzhhFqbLaQ2-MWjMDAmDfX8LQM4w1ZNBmcxS5pZG3sqdIMmU4KJ8WwImXlYbWv5P2JQXjA6sgFCDciARi0NP1Y_M4pWsK_ipkpESIxz9RRb3zabEDKtWgjgp_8DPi0swZ7VR56bIiM',
                            'Content-Type': 'application/x-www-form-urlencoded',

                        },
                        data: bodyFormData,
                        withCredentials: true
                    };
                    axios(config).then((response) => {
                        if (response.data) {
                            console.log(response.data);
                        } else {
                            console.log(response);
                        }
                    }).catch((e) => {
                        console.log(e);
                    });
                },
                clearIfCloseModalShiftOrDay() {
                    this.closeShiftOrDay.value = "";
                    this.errorCheckCloseDay = "";
                },
                sendCloseShiftOrDayToBackend(type) {
                    const checkOpenShift = this.validateOpenShift(this.closeShiftOrDay.value);
                    if (checkOpenShift.length > 0) {
                        this.errorCheckCloseDay = checkOpenShift;
                        return;
                    }
                    let urlSend = "";
                    if (type == "closeDay") {
                        urlSend = '<?= urlTobackend ?>closeDay';
                    } else if (type == "closeShift") {
                        urlSend = '<?= urlTobackend ?>closeShift';
                    } else {
                        return;
                    }
                    this.errorCheckCloseDay = [];
                    Swal.fire({
                        title: `ยืนยันปิดวัน/ปิดกะด้วยจำนวนเงิน ${ new Intl.NumberFormat().format(this.closeShiftOrDay.value) } บาท?`,
                        showDenyButton: true,
                        // showCancelButton: true,
                        confirmButtonText: `ยืนยัน`,
                        denyButtonText: `ยกเลิก`,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            let bodyFormData = new FormData();
                            bodyFormData.append('jsonData', JSON.stringify({
                                companyId: this.companyBy,
                                branchId: this.branchBy,
                                closeBy: this.loginBy,
                                cashPayment: this.closeShiftOrDay.value,
                                mainShiftId: this.currentDayId,
                                subShiftId: this.currentShiftId,
                            }));

                            var config = {
                                method: 'post',
                                url: urlSend,
                                headers: {
                                    'Accept': 'application/json',
                                    // 'Authorization': 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiI5IiwianRpIjoiYTJiMzdhNGViZjU1NDRmMDliZDY2ZTk0NWJhMmQxNGE1Nzg3YTk2ZGMyYmMzNGMyNjk2OTgxMzc1OWFkOGYxYjk5MGUwNjJiNTgzYzRhM2YiLCJpYXQiOjE2MjAyNjkwMjIsIm5iZiI6MTYyMDI2OTAyMiwiZXhwIjoxNjUxODA1MDIyLCJzdWIiOiIxIiwic2NvcGVzIjpbXX0.do6kFU1NYBpP1CctpkOe0CK-ZRmQSttF6pK_mnX01Bgv2trq1aHmBKw26YDnWEYR-v3IxqzwTZ32ab5O1PGWRttH20hGUZi4MXQITJ7a1I_JJVnt5WLLR9TCy9yr9FkkvFtpFjyyjEwq2i9crIzWp1VEpIPj563kIUzRlqc7bth0whoXHPKDuxbW0h7K_Vuh9mSc79npKdR_jQdlGLAig9QSVPHGZizZfxsuzy5Cwz6wTpbQMWHm-7JEZIaQt-snW19NJ65fEM7MJU7sgxpN-cUnC13o9xtV7Nj8sq7PYIJo9XnTCg3w541ClAYFlQy1ZGTfurVAoD8_DAUX73fUYN30Jyb9jf-TlTaD4zfjnF784QX0Gpe0HY8aN2ApXVdsaSTk_vq-SPLP9hWgs1OMKxX7YrsBlq6adWtnqUBQ9sTnllskqn9mF3-U06F6VBxsjHJZv3gWD0ot2ivGhZWzs8BvTd5d62YCLaQZQsxgHbJbLFBO2YTWsHnP9TnAD6C23PZa0hmTfvsYXrqePAi2Wa7-UPOtF9wYSNzhhFqbLaQ2-MWjMDAmDfX8LQM4w1ZNBmcxS5pZG3sqdIMmU4KJ8WwImXlYbWv5P2JQXjA6sgFCDciARi0NP1Y_M4pWsK_ipkpESIxz9RRb3zabEDKtWgjgp_8DPi0swZ7VR56bIiM',
                                    'Content-Type': 'application/x-www-form-urlencoded',

                                },
                                data: bodyFormData,
                                withCredentials: true
                            };
                            axios(config).then((response) => {
                                if (response.data.status) {
                                    if (type == "closeDay") {
                                        this.checkDayIsClose = true;
                                        this.haiGumLangJai = "ไม่สามารถเปิดวันได้อีก เนื่องจากได้ทำการปิดวันไปแล้ว";
                                    }
                                    app.loadFinishDayAndShift();
                                    // const mainShiftCreateAtExplode = this.loadCheckCloseDayData.mainShift[0].main_shift_created_at.split(" ");
                                    const mainShiftCreateAt = response.data.results.openDayAt;
                                    const classMyLibary = new myLibary();
                                    const lastDay = -1;
                                    const yesterday = classMyLibary.getCurrentDate(lastDay)
                                    const today = classMyLibary.getCurrentDate()
                                    const dateDiff = classMyLibary.dateDiff(today, mainShiftCreateAt, "day");
                                    // ส่ง line notify
                                    app.lineNotify(new Intl.NumberFormat().format(this.openShiftAmount), `ปิดวันที่ ${mainShiftCreateAt}(${(dateDiff == 0 ? "วันนี้": dateDiff +" วันก่อน")})`);
                                    // app.cookieValue = "true" เปิดวัน/เปิดกะแล้ว และยังไม่ปิดวัน/ปิดกะนั้นๆ
                                    // app.cookieValue = "false" เปิดวัน/เปิดกะแล้ว และปิดวัน/ปิดกะนั้นๆแล้ว
                                    app.closeShiftOrDay.value = "";
                                    app.closeShiftOrDayModal = false;
                                    app.loadCheckCloseDayData = [];

                                    app.setCookie(app.cookieName, "false", app.expireDays);
                                    // ให้ดึงข้อมูลปิดวันมาอีกรอบ เผื่อมีวันหลุด
                                    app.loadCloseDay();
                                    Swal.fire({
                                        title: `ปิดวันที่ ${mainShiftCreateAt}(${(dateDiff == 0 ? "วันนี้": dateDiff +" วันก่อน")}) สำเร็จ :)`,
                                        text: response.data.message,
                                        confirmButtonText: `ลุย`,
                                        showLoaderOnConfirm: true,
                                        icon: "success",
                                    }).then((result) => {})
                                } else {
                                    this.errorInfo = response.data.results;
                                    console.log(response.data);
                                }
                            }).catch((e) => {
                                console.log(e);
                            });
                        }
                        // ทำต่อพรุ่งนี้ sum total_sum - discount ใน ฝั่ง server
                        // หลังจากปิดวันแล้วให้ run function โหลดข้อมูลปิดกะ/ปิดวัน ใหม่
                    });
                },
            },
        });
    </script>
</body>

</html>