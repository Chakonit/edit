document.addEventListener("DOMContentLoaded", function () {
  let productCount = 0;

  // ฟังก์ชันสำหรับเพิ่มขนาดและราคา
  function addSizeAndPrice(button) {
    const productBox = button.closest(".product-box");
    const productIndex = productBox.dataset.productIndex;

    const sizePriceContainer = productBox.querySelector(
      ".size-price-container"
    );

    if (sizePriceContainer) {
      const sizePriceDiv = document.createElement("div");
      sizePriceDiv.classList.add("size-price-fields");

      const sizeInput = document.createElement("input");
      sizeInput.type = "text";
      sizeInput.name = `size[${productIndex}][]`;
      sizeInput.placeholder = "ขนาด";
      sizeInput.required = true;

      const priceInput = document.createElement("input");
      priceInput.type = "number";
      priceInput.name = `price[${productIndex}][]`;
      priceInput.step = "0.01";
      priceInput.placeholder = "ราคา";
      priceInput.required = true;

      sizePriceDiv.appendChild(sizeInput);
      sizePriceDiv.appendChild(priceInput);

      sizePriceContainer.appendChild(sizePriceDiv);
    }
  }

  // ฟังก์ชันสำหรับเพิ่มฟิลด์สินค้าใหม่
  function addProductField() {
    productCount++;

    const productBox = document.createElement("div");
    productBox.className = "input-box product-box";
    productBox.dataset.productIndex = productCount;

    // สร้างฟิลด์สำหรับชื่อเมนู
    const productField = document.createElement("label");
    productField.textContent = `${productCount}. อาหาร`;
    productBox.appendChild(productField);

    const inputField = document.createElement("input");
    inputField.type = "text";
    inputField.name = `menu_name[${productCount}]`;
    inputField.placeholder = "อาหาร";
    inputField.required = true;
    productBox.appendChild(inputField);

    // ฟิลด์สำหรับขนาดและราคา
    const sizePriceContainer = document.createElement("div");
    sizePriceContainer.className = "size-price-container";

    const sizePriceFields = document.createElement("div");
    sizePriceFields.className = "size-price-fields";

    const sizeInput = document.createElement("input");
    sizeInput.type = "text";
    sizeInput.name = `size[${productCount}][]`;
    sizeInput.placeholder = "ขนาด";
    sizeInput.required = true;

    const priceInput = document.createElement("input");
    priceInput.type = "number";
    priceInput.name = `price[${productCount}][]`;
    priceInput.step = "0.01";
    priceInput.placeholder = "ราคา";
    priceInput.required = true;

    sizePriceFields.appendChild(sizeInput);
    sizePriceFields.appendChild(priceInput);

    sizePriceContainer.appendChild(sizePriceFields);
    productBox.appendChild(sizePriceContainer);

    // ปุ่มเพิ่มขนาดและราคาใหม่
    const addSizePriceButton = document.createElement("button");
    addSizePriceButton.type = "button";
    addSizePriceButton.className = "btn btn-add-size-price";
    addSizePriceButton.textContent = "เพิ่มขนาดและราคา";
    addSizePriceButton.addEventListener("click", function () {
      addSizeAndPrice(addSizePriceButton);
    });
    productBox.appendChild(addSizePriceButton);

    // ฟิลด์สำหรับอัพโหลดรูปภาพ
    const imageLabel = document.createElement("label");
    imageLabel.textContent = "รูปภาพ";
    productBox.appendChild(imageLabel);

    const imageInput = document.createElement("input");
    imageInput.type = "file";
    imageInput.name = `image[${productCount}][]`;
    imageInput.accept = "image/*";
    imageInput.id = `image_${productCount}`;
    imageInput.className = "input-file";
    productBox.appendChild(imageInput);

    // ปุ่มลบเมนู
    const removeButton = document.createElement("button");
    removeButton.type = "button";
    removeButton.className = "btn btn-remove";
    removeButton.textContent = "ลบเมนู";
    removeButton.addEventListener("click", function () {
      removeProductField(removeButton);
    });
    productBox.appendChild(removeButton);

    document.getElementById("product-container").appendChild(productBox);
    updateProductLabels(); // อัพเดต label หลังเพิ่มสินค้าใหม่
  }

  // ฟังก์ชันลบสินค้า
  function removeProductField(button) {
    button.closest(".product-box").remove();
    productCount--;
    updateProductLabels(); // อัพเดต label หลังลบสินค้า
  }

  // ฟังก์ชันสำหรับอัพเดต label ของสินค้า
  function updateProductLabels() {
    const productBoxes = document.querySelectorAll(".product-box");
    productBoxes.forEach((box, index) => {
      const label = box.querySelector("label");
      label.textContent = `${index + 1}. อาหาร&สินค้าที่ขาย`;
    });
  }

  // เริ่มต้นฟังก์ชันแรก
  document
    .getElementById("add-product-button")
    .addEventListener("click", addProductField);

  document.querySelectorAll(".btn-add-size-price").forEach(function (button) {
    button.addEventListener("click", function () {
      addSizeAndPrice(button);
    });
  });
});

//ฟังก์ชันหน้าinformation
document.addEventListener("DOMContentLoaded", function () {
  const nextButton = document.getElementById("next-button");
  const form = document.getElementById("information-form");
  let isFormSaved = false;

  // ตรวจสอบและบันทึกข้อมูลเมื่อฟอร์มถูกส่ง
  form.addEventListener("submit", function (event) {
    event.preventDefault(); // ป้องกันการส่งฟอร์มปกติ
    const formData = new FormData(form);

    // ตรวจสอบว่า 'has_seating' ถูกเลือก
    const hasSeating = formData.get("has_seating");
    if (hasSeating === null) {
      alert("กรุณาเลือกว่ามีโต๊ะนั่งหรือไม่");
      return;
    }

    // ส่งข้อมูลฟอร์มด้วย AJAX
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "process-information.php", true);
    xhr.onload = function () {
      if (xhr.status === 200) {
        // ถ้าการบันทึกสำเร็จ
        isFormSaved = true;
        alert("บันทึกข้อมูลสำเร็จ");
      } else {
        alert("ไม่สามารถบันทึกข้อมูลได้");
      }
    };
    xhr.send(formData);
  });

  nextButton.addEventListener("click", function () {
    if (!isFormSaved) {
      alert("กรุณากดบันทึกข้อมูลก่อน");
      return;
    }

    // ส่งค่า shop_id และตรวจสอบค่า has_seating
    const formData = new FormData(form);
    const hasSeating = formData.get("has_seating");
    const shopId = encodeURIComponent(formData.get("shop_id"));

    if (hasSeating === "1") {
      // ถ้ามีโต๊ะนั่ง ไปที่ Table.php
      window.location.href = "Table.php?";
    } else if (hasSeating === "0") {
      // ถ้าไม่มีโต๊ะนั่ง ไปที่ Menu.php
      window.location.href = "Menu.php?";
    }
  });
});

//ฟังก์ชันสำหรับเพิ่มประเภทสินค้า&สินค้าอื่นๆ
//function enableCustomFoodType(event) {
//  const customFoodTypeInput = document.getElementById("custom-food-type");
//  if (event.target.value === "custom") {
//    customFoodTypeInput.style.display = "block";
//  } else {
//    customFoodTypeInput.style.display = "none";
//  }
//}
