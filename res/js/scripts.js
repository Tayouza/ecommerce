switch (window.location.pathname) {
    case "/":
        document.querySelector("#home").classList.add('active')
        break
    case "/lista-produtos":
        document.querySelector("#lista-produtos").classList.add('active')
        break
    case "/carrinho":
        document.querySelector("#carrinho").classList.add('active')
        break
}