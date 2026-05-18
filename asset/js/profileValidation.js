function validateProfile() {
    let isValid = true;

    document.getElementById("nameError").innerHTML = "";
    document.getElementById("emailError").innerHTML = "";
    document.getElementById("addressError").innerHTML = "";
    document.getElementById("phoneError").innerHTML = "";
    document.getElementById("profilePictureError").innerHTML = "";
    document.getElementById("currentPasswordError").innerHTML = "";
    document.getElementById("newPasswordError").innerHTML = "";
    document.getElementById("confirmPasswordError").innerHTML = "";

    const name = document.getElementById("name").value.trim();
    const email = document.getElementById("email").value.trim();
    const address = document.getElementById("address").value.trim();
    const phone = document.getElementById("phone").value.trim();
    const picture = document.getElementById("profile_picture").files[0];
    const currentPassword = document.getElementById("current_password").value;
    const newPassword = document.getElementById("new_password").value;
    const confirmPassword = document.getElementById("confirm_password").value;

    if (name === "") {
        document.getElementById("nameError").innerHTML = "Name is required";
        isValid = false;
    }

    if (email === "") {
        document.getElementById("emailError").innerHTML = "Email is required";
        isValid = false;
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        document.getElementById("emailError").innerHTML = "Enter a valid email address";
        isValid = false;
    }

    if (address === "") {
        document.getElementById("addressError").innerHTML = "Address is required";
        isValid = false;
    }

    if (phone === "") {
        document.getElementById("phoneError").innerHTML = "Phone is required";
        isValid = false;
    } else if (!/^[0-9+\-\s]{7,20}$/.test(phone)) {
        document.getElementById("phoneError").innerHTML = "Enter a valid phone number";
        isValid = false;
    }

    if (picture) {
        const allowedTypes = ["image/jpeg", "image/png"];

        if (!allowedTypes.includes(picture.type)) {
            document.getElementById("profilePictureError").innerHTML = "Only JPG and PNG images are allowed";
            isValid = false;
        } else if (picture.size > 2 * 1024 * 1024) {
            document.getElementById("profilePictureError").innerHTML = "Profile picture must be 2MB or smaller";
            isValid = false;
        }
    }

    const wantsPasswordChange = currentPassword !== "" || newPassword !== "" || confirmPassword !== "";

    if (wantsPasswordChange) {
        if (currentPassword === "") {
            document.getElementById("currentPasswordError").innerHTML = "Current password is required";
            isValid = false;
        }

        if (newPassword === "") {
            document.getElementById("newPasswordError").innerHTML = "New password is required";
            isValid = false;
        } else if (newPassword.length < 8) {
            document.getElementById("newPasswordError").innerHTML = "New password must be at least 8 characters";
            isValid = false;
        }

        if (confirmPassword === "") {
            document.getElementById("confirmPasswordError").innerHTML = "Confirm password is required";
            isValid = false;
        } else if (newPassword !== confirmPassword) {
            document.getElementById("confirmPasswordError").innerHTML = "Passwords do not match";
            isValid = false;
        }
    }

    return isValid;
}
