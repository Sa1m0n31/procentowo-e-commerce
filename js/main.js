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
        headerToToggle.setAttribute("src", "http://procentowo.com/woocommerce/wp-content/themes/storefront/assets/shop/minus_square.svg");
    }
    else {
        /* Hide */
        gsap.to(menuToToggle, { height: 0, opacity: 0, duration: .5 });
        headerToToggle.setAttribute("src", "http://procentowo.com/woocommerce/wp-content/themes/storefront/assets/shop/plus_square.svg");
    }
}

/* Amend My Account page */
const loginUsernameInput = document.querySelector(".woocommerce-form-login>.woocommerce-form-row:first-of-type>input");
const loginPasswordInput = document.querySelector(".woocommerce-form-login>.woocommerce-form-row:nth-of-type(2)>input");

if(loginUsernameInput) {
    loginUsernameInput.placeholder = "Adres email";
    loginPasswordInput.placeholder = "Hasło";
}

const registerUsernameInput = document.querySelector(".u-column2>form>p:nth-of-type(1)>input");
const registerEmailInput = document.querySelector(".u-column2>form>p:nth-of-type(2)>input");
const registerPasswordInput = document.querySelector(".u-column2>form>p:nth-of-type(3)>input");

if(registerUsernameInput) {
    registerUsernameInput.placeholder = "Nazwa użytkownika";
    registerEmailInput.placeholder = "Adres email";
    registerPasswordInput.placeholder = "Hasło";
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

/* Open and close mobile menu */
const mobileMenuRef = document.querySelector(".mobileMenu");
const openMobileMenu = () => {
    if(mobileMenuRef) mobileMenuRef.style.transform = "translateX(0)";
}

const closeMobileMenu = () => {
    if(mobileMenuRef) mobileMenuRef.style.transform = "translateX(1000px)";
}

/* Slider Siema carousel */
const sliderContainer = document.querySelector(".slider");
let siemaSlider, changeSlideInterval;
if(sliderContainer) {
    if(window.innerWidth > 768) {
        siemaSlider = new Siema({
            selector: ".sliderSiemaContainer",
            perPage: 1,
            loop: true
        });
    }
    else {
        siemaSlider = new Siema({
            selector: ".sliderSiemaContainer",
            perPage: 1,
            draggable: false,
            loop: false
        });
    }

    changeSlideInterval = setInterval(() => {
        siemaSlider.next();
        changeSliderControls(siemaSlider.currentSlide);
    }, 6000);
}

const siemaSliderControls = [
    document.querySelector("#sliderControl1"),
    document.querySelector("#sliderControl2"),
    document.querySelector("#sliderControl3")
]

const changeSliderControls = (n) => {
    if(siemaSliderControls[0]) {
        siemaSliderControls.forEach(item => {
            item.style.background = "transparent";
        });
    }
    siemaSliderControls[n].style.background = "#000";
}

const goToSiemaSlider = (n) => {
    clearInterval(changeSlideInterval);
    changeSliderControls(n);
    siemaSlider.goTo(n);
}

/* Slider arrows */
const sliderLeft = () => {
    siemaSlider.prev();
    goToSiemaSlider(siemaSlider.currentSlide);
}

const sliderRight = () => {
    siemaSlider.next();
    goToSiemaSlider(siemaSlider.currentSlide);
}

/* Sticky menu */
const topMenu = document.querySelector(".topMenu");
const topMenuBefore = document.querySelector(".topMenu__before");
const topMenuAfter = document.querySelector(".topMenu__after");

window.addEventListener("scroll", () => {
    if(window.pageYOffset > 200) {
        topMenuBefore.style.opacity = "1";
        topMenuAfter.style.opacity = "1";
    }
    else {
        topMenuBefore.style.opacity = "0";
        topMenuAfter.style.opacity = "0";
    }
});

/* Confirm your age button */
const confirmAgePopup = document.querySelector(".confirmAgePopup");
if(localStorage.getItem('procentowo-age') !== 'T') {
    confirmAgePopup.style.display = "flex";
}

const closeConfirmAgePopup = () => {
    localStorage.setItem('procentowo-age', 'T');
    confirmAgePopup.style.opacity = "0";
    setTimeout(() => {
        confirmAgePopup.style.display = "none";
    }, 500);
}

/* Add to cart popup - Polish captions */
const viewCartBtn = document.querySelector(".xoo-cp-btn-vc");
const continueShippingBtn = document.querySelector(".xoo-cp-btns>.xoo-cp-close");

if(viewCartBtn) {
    viewCartBtn.textContent = "Zobacz koszyk";
    continueShippingBtn.textContent = "Kontynuuj zakupy";
}