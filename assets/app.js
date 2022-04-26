/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.scss';

const $ = require('jquery');

global.$ = global.jQuery = $;

$(document).ready( function() {
    $('#table_todisplay').DataTable();
});

$(document).ready( function() {
    $('#measures_todisplay').DataTable({
        "order": [[0, 'desc']]
    });
});

import "../node_modules/datatables.net/js/jquery.dataTables.js";
import "../node_modules/datatables.net-dt/js/dataTables.dataTables.js";

// start the Stimulus application
import './bootstrap';
