/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.scss';

// start the Stimulus application
import './bootstrap';

let link = document.querySelector("#watchlist")

link.addEventListener('click', addToWatchlist);

function addToWatchlist(event) {
    event.preventDefault();

    fetch(link.href).then(response => {
        if (response.ok) return response.json()
    }).then(data => {
        if (data.isInWatchlist){
            link.innerHTML = 'Retirer de la liste des favoris <i class="fas fa-heart"></i>'
        }else{
            link.innerHTML = 'Ajouter en favori <i class="far fa-heart"></i>'
        }
    })
}