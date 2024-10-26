function validateForm() {
  var phone = document.getElementById("phone_account").value;

  // ตรวจสอบว่าหมายเลขโทรศัพท์มี 10 หลัก
  if (phone.length !== 10 || isNaN(phone)) {
    alert("กรุณากรอกหมายเลขโทรศัพท์ให้ถูกต้อง โดยต้องมี 10 ตัวเลข");
    return false;
  }
  return true;
}
