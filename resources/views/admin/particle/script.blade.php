<script src="{{ asset('newAdmin/js/jquery.min.js') }}"></script>
<script src="{{ asset('newAdmin/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('newAdmin/js/metismenu.min.js') }}"></script>
<script src="{{ asset('newAdmin/js/waves.js') }}"></script>
<script src="{{ asset('newAdmin/js/simplebar.min.js') }}"></script>

<!-- Plugins js -->
<script src="{{ asset('newAdmin/plugins/autonumeric/autoNumeric-min.js') }}"></script>
<script src="{{ asset('newAdmin/plugins/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('newAdmin/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js') }}"></script>
<script src="{{ asset('newAdmin/plugins/moment/moment.js') }}"></script>
<script src="{{ asset('newAdmin/plugins/daterangepicker/daterangepicker.js') }}"></script>
<script src="{{ asset('newAdmin/plugins/select2/select2.min.js') }}"></script>
<script src="{{ asset('newAdmin/plugins/switchery/switchery.min.js') }}"></script>
<script src="{{ asset('newAdmin/plugins/bootstrap-colorpicker/bootstrap-colorpicker.min.js') }}"></script>
<script src="{{ asset('newAdmin/plugins/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js') }}"></script>
<script src="{{ asset('newAdmin/plugins/katex/katex.min.js') }}"></script>
{{-- <script src="{{ asset('newAdmin/plugins/quill/quill.min.js') }}"></script> --}}

<!-- Summernote -->
<script src="{{ asset('newAdmin/plugins/summernote/summernote-bs4.min.js') }}"></script>

<!-- Init js-->
{{-- <script src="{{ asset('newAdmin/pages/quilljs-demo.js') }}"></script> --}}

<script src="{{ asset('newAdmin/plugins/dropify/dropify.min.js') }}"></script>
<!-- Init js-->
<script src="{{ asset('newAdmin/pages/fileuploads-demo.js') }}"></script>

<!-- Custom Js -->
<script src="{{ asset('newAdmin/pages/advanced-plugins-demo.js') }}"></script>

<!-- Mask Js-->
<script src="{{ asset('newAdmin/plugins/jquery-mask/jquery.mask.min.js') }}"></script>

<!-- Mask Custom Js-->
<script src="{{ asset('newAdmin/pages/mask-demo.js') }}"></script>

<!-- third party js -->
<script src="{{ asset('newAdmin/plugins/datatables/jquery.dataTables.min.js') }}"></script>

<!-- rowReorder removed -->

<script src="{{ asset('newAdmin/plugins/datatables/dataTables.bootstrap4.js') }}"></script>
<script src="{{ asset('newAdmin/plugins/datatables/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('newAdmin/plugins/datatables/responsive.bootstrap4.min.js') }}"></script>
<script src="{{ asset('newAdmin/plugins/datatables/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('newAdmin/plugins/datatables/buttons.bootstrap4.min.js') }}"></script>
<script src="{{ asset('newAdmin/plugins/datatables/buttons.html5.min.js') }}"></script>
<script src="{{ asset('newAdmin/plugins/datatables/buttons.flash.min.js') }}"></script>
<script src="{{ asset('newAdmin/plugins/datatables/buttons.print.min.js') }}"></script>
<script src="{{ asset('newAdmin/plugins/datatables/dataTables.keyTable.min.js') }}"></script>
<script src="{{ asset('newAdmin/plugins/datatables/dataTables.select.min.js') }}"></script>
<script src="{{ asset('newAdmin/plugins/datatables/pdfmake.min.js') }}"></script>
<script src="{{ asset('newAdmin/plugins/datatables/vfs_fonts.js') }}"></script>
<!-- third party js ends -->

<!-- Datatables init -->
<script src="{{ asset('newAdmin/pages/datatables-demo.js') }}"></script>

<!-- Sweet Alerts Js-->
<script src="{{ asset('newAdmin/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
<!-- App js -->
<script src="{{ asset('newAdmin/js/theme.js') }}"></script>
<!-- Sweet Alerts Js-->
<script src="{{ asset('newAdmin/pages/sweet-alert-demo.js') }}"></script>

<script src="https://www.gstatic.com/charts/loader.js"></script>

<!-- Google chart custom js-->
<script src="{{ asset('newAdmin/pages/google-chart-demo.js') }}"></script>

<script>
$(document).ready( function () {

    $(document).on("click", ".convertCustomerButton", function(event){    
        // Prevent the default form submission
        event.preventDefault();
        const form = this.closest('form');

        // Show the SweetAlert2 confirmation dialog
        Swal.fire({
            title: 'Are you sure convert to customer?',
            {{-- text: "You won't be able to revert this!", --}}
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes'
        }).then((result) => {
            if (result && (result.isConfirmed || result.value)) {
                // If confirmed, submit the form
                console.log("Submitting form...");
                form.submit();
            }
        });
    });

    $(document).on("click", ".status-change", function(event){
        
        // Prevent the default form submission
        event.preventDefault();
        const form = this.closest('form');
        console.log("form");
        // Show the SweetAlert2 confirmation dialog
        Swal.fire({
            title: 'Are you sure?',
            {{-- text: "You won't be able to revert this!", --}}
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes'
        }).then((result) => {
            if (result && (result.isConfirmed || result.value)) {
                // If confirmed, submit the form
                console.log("Submitting form...");
                form.submit();
            }
        });
    });

        if ($.fn.DataTable && $('#FoodTable').length) {
            load_foods();
        }
        if ($.fn.DataTable && $('#ChapterTable').length) {
            load_chapters();
        }
        if ($.fn.DataTable && $('#RelationTable').length) {
            load_relations();
        }
        if ($.fn.DataTable && $('#EventTable').length) {
            load_events();
        }
        if ($.fn.DataTable && $('#UsersTable').length) {
            load_users();
        }
        if ($.fn.DataTable && $('#ActivityTable').length) {
            load_activity();
        }
    function load_foods(){

        var table = $('#FoodTable').DataTable({
          "paging": true,
          "lengthChange": true,
           "searching": true,
          "ordering": true,
          "info": true,
          "autoWidth": false,
          "responsive": true,
          "processing": true,
          "serverSide": true,
          "order": [0, 'asc'],
          
          "ajax":{
             "url": "{{ route('foods.list') }}",
             "dataType": "json",
             "type": "GET",
             "data":{ _token: "{{csrf_token()}}",route:'foods.list'}
          },
          "columns": [
             { "data": "id" },
             { "data": "name" },
          ],
          aoColumnDefs: [
             {
                bSortable: false,
                aTargets: [ -1 ]
             }
          ],
          "language": {
                "paginate": {
                    "previous": "<i class='mdi mdi-chevron-left'>",
                    "next": "<i class='mdi mdi-chevron-right'>"
                }
            },
            "drawCallback": function () {
                $('.dataTables_paginate > .pagination').addClass('pagination-rounded');

                $('[data-toggle="tooltip"]').tooltip();
            }
        });
    }

    function load_activity(){

        var table = $('#ActivityTable').DataTable({
          "paging": true,
          "lengthChange": true,
          "searching": true,
          "ordering": true,
          "info": true,
          "autoWidth": false,
          "responsive": true,
          "processing": true,
          "serverSide": true,
          "order": [4, 'desc'],
          "ajax":{
             "url": "{{ route('activity.list') }}",
             "dataType": "json",
             "type": "GET",
             "data": function(d){ return $.extend({}, d); }
          },
          "columns": [
             { "data": "description" },
             { "data": "causer" },
             { "data": "subject" },
             { "data": "log_name" },
             { "data": "created_at" },
          ],
          aoColumnDefs: [
             {
                bSortable: false,
                aTargets: [ -1 ]
             }
          ],
          "language": {
                "paginate": {
                    "previous": "<i class='mdi mdi-chevron-left'>",
                    "next": "<i class='mdi mdi-chevron-right'>"
                }
            },
            "drawCallback": function () {
                $('.dataTables_paginate > .pagination').addClass('pagination-rounded');

                $('[data-toggle="tooltip"]').tooltip();
            }
        });

    }
    
    function load_chapters(){

        var table = $('#ChapterTable').DataTable({
          "paging": true,
          "lengthChange": true,
          "searching": true,
          "ordering": true,
          "info": true,
          "autoWidth": false,
          "responsive": true,
          "processing": true,
          "serverSide": true,
          "order": [0, 'asc'],
          
          "ajax":{
             "url": "{{ route('chapter.list') }}",
             "dataType": "json",
             "type": "GET",
             "data":{ _token: "{{csrf_token()}}",route:'chapter.list'}
          },
          "columns": [
             { "data": "id" },
             { "data": "name" },
          ],
          aoColumnDefs: [
             {
                bSortable: false,
                aTargets: [ -1 ]
             }
          ],
          "language": {
                "paginate": {
                    "previous": "<i class='mdi mdi-chevron-left'>",
                    "next": "<i class='mdi mdi-chevron-right'>"
                }
            },
            "drawCallback": function () {
                $('.dataTables_paginate > .pagination').addClass('pagination-rounded');

                $('[data-toggle="tooltip"]').tooltip();
            }
        });

    }
    function load_relations(){

        var table = $('#RelationTable').DataTable({
          "paging": true,
          "lengthChange": true,
          "searching": true,
          "ordering": true,
          "info": true,
          "autoWidth": false,
          "responsive": true,
          "processing": true,
          "serverSide": true,
          "order": [0, 'asc'],
          
          "ajax":{
             "url": "{{ route('relations.list') }}",
             "dataType": "json",
             "type": "GET",
             "data":{ _token: "{{csrf_token()}}",route:'relations.list'}
          },
          "columns": [
             { "data": "id" },
             { "data": "name" },
          ],
          aoColumnDefs: [
             {
                bSortable: false,
                aTargets: [ -1 ]
             }
          ],
          "language": {
                "paginate": {
                    "previous": "<i class='mdi mdi-chevron-left'>",
                    "next": "<i class='mdi mdi-chevron-right'>"
                }
            },
            "drawCallback": function () {
                $('.dataTables_paginate > .pagination').addClass('pagination-rounded');

                $('[data-toggle="tooltip"]').tooltip();
            }
        });

    }
    function load_events(){

        var table = $('#EventTable').DataTable({
          "paging": true,
          "lengthChange": true,
          "searching": true,
          "ordering": true,
          "info": true,
          "autoWidth": false,
          "responsive": true,
          "processing": true,
          "serverSide": true,
          "order": [0, 'asc'],
          "ajax":{
             "url": "{{ route('events.list') }}",
             "dataType": "json",
             "type": "GET",
             "data": function(d){
                 // add custom filters here if needed
                 return $.extend({}, d);
             }
          },
             "columns": [
                 { "data": "id" },
                 { "data": "name" },
                 { "data": "image", "render": function(data){ return data ? '<img src="'+data+'" style="height:40px;"/>' : ''; } },
                 { "data": "qr_code", "render": function(data){ return data ? '<a href="'+data+'" target="_blank" title="Open QR"><img src="'+data+'" style="height:40px; width:40px; object-fit:cover;"/></a>' : ''; } },
                 { "data": "description" },
                 { "data": "action" },
             ],
          aoColumnDefs: [
             {
                bSortable: false,
                aTargets: [ -1 ]
             }
          ],
          "language": {
                "paginate": {
                    "previous": "<i class='mdi mdi-chevron-left'>",
                    "next": "<i class='mdi mdi-chevron-right'>"
                }
            },
            "drawCallback": function () {
                $('.dataTables_paginate > .pagination').addClass('pagination-rounded');

                $('[data-toggle="tooltip"]').tooltip();
            }
        });

    }

    function load_users(){

        var table = $('#UsersTable').DataTable({
          "paging": true,
          "lengthChange": true,
          "searching": true,
          "ordering": true,
          "info": true,
          "autoWidth": false,
          "responsive": true,
          "processing": true,
          "serverSide": true,
          "order": [0, 'asc'],
          "ajax":{
             "url": "{{ route('users.list') }}",
             "dataType": "json",
             "type": "GET",
             "data": function(d){ return $.extend({}, d); }
          },
          "columns": [
             { "data": "id" },
             { "data": "first_name" },
             { "data": "last_name" },
             { "data": "mobile" },
             { "data": "email" },
             { "data": "action", "orderable": false, "searchable": false },
          ],
          "language": {
                "paginate": {
                    "previous": "<i class='mdi mdi-chevron-left'>",
                    "next": "<i class='mdi mdi-chevron-right'>"
                }
            },
            "drawCallback": function () {
                $('.dataTables_paginate > .pagination').addClass('pagination-rounded');

                $('[data-toggle="tooltip"]').tooltip();
            }
        });

    }

});
</script>
@if(session('success'))
<script>
    Swal.fire({
        toast: true,
        position: 'top-end',
        type: 'success',
        title: "{{ session('success') }}",
        showConfirmButton: false,
        timer: 3500
    });
</script>
@endif

@if(session('error'))
<script>
    Swal.fire({
        toast: true,
        position: 'top-end',
        type: 'error',
        title: "{{ session('error') }}",
        showConfirmButton: false,
        timer: 3500
    });
</script>
@endif

