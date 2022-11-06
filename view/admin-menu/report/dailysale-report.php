<?php require_once("../template.php"); ?>

<?= templateHeader; ?>
<link rel="stylesheet" href="/bear/css/dailysale-report.css">
<link rel="stylesheet" href="/bear/lib/jquery/js/datepicker/jq-datepicker.css">
<link rel="stylesheet" href="/bear/lib/datatable-bootstrap5/dataTables.bootstrap5.min.css">
<?= templateBody; ?>
<div class="content-wrapper p-4" id="app">
    <div class="content-header pb-1">
        <h4>รายงาน</h4>
        <hr>
    </div>
    <div class="content-body">
        <div class="container-fluid content-custom" style="position: relative;">
            <form class="load-daily-report-form pb-4" id="addNewMaterialForm" method="POST" enctype="multipart/form-data" @submit.prevent="getReportDailySale()">
                <div class="row form-step px-2 pb-3 overflow-y-custom" id="step1">
                    <h5 class="mb-2">รายงานยอดขาย</h5>
                    <div class="group-search pt-lg-2 ps-lg-3 mb-2">
                        <h6>เลือกรายการค้นหา</h6>
                        <div class="d-flex">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="searchCustomDate" id="searchCustomDate" :value="0" v-model="optionSearch" @change="[setOptionSearch(), reportDataShowInTable = []]">
                                <label class="form-check-label" for="inlineRadio1">ระบุวันที่</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="searchToday" id="searchToday" :value="1" v-model="optionSearch" @change="[labelSearchOption = 'เลือกรายการ', setOptionSearch(), reportDataShowInTable = []]">
                                <label class="form-check-label" for="inlineRadio2">ยอดขายประจำวัน</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="searchLastWeek" id="searchLastWeek" :value="2" v-model="optionSearch" @change="[labelSearchOption = 'เลือกรายการ', setOptionSearch(), reportDataShowInTable = []]">
                                <label class="form-check-label" for="inlineRadio2">ยอดขายย้อนหลัง 7 วัน</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="searchMonth" id="searchMonth" :value="3" v-model="optionSearch" @change="[labelSearchOption = 'เลือกรายการ', setOptionSearch(), reportDataShowInTable = []]">
                                <label class="form-check-label" for="inlineRadio2">ยอดขายประจำเดือน</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="searchYear" id="searchYear" :value="4" v-model="optionSearch" @change="[labelSearchOption = 'เลือกรายการ', setOptionSearch(), reportDataShowInTable = []]">
                                <label class="form-check-label" for="inlineRadio2">ยอดขายประจำปี</label>
                            </div>
                        </div>
                    </div>
                    <div class="row option-search-custom" v-if="optionSearch == 0">
                        <div class="col-6 col-lg-4">
                            <div class="form-group">
                                <label for="">วันที่เริ่มต้น</label>
                                <input type="text" class="form-control" id="startDate" name="startDate" placeholder="<?= date("Y-m-d") ?>" readonly v-model="startDate" @keyup="[doNotType(startDate, 'startDate')]">
                            </div>
                        </div>
                        <div class="col-6 col-lg-4">
                            <div class="form-group">
                                <label for="">ถึงวันที่</label>
                                <input type="text" class="form-control" id="endDate" name="endDate" placeholder="<?= date("Y-m-d") ?>" readonly v-model="endDate" @keyup="[doNotType(endDate, 'endDate')]">
                            </div>
                        </div>
                        <div class="col-6 col-lg-4 d-flex align-items-center mt-lg-3">
                            <button class="btn btn-primary">ค้นหา</button>
                        </div>
                    </div>
                    <div class="row option-search-custom" v-if="optionSearch == 1 || optionSearch == 2 || optionSearch == 3 || optionSearch == 4">
                        <div class="col-6 col-lg-4">
                            <div class="form-group">
                                <label for="">{{ labelSearchOption }}</label>
                                <select class="form-control" v-model="selectDate">
                                    <option v-for="(item, index) in dataDynamicSearch" :value="item.value">{{ item.text }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-6 col-lg-4 d-flex align-items-center mt-lg-3">
                            <button class="btn btn-primary">ค้นหา</button>
                        </div>
                    </div>

                    <div v-if="reportDataShowInTable.length > 0">
                        <table id="reportTable" class="table table-striped" style="width:100%">
                            <thead>
                                <tr>
                                    <th v-if="parseInt(optionSearch) != 0 && parseInt(optionSearch) != 1" v-for="(value, index) in dailyReportTable.tableHead1">{{ value }}</th>
                                    <th v-if="parseInt(optionSearch) == 0 || parseInt(optionSearch) == 1" v-for="(value, index) in dailyReportTable.tableHead">{{ value }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="index != reportDataShowInTable.length - 1 && (parseInt(optionSearch) != 0 && parseInt(optionSearch) != 1)" v-for="(value, index) in reportDataShowInTable">
                                    <td>{{ index + 1 }}</td>
                                    <td>{{ value.created_at }}</td>
                                    <td>{{ value.daily_order_count }}</td>
                                    <td>{{ value.daily_sum_sale }}</td>
                                    <td>{{ value.daily_discount }}</td>
                                    <td>{{ value.daily_total_sale }}</td>
                                </tr>

                                <tr v-if="index != reportDataShowInTable.length - 1 && (parseInt(optionSearch) == 0 || parseInt(optionSearch) == 1)" v-for="(value, index) in reportDataShowInTable">
                                    <td>{{ index + 1 }}</td>
                                    <td>{{ value.bill_sub_id }}</td>
                                    <td>{{ value.created_at }}</td>
                                    <td>{{ value.bill_order_count_all }}</td>
                                    <td>{{ value.bill_sum }}</td>
                                    <td>{{ value.bill_discount }}</td>
                                    <td>{{ value.bill_total_sum }}</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th v-if="parseInt(optionSearch) != 0 && parseInt(optionSearch) != 1" v-for="(value, index) in dailyReportTable.tableHead1">{{ value }}</th>
                                    <th v-if="parseInt(optionSearch) == 0 || parseInt(optionSearch) == 1" v-for="(value, index) in dailyReportTable.tableHead">{{ value }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="chart-container mt-2" v-if="chartJsData.length > 0">
                        <div class="chart">
                            <ul class="list-group list-group-horizontal-sm chart-type-list">
                                <li class="list-group-item" @click="renderChart(chartJsData, 'bar')">bar</li>
                                <li class="list-group-item" @click="renderChart(chartJsData, 'pie')">pie</li>
                            </ul>
                            <canvas id="myChart" width="100%" height="100%"></canvas>
                        </div>
                    </div>
                </div>
            </form>

        </div><!-- /.container-fluid -->
    </div>
</div>

<!-- <?= templateFootter ?> -->
<script src="/bear/js/customValidation.js"></script>
<!-- datepicker thai -->
<script src="/bear/lib/jquery/js/datepicker/jq-datepicker.js"></script>
<script src="/bear/lib/jquery/js/datepicker/jq-datepicker.th-TH.js"></script>
<!-- datatable -->
<script src="/bear/lib/datatable-bootstrap5/dataTables.bootstrap5.min.js"></script>
<script src="/bear/lib/datatable-bootstrap5/jquery.dataTables.min.js"></script>
<!-- chartjs -->
<script src="/bear/lib/chart.js/Chart.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/vue@2"></script>
<script>
    const app = new Vue({
        el: '#app',
        mounted: function() {
            this.loadDateOpenBranch();
            this.currentDate = this.formatDate();
            $('#startDate').datepicker(this.datepickerConfig);
            $('#endDate').datepicker(this.datepickerConfig);
        },
        data: {
            axiosConfig: {
                method: 'get',
                url: "",
                headers: {
                    'Accept': 'application/json',
                    // 'Authorization': 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiI5IiwianRpIjoiYTJiMzdhNGViZjU1NDRmMDliZDY2ZTk0NWJhMmQxNGE1Nzg3YTk2ZGMyYmMzNGMyNjk2OTgxMzc1OWFkOGYxYjk5MGUwNjJiNTgzYzRhM2YiLCJpYXQiOjE2MjAyNjkwMjIsIm5iZiI6MTYyMDI2OTAyMiwiZXhwIjoxNjUxODA1MDIyLCJzdWIiOiIxIiwic2NvcGVzIjpbXX0.do6kFU1NYBpP1CctpkOe0CK-ZRmQSttF6pK_mnX01Bgv2trq1aHmBKw26YDnWEYR-v3IxqzwTZ32ab5O1PGWRttH20hGUZi4MXQITJ7a1I_JJVnt5WLLR9TCy9yr9FkkvFtpFjyyjEwq2i9crIzWp1VEpIPj563kIUzRlqc7bth0whoXHPKDuxbW0h7K_Vuh9mSc79npKdR_jQdlGLAig9QSVPHGZizZfxsuzy5Cwz6wTpbQMWHm-7JEZIaQt-snW19NJ65fEM7MJU7sgxpN-cUnC13o9xtV7Nj8sq7PYIJo9XnTCg3w541ClAYFlQy1ZGTfurVAoD8_DAUX73fUYN30Jyb9jf-TlTaD4zfjnF784QX0Gpe0HY8aN2ApXVdsaSTk_vq-SPLP9hWgs1OMKxX7YrsBlq6adWtnqUBQ9sTnllskqn9mF3-U06F6VBxsjHJZv3gWD0ot2ivGhZWzs8BvTd5d62YCLaQZQsxgHbJbLFBO2YTWsHnP9TnAD6C23PZa0hmTfvsYXrqePAi2Wa7-UPOtF9wYSNzhhFqbLaQ2-MWjMDAmDfX8LQM4w1ZNBmcxS5pZG3sqdIMmU4KJ8WwImXlYbWv5P2JQXjA6sgFCDciARi0NP1Y_M4pWsK_ipkpESIxz9RRb3zabEDKtWgjgp_8DPi0swZ7VR56bIiM',
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                withCredentials: true,
                data: [],
            },
            user: {
                companyId: "<?= $_SESSION["usersLogin"]["companyId"] ?>",
                branchBy: "<?= $_SESSION["usersLogin"]["branchId"] ?>", //ต้องทำ login แยก ระหว่างคนที่มีสิทธิ์ดูได้ทุกสาขากับคนที่ดูได้เฉพาะสาขา
                loginBy: "<?= $_SESSION["usersLogin"]["companyUserId"] ?>",
                positionId: "<?= $_SESSION["usersLogin"]["positionId"] ?>",
            },
            dataJsonString: [],
            urlSend: "",
            optionSearch: "0",
            labelSearchOption: "",
            branchDateOpen: "",
            dataDynamicSearch: [],
            currentDate: "",
            startDate: "",
            endDate: "",
            selectDate: "",
            startDateBackup: "",
            endDateBackup: "",
            dailyReportTable: {
                tableHead1: {
                    no: "#",
                    date: "วันที",
                    salesCount: "จำนวนรายการขาย",
                    sales: "ยอดขาย",
                    discount: "ส่วนลด",
                    totalSales: "สุทธิ",
                },
                tableHead: {
                    no: "#",
                    billSubId: "เลขที่ใบเสร็จ",
                    date: "วันที",
                    salesCount: "จำนวนรายการขาย",
                    sales: "ยอดขาย",
                    discount: "ส่วนลด",
                    totalSales: "สุทธิ",
                },
            },
            reportDataShowInTable: [],
            dataTableOption: {
                "lengthMenu": [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "All"]
                ]
            },
            datepickerConfig: {
                language: 'th-TH',
                format: 'yyyy-mm-dd'
            },
            chartJsData: [],
            checkRenderChart: null,
            chartJsConfig: {
                type: 'pie',
                data: {
                    labels: ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
                    datasets: [{
                        label: '',
                        data: [12, 19, 3, 5, 2, 3],
                        backgroundColor: [],
                        borderColor: [],
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        // y: {
                        //     beginAtZero: true
                        // }
                    },
                    plugins: {
                        title: {
                            display: true,
                            text: ''
                        },
                        legend: {
                            display: false //จะปิดหัวข้อที่เป็นแถบสีๆ
                        },
                        // tooltips: {
                        //     mode: 'label',
                        //     callbacks: {
                        // label: function(tooltipItem, data) {
                        //     return data.labels[tooltipItem.index] + ': ' + data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index] + '€';
                        // }
                        //     }
                        // },

                        tooltip: {
                            usePointStyle: true,
                            callbacks: {
                                labelPointStyle: function(context) {
                                    return {
                                        pointStyle: 'triangle',
                                        rotation: 0
                                    };
                                },
                                label: function(context) {
                                    var label = context.dataset.label || '';

                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed.y !== null) {
                                        label += new Intl.NumberFormat().format(context.parsed.y) + " บาท";
                                    }
                                    return label;
                                }
                            }
                        },
                    },
                }
            }
        },
        computed: { // สามารถนำไปใช้ได้ทุก function ประกาศตัวแปรลอย
        },
        methods: {
            setOptionSearch() {
                // 1=รายวัน 2=ย้อนหลัง7วัน 3=รายเดือน 4=รายปี
                this.dataDynamicSearch = [];
                this.selectDate = "";
                this.startDate = "";
                this.endDate = "";
                this.chartJsData = []; // ให้ chart เป็นค่าว่างเวลาเปลี่ยนข้อมูล
                switch (this.optionSearch) {
                    case 0:
                        setTimeout(() => { // เหมือนถ้าไม่หน่วงมันแสดงผลไม่ทัน งง
                            $('#startDate').datepicker(this.datepickerConfig);
                            $('#endDate').datepicker(this.datepickerConfig);
                        }, 100);
                        break;
                    case 1:
                    case 2:
                        this.dataDynamicSearch = this.setDateBetween(this.branchDateOpen, this.currentDate);
                        break;
                    case 3:
                        this.dataDynamicSearch = this.setMonth();
                        break;
                    case 4:
                        let branchDateOpenExplode = this.branchDateOpen.split("-");
                        let currentDate = this.currentDate.split("-");
                        let startYear = branchDateOpenExplode[0];
                        let endYear = branchDateOpenExplode[0];
                        this.dataDynamicSearch = this.setYear(startYear, endYear);
                        break;
                }
            },
            setYear(startYear, endYear) {
                let dataReturn = [];
                for (let i = startYear; i <= endYear; i++) {
                    dataReturn.push({
                        value: i + "-01-01",
                        text: i,
                    });
                }
                return dataReturn;
            },
            setMonth() {
                let dataReturn = [];
                for (let i = 1; i <= 12; i++) {
                    let monthFormat = (i.toString().length == 1 ? "0" + i.toString() : i.toString());
                    dataReturn.push({
                        value: '<?= date("Y") ?>-' + monthFormat + '-01',
                        text: i,
                    });
                }
                return dataReturn;
            },
            setDateBetween(dateStart, dateEnd) { // yyyy-mm-dd
                let month30 = ["4", "6", "9", "11"];
                let month31 = ["1", "3", "5", "7", "8", "10", "12"];
                let month28 = ["2"];
                let branchDateOpenExplode = dateStart.split("-");
                let branchDateOpenDay = parseInt(branchDateOpenExplode[2]);
                let branchDateOpenMonth = parseInt(branchDateOpenExplode[1]);
                let branchDateOpenYear = parseInt(branchDateOpenExplode[0]);
                const dateOpen = new Date(dateStart);
                const dateCurrent = new Date(dateEnd);
                const diffTime = Math.abs(dateCurrent - dateOpen);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                let dataReturn = [];
                for (let i = 0; i < diffDays; i++) {
                    // เพิ่มวันแรกเข้าไปด้วย
                    if (i == 0) {
                        dataReturn.push({
                            value: branchDateOpenExplode[0] + "-" + branchDateOpenExplode[1] + "-" + branchDateOpenExplode[2],
                            text: branchDateOpenExplode[2] + "-" + branchDateOpenExplode[1] + "-" + branchDateOpenExplode[0],
                        });
                    }
                    let checkDay = false;
                    let checkMonth = false;
                    branchDateOpenDay++;
                    if (month28.indexOf(branchDateOpenMonth.toString()) >= 0 && branchDateOpenDay == 30 && (branchDateOpenYear % 4) == 0) {
                        checkDay = true;
                    } else if (month30.indexOf(branchDateOpenMonth.toString()) >= 0 && branchDateOpenDay == 31) {
                        checkDay = true;

                    } else if (month31.indexOf(branchDateOpenMonth.toString()) >= 0 && branchDateOpenDay == 32) {
                        checkDay = true;

                    } else if (month28.indexOf(branchDateOpenMonth.toString()) >= 0 && branchDateOpenDay == 29 && (branchDateOpenYear % 4) != 0) {
                        checkDay = true;
                    }

                    if (checkDay == true) {
                        branchDateOpenDay = 1;
                        branchDateOpenMonth++;
                    }
                    if (branchDateOpenMonth > 12) {
                        checkMonth = true;
                    }
                    if (checkMonth == true) {
                        branchDateOpenMonth = 1;
                        branchDateOpenYear++;
                    }
                    let dayFormat = (branchDateOpenDay.toString().length == 1 ? '0' + branchDateOpenDay.toString() : branchDateOpenDay.toString());
                    let monthFormat = (branchDateOpenMonth.toString().length == 1 ? '0' + branchDateOpenMonth.toString() : branchDateOpenMonth.toString());
                    let yearFormat = (branchDateOpenYear.toString().length == 1 ? '0' + branchDateOpenYear.toString() : branchDateOpenYear.toString());
                    dataReturn.push({
                        value: yearFormat + "-" + monthFormat + "-" + dayFormat,
                        text: dayFormat + "-" + monthFormat + "-" + yearFormat,
                    });
                }
                return dataReturn;
            },
            getReportDailySale() {
                if (!this.setDataForSend()) return; // เตรียมข้อมูลสำหรับส่ง
                this.reportDataShowInTable = [];
                this.chartJsData = [];
                let formData = new FormData();
                const dataSendToBackend = JSON.stringify(this.dataJsonString);
                formData.append('jsonData', dataSendToBackend);
                this.axiosConfig.url = `<?= urlTobackend ?>loadReportDailySale.php`;
                this.axiosConfig.method = "POST";
                this.axiosConfig.data = formData;
                axios.defaults.withCredentials = true;
                axios(this.axiosConfig).then((response) => {
                    if (response.data.status) {
                        let responseArray = Object.values(response.data.results);
                        this.reportDataShowInTable = responseArray;
                        this.chartJsData = responseArray;
                        setTimeout(() => { // ถ้าไม่หน่วงมันไม่ทำงาน
                            $('#reportTable').DataTable(this.dataTableOption);
                            this.renderChart(this.chartJsData, "bar");
                        }, 100);
                    } else {
                        throw response.data;
                    }
                }).catch((e) => {
                    console.log(e);
                });
            },
            setDataForSend() {
                // 0=betweenDate 1=รายวัน 2=ย้อนหลัง7วัน 3=รายเดือน 4=รายปี
                if (parseInt(this.optionSearch) == 0) {
                    this.startDate = document.getElementById("startDate").value;
                    this.endDate = document.getElementById("endDate").value;
                }

                this.dataJsonString = [];
                this.dataJsonString.push({
                    startDate: this.startDate,
                    endDate: this.endDate,
                    selectDate: this.selectDate,
                    companyId: btoa(this.user.companyId),
                    branchId: btoa(this.user.branchBy),
                    typeReport: this.optionSearch,
                });
                const dataValidate = this.validate(this.dataJsonString[0]);
                let dataReturn = true;
                if (dataValidate.length > 0) {
                    dataReturn = false
                }
                return dataReturn;
            },
            validate(JSONData) {
                const classCustomValidation = new CustomValidation();
                let optionCheckEmpty = [
                    "startDate",
                    "endDate",
                    "selectDate",
                ];
                const optionCheckNumericOnly = [
                    "startDate",
                    "endDate",
                    "selectDate",
                    "companyId",
                    "branchId",
                ];
                const optionCheckStringOnly = [
                    "typeReport",
                ];
                if (parseInt(JSONData.typeReport) == 0) {
                    optionCheckEmpty.splice(0, 2); // ลบตัวที่ไม่ต้องการเช็คออก
                } else {
                    optionCheckEmpty.splice(2, 1); // ลบตัวที่ไม่ต้องการเช็คออก
                }
                const setData = JSONData;
                const checkEmpty = classCustomValidation.checkEmpty(setData, optionCheckEmpty);
                const checkSpecialCharacter = classCustomValidation.checkSpecialCharacter(setData, [], /[ `!#$%^&*()+\[\]{};':"\\|,<>\/?~]/);
                const checkNumeric = classCustomValidation.checkNumeric(setData, optionCheckNumericOnly);
                const checkString = classCustomValidation.checkString(setData, optionCheckStringOnly);
                const setValidationErrorData = classCustomValidation.setValidationErrorData(checkEmpty, checkSpecialCharacter, checkNumeric, checkString);
                return setValidationErrorData;
            },
            loadDateOpenBranch() {
                this.axiosConfig.url = `<?= urlTobackend ?>loadBranch.php?companyId=${btoa(this.user.companyId)}`;
                axios.defaults.withCredentials = true;
                axios(this.axiosConfig).then((response) => {
                    if (response.data.status) {
                        let branchCreateExplode = response.data.results[0].branch_created_at.split(" ");
                        this.branchDateOpen = branchCreateExplode[0];
                    } else {
                        throw response.data;
                    }
                }).catch((e) => {
                    console.log(e);
                });
            },
            loadDataFromBackend: function() {

            },
            formatDate() {
                var d = new Date(),
                    month = '' + (d.getMonth() + 1),
                    day = '' + d.getDate(),
                    year = d.getFullYear();

                if (month.length < 2)
                    month = '0' + month;
                if (day.length < 2)
                    day = '0' + day;

                return [year, month, day].join('-');
            },
            doNotType(dateValue, checkInput) {
                if (this.isValidDate(dateValue) && checkInput == "startDate") {
                    this.startDateBackup = this.startDate;
                }
                if (this.isValidDate(dateValue) && checkInput == "endDate") {
                    this.endDateBackup = this.endDate;
                }
            },
            isValidDate(dateString) {
                var regEx = /^\d{4}-\d{2}-\d{2}$/;
                if (!dateString.match(regEx)) return false; // Invalid format
                var d = new Date(dateString);
                var dNum = d.getTime();
                if (!dNum && dNum !== 0) return false; // NaN value, Invalid date
                return true;
            },
            clearDatatable() { // เก็บไว้ดูเผื่อได้ใช้
                if ($.fn.DataTable.isDataTable('#reportTable')) { // clear datatable
                    $('#reportTable').DataTable().clear();
                }
                $('#reportTable tbody').empty();
            },
            async renderChart(data, gaphType) {
                await this.setChartData(data, gaphType);
                let ctx = await document.getElementById('myChart').getContext('2d');
                if (this.checkRenderChart != null) {
                    this.checkRenderChart.destroy();
                }
                this.checkRenderChart = await new Chart(ctx, this.chartJsConfig);

            },
            randomColor(dataLength) {
                let backgroundArr = [];
                let borderArr = [];
                for (let i = 0; i < dataLength; i++) {
                    let r = Math.floor(Math.random() * 256);
                    let g = Math.floor(Math.random() * 256);
                    let b = Math.floor(Math.random() * 256);
                    backgroundArr.push(`rgba(${r}, ${g}, ${b}, 0.4)`)
                    borderArr.push(`rgba(${r}, ${g}, ${b}, 1)`)
                }
                return {
                    backgroundColor: backgroundArr,
                    borderColor: borderArr,
                }
            },
            async setChartData(data, gaphType) {
                let setColor = await this.randomColor(data.length);
                let convertKey = "";
                let convertvalue = "";
                switch (parseInt(this.optionSearch)) {
                    case 0:
                    case 1:
                        convertKey = "bill_sub_id";
                        convertvalue = "bill_total_sum";
                        break;
                    case 2:
                    case 3:
                    case 4:
                        convertKey = "created_at";
                        convertvalue = "daily_total_sale";
                        break;
                }
                let labelAndValueChart = await this.convertDataForChart(data, convertKey, convertvalue);
                this.chartJsConfig.type = gaphType;
                // this.chartJsConfig.data.datasets[0].label = "ยอดสุทธิ";
                this.chartJsConfig.options.plugins.title.text = "ข้อมูลยอดขาย";
                this.chartJsConfig.data.datasets[0].backgroundColor = setColor.backgroundColor;
                this.chartJsConfig.data.datasets[0].borderColor = setColor.borderColor;
                this.chartJsConfig.data.labels = labelAndValueChart.labelData;
                this.chartJsConfig.data.datasets[0].data = labelAndValueChart.valueData;
                //         chartJsConfig: {
                //         type: 'bar',
                //         data: {
                //             labels: ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
                //             datasets: [{
                //                 label: '# of Votes',
                //                 data: [12, 19, 3, 5, 2, 3],
                //                 backgroundColor: [
                //                     'rgba(255, 99, 132, 0.2)',
                //                     'rgba(54, 162, 235, 0.2)',
                //                     'rgba(255, 206, 86, 0.2)',
                //                     'rgba(75, 192, 192, 0.2)',
                //                     'rgba(153, 102, 255, 0.2)',
                //                     'rgba(255, 159, 64, 0.2)'
                //                 ],
                //                 borderColor: [
                //                     'rgba(255, 99, 132, 1)',
                //                     'rgba(54, 162, 235, 1)',
                //                     'rgba(255, 206, 86, 1)',
                //                     'rgba(75, 192, 192, 1)',
                //                     'rgba(153, 102, 255, 1)',
                //                     'rgba(255, 159, 64, 1)'
                //                 ],
                //                 borderWidth: 1
                //             }]
                //         },
                //         options: {
                //             scales: {
                //                 y: {
                //                     beginAtZero: true
                //                 }
                //             },
                // plugins: {
                //         title: {
                //             display: true,
                //             text: ''
                //         }
                //     }
                //         }
                //     }
                // }
            },
            convertDataForChart(data, key, value) {
                let labelData = [];
                let valueData = [];
                for (var index in data) {
                    if (typeof data[index] == "object") {
                        for (var subIndex in data[index]) {
                            if (data[index].hasOwnProperty(subIndex)) {
                                if (subIndex == key) {
                                    labelData.push(data[index][subIndex]);
                                }
                                if (subIndex == value) {
                                    valueData.push(parseInt(data[index][subIndex]));
                                }

                            }
                        }
                    }

                }
                return {
                    labelData: labelData,
                    valueData: valueData,
                }
            },
        }
    });
</script>