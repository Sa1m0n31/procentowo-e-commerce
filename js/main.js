console.log("hi!");

/* Toggle categories on shop page */
const toggleCategory = n => {
    const menu1 = document.querySelector("#categoryMenu1");
    const menu2 = document.querySelector("#categoryMenu2");
    const menu3 = document.querySelector("#categoryMenu3");

    const headerImg1 = document.querySelector("#headerImg1");
    const headerImg2 = document.querySelector("#headerImg2");
    const headerImg3 = document.querySelector("#headerImg3");

    let menuToToggle, headerToToggle;

    switch(n) {
        case 0:
            menuToToggle = menu1;
            headerToToggle = headerImg1;
            break;
        case 1:
            menuToToggle = menu2;
            headerToToggle = headerImg2;
            break;
        default:
            menuToToggle = menu3;
            headerToToggle = headerImg3;
            break;
    }

    if(menuToToggle.style.height !== "auto") {
        /* Show */
        gsap.to(menuToToggle, { height: "auto", opacity: 1, duration: .5 });
        headerToToggle.setAttribute("src", "http://skylo-test3.pl/wp-content/themes/storefront/assets/shop/minus_square.svg");
    }
    else {
        /* Hide */
        gsap.to(menuToToggle, { height: 0, opacity: 0, duration: .5 });
        headerToToggle.setAttribute("src", "http://skylo-test3.pl/wp-content/themes/storefront/assets/shop/plus_square.svg");
    }
}

/* Amend My Account page */
const loginUsernameInput = document.querySelector(".woocommerce-form-login>.woocommerce-form-row:first-of-type>input");
const loginPasswordInput = document.querySelector(".woocommerce-form-login>.woocommerce-form-row:nth-of-type(2)>input");

if(loginUsernameInput) {
    loginUsernameInput.placeholder = "Adres email";
    loginPasswordInput.placeholder = "Hasło";
}

/* Hide text content on payment methods */
const paymentMethods = document.querySelectorAll(".wc_payment_methods>li>label");
if(paymentMethods[0]) {
    console.log("Start!");
    paymentMethods.map(item => {
       item.textContent = "";
       console.log(item.textContent);
    });
}