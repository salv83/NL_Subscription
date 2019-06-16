function subscribe(identifier){
        var formInputId = "form-id"+identifier;
        var respondeId = "response-id"+identifier; 
    	var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    	var email = document.getElementById(formInputId).value;
    	var element = document.getElementById(respondeId);
        if(re.test(email)){
            element.innerHTML = "Thank you for your subscription!";
            element.classList.remove("newsletter__check_error");
            element.classList.add("newsletter__check_success");
        }else{
        	element.innerHTML = "The email address provided has a wrong format.";
        	element.classList.remove("newsletter__check_success");
        	element.classList.add("newsletter__check_error");
        }
    	
}

window.subscribe = subscribe;