class myLibary {
  getCurrentDate(todays = 0, time = "") {
    if (todays == 0) {
      todays = todays + 1;
    } else {
      todays = todays * 24;
    }

    let today = new Date();
    let tomorrow = new Date(today.getTime() + todays * 60 * 60 * 1000);
    let dd = "";
    let mm = "";
    let yyyy = "";
    let H = today.getHours();
    let I = today.getMinutes();
    let S = today.getSeconds();
    if (todays != 1) {
      dd = tomorrow.getDate();
      mm = tomorrow.getMonth() + 1;
      yyyy = tomorrow.getFullYear();
    } else {
      dd = today.getDate();
      mm = today.getMonth() + 1;
      yyyy = today.getFullYear();

      H = today.getHours();
      I = today.getMinutes();
      S = today.getSeconds();
    }

    if (dd < 10) {
      dd = "0" + dd;
    }

    if (mm < 10) {
      mm = "0" + mm;
    }
    if (time == "t") {
      today = yyyy + "-" + mm + "-" + dd + " :" + H + ":" + I + ":" + S;
    } else {
      today = yyyy + "-" + mm + "-" + dd;
    }

    return today;
  }
  dateDiff(date1, date2, returnType = "") {
    let returnData = "";
    const myDate1 = new Date(date1);
    const myDate2 = new Date(date2);
    const diffTime = Math.abs(myDate2 - myDate1);
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    if (returnType == "day") {
      returnData = diffDays;
    } else {
      returnData = diffDays + " " + diffTime;
    }
    return returnData;
  }
}
