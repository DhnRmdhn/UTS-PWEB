$(function () {
    
    var $alertPesan = $('#alert-pesan');
    if ($alertPesan.length) {
        setTimeout(function () {
            $alertPesan.fadeOut('slow');
        }, 4500);
    }


    var daftarKategori = {
        pemasukan: ['Gaji', 'Uang Saku', 'Bonus', 'Lainnya'],
        pengeluaran: ['Makan', 'Transportasi', 'Top Up', 'Belanja', 'Tagihan', 'Lainnya']
    };

    // Mengisi pilihan kategori sesuai jenis transaksi yang dipilih
    $('#jenis').on('change', function () {
        var jenisDipilih = $(this).val();
        var $kategori = $('#kategori');

        $kategori.empty();
        $kategori.append('<option value="" selected disabled>— Pilih kategori —</option>');

        if (daftarKategori[jenisDipilih]) {
            $.each(daftarKategori[jenisDipilih], function (index, kategori) {
                $kategori.append('<option value="' + kategori + '">' + kategori + '</option>');
            });
        }
    });

    $('#form-transaksi').on('submit', function (e) {
        var tanggal = $.trim($('#tanggal').val());
        var jenis = $('#jenis').val();
        var kategori = $('#kategori').val();
        var nominal = $.trim($('#nominal').val());
        var $pesanValidasi = $('#pesan-validasi');
        var daftarError = [];

        
        if (tanggal === '') {
            daftarError.push('Tanggal wajib diisi.');
        }
        if (!jenis) {
            daftarError.push('Jenis transaksi wajib dipilih.');
        }
        if (!kategori) {
            daftarError.push('Kategori wajib dipilih.');
        }
        if (nominal === '') {
            daftarError.push('Nominal wajib diisi.');
        } else {
            var angkaNominal = parseFloat(nominal);
        
            if (isNaN(angkaNominal) || angkaNominal <= 0) {
                daftarError.push('Nominal harus berupa angka lebih dari 0.');
            }
        }

        if (daftarError.length > 0) {
            e.preventDefault();
            $pesanValidasi
                .removeClass('d-none')
                .text(daftarError.join(' '));
            return;
        }

    
        $pesanValidasi.addClass('d-none').text('');

       
        if (!window.confirm('Yakin ingin menyimpan transaksi ini?')) {
            e.preventDefault();
        }
    });
});
