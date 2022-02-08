function IsEmailValid(email) {
	var emailOK = true;
	if (email.trim().length === 0) return true;
	if (emailOK && email.indexOf("@") < 0) emailOK = false;
	if (emailOK && email.indexOf(" ") >= 0) emailOK = false;
	if (emailOK && email.indexOf("\t") >= 0) emailOK = false;
	if (emailOK) {
		var beforeAt = email.substr(0, email.indexOf("@"));
		var afterAt = email.substr(email.indexOf("@") + 1);

		if (afterAt.indexOf(".") < 0) emailOK = false;
		if (emailOK && afterAt.indexOf(".") === 0) emailOK = false;
		if (emailOK && afterAt.indexOf(".") === afterAt.length - 1) emailOK = false;
	}

	return emailOK
}

function IsTelephoneNoValid(no) {
	var l = no.length;
	if (no.length === 0) return true;
	for (var a = 0; a < l; a++) {
		var c = no.substr(a, 1);
		if (c != "0" &&
			c != "1" &&
			c != "2" &&
			c != "3" &&
			c != "4" &&
			c != "5" &&
			c != "6" &&
			c != "7" &&
			c != "8" &&
			c != "9" &&
			c != " " &&
			(c != "+" || a != 0)) {
			return false;
		}
	}

	var c1 = no.trim().substr(0, 1);
	if (c1 != "0" && c1 != "+") {
		return false;
	}

	return true;
}

function IsDateValid(thedate) {
	var d = thedate.trim();
	if (d.length === 0) return true;
	if (d.length != 10) return false;

	if (isNaN(d.substr(0, 2))) return false;
	if (isNaN(d.substr(3, 2))) return false;
	if (isNaN(d.substr(6, 4))) return false;

	if (d.substr(2, 1) != ".") return false;
	if (d.substr(5, 1) != ".") return false;

	return true;
}

function IsPasswordElementValid(element) {
	var elements = document.getElementsByClassName("PasswordVerification");
	for (var e = 0; e < elements.length; e++) {
		if (elements[e].value != element.value) return false;
	}
	return true;
}

function checkForEnterPressed(element, callback) {
	element.addEventListener("keyup", function (e) {
		if (e.which === 13) {
			callback(element);
		}
	});
}

function CheckStandardForm(e) {
	console.log(1);
	var errors = 0;
	var inputFields = e.target.getElementsByClassName("InputField");
	for (var i = 0; i < inputFields.length; i++) {
		var field = inputFields[i];

		var errorMessageElements = field.parentNode.getElementsByClassName("Errors");
		var errorMessage = false;
		if (errorMessageElements.length > 0) {
			errorMessage = errorMessageElements[0];
			console.log("Found errorMessage");
		} else {
			errorMessage = document.createElement("div");
			errorMessage.classList.add("Errors");
			field.parentNode.insertBefore(errorMessage, field.nextSibling);
			console.log("Added errorMessage");
		}

		if (errorMessage) {
			errorMessage.innerHTML = "";
			if (!errorMessage.classList.contains("Hidden")) errorMessage.classList.add("Hidden");
		}

		if (field.classList.contains("Mandatory") && field.value.trim().length === 0) {
			if (errorMessage) {
				errorMessage.innerHTML += "Angabe fehlt!";
				if (errorMessage.classList.contains("Hidden")) errorMessage.classList.remove("Hidden");
			}
			errors++;
		} else if (field.classList.contains("ValidEmail") && !IsEmailValid(field.value.trim())) {
			if (errorMessage) {
				errorMessage.innerHTML += "<div>E-Mail ungültig!</div>";
				if (errorMessage.classList.contains("Hidden")) errorMessage.classList.remove("Hidden");
			}
			errors++;
		} else if (field.classList.contains("ValidPhone") && !IsTelephoneNoValid(field.value.trim())) {
			if (errorMessage) {
				errorMessage.innerHTML += "<div>Format falsch!</div>";
				if (errorMessage.classList.contains("Hidden")) errorMessage.classList.remove("Hidden");
			}
			errors++;
		} else if (field.classList.contains("ValidDate") && !IsDateValid(field.value.trim())) {
			if (errorMessage) {
				errorMessage.innerHTML += "<div>Format falsch!</div>";
				if (errorMessage.classList.contains("Hidden")) errorMessage.classList.remove("Hidden");
			}
			errors++;
		} else if (field.classList.contains("ValidNumber") && (isNaN(field.value.trim()))) {
			if (errorMessage) {
				errorMessage.innerHTML += "<div>Keine gültige Nummer!</div>";
				if (errorMessage.classList.contains("Hidden")) errorMessage.classList.remove("Hidden");
			}
			errors++;
		} else if (field.classList.contains("ValidYear") && (isNaN(field.value.trim()) || 0 + field.value.trim() < 1000)) {
			if (errorMessage) {
				errorMessage.innerHTML += "<div>Kein gültiges Jahr!</div>";
				if (errorMessage.classList.contains("Hidden")) errorMessage.classList.remove("Hidden");
			}
			errors++;
		} else if (field.classList.contains("PasswordVerification") && !IsPasswordElementValid(field)) {
			if (errorMessage) {
				errorMessage.innerHTML += "<div>Passwörte nicht identisch!</div>";
				if (errorMessage.classList.contains("Hidden")) errorMessage.classList.remove("Hidden");
			}
			errors++;
		}
	}

	if (typeof (e) == "object" && typeof (e.preventDefault) == "function") {
		if (errors) e.preventDefault();
	} else {
		return errors === 0;
	}
}

window.addEventListener("load", function () {
	var forms = document.querySelectorAll(".StandardForm,.StandardFormWide");

	forms.forEach(function (theForm) {
		theForm.addEventListener("submit", CheckStandardForm);
		var submitButtons = theForm.getElementsByClassName("SubmitButton");
		for (var b = 0; b < submitButtons.length; b++) {
			var button = submitButtons[b];
			button.addEventListener("click", function (e) {
				e.preventDefault();
				if (CheckStandardForm({ target: theForm })) {
					theForm.submit();
				}
			});
		}

		var inputFields = theForm.getElementsByClassName("InputField");
		for (var i = 0; i < inputFields.length; i++) {
			var inputField = inputFields[i];
			inputField.addEventListener("keyup", function (e) {
				if (e.which === 13) {
					if (CheckStandardForm({ target: theForm })) {
						theForm.submit();
					}
				}
			});
		}

		var inputFields = theForm.getElementsByClassName("RichEditor");
		for (var i = 0; i < inputFields.length; i++) {
			var inputField = inputFields[i];
			if (inputField.classList.contains("NoToolbar")) {
				CKEDITOR.replace(inputField.id, { customConfig: '/assets/ckeditor/config_notoolbar.js' });
			} else {
				CKEDITOR.replace(inputField.id, { customConfig: '/assets/ckeditor/config_standard.js' });
			}
		}
	});
});