function validateRegistration() {

    let isValid = true;

    
    document.getElementById("nameError").innerHTML = "";
    document.getElementById("emailError").innerHTML = "";
    document.getElementById("passwordError").innerHTML = "";
    document.getElementById("confirmPasswordError").innerHTML = "";
    document.getElementById("addressError").innerHTML = "";
    document.getElementById("phoneError").innerHTML = "";
    document.getElementById("roleError").innerHTML = "";

    
    const name = document.getElementById("name").value.trim();
    const email = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value;
    const confirmPassword = document.getElementById("confirm_password").value;
    const address = document.getElementById("address").value.trim();
    const phone = document.getElementById("phone").value.trim();
    const role = document.getElementById("role").value;

    
    if (name === "") {
        document.getElementById("nameError").innerHTML = "Name is required";
        isValid = false;
    }

    
    if (email === "") {
        document.getElementById("emailError").innerHTML = "Email is required";
        isValid = false;
    }

    
    if (password.length < 8) {
        document.getElementById("passwordError").innerHTML =
            "Password must be at least 8 characters";
        isValid = false;
    }

    
    if (password !== confirmPassword) {
        document.getElementById("confirmPasswordError").innerHTML =
            "Passwords do not match";
        isValid = false;
    }

    
    if (address === "") {
        document.getElementById("addressError").innerHTML =
            "Address is required";
        isValid = false;
    }

    
    if (phone === "") {
        document.getElementById("phoneError").innerHTML =
            "Phone is required";
        isValid = false;
    }

    
    if (role === "") {
        document.getElementById("roleError").innerHTML =
            "Please select a role";
        isValid = false;
    }

    return isValid;
}