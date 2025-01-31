function showhidetoggle(className) {
    var elements = document.querySelectorAll("tr." + className);
    elements.forEach((el) => {
        el.classList.toggle('hide');
    });
}