function showhidetoggle(className) {
    var elements = document.querySelectorAll("tr." + className);
    console.log(className);
    elements.forEach((el) => {
        el.classList.toggle('hide');
    });
}