//Actualise le contenu de la messagerie toutes les 2 secondes
function rafraichirMessages() {
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "../controller/messages.php", true);
    xhr.onload = function () {
        if (xhr.status === 200) {
            document.querySelector('.messages_box').innerHTML = xhr.responseText;
        }
    };
    xhr.send();
}

setInterval(rafraichirMessages, 2000);
window.onload = rafraichirMessages;
