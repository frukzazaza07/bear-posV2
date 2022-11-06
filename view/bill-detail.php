<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include("../autoload/loadallLib.php") ?>
    <title>bear-pos</title>
</head>

<body style="background-color: rgba(230,220,220,0.5);">
    <div class="d-flex justify-content-center" style="width: 100%; height: auto">
        <div class="" style="width: 25%; height: auto; background-color: white; padding: 1rem 0">
            <div id="showCompanyDetail" class="bill-head text-center">

            </div>
            <div class="bill-body">
                <div class="d-flex justify-content-center px-3">
                    <table id="showOrders" style="width: 100%;">

                        <hr>
                    </table>
                </div>
                <hr style="margin: .5rem .5rem;">
                <div class="d-flex justify-content-center px-5">
                    <table id="showSum" style="width: 100%;">

                    </table>
                </div>

            </div>
            <div class="bill-footer text-center mt-2" id="showBillMessage">

            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            loadData();
        });
        async function loadData() {
            let rs1 = $.ajax({
                url: '<?= urlTobackend ?>loadCompany.php?companyId=<?= $_GET["companyId"]; ?>&branchId=<?= $_GET["branchId"]; ?>',
                type: "get",
                success: function(res) {
                    let data = JSON.parse(res);
                    if (data.status) {
                        let showCompanyDetail = `    
                            <div>${data.results[0].company_name}</div>
                            <div>TAX#${data.results[0].company_tax} (VAT Included)</div>          
                        `;
                        $("#showCompanyDetail").append(showCompanyDetail);
                    }

                },
                error: function(error) {
                    console.log(error);
                }
            });
            let rs2 = $.ajax({
                url: '<?= urlTobackend ?>loadBill.php?billId=<?= $_GET["billId"]; ?>&companyId=<?= $_GET["companyId"]; ?>&branchId=<?= $_GET["branchId"]; ?>',
                type: "get",
                success: function(res) {
                    let data = JSON.parse(res);
                    if (data.status) {
                        let showSum = `                    
                        <tr>
                            <td style="text-align: left;">ราคารวม (${data.results[0].bill_order_count_all})</td>
                            <td style="text-align: right;">${data.results[0].bill_sum}</td>
                        </tr>
                        <tr>
                            <td style="text-align: left;">VAT(%)</td>
                            <td style="text-align: right;">${data.results[0].bill_vat}</td>
                        </tr>
                        <tr>
                            <td style="text-align: left;">ส่วนลด</td>
                            <td style="text-align: right;">${data.results[0].bill_discount}</td>
                        </tr>
                        <tr>
                            <td style="text-align: left;"><b>ราคาสุทธิ</b></td>
                            <td style="text-align: right;"><b>${data.results[0].bill_total_sum}</b></td>
                        </tr>
                        <tr>
                            <td style="text-align: left;">เงินที่ชำระ</td>
                            <td style="text-align: right;">${data.results[0].bill_money_pay}</td>
                        </tr>
                        <tr>
                            <td style="text-align: left;">เงินทอน</td>
                            <td style="text-align: right;">${data.results[0].bill_money_change}</td>
                        </tr>`;
                        $("#showSum").append(showSum);
                        for (let i = 0; i < data.results.length; i++) {
                            let showOrders = `                        
                        <tr>
                            <td style="text-align: left;">${data.results[i].product_name} ${data.results[i].order_product_price} * ${data.results[i].order_product_amount}</td>
                            <td style="text-align: right;">${data.results[i].order_product_total_sum}</td>
                        </tr>`;
                            $("#showOrders").append(showOrders);
                        }
                        let showCompanyDetail = `<div>เลขที่ใบเสร็จ: ${data.results[0].bill_sub_id}</div>
                                            <div class="mt-2">ใบเสร็จรับเงิน/ใบกำกับภาษีแบบย่อ</div>`;
                        $("#showCompanyDetail").append(showCompanyDetail);
                    } else {
                        console.log(data);
                    }

                },
                error: function(error) {
                    console.log(error);
                }
            });
            let rs3 = $.ajax({
                url: '<?= urlTobackend ?>loadBillMessage.php?companyId=<?= $_GET["companyId"]; ?>&branchId=<?= $_GET["branchId"]; ?>',
                type: "get",
                success: function(res) {
                    let data = JSON.parse(res);
                    console.log(data.results[0].message_detail);
                    if (data.status) {
                        for (let i = 0; i < data.results.length; i++) {
                            let showBillMessage = `    
                                <div>${data.results[i].message_detail}</div>     
                        `;
                            $("#showBillMessage").append(showBillMessage);
                        }
                    } else {
                        console.length(data);
                    }
                },
                error: function(error) {
                    console.log(error);
                }
            });
        }
    </script>
</body>

</html>