/*
  使用者登入頁js
  created_by：Jeff
  created_at：2021/09/21
*/
$(document).ready(function($) {

  // loginAccount button 登入帳號
  $("#loginAccount").click(function(e) {
    var userAcc = document.getElementById("userAcc").value;
    var userPwd = document.getElementById("userPwd").value;
    $.ajax({
      type: "POST",
      url: "../php/login.php",
      data: {
        action: "loginAccount",
        account: userAcc,
        password: userPwd
      },
      dataType: "json"
    })
    // 伺服器端正確執行完成
    .done(function(data, msg, obj) {
      // 如果 responseCode 不是 0，代表有錯誤(錯誤包含"此帳號待審核開通")
      if (data.responseCode !== "0") {
        // 顯示 responseText (錯誤描述）
        alert(data.responseText);
      }
      // 登入順利的情況
      else {
        alert("登入成功");
      }
    })
    // 伺服器端正確執行完成發生問題
    .fail(function(obj, msg, err) {
      alert("伺服器程式錯誤");
    })
  });

  // 建立帳號的設計流程
  // 用戶按下"開啟帳號建立流程"按鈕
  // ->輸入 orgNo 與 orgTitle 並按下"確認單位編號"按鈕,後端確認是否有該單位,若查無單位編號會依據輸入的 orgTitle 建立單位
  // ->後端確認後會顯示表格用以建立帳號

  // createAccountTableDisplayStep button 開啟帳號建立流程
  $("#createAccountTableDisplayStep").click(function(e) {
    var checkOrgNoTableId = document.getElementById("checkOrgNoTable");
    // 顯示單位編號驗證表格,以區塊方式顯示
    checkOrgNoTableId.style.display = "block";
  });

  // checkOrgNo button 確認單位編號
  $("#checkOrgNo").click(function(e) {
    var orgNoId = document.getElementById("orgNo");
    var orgTitleId = document.getElementById("orgTitle");
    var createAccountTableId = document.getElementById("createAccountTable");
    var orgNo = orgNoId.value;
    var orgTitle = orgTitleId.value;
    $.ajax({
      type: "POST",
      url: "../php/login.php",
      data: {
        action: "checkOrgNo",
        org_no: orgNo,
        title: orgTitle,
      },
      dataType: "json"
    })
    // 伺服器端正確執行完成
    .done(function(data, msg, obj) {
      // 如果 responseCode 不是 0，代表有錯誤
      if (data.responseCode !== "0") {
        // 顯示 responseText (錯誤描述）
        alert(data.responseText);
      }
      else {
        // 確認該單位編號可使用,將單位編號輸入欄禁用避免使用者再度修改
        orgNoId.disabled = true;
        // 顯示帳號建立表格,以區塊方式顯示
        createAccountTableId.style.display = "block";
      }
    })
    // 伺服器端正確執行完成發生問題
    .fail(function(obj, msg, err) {
      alert("伺服器程式錯誤");
    })
  });

  // createAccount button 建立帳號
  $("#createAccount").click(function(e) {
    var orgNo = document.getElementById("orgNo").value;
    var userName = document.getElementById("userName").value;
    var birthday = document.getElementById("birthday").value;
    var email = document.getElementById("email").value;
    var createAcc = document.getElementById("createAcc").value;
    var createPwd = document.getElementById("createPwd").value;
    var applyQualification = $('input[name="applyQualification"]').get(0).files[0];
    var formData = new FormData();

    formData.append('action', 'createAccount');
    formData.append('org_no', orgNo);
    formData.append('name', userName);
    formData.append('birthday', birthday);
    formData.append('email', email);
    formData.append('account', createAcc);
    formData.append('password', createPwd);
    formData.append('applyQualification', applyQualification);

    $.ajax({
      type: "POST",
      url: "../php/login.php",
      data: formData,
      enctype: "multipart/form-data; charset=UTF-8",
      contentType: false,
      processData: false,
      cache: false
    })

    // 伺服器端正確執行完成
    .done(function(data, msg, obj) {
      data = JSON.parse(data);
      // 如果 responseCode 不是 0，代表有錯誤
      if (data.responseCode !== "0") {
        // 顯示 responseText (錯誤描述）
        alert(data.responseText);
      }
      // 成功建立的情況
      else {
        // 將表格重新變為隱藏狀態,並將單位編號輸入欄改為可用
        var orgNoId = document.getElementById("orgNo");
        var checkOrgNoTableId = document.getElementById("checkOrgNoTable");
        var createAccountTableId = document.getElementById("createAccountTable");
        orgNoId.disabled = false;
        checkOrgNoTableId.style.display = "none";
        createAccountTableId.style.display = "none";
        alert("建立帳號成功");
      }
    })
    // 伺服器端正確執行完成發生問題
    .fail(function(obj, msg, err) {
      console.log(msg);
      alert("伺服器程式錯誤");
    })
  });
  

});
