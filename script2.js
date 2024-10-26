function showNameFields() {
  const tableCount = parseInt(document.getElementById("table").value);
  const tablePrefix =
    document.getElementById("tablePrefix").value.toUpperCase() || "A";
  const nameFieldsContainer = document.getElementById("name-fields");

  console.log("จำนวนโต๊ะ:", tableCount); // ตรวจสอบค่าจำนวนโต๊ะ
  console.log("ตัวอักษรสำหรับชื่อโต๊ะ:", tablePrefix); // ตรวจสอบค่าตัวอักษร

  nameFieldsContainer.innerHTML = ""; // ล้างข้อมูลเก่า

  for (let i = 1; i <= tableCount; i++) {
    const label = document.createElement("label");
    label.textContent = `ชื่อโต๊ะ ${tablePrefix}${i}`;
    label.setAttribute("for", `name_table_${i}`);

    const input = document.createElement("input");
    input.type = "text";
    input.name = `table_name_${i}`;
    input.id = `name_table_${i}`;
    input.value = `${tablePrefix}${i}`; // กำหนดชื่อโต๊ะเป็นค่าเริ่มต้น เช่น A1, A2

    const div = document.createElement("div");
    div.className = "form-group";
    div.appendChild(label);
    div.appendChild(input);

    nameFieldsContainer.appendChild(div);
  }
}
