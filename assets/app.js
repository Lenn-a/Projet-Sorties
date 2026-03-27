import './stimulus_bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';

// EMILIA's JS code for OUTING CANCELLATION FORM
let cancelFormTrigger = document.querySelector('.cancel-form-trigger')
let cancelForm = document.querySelector('.cancel-form-div')
cancelFormTrigger.addEventListener('click', function(){
    cancelFormTrigger.style.display = 'none';
    cancelForm.style.display = 'block';
})

// -----------------------------------------------


console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');
