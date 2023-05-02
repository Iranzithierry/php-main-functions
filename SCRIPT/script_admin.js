setInterval(function() {
    var alert = document.querySelector(".alert");
    if ((alert.style.display = "flex")) {
        alert.style.display = "none";
    }
}, 3000);
const table_buttons = document.querySelectorAll(".action_btn");
const form = document.getElementById("form");

table_buttons.forEach(function(button) {
    button.addEventListener("click", function(e) {
        e.preventDefault();
    });
});

function printTable() {
    var printContents = document.getElementById("table-container").innerHTML;
    var originalContents = document.body.innerHTML;
    document.body.innerHTML = printContents;
    window.print();
    document.body.innerHTML = originalContents;
}

function printTableall() {
    var printContents = document.getElementById("all_table").innerHTML;
    var originalContents = document.body.innerHTML;
    document.body.innerHTML = printContents;
    window.print();
    document.body.innerHTML = originalContents;
}
btn_deleted = document.querySelector(".deleted");
btn_active = document.querySelector(".active");
table_active = document.getElementById("table-container");
table_deleted = document.getElementById("table_deleted");
btn_deleted.addEventListener("click", function(e) {
    if (table_active.style.display === "block") {
        table_active.style.display = "none";
    } else {
        table_active.style.display = "block";
        window.scrollTo({
            top: btn_active.offsetTop,
            behavior: "smooth",
        });
    }
});

btn_active.addEventListener("click", function(e) {
    if (table_deleted.style.display === "block") {
        table_deleted.style.display = "none";
    } else {
        table_deleted.style.display = "block";
        window.scrollTo({
            top: btn_deleted.offsetTop,
            behavior: "smooth",
        });
    }
});

function printTable_deleted() {
    var printContents = document.getElementById("table_deleted").innerHTML;
    var originalContents = document.body.innerHTML;
    document.body.innerHTML = printContents;
    window.print();
    document.body.innerHTML = originalContents;
}

function showConfirmation(id) {
    var popup = document.getElementById("popup-" + id);
    popup.style.display = "block";
}

function delete_forever(id) {
    var popup = document.getElementById("popup-" + id + "-forever");
    popup.style.display = "block";
}

function hide(id) {
    var popup = document.getElementById("popup-" + id);
    popup.style.display = "none";
}

function hide_forever(id) {
    var popup = document.getElementById("popup-" + id + "-forever");
    popup.style.display = "none";
}

function deleteUser(id) {
    window.location.href = "delete.php?id=" + id;
}

function deleteUserForever(id) {
    window.location.href = "deleted_forever.php?id=" + id;
}

function email() {
    var email_send = document.querySelector(".email_send");
    email_send.style.display = "block";
}

function closebtn() {
    var email_send = document.querySelector(".email_send");
    email_send.style.display = "none";
}