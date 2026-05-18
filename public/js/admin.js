function validateLogin(){
    let email = document.getElementById('email').value;
    let password = document.getElementById('password').value;

    if(email == "" || password == ""){
        document.getElementById('loginError').innerHTML = "Email and password are required!";
        return false;
    }else{
        return true;
    }
}

function validateCategory(){
    let name = document.getElementById('categoryName').value;
    let type = document.getElementById('categoryType').value;

    if(name == "" || type == ""){
        document.getElementById('categoryError').innerHTML = "Category name and type are required!";
        return false;
    }else{
        return true;
    }
}

function validateMedicine(){
    let name = document.getElementById('medicineName').value;
    let category = document.getElementById('medicineCategory').value;
    let vendor = document.getElementById('vendorName').value;
    let price = document.getElementById('price').value;
    let availability = document.getElementById('availability').value;
    let description = document.getElementById('description').value;
    let image = document.getElementById('image').files[0];

    if(name == "" || category == "" || vendor == "" || price == "" || availability == "" || description == ""){
        document.getElementById('medicineError').innerHTML = "All medicine fields are required!";
        return false;
    }else if(price <= 0){
        document.getElementById('medicineError').innerHTML = "Price must be greater than 0!";
        return false;
    }else if(availability < 0){
        document.getElementById('medicineError').innerHTML = "Stock can not be negative!";
        return false;
    }else if(image && image.size > 2097152){
        document.getElementById('medicineError').innerHTML = "Image size must be less than 2MB!";
        return false;
    }else{
        return true;
    }
}

function updateOrderStatus(id, status){
    let xhttp = new XMLHttpRequest();

    xhttp.open('post', '../controller/adminController.php', true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send('action=update_order_status&id='+id+'&status='+status);

    xhttp.onreadystatechange = function() {
        if(this.readyState == 4 && this.status == 200){
            let response = JSON.parse(this.responseText);
            let message = document.getElementById('orderMessage');

            message.innerHTML = response.message;
            message.style.display = "block";

            if(response.status == "success"){
                document.getElementById('status'+id).innerHTML = response.order_status;
                document.getElementById('accept'+id).disabled = true;
                document.getElementById('reject'+id).disabled = true;
                document.getElementById('accept'+id).className = "disabled-btn";
                document.getElementById('reject'+id).className = "disabled-btn";
            }
        }
    }
}
