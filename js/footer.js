function posicionFooter() {
    const footer = document.getElementById('idFin');
    const bodyHeight = document.body.scrollHeight;
    const windowHeight = window.innerHeight;
    if (bodyHeight > windowHeight) {
        footer.style.position = 'relative';
        footer.style.bottom = 'auto';
    } else {
        footer.style.position = 'absolute';
        footer.style.bottom = '0';
    }
}

window.addEventListener('resize', posicionFooter);
window.addEventListener('load', posicionFooter);