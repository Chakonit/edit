console.log("script.js loaded");

// ฟังก์ชันเเฮมเบอร์เกอร์เมนู
function toggleMenu() {
  const menu = document.getElementById("nav-menu");
  menu.classList.toggle("active");
}

// ฟังก์ชันสำหรับเปลี่ยนสถานะการแสดงของตะกร้า
document.addEventListener("DOMContentLoaded", function () {
  console.log("DOM fully loaded and parsed");

  const cartIcon = document.querySelector(".cart-icon");
  const cart = document.getElementById("cart");

  const cartItems = document.getElementById("cart-items");
  console.log(cartItems); // ตรวจสอบว่าถูกต้องหรือไม่

  // ฟังก์ชันสำหรับสลับการแสดงผลของตะกร้า
  cartIcon.addEventListener("click", function () {
    cart.classList.toggle("active");
  });

  // ฟังก์ชันสำหรับปิดตะกร้า
  const cancelButton = document.getElementById("cancel-button");
  if (cancelButton) {
    cancelButton.addEventListener("click", function () {
      cart.classList.add("hidden");
    });
  }
});

document.addEventListener("DOMContentLoaded", function () {
  const shopId = new URLSearchParams(window.location.search).get("shop_id");
  console.log("Fetching menu for shop_id:", shopId); // ดูค่า shop_id
  const url = `get_menu_details.php?shop_id=${shopId}`;
  console.log("Fetching from URL:", url); // ดู URL ที่เรียกใช้

  // ดึงข้อมูลเมนูทั้งหมด (ตรวจสอบข้อมูลดิบ)
  fetch(url)
    .then((response) => response.text()) // ใช้ .text() เพื่อตรวจสอบข้อมูลดิบที่ได้
    .then((data) => {
      console.log("Raw response:", data); // ดูข้อมูลดิบที่ได้จาก PHP
      try {
        const jsonData = JSON.parse(data); // แปลงข้อมูลเป็น JSON ถ้าข้อมูลถูกต้อง
        console.log("Parsed JSON data:", jsonData);
      } catch (error) {
        console.error("Error parsing JSON:", error);
        console.log("Response was:", data); // แสดงข้อมูลที่ไม่ใช่ JSON
      }
    })
    .catch((error) => console.error("Error fetching menu:", error));
});

function showDetails(menuId, shop_id) {
  console.log(`MenuId: ${menuId}, ShopId: ${shop_id}`);

  fetch(`get_menu_details.php?menuId=${menuId}&shop_id=${shop_id}`)
    .then((response) => response.json())
    .then((data) => {
      console.log("Response data:", data);
      if (data.error) {
        alert(data.error);
        return;
      }

      // แสดงชื่อเมนู
      const menuNameElem = document.getElementById("menuName");
      if (menuNameElem) {
        menuNameElem.innerText = data.menu_name;
      } else {
        console.error("Element with id 'menuName' not found.");
      }

      // แสดงรูปภาพเมนู
      const menuImage = document.getElementById("menuImage");
      if (menuImage) {
        menuImage.src = data.image || "default-image.jpg";
      }

      // แสดงขนาดและราคาใน select เดียวกัน
      const sizeSelect = document.getElementById("sizeSelect");
      if (sizeSelect) {
        sizeSelect.innerHTML = ""; // ล้างข้อมูลก่อนหน้า

        data.sizes.forEach((size, index) => {
          const option = document.createElement("option");
          option.value = index;
          option.innerText = `${size} - ฿${data.prices[index]}`;
          sizeSelect.appendChild(option);
        });
      } else {
        console.error("Element with id 'sizeSelect' not found.");
      }

      // ตั้งค่าราคาเริ่มต้น
      const menuPriceElem = document.getElementById("menuPrice");
      if (menuPriceElem) {
        menuPriceElem.innerText = `ราคา: ฿${data.prices[0]}`;
      }

      // เพิ่ม event listener เพื่อเปลี่ยนราคาเมื่อขนาดเปลี่ยน
      sizeSelect.addEventListener("change", function () {
        const selectedIndex = sizeSelect.selectedIndex;
        const selectedPrice = data.prices[selectedIndex];
        if (menuPriceElem) {
          menuPriceElem.innerText = `ราคา: ฿${selectedPrice}`;
        }
      });

      // แสดง Modal
      document.getElementById("menuModal").style.display = "block";
    })
    .catch((error) => {
      console.error("Error:", error);
      alert("เกิดข้อผิดพลาดในการดึงข้อมูลเมนู");
    });
}

// ฟังก์ชันปิด modal
function closeModal() {
  document.getElementById("menuModal").style.display = "none";
}

// ฟังก์ชันเพิ่มรายการลงตะกร้า
function addToCart() {
  const menuName = document.getElementById("menuName").innerText;
  const sizeSelect = document.getElementById("sizeSelect");
  const selectedIndex = sizeSelect.selectedIndex;

  if (selectedIndex < 0) {
    alert("กรุณาเลือกขนาดก่อนเพิ่มลงตะกร้า");
    return;
  }

  const selectedSize = sizeSelect.options[selectedIndex].innerText;
  const selectedPrice = parseFloat(
    document.getElementById("menuPrice").innerText.replace("ราคา: ฿", "")
  );

  if (isNaN(selectedPrice)) {
    alert("เกิดข้อผิดพลาดในการดึงราคา กรุณาลองอีกครั้ง");
    return;
  }

  const cartItems = document.getElementById("cart-items");
  const cartTotal = document.getElementById("cart-total");

  // หา item ที่มีอยู่ในตะกร้าแล้ว (เช็คทั้งชื่อเมนูและขนาด)
  let existingItem = null;
  for (let i = 0; i < cartItems.children.length; i++) {
    const item = cartItems.children[i];
    if (
      item.getAttribute("data-menu-name") === menuName &&
      item.getAttribute("data-size") === selectedSize
    ) {
      existingItem = item;
      break;
    }
  }

  // ถ้ามีสินค้าซ้ำในตะกร้าแล้วให้เพิ่มจำนวน
  if (existingItem) {
    const quantityElement = existingItem.querySelector(".quantity");
    let currentQuantity = parseInt(quantityElement.textContent) || 1;
    currentQuantity += 1;
    quantityElement.textContent = currentQuantity;

    // อัพเดทราคาในตะกร้า
    const itemPriceElement = existingItem.querySelector(".item-price");
    const totalItemPrice = selectedPrice * currentQuantity;
    itemPriceElement.textContent = `฿${totalItemPrice.toFixed(2)}`;
  } else {
    // ถ้ายังไม่มีสินค้าในตะกร้า ให้เพิ่มใหม่
    const listItem = document.createElement("li");
    listItem.setAttribute("data-menu-name", menuName);
    listItem.setAttribute("data-size", selectedSize);

    listItem.innerHTML = `
      ${menuName} (${selectedSize}) - 
      ฿<span class="item-price">${selectedPrice.toFixed(2)}</span>
      <button class="quantity-decrease">-</button>
      <span class="quantity">1</span>
      <button class="quantity-increase">+</button>
    `;
    cartItems.appendChild(listItem);

    // เพิ่มฟังก์ชันสำหรับปุ่ม + และ -
    listItem
      .querySelector(".quantity-increase")
      .addEventListener("click", () =>
        changeQuantity(listItem, selectedPrice, 1)
      );
    listItem
      .querySelector(".quantity-decrease")
      .addEventListener("click", () =>
        changeQuantity(listItem, selectedPrice, -1)
      );
  }
  // อัพเดทราคาทั้งหมดในตะกร้า
  const currentTotal = parseFloat(cartTotal.textContent) || 0;
  const newTotal = currentTotal + selectedPrice;
  cartTotal.textContent = newTotal.toFixed(2);

  // อัพเดทยอดที่ต้องจ่ายในกรณีที่เลือกชำระเงินด้วยเงินสดหรือ QR code
  const paymentMethod = document.getElementById("payment-method").value;
  if (paymentMethod === "cash" || paymentMethod === "qrcode") {
    document.getElementById("amount-due").innerText = newTotal.toFixed(2);
  }

  alert(
    `เพิ่ม ${menuName} ขนาด ${selectedSize} ราคา ฿${selectedPrice} ลงตะกร้าเรียบร้อยแล้ว`
  );

  closeModal();
}

// ฟังก์ชันเพิ่ม/ลดจำนวนสินค้า
function changeQuantity(listItem, pricePerItem, change) {
  const quantityElement = listItem.querySelector(".quantity");
  let currentQuantity = parseInt(quantityElement.textContent) || 1;
  currentQuantity += change;

  // ถ้าจำนวนลดลงจนเหลือ 0 ให้ลบ item นี้ออกจากตะกร้า
  if (currentQuantity <= 0) {
    listItem.remove();
  } else {
    // อัพเดทจำนวนสินค้า
    quantityElement.textContent = currentQuantity;

    // อัพเดทราคาของสินค้า
    const itemPriceElement = listItem.querySelector(".item-price");
    const totalItemPrice = pricePerItem * currentQuantity;
    itemPriceElement.textContent = `฿${totalItemPrice.toFixed(2)}`;
  }

  // อัพเดทราคาทั้งหมดในตะกร้า
  updateCartTotal();
}

// ฟังก์ชันคำนวณราคารวมในตะกร้า
function updateCartTotal() {
  const cartItems = document.getElementById("cart-items");
  const cartTotal = document.getElementById("cart-total");

  let newTotal = 0;
  for (let i = 0; i < cartItems.children.length; i++) {
    const itemPriceElement = cartItems.children[i].querySelector(".item-price");
    const itemPrice =
      parseFloat(itemPriceElement.textContent.replace("฿", "")) || 0;
    newTotal += itemPrice;
  }

  cartTotal.textContent = newTotal.toFixed(2);

  // อัพเดทยอดที่ต้องจ่ายในกรณีที่เลือกชำระเงินด้วยเงินสดแล้ว
  const paymentMethod = document.getElementById("payment-method").value;
  if (paymentMethod === "cash" || paymentMethod === "qrcode") {
    document.getElementById("amount-due-display").innerText =
      newTotal.toFixed(2);
  }
}

// การจัดการวิธีการชำระเงิน
document.addEventListener("DOMContentLoaded", function () {
  const paymentMethodSelect = document.getElementById("payment-method");
  const cashDetails = document.getElementById("cash-details");
  const qrcodeDetails = document.getElementById("qrcode-details");
  const amountDueDisplay = document.getElementById("amount-due-display");

  paymentMethodSelect.addEventListener("change", function () {
    const cartTotal =
      parseFloat(document.getElementById("cart-total").innerText) || 0;

    amountDueDisplay.innerText = cartTotal.toFixed(2); // อัปเดตยอดรวมตามการเลือกวิธีการชำระเงิน

    if (this.value === "cash") {
      cashDetails.classList.remove("hidden");
      qrcodeDetails.classList.add("hidden");
    } else if (this.value === "qrcode") {
      cashDetails.classList.add("hidden");
      qrcodeDetails.classList.remove("hidden");
    } else {
      cashDetails.classList.add("hidden");
      qrcodeDetails.classList.add("hidden");
    }
  });
});

// การคำนวณเงินทอนสำหรับเงินสด
document.getElementById("cash-received").addEventListener("input", function () {
  const amountDue = parseFloat(
    document.getElementById("amount-due-display").innerText
  );
  const cashReceived = parseFloat(this.value) || 0;
  const changeAmount = cashReceived - amountDue;

  document.getElementById("change-amount-display").innerText =
    changeAmount >= 0 ? changeAmount.toFixed(2) : "0.00";

  // อัปเดต hidden input สำหรับส่งค่า change_amount
  document.getElementById("change-amount").value =
    changeAmount >= 0 ? changeAmount.toFixed(2) : "0.00";

  console.log("Change Amount:", changeAmount); // เพิ่มบรรทัดนี้เพื่อตรวจสอบค่า
});

// ยืนยันการชำระเงิน
document
  .getElementById("confirm-button")
  .addEventListener("click", function () {
    const paymentMethod = document.getElementById("payment-method").value;
    const amountDue = parseFloat(
      document.getElementById("amount-due-display").innerText
    );
    let paymentStatus = "pending"; // ค่าเริ่มต้น

    if (paymentMethod) {
      if (paymentMethod === "cash") {
        const cashReceived = parseFloat(
          document.getElementById("cash-received").value
        );
        const changeAmount = cashReceived - amountDue; // คำนวณเงินทอนที่นี่
        if (cashReceived >= amountDue) {
          alert(`ชำระเงินสำเร็จ เงินทอน: ฿${changeAmount.toFixed(2)}`);
          paymentStatus = "completed"; // เปลี่ยนสถานะเป็น completed สำหรับเงินสด
          const menuItems = getCartItems();
          sendPaymentData(
            paymentMethod,
            cashReceived,
            amountDue,
            menuItems,
            changeAmount.toFixed(2), // ส่ง changeAmount ไปด้วย
            paymentStatus
          );
        } else {
          alert(
            "เงินที่ได้รับไม่เพียงพอ กรุณาเพิ่มจำนวนเงินให้มากกว่าหรือเท่ากับยอดชำระ"
          );
        }
      } else if (paymentMethod === "qrcode") {
        alert("ชำระเงินด้วย QRCODE สำเร็จ");
        paymentStatus = "paid"; // เปลี่ยนสถานะเป็น paid สำหรับ QRCODE
        const menuItems = getCartItems();
        sendPaymentData(
          paymentMethod,
          null, // cashReceived เป็น null สำหรับ QRCODE
          amountDue,
          menuItems,
          null, // changeAmount เป็น null สำหรับ QRCODE
          paymentStatus
        );
      }
    } else {
      alert("กรุณาเลือกวิธีการชำระเงิน");
    }
    console.log(data);
  });

// ส่งข้อมูลการชำระเงิน
function sendPaymentData(
  paymentMethod,
  cashReceived,
  amountDue,
  menuItems,
  changeAmount, // ตรวจสอบการส่งค่า changeAmount
  paymentStatus
) {
  const data = {
    shop_id: document.getElementById("shop_id").value,
    username_account: document.getElementById("username_account").value,
    payment_method: paymentMethod,
    cash_received: cashReceived,
    amount_due: amountDue,
    menu_items: menuItems,
    change_amount: changeAmount, // ตรวจสอบการส่งค่า change_amount
    payment_status: paymentStatus,
  };

  fetch("process-history.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(data),
  })
    .then((response) => response.json())
    .then((result) => {
      if (result.success) {
        alert("บันทึกข้อมูลสำเร็จ");
      } else {
        alert("เกิดข้อผิดพลาด: " + (result.errors || result.message));
      }
    })
    .catch((error) => {
      console.error("Error:", error);
    });
}

// ปรับการแสดงผลให้ซ่อนฟิลด์อัปโหลดและแสดงส่วนการชำระเงินที่เกี่ยวข้อง
document
  .getElementById("payment-method")
  .addEventListener("change", function () {
    var paymentMethod = this.value;
    var qrcodeUploadSection = document.getElementById("qrcode-upload-section");
    var cashDetails = document.getElementById("cash-details");

    if (paymentMethod === "qrcode") {
      cashDetails.style.display = "none"; // ซ่อนฟิลด์เงินสด
      if (qrcodeUploadSection) {
        qrcodeUploadSection.style.display = "block"; // แสดงฟิลด์อัปโหลด QRCODE
      }
    } else if (paymentMethod === "cash") {
      cashDetails.style.display = "block"; // แสดงฟิลด์เงินสด
      if (qrcodeUploadSection) {
        qrcodeUploadSection.style.display = "none"; // ซ่อนฟิลด์ QRCODE
      }
    } else {
      cashDetails.style.display = "none"; // ซ่อนทั้งสองฟิลด์หากไม่มีการเลือกวิธีชำระเงิน
      if (qrcodeUploadSection) {
        qrcodeUploadSection.style.display = "none";
      }
    }
  });

// ฟังก์ชันสำหรับดึงรายการในรถเข็น (cart)
function getCartItems() {
  const cartItems = document.getElementById("cart-items");
  const items = [];

  for (let i = 0; i < cartItems.children.length; i++) {
    const item = cartItems.children[i];

    const menuName = item.getAttribute("data-menu-name");
    const size = item.getAttribute("data-size");
    const quantity = parseInt(item.querySelector(".quantity").textContent) || 1;
    const totalPrice = parseFloat(
      item.querySelector(".item-price").textContent.replace("฿", "")
    );

    items.push({
      menu_name: menuName,
      size: size,
      quantity: quantity, // ส่งจำนวนไปด้วย
      total_price: totalPrice,
    });
  }

  return items;
}

// การยกเลิกรายการทั้งหมด
document.getElementById("cancel-item").addEventListener("click", function () {
  const cartItems = document.getElementById("cart-items");
  cartItems.innerHTML = ""; // ลบรายการทั้งหมดในตะกร้า

  const cartTotal = document.getElementById("cart-total");
  cartTotal.textContent = "0.00"; // รีเซ็ตยอดรวม
});
