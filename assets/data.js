/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import 'admin-lte/plugins/fontawesome-free/css/all.min.css';
import 'admin-lte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css';
import 'admin-lte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css';
import 'admin-lte/plugins/datatables-buttons/css/buttons.bootstrap4.min.css';
import 'admin-lte/dist/css/adminlte.min.css';

// start the Stimulus application
import $ from 'admin-lte/plugins/jquery/jquery.min.js';
import 'admin-lte/plugins/bootstrap/js/bootstrap.bundle.min.js';

import dt from 'admin-lte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js';
import 'admin-lte/plugins/datatables-responsive/js/dataTables.responsive.min.js';
import 'admin-lte/plugins/datatables-responsive/js/responsive.bootstrap4.min.js';
import 'admin-lte/plugins/datatables-buttons/js/dataTables.buttons.min.js';
import 'admin-lte/plugins/datatables-buttons/js/buttons.bootstrap4.min.js';
import 'admin-lte/plugins/jszip/jszip.min.js';
import 'admin-lte/plugins/pdfmake/pdfmake.min.js';
import 'admin-lte/plugins/pdfmake/vfs_fonts.js';
import 'admin-lte/plugins/datatables-buttons/js/buttons.html5.min.js';
import 'admin-lte/plugins/datatables-buttons/js/buttons.print.min.js';
import 'admin-lte/plugins/datatables-buttons/js/buttons.colVis.min.js';
import 'admin-lte/dist/js/adminlte.min.js';
import 'admin-lte/dist/js/demo.js';
$.fn.dataTable = $.fn.DataTable = global.DataTable = dt;
$.fn.dataTableSettings = dt.settings;
$.fn.dataTableExt = dt.ext;
dt.$ = $;
$(document).ready(function () {
    $("#example1").DataTable({
        dom: 'B<"clear">lfrtip',
        "responsive": true, "lengthChange": false, "autoWidth": false,
        "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    $('#example2').DataTable({
        dom: 'B<"clear">lfrtip',
        "paging": true,
        "lengthChange": false,
        "searching": false,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "responsive": true,
    });
});
