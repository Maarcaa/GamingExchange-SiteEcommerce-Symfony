/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';

// start the Stimulus application
import './bootstrap';


document.getElementById('generate').onclick = function() {
 
    var values = ["dog", "cat", "parrot", "rabbit"];
 
    var select = document.createElement("select");
    select.name = "pets";
    select.id = "pets"
 
    for (const val of values)
    {
        var option = document.createElement("option");
        option.value = val;
        option.text = val.charAt(0).toUpperCase() + val.slice(1);
        select.appendChild(option);
    }
 
    var label = document.createElement("label");
    label.innerHTML = "Choose your pets: "
    label.htmlFor = "pets";
 
    document.getElementById("container").appendChild(label).appendChild(select);
}
