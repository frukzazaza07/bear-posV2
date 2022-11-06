class CustomValidation {
  checkEmpty(data, dataOption = [], message = " is not empty!") {
    //   {name:"sad",detail:""} รูปแบบที่ส่งเข้ามา
    let returnData = [];
    for (let index in data) {
      if (typeof data[index] === "object") {
        for (let subIndex in data[index]) {
          if (
            !this.checkEmptyLogic(data[index][subIndex]) &&
            dataOption.indexOf(subIndex) < 0
          ) {
            returnData.push(
              // "#" +
              //   (parseInt(index) + 1) +
              //   " " +
              //   subIndex +
              //   " " +
              //   data[index][subIndex] +
              //   message
              `# ${parseInt(index) + 1} ${subIndex} '${data[index][subIndex]}' ${message}`
            );
          }
        }
      } else {
        if (
          !this.checkEmptyLogic(data[index]) &&
          dataOption.indexOf(index) < 0
        ) {
          // returnData.push(index + message);
          returnData.push(`${index} '${data[index]}' ${message}`);
        }
      }
    }
    return returnData;
  }
  checkEmptyLogic(empData) {
    if (empData === "") {
      return false;
    }
    return true;
  }
  checkNumeric(data, dataOption = [], message = " need type number only!") {
    //   {name:"sad",detail:""} รูปแบบที่ส่งเข้ามา
    let returnData = [];
    const format = /^[0-9.]+$/;
    for (let index in data) {
      if (typeof data[index] === "object") {
        for (let subIndex in data[index]) {
          if (
            !this.checkNumericLogic(data[index][subIndex]) &&
            dataOption.indexOf(subIndex) < 0 &&
            data[index][subIndex] != ""
          ) {
            returnData.push(
              `# ${parseInt(index) + 1} ${subIndex} "${data[index][subIndex]}" ${message}`
            );
          }
        }
      } else {
        if (
          !this.checkNumericLogic(data[index]) &&
          dataOption.indexOf(index) < 0 &&
          data[index] != ""
        ) {
          // returnData.push(index + " " + data[index] + message);
          returnData.push(`${index} '${data[index]}' ${message}`);
        }
      }
    }

    return returnData;
  }
  checkNumericLogic(numberData) {
    // false = ไม่ใช่ตัวเลข
    const format = /^[0-9.]+$/;
    const checkNumber = format.test(numberData);
    // check ก่อนว่าเป็นตัวเลขไหมถ้าจริงไปดัก ตัวอย่าง 10.
    if (!checkNumber) {
      // returnData.push(index + message);
      return false;
    } else {
      const number = numberData.toString();
      const myArr = number.split(".");
      const firstIndex = myArr[0];
      const lastIndex = myArr[myArr.length - 1];
      if (
        firstIndex == "" ||
        (lastIndex == "" && dataOption.indexOf(index) < 0)
      ) {
        // returnData.push(index + message);
        return false;
      }
    }
    return true;
  }
  checkSpecialCharacter(
    data,
    dataOption = [],
    formatCheck = /[ `!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?~]/,
    message = " can't use special character!"
  ) {
    //   /[ `!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?~]/
    let returnData = [];
    const format = formatCheck;
    for (let index in data) {
      if (typeof data[index] === "object") {
        for (let subIndex in data[index]) {
          if (
            !this.checkSpecialCharacterLogic(data[index][subIndex], format) &&
            dataOption.indexOf(subIndex) < 0
          ) {
            returnData.push(
              // "#" +
              //   (parseInt(index) + 1) +
              //   " " +
              //   subIndex +
              //   " " +
              //   data[index][subIndex] +
              //   message
              `# ${parseInt(index) + 1} ${subIndex} '${data[index][subIndex]}' ${message}`
            );
          }
        }
      } else {
        if (
          !this.checkSpecialCharacterLogic(data[index], format) &&
          dataOption.indexOf(index) < 0
        ) {
          // returnData.push(index + " " + data[index] + message);
          returnData.push(`${index} '${data[index]}' ${message}`);
        }
      }
    }

    // for (let index in data) {
    // backup
    // let checkFormat = format.test(data[index]);
    // if (checkFormat && dataOption.indexOf(index) < 0) {
    //   // returnData.push(index + message);
    // }
    // }
    return returnData;
  }
  checkSpecialCharacterLogic(specialCharacter, format) {
    let checkFormat = format.test(specialCharacter);
    if (checkFormat) {
      return false;
    }
    return true;
  }
  checkString(data, dataOption = [], message = " need type string only!") {
    //   {name:"sad",detail:""} รูปแบบที่ส่งเข้ามา
    let returnData = [];
    for (let index in data) {
      if (typeof data[index] === "object") {
        for (let subIndex in data[index]) {
          if (
            !this.checkStringLogic(data[index][subIndex]) &&
            dataOption.indexOf(subIndex) < 0
          ) {
            returnData.push(
              // "#" +
              //   (parseInt(index) + 1) +
              //   " " +
              //   subIndex +
              //   " " +
              //   data[index][subIndex] +
              //   message
              `# ${parseInt(index) + 1} ${subIndex} '${data[index][subIndex]}' ${message}`
            );
          }
        }
      } else {
        if (
          !this.checkStringLogic(data[index]) &&
          dataOption.indexOf(index) < 0
        ) {
          // returnData.push(index + " " + data[index] + message);
          returnData.push(`${index} '${data[index]}' ${message}`);
        }
      }

      // backup
      // if (typeof data[index] !== "string" && dataOption.indexOf(index) < 0) {
      //   // returnData.push(index + message);
      //   returnData.push(data[index] + message);
      // }
    }

    return returnData;
  }
  checkStringLogic(stringText) {
    if (typeof stringText !== "string") {
      return false;
    }
    return true;
  }

  setValidationErrorData() {
    let returnData = [];
    for (let i = 0; i < arguments.length; ++i) {
      for (let index in arguments[i]) {
        returnData.push(arguments[i][index]);
      }
    }
    return returnData;
  }
  checkFile(file, maxSize = 0, allowFileType = []) {
    let returnData = [];
    if (file == "") {
      returnData.push("Picture can't empty.");
      return false;
    }
    const checkFileTypeArray = file.name.split(".");
    if (
      allowFileType.indexOf(
        checkFileTypeArray[checkFileTypeArray.length - 1]
      ) <= 0
    ) {
      returnData.push("Picture type fail. Please upload type 'jpeg', 'png'!");
    }
    if (file.size > maxSize) {
      returnData.push("Picture type fail. Please upload size less 4mb.");
    }
    return returnData;
  }
}
