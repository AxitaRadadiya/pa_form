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

<script>
$(document).ready( function () {
        
        $('.select2').select2();
        // //Date range picker
        $('.single_date').daterangepicker({
            singleDatePicker: true,
            showDropdowns: true,
            autoUpdateInput: false,
            locale: {
                format: 'MM/YYYY'
            }
        }).on("apply.daterangepicker", function (e, picker) {
            picker.element.val(picker.startDate.format(picker.locale.format));
        });

        // $('.dob').daterangepicker({
        //     singleDatePicker: true,
        //     showDropdowns: true,
        //     autoUpdateInput: false,
        //     locale: {
        //         format: 'DD/MM/YYYY'
        //     }
        // }).on("apply.daterangepicker", function (e, picker) {
        //     picker.element.val(picker.startDate.format(picker.locale.format));
        // });

        // bsCustomFileInput.init();

        $('.single_date').daterangepicker({
            singleDatePicker: true,
            showDropdowns: true,
            autoUpdateInput: false,
            locale: {
                format: 'MM/YYYY'
            },
            minYear: 2000,
            maxYear: 2050,
        }, function(start, end, label) {
            $(this.element).val(start.format('MM/YYYY'));
        });

        $('.single_date').on('show.daterangepicker', function(ev, picker) {
            picker.container.find('.calendar-table thead tr:not(:first-child)').hide();
            picker.container.find('.calendar-table tbody tr').hide();
            // picker.container.find('.monthselect').css('display', 'block');
            // picker.container.find('.yearselect').css('display', 'block');
        });

        var Toast = Swal.mixin({
          toast: true,
          position: 'top-end',
          showConfirmButton: false,
          timer: 3000
        });

        @if (session('success'))
            Toast.fire({
                icon: 'success',
                title: '{{ session('success') }}'
            })
        @endif
        @if (session('error'))
            Toast.fire({
                icon: 'error',
                title: '{{ session('error') }}'
            })
        @endif

        $(document).on("click", ".deleteButton", function(event){
            
            // Prevent the default form submission
            event.preventDefault();
            const form = this.closest('form');

            // Show the SweetAlert2 confirmation dialog
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // If confirmed, submit the form
                    form.submit();
                }
            });
        });

        
        $('#roleTable').DataTable({
              "paging": true,
              "lengthChange": false,
              "searching": true,
              "ordering": true,
              "info": true,
              "autoWidth": false,
              "responsive": true,
              "processing": true,
              "serverSide": true,
              
            "order": [0, 'desc'],
              "ajax":{
                 "url": "{{ route('roles.list') }}",
                 "dataType": "json",
                 "type": "GET",
                 "data":{ _token: "{{csrf_token()}}",route:'roles.list'}
              },
              "columns": [
                 { "data": "id" },
                 { "data": "name" },
                 { "data": "action" }
              ],
              aoColumnDefs: [
                 {
                    bSortable: false,
                    aTargets: [ -1 ]
                 }
              ]  
        });

        $('#permissionsTable').DataTable({
              "paging": true,
              "lengthChange": false,
              "searching": true,
              "ordering": true,
              "info": true,
              "autoWidth": false,
              "responsive": true,
              "processing": true,
              "serverSide": true,
              
            "order": [0, 'desc'],
              "ajax":{
                 "url": "{{ route('permissions.list') }}",
                 "dataType": "json",
                 "type": "GET",
                 "data":{ _token: "{{csrf_token()}}",route:'permissions.list'}
              },
              "columns": [
                 { "data": "id" },
                 { "data": "name" },
                 { "data": "action" }
              ],
              aoColumnDefs: [
                 {
                    bSortable: false,
                    aTargets: [ -1 ]
                 }
              ]  
        });

        load_user();
        function load_user(){

            $('#userTable').DataTable({
              "paging": true,
              "lengthChange": false,
              "searching": true,
              "ordering": true,
              "info": true,
              "autoWidth": false,
              "responsive": true,
              "processing": true,
              "serverSide": true,
              
            "order": [0, 'desc'],
              "ajax":{
                 "url": "{{ route('users.list') }}",
                 "dataType": "json",
                 "type": "GET",
                 "data":{ _token: "{{csrf_token()}}",route:'users.list'}
              },
              "columns": [
                 { "data": "id" },
                 { "data": "name" },
                 { "data": "email" },
                 { "data": "action" }
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
                }
            });
        }

        
});
</script>

