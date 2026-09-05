<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{ asset('logo/logo-logindo.png') }}">
    <title>Vessel Daily Report</title>
<meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet"
        href="{{ asset('adminlte/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet"
        href="{{ asset('adminlte/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
    <!-- iCheck -->
    <link rel="stylesheet"
        href="{{ asset('adminlte/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <!-- JQVMap -->
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/jqvmap/jqvmap.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('adminlte/dist/css/adminlte.min.css') }}">
    <!-- overlayScrollbars -->
    <link rel="stylesheet"
        href="{{ asset('adminlte/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
    <!-- Daterange picker -->
    <link rel="stylesheet"
        href="{{ asset('adminlte/plugins/daterangepicker/daterangepicker.css') }}">
    <!-- summernote -->
    <link rel="stylesheet"
        href="{{ asset('adminlte/plugins/summernote/summernote-bs4.min.css') }}">

    {{-- css bootstarp --}}
    {{-- <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"> --}}
    {{-- java script --}}
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    {{-- Java Script Bootsrap --}}
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>



</head>

<body class="hold-transition sidebar-mini layout-fixed">


    <!-- Preloader -->
    <div class="preloader flex-column justify-content-center align-items-center">
        <img class="animation__shake" src="{{ asset('logo/logo-logindo.png') }}" alt="Logindo_Logo">
    </div>

    <!-- Navbar -->
    @include('layout.navbar')
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        @include('layout.sidebar')
    </aside>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">


        </div>
        <!-- /.content-header -->
        <div class="container-fluid">
            @yield('content')
        </div>

    </div>
    <!-- /.content-wrapper -->
    @include('layout.footer')

    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
        <!-- Control sidebar content goes here -->
    </aside>
    <!-- /.control-sidebar -->
    </div>
    <!-- ./wrapper -->

    <!-- jQuery -->
    {{-- <script src="{{asset('adminlte/plugins/jquery/jquery.min.js') }}">
    </script> --}}
    <!-- jQuery UI 1.11.4 -->
    <script src="{{ asset('adminlte/plugins/jquery-ui/jquery-ui.min.js') }}"></script>
    <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
    <script>
        $.widget.bridge('uibutton', $.ui.button)

    </script>
    <!-- Bootstrap 4 -->
    <script src="{{ asset('adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}">
    </script>
    <!-- ChartJS -->
    <script src="{{ asset('adminlte/plugins/chart.js/Chart.min.js') }}"></script>
    <!-- Sparkline -->
    <script src="{{ asset('adminlte/plugins/sparklines/sparkline.js') }}"></script>
    <!-- JQVMap -->
    <script src="{{ asset('adminlte/plugins/jqvmap/jquery.vmap.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/jqvmap/maps/jquery.vmap.usa.js') }}"></script>
    <!-- jQuery Knob Chart -->
    <script src="{{ asset('adminlte/plugins/jquery-knob/jquery.knob.min.js') }}"></script>
    <!-- daterangepicker -->
    <script src="{{ asset('adminlte/plugins/moment/moment.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/daterangepicker/daterangepicker.js') }}"></script>
    <!-- Tempusdominus Bootstrap 4 -->
    <script
        src="{{ asset('adminlte/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}">
    </script>
    <!-- Summernote -->
    <script src="{{ asset('adminlte/plugins/summernote/summernote-bs4.min.js') }}"></script>
    <!-- overlayScrollbars -->
    <script
        src="{{ asset('adminlte/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}">
    </script>
    <!-- AdminLTE App -->
    <script src="{{ asset('adminlte/dist/js/adminlte.js') }}"></script>

    <!-- DataTables -->
    <link rel="stylesheet"
        href="{{ asset('adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('adminlte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('adminlte/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <!-- DataTables  & Plugins -->

    <script src="{{ asset('adminlte/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script
        src="{{ asset('adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}">
    </script>
    <script
        src="{{ asset('adminlte/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}">
    </script>
    <script
        src="{{ asset('adminlte/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}">
    </script>
    <script
        src="{{ asset('adminlte/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}">
    </script>
    <script
        src="{{ asset('adminlte/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}">
    </script>
    <script src="{{ asset('adminlte/plugins/jszip/jszip.min.js') }}"></script>

<!-- PDFMake -->
<script src="{{ asset('adminlte/plugins/pdfmake/pdfmake.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/pdfmake/vfs_fonts.js') }}"></script>

<!-- DataTables HTML5 Export -->
<script src="{{ asset('adminlte/plugins/datatables-buttons/js/buttons.html5.min.js') }}">
</script>
    <script src="{{ asset('adminlte/plugins/datatables-buttons/js/buttons.print.min.js') }}">
    </script>
    <script src="{{ asset('adminlte/plugins/datatables-buttons/js/buttons.colVis.min.js') }}">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/5.3.0/papaparse.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.3.0/exceljs.min.js"></script>



    <script>
       var table1;
        $(function () {
            // Setup - add a text input to each footer cell
            $('#example thead tr').clone(true).appendTo('#example thead');
            $('#example thead tr:eq(1) th').each(function (i) {
                var title = $(this).text();
                $(this).html('<input type="text" placeholder="Search ' + title + '" />');

                $('input', this).on('keyup change', function () {
                    if (table1.column(i).search() !== this.value) {
                        table1
                            .column(i)
                            .search(this.value)
                            .draw();
                    }
                });
            });

            table1 = $('#result1').DataTable({
                "orderCellsTop": true,
                "searching": true,
                "lengthChange": true,
                "fixedHeader": true,
                "paging": true,
                "columnDefs": [{
                        "width": "20%",
                        "targets": [0, 1]
                    }, // Mengatur lebar kolom pertama dan kedua
                    // Mengatur lebar kolom keempat dan lainnya sesuai kebutuhan
                ],

                "dom": 'Bfrtip',
                "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"],
                "initComplete": function () {
                    var $buttons = $('.dt-buttons').hide();
                    $('#exportLink').on('change', function () {
                        var btnClass = $(this).find(":selected")[0].id ?
                            '.buttons-' + $(this).find(":selected")[0].id :
                            null;
                        if (btnClass) $buttons.find(btnClass).click();
                    });
                },
            });


        });

        function exportAllTablesToPDF() {
            var doc = new jsPDF();

            // Loop melalui semua tabel dan ekspor masing-masing ke PDF
            var tables = [table1, table2, table3, table4, table5, table6,tabel9,
            table7]; // Ganti dengan semua tabel yang Anda inisialisasi
            var startY = 10;

            tables.forEach(function (table, index) {
                // Tambahkan judul tabel ke PDF
                doc.setFontSize(16);
                doc.text('Table ' + (index + 1), 10, startY);

                // Tambahkan tabel ke PDF
                startY += 20; // Geser ke bawah untuk tabel berikutnya
                doc.autoTable({
                    html: '#' + table.table().node().id, // Menggunakan ID tabel saat ini
                    startY: startY
                });

                // Tambahkan spasi antara tabel
                startY += table.rows().count() * 7; // Menggunakan perkiraan tinggi tabel sebagai spasi
            });

            // Simpan file PDF
            doc.save('All_Tables.pdf');
        }

        // Tambahkan event handler ke tombol "Export All Tables to PDF"
        $('#export-all-tables-pdf-button').on('click', exportAllTablesToPDF);

        $('#date-range').daterangepicker({
            startDate: '2023-01-01',
            endDate: '2023-12-31',
            opens: 'left',
            locale: {
                format: 'YYYY-MM-DD'
            }
        }, function (start, end, label) {
            // Menyaring data tabel berdasarkan rentang tanggal yang dipilih
            table1.columns(0).search(start.format('YYYY-MM-DD') + '|' + end.format('YYYY-MM-DD')).draw();
        });

        function combineTableData() {
            var combinedData = [];
            combinedData = combinedData.concat(table2.rows().data().toArray());
            return combinedData;
        }

        function combineTableConsumption() {
            var combinedData = [];
            combinedData = combinedData.concat(table3.rows().data().toArray());
            return combinedData;
        }

        function combineTableRunning() {
            var combinedData = [];
            combinedData = combinedData.concat(table4.rows().data().toArray());
            return combinedData;
        }

        function combineTablePayload() {
            var combinedData = [];
            combinedData = combinedData.concat(table5.rows().data().toArray());
            return combinedData;
        }

        function combineTableStock() {
            var combinedData = [];
            combinedData = combinedData.concat(table6.rows().data().toArray());
            return combinedData;
        }

        function combineTablegeneral() {
            var combinedData = [];
            combinedData = combinedData.concat(table7.rows().data().toArray());
            return combinedData;
        }


        // Function untuk mengekspor data ke Excel
        function exportToExcel() {
            var combinedData = combineTableData();
            var combineTableRunningData = combineTableRunning();
            var combineTableconsumptionData = combineTableConsumption();
            var combineTablePayloadData = combineTablePayload();
            var combineTableStockData = combineTableStock();
            var combineTablegeneralData = combineTablegeneral();

            // Membuat objek workbook ExcelJS
            var workbook = new ExcelJS.Workbook();

            // Membuat lembar kerja untuk data tabel utama
            var worksheet1 = workbook.addWorksheet('Daily Activity');


            // Menambahkan judul ke lembar kerja
            worksheet1.mergeCells('A1:D1'); // Menggabungkan sel dari A1 hingga D1
            worksheet1.getCell('A1').value = 'Daily Activity'; // Mengatur nilai judul

            // Membuat lembar kerja untuk data tabel "Running Hours"
            var worksheet2 = workbook.addWorksheet('Running Hours');


            // Menambahkan judul ke lembar kerja
            worksheet2.mergeCells('A1:D1'); // Menggabungkan sel dari A1 hingga D1
            worksheet2.getCell('A1').value = 'Running Hours';

            var worksheet3 = workbook.addWorksheet('Consumption');


            // Menambahkan judul ke lembar kerja
            worksheet3.mergeCells('A1:D1'); // Menggabungkan sel dari A1 hingga D1
            worksheet3.getCell('A1').value = 'Consumption'; // Mengatur nilai judul

            var worksheet4 = workbook.addWorksheet('Stock Status');


            // Menambahkan judul ke lembar kerja
            worksheet4.mergeCells('A1:D1'); // Menggabungkan sel dari A1 hingga D1
            worksheet4.getCell('A1').value = 'Stock Status'; // Mengatur nilai judul

            var worksheet5 = workbook.addWorksheet('Payload');


            // Menambahkan judul ke lembar kerja
            worksheet5.mergeCells('A1:I1'); // Menggabungkan sel dari A1 hingga D1
            worksheet5.getCell('A1').value = 'Payload'; // Mengatur nilai judul

            var worksheet6 = workbook.addWorksheet('General Info');
            // Menambahkan judul ke lembar kerja
            worksheet6.mergeCells('A1:I1'); // Menggabungkan sel dari A1 hingga D1
            worksheet6.getCell('A1').value = 'General Info'; // Mengatur nilai judul

            // Mengatur ukuran judul
            worksheet1.getCell('A1').font = {
                size: 20
            }; // Mengatur nilai judul
            worksheet2.getCell('A1').font = {
                size: 20
            }; // Mengatur nilai judul
            worksheet3.getCell('A1').font = {
                size: 20
            }; // Mengatur nilai judul
            worksheet4.getCell('A1').font = {
                size: 20
            }; // Mengatur nilai judul
            worksheet5.getCell('A1').font = {
                size: 20
            }; // Mengatur nilai judul
            worksheet6.getCell('A1').font = {
                size: 20
            }; // Mengatur nilai judul



            // Tambahkan tabel untuk data tabel utama
            var table1Ref = 'E4'; // Lokasi awal tabel pada worksheet1
            var table1Data = table2.rows().data().toArray().map(function (row) {
                return [row[0], row[1], row[2], row[3]]; // Sesuaikan dengan jumlah kolom
            });

            table1 = worksheet1.addTable({
                name: 'Table1',
                ref: table1Ref,
                headerRow: true,
                totalsRow: false,
                style: {
                    theme: 'TableStyleLight9', // Opsi gaya tabel
                    showRowStripes: true,
                },
                columns: [{
                        name: 'Date'
                    },
                    {
                        name: 'Time From'
                    },
                    {
                        name: 'Time To'
                    },
                    {
                        name: 'Description'
                    }
                    // Tambahkan kolom lainnya sesuai kebutuhan
                ],
                rows: table1Data,
            });

            //  // Mengambil nama kolom dari tabel DataTables
            //  var table1Columns = table2.columns().header().toArray();
            //     var table2Columns = table3.columns().header().toArray();
            //     var table3Columns = table4.columns().header().toArray();
            //     var table4Columns = table5.columns().header().toArray();
            //     var table5Columns = table6.columns().header().toArray();

            //     // Header Tabel
            // var table2Headers = table2Columns.map(function (header) {
            //     return header.textContent;
            // });

            // Tambahkan tabel untuk data "Running Hours"
            // Tambahkan tabel untuk data "Payload"
            var table2Ref = 'E4'; // Lokasi awal tabel pada worksheet2
            var table2Data = table5.rows().data().toArray().map(function (row) {
                return [row[0], row[1], row[2], row[3], row[4], row[5], row[6]];
                // Sesuaikan dengan jumlah kolom
            });


            table2 = worksheet2.addTable({
                name: 'Table2',
                ref: table2Ref,
                headerRow: true,
                totalsRow: false,
                style: {
                    theme: 'TableStyleLight9', // Opsi gaya tabel
                    showRowStripes: true,
                },
                columns: [{
                        name: 'Date'
                    },
                    {
                        name: 'Machine'
                    },
                    {
                        name: 'Run Hours Towing'
                    },
                    {
                        name: 'Run Hours Manouver'
                    },
                    {
                        name: 'Run Hours Slow'
                    },
                    {
                        name: 'Run Hours Economi'
                    },
                    {
                        name: 'Run Hours Full Speed'
                    },
                    // Tambahkan kolom lainnya sesuai kebutuhan
                ],
                rows: table2Data,
                autoFilter: {
                    from: 'A1', // Mulai dari sel A1 (termasuk baris judul)
                    to: 'D1' // Hingga sel D1
                }
            });

            // Tambahkan tabel untuk data "Consumption"
            var table3Ref = 'E4'; // Lokasi awal tabel pada worksheet3
            var table3Data = table3.rows().data().toArray().map(function (row) {
                return [row[0], row[1], row[2], row[3], row[4], row[5]];
                // Sesuaikan dengan jumlah kolom
            });
            table3 = worksheet3.addTable({
                name: 'Table3',
                ref: table3Ref,
                headerRow: true,
                totalsRow: false,
                style: {
                    theme: 'TableStyleLight9', // Opsi gaya tabel
                    showRowStripes: true,
                },
                columns: [{
                        name: 'Date'
                    },
                    {
                        name: 'Machine'
                    },
                    {
                        name: 'Product Code'
                    },
                    {
                        name: 'Product Name'
                    },
                    {
                        name: 'Description'
                    },
                    {
                        name: 'used'
                    },
                    // Tambahkan kolom lainnya sesuai kebutuhan
                ],
                rows: table3Data,
                autoFilter: {
                    from: 'A1', // Mulai dari sel A1 (termasuk baris judul)
                    to: 'D1' // Hingga sel D1
                }
            });

            // Tambahkan tabel untuk data "Stock Status"
            var table4Ref = 'E4'; // Lokasi awal tabel pada worksheet4
            var table4Data = table6.rows().data().toArray().map(function (row) {
                return [row[0], row[1], row[2], row[3], row[4], row[5], row[6], row[7], row[8], row[9]];
                // Sesuaikan dengan jumlah kolom
            });
            table4s = worksheet4.addTable({
                name: 'Table4',
                ref: table4Ref,
                headerRow: true,
                totalsRow: false,
                style: {
                    theme: 'TableStyleLight9', // Opsi gaya tabel
                    showRowStripes: true,
                },
                columns: [{
                        name: 'Date'
                    },
                    {
                        name: 'Product Code'
                    },
                    {
                        name: 'Product Name'
                    },
                    {
                        name: 'Spec'
                    },
                    {
                        name: 'Previous'
                    },
                    {
                        name: 'Received'
                    },
                    {
                        name: 'Used'
                    },
                    {
                        name: 'Transfered'
                    },
                    {
                        name: 'Sounding'
                    },
                    {
                        name: 'Remain'
                    },
                    // Tambahkan kolom lainnya sesuai kebutuhan
                ],
                rows: table4Data,
                autoFilter: {
                    from: 'A1', // Mulai dari sel A1 (termasuk baris judul)
                    to: 'D1' // Hingga sel D1
                }
            });

            // Tambahkan tabel untuk data "Payload"
            var table5Ref = 'E4'; // Lokasi awal tabel pada worksheet5
            var table5Data = table4.rows().data().toArray().map(function (row) {
                return [row[0], row[1], row[2], row[3], row[4], row[5]];
                // Sesuaikan dengan jumlah kolom
            });
            table5 = worksheet5.addTable({
                name: 'Table5',
                ref: table5Ref,
                headerRow: true,
                totalsRow: false,
                style: {
                    theme: 'TableStyleLight9', // Opsi gaya tabel
                    showRowStripes: true,
                },
                columns: [{
                        name: 'Date'
                    },
                    {
                        name: 'Product Name'
                    },
                    {
                        name: 'Previous'
                    },
                    {
                        name: 'Received'
                    },
                    {
                        name: 'Transfered'
                    },
                    {
                        name: 'Remain'
                    },
                    // Tambahkan kolom lainnya sesuai kebutuhan
                ],
                rows: table5Data,
                autoFilter: {
                    from: 'A1', // Mulai dari sel A1 (termasuk baris judul)
                    to: 'D1' // Hingga sel D1
                }
            });

            // Tambahkan tabel untuk data "Running Hours"
            var table6Ref = 'A4'; // Lokasi awal tabel pada worksheet5
            var table6Data = table7.rows().data().toArray().map(function (row) {
                return [row[0], row[1], row[2], row[3], row[4], row[5], row[6], row[7], row[8], row[9], row[10],
                    row[11], row[12], row[13],
                    row[14], row[15], row[16], row[17], row[18], row[19], row[20], row[21], row[22], row[
                    23], row[24], row[25], row[26]
                ];
                // Sesuaikan dengan jumlah kolom
            });
            table6 = worksheet6.addTable({
                name: 'Table6',
                ref: table6Ref,
                headerRow: true,
                totalsRow: false,
                style: {
                    theme: 'TableStyleLight9', // Opsi gaya tabel
                    showRowStripes: true,
                },
                columns: [{
                        name: 'Date'
                    },
                    {
                        name: 'Vessel Name'
                    },
                    {
                        name: 'Total Distance Run'
                    },
                    {
                        name: 'Vessel Group'
                    },
                    {
                        name: 'Time Run'
                    },
                    {
                        name: 'General Position'
                    },
                    {
                        name: 'Total Time Run'
                    },
                    {
                        name: 'Master"s Name'
                    },
                    {
                        name: 'Average Speed'
                    },
                    {
                        name: 'Time Zone'
                    },
                    {
                        name: ' General Average Speed'
                    },
                    {
                        name: 'Latitude'
                    },
                    {
                        name: 'Visibility Scale'
                    },
                    {
                        name: 'Longitude'
                    },
                    {
                        name: 'Scale of Wind Force'
                    },
                    {
                        name: 'Scale of Sea Swell'
                    },
                    {
                        name: 'Barometer'
                    },
                    {
                        name: 'Wind Direction'
                    },
                    {
                        name: 'Vessel Course (T)'
                    },
                    {
                        name: 'Weather'
                    },
                    {
                        name: 'Distance To Go'
                    },
                    {
                        name: 'Temperature'
                    },
                    {
                        name: 'Towage Operation'
                    },
                    {
                        name: 'Destination'
                    },
                    {
                        name: 'Name / Size (GRT)'
                    },
                    {
                        name: 'ETA'
                    },
                    {
                        name: 'Vessel Status'
                    },
                ],
                rows: table6Data,
                autoFilter: {
                    from: 'A1', // Mulai dari sel A1 (termasuk baris judul)
                    to: 'D1' // Hingga sel D1
                }
            });

            worksheet1.columns.forEach(function (column, i) {
                var maxWidth = 0;
                column.eachCell({
                    includeEmpty: true
                }, function (cell) {
                    var cellWidth = cell.value ? cell.value.toString().length :
                    10; // 10 adalah lebar default jika sel kosong
                    if (cellWidth > maxWidth) {
                        maxWidth = cellWidth;
                    }
                });
                column.width = maxWidth < 10 ? 10 : maxWidth; // Mengatur lebar kolom minimum
            });

            // Mengatur lebar kolom secara otomatis pada worksheet2
            worksheet2.columns.forEach(function (column, i) {
                var maxWidth = 0;
                column.eachCell({
                    includeEmpty: true
                }, function (cell) {
                    var cellWidth = cell.value ? cell.value.toString().length :
                    10; // 10 adalah lebar default jika sel kosong
                    if (cellWidth > maxWidth) {
                        maxWidth = cellWidth;
                    }
                });
                column.width = maxWidth < 10 ? 10 : maxWidth; // Mengatur lebar kolom minimum
            });

            // Mengatur lebar kolom secara otomatis pada worksheet2
            worksheet3.columns.forEach(function (column, i) {
                var maxWidth = 0;
                column.eachCell({
                    includeEmpty: true
                }, function (cell) {
                    var cellWidth = cell.value ? cell.value.toString().length :
                    10; // 10 adalah lebar default jika sel kosong
                    if (cellWidth > maxWidth) {
                        maxWidth = cellWidth;
                    }
                });
                column.width = maxWidth < 10 ? 10 : maxWidth; // Mengatur lebar kolom minimum
            });

            // Mengatur lebar kolom secara otomatis pada worksheet2
            worksheet4.columns.forEach(function (column, i) {
                var maxWidth = 0;
                column.eachCell({
                    includeEmpty: true
                }, function (cell) {
                    var cellWidth = cell.value ? cell.value.toString().length :
                    10; // 10 adalah lebar default jika sel kosong
                    if (cellWidth > maxWidth) {
                        maxWidth = cellWidth;
                    }
                });
                column.width = maxWidth < 10 ? 10 : maxWidth; // Mengatur lebar kolom minimum
            });

            // Mengatur lebar kolom secara otomatis pada worksheet2
            worksheet5.columns.forEach(function (column, i) {
                var maxWidth = 0;
                column.eachCell({
                    includeEmpty: true
                }, function (cell) {
                    var cellWidth = cell.value ? cell.value.toString().length :
                    10; // 10 adalah lebar default jika sel kosong
                    if (cellWidth > maxWidth) {
                        maxWidth = cellWidth;
                    }
                });
                column.width = maxWidth < 10 ? 10 : maxWidth; // Mengatur lebar kolom minimum
            });
            worksheet6.columns.forEach(function (column, i) {
                var maxWidth = 0;
                column.eachCell({
                    includeEmpty: true
                }, function (cell) {
                    var cellWidth = cell.value ? cell.value.toString().length :
                    10; // 10 adalah lebar default jika sel kosong
                    if (cellWidth > maxWidth) {
                        maxWidth = cellWidth;
                    }
                });
                column.width = maxWidth < 10 ? 10 : maxWidth; // Mengatur lebar kolom minimum
            });

            // Mengubah hasil workbook menjadi blob
            workbook.xlsx.writeBuffer().then(function (buffer) {
                var blob = new Blob([buffer], {
                    type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                });
                var url = URL.createObjectURL(blob);
                var a = document.createElement('a');
                a.href = url;
                a.download = 'Vessel Daily Report.xlsx';
                a.click();
            });
        }

        // Menambahkan event handler ke tombol "Export Data"
        $('#export-data').on('click', exportToExcel);

    </script>

</body>

</html>
