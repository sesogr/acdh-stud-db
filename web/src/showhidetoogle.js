export function showhidetoogle(className) {
    var elements = document.getElementsByClassName(className);
    Array.prototype.forEach.call(elements, function(el) {
        if (el.nodeName.toLowerCase() === 'tr') {
            el.classList.toggle('hide');
        }
    });
}