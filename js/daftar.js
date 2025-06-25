function validateForm() {
  var password = document.getElementById("password").value;
  var confirmPassword = document.getElementById("confirm_password").value;

  if (password !== confirmPassword) {
    alert("Password and Confirm Password do not match!");
    return false;
  }
  return true;
}
